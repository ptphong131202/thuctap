<?php

namespace App\Excels;

use Illuminate\Http\Request;
use App\Models\SinhVien;
use App\Models\LopHoc;
use App\Models\KhoaDaoTao;
use App\Enums\BangDiemType;
use DB;
use \Carbon\Carbon;
use \PhpOffice\PhpWord\TemplateProcessor;
use \PhpOffice\PhpWord\Element\Table;
use \PhpOffice\PhpWord\Element\TextRun;
use \PhpOffice\PhpWord\SimpleType\TblWidth;
use \PhpOffice\PhpWord\IOFactory;

class KetQuaHocTapSinhVien
{
    private $templateQuyChe2020 = 'app/word-template/KQHT_SINHVIENTRUOCNIENCHE_TEMPLTE.docx';
    private $templateQuyChe2022 = 'app/word-template/KQHT_SINHVIENSAUNIENCHE_TEMPLTE.docx';

    public function download($danhSachNamHoc, $danhSachLopHoc, $sinhVien, $hoc_ky)
    {
        $lopHocDau = LopHoc::with('khoaDaoTao', 'nienKhoa', 'khoaDaoTao.heDaoTao')->find($danhSachLopHoc[0]['lh_id']);

        $soHocKy = 0;
        $tongDiemTrungBinhTichLuy = 0;
        $tongDiemRenLuyen = 0;
        $sumDiem = 0;
        $sumTinChi = 0;
        $quyChe2022 = $lopHocDau->lh_nienche == 1;
        if ($quyChe2022) {
            $templateProcessor = new TemplateProcessor(storage_path($this->templateQuyChe2022));
        } else {
            $templateProcessor = new TemplateProcessor(storage_path($this->templateQuyChe2020));
        }
        $tableStyle = array('borderSize' => 1, 'borderColor' => 'black', 'unit' => \PhpOffice\PhpWord\Style\Table::WIDTH_PERCENT, 'width' => 100 * 50);
        $fontBold = array('bold' => true);
        $alignCenter = array('align' => 'center');
        $lineHeight = array('lineHeight' => '1.5');
        $vAlignCenter = array('valign' => 'center');

        //tạo table  mẫu
        $tableHK = new Table($tableStyle);
        $cellRowSpan = array('vMerge' => 'restart', 'valign' => 'center');
        $cellRowContinue = array('vMerge' => 'continue', 'valign' => 'center');
        $cellColSpan = array('gridSpan' => 2, 'valign' => 'center');
        $cellColSpan3 = array('gridSpan' => 3, 'valign' => 'center');

        $tableHK->addRow(400);
        $tableHK->addCell(800, $cellRowSpan)->addText("TT", $fontBold, $alignCenter);
        $tableHK->addCell(7200, $cellRowSpan)->addText("Tên mô đun/môn học", $fontBold, $alignCenter);
        $tableHK->addCell(2000, $cellRowSpan)->addText("Tín chỉ", $fontBold, $alignCenter);
        $tableHK->addCell(2000, $cellRowSpan)->addText("Số tiết/giờ", $fontBold, $alignCenter);
        $tableHK->addCell(4000, $quyChe2022 ? $cellColSpan3 : $cellColSpan)->addText("Điểm hệ 10", $fontBold, $alignCenter);

        $tableHK->addRow(400);
        $tableHK->addCell(null, $cellRowContinue);
        $tableHK->addCell(null, $cellRowContinue);
        $tableHK->addCell(null, $cellRowContinue);
        $tableHK->addCell(null, $cellRowContinue);
        $tableHK->addCell(2000, $vAlignCenter)->addText("Lần 1", $fontBold, $alignCenter);
        $tableHK->addCell(2000, $vAlignCenter)->addText("Lần 2", $fontBold, $alignCenter);
        if ($quyChe2022) {
            $tableHK->addCell(2000, $vAlignCenter)->addText("Lần 3", $fontBold, $alignCenter);
        }
        $ngaygiohientai = Carbon::now()->format('H:i d/m/Y');
        //</w:t><w:br/><w:t>
        $templateProcessor->setValue('footer', 'Tài khoản in: ' . auth()->user()->username . '; thời gian ' . $ngaygiohientai);
        $templateProcessor->setValue('ngayhientai', Carbon::today()->format('d'));
        $templateProcessor->setValue('thanghientai', Carbon::today()->format('m'));
        $templateProcessor->setValue('namhientai', Carbon::today()->format('Y'));
        $templateProcessor->setValue('sv_ho', $sinhVien->sv_ho);
        $templateProcessor->setValue('sv_ten', $sinhVien->sv_ten);
        $templateProcessor->setValue('sv_ma', $sinhVien->sv_ma);
        $templateProcessor->setValue('kdt_ten', $lopHocDau->khoaDaoTao->nganhNghe->nn_ten);
        $templateProcessor->setValue('kdt_he', $lopHocDau->khoaDaoTao->kdt_he);
        $templateProcessor->setValue('nk_ten', $lopHocDau->nienKhoa->nk_ten);
        $templateProcessor->setValue('hdt_ten', $lopHocDau->khoaDaoTao->heDaoTao->hdt_ten);
        if (str_contains(strtolower($lopHocDau->khoaDaoTao->heDaoTao->hdt_ten), 'cao đẳng')) {
            $templateProcessor->setValue('loai', 'MSSV');
        } else {
            $templateProcessor->setValue('loai', 'MSHS');
        }

        //tạo các dòng hk
        $templateProcessor->cloneRow('hk_number', sizeof($danhSachLopHoc));
        foreach ($danhSachLopHoc as $index => $lopHocEach) {
            $schoolYear = explode(" - ", $danhSachNamHoc[0]->nk_ten);
            $schoolYearStart = $schoolYear[0];
            $schoolYearEnd = $schoolYear[1];

            $numberOfYears = $schoolYearEnd - $schoolYearStart;
            $semesters = [];
            if ($numberOfYears == 2) {
                $semesters = [
                    ['semester' => 1, 'year' => $schoolYearStart . " - " . $schoolYearEnd - 1],
                    ['semester' => 2, 'year' => $schoolYearStart . " - " . $schoolYearEnd - 1],
                    ['semester' => 3, 'year' => $schoolYearStart + 1 . " - " . $schoolYearEnd],
                    ['semester' => 4, 'year' => $schoolYearStart + 1 . " - " . $schoolYearEnd],
                ];
            } else {
                $semesters = [
                    ['semester' => 1, 'year' => $schoolYearStart . " - " . $schoolYearEnd - 2],
                    ['semester' => 2, 'year' => $schoolYearStart . " - " . $schoolYearEnd - 2],
                    ['semester' => 3, 'year' => $schoolYearStart + 1 . " - " . $schoolYearEnd - 1],
                    ['semester' => 4, 'year' => $schoolYearStart + 1 . " - " . $schoolYearEnd - 1],
                    ['semester' => 5, 'year' => $schoolYearStart + 2 . " - " . $schoolYearEnd],
                    ['semester' => 6, 'year' => $schoolYearStart + 2 . " - " . $schoolYearEnd],
                ];
            }

            //$templateProcessor->setValue('hk_number#'.($index+1), 'Học kỳ '.$lopHocEach['semester'].' năm '.numberToRoman(round($lopHocEach['year'])) . ' năm học ' . $danhSachNamHoc[0]->nk_ten);
            // Học kỳ 1 – Năm học: ….
            $templateProcessor->setValue('hk_number#' . ($index + 1), 'Học kỳ ' . $lopHocEach['kdt_hocky'] . ' - Năm học: ' . $semesters[$lopHocEach['kdt_hocky'] - 1]['year']);
            $templateProcessor->setValue('avg#' . ($index + 1), isset($lopHocEach['avg']) ? number_format($lopHocEach['avg'], 1) : '');
            $templateProcessor->setValue('tinhchi_total#' . ($index + 1), isset($lopHocEach['tinhchi_total']) ? $lopHocEach['tinhchi_total'] : '');

            $templateProcessor->setValue('avg_tichluy#' . ($index + 1), isset($lopHocEach['tichLuy']) ? number_format($lopHocEach['tichLuy'], 1) : '');
            $templateProcessor->setValue('tinhchi_total_tichluy#' . ($index + 1), isset($lopHocEach['tinChiTichLuy']) ? $lopHocEach['tinChiTichLuy'] : '');
            $tableTmp = clone $tableHK;
            foreach ($lopHocEach['monHoc'] as $indexMH => $monHoc) {
                $tableTmp->addRow(400);

                $tableTmp->addCell(800, $vAlignCenter)->addText($indexMH + 1, $fontBold, $alignCenter);
                $mh_ten = $monHoc->mh_tichluy ? $monHoc->mh_ten : $monHoc->mh_ten . ' (*)';
                $tableTmp->addCell(7200, $vAlignCenter)->addText($mh_ten);
                $tableTmp->addCell(2000, $vAlignCenter)->addText($monHoc->mh_sodonvihoctrinh, null, $alignCenter);
                $tableTmp->addCell(2000, $vAlignCenter)->addText($monHoc->mh_sotiet, null, $alignCenter);
                if (isset($monHoc->svd_first) && !empty($monHoc->svd_first)) {
                    $tableTmp->addCell(2000, $vAlignCenter)->addText(number_format($monHoc->svd_first, 1), null, $alignCenter);
                } else {
                    $tableTmp->addCell(2000, $vAlignCenter)->addText('', null, $alignCenter);
                }

                if (isset($monHoc->svd_second) && !empty($monHoc->svd_second)) {
                    $tableTmp->addCell(2000, $vAlignCenter)->addText(number_format($monHoc->svd_second, 1), null, $alignCenter);
                } else {
                    $tableTmp->addCell(2000, $vAlignCenter)->addText('', null, $alignCenter);
                }

                if ($quyChe2022) {
                    if (isset($monHoc->svd_third) && !empty($monHoc->svd_third)) {
                        $tableTmp->addCell(2000, $vAlignCenter)->addText(number_format($monHoc->svd_third, 1), null, $alignCenter);
                    } else {
                        $tableTmp->addCell(2000, $vAlignCenter)->addText('', null, $alignCenter);
                    }
                }

                if ($monHoc->svd_first) {
                    $monHoc->svd_first = number_format($monHoc->svd_first, 1);
                }
                if ($monHoc->svd_second >= 5) {
                    // Các học kỳ trước => hiển thị điểm cải thiện học kỳ trước
                    $monHoc->display_score = $monHoc->svd_second;
                } else {
                    // Mặc định hiển thị điểm lần 1
                    $monHoc->display_score = $monHoc->svd_first;
                }

                if ($monHoc->mh_tichluy) {
                    $sumTinChi += $monHoc->mh_sodonvihoctrinh;
                    if ($monHoc->display_score) {
                        $sumDiem += $monHoc->svd_first * $monHoc->mh_sodonvihoctrinh;
                    }
                }
            }
            $templateProcessor->setComplexBlock('table#' . ($index + 1), $tableTmp);

            $soHocKy++;

            if (isset($lopHocEach['avg'])) {
                $tongDiemTrungBinhTichLuy += $lopHocEach['avg'];
            }

            if (isset($lopHocEach['diemRenLuyen']->svd_final)) {
                $tongDiemRenLuyen += $lopHocEach['diemRenLuyen']->svd_final;
            }
        }
        $TBToanKhoa = -1;
        if ($sumTinChi) {
            $TBToanKhoa = number_format($sumDiem / $sumTinChi, 1);
        }

        //Tính điểm thi tốt nghiệp
        $dsMonThiTotNghiep = DB::table('qlsv_dotthi_bangdiem as bd')
            ->join('qlsv_dotthi_diem as diem', 'diem.dt_bd_id', '=', 'bd.dt_bd_id')
            ->join('qlsv_monhoc as mh', 'mh.mh_id', '=', 'bd.mh_id')
            ->where('bd.lh_id', $danhSachLopHoc[0]['lh_id'])
            ->where('diem.sv_id', $sinhVien->sv_id)
            ->select('mh_loai', 'diem.*')
            ->get();
        $soMonLyThuyet = 0;
        $soMonThucHanh = 0;
        $soMonBaoVeKhoaLuan = 0;
        $soMonChinhTri = 0;

        $tongDiemMonLyThuyet = 0;
        $tongDiemMonThucHanh = 0;
        $tongDiemMonBaoVeKhoaLuan = 0;
        $tongDiemMonChinhTri = 0;
        $coDiemRong = false;


        foreach ($dsMonThiTotNghiep as $monHocDT) {
            if ($monHocDT->svd_first) {
                $monHocDT->svd_first = number_format($monHocDT->svd_first, 1);
            }
            if ($monHocDT->svd_second >= 5) {
                // Các học kỳ trước => hiển thị điểm cải thiện học kỳ trước
                $monHocDT->display_score = $monHocDT->svd_second;
            } else {
                // Mặc định hiển thị điểm lần 1
                $monHocDT->display_score = $monHocDT->svd_first;
            }

            if ($monHocDT->mh_loai == 2) {
                //Chính trị
                $soMonChinhTri++;
                $tongDiemMonChinhTri += $monHocDT->display_score;
            } else if ($monHocDT->mh_loai == 3) {
                //Thực hành
                $soMonThucHanh++;
                $tongDiemMonThucHanh += $monHocDT->display_score;
            } else if ($monHocDT->mh_loai == 4) {
                //Lý thuyết
                $soMonLyThuyet++;
                $tongDiemMonLyThuyet += $monHocDT->display_score;
            } else if ($monHocDT->mh_loai == 5) {
                //Thi tốt nghiệp khóa luận
                $soMonBaoVeKhoaLuan++;
                $tongDiemMonBaoVeKhoaLuan += $monHocDT->display_score;
            }

            $coDiemRong = $monHocDT->display_score == null;
        }

        $TBMonLyThuyet = -1;
        $TBMonThucHanh = -1;
        $TBMonChinhTri = -1;
        $TBMonBaoVeKhoaLuan = -1;
        if (!$coDiemRong && $TBToanKhoa != -1) {
            // $TBMonLyThuyet = $soMonLyThuyet != 0 ? number_format($tongDiemMonLyThuyet  / $soMonLyThuyet, 1) : -1;
            // $TBMonThucHanh = $soMonThucHanh != 0 ? number_format($tongDiemMonThucHanh  / $soMonThucHanh, 1) : -1;
            // $TBMonChinhTri = $soMonChinhTri != 0 ? number_format($tongDiemMonChinhTri  / $soMonChinhTri, 1) : -1;
            //$TBMonBaoVeKhoaLuan = $soMonBaoVeKhoaLuan != 0 ? number_format($tongDiemMonBaoVeKhoaLuan / $soMonBaoVeKhoaLuan, 1) : -1;

            $TBMonLyThuyet = $soMonLyThuyet != 0 ? number_format($tongDiemMonLyThuyet, 1) : -1;
            $TBMonThucHanh = $soMonThucHanh != 0 ? number_format($tongDiemMonThucHanh, 1) : -1;
            $TBMonChinhTri = $soMonChinhTri != 0 ? number_format($tongDiemMonChinhTri, 1) : -1;
            $TBMonBaoVeKhoaLuan = $soMonBaoVeKhoaLuan != 0 ? number_format($tongDiemMonBaoVeKhoaLuan, 1) : -1;
        }

        $TBToanKhoa = $sinhVien->toanKhoa->tichLuy;

        if ($TBMonBaoVeKhoaLuan != -1) {
            $templateProcessor->setValue('diemchuyende', number_format((($TBToanKhoa * 3) + ($TBMonBaoVeKhoaLuan * 2)) / 5, 1));
        } else {
            $templateProcessor->setValue('diemchuyende', '');
        }
        if ($TBMonThucHanh != -1 && $TBMonLyThuyet != -1) {
            // * nếu có sai thì quay lại check $sinhVien->toanKhoa
            $templateProcessor->setValue('diemxeploai', number_format((($TBToanKhoa * 3) + ($TBMonThucHanh * 2) + $TBMonLyThuyet) / 6, 1));
            $trungBinhTN = number_format((($TBToanKhoa * 3) + ($TBMonThucHanh * 2) + $TBMonLyThuyet) / 6, 1);
            $ketquahoctap = new KetQuaHocTap();
            if ($hoc_ky == "123456") {
                $hocLuc = $ketquahoctap->getHocLucByDB($sinhVien->toanKhoa->final_xltn, $quyChe2022);
                if ($sinhVien->toanKhoa->final_xltn > $sinhVien->toanKhoa->temp_xltn) {
                    $stringTL = '';
                    $stringHL = '';
                    $countTL = 0;
                    $countHL = 0;
                    $stringTLHL = '';

                    foreach ($sinhVien->notes as $item) {
                        if ($item['type'] === 'TL') {
                            // if (!empty($stringTL)) {
                            //     $stringTL .= ', '; // Thêm dấu phẩy và khoảng trắng giữa các chuỗi
                            // }
                            // $stringTL .= $item['type'] . ' (' . $item['key'] . ')';
                            $countTL++;
                        }
                        if ($item['type'] === 'HL') {
                            $countHL++;
                        }
                    }

                    if ($countTL == 1 && $countHL == 1) {
                        $stringTLHL .= "thi lại 0" . $countTL . " môn học, học lại 0" . $countTL . " môn học";
                    } else if ($countTL == 1) {
                        $stringTLHL .= "thi lại 0" . $countTL . " môn học";
                    } else if ($countHL == 1) {
                        $stringTLHL .= "học lại 0" . $countTL . " môn học";
                    }

                    $templateProcessor->setValue('xeploai', $hocLuc);
                    $templateProcessor->setValue('habac', "(Giảm một mức xếp loại tốt nghiệp, lý do " . $stringTLHL . ")");
                } else {
                    $hocLuc = $ketquahoctap->getHocLucByDiem($trungBinhTN, $quyChe2022);
                    $templateProcessor->setValue('xeploai', $hocLuc);
                    $templateProcessor->setValue('habac', '');
                }
            } else {
                $hocLuc = $ketquahoctap->getHocLucByDiem($trungBinhTN, $quyChe2022);
                $templateProcessor->setValue('xeploai', $hocLuc);
                $templateProcessor->setValue('habac', '');
            }

            // if (9 <= $trungBinhTN) {
            //     $templateProcessor->setValue('xeploai', 'Xuất sắc');
            // } else if (8 <= $trungBinhTN && $trungBinhTN < 9) {
            //     $templateProcessor->setValue('xeploai', 'Giỏi');
            // } else if (7 <= $trungBinhTN && $trungBinhTN < 8) {
            //     $templateProcessor->setValue('xeploai', 'Khá');
            // } else if (5 <= $trungBinhTN && $trungBinhTN < 7) {
            //     $templateProcessor->setValue('xeploai', 'Trung bình');
            // } else {
            //     $templateProcessor->setValue('xeploai', 'Yếu');
            // }
        } else {
            $templateProcessor->setValue('diemxeploai', '');
            $templateProcessor->setValue('xeploai', '');
            $templateProcessor->setValue('habac', '');
        }

        if ($TBMonChinhTri != -1) {
            $templateProcessor->setValue('dct', $TBMonChinhTri);
        } else {
            $templateProcessor->setValue('dct', '');
        }

        if ($TBMonThucHanh != -1) {
            $templateProcessor->setValue('dth', $TBMonThucHanh);
        } else {
            $templateProcessor->setValue('dth', '');
        }

        if ($TBMonLyThuyet != -1) {
            $templateProcessor->setValue('dlt', $TBMonLyThuyet);
        } else {
            $templateProcessor->setValue('dlt', '');
        }

        if ($hoc_ky == "123456") {
            $dsQuyetDinh = $sinhVien->quyetDinhTotNghiep()->get();
            if (sizeof($dsQuyetDinh) > 0) {
                $templateProcessor->setValue('qd', $dsQuyetDinh[0]->qd_ma);
                $templateProcessor->setValue('ngayqd', Carbon::parse($dsQuyetDinh[0]->qd_ngay)->format('d'));
                $templateProcessor->setValue('thangqd', Carbon::parse($dsQuyetDinh[0]->qd_ngay)->format('m'));
                $templateProcessor->setValue('namqd', Carbon::parse($dsQuyetDinh[0]->qd_ngay)->format('Y'));
            } else {
                $templateProcessor->setValue('qd', "...");
                $templateProcessor->setValue('ngayqd', "...");
                $templateProcessor->setValue('thangqd', "...");
                $templateProcessor->setValue('namqd', "...");
            }
        } else {
            $templateProcessor->setValue('qd', "...");
            $templateProcessor->setValue('ngayqd', "...");
            $templateProcessor->setValue('thangqd', "...");
            $templateProcessor->setValue('namqd', "...");
        }

        if ($tongDiemRenLuyen > 0) {
            $templateProcessor->setValue('diemrenluyen', number_format($tongDiemRenLuyen / $soHocKy));
        } else {
            $templateProcessor->setValue('diemrenluyen', '');
        }

        if ($soHocKy > 0) {
            $trungBinhRenLuyen = $tongDiemRenLuyen / $soHocKy;
            if (90 <= $trungBinhRenLuyen) {
                $templateProcessor->setValue('xeploairenluyen', 'Xuất sắc');
            } else if (80 <= $trungBinhRenLuyen && $trungBinhRenLuyen < 90) {
                $templateProcessor->setValue('xeploairenluyen', 'Tốt');
            } else if (70 <= $trungBinhRenLuyen && $trungBinhRenLuyen < 80) {
                $templateProcessor->setValue('xeploairenluyen', 'Khá');
            } else if (50 <= $trungBinhRenLuyen && $trungBinhRenLuyen < 70) {
                $templateProcessor->setValue('xeploairenluyen', 'Trung bình');
            } else {
                $templateProcessor->setValue('xeploairenluyen', 'Yếu');
            }
        } else {
            $templateProcessor->setValue('xeploairenluyen', '');
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
        header('Content-Disposition: attachment; filename="KQHT_' . $sinhVien->sv_ma . '.docx"');
        $templateProcessor->saveAs('php://output');
        // $fileDownload = 'KQHT_'.$sinhVien->sv_ma.'.docx';
        // $templateProcessor->saveAs($fileDownload);
    }
}
