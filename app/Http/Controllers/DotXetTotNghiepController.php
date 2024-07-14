<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DotXetTotNghiep;
use App\Excels\KetQuaHocTap;
use App\Models\LopHoc;
use App\Models\DotThi;
use App\Http\Requests\DotXetTotNghiepEditRequest;
use App\Models\DotThiBangDiem;
use App\Models\DotThiDotXetTotNghiep;
use App\Models\DotXetTotNghiepSinhVien;
use App\Models\KhoaDaoTao;
use App\Models\QuyetDinh;
use App\Models\SinhVien;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use PhpParser\Node\Stmt\TryCatch;

class DotXetTotNghiepController extends Controller
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

        session(['parent_url:dot-xet-tot-nghiep' => $request->fullUrl()]);
        return view('qlsv.dotxettotnghiep.dotxettotnghiep_list', compact('permissions'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('qlsv.dotxettotnghiep.dotxettotnghiep_create');
    }

    // T.Phong ---->
    public function paginate(Request $request)
    {
        $search = $request->search;
        $dxtn_tunam = $request->tunam;
        $dxtn_dennam = $request->dennam;
        $hdt_id = $request->chuongtrinh;
        $dxtn_id = $request->dxtn_id;

        $danhSachDotXetTotNghiep = DotXetTotNghiep::withExists(['dotThi'])
            ->where(function ($builder) use ($search, $dxtn_id) {
                if($dxtn_id){
                    $builder->whereRaw('lower(qlsv_dotxettotnghiep.dxtn_id) like lower(?)', "%$dxtn_id%");
                }
                else {
                    $builder->whereRaw('lower(qlsv_dotxettotnghiep.dxtn_ten) like lower(?)', "%$search%");
                }
            })
            ->where(function ($builder) use ($dxtn_tunam) {
                if (isset ($dxtn_tunam)) {
                    $builder->whereRaw('qlsv_dotxettotnghiep.dxtn_tunam = ' . $dxtn_tunam);
                }
            })
            ->where(function ($builder) use ($dxtn_dennam) {
                if (isset ($dxtn_dennam)) {
                    $builder->whereRaw('qlsv_dotxettotnghiep.dxtn_dennam = ' . $dxtn_dennam);
                }
            })
            ->where(function ($builder) use ($hdt_id) {
                if (isset ($hdt_id) && $hdt_id != -1) {
                    if ($hdt_id == 4) {
                        if (auth()->user()->hasPermission('caodang')) {
                            $builder->whereRaw('qlsv_dotxettotnghiep.dxtn_hdt_id = ?', 4);
                        }
                    } else if ($hdt_id == 5) {
                        if (auth()->user()->hasPermission('trungcap')) {
                            $builder->whereRaw('qlsv_dotxettotnghiep.dxtn_hdt_id = ?', 5);
                        }
                    }
                } else {
                    if (auth()->user()->hasPermission('caodang')) {
                        $builder->whereRaw('qlsv_dotxettotnghiep.dxtn_hdt_id = ?', 4);
                    } else if (auth()->user()->hasPermission('trungcap')) {
                        $builder->whereRaw('qlsv_dotxettotnghiep.dxtn_hdt_id = ?', 5);
                    }
                }
            })
            ->orderBy('dxtn_id', 'desc')
            ->paginate(10)
            ->setPath(route('dot-xet-tot-nghiep.index'))
            ->onEachSide(2);

        foreach ($danhSachDotXetTotNghiep as $dotxettotnghiep) {
            if ($dotxettotnghiep->qd_id != null || $dotxettotnghiep->qd_id != -1) {
                $qlsv_quyetdinh = DB::table('qlsv_quyetdinh')
                    ->where('qd_id', '=', $dotxettotnghiep->qd_id)
                    ->where('qd_loai', '=', 1)
                    ->select('qd_ten', 'qd_ma', 'qd_ngay')
                    ->get();

                if (count($qlsv_quyetdinh) > 0) {
                    $dotxettotnghiep->qd_ten = $qlsv_quyetdinh[0]->qd_ten;
                    $dotxettotnghiep->qd_ma = $qlsv_quyetdinh[0]->qd_ma;
                    $dotxettotnghiep->qd_ngay = $qlsv_quyetdinh[0]->qd_ngay;
                }
            }

            $dotxettotnghiep->dot_thi = $dotxettotnghiep->dotThi;
            $dotxettotnghiep->chitiet_url = route('dot-xet-tot-nghiep.detail', $dotxettotnghiep);
        }
        return response()->json($danhSachDotXetTotNghiep);
    }

    // T.Phong ---->


    public function exportBanDiemTungMonVaDotThiThiDatTN(Request $request, $dt_id)
    {
        $export = new KetQuaHocTap;
        $lh_id = $request->lh_id;

        return $export->downloadMonHocDotThiThiDatTN($dt_id, $lh_id);
    }

    public function getDotThiTheoDotXet($dxtn_id, Request $request)
    {
        $search = $request->search;
        $danhSachDotThi = DotThi::withExists(['dotThiBangDiem'])
            ->join('qlsv_dotthi_dotxettotnghiep', function ($join) {
                $join->on('qlsv_dotthi_dotxettotnghiep.dt_id', '=', 'qlsv_dotthi.dt_id');
            })
            ->where(function ($builder) use ($search) {
                $builder->whereRaw('lower(qlsv_dotthi.dt_ten) like lower(?)', "%$search%");
            })
            ->where(function ($builder) use ($dxtn_id) {
                if (isset ($dxtn_id) && $dxtn_id != -1) {
                    $builder->whereRaw('qlsv_dotthi_dotxettotnghiep.dxtn_id = ?', "$dxtn_id");
                }
            })
            ->orderBy('qlsv_dotthi.dt_id', 'desc')
            ->paginate(10)
            ->setPath(route('dot-thi.index'))
            ->onEachSide(2);
        foreach ($danhSachDotThi as $dotthi) {
            $dotthi->chitiet_url = route('dot-thi.detail', $dotthi);
        }
        return response()->json($danhSachDotThi);
    }

    public function xemChiTietDotXetTotNghiepMonHocLop($lh_id, $dxtn_id)
    {
        $dotxettotnghiep = DotXetTotNghiep::find($dxtn_id);
        $lophoc = LopHoc::find($lh_id);
        return view('qlsv.dotxettotnghiep.dsmonhoc_dotxettotnghiep_lop', compact(['dotxettotnghiep', 'lophoc']));
    }

    public function xoaDotThi(Request $request)
    {
        $dotXet = $request->dxtn_id;
        $dotThi = $request->dt_id;

        DB::table('qlsv_dotthi_dotxettotnghiep')
            ->where('dxtn_id', $dotXet)
            ->where('dt_id', $dotThi)
            ->delete();
    }

    public function capNhatDotXetChoDotThi(Request $request)
    {
        $dxtn_id = $request->dxtn_id;
        $dt_id = $request->dt_id;
        $reqSemester = 123456;

        DB::transaction(function () use ($request, $reqSemester, $dt_id, $dxtn_id) {
            $KetQuaHocTap = new KetQuaHocTap;
            $filter = new Filters;

            // Insert qlsv_dotthi_dotxettotnghiep
            DB::insert('insert into qlsv_dotthi_dotxettotnghiep (dt_id, dxtn_id) values (?, ?)', [$dt_id, $dxtn_id]);

            // Lấy ra những sv đủ điều kiện thi TN
            $danhSachSinhVienDuDKDotThi = DB::table('qlsv_dotthi_bangdiem as bd')
                ->join('qlsv_dotthi_diem as svd', function ($join) {
                    $join->on('svd.dt_bd_id', '=', 'bd.dt_bd_id');
                })
                ->join('qlsv_sinhvien as sv', function ($join) {
                    $join->on('svd.sv_id', '=', 'sv.sv_id');
                })
                ->whereRaw('bd.dt_id =' . $dt_id)
                ->whereRaw('svd.svd_dieukien = 1')
                ->whereNull('svd.svd_khongdatloai')
                ->select(DB::raw('DISTINCT sv.sv_id, bd.lh_id, bd.dt_id'))
                ->get();

            $allLopHoc = $danhSachSinhVienDuDKDotThi->pluck('lh_id')->unique()->values()->all();

            // Nếu không tìm thấy lớp trong đợt thi thì check lại trong qlsv_dotthi_bangdiem
            // Trả về ds lớp
            if (empty ($allLopHoc)) {
                $danhSachSinhVienDuDKDotThi1 = DB::table('qlsv_dotthi_bangdiem as bd')
                    ->whereRaw('bd.dt_id =' . $dt_id)
                    ->select(DB::raw('DISTINCT bd.lh_id'))
                    ->get();
                $allLopHoc = $danhSachSinhVienDuDKDotThi1->pluck('lh_id')->unique()->values()->all();
            }

            // SV đã đậu TN nhưng chưa từng được xét TN theo lớp
            foreach ($allLopHoc as $index => $lh_id) {
                $sinhVienChuaDuocXTNTheoLop = DB::table('qlsv_dotxettotnghiep_sinhvien as dxtn_sv')
                    ->join('qlsv_dotxettotnghiep as dxtn', 'dxtn.dxtn_id', 'dxtn_sv.dxtn_id')
                    ->join('qlsv_dotthi_dotxettotnghiep as dt_dxtn', 'dt_dxtn.dxtn_id', 'dxtn.dxtn_id')
                    ->where('dxtn_sv.lh_id', $lh_id)
                    ->where('dxtn_sv.svxtn_dattn', 1)
                    ->where('dxtn_sv.svxtn_vipham', 1)
                    ->select(DB::raw('DISTINCT dxtn_sv.sv_id, dxtn_sv.lh_id, MIN(dt_dxtn.dt_id) as dt_id'))
                    ->groupBy('dxtn_sv.sv_id', 'dxtn_sv.lh_id')
                    ->get();

                $danhSachSinhVienDuDKDotThi = $danhSachSinhVienDuDKDotThi->concat($sinhVienChuaDuocXTNTheoLop);
            }


            // Insert những sinh đợt hiện tại
            foreach ($danhSachSinhVienDuDKDotThi as $index => $sv) {
                // dd($sv);
                $sv_id = $sv->sv_id;
                $lhId = $sv->lh_id;

                $checkSVDaDatTN = DB::table('qlsv_dotxettotnghiep_sinhvien as dxtn_sv')
                    ->where('dxtn_sv.lh_id', $lh_id)
                    ->where('dxtn_sv.sv_id', $sv_id)
                    ->where('dxtn_sv.svxtn_dattn', 1)
                    ->whereNull('dxtn_sv.svxtn_vipham')
                    ->whereNull('dxtn_sv.svxtn_ghichu')
                    ->select(DB::raw('DISTINCT dxtn_sv.sv_id, dxtn_sv.lh_id'))
                    ->get();

                if ($checkSVDaDatTN->count() > 0) {
                    continue;
                }


                // $data = $KetQuaHocTap->getKetQuaHocTap($reqSemester, $lhId, $sv_id);
                // $data = $KetQuaHocTap->getKetQuaHocTapTheoDotThiBySinhVien($reqSemester, $lhId, $dt_id, $sv_id);
                $data = $KetQuaHocTap->getKetQuaHocTapCapNhatDotXetChoDotThi($reqSemester, $lhId, $sv->dt_id, $sv_id);

                // dd($data["danhSachSinhVien"][0]->toanKhoa);
                // Check trạng thái đạt TN hay không đạt
                $datTotNghep = $filter->SinhVienDatThiTN($data["lopHoc"], $data["danhSachSinhVien"]);

                // Kiểm tra xem có môn học thi lại không
                $lh_nienche = $data["lopHoc"]["lh_nienche"];
                $quyChe2022 = $lh_nienche == 1;

                // hocLucTN
                if ($datTotNghep) {
                    $KetQuaHocTap = new KetQuaHocTap;
                    $temp_xltn = $KetQuaHocTap->setHocLucByDB($data["danhSachSinhVien"][0]->toanKhoa->hocLucTN, $quyChe2022);
                    $final_xltn = $temp_xltn;
                } else {
                    $temp_xltn = -1;
                    $final_xltn = $temp_xltn;
                }

                $MonThiLaiJsonString = null;
                if ($data["danhSachSinhVien"][0]->notes->count() > 0 || $data["danhSachSinhVien"][0]->toanKhoa->ghiChuThiLaiArray->count() > 0) {
                    // dd($data["danhSachSinhVien"][0]);

                    $monThiLaiArray = $data["danhSachSinhVien"][0]->toanKhoa->ghiChuThiLaiArray->toArray();
                    $monThiLaiNotes = $data["danhSachSinhVien"][0]->notes->toArray();

                    $mergedArrayMonThiLai = array_merge($monThiLaiArray, $monThiLaiNotes);

                    $soMonThiLaiTLHL = 0;
                    $daDem = [];

                    foreach ($mergedArrayMonThiLai as &$monThi) {
                        $type = $monThi['type'];
                        $key = $monThi['key'];

                        // Loại bỏ "key"
                        if (array_key_exists('key', $monThi)) {
                            unset ($monThi['key']);
                        }

                        // Kiểm tra nếu môn chưa được đếm và có type là "TL"
                        if (!in_array($key, $daDem) && ($type === 'TL' || $type === 'HL')) {
                            $soMonThiLaiTLHL++;
                            $daDem[] = $key; // Thêm môn vào danh sách đã đếm
                        }
                    }
                    // Bỏ tham chiếu để đảm bảo không thay đổi các phần tử sau này
                    unset ($monThi);


                    // Chuyển $mergedArrayMonThiLai thành chuỗi JSON
                    $MonThiLaiJsonString = json_encode($mergedArrayMonThiLai);

                    if ($soMonThiLaiTLHL > 0) {
                        // QC22:
                        // - Xuất sắc có TL => hạ
                        // - Giỏi có 2 môn TL => hạ
                        // QC20:
                        // - Giỏi trở lên có TL => hạ

                        // 1 = Xuất sắc; 2 = Giỏi
                        if ($quyChe2022) {
                            // quyChe2022

                            if ($temp_xltn == 1) {
                                // Loại Xuất sắc
                                $final_xltn = $final_xltn + 1;
                            } else if ($temp_xltn == 2 && $soMonThiLaiTLHL >= 2) {
                                // Loại Giỏi
                                $final_xltn = $final_xltn + 1;
                            }
                        } else {
                            // quyChe2020
                            if ($temp_xltn == 1 || $temp_xltn == 2) {
                                // Loại Xuất sắc và Loại Giỏi
                                $final_xltn = $final_xltn + 1;
                            }
                        }
                    }
                }


                DB::insert(
                    'insert into qlsv_dotxettotnghiep_sinhvien (sv_id, dxtn_id, lh_id, temp_xltn, final_xltn ,svxtn_dattn, ghichu_tlhl) values (?, ?, ?, ?, ?, ?, ?)',
                    [$sv_id, $dxtn_id, $lhId, $temp_xltn, $final_xltn, $datTotNghep, $MonThiLaiJsonString]
                );
            }

            // Insert những sinh đạt điểm xét TN nhưng vi phạm ở đợt trước - dssv này được tìm lại theo mã lớp
            // foreach ($danhSachSinhVienChuaDuocXTNTheoLop as $sv) {
            //     $sv_id = $sv->sv_id;
            //     $lhId = $sv->lh_id;

            //     $data = $KetQuaHocTap->getKetQuaHocTapTheoDotThiBySinhVien($reqSemester, $lhId, $dt_id, $sv_id);

            //     // Check trạng thái đạt TN hay không đạt
            //     $datTotNghep = $filter->SinhVienDatThiTN($data["lopHoc"], $data["danhSachSinhVien"]);
            //     // Kiểm tra xem có môn học thi không

            //     // hocLucTN
            //     $lh_nienche = $data["lopHoc"]["lh_nienche"];
            //     $quyChe2022 = $lh_nienche == 1;

            //     $KetQuaHocTap = new KetQuaHocTap;
            //     $temp_xltn = $KetQuaHocTap->setHocLucByDB($data["danhSachSinhVien"][0]->toanKhoa->hocLucTN, $quyChe2022);
            //     $final_xltn = $temp_xltn;
            //     $MonThiLaiJsonString = null;

            //     if ($data["danhSachSinhVien"][0]->notes->count() > 0 || $data["danhSachSinhVien"][0]->toanKhoa->ghiChuThiLaiArray->count() > 0) {
            //         // dd($data["danhSachSinhVien"][0]);

            //         $monThiLaiArray = $data["danhSachSinhVien"][0]->toanKhoa->ghiChuThiLaiArray->toArray();
            //         $monThiLaiNotes = $data["danhSachSinhVien"][0]->notes->toArray();

            //         $mergedArrayMonThiLai = array_merge($monThiLaiArray, $monThiLaiNotes);

            //         $soMonThiLaiTL = 0;
            //         $daDem = [];

            //         foreach ($mergedArrayMonThiLai as &$monThi) {
            //             $type = $monThi['type'];
            //             $key = $monThi['key'];

            //             // Loại bỏ "key"
            //             if (array_key_exists('key', $monThi)) {
            //                 unset($monThi['key']);
            //             }

            //             // Kiểm tra nếu môn chưa được đếm và có type là "TL"
            //             if (!in_array($key, $daDem) && $type === 'TL') {
            //                 $soMonThiLaiTL++;
            //                 $daDem[] = $key; // Thêm môn vào danh sách đã đếm
            //             }
            //         }
            //         // Bỏ tham chiếu để đảm bảo không thay đổi các phần tử sau này
            //         unset($monThi);


            //         // Chuyển $mergedArrayMonThiLai thành chuỗi JSON
            //         $MonThiLaiJsonString = json_encode($mergedArrayMonThiLai);

            //         if ($soMonThiLaiTL > 0) {
            //             // QC22:
            //             // - Xuất sắc có TL => hạ
            //             // - Giỏi có 2 môn TL => hạ
            //             // QC20:
            //             // - Giỏi trở lên có TL => hạ

            //             // 1 = Xuất sắc; 2 = Giỏi
            //             if ($quyChe2022) {
            //                 // quyChe2022

            //                 if ($temp_xltn == 1) {
            //                     // Loại Xuất sắc
            //                     $final_xltn = $final_xltn + 1;
            //                 } else if ($temp_xltn == 2 && $soMonThiLaiTL >= 2) {
            //                     // Loại Giỏi
            //                     $final_xltn = $final_xltn + 1;
            //                 }
            //             } else {
            //                 // quyChe2020
            //                 if ($temp_xltn == 1 || $temp_xltn == 2) {
            //                     // Loại Xuất sắc và Loại Giỏi
            //                     $final_xltn = $final_xltn + 1;
            //                 }
            //             }
            //         }
            //     }


            //     DB::insert(
            //         'insert into qlsv_dotxettotnghiep_sinhvien (sv_id, dxtn_id, lh_id, temp_xltn, final_xltn ,svxtn_dattn, ghichu_tlhl) values (?, ?, ?, ?, ?, ?, ?)',
            //         [$sv_id, $dxtn_id, $lhId, $temp_xltn, $final_xltn, 1, $MonThiLaiJsonString]
            //     );
            // }



            // dd($danhSachSinhVienDuDKDotThi);


            // Get SV trong đợt thi $dt_id đủ đk thi TN

            // Check SV trong đợt xtn $dt_id có đạt TN chưa ?

            // Insert vào bảng xét TN SV
            // DB::insert(
            //     'insert into qlsv_dotxettotnghiep_sinhvien (sv_id, dt_id, dxtn_id, lh_id, svxtn_dattn) values (?, ?, ?, ?, ?)',
            //     [$sv_id, $dt_id, $dxtn_id, $lhId, $datTotNghep]
            // );

            // $danhSachSinhVienLop = DB::table('qlsv_dotthi_bangdiem as bd')
            //     ->join('qlsv_dotthi_diem as svd', function ($join) {
            //         $join->on('svd.dt_bd_id', '=', 'bd.dt_bd_id');
            //     })
            //     ->join('qlsv_sinhvien as sv', function ($join) {
            //         $join->on('svd.sv_id', '=', 'sv.sv_id');
            //     })
            //     ->join('qlsv_lophoc as lh', function ($join) {
            //         $join->on('bd.lh_id', '=', 'lh.lh_id');
            //     })
            //     ->join('qlsv_khoadaotao as kdt', function ($join) {
            //         $join->on('kdt.kdt_id', '=', 'lh.kdt_id');
            //     })
            //     ->whereRaw('bd.dt_id =' . $dt_id)
            //     ->whereRaw('svd.svd_dieukien = 1')
            //     ->whereNull('svd.svd_khongdatloai')
            //     ->select(DB::raw('DISTINCT sv.*, lh.*, svd.svd_dieukien, svd.svd_khongdatloai, svd.svd_loai'))
            //     ->get();


            // foreach ($danhSachSinhVienLop as $index => $sv) {
            //     $sv_id = $sv->sv_id;
            //     $lhId = $sv->lh_id;
            //     // $data = $KetQuaHocTap->getKetQuaHocTap($reqSemester, $lhId, $sv_id);
            //     $data = $KetQuaHocTap->getKetQuaHocTapTheoDotThiBySinhVien($reqSemester, $lhId, $dt_id, $sv_id);
            //     // dd($data["danhSachSinhVien"]);
            //     // dd($data["danhSachSinhVien"]);


            //     $datTotNghep = $filter->SinhVienDatThiTN($data["lopHoc"], $data["danhSachSinhVien"]);
            //     // Kiểm tra xem có môn học thi không


            //     DB::insert(
            //         'insert into qlsv_dotxettotnghiep_sinhvien (sv_id, dt_id, dxtn_id, lh_id, svxtn_dattn) values (?, ?, ?, ?, ?)',
            //         [$sv_id, $dt_id, $dxtn_id, $lhId, $datTotNghep]
            //     );
            // }
        });

        // dd(count($danhSachSinhVienDuDieuKien));

        // DB::insert('insert into qlsv_dotthi_dotxettotnghiep (dt_id, dxtn_id) values (?, ?)',
        //     [$dt_id, $dxtn_id]);
    }

    // Dùng để cập nhật lại ghi chú đồng nghĩa với cập nhật lại XLTN
    // Trường họp bên điểm thi môn học đang sai và muốn cập nhật lại nhưng không muốn đi lại quy trình
    public function capNhatDotXetChoDotThi1(Request $request)
    {
        $dxtn_id = $request->dxtn_id;
        $dt_id = $request->dt_id;
        $reqSemester = 123456;

        DB::transaction(function () use ($request, $reqSemester, $dt_id, $dxtn_id) {
            $KetQuaHocTap = new KetQuaHocTap;
            $filter = new Filters;


            $danhSachSinhVienDuDKDotThi = DB::table('qlsv_dotxettotnghiep_sinhvien as dxtn_sv')
                ->where('dxtn_sv.dxtn_id', $dxtn_id)
                ->select(DB::raw('DISTINCT dxtn_sv.sv_id, dxtn_sv.lh_id'))
                ->get();

            // Insert những sinh đợt hiện tại
            foreach ($danhSachSinhVienDuDKDotThi as $index => $sv) {
                // dd($sv);
                $sv_id = $sv->sv_id;
                $lhId = $sv->lh_id;

                $data = $KetQuaHocTap->getKetQuaHocTapCapNhatDotXetChoDotThi($reqSemester, $lhId, $dt_id, $sv_id);
                // dd($data["danhSachSinhVien"][0]->toanKhoa);
                // Check trạng thái đạt TN hay không đạt
                $datTotNghep = $filter->SinhVienDatThiTN($data["lopHoc"], $data["danhSachSinhVien"]);

                // Kiểm tra xem có môn học thi lại không
                $lh_nienche = $data["lopHoc"]["lh_nienche"];
                $quyChe2022 = $lh_nienche == 1;

                // hocLucTN
                if ($datTotNghep) {
                    $KetQuaHocTap = new KetQuaHocTap;
                    $temp_xltn = $KetQuaHocTap->setHocLucByDB($data["danhSachSinhVien"][0]->toanKhoa->hocLucTN, $quyChe2022);
                    $final_xltn = $temp_xltn;
                } else {
                    $temp_xltn = -1;
                    $final_xltn = $temp_xltn;
                }

                $MonThiLaiJsonString = null;
                if ($data["danhSachSinhVien"][0]->notes->count() > 0 || $data["danhSachSinhVien"][0]->toanKhoa->ghiChuThiLaiArray->count() > 0) {
                    // dd($data["danhSachSinhVien"][0]);

                    $monThiLaiArray = $data["danhSachSinhVien"][0]->toanKhoa->ghiChuThiLaiArray->toArray();
                    $monThiLaiNotes = $data["danhSachSinhVien"][0]->notes->toArray();

                    $mergedArrayMonThiLai = array_merge($monThiLaiArray, $monThiLaiNotes);

                    $soMonThiLaiTL = 0;
                    $daDem = [];

                    foreach ($mergedArrayMonThiLai as &$monThi) {
                        $type = $monThi['type'];
                        $key = $monThi['key'];

                        // Loại bỏ "key"
                        if (array_key_exists('key', $monThi)) {
                            unset ($monThi['key']);
                        }

                        // Kiểm tra nếu môn chưa được đếm và có type là "TL"
                        if (!in_array($key, $daDem) && $type === 'TL') {
                            $soMonThiLaiTL++;
                            $daDem[] = $key; // Thêm môn vào danh sách đã đếm
                        }
                    }
                    // Bỏ tham chiếu để đảm bảo không thay đổi các phần tử sau này
                    unset ($monThi);


                    // Chuyển $mergedArrayMonThiLai thành chuỗi JSON
                    $MonThiLaiJsonString = json_encode($mergedArrayMonThiLai);

                    if ($soMonThiLaiTL > 0) {
                        // QC22:
                        // - Xuất sắc có TL => hạ
                        // - Giỏi có 2 môn TL => hạ
                        // QC20:
                        // - Giỏi trở lên có TL => hạ

                        // 1 = Xuất sắc; 2 = Giỏi
                        if ($quyChe2022) {
                            // quyChe2022

                            if ($temp_xltn == 1) {
                                // Loại Xuất sắc
                                $final_xltn = $final_xltn + 1;
                            } else if ($temp_xltn == 2 && $soMonThiLaiTL >= 2) {
                                // Loại Giỏi
                                $final_xltn = $final_xltn + 1;
                            }
                        } else {
                            // quyChe2020
                            if ($temp_xltn == 1 || $temp_xltn == 2) {
                                // Loại Xuất sắc và Loại Giỏi
                                $final_xltn = $final_xltn + 1;
                            }
                        }
                    }
                }


                // DB::insert(
                //     'insert into qlsv_dotxettotnghiep_sinhvien (sv_id, dxtn_id, lh_id, temp_xltn, final_xltn ,svxtn_dattn, ghichu_tlhl) values (?, ?, ?, ?, ?, ?, ?)',
                //     [$sv_id, $dxtn_id, $lhId, $temp_xltn, $final_xltn, $datTotNghep, $MonThiLaiJsonString]
                // );


                DB::update(
                    'update qlsv_dotxettotnghiep_sinhvien
                    set temp_xltn = ?, final_xltn = ?, ghichu_tlhl = ?
                    where sv_id = ? and dxtn_id = ? and lh_id = ?',
                    [$temp_xltn, $final_xltn, $MonThiLaiJsonString, $sv_id, $dxtn_id, $lhId]
                );
            }
        });
    }


    public function capNhatXltn(Request $request)
    {
        $dsSinhVien = $request->sinhVien;
        DB::transaction(function () use ($dsSinhVien) {
            // Insert những sinh đợt hiện tại
            foreach ($dsSinhVien as $sv) {
                $final_xltn = $sv["xltn"];
                $sv_id = $sv["sv_id"];
                if ($final_xltn != -1) {
                    DB::update(
                        'update qlsv_dotxettotnghiep_sinhvien
                    set final_xltn = ?
                    where sv_id = ?',
                        [$final_xltn, $sv_id]
                    );
                }
            }
        });
    }

    public function getAllDotXetTotNghiep(Request $request)
    {
        $chuongtrinh = $request->chuongtrinh;
        $danhSachDotXetTotNghiep = DotXetTotNghiep::where('dxtn_hdt_id', $chuongtrinh)
            ->orderBy('dxtn_id', 'desc')
            ->get();
        return response()->json($danhSachDotXetTotNghiep);
    }

    public function xemChiTietDotXetTotNghiep($id)
    {
        $dotxettotnghiep = DotXetTotNghiep::find($id);
        $dotthidotXxettotghiep = DotThiDotXetTotNghiep::find($id);
        $dt_id = $dotthidotXxettotghiep->dt_id;

        // dd($dotxettotnghiep);
        $quyetDinh = false;
        if ($dotxettotnghiep->dxtn_qd_trangthai == 1) {
            $quyetDinh = true;
        }

        $parentUrl = session('parent_url:dot-xet-tot-nghiep', '/dot-xet-tot-nghiep');
        return view('qlsv.dotxettotnghiep.dotxettotnghiep_chitiet', compact(['dotxettotnghiep', 'quyetDinh', 'dt_id', 'parentUrl']));
    }

    public function xemChiTietDotThi($id)
    {
        $dotthi = DotThi::find($id);
        return view('qlsv.dotxettotnghiep.dotxettotnghiep_dotthi_chitiet', compact(['dotthi']));
    }
    public function getDanhSachSinhVienTheoDotXetTN($dxtn_id, Request $request)
    {
        $search = $request->search;
        $loai = $request->loai;
        $loai_thi = $request->loai_thi;

        $danhSachNganhNghe = DB::table('qlsv_lophoc as lh')
            ->join('qlsv_dotxettotnghiep_sinhvien as dxtn_sv', function ($join) {
                $join->on('dxtn_sv.lh_id', '=', 'lh.lh_id');
            })
            ->join('qlsv_khoadaotao as kdt', function ($join) {
                $join->on('kdt.kdt_id', '=', 'lh.kdt_id');
            })
            ->join('qlsv_nganhnghe as nn', function ($join) {
                $join->on('nn.nn_id', '=', 'kdt.nn_id');
            })
            ->where('dxtn_sv.dxtn_id', '=', $dxtn_id)
            ->orderBy('lh.lh_id', 'desc')
            ->select(DB::raw('DISTINCT nn.nn_ten, nn.nn_ma, nn.nn_id, dxtn_sv.lh_id, lh.lh_id'))
            ->get();

        foreach ($danhSachNganhNghe as $nn) {
            $danhSachSinhVienLop = DB::table('qlsv_dotxettotnghiep_sinhvien as dxtn_sv')
                ->join('qlsv_sinhvien as sv', function ($join) {
                    $join->on('sv.sv_id', '=', 'dxtn_sv.sv_id');
                })
                ->join('qlsv_lophoc as lh', function ($join) {
                    $join->on('lh.lh_id', '=', 'dxtn_sv.lh_id');
                })
                ->join('qlsv_khoadaotao as kdt', function ($join) {
                    $join->on('kdt.kdt_id', '=', 'lh.kdt_id');
                })
                ->where('dxtn_sv.lh_id', '=', $nn->lh_id)
                ->where('dxtn_sv.dxtn_id', '=', $dxtn_id)
                ->where(function ($builder) use ($search) {
                    $builder->whereRaw("lower(sv.sv_ho) like lower('%$search%') OR lower(sv.sv_ten) like lower('%$search%') OR lower(sv.sv_ma) like lower('%$search%') OR lower(lh.lh_ten) like lower('%$search%') OR lower(lh.lh_ma) like lower('%$search%')");
                })
                ->where(function ($builder) use ($loai) {
                    if (isset ($loai)) {
                        if ($loai == 'true') {
                            $builder->whereRaw('dxtn_sv.svxtn_dattn = 1')->whereNull('dxtn_sv.svxtn_vipham');
                        } else {
                            $builder->where('dxtn_sv.svxtn_dattn', 0)->orWhere('dxtn_sv.svxtn_vipham', 1);
                        }
                    } else {
                        $builder->whereRaw('dxtn_sv.svxtn_dattn = 1')->whereNull('dxtn_sv.svxtn_vipham');
                    }
                })
                ->select(DB::raw('DISTINCT sv.*, lh.*, kdt.kdt_khoa, dxtn_sv.svxtn_dattn, dxtn_sv.svxtn_vipham, dxtn_sv.svxtn_ghichu'))
                ->get();
            $nn->danhsachsinhvien = $danhSachSinhVienLop;
        }
        return response()->json($danhSachNganhNghe);
    }

    public function exportDanhSachKetQuaXetTNheoDotXetTN(Request $request, $dxtn_id)
    {
        $reqSemester = 123456;
        $export = new KetQuaHocTap;
        $search = $request->search;
        $loai = $request->loai;

        $dxtn = DotThiDotXetTotNghiep::find($dxtn_id);
        $dt_id = $dxtn->dt_id;

        // $danhSachNganhNghe = DB::table('qlsv_lophoc as lh')
        //     ->join('qlsv_dotxettotnghiep_sinhvien as dxtn_sv', function ($join) {
        //         $join->on('dxtn_sv.lh_id', '=', 'lh.lh_id');
        //     })
        //     ->join('qlsv_khoadaotao as kdt', function ($join) {
        //         $join->on('kdt.kdt_id', '=', 'lh.kdt_id');
        //     })
        //     ->join('qlsv_nganhnghe as nn', function ($join) {
        //         $join->on('nn.nn_id', '=', 'kdt.nn_id');
        //     })
        //     ->where('dxtn_sv.dxtn_id', '=', $dxtn_id)
        //     ->select(DB::raw('DISTINCT nn.nn_ten, nn.nn_ma, nn.nn_id, dxtn_sv.lh_id, lh.lh_id, dxtn_sv.dt_id'))
        //     ->get();


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
            ->select(DB::raw('DISTINCT nn.*, bd.dt_id, kdt.*, bd.lh_id'))
            ->get();

        foreach ($danhSachNganhNghe as $index => $nn) {
            $danhSachSinhVienLop = DB::table('qlsv_dotxettotnghiep_sinhvien as dxtn_sv')
                ->join('qlsv_dotthi_dotxettotnghiep as dt_dxtn', function ($join) {
                    $join->on('dt_dxtn.dxtn_id', '=', 'dxtn_sv.dxtn_id');
                })
                ->join('qlsv_sinhvien as sv', function ($join) {
                    $join->on('sv.sv_id', '=', 'dxtn_sv.sv_id');
                })
                ->join('qlsv_lophoc as lh', function ($join) {
                    $join->on('lh.lh_id', '=', 'dxtn_sv.lh_id');
                })
                ->join('qlsv_khoadaotao as kdt', function ($join) {
                    $join->on('kdt.kdt_id', '=', 'lh.kdt_id');
                })
                ->where('dxtn_sv.lh_id', '=', $nn->lh_id)
                ->where('dxtn_sv.dxtn_id', '=', $dxtn_id)
                ->where(function ($builder) use ($loai) {
                    if (isset ($loai)) {
                        if ($loai == 'true') {
                            $builder->whereRaw('dxtn_sv.svxtn_dattn = 1')->whereNull('dxtn_sv.svxtn_vipham');
                        } else {
                            $builder->whereRaw('dxtn_sv.svxtn_dattn = 0')->orWhere('dxtn_sv.svxtn_vipham', 1);
                        }
                    } else {
                        $builder->whereRaw('dxtn_sv.svxtn_dattn = 1')->whereNull('dxtn_sv.svxtn_vipham');
                    }
                })
                ->select(DB::raw('DISTINCT sv.*, lh.*, kdt.kdt_khoa, dt_dxtn.dt_id, dxtn_sv.dxtn_id, dxtn_sv.svxtn_dattn, dxtn_sv.svxtn_vipham, dxtn_sv.svxtn_ghichu'))
                ->get();


            if ($danhSachSinhVienLop->isEmpty()) {
                unset($danhSachNganhNghe[$index]);
            } else {
                $dxtn_id = $danhSachSinhVienLop[0]->dxtn_id;
                $dt_id = $danhSachSinhVienLop[0]->dt_id;
                $lh_id = $danhSachSinhVienLop[0]->lh_id;
                $lh_nienche = $danhSachSinhVienLop[0]->lh_nienche;
                $lh_ten = $danhSachSinhVienLop[0]->lh_ten;
                $kdt_khoa = $danhSachSinhVienLop[0]->kdt_khoa;

                $data = $export->getKetQuaHocTapTheoDotThiTest($reqSemester, $nn->lh_id, $dt_id, $dxtn_id);
                $lopHoc = $data['lopHoc'];

                $danhSachSinhVienAll = $data['danhSachSinhVien'];
                $dsSv_idArr = $danhSachSinhVienLop->pluck('sv_id')->toArray();

                $danhSachSinhVienLop = $danhSachSinhVienAll->filter(function ($sinhVien) use ($dsSv_idArr) {
                    return in_array($sinhVien->sv_id, $dsSv_idArr);
                })->map(function ($sinhVien) use ($dt_id, $lh_id, $lh_nienche, $lh_ten, $kdt_khoa, $lopHoc) {
                    // Thêm thuộc tính mới
                    $sinhVien->dt_id = $dt_id;
                    $sinhVien->lh_id = $lh_id;
                    $sinhVien->lh_nienche = $lh_nienche;
                    $sinhVien->lh_ten = $lh_ten;
                    $sinhVien->kdt_khoa = $kdt_khoa;
                    $sinhVien->lopHoc = $lopHoc;

                    return $sinhVien;
                })->values();
                $nn->danhsachsinhvien = $danhSachSinhVienLop;
            }
        }

        if (!$danhSachNganhNghe->isEmpty()) {
            //  dd($danhSachNganhNghe->values());
            return $export->downloadKetQuaSinhVienThiTNDatKhongDat($danhSachNganhNghe->values(), $loai, $dxtn_id);
        } else {
            return redirect()->back();
        }
    }

    // public function exportDanhSachKetQuaXetTNheoDotXetTN(Request $request, $dxtn_id)
    // {
    //     $reqSemester = 123456;
    //     $export = new KetQuaHocTap;
    //     $search = $request->search;
    //     $loai = $request->loai;

    //     $dxtn = DotThiDotXetTotNghiep::find($dxtn_id);
    //     $dt_id = $dxtn->dt_id;

    //     $danhSachNganhNghe = DB::table('qlsv_lophoc as lh')
    //         ->join('qlsv_dotthi_bangdiem as bd', function ($join) {
    //             $join->on('bd.lh_id', '=', 'lh.lh_id');
    //         })
    //         ->join('qlsv_khoadaotao as kdt', function ($join) {
    //             $join->on('kdt.kdt_id', '=', 'lh.kdt_id');
    //         })
    //         ->join('qlsv_nganhnghe as nn', function ($join) {
    //             $join->on('nn.nn_id', '=', 'kdt.nn_id');
    //         })
    //         ->where('bd.dt_id', '=', $dt_id)
    //         ->select(DB::raw('DISTINCT nn.*, bd.dt_id, kdt.*, bd.lh_id'))
    //         ->get();

    //     foreach ($danhSachNganhNghe as $index => $nn) {
    //         // $danhSachSinhVienLop = DB::table('qlsv_dotxettotnghiep_sinhvien as dxtn_sv')
    //         //     ->join('qlsv_dotthi_dotxettotnghiep as dt_dxtn', function ($join) {
    //         //         $join->on('dt_dxtn.dxtn_id', '=', 'dxtn_sv.dxtn_id');
    //         //     })
    //         //     ->join('qlsv_sinhvien as sv', function ($join) {
    //         //         $join->on('sv.sv_id', '=', 'dxtn_sv.sv_id');
    //         //     })
    //         //     ->join('qlsv_lophoc as lh', function ($join) {
    //         //         $join->on('lh.lh_id', '=', 'dxtn_sv.lh_id');
    //         //     })
    //         //     ->join('qlsv_khoadaotao as kdt', function ($join) {
    //         //         $join->on('kdt.kdt_id', '=', 'lh.kdt_id');
    //         //     })
    //         //     ->where('dxtn_sv.lh_id', '=', $nn->lh_id)
    //         //     ->where('dxtn_sv.dxtn_id', '=', $dxtn_id)
    //         //     ->where(function ($builder) use ($loai) {
    //         //         if (isset($loai)) {
    //         //             if ($loai == 'true') {
    //         //                 $builder->whereRaw('dxtn_sv.svxtn_dattn = 1')->whereNull('dxtn_sv.svxtn_vipham');
    //         //             } else {
    //         //                 $builder->whereRaw('dxtn_sv.svxtn_dattn = 0')->orWhere('dxtn_sv.svxtn_vipham', 1);
    //         //             }
    //         //         } else {
    //         //             $builder->whereRaw('dxtn_sv.svxtn_dattn = 1')->whereNull('dxtn_sv.svxtn_vipham');
    //         //         }
    //         //     })
    //         //     ->select(DB::raw('DISTINCT sv.*, lh.*, kdt.kdt_khoa, dt_dxtn.dt_id, dxtn_sv.dxtn_id, dxtn_sv.svxtn_dattn, dxtn_sv.svxtn_vipham, dxtn_sv.svxtn_ghichu'))
    //         //     ->get();

    //         // $dxtn_id = $danhSachSinhVienLop[0]->dxtn_id;
    //         // $dt_id = $danhSachSinhVienLop[0]->dt_id;
    //         // $lh_id = $danhSachSinhVienLop[0]->lh_id;
    //         // $lh_nienche = $danhSachSinhVienLop[0]->lh_nienche;
    //         // $lh_ten = $danhSachSinhVienLop[0]->lh_ten;
    //         // $kdt_khoa = $danhSachSinhVienLop[0]->kdt_khoa;

    //         $data = $export->getKetQuaHocTapTheoDotThiTest($reqSemester, $nn->lh_id, $dt_id, $dxtn_id);
    //         //$lopHoc = $data['lopHoc'];
    //         //     $sinhVien->lh_nienche = $lh_nienche;



    //         $danhSachSinhVien = $data['danhSachSinhVien'];
    //         // $dsSv_idArr = $danhSachSinhVienLop->pluck('sv_id')->toArray();

    //         // $danhSachSinhVienLop = $danhSachSinhVien->filter(function ($sinhVien) use ($dsSv_idArr) {
    //         //     return in_array($sinhVien->sv_id, $dsSv_idArr);
    //         // })->map(function ($sinhVien) use ($dt_id, $lh_id, $lh_nienche, $lh_ten, $kdt_khoa, $lopHoc) {
    //         //     // Thêm thuộc tính mới
    //         //     $sinhVien->dt_id = $dt_id;
    //         //     $sinhVien->lh_id = $lh_id;
    //         //     $sinhVien->lh_nienche = $lh_nienche;
    //         //     $sinhVien->lh_ten = $lh_ten;
    //         //     $sinhVien->kdt_khoa = $kdt_khoa;
    //         //     $sinhVien->lopHoc = $lopHoc;

    //         //     return $sinhVien;
    //         // })->values();

    //         $khoaDaoTao = KhoaDaoTao::where('kdt_id', '=', $data['lopHoc']->kdt_id)->first();
    //         if ($danhSachSinhVien->isEmpty()) {
    //             unset($danhSachNganhNghe[$index]);
    //         } else {
    //             $nn->danhsachsinhvien = $danhSachSinhVien;
    //             $nn->lopHoc = $data['lopHoc'];
    //             $nn->khoaDaoTao = $khoaDaoTao;
    //         }
    //     }

    //     //  dd($danhSachNganhNghe->values());
    //     return $export->downloadKetQuaSinhVienThiTNDatKhongDat($danhSachNganhNghe->values(), $loai, $dxtn_id);
    // }

    public function capNhatTrangThaiSinhVienDotXetTN($dxtn_id, Request $request)
    {
        DB::transaction(function () use ($request, $dxtn_id) {
            $sv = $request->svid;
            $dxtn_id = $request->dxtn_id;
            $dt_id = $request->dt_id;
            $lh_id = $request->lhid;
            $svxtn_ghichu = $request->svxtn_ghichu == "null" ? NULL : $request->svxtn_ghichu;
            $svxtn_vipham = $request->svxtn_vipham == "null" ? NULL : $request->svxtn_vipham;

            $dxtn_sv = DB::table('qlsv_dotxettotnghiep_sinhvien as dxtn_sv')
                ->whereRaw('dxtn_sv.sv_id = ' . $sv . ' and dxtn_sv.dxtn_id = ' . $dxtn_id
                    . ' and dxtn_sv.lh_id = ' . $lh_id)
                ->first();

            if ($dxtn_sv) {
                DB::table('qlsv_dotxettotnghiep_sinhvien as dxtn_sv')
                    ->whereRaw('dxtn_sv.sv_id = ' . $sv . ' and dxtn_sv.dxtn_id = ' . $dxtn_id
                        . ' and dxtn_sv.lh_id = ' . $lh_id)
                    ->update([
                        'svxtn_vipham' => $svxtn_vipham,
                        'svxtn_ghichu' => $svxtn_ghichu
                    ]);
            }
        });
    }

    public function xemDanhSachSinhVienThamGiaDotXetTN($id)
    {
        $dotxettotnghiep = DotXetTotNghiep::find($id);
        $dt_id = DotThiDotXetTotNghiep::find($id)->dt_id;
        $dotthi = DotThi::find($dt_id);

        $quyetDinh = false;
        // Có quyết định rồi sẽ có quyền nhập điểm
        if ($dotxettotnghiep->dxtn_qd_trangthai == 0) {
            $quyetDinh = true;
        }

        return view('qlsv.dotxettotnghiep.dotxettotnghiep_sinhvienthamgia', compact(['dotxettotnghiep', 'dotthi', 'quyetDinh']));
    }

    public function exportDanhSachXetTotNghiep(Request $request, $dt_id)
    {
        $export = new KetQuaHocTap;
        $lh_id = $request->lh_id;

        return $export->downloadMonHocDotThi($dt_id, $lh_id);
    }


    // public function ketQuaHocTap(Request $request, $lh_id, $dxtn_id)
    // {
    //     $reqSemester = 123456;
    //     $export = new KetQuaHocTap;
    //     $dsDotThi = DB::select('select DISTINCT dt_id from qlsv_dotthi_dotxettotnghiep where dxtn_id = ? AND lh_id = ?', [$dxtn_id, $lh_id]);
    //     $dsRs = [];
    //     foreach($dsDotThi as $dtIndex => $dt_id) {
    //         $dotThi = DotThi::find($dt_id->dt_id);
    //         $data = $export->getKetQuaHocTapTheoDotThi($reqSemester, $lh_id, $dt_id->dt_id);
    //         $dotThi->reqSemester = $data['reqSemester'];
    //         $dotThi->reqYear = $data['reqYear'];
    //         $dotThi->lopHoc = $data['lopHoc'];
    //         $dotThi->danhSachSinhVien = $data['danhSachSinhVien'];
    //         $dotThi->danhSachMonTheoDotThi = $data['danhSachMonTheoDotThi'];
    //         $dotThi->danhSachNamHoc = $data['danhSachNamHoc'];
    //         $dotThi->semesters = $data['semesters'];
    //         $dotThi->sumTinChi = $data['sumTinChi'];
    //         $dotThi->chunkedNotes = $data['notes']->splitIn(3);
    //         array_push($dsRs, $dotThi);
    //     }
    //     return view('qlsv.dotxettotnghiep.dotxettotnghiep_ketquahoctap', compact([
    //             'dsRs', 'dxtn_id'
    //     ]));
    // }


    public function updateQdTrangThai(request $request, $dxtn_id)
    {

        $dxtn_qd_trangthai = $request->dxtn_qd_trangthai;

        // $sinhVienDotXTN = DB::table('qlsv_dotxettotnghiep_sinhvien as dxtn_sv')
        //     ->where('dxtn_sv.dxtn_id', $dxtn_id)
        //     ->where('dxtn_sv.svxtn_dattn', 1)
        //     ->whereNull('dxtn_sv.svxtn_vipham')
        //     ->get();

        // $qd_id = DotXetTotNghiep::find($dxtn_id)->qd_id;

        // if ($qd_id != -1) {
        //     foreach ($sinhVienDotXTN as $sinhvien) {
        //         $modelSV = new SinhVien();
        //         $modelSV->sv_id = $sinhvien->sv_id;
        //         $hocky = 4;

        //         // Trạng thái áp dụng
        //         if ($dxtn_qd_trangthai == 1) {
        //             if ($modelSV->quyetDinhTotNghiep()->exists()) {
        //                 DB::table('qlsv_sinhvien_quyetdinh')->whereQdId($qd_id->first()->qd_id)
        //                     ->whereSvId($modelSV->sv_id)->delete();
        //             }
        //             $modelSV->quyetDinhTotNghiep()->attach($qd_id, ['qd_hocky' => $hocky]);
        //         } else {
        //             DB::table('qlsv_sinhvien_quyetdinh')
        //                 ->where('sv_id', $modelSV->sv_id)
        //                 ->where('qd_id', $qd_id)
        //                 ->delete();
        //         }
        //     }
        // }

        $model = DB::table('qlsv_dotxettotnghiep')
            ->where('dxtn_id', $dxtn_id)
            ->update(['dxtn_qd_trangthai' => $dxtn_qd_trangthai]);

        return response()->json($model);
    }

    public function ketQuaHocTap(Request $request, $lh_id, $dt_id)
    {
        $reqSemester = 123456;
        $export = new KetQuaHocTap;

        $dxtn_id = DotThiDotXetTotNghiep::where('dt_id', $dt_id)->first()->dxtn_id;
        $data = $export->getKetQuaHocTapTheoDotXetTN($reqSemester, $lh_id, $dt_id, $dxtn_id);

        $reqSemester = $data['reqSemester'];
        $reqYear = $data['reqYear'];
        $lopHoc = $data['lopHoc'];
        $dotThi = DotThi::find($dt_id);
        $danhSachSinhVien = $data['danhSachSinhVien'];
        $danhSachNamHoc = $data['danhSachNamHoc'];
        $semesters = $data['semesters'];
        $sumTinChi = $data['sumTinChi'];
        $chunkedNotes = $data['notes'];
        $danhSachDotXetTotNghiep = [];
        //DotXetTotNghiep::orderBy('dxtn_id', 'desc')->get();
        //  dd($danhSachSinhVien);

        $note = $data['notes'];
        $validValues = ['RLHK2', 'ĐTBN1', 'ĐTBHK1', 'RLHK1', 'ĐTBHK2', 'RLN1', 'ĐTBN2', 'RLN2', 'ĐTN', 'ĐTBN3', 'RLN3', 'ĐTBNTK', 'ĐTBCTL'];

        $additionalItems = collect([
            ['key' => 'ĐTB', 'value' => 'Điểm trung bình chung toàn khóa học'],
            ['key' => 'ĐTNCT', 'value' => 'Điểm thi môn Chính trị'],
            ['key' => 'ĐTNLT', 'value' => 'Điểm thi môn Lý thuyết chuyên môn'],
            ['key' => 'ĐTNTH', 'value' => 'Điểm thi môn Thực hành'],
            ['key' => 'ĐTN', 'value' => 'Điểm đánh giá xếp loại tốt nghiệp'],
            ['key' => 'XLTN', 'value' => 'Xếp loại tốt nghiệp']
        ]);
        $note = $note->concat($additionalItems);

        $chunkedNotes = $note->reject(function ($item) use ($validValues) {
            return in_array($item['key'], $validValues);
        })->splitIn(3);


        $filter = new Filters;
        $danhSachSinhVien = $filter->KetQuaSinhVienThamDuThiTN($danhSachSinhVien, $lh_id, $dt_id, $lopHoc);
        // Lặp qua danh sách sinh viên đã sắp xếp
        foreach ($danhSachSinhVien as $sinhvien) {
            // Kiểm tra nếu ghiChu là "Chưa đạt"
            if ($sinhvien->ghiChu == "Chưa đạt") {
                $sinhvien->toanKhoa->avg_totnghiep = 0;
                $sinhvien->toanKhoa->final_xltn == 'NA';
            } else if ($sinhvien->svxtn_vipham == 1) {
                $sinhvien->toanKhoa->avg_totnghiep = 0;
                $sinhvien->ghiChu = "(Chưa đạt) lý do: " . $sinhvien->ghiChu;
                $sinhvien->toanKhoa->final_xltn == 'NA';
            } else if ($sinhvien->giamXltn == true) {
                $sinhvien->ghiChu = "Giảm một mức XLTN";
            }
        }

        $danhSachSinhVien = $danhSachSinhVien->sortBy([
            function ($a, $b) {
                if (isset ($a->toanKhoa->avg_totnghiep) && isset ($b->toanKhoa->avg_totnghiep)) {
                    return $b->toanKhoa->avg_totnghiep <=> $a->toanKhoa->avg_totnghiep;
                } else if (isset ($b->toanKhoa->avg_totnghiep)) {
                    return 1;
                } else if (isset ($a->toanKhoa->avg_totnghiep)) {
                    return -1;
                }
                return 0;
            },
            function ($a, $b) {
                return $a->sv_ma <=> $b->sv_ma;
            }
        ]);


        $dxtn_qd_trangthai = DB::table('qlsv_dotxettotnghiep as dxtn')
            ->where('dxtn.dxtn_id', $dxtn_id)
            ->select(DB::raw('DISTINCT dxtn.dxtn_qd_trangthai'))
            ->get();
        $dxtn_qd_trangthai = $dxtn_qd_trangthai[0]->dxtn_qd_trangthai;

        return view('qlsv.dotxettotnghiep.dotxettotnghiep_ketquahoctap', compact([
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
            'dt_id',
            'dxtn_id',
            'lh_id',
            'dxtn_qd_trangthai'
        ]));
    }


    public function nhapDotXetTotNghiep(Request $request)
    {
        $dsSinhVien = $request->sinhVien;
        $lopHoc = $request->lopHoc;
        $dotThi = $request->dotThi;
        $dotXetTotNghiep = $request->dotXetTotNghiep;
        DB::transaction(function () use ($dsSinhVien, $lopHoc, $dotThi, $dotXetTotNghiep) {
            foreach ($dsSinhVien as $svIndex => $sinhVien) {
                if (
                    DB::select(
                        'select * from qlsv_dotthi_dotxettotnghiep where dt_id = ? AND sv_id = ? AND dxtn_id = ? AND lh_id = ?',
                        [$dotThi, $sinhVien['sv_id'], $dotXetTotNghiep, $lopHoc]
                    ) == null
                ) {
                    DB::insert(
                        'insert into qlsv_dotthi_dotxettotnghiep (dt_id, sv_id, dxtn_id, lh_id) values (?, ?, ?, ?)',
                        [$dotThi, $sinhVien['sv_id'], $dotXetTotNghiep, $lopHoc]
                    );
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
    public function store(DotXetTotNghiepEditRequest $request)
    {
        $quyetDinhPassedData = $request->only(['qd_id', 'qd_ten', 'qd_ngay', 'qd_ma']);
        $loai = 1;
        $dotXetTotNghiepResult = null; // Biến để lưu kết quả

        DB::transaction(function () use ($loai, $quyetDinhPassedData, $request, &$dotXetTotNghiepResult) {
            $qd_id = $quyetDinhPassedData['qd_id'];

            if ($qd_id == 0) {
                $modelQD = new QuyetDinh();
                $modelQD->fill($quyetDinhPassedData);
                $modelQD->qd_loai = $loai;
                $modelQD->save();
                $qd_id = $modelQD->qd_id;
            }


            $passedData = $request->only(['dxtn_hdt_id', 'dxtn_ten', 'dxtn_tungay', 'dxtn_tuthang', 'dxtn_tunam', 'dxtn_denngay', 'dxtn_denthang', 'dxtn_dennam', 'dxtn_ghichu']);
            // != -1
            // if ($qd_id > 0) {
            $passedData = array_merge($passedData, ['qd_id' => $qd_id]);
            // }
            $model = new DotXetTotNghiep;
            $model->fill($passedData);
            $model->save();
            $dotXetTotNghiepResult = $model;
        });
        return response()->json($dotXetTotNghiepResult);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getDotXetTotNghiep($id)
    {
        $dotXetTNModel = DotXetTotNghiep::find($id);
        $dotXetTNModel->quyet_dinh = $dotXetTNModel->quyetDinh;
        return response()->json($dotXetTNModel);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(DotXetTotNghiepEditRequest $request, $dxtn_id)
    {
        // DB::transaction(function () use ($request, $dxtn_id) {
        // $dxtn_qd_trangthai = $request->dxtn_qd_trangthai;


        $quyetDinhPassedData = $request->only(['qd_id', 'qd_id_root', 'qd_ten', 'qd_ngay', 'qd_ma']);
        $loai = 1;
        $dotThiXetTNResult = null; // Biến để lưu kết quả

        DB::transaction(function () use ($dxtn_id, $loai, $quyetDinhPassedData, $request, &$dotThiXetTNResult) {
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

            $passedData = $request->only(['dxtn_ten', 'dxtn_tungay', 'dxtn_tuthang', 'dxtn_tunam', 'dxtn_denngay', 'dxtn_denthang', 'dxtn_dennam', 'dxtn_ghichu', 'dxtn_qd_trangthai', 'qd_id']);
            $passedData = array_merge($passedData, ['qd_id' => $qd_id]);

            $model = DotXetTotNghiep::find($dxtn_id);
            $model->fill($passedData);
            $model->save();
            $dotThiXetTNResult = $model;
        });
        return response()->json($dotThiXetTNResult);



        // $sinhVienDotXTN = DB::table('qlsv_dotxettotnghiep_sinhvien as dxtn_sv')
        //     ->where('dxtn_sv.dxtn_id',  $dxtn_id)
        //     ->where('dxtn_sv.svxtn_dattn',  1)
        //     ->whereNull('dxtn_sv.svxtn_vipham')
        //     ->get();

        // // dd($dxtn_qd_trangthai);

        // $qd_id = DotXetTotNghiep::find($dxtn_id)->qd_id;

        // foreach ($sinhVienDotXTN as $sinhvien) {
        //     $modelSV = new SinhVien();
        //     $modelSV->sv_id = $sinhvien->sv_id;
        //     $hocky = 4;

        //     // Trạng thái áp dụng
        //     if ($dxtn_qd_trangthai == 1) {
        //         if ($modelSV->quyetDinhTotNghiep()->exists()) {
        //             DB::table('qlsv_sinhvien_quyetdinh')->whereQdId($qd_id->first()->qd_id)
        //                 ->whereSvId($modelSV->sv_id)->delete();
        //         }
        //         $modelSV->quyetDinhTotNghiep()->attach($qd_id, ['qd_hocky' => $hocky]);
        //     } else {
        //         DB::table('qlsv_sinhvien_quyetdinh')
        //             ->where('sv_id', $modelSV->sv_id)
        //             ->where('qd_id', $qd_id)
        //             ->delete();
        //     }
        // }

        // });
    }

    public function getDsMonHocTheoLop(Request $rq)
    {

        \Log::debug($rq);
        $dsMonHoc = DB::table('qlsv_dotxettotnghiep_bangdiem as dtbd')
            ->join('qlsv_monhoc as mh', 'mh.mh_id', '=', 'dtbd.mh_id')
            ->leftJoin('qlsv_dotxettotnghiep_diem as d', 'd.dxtn_bd_id', '=', 'dtbd.dxtn_bd_id')
            ->where('dtbd.dxtn_id', $rq->dxtn_id)
            ->where('dtbd.lh_id', $rq->lh_id)
            ->select('mh.*', 'dtbd.*', DB::raw('COUNT(d.dxtn_bd_id) >  0 as codiem'))
            ->groupBy('mh.mh_id', 'dtbd.dxtn_bd_id')
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
        // $model = DotXetTotNghiep::find($id);
        // $model->delete();
        // return response()->json($model);

        $model = DotXetTotNghiep::find($id);
        $model->forceDelete();
        return response()->json($model);
    }

    public function destroyDxtnLop($dxtn_id, Request $request)
    {
        $lh_id = $request->lh_id;
        $dt_id = $request->dt_id;

        // return dd($dxtn_id . " - " . $lh_id  . " - " . $dt_id);
        DB::transaction(function () use ($dxtn_id, $lh_id, $dt_id) {

            $model = DB::table('qlsv_dotxettotnghiep_sinhvien as dxtn_sv')
                ->join('qlsv_dotthi_dotxettotnghiep as dt_dxtn', 'dxtn_sv.dxtn_id', 'dt_dxtn.dxtn_id')
                ->join('qlsv_dotthi_bangdiem as dt_bd', 'dt_dxtn.dt_id', 'dt_bd.dt_id')
                ->where('dxtn_sv.lh_id', '=', $lh_id)
                ->where('dxtn_sv.dxtn_id', '=', $dxtn_id)
                ->where('dt_bd.dt_id', '=', $dt_id)
                ->select('dxtn_sv.sv_id')
                ->distinct()
                ->delete();

            $dxtn_dt = DB::table('qlsv_dotxettotnghiep_sinhvien as dxtn_sv')
                ->where('dxtn_sv.dxtn_id', '=', $dxtn_id)
                ->get();


            if ($dxtn_dt->isEmpty()) {
                // $dxtn = DotThiDotXetTotNghiep::find($dxtn_id);
                DB::table('qlsv_dotthi_dotxettotnghiep')
                    ->where('dxtn_id', '=', $dxtn_id)->delete();
            }

            return response()->json($model);
        });

        // dd($danhSachSinhVienLop);

        // $model = DotXetTotNghiep::find($dxtn_id);
        // $model->delete();
    }
}
