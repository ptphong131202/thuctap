<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LopHoc;
use App\Models\SinhVien;
use App\Models\BangDiem;
use App\Models\MonHoc;
use App\Models\DotThi;
use App\Models\DotThiBangDiem;
use App\Enums\BangDiemType;
use App\Excels\KetQuaHocTap;
use DB;
use Maatwebsite\Excel\Concerns\ToArray;

class NhapDiemController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $permissions = auth()->user()->permissions()->get()->map(function ($item) {
            return $item->permission_id;
        });

        // Lưu url vào session
        session(['parent_url:nhap-diem' => $request->fullUrl()]);

        return view('qlsv.nhapdiem.nhapdiem_list', compact('permissions'));
    }

    public function paginate(Request $request)
    {
        $search = $request->search;
        $nk_id = $request->nienkhoa;
        $hdt_id = $request->chuongtrinh;
        $danhSach = LopHoc::with('khoaDaoTao', 'nienKhoa')
            ->withCount('sinhVien')
            ->where(function ($builder) use ($search) {
                $builder->whereRaw('lower(qlsv_lophoc.lh_ma) like lower(?)', "%$search%")
                    ->orWhereRaw('lower(qlsv_lophoc.lh_ten) like lower(?)', "%$search%");
            })
            ->where(function ($builder) use ($nk_id) {
                if (isset ($nk_id) && $nk_id != -1) {
                    $builder->whereRaw('qlsv_lophoc.nk_id = ?', "$nk_id");
                }
            })
            ->whereHas('khoaDaoTao', function ($builder) use ($hdt_id) {
                if (auth()->user()->hasPermission('caodang') && auth()->user()->hasPermission('trungcap')) {
                    if (isset ($hdt_id) && $hdt_id != -1) {
                        $builder->whereRaw('qlsv_khoadaotao.hdt_id = ?', $hdt_id);
                    }
                } else {
                    if (auth()->user()->hasPermission('caodang')) {
                        $builder->whereRaw('qlsv_khoadaotao.hdt_id = ?', 4);
                    } else if (auth()->user()->hasPermission('trungcap')) {
                        $builder->whereRaw('qlsv_khoadaotao.hdt_id = ?', 5);
                    }
                }
            })
            ->orderBy('lh_id', 'desc')
            ->paginate(10)
            ->setPath(route('nhap-diem.index'))
            ->appends([
                'search' => $search,
                'nienkhoa' => $nk_id,
                'chuongtrinh' => $hdt_id
            ])
            ->onEachSide(2);
        foreach ($danhSach as $lophoc) {
            $lophoc->show_url = route('nhap-diem.show', $lophoc);
            $lophoc->result_url = route('nhap-diem.ket-qua-hoc-tap', $lophoc);
            $lophoc->themdotthi = DB::table('qlsv_lophoc_monhoc as kdt')
                ->join('qlsv_monhoc as mh', 'mh.mh_id', '=', 'kdt.mh_id')
                ->where('kdt.lh_id', $lophoc->lh_id)
                ->selectRaw('SUM(CASE WHEN mh.mh_loai = 5 THEN 1 ELSE 0 END) > 0 as tontaiBC,
                    SUM(CASE WHEN mh.mh_loai = 4 THEN 1 ELSE 0 END) > 0 as tontaiLT,
                    SUM(CASE WHEN mh.mh_loai = 3 THEN 1 ELSE 0 END) > 0 as tontaiTH,
                    SUM(CASE WHEN mh.mh_loai = 2 THEN 1 ELSE 0 END) > 0 as tontaiCT')
                ->first();
        }
        return response()->json($danhSach);
    }

    public function getDanhSachHocKy($lhId)
    {
        $lopHoc = LopHoc::find($lhId);
        if (!$lopHoc) {
            abort(404);
        }
        $danhSachMonHoc = DB::table('qlsv_khoadaotao as kdt')
            ->join('qlsv_lophoc as lh', 'lh.kdt_id', '=', 'kdt.kdt_id')
            ->join('qlsv_lophoc_monhoc as kdtp', 'kdtp.lh_id', '=', 'lh.lh_id')
            ->join('qlsv_monhoc as mh', 'mh.mh_id', '=', 'kdtp.mh_id')
            ->leftJoin('qlsv_bangdiem as bd', function ($join) {
                $join->on('bd.lh_id', '=', 'lh.lh_id')
                    ->on('bd.kdt_hocky', '=', 'kdtp.lh_mh_hocky')
                    ->on('kdtp.mh_id', '=', 'bd.mh_id')
                    ->where('bd.bd_type', BangDiemType::BANGDIEM_MONHOC);
            })
            ->where('lh.lh_id', $lhId)
            ->whereRaw('mh.mh_loai = 1')
            ->select('mh.*', 'kdtp.lh_mh_hocky as kdt_mh_hocky', 'bd.bd_id', 'bd.created_at as bd_created_at', 'bd.bd_giangvien')
            ->orderBy('kdtp.lh_mh_hocky', 'asc')
            ->orderBy('kdtp.lh_mh_index', 'asc')
            ->get();


        $danhSachDiemRenLuyen = BangDiem::whereLhId($lhId)
            ->whereBdType(BangDiemType::BANGDIEM_HOCKY)
            ->orderBy('kdt_hocky', 'asc')
            ->get();

        $danhSachDotThi = DB::table('qlsv_dotthi_bangdiem as dtbd')
            ->join('qlsv_dotthi as dt', 'dt.dt_id', '=', 'dt.dt_id')
            ->where('dtbd.lh_id', $lhId)
            ->select('dtbd.dt_id', 'dt.*')
            ->orderBy('dt.created_at', 'asc')
            ->distinct()
            ->get();

        foreach ($danhSachDotThi as $dtIndex => $dotthi) {
            $dotthi->monHoc = DB::table('qlsv_dotthi_bangdiem as dtbd')
                ->join('qlsv_monhoc as mh', 'mh.mh_id', '=', 'dtbd.mh_id')
                ->where('dtbd.dt_id', $dotthi->dt_id)
                ->where('dtbd.lh_id', $lhId)
                ->whereRaw('mh.mh_loai != 1')
                ->select('mh.*', 'dtbd.*')
                ->get();
        }

        $result = [
            'danh_sach_mon_hoc' => $danhSachMonHoc,
            'danh_sach_diem_ren_luyen' => $danhSachDiemRenLuyen,
            'danh_sach_dot_thi' => $danhSachDotThi
        ];
        return response()->json($result);
    }

    public function show(LopHoc $lopHoc)
    {
        $permissions = auth()->user()->permissions()->get()->map(function ($item) {
            return $item->permission_id;
        });

        $parentUrl = session('parent_url:nhap-diem', '/nhap-diem');
        return view('qlsv.nhapdiem.nhapdiem_show', compact(['lopHoc', 'permissions', 'parentUrl']));
    }

    public function nhapDiemRenLuyen($token)
    {
        $params = json_decode(base64_decode($token));
        try {
            $lopHoc = LopHoc::find($params->lh_id);
            if (!$lopHoc) {
                abort(404);
            }
            $hocKy = $params->hoc_ky;
        } catch (\Exception $e) {
            abort(404);
        }
        return view('qlsv.nhapdiem.nhapdiem_renluyen', compact(['lopHoc', 'hocKy']));
    }

    public function nhapDiemMonHoc($token)
    {
        $params = json_decode(base64_decode($token));
        try {
            $lopHoc = LopHoc::find($params->lh_id);
            if (!$lopHoc) {
                abort(404);
            }
            $hocKy = $params->hoc_ky;
            $monHoc = MonHoc::find($params->mh_id);
            if (!$monHoc) {
                abort(404);
            }
        } catch (\Exception $e) {
            abort(404);
        }
        return view('qlsv.nhapdiem.nhapdiem_monhoc', compact(['lopHoc', 'hocKy', 'monHoc']));
    }

    public function nhapDiemDotThi($token)
    {
        $params = json_decode(base64_decode($token));
        try {
            $lopHoc = LopHoc::find($params->lh_id);
            if (!$lopHoc) {
                abort(404);
            }
            $dotthi = DotThi::find($params->dot_thi);
            $monHoc = MonHoc::find($params->mh_id);

            if (!$monHoc) {
                abort(404);
            }
        } catch (\Exception $e) {
            abort(404);
        }
        return view('qlsv.nhapdiem.nhapdiem_dotthi', compact(['lopHoc', 'dotthi', 'monHoc']));
    }

    public function getBangDiemDotThi(Request $request, $lh_id)
    {

        $bd_type = $request->bd_type;
        $mh_id = $request->mh_id;
        $dt_id = $request->dt_id;
        $bangDiem = DotThiBangDiem::whereLhId($lh_id)
            ->where(function ($builder) use ($mh_id) {
                if ($mh_id) {
                    $builder->whereMhId($mh_id);
                }
            })
            ->first();
        if (!$bangDiem) {
            $bangDiem = new DotThiBangDiem;
            $bangDiem->fill([
                'lh_id' => $lh_id,
                'mh_id' => $mh_id,
                'dt_id' => $dt_id,
            ]);
        }

        $bangDiem->data = $this->getDanhSachDiemDotThiSinhVien($lh_id, $dt_id, $mh_id);

        return response()->json($bangDiem);
    }

    public function getBangDiem(Request $request, $lh_id)
    {

        $bd_type = $request->bd_type;
        $mh_id = $request->mh_id;
        $kdt_hocky = $request->hocky;
        $bangDiem = BangDiem::whereLhId($lh_id)
            ->whereBdType($bd_type)
            ->where(function ($builder) use ($mh_id) {
                if ($mh_id) {
                    $builder->whereMhId($mh_id);
                }
            })
            ->whereKdtHocky($kdt_hocky)
            ->first();
        if (!$bangDiem) {
            $bangDiem = new BangDiem;
            $bangDiem->fill([
                'bd_type' => $bd_type,
                'lh_id' => $lh_id,
                'mh_id' => $mh_id,
                'kdt_hocky' => $kdt_hocky,
            ]);
        }

        $bangDiem->data = $this->getDanhSachDiemSinhVien($lh_id, $kdt_hocky, $mh_id, $bd_type);

        $listSvid = $bangDiem->data->map(function ($svd) {
            return $svd->sv_id;
        });

        $danhSachSinhVien = SinhVien::with(['quyetDinhTotNghiep', 'quyetDinhXoaTen'])->whereIn('sv_id', $listSvid)->get();

        $bangDiem->data->each(function ($svd) use ($danhSachSinhVien) {
            $sinhVien = $danhSachSinhVien->firstWhere('sv_id', $svd->sv_id);
            if ($sinhVien && $sinhVien->quyetDinhXoaTen()->exists()) {
                $svd->quyetDinhXoaTen = $sinhVien->quyetDinhXoaTen()->first();
            }

            if ($sinhVien && $sinhVien->quyetDinhTotNghiep()->exists()) {
                $svd->quyetDinhTotNghiep = $sinhVien->quyetDinhTotNghiep()->first();
            }
        });

        return response()->json($bangDiem);
    }

    public function getDanhSachDiemDotThiSinhVien($lh_id, $dt_id, $mh_id)
    {
        $danhSachDiem = DB::table('qlsv_dotthi_bangdiem as bd')
            ->where('bd.lh_id', $lh_id)
            ->where('bd.dt_id', $dt_id)
            ->where('bd.mh_id', $mh_id)
            ->join('qlsv_dotthi_diem as svd', function ($join) {
                $join->on('svd.dt_bd_id', '=', 'bd.dt_bd_id');
            })
            ->join('qlsv_sinhvien as sv', function ($join) {
                $join->on('sv.sv_id', '=', 'svd.sv_id');
            })
            ->whereNull('sv.deleted_at')
            ->whereRaw('svd.svd_dieukien = 1')
            ->select(
                'sv.sv_id',
                'sv.sv_ma',
                'sv.sv_ho',
                'sv.sv_ten',
                'sv.user_id',
                'svd.svd_dulop',
                'svd.svd_first',
                'svd.svd_second',
                'svd.svd_second_hocky',
                'svd_final',
                'svd.svd_ghichu',
                'svd_loai'
            )
            ->orderBy('sv.sv_ma', 'asc')
            ->get();
        return $danhSachDiem;
    }

    public function getDanhSachDiemSinhVien($lh_id, $kdt_hocky, $mh_id, $bd_type)
    {
        $danhSachDiem = DB::table('qlsv_sinhvien as sv')
            ->join('qlsv_sinhvien_lophoc as pl', function ($join) use ($lh_id) {
                $join->on('pl.sv_id', '=', 'sv.sv_id')
                    ->where('pl.lh_id', $lh_id);
            })
            ->leftJoin('qlsv_bangdiem as bd', function ($join) use ($kdt_hocky, $mh_id, $bd_type) {
                $join->on('bd.lh_id', 'pl.lh_id')
                    ->where('bd.kdt_hocky', $kdt_hocky)
                    ->where('bd.mh_id', $mh_id)
                    ->where('bd.bd_type', $bd_type);
            })
            ->leftJoin('qlsv_sinhvien_diem as svd', function ($join) {
                $join->on('svd.bd_id', '=', 'bd.bd_id')
                    ->whereRaw('svd.sv_id = sv.sv_id');
            })
            ->whereNull('sv.deleted_at')
            ->select(
                'sv.sv_id',
                'sv.sv_ma',
                'sv.sv_ho',
                'sv.sv_ten',
                'sv.user_id',
                'svd.svd_dulop',
                'svd.svd_first',
                'svd.svd_second',
                'svd.svd_second_hocky',
                'svd.svd_third',
                'svd.svd_third_hocky',
                'svd_final',
                'svd.svd_ghichu',
                'svd.svd_exam_first',
                'svd.svd_exam_second',
                'svd.svd_exam_third'
            )
            ->orderBy('sv.sv_ma', 'asc')
            ->get();
        $danhSachDiem->each(function ($item) use ($kdt_hocky) {
            if (!$item->svd_second_hocky) {
                $item->svd_second_hocky = $kdt_hocky;
            }
        });

        return $danhSachDiem;
    }

    public function updateBangDiem(Request $request, $lh_id)
    {
        $validator = \Validator::make($request->all(), [
            'data.*.svd_dulop' => 'nullable|gte:0|lte:100|integer',
            'data.*.svd_final' => 'nullable|gte:0|lte:100',
            // 'data.*.svd_lopdu' => 'nullable|gte:0|lte:100',
            // Diem tong ket
            'data.*.svd_first' => 'nullable|gte:0|lte:10',
            'data.*.svd_second' => 'nullable|gte:0|lte:10',
            'data.*.svd_third' => 'nullable|gte:0|lte:10',
            // Diem thi
            'data.*.svd_exam_first' => 'nullable|gte:0|lte:10',
            'data.*.svd_exam_second' => 'nullable|gte:0|lte:10',
            'data.*.svd_exam_third' => 'nullable|gte:0|lte:10',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }
        DB::transaction(function () use ($request, $lh_id) {
            $bd_type = $request->bd_type;
            $mh_id = $request->mh_id;
            $hocky = $request->kdt_hocky;
            $bangDiem = BangDiem::whereLhId($lh_id)
                ->whereBdType($bd_type)
                ->whereMhId($mh_id)
                ->whereKdtHocky($hocky)
                ->first();
            if (!$bangDiem) {
                $bangDiem = new BangDiem;
                $bangDiem->bd_type = $bd_type;
                $bangDiem->fill($request->only(['lh_id', 'mh_id', 'kdt_hocky']));
            }
            $bangDiem->fill($request->only(['bd_tungay', 'bd_denngay', 'bd_giangvien']));
            $bangDiem->user_id = auth()->user()->user_id;
            $bangDiem->save();
            $danhSachSinhVien = $request->data;
            if (is_array($danhSachSinhVien)) {
                foreach ($danhSachSinhVien as $sinhVien) {
                    $updateData = [
                        'svd_dulop' => $sinhVien['svd_dulop'],
                        'svd_first' => $sinhVien['svd_first'],
                        'svd_exam_first' => isset ($sinhVien['svd_exam_first']) ? $sinhVien['svd_exam_first'] : null,
                        'svd_second' => $sinhVien['svd_second'],
                        'svd_exam_second' => isset ($sinhVien['svd_exam_second']) ? $sinhVien['svd_exam_second'] : null,
                        'svd_third' => isset ($sinhVien['svd_third']) ? $sinhVien['svd_third'] : null,
                        'svd_exam_third' => isset ($sinhVien['svd_exam_third']) ? $sinhVien['svd_exam_third'] : null,
                        'svd_ghichu' => $sinhVien['svd_ghichu'],
                        'svd_final' => $sinhVien['svd_final'],
                        'svd_second_hocky' => isset ($sinhVien['svd_second_hocky']) ? $sinhVien['svd_second_hocky'] : null,
                        'svd_third_hocky' => isset ($sinhVien['svd_third_hocky']) ? $sinhVien['svd_third_hocky'] : null,
                        'svd_total' => isset ($sinhVien['svd_total']) ? $sinhVien['svd_total'] : null,
                    ];
                    if (DB::table('qlsv_sinhvien_diem')->whereBdId($bangDiem->bd_id)->whereSvId($sinhVien['sv_id'])->exists()) {
                        DB::table('qlsv_sinhvien_diem')
                            ->whereBdId($bangDiem->bd_id)
                            ->whereSvId($sinhVien['sv_id'])
                            ->update($updateData);
                    } else {
                        $updateData['bd_id'] = $bangDiem->bd_id;
                        $updateData['sv_id'] = $sinhVien['sv_id'];
                        DB::table('qlsv_sinhvien_diem')->insert($updateData);
                    }
                }
            }
        });
        return $request->all();
    }

    public function destroyBangDiem($bdId)
    {
        DB::transaction(function () use ($bdId) {
            $bangDiem = BangDiem::find($bdId);
            $bangDiem->delete();
            DB::table('qlsv_sinhvien_diem')->whereBdId($bdId)->delete();
            return response()->json($bangDiem);
        });
    }

    public function destroyBangDiemDotThi($dtbdId)
    {
        DB::transaction(function () use ($dtbdId) {
            DB::table('qlsv_dotthi_diem')->whereDtBdId($dtbdId)->delete();
            return response()->json($dtbdId);
        });
    }

    public function updateBangDiemDotThi(Request $request, $lh_id)
    {
        $validator = \Validator::make($request->all(), [
            'data.*.svd_dulop' => 'nullable|gte:0|lte:100|integer',
            'data.*.svd_final' => 'nullable|gte:0|lte:100',
            'data.*.svd_first' => 'nullable|gte:0|lte:10',
            'data.*.svd_second' => 'nullable|gte:0|lte:10',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }
        DB::transaction(function () use ($request, $lh_id) {
            $bd_type = $request->bd_type;
            $mh_id = $request->mh_id;
            $dt_id = $request->dt_id;
            $bangDiem = DotThiBangDiem::whereLhId($lh_id)
                ->whereMhId($mh_id)
                ->whereDtId($dt_id)
                ->first();
            if (!$bangDiem) {
                $bangDiem = new DotThiBangDiem;
                $bangDiem->fill($request->only(['lh_id', 'mh_id', 'dt_id']));
            }
            $bangDiem->fill($request->only(['dt_bd_tungay', 'dt_bd_denngay']));
            $bangDiem->save();
            $danhSachSinhVien = $request->data;
            if (is_array($danhSachSinhVien)) {
                foreach ($danhSachSinhVien as $sinhVien) {
                    if (DB::table('qlsv_dotthi_diem')->whereDtBdId($bangDiem->dt_bd_id)->whereSvId($sinhVien['sv_id'])->exists()) {
                        DB::table('qlsv_dotthi_diem')->whereDtBdId($bangDiem->dt_bd_id)->whereSvId($sinhVien['sv_id'])->update([
                            'svd_dulop' => $sinhVien['svd_dulop'],
                            'svd_first' => $sinhVien['svd_first'],
                            // 'svd_ghichu' => $sinhVien['svd_ghichu'],
                            'svd_final' => $sinhVien['svd_final'],
                            'svd_second_hocky' => isset ($sinhVien['svd_second_hocky']) ? $sinhVien['svd_second_hocky'] : null,
                            'svd_total' => isset ($sinhVien['svd_total']) ? $sinhVien['svd_total'] : null,
                        ]);
                    } else {
                        DB::table('qlsv_dotthi_diem')->insert([
                            'dt_bd_id' => $bangDiem->dt_bd_id,
                            'sv_id' => $sinhVien['sv_id'],
                            'svd_dulop' => $sinhVien['svd_dulop'],
                            'svd_first' => $sinhVien['svd_first'],
                            'svd_second' => $sinhVien['svd_second'],
                            'svd_ghichu' => $sinhVien['svd_ghichu'],
                            'svd_final' => $sinhVien['svd_final'],
                            'svd_second_hocky' => isset ($sinhVien['svd_second_hocky']) ? $sinhVien['svd_second_hocky'] : null,
                            'svd_total' => isset ($sinhVien['svd_total']) ? $sinhVien['svd_total'] : null,
                        ]);
                    }
                }
            }
        });
        return $request->all();
    }


    public function updateBangDiemLoai0DotThi(Request $request, $lh_id)
    {
        $validator = \Validator::make($request->all(), [
            'data.*.svd_first' => 'nullable|gte:0|lte:10'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }

        $bd_type = $request->bd_type;
        $mh_id = $request->mh_id;
        $dt_id = $request->dt_id;
        //     $bangDiem = DotThiBangDiem::whereLhId($lh_id[0])
        //         ->whereMhId($mh_id)
        //         ->whereDtId($dt_id)
        //         ->first();
        //     if (!$bangDiem) {
        //         $bangDiem = new DotThiBangDiem;
        //         $bangDiem->fill($request->only(['lh_id', 'mh_id', 'dt_id']));
        //     }



        $bangDiem = DB::table('qlsv_dotthi_bangdiem as bd')
            ->join('qlsv_dotthi_diem as svd', function ($join) {
                $join->on('svd.dt_bd_id', '=', 'bd.dt_bd_id');
            })
            ->whereIn('bd.mh_id', $mh_id)
            ->where('svd.dt_id', $dt_id)
            ->where('bd.lh_id', $lh_id)
            ->select(DB::raw('DISTINCT svd.dt_bd_id'))
            ->get()
            ->toArray();

        $danhSachSinhVien = $request->data;
        if (is_array($danhSachSinhVien)) {
            foreach ($danhSachSinhVien as $sinhVien) {
                // update 3 môn
                // 885, 887, 886
                // $monthi = null;
                // switch ($mh) {
                //     case 885:
                //         $monthi = $sinhVien['chinhtri'];
                //         break;
                //     case 886:
                //         $monthi = $sinhVien['lythuyet'];
                //         break;
                //     case 887:
                //         $monthi = $sinhVien['thuchanh'];
                //         break;
                //     default:
                //         $monthi = null;
                //         break;
                // }

                // Cập nhật điểm Chính trị
                if ($mh_id[0] === 885) {
                    if (DB::table('qlsv_dotthi_diem')->where('dt_bd_id', $bangDiem[1]->dt_bd_id)->where('sv_id', $sinhVien['sv_id'])->exists()) {
                        DB::table('qlsv_dotthi_diem')->where('dt_bd_id', $bangDiem[1]->dt_bd_id)->where('sv_id', $sinhVien['sv_id'])->update([
                            'svd_first' => $sinhVien['chinhtri']
                        ]);
                    }
                }

                // Cập nhật điểm Lý thuyết
                if ($mh_id[2] === 886) {
                    if (DB::table('qlsv_dotthi_diem')->where('dt_bd_id', $bangDiem[0]->dt_bd_id)->where('sv_id', $sinhVien['sv_id'])->exists()) {
                        DB::table('qlsv_dotthi_diem')->where('dt_bd_id', $bangDiem[0]->dt_bd_id)->where('sv_id', $sinhVien['sv_id'])->update([
                            'svd_first' => $sinhVien['lythuyet']
                        ]);
                    }
                }

                // Cập nhật điểm Thực hành
                if ($mh_id[1] === 887) {
                    if (DB::table('qlsv_dotthi_diem')->where('dt_bd_id', $bangDiem[2]->dt_bd_id)->where('sv_id', $sinhVien['sv_id'])->exists()) {
                        DB::table('qlsv_dotthi_diem')->where('dt_bd_id', $bangDiem[2]->dt_bd_id)->where('sv_id', $sinhVien['sv_id'])->update([
                            'svd_first' => $sinhVien['thuchanh']
                        ]);
                    }
                }
            }
        }


        // dd($bangDiem);

        // DB::transaction(function () use ($request, $lh_id) {
        //     $bd_type = $request->bd_type;
        //     $mh_id = $request->mh_id;
        //     $dt_id = $request->dt_id;
        //     $bangDiem = DotThiBangDiem::whereLhId($lh_id)
        //         ->whereMhId($mh_id)
        //         ->whereDtId($dt_id)
        //         ->first();
        //     if (!$bangDiem) {
        //         $bangDiem = new DotThiBangDiem;
        //         $bangDiem->fill($request->only(['lh_id', 'mh_id', 'dt_id']));
        //     }
        //     $bangDiem->fill($request->only(['dt_bd_tungay', 'dt_bd_denngay']));
        //     $bangDiem->save();
        //     $danhSachSinhVien = $request->data;
        //     if (is_array($danhSachSinhVien)) {
        //         foreach ($danhSachSinhVien as $sinhVien) {
        //             if (DB::table('qlsv_dotthi_diem')->whereDtBdId($bangDiem->dt_bd_id)->whereSvId($sinhVien['sv_id'])->exists()) {
        //                 DB::table('qlsv_dotthi_diem')->whereDtBdId($bangDiem->dt_bd_id)->whereSvId($sinhVien['sv_id'])->update([
        //                     'svd_dulop' => $sinhVien['svd_dulop'],
        //                     'svd_first' => $sinhVien['svd_first'],
        //                     'svd_second' => $sinhVien['svd_second'],
        //                     'svd_ghichu' => $sinhVien['svd_ghichu'],
        //                     'svd_final' => $sinhVien['svd_final'],
        //                     'svd_second_hocky' => isset($sinhVien['svd_second_hocky']) ? $sinhVien['svd_second_hocky'] : null,
        //                     'svd_total' => isset($sinhVien['svd_total']) ? $sinhVien['svd_total'] : null,
        //                 ]);
        //             } else {
        //                 DB::table('qlsv_dotthi_diem')->insert([
        //                     'dt_bd_id' => $bangDiem->dt_bd_id,
        //                     'sv_id' => $sinhVien['sv_id'],
        //                     'svd_dulop' => $sinhVien['svd_dulop'],
        //                     'svd_first' => $sinhVien['svd_first'],
        //                     'svd_second' => $sinhVien['svd_second'],
        //                     'svd_ghichu' => $sinhVien['svd_ghichu'],
        //                     'svd_final' => $sinhVien['svd_final'],
        //                     'svd_second_hocky' => isset($sinhVien['svd_second_hocky']) ? $sinhVien['svd_second_hocky'] : null,
        //                     'svd_total' => isset($sinhVien['svd_total']) ? $sinhVien['svd_total'] : null,
        //                 ]);
        //             }
        //         }
        //     }
        // });
        return $request->all();
    }

    public function ketQuaHocTap(Request $request, $lhId)
    {
        $reqSemester = $request->semester ?: 1;
        $export = new KetQuaHocTap;
        $data = $export->getKetQuaHocTap($reqSemester, $lhId, 0, true);



        // dd($data['danhSachSinhVien'][0]);
        //$sinhVien->notes
        $reqSemester = $data['reqSemester'];
        $reqYear = $data['reqYear'];
        $lopHoc = $data['lopHoc'];
        $danhSachSinhVien = $data['danhSachSinhVien'];
        // if ($request->sorttotnghiep) {
        //     foreach ($danhSachSinhVien as $sinhVien) {
        //         //sắp sếp
        //     }
        // }

        // Nếu toàn khóa
        if ($reqSemester == '123456') {
            // Lặp qua danh sách sinh viên
            foreach ($danhSachSinhVien as $sinhVien) {
                // Kiểm tra xem $sinhVien có thuộc tính toanKhoa và avg đã được định nghĩa không
                if (isset($sinhVien->toanKhoa->avg)) {
                    // Xác định không còn sinh viên nào nợ môn nên avg = tichLuy
                    $sinhVien->toanKhoa->avg = $sinhVien->toanKhoa->tichLuy;
                }
            }
        }

        $danhSachNamHoc = $data['danhSachNamHoc'];
        $semesters = $data['semesters'];
        $sumTinChi = $data['sumTinChi'];

        $note = $data['notes'];
        // Thêm ghi chú
        $additionalItems = collect([
            ['key' => 'TL', 'value' => 'Thi lại'],
            ['key' => 'HL', 'value' => 'Học lại'],
        ]);
        $note = $note->concat($additionalItems);

        $chunkedNotes = $note->splitIn(3);

        $danhSachDotThi = DotThi::orderBy('dt_id', 'desc')->get();

        $parentUrl = session('parent_url:nhap-diem', '/nhap-diem');
        //dd($danhSachSinhVien[0]);
        return view('qlsv.nhapdiem.nhapdiem_ketquahoctap', compact([
            'parentUrl',
            'reqSemester',
            'reqYear',
            'lopHoc',
            'danhSachSinhVien',
            'danhSachNamHoc',
            'semesters',
            'chunkedNotes',
            'sumTinChi',
            'danhSachDotThi',
            'lhId'
        ]));
    }

    public function ketQuaHocTapAPI(Request $request, $lhId)
    {
        $reqSemester = $request->semester ?: 1;
        $export = new KetQuaHocTap;
        $data = $export->getKetQuaHocTap($reqSemester, $lhId);

        $reqSemester = $data['reqSemester'];
        $reqYear = $data['reqYear'];
        $lopHoc = $data['lopHoc'];
        $danhSachSinhVien = $data['danhSachSinhVien'];
        $danhSachNamHoc = $data['danhSachNamHoc'];
        $semesters = $data['semesters'];
        $sumTinChi = $data['sumTinChi'];
        $chunkedNotes = $data['notes']->splitIn(3);

        $results = compact([
            'reqSemester',
            'reqYear',
            'lopHoc',
            'danhSachSinhVien',
            'danhSachNamHoc',
            'semesters',
            'chunkedNotes',
            'sumTinChi'
        ]);
        return response()->json($results);
    }

    public function kiemTraDiem(Request $request)
    {
        $configSv = DB::table('qlsv_config')
            ->where('qlsv_config.name', 'allow-update-info-hssv')
            ->select('status')
            ->first();

        $configSv = $configSv->status;

        return view('qlsv.nhapdiem.nhapdiem_kiemtradiem', compact(['configSv']));
    }

    public function nhapDotThi(Request $request)
    {
        $dsSinhVien = $request->sinhVien;
        $lopHoc = $request->lopHoc;
        $dotThi = $request->dotThi;
        $modalDotThi = DotThi::find($dotThi);
        if ($modalDotThi->dt_loai == 1) {
            $danhSachMonHoc = DB::table('qlsv_lophoc as lh')
                ->join('qlsv_khoadaotao_monhoc as kdt', function ($join) {
                    $join->on('kdt.kdt_id', '=', 'lh.kdt_id');
                })
                ->join('qlsv_monhoc as mh', 'mh.mh_id', '=', 'kdt.mh_id')
                ->where('lh.lh_id', $lopHoc)
                ->whereRaw('mh.mh_loai != 1 AND mh_loai != 5')
                ->select('mh.mh_id', 'mh.mh_ten')
                ->get();
        } else {
            $danhSachMonHoc = DB::table('qlsv_lophoc as lh')
                ->join('qlsv_khoadaotao_monhoc as kdt', function ($join) {
                    $join->on('kdt.kdt_id', '=', 'lh.kdt_id');
                })
                ->join('qlsv_monhoc as mh', 'mh.mh_id', '=', 'kdt.mh_id')
                ->where('lh.lh_id', $lopHoc)
                ->whereRaw('mh.mh_loai = 5')
                ->select('mh.mh_id', 'mh.mh_ten')
                ->get();
        }

        foreach ($danhSachMonHoc as $mhIndex => $monHoc) {
            $dt_bd = DB::table('qlsv_dotthi_bangdiem as bd')
                ->where('bd.lh_id', $lopHoc)
                ->where('bd.mh_id', $monHoc->mh_id)
                ->where('bd.dt_id', $dotThi)->first();
            if ($dt_bd == null) {
                $dt_bd = new DotThiBangDiem;
                $dt_bd->lh_id = $lopHoc;
                $dt_bd->dt_id = $dotThi;
                $dt_bd->userid = auth()->user()->user_id;
                $dt_bd->mh_id = $monHoc->mh_id;
                $dt_bd->save();
            }

            foreach ($dsSinhVien as $svIndex => $sinhVien) {
                DB::insert(
                    'insert into qlsv_dotthi_diem (dt_bd_id,sv_id) values (?, ?)',
                    [$dt_bd->dt_bd_id, $sinhVien['sv_id']]
                );
            }
        }
    }

    /**
     * Undocumented function
     * @param \Illuminate\Http\Request $request
     * @return void
     * @author ttdat
     * @version 1.0
     */
    public function exportKetQuaHocTap(Request $request, $lh_id)
    {
        $export = new KetQuaHocTap;
        return $export->download($request->semester, $lh_id);
    }
}
