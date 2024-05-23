<?php

namespace App\Http\Controllers;

use App\Models\DotThiDotXetTotNghiep;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Filters extends Controller
{
    public function LocSinhVienKhongDatTN($danhSachSinhVien, $lopHoc)
    {
        // lọc danh sinh viên đạt và không đạt
        foreach ($danhSachSinhVien->values() as $sIndex => $sinhVien) {
            $datTotNghep = true;
            // svd_loai = 0 là thi TN
            // svd_loai = 1 là báo cáo luận văn
            if ($sinhVien->svd_loai == 1) {
                if ($sinhVien->khoaluanlan1 != -1) {
                    if ($sinhVien->khoaluanlan1 < 5) {
                        $datTotNghep = false;
                    }
                    else {
                        if($sinhVien->svd_lanthihientai == 2 || $sinhVien->svd_lanthihientai == 3) {
                            $datTotNghep = true;
                        }
                    }

                }

                if ($sinhVien->khoaluanlan2 != -1) {
                    if ($sinhVien->khoaluanlan2 < 5) {
                        $datTotNghep = false;
                    }
                    else {
                        if($sinhVien->svd_lanthihientai == 2 || $sinhVien->svd_lanthihientai == 3) {
                            $datTotNghep = true;
                        }
                    }

                }

                if ($sinhVien->khoaluanlan3 != -1) {
                    if ($sinhVien->khoaluanlan3 < 5) {
                        $datTotNghep = false;
                    }
                    else {
                        if($sinhVien->svd_lanthihientai == 2 || $sinhVien->svd_lanthihientai == 3) {
                            $datTotNghep = true;
                        }
                    }

                }
            } else {
                // Lần 1
                if($sinhVien->svd_lanthihientai == 1) {
                    if ($lopHoc->lh_nienche == 0) {
                        if ($sinhVien->chinhtrilan1 != -1) {
                            if ($sinhVien->chinhtrilan1 < 5) {
                                $datTotNghep = false;
                            }
                        }
                    }

                    if ($sinhVien->lythuyetlan1 != -1) {
                        if ($sinhVien->lythuyetlan1 < 5) {
                            $datTotNghep = false;
                        }
                    }

                    if ($sinhVien->thuchanhlan1 != -1) {
                        if ($sinhVien->thuchanhlan1 < 5) {
                            $datTotNghep = false;
                        }
                    }
                }

                // Lần 2
                if($sinhVien->svd_lanthihientai == 2) {
                    if ($lopHoc->lh_nienche == 0) {
                        if ($sinhVien->chinhtrilan2 != -1) {
                            if ($sinhVien->chinhtrilan2 < 5) {
                                $datTotNghep = false;
                            }
                        }
                    }

                    if ($sinhVien->lythuyetlan2 != -1) {
                        if ($sinhVien->lythuyetlan2 < 5) {
                            $datTotNghep = false;
                        }
                    }

                    if ($sinhVien->thuchanhlan2 != -1) {
                        if ($sinhVien->thuchanhlan2 < 5) {
                            $datTotNghep = false;
                        }
                    }
                }

                // Lần 3
                if($sinhVien->svd_lanthihientai == 3) {
                    if ($lopHoc->lh_nienche == 0) {
                        if ($sinhVien->chinhtrilan3 != -1) {
                            if ($sinhVien->chinhtrilan3 < 5) {
                                $datTotNghep = false;
                            }
                        }
                    }

                    if ($sinhVien->lythuyetlan3 != -1) {
                        if ($sinhVien->lythuyetlan3 < 5) {
                            $datTotNghep = false;
                        }
                    }

                    if ($sinhVien->thuchanhlan3 != -1) {
                        if ($sinhVien->thuchanhlan3 < 5) {
                            $datTotNghep = false;
                        }
                    }
                }
            }

            if (isset($sinhVien->toanKhoa->avg_totnghiep)) {
                if ($sinhVien->toanKhoa->avg_totnghiep < 5) {
                    $datTotNghep = false;
                }
            } else {
                // Không có điểm Đ_TN
                $datTotNghep = false;
            }


            if ($datTotNghep) {
                $sinhVien->datTotNghep = true;
                $sinhVien->ghiChu = "";

                if ($sinhVien->svxtn_vipham != null && $sinhVien->svxtn_vipham == 1) {
                    $sinhVien->ghiChu = $sinhVien->svxtn_ghichu;
                }
            } else {
                $sinhVien->datTotNghep = false;
                $sinhVien->toanKhoa->hocLucTN = 'Rớt';
                if ($sinhVien->svd_ghichu != null) {
                    $sinhVien->ghiChu = $sinhVien->svd_ghichu;
                } else {
                    $sinhVien->ghiChu = 'Chưa đạt';
                }
            }
        }
        return  $danhSachSinhVien->sortByDesc('datTotNghep')->values();
    }


    public function SinhVienDatThiTN($lopHoc, $danhSachSinhVien)
    {
        // lọc danh sinh viên đạt và không đạt
        foreach ($danhSachSinhVien as $sinhVien) {
            $datTotNghep = true;
            // svd_loai = 0 là thi TN
            // svd_loai = 1 là báo cáo luận văn
            if ($sinhVien->svd_loai == 1) {
                if ($sinhVien->khoaluanlan1 != -1) {
                    if ($sinhVien->khoaluanlan1 < 5) {
                        $datTotNghep = false;
                    }
                    else {
                        if($sinhVien->svd_lanthihientai == 2 || $sinhVien->svd_lanthihientai == 3)
                        $datTotNghep = true;
                    }

                }

                if ($sinhVien->khoaluanlan2 != -1) {
                    if ($sinhVien->khoaluanlan2 < 5) {
                        $datTotNghep = false;
                    }
                    else {
                        if($sinhVien->svd_lanthihientai == 2 || $sinhVien->svd_lanthihientai == 3)
                        $datTotNghep = true;
                    }

                }

                if ($sinhVien->khoaluanlan3 != -1) {
                    if ($sinhVien->khoaluanlan3 < 5) {
                        $datTotNghep = false;
                    }
                    else {
                        if($sinhVien->svd_lanthihientai == 2 || $sinhVien->svd_lanthihientai == 3)
                        $datTotNghep = true;
                    }

                }
            } else {
                // Lần 1
                if($sinhVien->svd_lanthihientai == 1) {
                    if ($lopHoc->lh_nienche == 0) {
                        if ($sinhVien->chinhtrilan1 != -1) {
                            if ($sinhVien->chinhtrilan1 < 5) {
                                $datTotNghep = false;
                            }
                        }
                    }

                    if ($sinhVien->lythuyetlan1 != -1) {
                        if ($sinhVien->lythuyetlan1 < 5) {
                            $datTotNghep = false;
                        }
                    }

                    if ($sinhVien->thuchanhlan1 != -1) {
                        if ($sinhVien->thuchanhlan1 < 5) {
                            $datTotNghep = false;
                        }
                    }
                }

                // Lần 2
                if($sinhVien->svd_lanthihientai == 2) {
                    if ($lopHoc->lh_nienche == 0) {
                        if ($sinhVien->chinhtrilan2 != -1) {
                            if ($sinhVien->chinhtrilan2 < 5) {
                                $datTotNghep = false;
                            }
                        }
                    }

                    if ($sinhVien->lythuyetlan2 != -1) {
                        if ($sinhVien->lythuyetlan2 < 5) {
                            $datTotNghep = false;
                        }
                    }

                    if ($sinhVien->thuchanhlan2 != -1) {
                        if ($sinhVien->thuchanhlan2 < 5) {
                            $datTotNghep = false;
                        }
                    }
                }

                // Lần 3
                if($sinhVien->svd_lanthihientai == 3) {
                    if ($lopHoc->lh_nienche == 0) {
                        if ($sinhVien->chinhtrilan3 != -1) {
                            if ($sinhVien->chinhtrilan3 < 5) {
                                $datTotNghep = false;
                            }
                        }
                    }

                    if ($sinhVien->lythuyetlan3 != -1) {
                        if ($sinhVien->lythuyetlan3 < 5) {
                            $datTotNghep = false;
                        }
                    }

                    if ($sinhVien->thuchanhlan3 != -1) {
                        if ($sinhVien->thuchanhlan3 < 5) {
                            $datTotNghep = false;
                        }
                    }
                }
            }

            if (isset($sinhVien->toanKhoa->avg_totnghiep)) {
                if ($sinhVien->toanKhoa->avg_totnghiep < 5) {
                    $datTotNghep = false;
                }
            }
        }

        if ($datTotNghep) {
            return 1;
        } else {
            return 0;
        }
    }

    public function SvDatThiTN($lopHoc, $sinhVien)
    {
        // lọc danh sinh viên đạt và không đạt
            $datTotNghep = true;
            // svd_loai = 0 là thi TN
            // svd_loai = 1 là báo cáo luận văn
            if ($sinhVien->svd_loai == 1) {
                if ($sinhVien->khoaluanlan1 != -1) {
                    if ($sinhVien->khoaluanlan1 < 5) {
                        $datTotNghep = false;
                    }
                    else {
                        if($sinhVien->svd_lanthihientai == 2 || $sinhVien->svd_lanthihientai == 3)
                        $datTotNghep = true;
                    }

                }

                if ($sinhVien->khoaluanlan2 != -1) {
                    if ($sinhVien->khoaluanlan2 < 5) {
                        $datTotNghep = false;
                    }
                    else {
                        if($sinhVien->svd_lanthihientai == 2 || $sinhVien->svd_lanthihientai == 3)
                        $datTotNghep = true;
                    }

                }

                if ($sinhVien->khoaluanlan3 != -1) {
                    if ($sinhVien->khoaluanlan3 < 5) {
                        $datTotNghep = false;
                    }
                    else {
                        if($sinhVien->svd_lanthihientai == 2 || $sinhVien->svd_lanthihientai == 3)
                        $datTotNghep = true;
                    }

                }
            } else {
                // Lần 1
                if($sinhVien->svd_lanthihientai == 1) {
                    if ($lopHoc->lh_nienche == 0) {
                        if ($sinhVien->chinhtrilan1 != -1) {
                            if ($sinhVien->chinhtrilan1 < 5) {
                                $datTotNghep = false;
                            }
                        }
                    }

                    if ($sinhVien->lythuyetlan1 != -1) {
                        if ($sinhVien->lythuyetlan1 < 5) {
                            $datTotNghep = false;
                        }
                    }

                    if ($sinhVien->thuchanhlan1 != -1) {
                        if ($sinhVien->thuchanhlan1 < 5) {
                            $datTotNghep = false;
                        }
                    }
                }

                // Lần 2
                if($sinhVien->svd_lanthihientai == 2) {
                    if ($lopHoc->lh_nienche == 0) {
                        if ($sinhVien->chinhtrilan2 != -1) {
                            if ($sinhVien->chinhtrilan2 < 5) {
                                $datTotNghep = false;
                            }
                        }
                    }

                    if ($sinhVien->lythuyetlan2 != -1) {
                        if ($sinhVien->lythuyetlan2 < 5) {
                            $datTotNghep = false;
                        }
                    }

                    if ($sinhVien->thuchanhlan2 != -1) {
                        if ($sinhVien->thuchanhlan2 < 5) {
                            $datTotNghep = false;
                        }
                    }
                }

                // Lần 3
                if($sinhVien->svd_lanthihientai == 3) {
                    if ($lopHoc->lh_nienche == 0) {
                        if ($sinhVien->chinhtrilan3 != -1) {
                            if ($sinhVien->chinhtrilan3 < 5) {
                                $datTotNghep = false;
                            }
                        }
                    }

                    if ($sinhVien->lythuyetlan3 != -1) {
                        if ($sinhVien->lythuyetlan3 < 5) {
                            $datTotNghep = false;
                        }
                    }

                    if ($sinhVien->thuchanhlan3 != -1) {
                        if ($sinhVien->thuchanhlan3 < 5) {
                            $datTotNghep = false;
                        }
                    }
                }
            }

            if (isset($sinhVien->toanKhoa->avg_totnghiep)) {
                if ($sinhVien->toanKhoa->avg_totnghiep < 5) {
                    $datTotNghep = false;
                }
            }


        if ($datTotNghep) {
            return 1;
        } else {
            return 0;
        }
    }

    public function LocSinhVienKhongDuDKThiTN($danhSachSinhVien)
    {
        // lọc danh sinh viên đạt và không đạt điều kiện thi tốt nghiệp
        foreach ($danhSachSinhVien->values() as $sIndex => $sinhVien) {
            $sinhVienDuDkThiTN = true;
            foreach ($sinhVien->years as $yIndex => $year) {
                foreach ($year->semesters as $semester) {
                    // 4. Điểm các môn trong học kỳ
                    foreach ($semester->monHoc as $xIndex => $monHoc) {
                        // if($xIndex == 3) {
                        //     dd($monHoc->ketQua);
                        // }
                        if ($monHoc->ketQua) {
                            $final = $monHoc->ketQua->display_score;
                        }

                        if ($final < 5) {
                            $sinhVienDuDkThiTN = false;
                        }
                    }
                }
            }

            if ($sinhVienDuDkThiTN == false) {
                $sinhVien->datTotNghep = false;
            } else if ($sinhVien->svd_khongDatloai == 1) {
                $sinhVien->datTotNghep = false;
            } else {
                $sinhVien->datTotNghep = true;
            }
        }

        return $danhSachSinhVien->sortByDesc('datTotNghep')->values();
    }


    public function LocSinhVienTN($danhSachSinhVien)
    {
        return  $danhSachSinhVien->sortByDesc('datTotNghep')->values();
    }

    public function LocSinhVienDatTN($danhSachSinhVien, $lopHoc)
    {
        $sinhVienDatTN = [];

        // lọc danh sinh viên đạt và không đạt
        foreach ($danhSachSinhVien->values() as $sIndex => &$sinhVien) {
            $datTotNghep = true;
            // svd_loai = 0 là thi TN
            // svd_loai = 1 là báo cáo luận văn
            if ($sinhVien->svd_loai == 1) {
                if ($sinhVien->khoaluanlan1 != -1) {
                    if ($sinhVien->khoaluanlan1 < 5) {
                        $datTotNghep = false;
                    }
                }

                if ($sinhVien->khoaluanlan2 != -1) {
                    if ($sinhVien->khoaluanlan2 < 5) {
                        $datTotNghep = false;
                    }
                }

                if ($sinhVien->khoaluanlan3 != -1) {
                    if ($sinhVien->khoaluanlan3 < 5) {
                        $datTotNghep = false;
                    }
                }
            } else {
                if ($lopHoc->lh_nienche == 0) {
                    if ($sinhVien->chinhtrilan1 != -1) {
                        if ($sinhVien->chinhtrilan1 < 5) {
                            $datTotNghep = false;
                        }
                    }
                }

                if ($sinhVien->lythuyetlan1 != -1) {
                    if ($sinhVien->lythuyetlan1 < 5) {
                        $datTotNghep = false;
                    }
                }

                if ($sinhVien->thuchanhlan1 != -1) {
                    if ($sinhVien->thuchanhlan1 < 5) {
                        $datTotNghep = false;
                    }
                }

                if ($lopHoc->lh_nienche == 0) {
                    if ($sinhVien->chinhtrilan2 != -1) {
                        if ($sinhVien->chinhtrilan2 < 5) {
                            $datTotNghep = false;
                        }
                    }
                }

                if ($sinhVien->lythuyetlan2 != -1) {
                    if ($sinhVien->lythuyetlan2 < 5) {
                        $datTotNghep = false;
                    }
                }

                if ($sinhVien->thuchanhlan2 != -1) {
                    if ($sinhVien->thuchanhlan2 < 5) {
                        $datTotNghep = false;
                    }
                }

                if ($lopHoc->lh_nienche == 0) {
                    if ($sinhVien->chinhtrilan3 != -1) {
                        if ($sinhVien->chinhtrilan3 < 5) {
                            $datTotNghep = false;
                        }
                    }
                }

                if ($sinhVien->lythuyetlan3 != -1) {
                    if ($sinhVien->lythuyetlan3 < 5) {
                        $datTotNghep = false;
                    }
                }

                if ($sinhVien->thuchanhlan3 != -1) {
                    if ($sinhVien->thuchanhlan3 < 5) {
                        $datTotNghep = false;
                    }
                }
            }

            if (isset($sinhVien->toanKhoa->avg_totnghiep)) {
                if ($sinhVien->toanKhoa->avg_totnghiep < 5) {
                    $datTotNghep = false;
                }
            }


            if ($datTotNghep) {
                $sinhVienDatTN[] = $sinhVien;
            }
        }

        return $sinhVienDatTN;
    }


    public function KetQuaSinhVienThamDuThiTN($danhSachSinhVien, $lh_id, $dt_id, $lopHoc)
    {
        $filter = new Filters;

        $dsSv_id = DB::table('qlsv_dotxettotnghiep_sinhvien as dxtn_sv')
            ->join('qlsv_dotthi_dotxettotnghiep as dt_dxt', function ($join) {
                $join->on('dxtn_sv.dxtn_id', '=', 'dt_dxt.dxtn_id');
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
            ->where('dxtn_sv.lh_id', '=', $lh_id)
            ->where('dt_dxt.dt_id', '=', $dt_id)
            ->select(DB::raw('DISTINCT sv.sv_id'))
            ->get();



        $dsSv_idArr = $dsSv_id->pluck('sv_id')->toArray(); // Chuyển danh sách $dsSv_id thành mảng sv_id

        $danhSachSinhVien = $danhSachSinhVien->filter(function ($sinhVien) use ($dsSv_idArr) {
            return in_array($sinhVien->sv_id, $dsSv_idArr);
        });


        // lọc danh sinh viên đạt và không đạt
        return $danhSachSinhVien = $filter->LocSinhVienKhongDatTN($danhSachSinhVien, $lopHoc);
    }

    public function SinhVienThiLaiTN($danhSachSinhVien, $lh_id, $dt_id, $lopHoc)
    {
        $filter = new Filters;

        $dsSv_id = DB::table('qlsv_dotxettotnghiep_sinhvien as dxtn_sv')
            ->join('qlsv_dotthi_dotxettotnghiep as dt_dxt', function ($join) {
                $join->on('dxtn_sv.dxtn_id', '=', 'dt_dxt.dxtn_id');
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
            ->where('dxtn_sv.lh_id', '=', $lh_id)
            ->where('dt_dxt.dt_id', '=', $dt_id)
            ->where('dxtn_sv.svxtn_dattn', '=', 0)
            ->select(DB::raw('DISTINCT sv.sv_id'))
            ->get();


        $dsSv_idArr = $dsSv_id->pluck('sv_id')->toArray(); // Chuyển danh sách $dsSv_id thành mảng sv_id

        $danhSachSinhVien = $danhSachSinhVien->filter(function ($sinhVien) use ($dsSv_idArr) {
            return in_array($sinhVien->sv_id, $dsSv_idArr);
        });


        // lọc danh sinh viên đạt và không đạt
        return $danhSachSinhVien = $filter->LocSinhVienKhongDatTN($danhSachSinhVien, $lopHoc);
    }

    // public function KetQuaSinhVienThamDuThiTNToanKhoa($danhSachSinhVien, $lh_id, $dt_id, $lopHoc)
    // {
    //     $filter = new Filters;

    //     $dsSv_id = DB::table('qlsv_dotxettotnghiep_sinhvien as dxtn_sv')
    //         ->join('qlsv_dotthi_dotxettotnghiep as dt_dxt', function ($join) {
    //             $join->on('dxtn_sv.dxtn_id', '=', 'dt_dxt.dxtn_id');
    //         })
    //         ->join('qlsv_sinhvien as sv', function ($join) {
    //             $join->on('sv.sv_id', '=', 'dxtn_sv.sv_id');
    //         })
    //         ->join('qlsv_lophoc as lh', function ($join) {
    //             $join->on('lh.lh_id', '=', 'dxtn_sv.lh_id');
    //         })
    //         ->join('qlsv_khoadaotao as kdt', function ($join) {
    //             $join->on('kdt.kdt_id', '=', 'lh.kdt_id');
    //         })
    //         ->where('dxtn_sv.lh_id', '=', $lh_id)
    //         ->where('dt_dxt.dt_id', '=', $dt_id)
    //         ->select(DB::raw('DISTINCT sv.sv_id'))
    //         ->get();



    //     $dsSv_idArr = $dsSv_id->pluck('sv_id')->toArray(); // Chuyển danh sách $dsSv_id thành mảng sv_id

    //     $danhSachSinhVien = $danhSachSinhVien->filter(function ($sinhVien) use ($dsSv_idArr) {
    //         return in_array($sinhVien->sv_id, $dsSv_idArr);
    //     });


    //     // lọc danh sinh viên đạt và không đạt
    //     return $danhSachSinhVien = $filter->LocSinhVienKhongDatTN($danhSachSinhVien, $lopHoc);
    // }

    public function KetQuaSinhVienThiDatTN($danhSachSinhVien, $lh_id, $dt_id, $lopHoc)
    {
        $filter = new Filters;
        $dsSv_id = DB::table('qlsv_dotxettotnghiep_sinhvien as dxtn_sv')
            ->join('qlsv_sinhvien as sv', function ($join) {
                $join->on('sv.sv_id', '=', 'dxtn_sv.sv_id');
            })
            ->join('qlsv_lophoc as lh', function ($join) {
                $join->on('lh.lh_id', '=', 'dxtn_sv.lh_id');
            })
            ->join('qlsv_khoadaotao as kdt', function ($join) {
                $join->on('kdt.kdt_id', '=', 'lh.kdt_id');
            })
            ->where('dxtn_sv.lh_id', '=', $lh_id)
            ->where('dxtn_sv.dt_id', '=', $dt_id)
            ->whereRaw('dxtn_sv.svxtn_dattn = 1')
            ->select(DB::raw('DISTINCT sv.sv_id'))
            ->get();


        $dsSv_idArr = $dsSv_id->pluck('sv_id')->toArray(); // Chuyển danh sách $dsSv_id thành mảng sv_id

        $danhSachSinhVien = $danhSachSinhVien->filter(function ($sinhVien) use ($dsSv_idArr) {
            return in_array($sinhVien->sv_id, $dsSv_idArr);
        });

        // lọc danh sinh viên đạt và không đạt
        return $danhSachSinhVien = $filter->LocSinhVienKhongDatTN($danhSachSinhVien, $lopHoc);
    }

    // public function SinhVienThamDuThiTN($danhSachSinhVien)
    // {
    //     // lọc danh sinh viên đạt và không đạt điều kiện thi tốt nghiệp
    //     foreach ($danhSachSinhVien->values() as $sIndex => $sinhVien) {
    //         $sinhVienDuDkThiTN = true;
    //         foreach ($sinhVien->years as $yIndex => $year) {
    //             foreach ($year->semesters as $semester) {
    //                 // 4. Điểm các môn trong học kỳ
    //                 foreach ($semester->monHoc as $xIndex => $monHoc) {
    //                     // if($xIndex == 3) {
    //                     //     dd($monHoc->ketQua);
    //                     // }
    //                     if ($monHoc->ketQua) {
    //                         $final = $monHoc->ketQua->display_score;
    //                     }

    //                     if ($final < 5) {
    //                         $sinhVienDuDkThiTN = false;
    //                     }
    //                 }
    //             }
    //         }

    //         if (!$sinhVienDuDkThiTN || $sinhVien->svd_khongDatloai == 1) {
    //             $danhSachSinhVien->forget($sIndex);
    //         } else {
    //             $sinhVien->datTotNghep = true;
    //         }
    //     }

    //     return $danhSachSinhVien;
    // }

    // public function SinhVienDuDieuThiTN($danhSachSinhVien)
    // {
    //     $sinhVienDuDieuKienThiTN = [];

    //     // lọc danh sinh viên đạt và không đạt điều kiện thi tốt nghiệp
    //     foreach ($danhSachSinhVien->values() as $sIndex => $sinhVien) {
    //         $sinhVienDuDkThiTN = true;
    //         foreach ($sinhVien->years as $yIndex => $year) {
    //             foreach ($year->semesters as $semester) {
    //                 // 4. Điểm các môn trong học kỳ
    //                 foreach ($semester->monHoc as $xIndex => $monHoc) {
    //                     // if($xIndex == 3) {
    //                     //     dd($monHoc->ketQua);
    //                     // }
    //                     if ($monHoc->ketQua) {
    //                         $final = $monHoc->ketQua->display_score;
    //                     }

    //                     if ($final < 5) {
    //                         $sinhVienDuDkThiTN = false;
    //                     }
    //                 }
    //             }
    //         }

    //         if ($sinhVienDuDkThiTN == false) {
    //             $sinhVien->datTotNghep = false;
    //         } else if($sinhVien->svd_khongdatloai == 1 || $sinhVien->svd_khongdatloai == 2) {
    //             $sinhVien->datTotNghep = false;
    //         } else {
    //             $sinhVienDuDieuKienThiTN[] = $sinhVien;
    //         }
    //     }

    //     return $sinhVienDuDieuKienThiTN;
    // }

}
