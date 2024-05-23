<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SinhVien;
use App\Models\LopHoc;
use App\Models\KhoaDaoTao;
use App\Enums\BangDiemType;
use App\Excels\KetQuaHocTap;
use DB;
use App\Excels\KetQuaHocTapSinhVien;
use App\Models\Log;

class TraCuuDiemController extends Controller
{
    public function index()
    {
        return view('qlsv.tracuudiem.tracuudiem_list');
    }

    public function getDanhSachDiemMonHocTichLuy($svId, $kdtId, $lhId, $semesters)
    {
        $danhSachDiemMonHocTichLuy = DB::table('qlsv_sinhvien as sv')
            ->join('qlsv_sinhvien_diem as svd', 'svd.sv_id', '=', 'sv.sv_id')
            ->join('qlsv_bangdiem as bd', 'bd.bd_id', '=', 'svd.bd_id')
            ->join('qlsv_monhoc as mh', 'mh.mh_id', '=', 'bd.mh_id')
            ->join('qlsv_lophoc_monhoc as kdtm', function ($join) {
                $join->on('kdtm.mh_id', '=', 'mh.mh_id')
                    ->on('bd.kdt_hocky', '=', 'kdtm.lh_mh_hocky');
            })
            ->where('mh_tichluy', 1)
            ->where('bd.lh_id', $lhId)
            ->whereIn('bd.kdt_hocky', $semesters)
            ->where(function ($builder) {
                $builder->where('svd.svd_first', '>=', 5)
                    ->orWhere('svd.svd_second', '>=', 5)
                    ->orWhere('svd.svd_third', '>=', 5);
            })
            ->where('sv.sv_id', $svId)
            ->select(
                'bd.kdt_hocky',
                'mh.mh_id',
                'mh.mh_ma',
                'mh.mh_sodonvihoctrinh',
                'svd.svd_first',
                'svd.svd_second',
                'svd.svd_second_hocky',
                'svd.svd_third',
                'svd.svd_third_hocky',
                'svd.svd_final'
            )
            ->distinct()
            ->get();
        return $danhSachDiemMonHocTichLuy;
    }

    private function truyVanKetQua($svId, $hocKy)
    {
        $reqSemester = $hocKy ?: 1;
        $reqYear = 1;
        $semesters = [$reqSemester];
        if ($reqSemester == 123456 || $reqSemester == -1) {
            $reqYear = -1;
            $semesters = [1, 2, 3, 4, 5, 6];
        } else if ($reqSemester <= 2) {
            $reqYear = 1;
        } else if ($reqSemester <= 4) {
            $reqYear = 2;
        } else if ($reqSemester <= 6) {
            $reqYear = 3;
        } else if ($reqSemester == 12) {
            $reqYear = 1;
            $semesters = [1, 2];
        } else if ($reqSemester == 34) {
            $reqYear = 2;
            $semesters = [3, 4];
        } else if ($reqSemester == 56) {
            $reqYear = 3;
            $semesters = [5, 6];
        } else {
            abort(404);
        }
        $sinhVien = SinhVien::find($svId);
        if (!$sinhVien) {
            abort(404);
        }
        $danhSachNamHoc = $sinhVien->lopHoc()->with('nienKhoa')->select('nk_id')->get();
        $danhSachNamHoc = $danhSachNamHoc->map(function ($item) {
            return $item->nienKhoa;
        });

        $danhSachMonHoc = DB::table('qlsv_lophoc as lh')
            ->join('qlsv_sinhvien_lophoc as svl', 'svl.lh_id', '=', 'lh.lh_id')
            ->join('qlsv_lophoc_monhoc as kdt', function ($join) use ($semesters) {
                $join->on('kdt.lh_id', '=', 'lh.lh_id');
                $join->whereIn('kdt.lh_mh_hocky', $semesters);
            })
            ->join('qlsv_monhoc as mh', function ($join) {
                $join->on('mh.mh_id', '=', 'kdt.mh_id');
                $join->where('mh.mh_loai', 1);
            })
            ->leftJoin('qlsv_bangdiem as bd', function ($join) {
                $join->on('bd.lh_id', '=', 'lh.lh_id')
                    ->on('bd.mh_id', '=', 'mh.mh_id')
                    ->on('bd.kdt_hocky', '=', 'kdt.lh_mh_hocky')
                    ->where('bd.bd_type', BangDiemType::BANGDIEM_MONHOC);
            })
            ->leftJoin('qlsv_sinhvien_diem as svd', function ($join) use ($sinhVien) {
                $join->on('svd.bd_id', '=', 'bd.bd_id')
                    ->where('svd.sv_id', $sinhVien->sv_id);
            })
            ->where('svl.sv_id', $sinhVien->sv_id)
            ->select(
                'lh.lh_id',
                'lh.lh_ma',
                'lh.kdt_id',
                'bd.bd_id',
                'kdt.lh_mh_hocky as kdt_hocky',
                'mh.mh_id',
                'mh.mh_ma',
                'mh.mh_ten',
                'mh.mh_giangvien',
                'mh.mh_sodonvihoctrinh',
                'mh.mh_sotiet',
                'mh.mh_tichluy',
                'bd.bd_type',
                'svd.svd_dulop',
                'svd.svd_first',
                'svd.svd_second',
                'svd.svd_final',
                'svd.svd_ghichu',
                'svd.svd_second_hocky',
                'svd.svd_third',
                'svd.svd_third_hocky'
            )
            ->orderBy('lh.lh_id', 'asc')
            ->orderBy('kdt.lh_mh_hocky', 'asc')
            ->orderBy('kdt.lh_mh_index', 'asc')
            ->get();

        $danhSachLopHoc = [];
        $currentlhId = 0;
        $currentHocKy = 0;
        $currentIndex = -1;
        foreach ($danhSachMonHoc as $monHoc) {
            if ($currentlhId != $monHoc->lh_id || $currentHocKy != $monHoc->kdt_hocky) {
                $lopHoc = LopHoc::find($monHoc->lh_id);
                $currentlhId = $monHoc->lh_id;
                $currentHocKy = $monHoc->kdt_hocky;

                $currentIndex++;
                $countMonHoc = KhoaDaoTao::find($monHoc->kdt_id)->monHoc()->where('kdt_mh_hocky', $currentHocKy)->count();
                $sTemp = $monHoc->kdt_hocky + 2 - (ceil(($monHoc->kdt_hocky) / 2) * 2);
                $yTemp = ceil($monHoc->kdt_hocky) / 2;
                $danhSachLopHoc[$currentIndex] = [
                    'lh_id' => $monHoc->lh_id,
                    'lh_ma' => $monHoc->lh_ma,
                    'kdt_id' => $monHoc->kdt_id,
                    'kdt_hocky' => $monHoc->kdt_hocky,
                    'semester' => $sTemp,
                    'year' => $yTemp,
                    'lh_nienche' => $lopHoc ? $lopHoc->lh_nienche : 1,
                    'monHoc_count' => $countMonHoc,
                    'monHoc' => [],
                ];
            }
            $danhSachLopHoc[$currentIndex]['monHoc'][] = $monHoc;
        }

        foreach ($danhSachLopHoc as &$lopHoc) {
            $monHoc = count($lopHoc['monHoc']);
            $collection = collect($lopHoc['monHoc']);
            $countCoDuLieu = $collection->filter(function ($item) {
                return $item->svd_first || $item->svd_second || $item->svd_third;
            })
                ->count();
            $lopHoc['diemRenLuyen'] = DB::table('qlsv_bangdiem as bd')
                ->join('qlsv_sinhvien_diem as svd', 'svd.bd_id', '=', 'bd.bd_id')
                ->where('bd.bd_type', BangDiemType::BANGDIEM_HOCKY)
                ->where('bd.kdt_hocky', $lopHoc['kdt_hocky'])
                ->where('bd.lh_id', $lopHoc['lh_id'])
                ->where('svd.sv_id', $sinhVien->sv_id)
                ->whereNotNull('svd.svd_final')
                ->select(
                    'bd.bd_id',
                    'bd.kdt_hocky',
                    'bd.bd_type',
                    'svd.svd_dulop',
                    'svd.svd_first',
                    'svd.svd_second',
                    'svd.svd_third',
                    'svd.svd_final',
                    'svd.svd_ghichu'
                )
                ->first();
            if ($lopHoc['diemRenLuyen']) {
                // Tinh trung binh cong
                $sumDiem = 0;
                $sumTinChi = 0;
                $sumDiemTl = 0;
                $sumTinChiTl = 0;
                $collection->each(function ($item) use (&$sumDiem, &$sumTinChi, &$sumDiemTl, &$sumTinChiTl) {
                    if ($item->mh_tichluy) {
                        $sumDiem += $item->svd_first * $item->mh_sodonvihoctrinh;
                        $sumTinChi += $item->mh_sodonvihoctrinh;
                        if ($item->svd_third >= 5) {
                            $sumDiemTl += $item->svd_third * $item->mh_sodonvihoctrinh;
                            $sumTinChiTl += $item->mh_sodonvihoctrinh;
                        } else if ($item->svd_second >= 5) {
                            $sumDiemTl += $item->svd_second * $item->mh_sodonvihoctrinh;
                            $sumTinChiTl += $item->mh_sodonvihoctrinh;
                        } else if ($item->svd_first >= 5) {
                            $sumDiemTl += $item->svd_first * $item->mh_sodonvihoctrinh;
                            $sumTinChiTl += $item->mh_sodonvihoctrinh;
                        }
                    }
                });
                $lopHoc['tinhchi_total'] = $sumTinChi;
                if ($sumTinChi > 0) {
                    $lopHoc['avg'] = number_format($sumDiem / $sumTinChi, 1);
                    $lopHoc['hocLuc'] = getHocLuc($lopHoc['avg']);
                }

                $curSemester = $lopHoc['kdt_hocky'];
                $semesterSumTinChi = [];
                for ($tI = 1; $tI <= $curSemester; $tI++) {
                    $semesterSumTinChi[] = $tI;
                }

                $danhSachDiemMonHocTichLuy = $this->getDanhSachDiemMonHocTichLuy($svId, $lopHoc['kdt_id'], $lopHoc['lh_id'], $semesterSumTinChi);
                $danhSachDiemMonHocTichLuy = $danhSachDiemMonHocTichLuy->filter(function ($item) use ($curSemester) {
                    // Mặc định lấy điểm lần 1
                    $item->score = $item->svd_first;
                    if ($item->svd_third >= 5 && $item->svd_third_hocky <= $curSemester) {
                        // Nếu là các học kỳ trước => hiển thị điểm cải thiện học kỳ trước
                        $item->score = $item->svd_third;
                    } else if ($item->svd_second >= 5 && $item->svd_second_hocky <= $curSemester) {
                        // Nếu là các học kỳ trước => hiển thị điểm cải thiện học kỳ trước
                        $item->score = $item->svd_second;
                    }
                    return $item->score >= 5;
                });
                $tichLuy = $danhSachDiemMonHocTichLuy->map(function ($item) {
                    return $item->score * $item->mh_sodonvihoctrinh;
                })->sum();

                $tinChiTichLuy = $danhSachDiemMonHocTichLuy->map(function ($item) {
                    return $item->mh_sodonvihoctrinh;
                })->sum();
                if ($tinChiTichLuy > 0) {
                    $lopHoc['tichLuy'] = number_format($tichLuy / $tinChiTichLuy, 1);
                    $lopHoc['tinChiTichLuy'] = $tinChiTichLuy;
                }
            }
        }

        return [
            'danhSachNamHoc' => $danhSachNamHoc,
            'danhSachLopHoc' => $danhSachLopHoc
        ];
    }

    public function xuatKetQuaHocTap(Request $request)
    {
        $lhId = $request->lhId;
        $export = new KetQuaHocTap;
        if (isset($request->sv_id)) {
            //$sinhVien = SinhVien::find($request->sv_id);
            if ($request->hoc_ky == "123456") {
                $bdTN = false;
                $datakqht = $export->getKetQuaHocTapSVToanKhoa($request->hoc_ky, $lhId, $request->sv_id);
            } else {
                $datakqht = $export->getKetQuaHocTap($request->hoc_ky, $lhId, $request->sv_id);
            }
        } else {
            $sinhVien = auth()->user()->sinhVien;
            if ($request->hoc_ky == "123456") {
                $datakqht = $export->getKetQuaHocTapSVToanKhoa($request->hoc_ky, $lhId, $sinhVien->sv_id);
            } else {
                $datakqht = $export->getKetQuaHocTap($request->hoc_ky, $lhId, $sinhVien->sv_id);
            }
        }
        $sinhVien = $datakqht["danhSachSinhVien"][0];

        $data = $this->truyVanKetQua($sinhVien->sv_id, $request->hoc_ky);
        $danhSachNamHoc = $data['danhSachNamHoc'];
        $danhSachLopHoc = $data['danhSachLopHoc'];
        $export = new KetQuaHocTapSinhVien;
        $export->download($danhSachNamHoc, $danhSachLopHoc, $sinhVien, $request->hoc_ky);
    }

    public function traCuu(Request $request)
    {
        $sinhVien = auth()->user()->sinhVien;
        $data = $this->truyVanKetQua($sinhVien->sv_id, $request->hoc_ky);
        $danhSachNamHoc = $data['danhSachNamHoc'];
        $danhSachLopHoc = $data['danhSachLopHoc'];
        $diemTrungBinh = -1;
        $soHocKy = 0;
        foreach ($danhSachLopHoc as &$lopHoc) {
            if (isset($lopHoc['avg'])) {
                $diemTrungBinh += $lopHoc['avg'];
                $soHocKy++;
            }
        }
        if ($soHocKy) {
            $diemTrungBinh = ($diemTrungBinh + 1) / $soHocKy;
        }

        $provider = new \App\Excels\KetQuaHocTap;
        $lopHocTemp = $sinhVien->lopHoc->first();
        $lhId = $lopHocTemp->lh_id;
        $warnings = collect();
        if ($lopHocTemp) {
            $ketQuaHocKyI = $provider->getKetQuaHocTap(123456, $lopHocTemp->lh_id, $sinhVien->sv_id);
            // dd($ketQuaHocKyI['danhSachSinhVien']->toArray());
            $ketQuaHocKyI['danhSachSinhVien']->each(function ($sv) use ($warnings) {
                $sv->years->each(function ($year, $yIndex) use ($warnings) {
                    $dtbcCondition = isset ($year->avg) && $year->avg < 4;
                    $dtctlCondition = isset ($year->avg) && isset ($year->tichLuy) && $year->tichLuy < 4;
                    if ($dtbcCondition || $dtctlCondition) {
                        // Buộc thôi học
                        $warnings->push([
                            'type' => 'BTH',
                            'year' => ($yIndex + 1),
                        ]);
                    }

                    if (isset ($year->avg) && $year->avg <= 5 && $year->avg >= 4) {
                        // ĐIều chỉnh tiến độ học
                        $warnings->push([
                            'type' => 'DCTCH',
                            'year' => ($yIndex + 1),
                        ]);
                    }
                });
            });
        }

        return view('qlsv.tracuudiem.tracuudiem_index', compact(['danhSachNamHoc', 'danhSachLopHoc', 'diemTrungBinh', 'warnings', 'lhId']));
    }

    public function getDanhSachLopHoc()
    {
        $svId = 0;
        $sinhVien = auth()->user()->sinhVien;
        if ($sinhVien) {
            $svId = $sinhVien->sv_id;
        }
        $danhSachHocKy = DB::table('qlsv_lophoc as lh')
            ->join('qlsv_nienkhoa as nk', 'nk.nk_id', '=', 'lh.nk_id')
            ->join('qlsv_khoadaotao as kd', 'kd.kdt_id', '=', 'lh.kdt_id')
            ->join('qlsv_sinhvien_lophoc as svl', 'svl.lh_id', '=', 'lh.lh_id')
            ->join('qlsv_lophoc_monhoc as kdt', 'kdt.lh_id', '=', 'lh.lh_id')
            ->join('qlsv_bangdiem as     bd', 'bd.kdt_hocky', '=', 'kdt.lh_mh_hocky')
            ->where('svl.sv_id', '=', $svId)
            ->groupBy('lh.lh_id', 'lh.lh_ma', 'lh.lh_ten', 'kd.kdt_ten', 'kdt.lh_mh_hocky as kdt_mh_hocky', 'nk.nk_ten')
            ->select('lh.lh_id', 'lh.lh_ma', 'lh.lh_ten', 'kd.kdt_ten', 'kdt.lh_mh_hocky as kdt_mh_hocky', 'nk.nk_ten')
            ->orderBy('lh.lh_id', 'desc')
            ->orderBy('kdt.lh_mh_hocky', 'desc')
            ->get();

        foreach ($danhSachHocKy as $hocKy) {
            $hocKy->detail_url = route('tra-cuu-diem.detail', ['lop_hoc' => $hocKy->lh_id, 'hoc_ky' => $hocKy->kdt_mh_hocky]);
        }

        return response()->json($danhSachHocKy);
    }

    public function detail($lhId, $hocKy)
    {
        $svId = 0;
        $sinhVien = auth()->user()->sinhVien;
        if ($sinhVien) {
            $svId = $sinhVien->sv_id;
        }
        $lopHoc = LopHoc::find($lhId);
        if (!$lopHoc) {
            abort(404);
        }

        if (!$lopHoc->bangDiem()->where('kdt_hocky', $hocKy)->exists()) {
            abort(404);
        }

        $bangDiem = DB::table('qlsv_lophoc as lh')
            ->join('qlsv_bangdiem as bd', 'bd.lh_id', '=', 'lh.lh_id')
            ->join('qlsv_monhoc as mh', 'mh.mh_id', '=', 'bd.mh_id')
            ->join('qlsv_lophoc_monhoc as kdt', function ($join) {
                $join->on('kdt.lh_id', '=', 'lh.lh_id')
                    ->whereRaw('mh.mh_id = kdt.mh_id')
                    ->whereRaw('kdt.lh_mh_hocky = bd.kdt_hocky');
            })
            ->join('qlsv_sinhvien_diem as svd', 'svd.bd_id', '=', 'bd.bd_id')
            ->where('svd.sv_id', $svId)
            ->where('lh.lh_id', $lhId)
            ->where('bd.kdt_hocky', $hocKy)
            ->whereRaw('(svd.svd_dulop IS NOT NULL OR svd.svd_first IS NOT NULL OR svd.svd_second IS NOT NULL OR svd.svd_third IS NOT NULL)')
            ->orderBy('lh.lh_id', 'desc')
            ->orderBy('kdt.lh_mh_index', 'asc')
            ->select('lh.lh_id', 'bd.kdt_hocky', 'mh.mh_ma', 'bd.mh_id', 'mh.mh_ten', 'bd.created_at as bd_ngaynhap', 'svd.*')
            ->get();


        return view('qlsv.tracuudiem.tracuudiem_view', compact(['bangDiem', 'lopHoc', 'hocKy']));
    }
}
