<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use App\Models\DotThi;
use App\Models\LopHoc;
use App\Models\DotXetTotNghiep;
use App\Models\DotThiBangDiem;
use App\Http\Requests\DotThiEditRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Excels\KetQuaHocTap;
use App\Models\DotThiDotXetTotNghiep;
use App\Models\QuyetDinh;
use App\Models\User;
use Illuminate\Support\Facades\Session;

class DotThiController extends Controller
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

        session(['parent_url:dot-thi' => $request->fullUrl()]);
        return view('qlsv.dotthi.dotthi_list', compact('permissions'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('qlsv.dotthi.dotthi_create');
    }

    // T.Phong ->>>
    public function paginate(Request $request)
    {
        $search = $request->search;
        $dt_loai = $request->loai;
        $dt_ketthuc = $request->trangthai;
        $dt_tunam = $request->tunam;
        $dt_dennam = $request->dennam;
        $hdt_id = $request->chuongtrinh;
        $dt_id = $request->dt_id; // Thêm biến cho dt_id


        $danhSachDotThi = DotThi::withExists(['dotThiBangDiem'])
            ->with('dotXetTotNghiep')
            ->where(function ($builder) use ($search, $dt_id) {
                if ($search) {
                    $builder->whereRaw('lower(qlsv_dotthi.dt_ten) like lower(?)', "%$search%");
                }
                if ($dt_id) {
                    $builder->where('qlsv_dotthi.dt_id', $dt_id);
                }
            })
            ->where(function ($builder) use ($dt_loai) {
                if (isset($dt_loai) && $dt_loai != -1) {
                    $builder->whereRaw('qlsv_dotthi.dt_loai = ?', "$dt_loai");
                }
            })
            ->where(function ($builder) use ($dt_ketthuc) {
                if (isset($dt_ketthuc) && $dt_ketthuc != -1) {
                    $builder->whereRaw('qlsv_dotthi.dt_ketthuc = ?', "$dt_ketthuc");
                }
            })
            ->where(function ($builder) use ($dt_tunam) {
                if (isset($dt_tunam)) {
                    $builder->whereRaw('qlsv_dotthi.dt_tunam = ' . $dt_tunam);
                }
            })
            ->where(function ($builder) use ($dt_dennam) {
                if (isset($dt_dennam)) {
                    $builder->whereRaw('qlsv_dotthi.dt_dennam = ' . $dt_dennam);
                }
            })
            ->where(function ($builder) use ($hdt_id) {
                if (isset($hdt_id) && $hdt_id != -1) {
                    if ($hdt_id == 4) {
                        if (auth()->user()->hasPermission('caodang')) {
                            $builder->whereRaw('qlsv_dotthi.dt_hdt_id = ?', 4);
                        }
                    } else if ($hdt_id == 5) {
                        if (auth()->user()->hasPermission('trungcap')) {
                            $builder->whereRaw('qlsv_dotthi.dt_hdt_id = ?', 5);
                        }
                    }
                } else {
                    if (auth()->user()->hasPermission('caodang')) {
                        $builder->whereRaw('qlsv_dotthi.dt_hdt_id = ?', 4);
                    } else if (auth()->user()->hasPermission('trungcap')) {
                        $builder->whereRaw('qlsv_dotthi.dt_hdt_id = ?', 5);
                    }
                }
            })
            ->orderBy('qlsv_dotthi.dt_id', 'desc')
            ->paginate(10)
            ->setPath(route('dot-thi.index'))
            ->onEachSide(2);


        foreach ($danhSachDotThi as $dotthi) {
            if ($dotthi->qd_id != null) {
                $qlsv_quyetdinh = DB::table('qlsv_quyetdinh')
                    ->where('qd_id', '=', $dotthi->qd_id)
                    ->where('qd_loai', '=', 3)
                    ->select('qd_ma', 'qd_ten', 'qd_ngay')
                    ->get();

                if (count($qlsv_quyetdinh) > 0) {
                    $dotthi->qd_ma = $qlsv_quyetdinh[0]->qd_ma;
                    $dotthi->qd_ten = $qlsv_quyetdinh[0]->qd_ten;
                    $dotthi->qd_ngay = $qlsv_quyetdinh[0]->qd_ngay;
                }
            }

            $dotthi->chitiet_url = route('dot-thi.detail', $dotthi);
        }

        return response()->json($danhSachDotThi);
    }

    // T.Phong  --------

    public function xoaLopHocKhoiDotThi($lh_id, $dt_id)
    {
        DB::transaction(function () use ($lh_id, $dt_id) {

            $danhSachXoa = DB::table('qlsv_dotthi_bangdiem')
                ->where('dt_id', '=', $dt_id)
                ->where('lh_id', '=', $lh_id)
                ->select('dt_bd_id')
                ->get();
            foreach ($danhSachXoa as $bd) {
                DB::table('qlsv_dotthi_bangdiem')
                    ->where('dt_bd_id', '=', $bd->dt_bd_id)
                    ->delete();
                DB::table('qlsv_dotthi_diem')
                    ->where('dt_bd_id', '=', $bd->dt_bd_id)
                    ->delete();
            }
            return response()->json($danhSachXoa);
        });
    }

    public function exportBanDiemTungMonVaDotThi(Request $request, $dt_id)
    {
        $export = new KetQuaHocTap;
        $lh_id = $request->lh_id;

        return $export->downloadMonHocDotThi($dt_id, $lh_id);
    }

    public function exportBanDiemTungMonVaDotThiFull(Request $request, $dt_id)
    {
        $export = new KetQuaHocTap;
        $lh_id = $request->lh_id;

        return $export->downloadMonHocDotThiFull($dt_id, $lh_id);
    }

    public function exportDanhSachXetThiTotNghiep(Request $request, $dt_id)
    {
        $export = new KetQuaHocTap;
        $lh_id = $request->lh_id;

        return $export->downloadDanhSachXetThiTotNghiep($dt_id, $lh_id);
    }

    public function exportDiemDotThiTheoLop(Request $request, $dt_id)
    {
        $export = new KetQuaHocTap;
        $lh_id = $request->lh_id;
        $danhSachNganhNghe = DB::table('qlsv_lophoc as lh')
            ->join('qlsv_dotthi_bangdiem as bd', function ($join) {
                $join->on('bd.lh_id', '=', 'lh.lh_id');
            })
            ->join('qlsv_dotthi as dt', function ($join) {
                $join->on('bd.dt_id', '=', 'dt.dt_id');
            })
            ->join('qlsv_khoadaotao as kdt', function ($join) {
                $join->on('kdt.kdt_id', '=', 'lh.kdt_id');
            })
            ->join('qlsv_nganhnghe as nn', function ($join) {
                $join->on('nn.nn_id', '=', 'kdt.nn_id');
            })
            ->where('bd.dt_id', '=', $dt_id)
            ->where('bd.lh_id', '=', $lh_id)
            ->select(DB::raw('DISTINCT nn.*, dt.*, lh.*, kdt.*'))
            ->get();
        foreach ($danhSachNganhNghe as $nn) {
            $danhSachSinhVienLop = DB::table('qlsv_dotthi_bangdiem as bd')
                ->join('qlsv_dotthi_diem as svd', function ($join) {
                    $join->on('svd.dt_bd_id', '=', 'bd.dt_bd_id');
                })
                ->join('qlsv_sinhvien as sv', function ($join) {
                    $join->on('svd.sv_id', '=', 'sv.sv_id');
                })
                ->join('qlsv_lophoc as lh', function ($join) {
                    $join->on('bd.lh_id', '=', 'lh.lh_id');
                })
                ->join('qlsv_khoadaotao as kdt', function ($join) {
                    $join->on('kdt.kdt_id', '=', 'lh.kdt_id');
                })
                ->whereRaw('kdt.nn_id = ' . $nn->nn_id . ' AND bd.dt_id =' . $dt_id)
                // ->where(function ($builder) use ($search) {
                //     $builder->whereRaw("lower(sv.sv_ho) like lower('%$search%') OR lower(sv.sv_ten) like lower('%$search%') OR lower(sv.sv_ma) like lower('%$search%') OR lower(lh.lh_ten) like lower('%$search%') OR lower(lh.lh_ma) like lower('%$search%')");
                // })
                // ->where(function ($builder) use ($loai) {
                //     if(isset($loai)){
                //         $builder->whereRaw('svd.svd_dieukien = '.($loai=='true'?'1':'0'));
                //     } else {
                //         $builder->whereRaw('svd.svd_dieukien = 1');
                //     }
                // })
                ->select(DB::raw('DISTINCT sv.*, svd.svd_dieukien, svd.svd_loai'))
                ->get();
            $nn->danhsachsinhvien = $danhSachSinhVienLop;
        }
        return $export->downloadDiemDotThiTheoLop($danhSachNganhNghe);
    }

    public function exportDanhSachThiLai(Request $request, $dt_id)
    {
        $reqSemester = 123456;
        $export = new KetQuaHocTap;
        $search = $request->search;
        $loai = $request->loai;
        $typeExcel = $request->type_excel;

        // $khoaDaoTao = $lopHoc->khoaDaoTao;

        // $dxtn_id = DotThiDotXetTotNghiep::where('dt_id', $dt_id)->first()->dxtn_id;

        $danhSachNganhNghe = DB::table('qlsv_lophoc as lh')
            ->join('qlsv_dotthi_bangdiem as bd', function ($join) {
                $join->on('bd.lh_id', '=', 'lh.lh_id');
            })
            ->join('qlsv_khoadaotao as kdt', function ($join) {
                $join->on('kdt.kdt_id', '=', 'lh.kdt_id');
            })
            ->join('qlsv_nganhnghe as nn', function ($join) {
                $join->on('nn.nn_id', '=', 'kdt.nn_id');
            })
            ->where('bd.dt_id', '=', $dt_id)
            ->select(DB::raw('DISTINCT nn.*, bd.dt_id, bd.lh_id, kdt.*'))
            ->get();

        // Mảng để lưu trữ các bản ghi duy nhất
        $uniqueRecords = [];
        foreach ($danhSachNganhNghe as $result) {
            // Sử dụng trường nn_ma làm khóa để kiểm tra trùng lặp
            $nnMa = $result->nn_ma;

            // Nếu chưa tồn tại trong mảng $uniqueRecords, thêm vào
            if (!isset($uniqueRecords[$nnMa])) {
                $uniqueRecords[$nnMa] = $result;
            }
        }
        // Chuyển mảng kết quả duy nhất thành collection để tiện sử dụng
        $danhSachNganhNghe = collect($uniqueRecords)->values();


        foreach ($danhSachNganhNghe as $index => $nn) {
            if ($typeExcel != 0) {
                $danhSachSinhVienLop = DB::table('qlsv_dotthi_bangdiem as bd')
                    ->join('qlsv_dotthi_diem as svd', function ($join) {
                        $join->on('svd.dt_bd_id', '=', 'bd.dt_bd_id');
                    })
                    ->join('qlsv_sinhvien as sv', function ($join) {
                        $join->on('svd.sv_id', '=', 'sv.sv_id');
                    })
                    ->join('qlsv_lophoc as lh', function ($join) {
                        $join->on('bd.lh_id', '=', 'lh.lh_id');
                    })
                    ->join('qlsv_khoadaotao as kdt', function ($join) {
                        $join->on('kdt.kdt_id', '=', 'lh.kdt_id');
                    })
                    ->whereRaw('kdt.nn_id = ' . $nn->nn_id . ' AND bd.dt_id =' . $dt_id)
                    // ->where(function ($builder) use ($search) {
                    //     $builder->whereRaw("lower(sv.sv_ho) like lower('%$search%') OR lower(sv.sv_ten) like lower('%$search%') OR lower(sv.sv_ma) like lower('%$search%') OR lower(lh.lh_ten) like lower('%$search%') OR lower(lh.lh_ma) like lower('%$search%')");
                    // })
                    ->where(function ($builder) use ($loai) {
                        if (isset($loai)) {
                            if ($loai == 'true') {
                                $builder->whereRaw('svd.svd_dieukien = 1');
                            } else {
                                $builder->whereRaw('svd.svd_dieukien = 0 and svd.svd_khongdatloai != 2');
                            }
                        } else {
                            $builder->whereRaw('svd.svd_dieukien = 1');
                        }
                    })
                    ->select(DB::raw('DISTINCT sv.*, lh.*, kdt.*, svd.svd_dieukien, svd.svd_loai'))
                    ->get();
            } else {
                $danhSachSinhVienLop = DB::table('qlsv_dotthi_bangdiem as bd')
                    ->join('qlsv_dotthi_diem as svd', function ($join) {
                        $join->on('svd.dt_bd_id', '=', 'bd.dt_bd_id');
                    })
                    ->join('qlsv_sinhvien as sv', function ($join) {
                        $join->on('svd.sv_id', '=', 'sv.sv_id');
                    })
                    ->join('qlsv_lophoc as lh', function ($join) {
                        $join->on('bd.lh_id', '=', 'lh.lh_id');
                    })
                    ->join('qlsv_khoadaotao as kdt', function ($join) {
                        $join->on('kdt.kdt_id', '=', 'lh.kdt_id');
                    })
                    ->whereRaw('kdt.nn_id = ' . $nn->nn_id . ' AND bd.dt_id =' . $dt_id)
                    ->where(function ($builder) use ($loai) {
                        $builder->whereRaw('svd.svd_dieukien = 1')->whereNull('svd.svd_khongdatloai');
                    })
                    ->select(DB::raw('DISTINCT sv.*, lh.*, svd.svd_dieukien, svd.svd_loai'))
                    ->get();
            }

            foreach ($danhSachSinhVienLop as $svl) {
                $lh_id = $svl->lh_id;
                $sv_id = $svl->sv_id;

                if ($typeExcel != 0) {
                    $data = $export->getKetQuaHocTap($reqSemester, $lh_id, $sv_id);
                    $danhSachSinhVien = $data['danhSachSinhVien'];

                    foreach ($danhSachSinhVien as $sinhVienindex => $sinhVien) {
                        foreach ($sinhVien->years as $year) {
                            foreach ($year->semesters as $semester) {
                                $diemTkMonChuaDat = false;
                                foreach ($semester->monHoc as $mdIndex => $monHoc) {
                                    if ($monHoc->ketQua && $monHoc->ketQua->display_score < 5) {
                                        $diemTkMonChuaDat = true;
                                    }
                                }
                                $svl->diemTkMonChuaDat = $diemTkMonChuaDat;
                            }
                        }
                    }
                }

                // dd($danhSachSinhVien );

                // if ($typeExcel == 0) {
                //     $filter = new Filters;
                //     $lopHoc = LopHoc::find($lh_id);

                //     $danhSachSinhVien = $filter->SinhVienThiLaiTN($danhSachSinhVien, $lh_id, $dt_id, $lopHoc);

                //     // join bảng mới có svd_loai
                // }

            }

            $nn->danhsachsinhvien = $danhSachSinhVienLop;
        }


        if ($typeExcel == 0) {
            return $export->downloadSVThiLai($danhSachNganhNghe->values());
        } else if ($typeExcel == 1) {
            return $export->downloadSVDuDieuKienKhongDuDieuKien($danhSachNganhNghe->values(), 1);
        } else if ($typeExcel == 2) {
            return $export->downloadSVDuDieuKienKhongDuDieuKien($danhSachNganhNghe->values(), 0);
        } else {
            return $export->downloadSVThiLai($danhSachNganhNghe->values());
        }
    }

    public function xemChiTietDotThiMonHocLop($lh_id, $dt_id)
    {
        $dotthi = DotThi::find($dt_id);
        $quyetDinh = false;
        if ($dotthi->dt_qd_trangthai == 1) {
            $quyetDinh = true;
        }

        $lophoc = LopHoc::find($lh_id);
        $url = route('dot-thi.nhap-diem-excel', ['lh_id' => $lophoc->lh_id, 'dt_id' => $dotthi->dt_id]);
        return view('qlsv.dotthi.dsmonhoc_dotthi_lop', compact(['dotthi', 'lophoc', 'url', 'quyetDinh']));
    }

    public function capNhatLoaiThiChoSinhVien(Request $request, $dt_id)
    {
        DB::transaction(function () use ($request, $dt_id) {
            foreach ($request->data as $sv) {
                if (isset($sv['sv_id'])) {
                    $danhSachBD = DB::table('qlsv_dotthi_bangdiem as bd')
                        ->join('qlsv_dotthi_diem as svd', function ($join) {
                            $join->on('svd.dt_bd_id', '=', 'bd.dt_bd_id');
                        })
                        ->join('qlsv_monhoc as mh', function ($join) {
                            $join->on('mh.mh_id', '=', 'bd.mh_id');
                        })
                        ->whereRaw('bd.lh_id = ' . $sv['lh_id'] . ' AND svd.sv_id = ' . $sv['sv_id'] . ' AND bd.dt_id =' . $dt_id)
                        ->select(DB::raw('svd.dt_bd_id, mh.mh_id, mh.mh_loai, svd.svd_lan'))
                        ->get();

                    if (isset($danhSachBD) && !empty($danhSachBD)) {
                        foreach ($danhSachBD as $bdIndex => $bd) {
                            // xóa nếu loại thi và loại môn thi không khớp
                            if (($sv['svd_loai'] == 0 && $bd->mh_loai == 5) || ($sv['svd_loai'] == 1 && $bd->mh_loai != 5)) {

                                DB::table('qlsv_dotthi_diem')
                                    ->where('dt_bd_id', '=', $bd->dt_bd_id)
                                    ->whereSvId($sv['sv_id'])
                                    ->where('dt_id', $dt_id)
                                    ->delete();
                            }
                        }


                        $dothimoi = DB::table('qlsv_dotthi_bangdiem as bd')
                            ->join('qlsv_monhoc as mh', function ($join) {
                                $join->on('mh.mh_id', '=', 'bd.mh_id');
                            })
                            ->where('bd.dt_id', $dt_id)
                            ->where('bd.lh_id', $sv['lh_id'])
                            ->where(function ($builder) use ($sv) {
                                if ($sv['svd_loai'] == 0) {
                                    $builder->whereRaw("mh.mh_loai != 5");
                                } else {
                                    $builder->whereRaw("mh.mh_loai = 5");
                                }
                            })
                            ->selectRaw('bd.dt_bd_id')->get();
                        foreach ($dothimoi as $dt) {
                            $tontai = DB::table('qlsv_dotthi_diem as d')
                                ->where('d.dt_bd_id', $dt->dt_bd_id)
                                ->where('d.sv_id', $sv['sv_id'])
                                ->where('d.dt_id', $dt_id)
                                ->selectRaw('Count(1) > 0 as tontai')->first();

                            if (!isset($tontai->tontai) || $tontai->tontai != 1) {
                                $diem = DB::table('qlsv_dotthi_diem as d')
                                    ->join('qlsv_dotthi_bangdiem as bd', 'bd.dt_bd_id', '=', 'd.dt_bd_id')
                                    ->where('bd.mh_id', $bd->mh_id)
                                    ->where('d.sv_id', $sv['sv_id'])
                                    ->orderBy('d.svd_lan', 'desc')
                                    ->selectRaw('d.svd_lan')->first();
                                $solanthi = !isset($diem->svd_lan) || $diem->svd_lan == null ? 1 : $diem->svd_lan + 1;
                                //Thêm loại thi phù hợp
                                DB::insert(
                                    'insert into qlsv_dotthi_diem (dt_bd_id, dt_id, sv_id, svd_dieukien, svd_lan, svd_loai) values (?, ?, ?, ?, ?, ?)',
                                    [$dt->dt_bd_id, $dt_id, $sv['sv_id'], $sv['svd_dieukien'], $solanthi, $sv['svd_loai']]
                                );
                            }
                        }
                    }
                }
            }
        });
    }

    public function getDanhSachSinhVienTheoDotThi($dt_id, Request $request)
    {
        $search = $request->search;
        $loai = $request->loai;
        $loai_thi = $request->loai_thi;
        $danhSachNganhNghe = DB::table('qlsv_lophoc as lh')
            ->join('qlsv_dotthi_bangdiem as bd', function ($join) {
                $join->on('bd.lh_id', '=', 'lh.lh_id');
            })
            ->join('qlsv_khoadaotao as kdt', function ($join) {
                $join->on('kdt.kdt_id', '=', 'lh.kdt_id');
            })
            ->join('qlsv_nganhnghe as nn', function ($join) {
                $join->on('nn.nn_id', '=', 'kdt.nn_id');
            })
            ->where('bd.dt_id', '=', $dt_id)
            ->select(DB::raw('DISTINCT nn_ma, nn.*, nn.nn_ten, nn.nn_ma, nn.nn_id, bd.created_at, kdt.*'))
            ->get();

        // $danhSachNganhNghe = collect($danhSachNganhNghe)->sortByDesc('created_at');

        // Mảng để lưu trữ các bản ghi duy nhất
        $uniqueRecords = [];
        foreach ($danhSachNganhNghe as $result) {
            // Sử dụng trường nn_ma làm khóa để kiểm tra trùng lặp
            $nnMa = $result->nn_ma;

            // Nếu chưa tồn tại trong mảng $uniqueRecords, thêm vào
            if (!isset($uniqueRecords[$nnMa])) {
                $uniqueRecords[$nnMa] = $result;
            }
        }
        // Chuyển mảng kết quả duy nhất thành collection để tiện sử dụng
        $danhSachNganhNghe = collect($uniqueRecords)->values();


        foreach ($danhSachNganhNghe as $nn) {
            $danhSachSinhVienLop = DB::table('qlsv_dotthi_bangdiem as bd')
                ->join('qlsv_dotthi_diem as svd', function ($join) {
                    $join->on('svd.dt_bd_id', '=', 'bd.dt_bd_id');
                })
                ->join('qlsv_sinhvien as sv', function ($join) {
                    $join->on('svd.sv_id', '=', 'sv.sv_id');
                })
                ->join('qlsv_lophoc as lh', function ($join) {
                    $join->on('bd.lh_id', '=', 'lh.lh_id');
                })
                ->join('qlsv_khoadaotao as kdt', function ($join) {
                    $join->on('kdt.kdt_id', '=', 'lh.kdt_id');
                })
                ->join('qlsv_monhoc as mh', function ($join) {
                    $join->on('mh.mh_id', '=', 'bd.mh_id');
                })
                ->whereRaw('kdt.nn_id = ' . $nn->nn_id . ' AND bd.dt_id =' . $dt_id)
                ->where(function ($builder) use ($search) {
                    $builder->whereRaw("lower(sv.sv_ho) like lower('%$search%') OR lower(sv.sv_ten) like lower('%$search%') OR lower(sv.sv_ma) like lower('%$search%') OR lower(lh.lh_ten) like lower('%$search%') OR lower(lh.lh_ma) like lower('%$search%')");
                })
                ->where(function ($builder) use ($loai) {
                    if (isset($loai)) {
                        if ($loai == 2) {
                            $builder->whereRaw('svd.svd_khongdatloai = 2');
                        } else {
                            if ($loai == 'true') {
                                $builder->whereRaw('svd.svd_dieukien = 1');
                            } else {
                                $builder->whereRaw('svd.svd_dieukien = 0')->WhereRaw('svd.svd_khongdatloai != 2');
                            }
                        }
                    } else {
                        $builder->whereRaw('svd.svd_dieukien = 1');
                    }
                })
                ->where(function ($builder) use ($loai_thi) {
                    if (isset($loai_thi) && $loai_thi != -1) {
                        $builder->whereRaw($loai_thi == 0 ? 'mh.mh_loai = 5' : 'mh.mh_loai != 5');
                    }
                })
                ->select(DB::raw('DISTINCT sv.*, lh.*, kdt.kdt_khoa, svd.svd_ghichu, svd.svd_dieukien, svd.svd_lan, svd.svd_loai, svd_khongdatloai'))
                ->get();

            $nn->danhsachsinhvien = $danhSachSinhVienLop;
        }

        return response()->json($danhSachNganhNghe);
    }


    public function getAllDotThi(Request $request)
    {
        $chuongtrinh = $request->chuongtrinh;

        $danhSachDotThi = DotThi::orderBy('dt_id', 'desc')
            ->where('dt_qd_trangthai', '=', '0')
            ->where('dt_hdt_id', '=', $chuongtrinh)
            ->get();
        return response()->json($danhSachDotThi);
    }

    public function getDotThiChoDotXetTN(Request $request)
    {
        $chuongtrinh = $request->chuongtrinh;

        $danhSachDotThi = DotThi::orderBy('dt_id', 'desc')
            ->where('dt_hdt_id', '=', $chuongtrinh)
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('qlsv_dotthi_dotxettotnghiep')
                    ->whereRaw('qlsv_dotthi_dotxettotnghiep.dt_id = qlsv_dotthi.dt_id');
            })
            ->get();

        return response()->json($danhSachDotThi);
    }

    public function xemDanhSachSinhVienThamGiaDotThi($id)
    {
        $dotthi = DotThi::find($id);
        $quyetDinh = false;
        // Có quyết định rồi sẽ có quyền nhập điểm
        if ($dotthi->dt_qd_trangthai == 0 && $dotthi->dt_ketthuc == 0) {
            $quyetDinh = true;
        } else if ($dotthi->dt_qd_trangthai == 1 || $dotthi->dt_ketthuc == 1) {
            $quyetDinh = false;
        }

        return view('qlsv.dotthi.dotthi_sinhvienthamgia', compact(['dotthi', 'quyetDinh']));
    }

    public function xemChiTietDotThi($id)
    {
        $dotthi = DotThi::find($id);

        $quyetDinh = false;
        if ($dotthi->dt_qd_trangthai == 0 && $dotthi->dt_ketthuc == 0) {
            $quyetDinh = true;
        } else if ($dotthi->dt_qd_trangthai == 1 || $dotthi->dt_ketthuc == 1) {
            $quyetDinh = false;
        }
        $parentUrl = session('parent_url:dot-thi', '/dot-thi');
        return view('qlsv.dotthi.dotthi_chitiet', compact(['dotthi', 'quyetDinh', 'parentUrl']));
    }

    public function preNhapDiemExcel($lh_id, $dt_id)
    {
        $lophoc = LopHoc::find($lh_id);
        return view('qlsv.dotthi.dotthi_import_diem', compact(['dt_id', 'lh_id', 'lophoc']));
    }

    public function nhapDiemExcel(Request $request, $dt_id)
    {
        $lopHoc = LopHoc::find($request->lh_id);
        if ($lopHoc->lh_nienche == 1) {
            $validator = Validator::make($request->all(), [
                'data.*.khoaluan' => 'nullable|gte:0|lte:10|numeric',
                'data.*.lythuyet' => 'nullable|gte:0|lte:10|numeric',
                'data.*.thuchanh' => 'nullable|gte:0|lte:10|numeric',
            ]);
        } else {
            $validator = Validator::make($request->all(), [
                'data.*.khoaluan' => 'nullable|gte:0|lte:10|numeric',
                'data.*.chinhtri' => 'nullable|gte:0|lte:10|numeric',
                'data.*.lythuyet' => 'nullable|gte:0|lte:10|numeric',
                'data.*.thuchanh' => 'nullable|gte:0|lte:10|numeric',
            ]);
        }
        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }
        $monThucHanh = DB::table('qlsv_dotthi_bangdiem as bd')
            ->join('qlsv_monhoc as mh', 'mh.mh_id', '=', 'bd.mh_id')
            ->where('bd.lh_id', $lopHoc->lh_id)
            ->where('bd.dt_id', $dt_id)
            ->whereRaw('mh.mh_loai = 3 ')
            ->select('bd.dt_bd_id')
            ->first();
        $monLyThuyet = DB::table('qlsv_dotthi_bangdiem as bd')
            ->join('qlsv_monhoc as mh', 'mh.mh_id', '=', 'bd.mh_id')
            ->where('bd.lh_id', $lopHoc->lh_id)
            ->where('bd.dt_id', $dt_id)
            ->whereRaw('mh.mh_loai = 4 ')
            ->select('bd.dt_bd_id')
            ->first();
        $monChinhTri = DB::table('qlsv_dotthi_bangdiem as bd')
            ->join('qlsv_monhoc as mh', 'mh.mh_id', '=', 'bd.mh_id')
            ->where('bd.lh_id', $lopHoc->lh_id)
            ->where('bd.dt_id', $dt_id)
            ->whereRaw('mh.mh_loai = 2 ')
            ->select('bd.dt_bd_id')
            ->first();
        $monKhoaLuan = DB::table('qlsv_dotthi_bangdiem as bd')
            ->join('qlsv_monhoc as mh', 'mh.mh_id', '=', 'bd.mh_id')
            ->where('bd.lh_id', $lopHoc->lh_id)
            ->where('bd.dt_id', $dt_id)
            ->whereRaw('mh.mh_loai = 5 ')
            ->select('bd.dt_bd_id')
            ->first();
        $dsSinhVien = $request->data;
        DB::transaction(function () use ($dsSinhVien, $lopHoc, $monThucHanh, $monLyThuyet, $monChinhTri, $monKhoaLuan) {
            foreach ($dsSinhVien as $sinhVienTmp) {
                $sinhVien = DB::table('qlsv_sinhvien')
                    ->where('sv_ma', $sinhVienTmp['sv_ma'])
                    ->first();
                if (isset($monThucHanh->dt_bd_id) && DB::table('qlsv_dotthi_diem')->whereDtBdId($monThucHanh->dt_bd_id)->whereSvId($sinhVien->sv_id)->exists()) {
                    DB::table('qlsv_dotthi_diem')->whereDtBdId($monThucHanh->dt_bd_id)->whereSvId($sinhVien->sv_id)
                        ->update([
                            'svd_first' => $sinhVienTmp['thuchanh'],
                        ]);
                }

                if (isset($monLyThuyet->dt_bd_id) && DB::table('qlsv_dotthi_diem')->whereDtBdId($monLyThuyet->dt_bd_id)->whereSvId($sinhVien->sv_id)->exists()) {
                    DB::table('qlsv_dotthi_diem')->whereDtBdId($monLyThuyet->dt_bd_id)->whereSvId($sinhVien->sv_id)
                        ->update([
                            'svd_first' => $sinhVienTmp['lythuyet'],
                        ]);
                }
                if (isset($monKhoaLuan->dt_bd_id) && DB::table('qlsv_dotthi_diem')->whereDtBdId($monKhoaLuan->dt_bd_id)->whereSvId($sinhVien->sv_id)->exists()) {
                    DB::table('qlsv_dotthi_diem')->whereDtBdId($monKhoaLuan->dt_bd_id)->whereSvId($sinhVien->sv_id)
                        ->update([
                            'svd_first' => $sinhVienTmp['khoaluan'],
                        ]);
                }

                if ($lopHoc->lh_nienche == 0 && isset($monChinhTri->dt_bd_id) && DB::table('qlsv_dotthi_diem')->whereDtBdId($monChinhTri->dt_bd_id)->whereSvId($sinhVien->sv_id)->exists()) {
                    DB::table('qlsv_dotthi_diem')->whereDtBdId($monChinhTri->dt_bd_id)->whereSvId($sinhVien->sv_id)
                        ->update([
                            'svd_first' => $sinhVienTmp['chinhtri'],
                        ]);
                }
            }
        });
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(DotThiEditRequest $request)
    {

        $quyetDinhPassedData = $request->only(['qd_id', 'qd_ten', 'qd_ngay', 'qd_ma']);
        $loai = 3;
        $dotThiResult = null; // Biến để lưu kết quả

        DB::transaction(function () use ($loai, $quyetDinhPassedData, $request, &$dotThiResult) {
            $qd_id = $quyetDinhPassedData['qd_id'];

            if ($qd_id == 0) {
                $modelQD = new QuyetDinh();
                $modelQD->fill($quyetDinhPassedData);
                $modelQD->qd_loai = $loai;
                $modelQD->save();
                $qd_id = $modelQD->qd_id;
            }

            $passedData = $request->only(['dt_hdt_id', 'dt_ten', 'dt_loai', 'dt_tungay', 'dt_tuthang', 'dt_tunam', 'dt_denngay', 'dt_denthang', 'dt_dennam', 'dt_ghichu', 'dt_so_ke_hoach']);
            // != -1
            // if ($qd_id > 0) {
            $passedData = array_merge($passedData, ['qd_id' => $qd_id]);
            // }
            $model = new DotThi;
            $model->fill($passedData);
            $model->save();
            $dotThiResult = $model;
        });

        return response()->json($dotThiResult);
    }

    public function capNhatTrangThaiSinhVienThamGia($dt_id, Request $request)
    {
        DB::transaction(function () use ($request, $dt_id) {
            $sv = $request->svid;
            $lh = $request->lhid;
            $loai = $request->loai == 'true' ? 0 : 1;
            $svd_ghichu = $request->svd_ghichu;
            $svd_khongdatloai = $request->svd_khongdatloai == "null" ? NULL : $request->svd_khongdatloai;


            $danhSachBD = DB::table('qlsv_dotthi_bangdiem as bd')
                ->join('qlsv_dotthi_diem as svd', function ($join) {
                    $join->on('svd.dt_bd_id', '=', 'bd.dt_bd_id');
                })
                ->whereRaw('bd.lh_id = ' . $lh . ' AND svd.sv_id = ' . $sv . ' AND bd.dt_id =' . $dt_id)
                ->select(DB::raw('GROUP_CONCAT(DISTINCT svd.dt_bd_id) as ds'))
                ->first();
            if (isset($danhSachBD->ds) && !empty($danhSachBD->ds)) {
                DB::table('qlsv_dotthi_diem')
                    ->whereRaw('dt_bd_id IN (' . $danhSachBD->ds . ')')
                    ->whereSvId($sv)
                    ->update([
                        'svd_ghichu' => $svd_ghichu,
                        'svd_dieukien' => $loai,
                        'svd_khongdatloai' => $svd_khongdatloai
                    ]);
            }
        });
    }

    public function xoaSinhVienThamGia($dt_id, Request $request)
    {
        DB::transaction(function () use ($request, $dt_id) {
            $sv = $request->svid;
            $lh = $request->lhid;
            $danhSachBD = DB::table('qlsv_dotthi_bangdiem as bd')
                ->join('qlsv_dotthi_diem as svd', function ($join) {
                    $join->on('svd.dt_bd_id', '=', 'bd.dt_bd_id');
                })
                ->whereRaw('bd.lh_id = ' . $lh . ' AND svd.sv_id = ' . $sv . ' AND bd.dt_id =' . $dt_id)
                ->select(DB::raw('GROUP_CONCAT(DISTINCT svd.dt_bd_id) as ds'))
                ->first();
            if (isset($danhSachBD->ds) && !empty($danhSachBD->ds)) {
                DB::table('qlsv_dotthi_diem')
                    ->whereRaw('dt_bd_id IN (' . $danhSachBD->ds . ')')
                    ->whereSvId($sv)
                    ->delete();
            }
        });
    }

    public function dongBoMonHocChoKhoaDaoTao()
    {
        $danhSachKhoaDaoTao = DB::table('qlsv_khoadaotao_monhoc as kdt')
            ->select(DB::raw('DISTINCT kdt.kdt_id'))
            ->where('kdt.kdt_mh_hocky', 4)
            ->get();
        foreach ($danhSachKhoaDaoTao as $kdtIndex => $kdt) {
            $tontai = DB::table('qlsv_khoadaotao_monhoc as kdt')
                ->join('qlsv_monhoc as mh', 'mh.mh_id', '=', 'kdt.mh_id')
                ->where('kdt.kdt_id', $kdt->kdt_id)
                ->selectRaw('SUM(CASE WHEN mh.mh_loai = 5 THEN 1 ELSE 0 END) > 0 as tontaiBC,
                    SUM(CASE WHEN mh.mh_loai = 4 THEN 1 ELSE 0 END) > 0 as tontaiLT,
                    SUM(CASE WHEN mh.mh_loai = 3 THEN 1 ELSE 0 END) > 0 as tontaiTH,
                    SUM(CASE WHEN mh.mh_loai = 2 THEN 1 ELSE 0 END) > 0 as tontaiCT,
                    MAX(kdt.kdt_mh_index) kdt_mh_index,
                    MAX(kdt.kdt_mh_hocky) kdt_mh_hocky')
                ->first();
            if ((!isset($tontai->tontaiBC) || $tontai->tontaiBC != 1) && $tontai->kdt_mh_hocky == 4) {
                DB::insert(
                    'insert into qlsv_khoadaotao_monhoc (kdt_id, mh_id, kdt_mh_index, kdt_mh_hocky) values (?, ?, ?, ?)',
                    [$kdt->kdt_id, 894, $tontai->kdt_mh_index + 1, 4]
                );
            }
            if ((!isset($tontai->tontaiLT) || $tontai->tontaiLT != 1) && $tontai->kdt_mh_hocky == 4) {
                DB::insert(
                    'insert into qlsv_khoadaotao_monhoc (kdt_id, mh_id, kdt_mh_index, kdt_mh_hocky) values (?, ?, ?, ?)',
                    [$kdt->kdt_id, 887, $tontai->kdt_mh_index + 1, 4]
                );
            }

            if ((!isset($tontai->tontaiTH) || $tontai->tontaiTH != 1) && $tontai->kdt_mh_hocky == 4) {
                DB::insert(
                    'insert into qlsv_khoadaotao_monhoc (kdt_id, mh_id, kdt_mh_index, kdt_mh_hocky) values (?, ?, ?, ?)',
                    [$kdt->kdt_id, 886, $tontai->kdt_mh_index + 1, 4]
                );
            }
            if ((!isset($tontai->tontaiCT) || $tontai->tontaiCT != 1) && $tontai->kdt_mh_hocky == 4) {
                DB::insert(
                    'insert into qlsv_khoadaotao_monhoc (kdt_id, mh_id, kdt_mh_index, kdt_mh_hocky) values (?, ?, ?, ?)',
                    [$kdt->kdt_id, 885, $tontai->kdt_mh_index + 1, 4]
                );
            }
        }
    }

    public function capNhatDotThiChoLop(Request $request)
    {
        $lopHoc = $request->lh_id;
        $dotThi = $request->dt_id;
        $export = new KetQuaHocTap;
        $danhSachSinhVien = $export->getKetQuaHocTap(123456, $lopHoc)['danhSachSinhVien'];

        $modalDotThi = DotThi::find($dotThi);

        // Tạm thời gán cứng các môn thi TN
        $danhSachMonHoc = DB::table('qlsv_monhoc as mh')
            ->whereIn('mh.mh_id', [885, 886, 887, 894])
            ->whereRaw('mh.mh_loai != 1 ')
            ->select('mh.mh_id', 'mh.mh_ten', 'mh.mh_loai')
            ->get();

        // $danhSachMonHoc = DB::table('qlsv_lophoc as lh')
        //     ->join('qlsv_lophoc_monhoc as lh_mh', function ($join) {
        //         $join->on('lh_mh.lh_id', '=', 'lh.lh_id');
        //     })
        //     ->join('qlsv_monhoc as mh', 'mh.mh_id', '=', 'lh_mh.mh_id')
        //     ->where('lh.lh_id', $lopHoc)
        //     ->whereRaw('mh.mh_loai != 1 ')
        //     ->select('mh.mh_id', 'mh.mh_ten', 'mh.mh_loai')
        //     ->get();


        DB::transaction(function () use ($danhSachMonHoc, $danhSachSinhVien, $lopHoc, $dotThi) {
            $sinhdacapnhat = array();
            $danhSachSinhVienNew = array();
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

                foreach ($danhSachSinhVien as $svIndex => $sinhVien) {
                    // check sv bị xóa tên sẽ không được thêm vào đợt thi
                    $svCoQdXoaTen = DB::table('qlsv_sinhvien_quyetdinh as svqd')
                        ->join('qlsv_quyetdinh as qd', 'qd.qd_id', 'svqd.qd_id')
                        ->where('svqd.sv_id', $sinhVien->sv_id)
                        ->where('qd.qd_loai', 2)
                        ->get();


                    if ($svCoQdXoaTen->count() === 0) {
                        $tontai = DB::table('qlsv_dotthi_diem as d')
                            ->where('d.dt_bd_id', $dt_bd->dt_bd_id)
                            ->where('d.sv_id', $sinhVien->sv_id)
                            ->selectRaw('Count(1) > 0 as tontai')->first();

                        // lần 1 rớt thì thi lần 2
                        // lần 1 đạt thì k thêm vô
                        // kiểm tra thi là lần thi đầu thì mặc định là thi tốt nghiệp còn nếu là lần > 1 thì loại thi giống lần 1

                        if ((!isset($tontai->tontai) || $tontai->tontai != 1) && !in_array($sinhVien->sv_id, $sinhdacapnhat)) {
                            $diem = DB::table('qlsv_dotthi_diem as d')
                                ->join('qlsv_dotthi_bangdiem as bd', 'bd.dt_bd_id', '=', 'd.dt_bd_id')
                                ->where('bd.mh_id', $monHoc->mh_id)
                                ->where('d.sv_id', $sinhVien->sv_id)
                                ->orderBy('d.svd_lan', 'desc')
                                ->selectRaw('d.*')->first();

                            $dieukienLast = DB::table('qlsv_dotthi_diem as d')
                                ->where('d.sv_id', $sinhVien->sv_id)
                                ->orderBy('d.svd_lan', 'desc')
                                ->first();


                            // Check sv có điểm tổng kết môn dưới 5
                            $dotThiController = new DotThiController();
                            $diemTkMonChuaDat = $dotThiController->KiemTraDuDieuKienThiTN($sinhVien);

                            $dieuKien = isset($sinhVien->toanKhoa->avg) && $sinhVien->toanKhoa->avg >= 5 ? 1 : 0;
                            $dieuKien = isset($diemTkMonChuaDat) && $diemTkMonChuaDat == false ? 1 : 0;
                            if (!isset($diem->svd_first) || $diem->svd_first == null || $diem->svd_first < 5) {
                                // Những sv chưa đủ điều kiện sẽ không phải tăng số lần thi "svd_lan"
                                $solanthi = !isset($diem->svd_lan) || $diem->svd_lan == null ? 1 : $diem->svd_lan + $dieukienLast->svd_dieukien;
                                // $solanthi = !isset($diem->svd_lan)|| $diem->svd_lan == null ? 1 : $diem->svd_lan + 1;

                                if ($solanthi < 4) {
                                    $loai = !isset($diem->svd_lan) || $diem->svd_lan == null ? 0 : $diem->svd_loai;

                                    // svd_khongDatloai: mô tả sv không đạt thuộc loại nào  -  ngoặc định = null
                                    // 0 có điểm tk môn chưa đạt
                                    // 1 ko đạt tiêu chuẩn
                                    // 2 không dự thi

                                    if ($loai == 0 && $monHoc->mh_loai != 5) {
                                        // thi tốt nghiệp
                                        array_push($danhSachSinhVienNew, ["dt_bd_id" => $dt_bd->dt_bd_id, "sv_id" => $sinhVien->sv_id, "svd_dieukien" => $dieuKien, "svd_lan" => $solanthi, "svd_loai" => 0, "diem" => $diem == null ? false : $diem, "svd_khongDatloai" => $dieuKien == 1 ? NULL : 0]);
                                        // DB::insert('insert into qlsv_dotthi_diem (dt_bd_id, sv_id, svd_dieukien, svd_lan, svd_loai) values (?, ?, ?, ?, ?)',
                                        //     [$dt_bd->dt_bd_id, $sinhVien->sv_id, $dieuKien, $solanthi, 0]);
                                    } else if ($loai == 1 && $monHoc->mh_loai == 5) {
                                        // thi bảo vệ luận văn
                                        array_push($danhSachSinhVienNew, ["dt_bd_id" => $dt_bd->dt_bd_id, "sv_id" => $sinhVien->sv_id, "svd_dieukien" => $dieuKien, "svd_lan" => $solanthi, "svd_loai" => 1, "diem" => $diem == null ? false : $diem, "svd_khongDatloai" => $dieuKien == 1 ? NULL : 0]);
                                        array_push($sinhdacapnhat, $sinhVien->sv_id);

                                        // DB::insert('insert into qlsv_dotthi_diem (dt_bd_id, sv_id, svd_dieukien, svd_lan, svd_loai) values (?, ?, ?, ?, ?)',
                                        //     [$dt_bd->dt_bd_id, $sinhVien->sv_id, $dieuKien, $solanthi, 1]);
                                    }
                                }
                            }
                        }
                    }
                }
            }
            //dd($svThiLoai0);
            // dd($danhSachSinhVienNew);

            // Kiểm tra để biết đây là lần đầu tiên lớp này được thêm vào đợt thi TN
            $countFalse = 0;
            $isExist = true;
            $arrayLength = count($danhSachSinhVienNew) - 1;

            foreach ($danhSachSinhVienNew as $item) {
                if ($item['diem'] === false) {
                    $countFalse++;
                    if ($countFalse === $arrayLength) {
                        $isExist = false;
                        break;
                    }
                } else {
                    $countFalse = 0;
                }
            }
            if ($isExist) {
                // Lọc ra nhưng dữ liệu dư thừa do sai cấu trúc
                $danhSachSinhVienNew = array_values(array_filter($danhSachSinhVienNew, function ($item) {
                    return $item['diem'] !== false;
                }));
                //dd("Mảng này đã tồn tại.");
            }
            //  else {
            //dd( "Mảng này chưa tồn tại.");
            // }


            // insert sau khi đã tái cấu trúc
            foreach ($danhSachSinhVienNew as $svIndex => $sinhVien) {
                $diem = $sinhVien['diem'] == false ? false : true;
                $dt_bd_id = $sinhVien["dt_bd_id"];
                $sv_id = $sinhVien["sv_id"];
                $dieuKien = $sinhVien["svd_dieukien"];
                $solanthi = $sinhVien["svd_lan"];
                $loai = $sinhVien["svd_loai"];
                $svd_khongDatloai = $sinhVien["svd_khongDatloai"];
                if ($loai == 0) {
                    // thi tốt nghiệp
                    DB::insert(
                        'insert into qlsv_dotthi_diem (dt_bd_id, sv_id, dt_id, svd_dieukien, svd_lan, svd_loai, svd_khongDatloai) values (?, ?, ?, ?, ?, ?, ?)',
                        [$dt_bd_id, $sv_id, $dotThi, $dieuKien, $solanthi, 0, $svd_khongDatloai]
                    );
                } else if ($loai == 1) {
                    // thi bảo vệ luận văn
                    DB::insert(
                        'insert into qlsv_dotthi_diem (dt_bd_id, sv_id, dt_id, svd_dieukien, svd_lan, svd_loai, svd_khongDatloai) values (?, ?, ?, ?, ?, ?, ?)',
                        [$dt_bd_id, $sv_id, $dotThi, $dieuKien, $solanthi, 1, $svd_khongDatloai]
                    );
                }
            }
        });

        return response()->json($modalDotThi);
    }

    public function KiemTraDuDieuKienThiTN($sinhVien)
    {
        $diemTkMonChuaDat = false;
        foreach ($sinhVien->years as $year) {
            foreach ($year->semesters as $semester) {
                foreach ($semester->monHoc as $mdIndex => $monHoc) {
                    if ($monHoc->ketQua && $monHoc->ketQua->display_score < 5) {
                        $diemTkMonChuaDat = true;
                    }
                }
            }
        }
        return $diemTkMonChuaDat;
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getDotThi($id)
    {
        $dotThiModel = DotThi::find($id);
        $dotThiModel->quyet_dinh = $dotThiModel->quyetDinh;
        return response()->json($dotThiModel);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(DotThiEditRequest $request, $id)
    {

        $quyetDinhPassedData = $request->only(['qd_id', 'qd_id_root', 'qd_ten', 'qd_ngay', 'qd_ma']);
        $loai = 3;
        $dotThiResult = null; // Biến để lưu kết quả

        DB::transaction(function () use ($id, $loai, $quyetDinhPassedData, $request, &$dotThiResult) {
            $qd_id = $quyetDinhPassedData['qd_id'];
            $qd_id_root = $quyetDinhPassedData['qd_id_root'];

            if ($qd_id == 0) {
                $modelQD = new QuyetDinh();
                $modelQD->fill($quyetDinhPassedData);
                $modelQD->qd_loai = $loai;
                $modelQD->save();
                $qd_id = $modelQD->qd_id;
            } else if ($qd_id != -1) {
                $modelQD = QuyetDinh::find($qd_id);
                $modelQD->fill($quyetDinhPassedData);
                $modelQD->qd_loai = $loai;
                $modelQD->save();
                $qd_id = $modelQD->qd_id;
            }

            if ($qd_id == -1 && $qd_id_root != -1) {
                $modelQD = QuyetDinh::find($qd_id_root);
                $modelQD->forceDelete();
            }

            $passedData = $request->only(['dt_ten', 'dt_loai', 'dt_tungay', 'dt_tuthang', 'dt_tunam', 'dt_denngay', 'dt_denthang', 'dt_dennam', 'dt_ghichu', 'dt_so_ke_hoach']);
            $passedData = array_merge($passedData, ['qd_id' => $qd_id]);

            $model = DotThi::find($id);
            $model->fill($passedData);
            $model->save();
            $dotThiResult = $model;
        });

        return response()->json($dotThiResult);





        // $passedData = $request->only(['dt_ten', 'dt_loai', 'dt_tungay', 'dt_tuthang', 'dt_tunam', 'dt_denngay', 'dt_denthang', 'dt_dennam', 'dt_ghichu', 'dt_ketthuc', 'qd_id']);
        // $model = DotThi::find($id);
        // $model->fill($passedData);
        // $model->save();
        // return response()->json($model);
    }

    public function updateQdTrangThai(request $request, $dt_id)
    {
        $dt_qd_trangthai = $request->dt_qd_trangthai;

        $model = DB::table('qlsv_dotthi')
            ->where('dt_id', $dt_id)
            ->update(['dt_qd_trangthai' => $dt_qd_trangthai]);

        return response()->json($model);
    }


    public function getFullDanhSachDotThi()
    {
    }

    public function ketQuaHocTap(Request $request, $lh_id, $dt_id)
    {
        $reqSemester = 123456;
        $export = new KetQuaHocTap;
        $data = $export->getKetQuaHocTapTheoDotThi($reqSemester, $lh_id, $dt_id);

        $reqSemester = $data['reqSemester'];
        $reqYear = $data['reqYear'];
        $lopHoc = $data['lopHoc'];
        $dotThi = DotThi::find($dt_id);
        $danhSachSinhVien = $data['danhSachSinhVien'];
        $danhSachNamHoc = $data['danhSachNamHoc'];
        $semesters = $data['semesters'];
        $sumTinChi = $data['sumTinChi'];
        // $chunkedNotes = $data['notes']->splitIn(3);

        $note = $data['notes'];
        $validValues = ['ĐTBHK1', 'RLHK1', 'ĐTBHK2', 'RLHK2', 'ĐTBN1', 'RLN1', 'ĐTN', 'ĐTBN2', 'RLN2', 'ĐTN', 'ĐTBNTK'];

        $chunkedNotes = $note->reject(function ($item) use ($validValues) {
            return in_array($item['key'], $validValues);
        })->splitIn(3);


        $danhSachDotXetTotNghiep = []; //DotXetTotNghiep::orderBy('dxtn_id', 'desc')->get();

        $filter = new Filters;
        // lọc danh sinh viên đạt và không đạt điều kiện thi tốt nghiệp
        $danhSachSinhVien = $filter->LocSinhVienKhongDuDKThiTN($danhSachSinhVien);

        return view('qlsv.dotthi.dotthi_ketquahoctap', compact([
            'reqSemester',
            'reqYear',
            'lopHoc',
            'danhSachSinhVien',
            'danhSachNamHoc',
            'semesters',
            'chunkedNotes',
            'sumTinChi',
            'danhSachDotXetTotNghiep',
            'dotThi',
        ]));
    }

    public function ketQuaThiTN(Request $request, $lh_id, $dt_id)
    {
        $reqSemester = 123456;
        $export = new KetQuaHocTap;
        $data = $export->getKetQuaHocTapTheoDotThi($reqSemester, $lh_id, $dt_id);

        $reqSemester = $data['reqSemester'];
        $reqYear = $data['reqYear'];
        $lopHoc = $data['lopHoc'];
        $dotThi = DotThi::find($dt_id);
        $danhSachSinhVien = $data['danhSachSinhVien'];
        $danhSachNamHoc = $data['danhSachNamHoc'];
        $semesters = $data['semesters'];
        $sumTinChi = $data['sumTinChi'];
        $chunkedNotes = $data['notes']->splitIn(3);
        $danhSachDotXetTotNghiep = [];
        //DotXetTotNghiep::orderBy('dxtn_id', 'desc')->get();
        //  dd($danhSachSinhVien);


        $filter = new Filters;
        $danhSachSinhVien = $filter->LocSinhVienKhongDatTN($danhSachSinhVien, $lopHoc);

        $danhSachSinhVien = $danhSachSinhVien->sortBy([
            function ($a, $b) {
                if (isset($a->toanKhoa->avg_totnghiep) && isset($b->toanKhoa->avg_totnghiep)) {
                    return $b->toanKhoa->avg_totnghiep <=> $a->toanKhoa->avg_totnghiep;
                } else if (isset($b->toanKhoa->avg_totnghiep)) {
                    return 1;
                } else if (isset($a->toanKhoa->avg_totnghiep)) {
                    return -1;
                }
                return 0;
            },
            function ($a, $b) {
                return $a->sv_ma <=> $b->sv_ma;
            }
        ]);

        // chia làm 3 nhóm
        $nhomThiRotTN = collect();
        $nhomKhongDuThi = collect();
        $nhomKhongDatTieuChuan = collect();
        $nhomKhongDuDieuKien = collect();

        // Lặp qua danh sách sinh viên đã sắp xếp
        foreach ($danhSachSinhVien as $key => $sinhvien) {
            $sinhvien->hienthiDiemThi = true;

            if ($sinhvien->datTotNghep == false) {
                // Chưa đủ đk thì avg_totnghiep = 0
                $sinhvien->toanKhoa->avg_totnghiep = "";

                if ($sinhvien->svd_khongdatloai == null) {
                    if ($sinhvien->svd_khongdatloai == 0 && $sinhvien->svd_dieukien == 0) {
                        $nhomKhongDuDieuKien->push($sinhvien);
                        unset($danhSachSinhVien[$key]);
                        $sinhvien->hienthiDiemThi = false;
                    } else {
                        // Thi rớt
                        $nhomThiRotTN->push($sinhvien);
                        unset($danhSachSinhVien[$key]);
                    }
                } else {
                    if ($sinhvien->svd_khongdatloai == 1) {
                        $nhomKhongDatTieuChuan->push($sinhvien);
                        unset($danhSachSinhVien[$key]);
                        $sinhvien->hienthiDiemThi = false;
                    } else if ($sinhvien->svd_khongdatloai == 2) {
                        $nhomKhongDuThi->push($sinhvien);
                        unset($danhSachSinhVien[$key]);
                        $sinhvien->hienthiDiemThi = false;
                    }
                }
            }
        }

        // Gộp toàn bộ các nhóm sv vào 1 ds
        $danhSachSinhVien = $danhSachSinhVien
            ->concat($nhomThiRotTN)
            ->concat($nhomKhongDatTieuChuan)
            ->concat($nhomKhongDuThi)
            ->concat($nhomKhongDuDieuKien);

        return view('qlsv.dotthi.dotthi_ketquathitn', compact([
            'reqSemester',
            'reqYear',
            'lopHoc',
            'danhSachSinhVien',
            'danhSachNamHoc',
            'semesters',
            'chunkedNotes',
            'sumTinChi',
            'danhSachDotXetTotNghiep',
            'dotThi',
            'dt_id'
        ]));
    }


    public function getDsMonHocTheoLop(Request $rq)
    {

        $dsMonHoc = DB::table('qlsv_dotthi_bangdiem as dtbd')
            ->join('qlsv_monhoc as mh', 'mh.mh_id', '=', 'dtbd.mh_id')
            ->leftJoin('qlsv_dotthi_diem as d', 'd.dt_bd_id', '=', 'dtbd.dt_bd_id')
            ->where('dtbd.dt_id', $rq->dt_id)
            ->where('dtbd.lh_id', $rq->lh_id)
            ->select(DB::raw('DISTINCT mh.mh_ma, mh.mh_ten, mh.mh_id, mh.mh_loai,  dtbd.dt_id, COUNT(d.dt_bd_id) >  0 as codiem'))
            ->groupBy('mh.mh_id', 'dtbd.dt_id', 'mh.mh_ma', 'mh.mh_ten', 'mh.mh_loai')
            ->get();

        return response()->json($dsMonHoc);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // $model = DotThi::find($id);
        // $model->delete();
        // return response()->json($model);
        DB::transaction(function () use ($id) {
            $modelDotThi = DotThi::find($id);

            $qd_id = $modelDotThi->qd_id;
            if ($qd_id != -1) {
                $modelQD = QuyetDinh::find($qd_id);
                $modelQD->forceDelete();
            }

            $modelDotThi->forceDelete();
            return response()->json($modelDotThi);
        });
    }
}
