<?php

namespace App\Excels;

use App\Traits\ExcelCursor;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use App\Models\LopHoc;
use App\Models\SinhVien;
use App\Models\BangDiem;
use App\Models\MonHoc;
use App\Models\DotThi;
use App\Enums\BangDiemType;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use DB;
use Illuminate\Support\Collection;
use App\Http\Controllers\Filters;
use App\Models\DotThiDotXetTotNghiep;
use App\Models\DotXetTotNghiep;
use App\Models\QuyetDinh;
use Carbon\Carbon;
use Exception;

class KetQuaHocTap
{
    use ExcelCursor;

    /**
     * Template path
     * @var string
     * @author ttdat
     * @version 1.0
     */
    private $template_semester = 'app/excel-template/KQHT_SEMESTER_TEMPLATE.xlsx';

    private $template_year = 'app/excel-template/KQHT_YEAR_TEMPLATE.xlsx';

    private $template_all = 'app/excel-template/KQHT_ALL.xlsx';

    private $template_danhsachxetthi = 'app/excel-template/DIEMMONHOCXETTHI.xlsx';

    private $template_dssinhvienthilai2022 = 'app/excel-template/THILAITOTNGHIEP2022.xlsx';
    private $template_dssinhvienthilai2020 = 'app/excel-template/THILAITOTNGHIEP2020.xlsx';

    private $template_dssinhviendudieukienthitn = 'app/excel-template/DOTTHI_TN_DUDIEUKIENDUTHI.xlsx';
    // private $template_dssinhvienthilai2020 = 'app/excel-template/THILAITOTNGHIEP2020.xlsx';

    private $template_diemdotthitheolop2022 = 'app/excel-template/DIEMDOTTHITHEOLOP2022.xlsx';
    private $template_diemdotthitheolop2020 = 'app/excel-template/DIEMDOTTHITHEOLOP2020.xlsx';

    private $template_diemmonhocdotthitheolop2022 = 'app/excel-template/DIEMMONHOCDOTTHI2022.xlsx';
    private $template_diemmonhocdotthitheolop2020 = 'app/excel-template/DIEMMONHOCDOTTHI2020.xlsx';
    private $template_diemthidautotnghieptheolop2022 = 'app/excel-template/DIEMTHIDAUTOTNGHIEP2022.xlsx';
    private $template_diemthidautotnghieptheolop2020 = 'app/excel-template/DIEMTHIDAUTOTNGHIEP2020.xlsx';
    private $template_diemthichuadattotnghieptheolop2022 = 'app/excel-template/DIEMTHICHUADATTOTNGHIEP2022.xlsx';
    private $template_diemthichuadattotnghieptheolop2020 = 'app/excel-template/DIEMTHICHUADATTOTNGHIEP2020.xlsx';
    private $template_diemmonhocdotthitheolopfull2022 = 'app/excel-template/DIEMMONHOCDOTTHI_FULL2022.xlsx';
    private $template_diemmonhocdotthitheolopfull2020 = 'app/excel-template/DIEMMONHOCDOTTHI_FULL2020.xlsx';

    /**
     * Download function
     * @param [type] $filterSemester
     * @param [type] $filterLhId
     * @return void
     * @author ttdat
     * @version 1.0
     */
    public function download($filterSemester, $filterLhId)
    {
        $result = $this->createSheet($filterSemester, $filterLhId);
        $writer = new Xlsx($result->spreadsheet);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $result->filename . '"');
        $writer->save("php://output");
        // $writer->save($result->filename);
    }

    public function downloadDanhSachXetThiTotNghiep($dt_id, $filterLhId)
    {
        $result = $this->createSheetDanhSachXetThiTotNghiep($filterLhId, $dt_id);
        $fileName = "DANH SÁCH HỌC SINH DỰ XÉT THI TỐT NGHIỆP: " . $result->fileName . ".xlsx";
        $writer = new Xlsx($result->spreadsheet);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        $writer->save("php://output");
    }

    public function downloadMonHocDotThi($dt_id, $filterLhId)
    {
        $result = $this->createSheetMonHocDotThi($filterLhId, $dt_id);
        $fileName = $result->fileName . ".xlsx";

        $writer = new Xlsx($result->spreadsheet);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        $writer->save("php://output");
    }

    public function downloadMonHocDotThiThiDatTN($dt_id, $filterLhId)
    {
        $result = $this->createSheetMonHocDotThiThiDatTN($filterLhId, $dt_id);
        $writer = new Xlsx($result->spreadsheet);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="DiemMonHocDotThi.xlsx"');
        $writer->save("php://output");
    }


    public function downloadMonHocDotThiFull($dt_id, $filterLhId)
    {
        $result = $this->createSheetMonHocDotThiFull($filterLhId, $dt_id);
        $fileName = $result->fileName . ".xlsx";
        $writer = new Xlsx($result->spreadsheet);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        $writer->save("php://output");
    }

    public function downloadSVThiLai($danhSachNganhNgheSinhVien)
    {
        $result = $this->createSheetSVThiLai($danhSachNganhNgheSinhVien);
        $fileName = $result->fileName . ".xlsx";

        $writer = new Xlsx($result->spreadsheet);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        $writer->save("php://output");
        // $writer->save($result->filename);
    }

    public function downloadKetQuaSinhVienThiTNDatKhongDat($danhSachNganhNgheSinhVien, $loai, $dxtn_id)
    {
        // loai: đạt = true
        if ($loai == 'true') {
            $result = $this->createSheetKetQuaThiDatTN($danhSachNganhNgheSinhVien, $dxtn_id);
        } else {
            $result = $this->createSheetKetQuaThiKhongDatTN($danhSachNganhNgheSinhVien);
        }
        $fileName = $result->fileName . ".xlsx";

        $writer = new Xlsx($result->spreadsheet);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        $writer->save("php://output");
        // $writer->save($result->filename);
    }

    public function downloadSVDuDieuKienKhongDuDieuKien($danhSachNganhNgheSinhVien, $loai)
    {
        if ($loai == 1) {
            $result = $this->createSheetSVDuDieuKienKhongDuDieuKien($danhSachNganhNgheSinhVien, $loai);
        } else if ($loai == 0) {
            $result = $this->createSheetSVDuDieuKienKhongDuDieuKien($danhSachNganhNgheSinhVien, $loai);
        }

        $fileName = $result->fileName . ".xlsx";

        $writer = new Xlsx($result->spreadsheet);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        $writer->save("php://output");
        // $writer->save($result->filename);
    }


    public function downloadDiemDotThiTheoLop($danhSachNganhNgheSinhVien)
    {
        $result = $this->createSheetDiemDotThiTheoLop($danhSachNganhNgheSinhVien);

        $writer = new Xlsx($result->spreadsheet);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="DiemDotThi.xlsx"');
        $writer->save("php://output");
        // $writer->save($result->filename);
    }

    public function createSheetDiemDotThiTheoLop($danhSachNganhNgheSinhVien)
    {
        $result = new \stdClass;
        $spreadsheet = new Spreadsheet();
        $nienche = $danhSachNganhNgheSinhVien[0]->lh_nienche;
        if ($nienche != 0) {
            $spreadsheet = IOFactory::load(storage_path($this->template_diemdotthitheolop2022));
        } else {
            $spreadsheet = IOFactory::load(storage_path($this->template_diemdotthitheolop2020));
        }
        $spreadsheet->getDefaultStyle()->getFont()->setName('Times New Roman');
        $spreadsheet->getDefaultStyle()->getFont()->setSize(13);

        $centerBorderStyle = [
            'font' => [
                'bold' => false,
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ]
        ];

        $centerStyle = [
            'font' => [
                'bold' => false,
                'size' => 13,
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => [
                    'argb' => 'FFFFFFFF',
                ],
            ]
        ];

        $normalStyle = [
            'font' => [
                'bold' => false,
                'size' => 13,
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ]
        ];

        $boldStyle = [
            'font' => [
                'bold' => true,
            ]
        ];

        $boldCenterStyle = [
            'font' => [
                'bold' => true,
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ]
        ];


        $boldLeftStyle = [
            'font' => [
                'bold' => true,
                'size' => 13,
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
            ]
        ];

        $markedColor = 'FFC4CC';
        $sheet = $spreadsheet->getActiveSheet();
        $currentAddress = 'A10';
        $index = 10;
        $sohocsinh = 0;
        $nganhNghe = $danhSachNganhNgheSinhVien[0];
        $hedaotao = $nganhNghe->hdt_id == 4 ? 'CAO ĐẲNG' : 'TRUNG CẤP';
        $sheet->setCellValue('B8', $nganhNghe->hdt_id == 4 ? 'Mã số SV' : 'Mã số HS');
        $sheet->setCellValue('A4', 'KẾT QUẢ THI TỐT NGHIỆP ' . $hedaotao . ' - HỆ: ' . mb_strtoupper($nganhNghe->kdt_he, 'UTF-8'));
        $sheet->setCellValue('A6', 'Ngành/Nghề: ' . $nganhNghe->nn_ten);
        $sheet->setCellValue('F6', 'Lớp: ' . $nganhNghe->lh_ten);
        $sheet->setCellValue('A5', 'ĐỢT THI: THÁNG ' . $nganhNghe->dt_tuthang . ' NĂM ' . $nganhNghe->dt_tunam);
        //$firstNoteRow = $this->currentRow($currentAddress);
        foreach ($danhSachNganhNgheSinhVien as $nnIndex => $nn) {
            foreach ($nn->danhsachsinhvien as $svIndex => $sv) {
                $sheet->insertNewRowBefore($this->currentRow('A' . $index), 1);
                $sheet->setCellValue('A' . $index, ($svIndex + 1) . '. ');
                $sheet->getStyle('A' . $index)->applyFromArray($centerStyle);

                $sheet->setCellValue('B' . $index, $sv->sv_ma);

                $sheet->setCellValue('C' . $index, $sv->sv_ho);
                $sheet->getStyle('C' . $index)->applyFromArray($normalStyle);

                $sheet->setCellValue('D' . $index, $sv->sv_ten);
                $sheet->getStyle('D' . $index)->applyFromArray($normalStyle);

                $sohocsinh++;
                $diemdotthi = DB::table('qlsv_dotthi_diem as d')
                    ->join('qlsv_dotthi_bangdiem as bd', 'bd.dt_bd_id', '=', 'd.dt_bd_id')
                    ->join('qlsv_monhoc as mh', 'mh.mh_id', '=', 'bd.mh_id')
                    ->where('d.sv_id', $sv->sv_id)
                    ->where('bd.lh_id', $nn->lh_id)
                    ->select('d.svd_first', 'd.svd_lan', 'bd.mh_id', 'mh.mh_loai', 'd.svd_loai', 'bd.dt_id')
                    ->get();
                $sv->thuchanhlan1 = -1;
                $sv->lythuyetlan1 = -1;
                $sv->chinhtrilan1 = -1;
                $sv->khoaluanlan1 = -1;

                $sv->thuchanhlan2 = -1;
                $sv->lythuyetlan2 = -1;
                $sv->chinhtrilan2 = -1;
                $sv->khoaluanlan2 = -1;

                $sv->thuchanhlan3 = -1;
                $sv->lythuyetlan3 = -1;
                $sv->chinhtrilan3 = -1;
                $sv->khoaluanlan3 = -1;

                foreach ($diemdotthi as $ddt) {
                    $sv->svd_loai = $ddt->svd_loai;
                    if ($ddt->svd_lan == 1) {
                        if ($ddt->mh_loai == 2) {
                            $sv->chinhtrilan1 = $ddt->svd_first;
                        } else if ($ddt->mh_loai == 3) {
                            $sv->thuchanhlan1 = $ddt->svd_first;
                        } else if ($ddt->mh_loai == 4) {
                            $sv->lythuyetlan1 = $ddt->svd_first;
                        } else if ($ddt->mh_loai == 5) {
                            $sv->khoaluanlan1 = $ddt->svd_first;
                        }
                        $sv->dtidlan1 = $ddt->dt_id;
                    } else if ($ddt->svd_lan == 2) {
                        if ($ddt->mh_loai == 2) {
                            $sv->chinhtrilan2 = $ddt->svd_first;
                        } else if ($ddt->mh_loai == 3) {
                            $sv->thuchanhlan2 = $ddt->svd_first;
                        } else if ($ddt->mh_loai == 4) {
                            $sv->lythuyetlan2 = $ddt->svd_first;
                        } else if ($ddt->mh_loai == 5) {
                            $sv->khoaluanlan2 = $ddt->svd_first;
                        }
                        $sv->dtidlan2 = $ddt->dt_id;
                    } else if ($ddt->svd_lan == 3) {
                        if ($ddt->mh_loai == 2) {
                            $sv->chinhtrilan3 = $ddt->svd_first;
                        } else if ($ddt->mh_loai == 3) {
                            $sv->thuchanhlan3 = $ddt->svd_first;
                        } else if ($ddt->mh_loai == 4) {
                            $sv->lythuyetlan3 = $ddt->svd_first;
                        } else if ($ddt->mh_loai == 5) {
                            $sv->khoaluanlan3 = $ddt->svd_first;
                        }
                        $sv->dtidlan3 = $ddt->dt_id;
                    }
                }

                $diemlythuyet = $sv->lythuyetlan1;
                $diemthuchanh = $sv->thuchanhlan1;
                $diemkhoaluan = $sv->khoaluanlan1;
                $diemchinhtri = $sv->chinhtrilan1;

                if ($sv->khoaluanlan2 != -1 && $sv->dtidlan2 == $nn->dt_id) {
                    $diemkhoaluan = $sv->khoaluanlan2;
                } else if ($sv->khoaluanlan3 != -1 && $sv->dtidlan3 == $nn->dt_id) {
                    $diemkhoaluan = $sv->khoaluanlan3;
                }

                if ($sv->thuchanhlan2 != -1 && $sv->dtidlan2 == $nn->dt_id) {
                    $diemthuchanh = $sv->thuchanhlan2;
                } else if ($sv->thuchanhlan3 != -1 && $sv->dtidlan3 == $nn->dt_id) {
                    $diemthuchanh = $sv->thuchanhlan3;
                }

                if ($sv->lythuyetlan2 != -1 && $sv->dtidlan2 == $nn->dt_id) {
                    $diemlythuyet = $sv->lythuyetlan2;
                } else if ($sv->lythuyetlan3 != -1 && $sv->dtidlan3 == $nn->dt_id) {
                    $diemlythuyet = $sv->lythuyetlan3;
                }

                if ($sv->chinhtrilan2 != -1 && $sv->dtidlan2 == $nn->dt_id) {
                    $diemlythuyet = $sv->chinhtrilan2;
                } else if ($sv->chinhtrilan3 != -1 && $sv->dtidlan3 == $nn->dt_id) {
                    $diemlythuyet = $sv->chinhtrilan3;
                }

                $ghiChu = "";

                if ($nienche != 0) {
                    $sheet->setCellValue('E' . $index, $diemlythuyet == -1 ? '' : $diemlythuyet);
                    $sheet->getStyle('E' . $index)->applyFromArray($centerStyle);
                    if ($diemlythuyet < 5) {
                        $sheet->getStyle('E' . $index)->getFill()
                            ->setFillType(Fill::FILL_SOLID)
                            ->getStartColor()->setARGB($markedColor);

                        $ghiChu .= 'ĐTNLT(L1:' . number_format($sv->lythuyetlan1, 1);
                        if ($sv->lythuyetlan2 != -1) {
                            $ghiChu .= ', L2:' . number_format($sv->lythuyetlan2, 1);
                        }
                        if ($sv->lythuyetlan3 != -1) {
                            $ghiChu .= ', L3:' . number_format($sv->lythuyetlan3, 1);
                        }
                        $ghiChu .= ')';
                    }

                    $sheet->setCellValue('F' . $index, $diemthuchanh == -1 ? '' : $diemthuchanh);
                    $sheet->getStyle('F' . $index)->applyFromArray($centerStyle);
                    if ($diemthuchanh < 5) {
                        $sheet->getStyle('F' . $index)->getFill()
                            ->setFillType(Fill::FILL_SOLID)
                            ->getStartColor()->setARGB($markedColor);
                        if ($ghiChu != '') {
                            $ghiChu .= ',';
                        }
                        $ghiChu .= ' ĐTNTH(L1:' . number_format($sv->thuchanhlan1, 1);
                        if ($sv->thuchanhlan2 != -1) {
                            $ghiChu .= ', L2:' . number_format($sv->thuchanhlan2, 1);
                        }
                        if ($sv->thuchanhlan3 != -1) {
                            $ghiChu .= ', L3:' . number_format($sv->thuchanhlan3, 1);
                        }
                        $ghiChu .= ')';
                    }

                    // $sheet->setCellValue('G'.$index, $ghiChu);
                    // $sheet->getStyle('G'.$index)->applyFromArray($normalStyle);
                } else {
                    $sheet->setCellValue('F' . $index, $diemlythuyet == -1 ? '' : $diemlythuyet);
                    $sheet->getStyle('F' . $index)->applyFromArray($centerStyle);
                    if ($diemlythuyet < 5) {
                        $sheet->getStyle('F' . $index)->getFill()
                            ->setFillType(Fill::FILL_SOLID)
                            ->getStartColor()->setARGB($markedColor);

                        $ghiChu .= 'ĐTNLT(L1:' . number_format($sv->lythuyetlan1, 1);
                        if ($sv->lythuyetlan2 != -1) {
                            $ghiChu .= ', L2:' . number_format($sv->lythuyetlan2, 1);
                        }
                        if ($sv->lythuyetlan3 != -1) {
                            $ghiChu .= ', L3:' . number_format($sv->lythuyetlan3, 1);
                        }
                        $ghiChu .= ')';
                    }

                    $sheet->setCellValue('G' . $index, $diemthuchanh == -1 ? '' : $diemthuchanh);
                    $sheet->getStyle('G' . $index)->applyFromArray($centerStyle);
                    if ($diemthuchanh < 5) {
                        $sheet->getStyle('G' . $index)->getFill()
                            ->setFillType(Fill::FILL_SOLID)
                            ->getStartColor()->setARGB($markedColor);
                        if ($ghiChu != '') {
                            $ghiChu .= ',';
                        }
                        $ghiChu .= ' ĐTNTH(L1:' . number_format($sv->thuchanhlan1, 1);
                        if ($sv->thuchanhlan2 != -1) {
                            $ghiChu .= ', L2:' . number_format($sv->thuchanhlan2, 1);
                        }
                        if ($sv->thuchanhlan3 != -1) {
                            $ghiChu .= ', L3:' . number_format($sv->thuchanhlan3, 1);
                        }
                        $ghiChu .= ')';
                    }

                    $sheet->setCellValue('E' . $index, $diemchinhtri == -1 ? '' : $diemchinhtri);
                    $sheet->getStyle('E' . $index)->applyFromArray($centerStyle);
                    if ($diemchinhtri < 5) {
                        $sheet->getStyle('E' . $index)->getFill()
                            ->setFillType(Fill::FILL_SOLID)
                            ->getStartColor()->setARGB($markedColor);
                        if ($ghiChu != '') {
                            $ghiChu .= ',';
                        }
                        $ghiChu .= ' ĐTNCT(L1:' . number_format($sv->chinhtrilan1, 1);
                        if ($sv->thuchanhlan2 != -1) {
                            $ghiChu .= ', L2:' . number_format($sv->chinhtrilan2, 1);
                        }
                        if ($sv->thuchanhlan3 != -1) {
                            $ghiChu .= ', L3:' . number_format($sv->chinhtrilan3, 1);
                        }
                        $ghiChu .= ')';
                    }


                    // $sheet->setCellValue('H'.$index, $ghiChu);
                    // $sheet->getStyle('H'.$index)->applyFromArray($normalStyle);
                }

                $index++;
            }
        }

        $result->spreadsheet = $spreadsheet;
        return $result;
    }

    public function createSheetSVThiLai($danhSachNganhNgheSinhVien)
    {
        $result = new \stdClass;
        $spreadsheet = new Spreadsheet();

        $dotThi = DotThi::with('dotXetTotNghiep')->where('qlsv_dotthi.dt_id', '=', $danhSachNganhNgheSinhVien[0]->dt_id)->first();

        $nienche = $danhSachNganhNgheSinhVien[0]->danhsachsinhvien[0]->lh_nienche;
        $nganhnghe = $danhSachNganhNgheSinhVien[0];
        if ($nienche != 0) {
            $spreadsheet = IOFactory::load(storage_path($this->template_dssinhvienthilai2022));
        } else {
            $spreadsheet = IOFactory::load(storage_path($this->template_dssinhvienthilai2020));
        }
        $spreadsheet->getDefaultStyle()->getFont()->setName('Times New Roman');
        $spreadsheet->getDefaultStyle()->getFont()->setSize(13);

        $centerBorderStyle = [
            'font' => [
                'bold' => false,
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ]
        ];

        $centerStyle = [
            'font' => [
                'bold' => false,
                'size' => 13,
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => [
                    'argb' => 'FFFFFFFF',
                ],
            ]
        ];

        $normalStyle = [
            'font' => [
                'bold' => false,
                'size' => 13,
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ]
        ];

        $boldStyle = [
            'font' => [
                'bold' => true,
            ]
        ];

        $boldCenterStyle = [
            'font' => [
                'bold' => true,
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ]
        ];


        $boldLeftStyle = [
            'font' => [
                'bold' => true,
                'size' => 13,
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
            ]
        ];

        $markedColor = '00a65a';
        $sheet = $spreadsheet->getActiveSheet();
        $currentAddress = 'A11';
        $index = 11;
        $sohocsinh = 0;

        //$firstNoteRow = $this->currentRow($currentAddress);
        foreach ($danhSachNganhNgheSinhVien as $nnIndex => $nn) {

            $sheet->insertNewRowBefore($this->currentRow('A' . $index), 1);
            $sheet->mergeCells('A' . $index . ':L' . $index);
            $sheet->setCellValue('A' . $index, ($nnIndex + 1) . '. Ngành/nghề ' . $nn->nn_ten);
            $sheet->getStyle('A' . $index)->applyFromArray($boldLeftStyle);
            $index++;
            $sinhVienIndex = 1;
            foreach ($nn->danhsachsinhvien as $svIndex => $sv) {
                $diemdotthi = DB::table('qlsv_dotthi_diem as d')
                    ->join('qlsv_dotthi_bangdiem as bd', 'bd.dt_bd_id', '=', 'd.dt_bd_id')
                    ->join('qlsv_monhoc as mh', 'mh.mh_id', '=', 'bd.mh_id')
                    ->where('d.sv_id', $sv->sv_id)
                    ->where('bd.lh_id', $sv->lh_id)
                    ->select('d.svd_first', 'd.svd_lan', 'bd.mh_id', 'mh.mh_loai', 'd.svd_loai', 'bd.dt_id')
                    ->get();
                $sv->thuchanhlan1 = -1;
                $sv->lythuyetlan1 = -1;
                $sv->chinhtrilan1 = -1;
                $sv->khoaluanlan1 = -1;

                $sv->thuchanhlan2 = -1;
                $sv->lythuyetlan2 = -1;
                $sv->chinhtrilan2 = -1;
                $sv->khoaluanlan2 = -1;

                $sv->thuchanhlan3 = -1;
                $sv->lythuyetlan3 = -1;
                $sv->chinhtrilan3 = -1;
                $sv->khoaluanlan3 = -1;

                foreach ($diemdotthi as $ddt) {
                    $sv->svd_loai = $ddt->svd_loai;
                    if ($ddt->svd_lan == 1) {
                        if ($ddt->mh_loai == 2) {
                            $sv->chinhtrilan1 = $ddt->svd_first;
                        } else if ($ddt->mh_loai == 3) {
                            $sv->thuchanhlan1 = $ddt->svd_first;
                        } else if ($ddt->mh_loai == 4) {
                            $sv->lythuyetlan1 = $ddt->svd_first;
                        } else if ($ddt->mh_loai == 5) {
                            $sv->khoaluanlan1 = $ddt->svd_first;
                        }
                        $sv->dtidlan1 = $ddt->dt_id;
                    } else if ($ddt->svd_lan == 2) {
                        if ($ddt->mh_loai == 2) {
                            $sv->chinhtrilan2 = $ddt->svd_first;
                        } else if ($ddt->mh_loai == 3) {
                            $sv->thuchanhlan2 = $ddt->svd_first;
                        } else if ($ddt->mh_loai == 4) {
                            $sv->lythuyetlan2 = $ddt->svd_first;
                        } else if ($ddt->mh_loai == 5) {
                            $sv->khoaluanlan2 = $ddt->svd_first;
                        }
                        $sv->dtidlan2 = $ddt->dt_id;
                    } else if ($ddt->svd_lan == 3) {
                        if ($ddt->mh_loai == 2) {
                            $sv->chinhtrilan3 = $ddt->svd_first;
                        } else if ($ddt->mh_loai == 3) {
                            $sv->thuchanhlan3 = $ddt->svd_first;
                        } else if ($ddt->mh_loai == 4) {
                            $sv->lythuyetlan3 = $ddt->svd_first;
                        } else if ($ddt->mh_loai == 5) {
                            $sv->khoaluanlan3 = $ddt->svd_first;
                        }
                        $sv->dtidlan3 = $ddt->dt_id;
                    }
                }

                $diemlythuyet = $sv->lythuyetlan1;
                $diemthuchanh = $sv->thuchanhlan1;
                $diemkhoaluan = $sv->khoaluanlan1;
                $diemchinhtri = $sv->chinhtrilan1;

                if ($sv->khoaluanlan2 != -1 && $sv->dtidlan2 == $nn->dt_id) {
                    $diemkhoaluan = $sv->khoaluanlan2;
                } else if ($sv->khoaluanlan3 != -1 && $sv->dtidlan3 == $nn->dt_id) {
                    $diemkhoaluan = $sv->khoaluanlan3;
                }

                if ($sv->thuchanhlan2 != -1 && $sv->dtidlan2 == $nn->dt_id) {
                    $diemthuchanh = $sv->thuchanhlan2;
                } else if ($sv->thuchanhlan3 != -1 && $sv->dtidlan3 == $nn->dt_id) {
                    $diemthuchanh = $sv->thuchanhlan3;
                }

                if ($sv->lythuyetlan2 != -1 && $sv->dtidlan2 == $nn->dt_id) {
                    $diemlythuyet = $sv->lythuyetlan2;
                } else if ($sv->lythuyetlan3 != -1 && $sv->dtidlan3 == $nn->dt_id) {
                    $diemlythuyet = $sv->lythuyetlan3;
                }

                if ($sv->chinhtrilan2 != -1 && $sv->dtidlan2 == $nn->dt_id) {
                    $diemchinhtri = $sv->chinhtrilan2;
                } else if ($sv->chinhtrilan3 != -1 && $sv->dtidlan3 == $nn->dt_id) {
                    $diemchinhtri = $sv->chinhtrilan3;
                }

                if ($diemchinhtri >= 5 && $diemlythuyet >= 5 && $diemthuchanh >= 5) {
                    continue;
                }

                $sheet->insertNewRowBefore($this->currentRow('A' . $index), 1);
                $sheet->setCellValue('A' . $index, ($sinhVienIndex) . '. ');
                $sinhVienIndex++;
                $sheet->getStyle('A' . $index)->applyFromArray($centerStyle);

                $sheet->setCellValue('B' . $index, $sv->sv_ma);

                $sheet->setCellValue('C' . $index, $sv->sv_ho);
                $sheet->getStyle('C' . $index)->applyFromArray($normalStyle);

                $sheet->setCellValue('D' . $index, $sv->sv_ten);
                $sheet->getStyle('D' . $index)->applyFromArray($normalStyle);

                if ($sv->sv_gioitinh == 1) {
                    $sheet->setCellValue('E' . $index, $sv->sv_ngaysinh);
                    $sheet->getStyle('E' . $index)->applyFromArray($normalStyle);
                } else {
                    $sheet->setCellValue('F' . $index, $sv->sv_ngaysinh);
                    $sheet->getStyle('F' . $index)->applyFromArray($normalStyle);
                }

                $sheet->setCellValue('G' . $index, $sv->lh_ten . ' - K' . $nn->kdt_khoa);
                $sheet->getStyle('G' . $index)->applyFromArray($centerStyle);

                $sohocsinh++;

                $ghiChu = "";

                if ($nienche != 0) {
                    $sheet->setCellValue('H' . $index, $diemlythuyet == -1 ? '' : $diemlythuyet);
                    $sheet->getStyle('H' . $index)->applyFromArray($centerStyle)->getNumberFormat()->setFormatCode('0.0');

                    if ($diemlythuyet < 5) {
                        $sheet->getStyle('H' . $index)->getFill()
                            ->setFillType(Fill::FILL_SOLID)
                            ->getStartColor()->setARGB($markedColor);
                        $sheet->setCellValue('J' . $index, 'X');
                        $sheet->getStyle('J' . $index)->applyFromArray($centerStyle)->getNumberFormat()->setFormatCode('0.0');

                        $ghiChu .= 'ĐTNLT(L1:' . number_format($sv->lythuyetlan1, 1);
                        if ($sv->lythuyetlan2 != -1) {
                            $ghiChu .= ', L2:' . number_format($sv->lythuyetlan2, 1);
                        }
                        if ($sv->lythuyetlan3 != -1) {
                            $ghiChu .= ', L3:' . number_format($sv->lythuyetlan3, 1);
                        }
                        $ghiChu .= ')';
                    }

                    $sheet->setCellValue('I' . $index, $diemthuchanh == -1 ? '' : $diemthuchanh);
                    $sheet->getStyle('I' . $index)->applyFromArray($centerStyle)->getNumberFormat()->setFormatCode('0.0');
                    if ($diemthuchanh < 5) {
                        $sheet->getStyle('I' . $index)->getFill()
                            ->setFillType(Fill::FILL_SOLID)
                            ->getStartColor()->setARGB($markedColor);
                        $sheet->setCellValue('K' . $index, 'X');
                        $sheet->getStyle('K' . $index)->applyFromArray($centerStyle)->getNumberFormat()->setFormatCode('0.0');
                        if ($ghiChu != '') {
                            $ghiChu .= ',';
                        }
                        $ghiChu .= ' ĐTNTH(L1:' . number_format($sv->thuchanhlan1, 1);
                        if ($sv->thuchanhlan2 != -1) {
                            $ghiChu .= ', L2:' . number_format($sv->thuchanhlan2, 1);
                        }
                        if ($sv->thuchanhlan3 != -1) {
                            $ghiChu .= ', L3:' . number_format($sv->thuchanhlan3, 1);
                        }
                        $ghiChu .= ')';
                    }

                    $sheet->getColumnDimension("L")->setAutoSize(true);
                    $sheet->setCellValue('L' . $index, $ghiChu);
                    $sheet->getStyle('L' . $index)->applyFromArray($normalStyle);
                } else {
                    $sheet->setCellValue('I' . $index, $diemlythuyet == -1 ? '' : $diemlythuyet);
                    $sheet->getStyle('I' . $index)->applyFromArray($centerStyle)->getNumberFormat()->setFormatCode('0.0');
                    if ($diemlythuyet < 5) {
                        $sheet->getStyle('I' . $index)->getFill()
                            ->setFillType(Fill::FILL_SOLID)
                            ->getStartColor()->setARGB($markedColor);
                        $sheet->setCellValue('L' . $index, 'X');
                        $sheet->getStyle('L' . $index)->applyFromArray($centerStyle)->getNumberFormat()->setFormatCode('0.0');

                        $ghiChu .= 'ĐTNLT(L1:' . number_format($sv->lythuyetlan1, 1);
                        if ($sv->lythuyetlan2 != -1) {
                            $ghiChu .= ', L2:' . number_format($sv->lythuyetlan2, 1);
                        }
                        if ($sv->lythuyetlan3 != -1) {
                            $ghiChu .= ', L3:' . number_format($sv->lythuyetlan3, 1);
                        }
                        $ghiChu .= ')';
                    }

                    $sheet->setCellValue('J' . $index, $diemthuchanh == -1 ? '' : $diemthuchanh);
                    $sheet->getStyle('J' . $index)->applyFromArray($centerStyle)->getNumberFormat()->setFormatCode('0.0');
                    if ($diemthuchanh < 5) {
                        $sheet->getStyle('J' . $index)->getFill()
                            ->setFillType(Fill::FILL_SOLID)
                            ->getStartColor()->setARGB($markedColor);
                        $sheet->setCellValue('M' . $index, 'X');
                        $sheet->getStyle('M' . $index)->applyFromArray($centerStyle)->getNumberFormat()->setFormatCode('0.0');
                        if ($ghiChu != '') {
                            $ghiChu .= ',';
                        }
                        $ghiChu .= ' ĐTNTH(L1:' . number_format($sv->thuchanhlan1, 1);
                        if ($sv->thuchanhlan2 != -1) {
                            $ghiChu .= ', L2:' . number_format($sv->thuchanhlan2, 1);
                        }
                        if ($sv->thuchanhlan3 != -1) {
                            $ghiChu .= ', L3:' . number_format($sv->thuchanhlan3, 1);
                        }
                        $ghiChu .= ')';
                    }

                    $sheet->setCellValue('H' . $index, $diemchinhtri == -1 ? '' : $diemchinhtri);
                    $sheet->getStyle('H' . $index)->applyFromArray($centerStyle)->getNumberFormat()->setFormatCode('0.0');
                    if ($diemchinhtri < 5) {
                        $sheet->getStyle('H' . $index)->getFill()
                            ->setFillType(Fill::FILL_SOLID)
                            ->getStartColor()->setARGB($markedColor);
                        $sheet->setCellValue('K' . $index, 'X');
                        $sheet->getStyle('K' . $index)->applyFromArray($centerStyle)->getNumberFormat()->setFormatCode('0.0');
                        if ($ghiChu != '') {
                            $ghiChu .= ',';
                        }
                        $ghiChu .= ' ĐTNCT(L1:' . number_format($sv->chinhtrilan1, 1);
                        if ($sv->thuchanhlan2 != -1) {
                            $ghiChu .= ', L2:' . number_format($sv->chinhtrilan2, 1);
                        }
                        if ($sv->thuchanhlan3 != -1) {
                            $ghiChu .= ', L3:' . number_format($sv->chinhtrilan3, 1);
                        }
                        $ghiChu .= ')';
                    }

                    $sheet->getColumnDimension("N")->setAutoSize(true);
                    $sheet->setCellValue('N' . $index, $ghiChu);
                    $sheet->getStyle('N' . $index)->applyFromArray($normalStyle);
                }

                $index++;
            }
        }

        $sheet->setCellValue('A7', mb_strtoupper('ĐỢT XÉT: ' . $dotThi->dt_ten, 'UTF-8'));
        if ($nganhnghe->hdt_id == 5) {
            $sheet->setCellValue('A5', 'DANH SÁCH HỌC SINH THI LẠI TỐT NGHIỆP');
            $sheet->setCellValue('A6', 'TRÌNH ĐỘ: TRUNG CẤP - HỆ: ' . mb_strtoupper($nganhnghe->kdt_he, 'UTF-8'));
            $sheet->setCellValue('A' . ($index + 1), 'Tổng số học sinh: ' . $sohocsinh);
            $sheet->setCellValue('B9', 'MSHS');
        } else {
            $sheet->setCellValue('A5', 'DANH SÁCH SINH VIÊN THI LẠI TỐT NGHIỆP');
            $sheet->setCellValue('A6', 'TRÌNH ĐỘ: CAO ĐẲNG - HỆ: ' . mb_strtoupper($nganhnghe->kdt_he, 'UTF-8'));
            $sheet->setCellValue('A' . ($index + 1), 'Tổng số sinh viên: ' . $sohocsinh);
            $sheet->setCellValue('B9', 'MSSV');
        }
        $sheet->getStyle('A' . $index)->applyFromArray($boldLeftStyle);

        $result->spreadsheet = $spreadsheet;
        $result->fileName = mb_strtoupper('DSTLTN - ĐỢT XÉT: ' . $dotThi->dt_ten, 'UTF-8');
        return $result;
    }

    public function createSheetKetQuaThiDatTN($danhSachNganhNgheSinhVien, $dxtn_id)
    {
        $result = new \stdClass;
        $spreadsheet = new Spreadsheet();
        // dd($danhSachNganhNgheSinhVien[0]->danhsachsinhvien[0]->lopHoc);

        $dotThi = DotThi::with('dotXetTotNghiep')->where('qlsv_dotthi.dt_id', '=', $danhSachNganhNgheSinhVien[0]->dt_id)->first();
        // $dotxettotnghiep = DotXetTotNghiep::find($dxtn_id);

        $nienche = $danhSachNganhNgheSinhVien[0]->danhsachsinhvien[0]["lh_nienche"];
        $nganhnghe = $danhSachNganhNgheSinhVien[0];

        if ($nienche != 0) {
            $spreadsheet = IOFactory::load(storage_path($this->template_diemthidautotnghieptheolop2022));
        } else {
            $spreadsheet = IOFactory::load(storage_path($this->template_diemthidautotnghieptheolop2020));
        }

        $spreadsheet->getDefaultStyle()->getFont()->setName('Times New Roman');
        $spreadsheet->getDefaultStyle()->getFont()->setSize(13);

        // Thống kê học lực
        $thongKeHocLuc = null;

        $centerBorderStyle = [
            'font' => [
                'bold' => false,
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ]
        ];

        $centerStyle = [
            'font' => [
                'bold' => false,
                'size' => 13,
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => [
                    'argb' => 'FFFFFFFF',
                ],
            ]
        ];

        $normalStyle = [
            'font' => [
                'bold' => false,
                'size' => 13,
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ]
        ];

        $boldStyle = [
            'font' => [
                'bold' => true,
            ]
        ];

        $boldCenterStyle = [
            'font' => [
                'bold' => true,
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ]
        ];


        $boldLeftStyle = [
            'font' => [
                'bold' => true,
                'size' => 13,
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
            ]
        ];

        $markedColor = 'FFC4CC';
        $sheet = $spreadsheet->getActiveSheet();
        $currentAddress = 'A11';
        $index = 12;
        $sohocsinh = 0;
        $thongKeHocLucTemp = collect();
        $thongKeHocLuc = collect([
            'xuất sắc' => 0,
            'Giỏi' => 0,
            'Khá' => 0,
            'Trung bình' => 0,
            'Trung bình khá' => 0,
            'Rớt' => 0
        ]);

        $thongKeMessage = [];
        $soHocSinhXepLoai = $sohocsinh;
        $khongXepLoai = 0;

        //$firstNoteRow = $this->currentRow($currentAddress);
        foreach ($danhSachNganhNgheSinhVien as $nnIndex => $nn) {
            $sheet->insertNewRowBefore($this->currentRow('A' . $index), 1);
            if ($nienche != 0) {
                $sheet->mergeCells('A' . $index . ':L' . $index);
            } else {
                $sheet->mergeCells('A' . $index . ':M' . $index);
            }
            $sheet->setCellValue('A' . $index, ($nnIndex + 1) . '. Ngành/nghề ' . $nn->nn_ten);
            $sheet->getStyle('A' . $index)->applyFromArray($boldLeftStyle);
            $index++;

            // sort avg_totnghiep giảm dần
            $danhsachsinhvien = $nn->danhsachsinhvien->sortBy([
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

            foreach ($danhsachsinhvien as $svIndex => $sv) {
                $diemdotthi = DB::table('qlsv_dotthi_diem as d')
                    ->join('qlsv_dotthi_bangdiem as bd', 'bd.dt_bd_id', '=', 'd.dt_bd_id')
                    ->join('qlsv_monhoc as mh', 'mh.mh_id', '=', 'bd.mh_id')
                    ->where('d.sv_id', $sv->sv_id)
                    ->where('bd.lh_id', $sv->lh_id)
                    ->select('d.svd_first', 'd.svd_lan', 'bd.mh_id', 'mh.mh_loai', 'd.svd_loai', 'bd.dt_id')
                    ->get();
                $sv->thuchanhlan1 = -1;
                $sv->lythuyetlan1 = -1;
                $sv->chinhtrilan1 = -1;
                $sv->khoaluanlan1 = -1;

                $sv->thuchanhlan2 = -1;
                $sv->lythuyetlan2 = -1;
                $sv->chinhtrilan2 = -1;
                $sv->khoaluanlan2 = -1;

                $sv->thuchanhlan3 = -1;
                $sv->lythuyetlan3 = -1;
                $sv->chinhtrilan3 = -1;
                $sv->khoaluanlan3 = -1;

                foreach ($diemdotthi as $ddt) {
                    $sv->svd_loai = $ddt->svd_loai;
                    if ($ddt->svd_lan == 1) {
                        if ($ddt->mh_loai == 2) {
                            $sv->chinhtrilan1 = $ddt->svd_first;
                        } else if ($ddt->mh_loai == 3) {
                            $sv->thuchanhlan1 = $ddt->svd_first;
                        } else if ($ddt->mh_loai == 4) {
                            $sv->lythuyetlan1 = $ddt->svd_first;
                        } else if ($ddt->mh_loai == 5) {
                            $sv->khoaluanlan1 = $ddt->svd_first;
                        }
                        $sv->dtidlan1 = $ddt->dt_id;
                    } else if ($ddt->svd_lan == 2) {
                        if ($ddt->mh_loai == 2) {
                            $sv->chinhtrilan2 = $ddt->svd_first;
                        } else if ($ddt->mh_loai == 3) {
                            $sv->thuchanhlan2 = $ddt->svd_first;
                        } else if ($ddt->mh_loai == 4) {
                            $sv->lythuyetlan2 = $ddt->svd_first;
                        } else if ($ddt->mh_loai == 5) {
                            $sv->khoaluanlan2 = $ddt->svd_first;
                        }
                        $sv->dtidlan2 = $ddt->dt_id;
                    } else if ($ddt->svd_lan == 3) {
                        if ($ddt->mh_loai == 2) {
                            $sv->chinhtrilan3 = $ddt->svd_first;
                        } else if ($ddt->mh_loai == 3) {
                            $sv->thuchanhlan3 = $ddt->svd_first;
                        } else if ($ddt->mh_loai == 4) {
                            $sv->lythuyetlan3 = $ddt->svd_first;
                        } else if ($ddt->mh_loai == 5) {
                            $sv->khoaluanlan3 = $ddt->svd_first;
                        }
                        $sv->dtidlan3 = $ddt->dt_id;
                    }
                }

                $diemlythuyet = $sv->lythuyetlan1;
                $diemthuchanh = $sv->thuchanhlan1;
                $diemkhoaluan = $sv->khoaluanlan1;
                $diemchinhtri = $sv->chinhtrilan1;

                if ($sv->khoaluanlan2 != -1 && $sv->dtidlan2 == $nn->dt_id) {
                    $diemkhoaluan = $sv->khoaluanlan2;
                } else if ($sv->khoaluanlan3 != -1 && $sv->dtidlan3 == $nn->dt_id) {
                    $diemkhoaluan = $sv->khoaluanlan3;
                }

                if ($sv->thuchanhlan2 != -1 && $sv->dtidlan2 == $nn->dt_id) {
                    $diemthuchanh = $sv->thuchanhlan2;
                } else if ($sv->thuchanhlan3 != -1 && $sv->dtidlan3 == $nn->dt_id) {
                    $diemthuchanh = $sv->thuchanhlan3;
                }

                if ($sv->lythuyetlan2 != -1 && $sv->dtidlan2 == $nn->dt_id) {
                    $diemlythuyet = $sv->lythuyetlan2;
                } else if ($sv->lythuyetlan3 != -1 && $sv->dtidlan3 == $nn->dt_id) {
                    $diemlythuyet = $sv->lythuyetlan3;
                }

                if ($sv->chinhtrilan2 != -1 && $sv->dtidlan2 == $nn->dt_id) {
                    $diemchinhtri = $sv->chinhtrilan2;
                } else if ($sv->chinhtrilan3 != -1 && $sv->dtidlan3 == $nn->dt_id) {
                    $diemchinhtri = $sv->chinhtrilan3;
                }

                // if ($diemchinhtri >= 5 && $diemlythuyet >= 5 && $diemthuchanh >= 5) {
                //     continue;
                // }

                $sheet->insertNewRowBefore($this->currentRow('A' . $index), 1);
                $sheet->setCellValue('A' . $index, ($svIndex + 1) . '. ');
                $sheet->getStyle('A' . $index)->applyFromArray($centerStyle);

                $sheet->setCellValue('B' . $index, $sv->sv_ma);
                $sheet->getStyle('B' . $index)->applyFromArray($centerStyle);

                $sheet->setCellValue('C' . $index, $sv->sv_ho);
                $sheet->getStyle('C' . $index)->applyFromArray($normalStyle);

                $sheet->setCellValue('D' . $index, $sv->sv_ten);
                $sheet->getStyle('D' . $index)->applyFromArray($normalStyle);

                if ($sv->sv_gioitinh == 1) {
                    $sheet->setCellValue('E' . $index, $sv->sv_ngaysinh);
                    $sheet->getStyle('E' . $index)->applyFromArray($normalStyle);
                } else {
                    $sheet->setCellValue('F' . $index, $sv->sv_ngaysinh);
                    $sheet->getStyle('F' . $index)->applyFromArray($normalStyle);
                }

                $sheet->setCellValue('G' . $index, $sv->lh_ten . ' - K' . $sv->kdt_khoa);
                $sheet->getStyle('G' . $index)->applyFromArray($centerStyle);
                $sheet->getColumnDimension("G")->setAutoSize(true);

                $sohocsinh++;

                $ghiChu = "";


                // Ghi chú
                // if ($nienche != 0) {
                //     $sheet->setCellValue('M' . $index, $sv->toanKhoa->ghiChuThiLai);
                //     $sheet->getStyle($currentAddress)->applyFromArray($centerBorderStyle);
                // } else {
                //     $sheet->setCellValue('L' . $index, $sv->toanKhoa->ghiChuThiLai);
                //     $sheet->getStyle($currentAddress)->applyFromArray($centerBorderStyle);
                // }


                // Điểm TB_tk
                if (isset($sv->toanKhoa->avg)) {
                    $sheet->getStyle('H' . $index)->applyFromArray($centerBorderStyle);
                    $sheet->setCellValue('H' . $index, $sv->toanKhoa->avg);
                    $sheet->getStyle('H' . $index)->getNumberFormat()->setFormatCode('0.0');
                }

                // Điểm đợt thi

                // TẠM BỎ
                // if ($sv->khoaluanlan1 != -1) {
                //     $sheet->getStyle('I' . $index)->applyFromArray($centerBorderStyle);
                //     $sheet->setCellValue('I' . $index,  $sv->khoaluanlan1);
                //     $sheet->getStyle('I' . $index)->getNumberFormat()->setFormatCode('0.0');

                //     if ($sv->khoaluanlan1 < 5) {
                //         $sheet->getStyle('I' . $index)->getFill()
                //             ->setFillType(Fill::FILL_SOLID)
                //             ->getStartColor()->setARGB($markedColor);
                //     }
                // }

                // if ($sv->khoaluanlan2 != -1) {
                //     $sheet->getStyle('I' . $index)->applyFromArray($centerBorderStyle);
                //     $sheet->setCellValue('I' . $index,  $sv->khoaluanlan2);
                //     $sheet->getStyle('I' . $index)->getNumberFormat()->setFormatCode('0.0');

                //     if ($sv->khoaluanlan2 < 5) {
                //         $sheet->getStyle('I' . $index)->getFill()
                //             ->setFillType(Fill::FILL_SOLID)
                //             ->getStartColor()->setARGB($markedColor);
                //     }
                // }

                // if ($sv->khoaluanlan3 != -1) {
                //     $sheet->getStyle('I' . $index)->applyFromArray($centerBorderStyle);
                //     $sheet->setCellValue('I' . $index,  $sv->khoaluanlan3);
                //     $sheet->getStyle('I' . $index)->getNumberFormat()->setFormatCode('0.0');

                //     if ($sv->khoaluanlan3 < 5) {
                //         $sheet->getStyle('I' . $index)->getFill()
                //             ->setFillType(Fill::FILL_SOLID)
                //             ->getStartColor()->setARGB($markedColor);
                //     }
                // }

                // if ($nienche == 0) {
                //     if ($sv->chinhtrilan1 != -1) {
                //         $sheet->getStyle('J' . $index)->applyFromArray($centerBorderStyle);
                //         $sheet->setCellValue('J' . $index,  $sv->chinhtrilan1);
                //         $sheet->getStyle('J' . $index)->getNumberFormat()->setFormatCode('0.0');

                //         if ($sv->chinhtrilan1 < 5) {
                //             $sheet->getStyle('j' . $index)->getFill()
                //                 ->setFillType(Fill::FILL_SOLID)
                //                 ->getStartColor()->setARGB($markedColor);
                //         }
                //     }

                //     if ($sv->chinhtrilan2 != -1) {
                //         $sheet->getStyle('J' . $index)->applyFromArray($centerBorderStyle);
                //         $sheet->setCellValue('J' . $index,  $sv->chinhtrilan2);
                //         $sheet->getStyle('J' . $index)->getNumberFormat()->setFormatCode('0.0');

                //         if ($sv->chinhtrilan2 < 5) {
                //             $sheet->getStyle('j' . $index)->getFill()
                //                 ->setFillType(Fill::FILL_SOLID)
                //                 ->getStartColor()->setARGB($markedColor);
                //         }
                //     }

                //     if ($sv->chinhtrilan3 != -1) {
                //         $sheet->getStyle('J' . $index)->applyFromArray($centerBorderStyle);
                //         $sheet->setCellValue('J' . $index,  $sv->chinhtrilan3);
                //         $sheet->getStyle('J' . $index)->getNumberFormat()->setFormatCode('0.0');

                //         if ($sv->chinhtrilan3 < 5) {
                //             $sheet->getStyle('J' . $index)->getFill()
                //                 ->setFillType(Fill::FILL_SOLID)
                //                 ->getStartColor()->setARGB($markedColor);
                //         }
                //     }

                //     if ($sv->lythuyetlan1 != -1) {
                //         $sheet->getStyle('K' . $index)->applyFromArray($centerBorderStyle);
                //         $sheet->setCellValue('K' . $index,  $sv->lythuyetlan1);
                //         $sheet->getStyle('K' . $index)->getNumberFormat()->setFormatCode('0.0');

                //         if ($sv->lythuyetlan1 < 5) {
                //             $sheet->getStyle('K' . $index)->getFill()
                //                 ->setFillType(Fill::FILL_SOLID)
                //                 ->getStartColor()->setARGB($markedColor);
                //         }
                //     }

                //     if ($sv->thuchanhlan1 != -1) {
                //         $sheet->getStyle('L' . $index)->applyFromArray($centerBorderStyle);
                //         $sheet->setCellValue('L' . $index,  $sv->thuchanhlan1);
                //         $sheet->getStyle('L' . $index)->getNumberFormat()->setFormatCode('0.0');

                //         if ($sv->thuchanhlan1 < 5) {
                //             $sheet->getStyle('L' . $index)->getFill()
                //                 ->setFillType(Fill::FILL_SOLID)
                //                 ->getStartColor()->setARGB($markedColor);
                //         }
                //     }

                //     if ($sv->lythuyetlan2 != -1) {
                //         $sheet->getStyle('K' . $index)->applyFromArray($centerBorderStyle);
                //         $sheet->setCellValue('K' . $index,  $sv->lythuyetlan2);
                //         $sheet->getStyle('K' . $index)->getNumberFormat()->setFormatCode('0.0');

                //         if ($sv->lythuyetlan2 < 5) {
                //             $sheet->getStyle('k' . $index)->getFill()
                //                 ->setFillType(Fill::FILL_SOLID)
                //                 ->getStartColor()->setARGB($markedColor);
                //         }
                //     }

                //     if ($sv->thuchanhlan2 != -1) {
                //         $sheet->getStyle('L' . $index)->applyFromArray($centerBorderStyle);
                //         $sheet->setCellValue('L' . $index,  $sv->thuchanhlan2);
                //         $sheet->getStyle('L' . $index)->getNumberFormat()->setFormatCode('0.0');

                //         if ($sv->thuchanhlan2 < 5) {
                //             $sheet->getStyle('L' . $index)->getFill()
                //                 ->setFillType(Fill::FILL_SOLID)
                //                 ->getStartColor()->setARGB($markedColor);
                //         }
                //     }
                //     if ($sv->lythuyetlan3 != -1) {
                //         $sheet->getStyle('K' . $index)->applyFromArray($centerBorderStyle);
                //         $sheet->setCellValue('K' . $index,  $sv->lythuyetlan3);
                //         $sheet->getStyle('K' . $index)->getNumberFormat()->setFormatCode('0.0');

                //         if ($sv->lythuyetlan3 < 5) {
                //             $sheet->getStyle('K' . $index)->getFill()
                //                 ->setFillType(Fill::FILL_SOLID)
                //                 ->getStartColor()->setARGB($markedColor);
                //         }
                //     }

                //     if ($sv->thuchanhlan3 != -1) {
                //         $sheet->getStyle('L' . $index)->applyFromArray($centerBorderStyle);
                //         $sheet->setCellValue('L' . $index,  $sv->thuchanhlan3);
                //         $sheet->getStyle('L' . $index)->getNumberFormat()->setFormatCode('0.0');

                //         if ($sv->thuchanhlan3 < 5) {
                //             $sheet->getStyle('L' . $index)->getFill()
                //                 ->setFillType(Fill::FILL_SOLID)
                //                 ->getStartColor()->setARGB($markedColor);
                //         }
                //     }
                // } else {
                //     if ($sv->lythuyetlan1 != -1) {
                //         $sheet->getStyle('J' . $index)->applyFromArray($centerBorderStyle);
                //         $sheet->setCellValue('J' . $index,  $sv->lythuyetlan1);
                //         $sheet->getStyle('J' . $index)->getNumberFormat()->setFormatCode('0.0');

                //         if ($sv->lythuyetlan1 < 5) {
                //             $sheet->getStyle('J' . $index)->getFill()
                //                 ->setFillType(Fill::FILL_SOLID)
                //                 ->getStartColor()->setARGB($markedColor);
                //         }
                //     }

                //     if ($sv->thuchanhlan1 != -1) {
                //         $sheet->getStyle('K' . $index)->applyFromArray($centerBorderStyle);
                //         $sheet->setCellValue('K' . $index,  $sv->thuchanhlan1);
                //         $sheet->getStyle('K' . $index)->getNumberFormat()->setFormatCode('0.0');

                //         if ($sv->thuchanhlan1 < 5) {
                //             $sheet->getStyle('K' . $index)->getFill()
                //                 ->setFillType(Fill::FILL_SOLID)
                //                 ->getStartColor()->setARGB($markedColor);
                //         }
                //     }

                //     if ($sv->lythuyetlan2 != -1) {
                //         $sheet->getStyle('J' . $index)->applyFromArray($centerBorderStyle);
                //         $sheet->setCellValue('J' . $index,  $sv->lythuyetlan2);
                //         $sheet->getStyle('J' . $index)->getNumberFormat()->setFormatCode('0.0');

                //         if ($sv->lythuyetlan2 < 5) {
                //             $sheet->getStyle('J' . $index)->getFill()
                //                 ->setFillType(Fill::FILL_SOLID)
                //                 ->getStartColor()->setARGB($markedColor);
                //         }
                //     }

                //     if ($sv->thuchanhlan2 != -1) {
                //         $sheet->getStyle('K' . $index)->applyFromArray($centerBorderStyle);
                //         $sheet->setCellValue('K' . $index,  $sv->thuchanhlan2);
                //         $sheet->getStyle('K' . $index)->getNumberFormat()->setFormatCode('0.0');

                //         if ($sv->thuchanhlan2 < 5) {
                //             $sheet->getStyle('K' . $index)->getFill()
                //                 ->setFillType(Fill::FILL_SOLID)
                //                 ->getStartColor()->setARGB($markedColor);
                //         }
                //     }
                //     if ($sv->lythuyetlan3 != -1) {
                //         $sheet->getStyle('J' . $index)->applyFromArray($centerBorderStyle);
                //         $sheet->setCellValue('J' . $index,  $sv->lythuyetlan3);
                //         $sheet->getStyle('J' . $index)->getNumberFormat()->setFormatCode('0.0');

                //         if ($sv->lythuyetlan3 < 5) {
                //             $sheet->getStyle('J' . $index)->getFill()
                //                 ->setFillType(Fill::FILL_SOLID)
                //                 ->getStartColor()->setARGB($markedColor);
                //         }
                //     }

                //     if ($sv->thuchanhlan3 != -1) {
                //         $sheet->getStyle('K' . $index)->applyFromArray($centerBorderStyle);
                //         $sheet->setCellValue('K' . $index,  $sv->thuchanhlan3);
                //         $sheet->getStyle('K' . $index)->getNumberFormat()->setFormatCode('0.0');

                //         if ($sv->thuchanhlan3 < 5) {
                //             $sheet->getStyle('K' . $index)->getFill()
                //                 ->setFillType(Fill::FILL_SOLID)
                //                 ->getStartColor()->setARGB($markedColor);
                //         }
                //     }
                // }
                // TẠM BỎ


                if ($sv->svd_loai == 1) {
                    if ($diemkhoaluan < 5) {
                        $sheet->getStyle('I' . $index)->getFill()
                            ->setFillType(Fill::FILL_SOLID)
                            ->getStartColor()->setARGB($markedColor);
                    }
                } else {
                    if ($nienche != 0) {
                        $sheet->setCellValue('J' . $index, $diemlythuyet == -1 ? '' : $diemlythuyet);
                        $sheet->getStyle('J' . $index)->applyFromArray($centerStyle)->getNumberFormat()->setFormatCode('0.0');

                        if ($diemlythuyet < 5) {
                            $sheet->getStyle('J' . $index)->getFill()
                                ->setFillType(Fill::FILL_SOLID)
                                ->getStartColor()->setARGB($markedColor);
                        }

                        $sheet->setCellValue('K' . $index, $diemthuchanh == -1 ? '' : $diemthuchanh);
                        $sheet->getStyle('K' . $index)->applyFromArray($centerStyle)->getNumberFormat()->setFormatCode('0.0');

                        if ($diemthuchanh < 5) {
                            $sheet->getStyle('K' . $index)->getFill()
                                ->setFillType(Fill::FILL_SOLID)
                                ->getStartColor()->setARGB($markedColor);
                        }

                        // $sheet->getColumnDimension("L")->setAutoSize(true);
                        // $sheet->setCellValue('L' . $index, $ghiChu);
                        // $sheet->getStyle('L' . $index)->applyFromArray($normalStyle);
                    } else {
                        $sheet->setCellValue('J' . $index, $diemchinhtri == -1 ? '' : $diemchinhtri);
                        $sheet->getStyle('J' . $index)->applyFromArray($centerStyle)->getNumberFormat()->setFormatCode('0.0');

                        if ($diemchinhtri < 5) {
                            $sheet->getStyle('J' . $index)->getFill()
                                ->setFillType(Fill::FILL_SOLID)
                                ->getStartColor()->setARGB($markedColor);
                        }

                        $sheet->setCellValue('K' . $index, $diemlythuyet == -1 ? '' : $diemlythuyet);
                        $sheet->getStyle('K' . $index)->applyFromArray($centerStyle)->getNumberFormat()->setFormatCode('0.0');

                        if ($diemlythuyet < 5) {
                            $sheet->getStyle('K' . $index)->getFill()
                                ->setFillType(Fill::FILL_SOLID)
                                ->getStartColor()->setARGB($markedColor);
                        }

                        $sheet->setCellValue('L' . $index, $diemthuchanh == -1 ? '' : $diemthuchanh);
                        $sheet->getStyle('L' . $index)->applyFromArray($centerStyle)->getNumberFormat()->setFormatCode('0.0');

                        if ($diemthuchanh < 5) {
                            $sheet->getStyle('L' . $index)->getFill()
                                ->setFillType(Fill::FILL_SOLID)
                                ->getStartColor()->setARGB($markedColor);
                        }
                    }
                }

                // Điểm TN
                if (isset($sv->toanKhoa->avg_totnghiep)) {
                    $sheet->getStyle('M' . $index)->getNumberFormat()->setFormatCode('0.0');
                    $sheet->setCellValue('M' . $index, $sv->toanKhoa->avg_totnghiep);
                    $sheet->getStyle($currentAddress)->applyFromArray($centerBorderStyle);
                }

                // XLTN
                $sheet->setCellValue('N' . $index, $sv->toanKhoa->hocLucTN);
                $sheet->getStyle($currentAddress)->applyFromArray($centerBorderStyle);


                // Ghi chú
                if ($sv->giamXltn == true) {
                    $sheet->setCellValue('O' . $index, "Giảm một mức XLTN");
                    $sheet->getStyle($currentAddress)->applyFromArray($centerBorderStyle);
                }


                // if ($nienche != 0) {
                //     $sheet->setCellValue('H' . $index, $diemlythuyet == -1 ? '' : $diemlythuyet);
                //     $sheet->getStyle('H' . $index)->applyFromArray($centerStyle);
                //     if ($diemlythuyet < 5) {
                //         $sheet->getStyle('H' . $index)->getFill()
                //             ->setFillType(Fill::FILL_SOLID)
                //             ->getStartColor()->setARGB($markedColor);
                //         $sheet->setCellValue('J' . $index, 'X');
                //         $sheet->getStyle('J' . $index)->applyFromArray($centerStyle);

                //         $ghiChu .= 'ĐTNLT(L1:' . number_format($sv->lythuyetlan1, 1);
                //         if ($sv->lythuyetlan2 != -1) {
                //             $ghiChu .= ', L2:' . number_format($sv->lythuyetlan2, 1);
                //         }
                //         if ($sv->lythuyetlan3 != -1) {
                //             $ghiChu .= ', L3:' . number_format($sv->lythuyetlan3, 1);
                //         }
                //         $ghiChu .= ')';
                //     }

                //     $sheet->setCellValue('I' . $index, $diemthuchanh == -1 ? '' : $diemthuchanh);
                //     $sheet->getStyle('I' . $index)->applyFromArray($centerStyle);
                //     if ($diemthuchanh < 5) {
                //         $sheet->getStyle('I' . $index)->getFill()
                //             ->setFillType(Fill::FILL_SOLID)
                //             ->getStartColor()->setARGB($markedColor);
                //         $sheet->setCellValue('K' . $index, 'X');
                //         $sheet->getStyle('K' . $index)->applyFromArray($centerStyle);
                //         if ($ghiChu != '') {
                //             $ghiChu .= ',';
                //         }
                //         $ghiChu .= ' ĐTNTH(L1:' . number_format($sv->thuchanhlan1, 1);
                //         if ($sv->thuchanhlan2 != -1) {
                //             $ghiChu .= ', L2:' . number_format($sv->thuchanhlan2, 1);
                //         }
                //         if ($sv->thuchanhlan3 != -1) {
                //             $ghiChu .= ', L3:' . number_format($sv->thuchanhlan3, 1);
                //         }
                //         $ghiChu .= ')';
                //     }

                //     $sheet->getColumnDimension("L")->setAutoSize(true);
                //     $sheet->setCellValue('L' . $index, $ghiChu);
                //     $sheet->getStyle('L' . $index)->applyFromArray($normalStyle);
                // } else {
                //     $sheet->setCellValue('I' . $index, $diemlythuyet == -1 ? '' : $diemlythuyet);
                //     $sheet->getStyle('I' . $index)->applyFromArray($centerStyle);
                //     if ($diemlythuyet < 5) {
                //         $sheet->getStyle('I' . $index)->getFill()
                //             ->setFillType(Fill::FILL_SOLID)
                //             ->getStartColor()->setARGB($markedColor);
                //         $sheet->setCellValue('L' . $index, 'X');
                //         $sheet->getStyle('L' . $index)->applyFromArray($centerStyle);

                //         $ghiChu .= 'ĐTNLT(L1:' . number_format($sv->lythuyetlan1, 1);
                //         if ($sv->lythuyetlan2 != -1) {
                //             $ghiChu .= ', L2:' . number_format($sv->lythuyetlan2, 1);
                //         }
                //         if ($sv->lythuyetlan3 != -1) {
                //             $ghiChu .= ', L3:' . number_format($sv->lythuyetlan3, 1);
                //         }
                //         $ghiChu .= ')';
                //     }

                //     $sheet->setCellValue('J' . $index, $diemthuchanh == -1 ? '' : $diemthuchanh);
                //     $sheet->getStyle('J' . $index)->applyFromArray($centerStyle);
                //     if ($diemthuchanh < 5) {
                //         $sheet->getStyle('J' . $index)->getFill()
                //             ->setFillType(Fill::FILL_SOLID)
                //             ->getStartColor()->setARGB($markedColor);
                //         $sheet->setCellValue('M' . $index, 'X');
                //         $sheet->getStyle('M' . $index)->applyFromArray($centerStyle);
                //         if ($ghiChu != '') {
                //             $ghiChu .= ',';
                //         }
                //         $ghiChu .= ' ĐTNTH(L1:' . number_format($sv->thuchanhlan1, 1);
                //         if ($sv->thuchanhlan2 != -1) {
                //             $ghiChu .= ', L2:' . number_format($sv->thuchanhlan2, 1);
                //         }
                //         if ($sv->thuchanhlan3 != -1) {
                //             $ghiChu .= ', L3:' . number_format($sv->thuchanhlan3, 1);
                //         }
                //         $ghiChu .= ')';
                //     }

                //     $sheet->setCellValue('H' . $index, $diemchinhtri == -1 ? '' : $diemchinhtri);
                //     $sheet->getStyle('H' . $index)->applyFromArray($centerStyle);
                //     if ($diemchinhtri < 5) {
                //         $sheet->getStyle('H' . $index)->getFill()
                //             ->setFillType(Fill::FILL_SOLID)
                //             ->getStartColor()->setARGB($markedColor);
                //         $sheet->setCellValue('K' . $index, 'X');
                //         $sheet->getStyle('K' . $index)->applyFromArray($centerStyle);
                //         if ($ghiChu != '') {
                //             $ghiChu .= ',';
                //         }
                //         $ghiChu .= ' ĐTNCT(L1:' . number_format($sv->chinhtrilan1, 1);
                //         if ($sv->thuchanhlan2 != -1) {
                //             $ghiChu .= ', L2:' . number_format($sv->chinhtrilan2, 1);
                //         }
                //         if ($sv->thuchanhlan3 != -1) {
                //             $ghiChu .= ', L3:' . number_format($sv->chinhtrilan3, 1);
                //         }
                //         $ghiChu .= ')';
                //     }

                //     $sheet->getColumnDimension("N")->setAutoSize(true);
                //     $sheet->setCellValue('N' . $index, $ghiChu);
                //     $sheet->getStyle('N' . $index)->applyFromArray($normalStyle);
                // }

                $index++;
            }

            // Sau khi kết thúc vòng lặp $nn->danhsachsinhvien, thêm một hàng trống
            $sheet->insertNewRowBefore($this->currentRow('A' . $index), 1);
            $sheet->getStyle('A' . $index)->applyFromArray($boldLeftStyle);
            $index++;


            $thongKeHocLucTemp = $nn->danhsachsinhvien->countBy(function ($sinhVien) {
                if (isset($sinhVien->toanKhoa) && isset($sinhVien->toanKhoa->hocLucTN)) {
                    return $sinhVien->toanKhoa->hocLucTN;
                };
                return 'NA';
            });


            foreach ($thongKeHocLucTemp as $key => $value) {
                if ($key == 'Xuất sắc') {
                    $thongKeHocLuc->put('Xuất sắc', $thongKeHocLuc['Xuất sắc'] + $value);
                }
                if ($key == 'Giỏi') {
                    $thongKeHocLuc->put('Giỏi', $thongKeHocLuc['Giỏi'] + $value);
                }
                if ($key == 'Khá') {
                    $thongKeHocLuc->put('Khá', $thongKeHocLuc['Khá'] + $value);
                }
                if ($key == 'Trung bình khá') {
                    $thongKeHocLuc->put('Trung bình khá', $thongKeHocLuc['Trung bình khá'] + $value);
                }
                if ($key == 'Trung bình') {
                    $thongKeHocLuc->put('Trung bình', $thongKeHocLuc['Trung bình'] + $value);
                }

                if ($key == 'Rớt' || $key = 'NA') {
                    $khongXepLoai = (isset($thongKeHocLuc['NA']) ? $thongKeHocLuc['NA'] : 0) + (isset($thongKeHocLuc['Rớt']) ? $thongKeHocLuc['Rớt'] : 0);
                    $soHocSinhXepLoai = $soHocSinhXepLoai - $khongXepLoai;
                }
            }
        }


        if (isset($thongKeHocLuc['Xuất sắc']) && $thongKeHocLuc['Xuất sắc'] > 0) {
            $thongKeMessage[] = 'Xuất sắc: ' . $thongKeHocLuc['Xuất sắc'] . ' ' . ($nganhnghe->hdt_id == 4 ? 'SV' : 'HS');
        }
        if (isset($thongKeHocLuc['Giỏi']) && $thongKeHocLuc['Giỏi'] > 0) {
            $thongKeMessage[] = 'Giỏi: ' . $thongKeHocLuc['Giỏi'] . ' ' . ($nganhnghe->hdt_id == 4 ? 'SV' : 'HS');
        }
        if (isset($thongKeHocLuc['Khá']) && $thongKeHocLuc['Khá'] > 0) {
            $thongKeMessage[] = 'Khá: ' . $thongKeHocLuc['Khá'] . ' ' . ($nganhnghe->hdt_id == 4 ? 'SV' : 'HS');
        }
        if (isset($thongKeHocLuc['Trung bình khá']) && $thongKeHocLuc['Trung bình khá'] > 0) {
            $thongKeMessage[] = 'Trung bình khá: ' . $thongKeHocLuc['Trung bình khá'] . ' ' . ($nganhnghe->hdt_id == 4 ? 'SV' : 'HS');
        }
        if (isset($thongKeHocLuc['Trung bình']) && $thongKeHocLuc['Trung bình'] > 0) {
            $thongKeMessage[] = 'Trung bình: ' . $thongKeHocLuc['Trung bình'] . ' ' . ($nganhnghe->hdt_id == 4 ? 'SV' : 'HS');
        }

        if ((isset($thongKeHocLuc['Rớt']) && isset($thongKeHocLuc['Rớt']) > 0) || isset($thongKeHocLuc['NA'])) {
            $khongXepLoai = (isset($thongKeHocLuc['NA']) ? $thongKeHocLuc['NA'] : 0) + (isset($thongKeHocLuc['Rớt']) ? $thongKeHocLuc['Rớt'] : 0);
            $soHocSinhXepLoai = $soHocSinhXepLoai - $khongXepLoai;
        }

        $sheet->setCellValue('A7', mb_strtoupper('ĐỢT XÉT: ' . $dotThi->dotXetTotNghiep[0]->dxtn_ten, 'UTF-8'));
        if ($dotThi->dotXetTotNghiep[0]->qd_id != null) {
            $ngayQD = Carbon::parse($dotThi->dotXetTotNghiep[0]->qd_ngay)->format('d \t\h\á\n\g m \t\h\á\n\g Y');
            $formatQD = "(Theo QĐ số: {$dotThi->dotXetTotNghiep[0]->qd_ma}, ngày {$ngayQD} của Hiệu trưởng TCĐCĐ & NN Nam Bộ)";
            $sheet->setCellValue('A8', $formatQD);
            $sheet->getStyle('A8')->applyFromArray($centerStyle);
        }

        if ($nganhnghe->hdt_id == 5) {
            $sheet->setCellValue('A5', 'DANH SÁCH HỌC SINH TỐT NGHIỆP');
            $sheet->setCellValue('A6', 'TRÌNH ĐỘ: TRUNG CẤP - HỆ: ' . mb_strtoupper($nganhnghe->kdt_he, 'UTF-8'));
            $sheet->setCellValue('B10', 'MSHS');
        } else {
            $sheet->setCellValue('A5', 'DANH SÁCH SINH VIÊN TỐT NGHIỆP');
            $sheet->setCellValue('A6', 'TRÌNH ĐỘ: CAO ĐẲNG - HỆ: ' . mb_strtoupper($nganhnghe->kdt_he, 'UTF-8'));
            $sheet->setCellValue('B10', 'MSSV');
        }
        // Tổng số Hs
        $sheet->setCellValue('A' . ($index + 1), 'Tổng số: ' . $sohocsinh . ' HSSV');


        $exportMessage = 'Trong đó: ';
        $exportMessage .= join('; ', $thongKeMessage) . '.';
        $sheet->setCellValue('A' . ($index + 2), $exportMessage);
        $currentAddress = $this->nextRowAddress($currentAddress, true);


        $sheet->getStyle('A' . $index)->applyFromArray($boldLeftStyle);


        $result->spreadsheet = $spreadsheet;
        $result->fileName = mb_strtoupper('DS' . ($nganhnghe->hdt_id == 5 ? 'HS' : 'SV') . 'ĐTN - ĐỢT XÉT: ' . $dotThi->dotXetTotNghiep[0]->dxtn_ten, 'UTF-8');
        return $result;
    }

    public function createSheetKetQuaThiKhongDatTN($danhSachNganhNgheSinhVien)
    {
        $result = new \stdClass;
        $spreadsheet = new Spreadsheet();
        // dd($danhSachNganhNgheSinhVien[0]->danhsachsinhvien[0]->lopHoc);
        $dotThi = DotThi::with('dotXetTotNghiep')->where('qlsv_dotthi.dt_id', '=', $danhSachNganhNgheSinhVien[0]->dt_id)->first();

        $nienche = $danhSachNganhNgheSinhVien[0]->danhsachsinhvien[0]->lh_nienche;
        $nganhnghe = $danhSachNganhNgheSinhVien[0];

        if ($nienche != 0) {
            $spreadsheet = IOFactory::load(storage_path($this->template_diemthichuadattotnghieptheolop2022));
        } else {
            $spreadsheet = IOFactory::load(storage_path($this->template_diemthichuadattotnghieptheolop2020));
        }

        $spreadsheet->getDefaultStyle()->getFont()->setName('Times New Roman');
        $spreadsheet->getDefaultStyle()->getFont()->setSize(13);

        $color = [
            'red' => '9C0006',
            'default' => '000000'
        ];

        $centerBorderStyle = [
            'font' => [
                'bold' => false,
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ]
        ];

        $centerStyle = [
            'font' => [
                'bold' => false,
                'size' => 13,
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => [
                    'argb' => 'FFFFFFFF',
                ],
            ]
        ];

        $normalStyle = [
            'font' => [
                'bold' => false,
                'size' => 13,
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ]
        ];

        $boldStyle = [
            'font' => [
                'bold' => true,
            ]
        ];

        $boldCenterStyle = [
            'font' => [
                'bold' => true,
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ]
        ];


        $boldLeftStyle = [
            'font' => [
                'bold' => true,
                'size' => 13,
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
            ]
        ];

        $markedColor = '00a65a';
        $sheet = $spreadsheet->getActiveSheet();
        $currentAddress = 'A10';
        $index = 11;
        $sohocsinh = 0;

        //$firstNoteRow = $this->currentRow($currentAddress);
        foreach ($danhSachNganhNgheSinhVien as $nnIndex => $nn) {
            // sort avg giảm dần
            $danhsachsinhvien = $nn->danhsachsinhvien->sortBy([
                function ($a, $b) {
                    if (isset($a->toanKhoa->avg) && isset($b->toanKhoa->avg)) {
                        return $b->toanKhoa->avg <=> $a->toanKhoa->avg;
                    } else if (isset($b->toanKhoa->avg)) {
                        return 1;
                    } else if (isset($a->toanKhoa->avg)) {
                        return -1;
                    }
                    return 0;
                },
                function ($a, $b) {
                    return $a->sv_ma <=> $b->sv_ma;
                }
            ]);

            $sheet->insertNewRowBefore($this->currentRow('A' . $index), 1);
            if ($nienche != 0) {
                $sheet->mergeCells('A' . $index . ':L' . $index);
            } else {
                $sheet->mergeCells('A' . $index . ':M' . $index);
            }

            $sheet->setCellValue('A' . $index, ($nnIndex + 1) . '. Ngành/nghề ' . $nn->nn_ten);
            $sheet->getStyle('A' . $index)->applyFromArray($boldLeftStyle);
            $index++;

            foreach ($danhsachsinhvien as $svIndex => $sv) {
                $diemdotthi = DB::table('qlsv_dotthi_diem as d')
                    ->join('qlsv_dotthi_bangdiem as bd', 'bd.dt_bd_id', '=', 'd.dt_bd_id')
                    ->join('qlsv_monhoc as mh', 'mh.mh_id', '=', 'bd.mh_id')
                    ->where('d.sv_id', $sv->sv_id)
                    ->where('bd.lh_id', $sv->lh_id)
                    ->where('bd.dt_id', $dotThi->dt_id)
                    ->select('d.svd_first', 'd.svd_lan', 'bd.mh_id', 'mh.mh_loai', 'd.svd_loai', 'bd.dt_id')
                    ->get();
                $sv->thuchanhlan1 = -1;
                $sv->lythuyetlan1 = -1;
                $sv->chinhtrilan1 = -1;
                $sv->khoaluanlan1 = -1;

                $sv->thuchanhlan2 = -1;
                $sv->lythuyetlan2 = -1;
                $sv->chinhtrilan2 = -1;
                $sv->khoaluanlan2 = -1;

                $sv->thuchanhlan3 = -1;
                $sv->lythuyetlan3 = -1;
                $sv->chinhtrilan3 = -1;
                $sv->khoaluanlan3 = -1;


                foreach ($diemdotthi as $ddt) {
                    $sv->svd_loai = $ddt->svd_loai;
                    if ($ddt->svd_lan == 1) {
                        if ($ddt->mh_loai == 2) {
                            $sv->chinhtrilan1 = $ddt->svd_first;
                        } else if ($ddt->mh_loai == 3) {
                            $sv->thuchanhlan1 = $ddt->svd_first;
                        } else if ($ddt->mh_loai == 4) {
                            $sv->lythuyetlan1 = $ddt->svd_first;
                        } else if ($ddt->mh_loai == 5) {
                            $sv->khoaluanlan1 = $ddt->svd_first;
                        }
                        $sv->dtidlan1 = $ddt->dt_id;
                    } else if ($ddt->svd_lan == 2) {
                        if ($ddt->mh_loai == 2) {
                            $sv->chinhtrilan2 = $ddt->svd_first;
                        } else if ($ddt->mh_loai == 3) {
                            $sv->thuchanhlan2 = $ddt->svd_first;
                        } else if ($ddt->mh_loai == 4) {
                            $sv->lythuyetlan2 = $ddt->svd_first;
                        } else if ($ddt->mh_loai == 5) {
                            $sv->khoaluanlan2 = $ddt->svd_first;
                        }
                        $sv->dtidlan2 = $ddt->dt_id;
                    } else if ($ddt->svd_lan == 3) {
                        if ($ddt->mh_loai == 2) {
                            $sv->chinhtrilan3 = $ddt->svd_first;
                        } else if ($ddt->mh_loai == 3) {
                            $sv->thuchanhlan3 = $ddt->svd_first;
                        } else if ($ddt->mh_loai == 4) {
                            $sv->lythuyetlan3 = $ddt->svd_first;
                        } else if ($ddt->mh_loai == 5) {
                            $sv->khoaluanlan3 = $ddt->svd_first;
                        }
                        $sv->dtidlan3 = $ddt->dt_id;
                    }
                }

                $diemlythuyet = $sv->lythuyetlan1;
                $diemthuchanh = $sv->thuchanhlan1;
                $diemkhoaluan = $sv->khoaluanlan1;
                $diemchinhtri = $sv->chinhtrilan1;

                if ($sv->khoaluanlan2 != -1 && $sv->dtidlan2 == $nn->dt_id) {
                    $diemkhoaluan = $sv->khoaluanlan2;
                } else if ($sv->khoaluanlan3 != -1 && $sv->dtidlan3 == $nn->dt_id) {
                    $diemkhoaluan = $sv->khoaluanlan3;
                }

                if ($sv->thuchanhlan2 != -1 && $sv->dtidlan2 == $nn->dt_id) {
                    $diemthuchanh = $sv->thuchanhlan2;
                } else if ($sv->thuchanhlan3 != -1 && $sv->dtidlan3 == $nn->dt_id) {
                    $diemthuchanh = $sv->thuchanhlan3;
                }

                if ($sv->lythuyetlan2 != -1 && $sv->dtidlan2 == $nn->dt_id) {
                    $diemlythuyet = $sv->lythuyetlan2;
                } else if ($sv->lythuyetlan3 != -1 && $sv->dtidlan3 == $nn->dt_id) {
                    $diemlythuyet = $sv->lythuyetlan3;
                }

                if ($sv->chinhtrilan2 != -1 && $sv->dtidlan2 == $nn->dt_id) {
                    $diemchinhtri = $sv->chinhtrilan2;
                } else if ($sv->chinhtrilan3 != -1 && $sv->dtidlan3 == $nn->dt_id) {
                    $diemchinhtri = $sv->chinhtrilan3;
                }

                // if ($diemchinhtri >= 5 && $diemlythuyet >= 5 && $diemthuchanh >= 5) {
                //     continue;
                // }

                $sheet->insertNewRowBefore($this->currentRow('A' . $index), 1);
                $sheet->setCellValue('A' . $index, ($svIndex + 1) . '. ');
                $sheet->getStyle('A' . $index)->applyFromArray($centerStyle);

                $sheet->setCellValue('B' . $index, $sv->sv_ma);
                $sheet->getStyle('B' . $index)->applyFromArray($centerStyle);

                $sheet->setCellValue('C' . $index, $sv->sv_ho);
                $sheet->getStyle('C' . $index)->applyFromArray($normalStyle);

                $sheet->setCellValue('D' . $index, $sv->sv_ten);
                $sheet->getStyle('D' . $index)->applyFromArray($normalStyle);

                if ($sv->sv_gioitinh == 1) {
                    $sheet->setCellValue('E' . $index, $sv->sv_ngaysinh);
                    $sheet->getStyle('E' . $index)->applyFromArray($normalStyle);
                } else {
                    $sheet->setCellValue('F' . $index, $sv->sv_ngaysinh);
                    $sheet->getStyle('F' . $index)->applyFromArray($normalStyle);
                }

                $sheet->setCellValue('G' . $index, $sv->lh_ten . ' - K' . $sv->kdt_khoa);
                $sheet->getStyle('G' . $index)->applyFromArray($centerBorderStyle);

                $sohocsinh++;

                $ghiChu = "";

                // Điểm TB_tk
                if (isset($sv->toanKhoa->avg)) {
                    $sheet->setCellValue('H' . $index, $sv->toanKhoa->avg);
                    $sheet->getStyle('H' . $index)->applyFromArray($centerBorderStyle)->getNumberFormat()->setFormatCode('0.0');
                }

                // // Điểm đợt thi
                // // 1 là báo cáo luận văn, 0 là 3 môn

                // if ($sv->svd_loai == 1) {
                //     $currentAddress = 'I' . $index;
                //     if ($sv->khoaluanlan1 != -1) {
                //         $sheet->getStyle($currentAddress)->applyFromArray($centerStyle);
                //         $sheet->setCellValue($currentAddress, $sv->khoaluanlan1);
                //         if ($sv->khoaluanlan1 < 5) {
                //             $sheet->getStyle($currentAddress)->getFill()
                //                 ->setFillType(Fill::FILL_SOLID)
                //                 ->getStartColor()->setARGB($markedColor);
                //         }
                //     }
                //     $currentAddress = $this->nextColAddress($currentAddress);

                //     $currentAddress = 'I' . $index;
                //     if ($sv->khoaluanlan2 != -1) {
                //         $sheet->getStyle($currentAddress)->applyFromArray($centerStyle);
                //         $sheet->setCellValue($currentAddress, $sv->khoaluanlan2);
                //         if ($sv->khoaluanlan2 < 5) {
                //             $sheet->getStyle($currentAddress)->getFill()
                //                 ->setFillType(Fill::FILL_SOLID)
                //                 ->getStartColor()->setARGB($markedColor);
                //         }
                //     }
                //     $currentAddress = $this->nextColAddress($currentAddress);

                //     $currentAddress = 'I' . $index;
                //     if ($sv->khoaluanlan3 != -1) {
                //         $sheet->getStyle($currentAddress)->applyFromArray($centerStyle);
                //         $sheet->setCellValue($currentAddress, $sv->khoaluanlan3);
                //         if ($sv->khoaluanlan3 < 5) {
                //             $sheet->getStyle($currentAddress)->getFill()
                //                 ->setFillType(Fill::FILL_SOLID)
                //                 ->getStartColor()->setARGB($markedColor);
                //         }
                //     }
                //     $currentAddress = $this->nextColAddress($currentAddress);
                // }

                // if ($sv->lh_nienche == 0) {
                //     $currentAddress = 'J' . $index;
                //     if ($sv->chinhtrilan1 != -1) {
                //         $sheet->getStyle($currentAddress)->applyFromArray($centerStyle);
                //         $sheet->setCellValue($currentAddress, $sv->chinhtrilan1);
                //         if ($sv->chinhtrilan1 < 5) {
                //             $sheet->getStyle($currentAddress)->getFill()
                //                 ->setFillType(Fill::FILL_SOLID)
                //                 ->getStartColor()->setARGB($markedColor);
                //         }
                //     }
                //     $currentAddress = $this->nextColAddress($currentAddress);
                // }

                // $currentAddress = 'K' . $index;
                // if ($sv->lythuyetlan1 != -1) {
                //     $sheet->getStyle($currentAddress)->applyFromArray($centerStyle);
                //     $sheet->setCellValue($currentAddress, $sv->lythuyetlan1);
                //     if ($sv->lythuyetlan1 < 5) {
                //         $sheet->getStyle($currentAddress)->getFill()
                //             ->setFillType(Fill::FILL_SOLID)
                //             ->getStartColor()->setARGB($markedColor);
                //     }
                // }
                // $currentAddress = $this->nextColAddress($currentAddress);


                // $currentAddress = 'L' . $index;
                // if ($sv->thuchanhlan1 != -1) {
                //     $sheet->getStyle($currentAddress)->applyFromArray($centerStyle);
                //     $sheet->setCellValue($currentAddress, $sv->thuchanhlan1);
                //     if ($sv->thuchanhlan1 < 5) {
                //         $sheet->getStyle($currentAddress)->getFill()
                //             ->setFillType(Fill::FILL_SOLID)
                //             ->getStartColor()->setARGB($markedColor);
                //     }
                // }
                // $currentAddress = $this->nextColAddress($currentAddress);

                // if ($sv->lh_nienche == 0) {
                //     $currentAddress = 'J' . $index;
                //     if ($sv->chinhtrilan2 != -1) {
                //         $sheet->getStyle($currentAddress)->applyFromArray($centerStyle);
                //         $sheet->setCellValue($currentAddress, $sv->chinhtrilan2);
                //         if ($sv->chinhtrilan2 < 5) {
                //             $sheet->getStyle($currentAddress)->getFill()
                //                 ->setFillType(Fill::FILL_SOLID)
                //                 ->getStartColor()->setARGB($markedColor);
                //         }
                //     }
                //     $currentAddress = $this->nextColAddress($currentAddress);
                // }

                // $currentAddress = 'K' . $index;
                // if ($sv->lythuyetlan2 != -1) {
                //     $sheet->getStyle($currentAddress)->applyFromArray($centerStyle);
                //     $sheet->setCellValue($currentAddress, $sv->lythuyetlan2);
                //     if ($sv->lythuyetlan2 < 5) {
                //         $sheet->getStyle($currentAddress)->getFill()
                //             ->setFillType(Fill::FILL_SOLID)
                //             ->getStartColor()->setARGB($markedColor);
                //     }
                // }
                // $currentAddress = $this->nextColAddress($currentAddress);

                // $currentAddress = 'L' . $index;
                // if ($sv->thuchanhlan2 != -1) {
                //     $sheet->getStyle($currentAddress)->applyFromArray($centerStyle);
                //     $sheet->setCellValue($currentAddress, $sv->thuchanhlan2);
                //     if ($sv->thuchanhlan2 < 5) {
                //         $sheet->getStyle($currentAddress)->getFill()
                //             ->setFillType(Fill::FILL_SOLID)
                //             ->getStartColor()->setARGB($markedColor);
                //     }
                // }
                // $currentAddress = $this->nextColAddress($currentAddress);

                // if ($sv->lh_nienche == 0) {
                //     $currentAddress = 'J' . $index;
                //     if ($sv->chinhtrilan3 != -1) {
                //         $sheet->getStyle($currentAddress)->applyFromArray($centerStyle);
                //         $sheet->setCellValue($currentAddress, $sv->chinhtrilan3);
                //         if ($sv->chinhtrilan3 < 5) {
                //             $sheet->getStyle($currentAddress)->getFill()
                //                 ->setFillType(Fill::FILL_SOLID)
                //                 ->getStartColor()->setARGB($markedColor);
                //         }
                //     }
                //     $currentAddress = $this->nextColAddress($currentAddress);
                // }

                // $currentAddress = 'K' . $index;
                // if ($sv->lythuyetlan3 != -1) {
                //     $sheet->getStyle($currentAddress)->applyFromArray($centerStyle);
                //     $sheet->setCellValue($currentAddress, $sv->lythuyetlan3);
                //     if ($sv->lythuyetlan3 < 5) {
                //         $sheet->getStyle($currentAddress)->getFill()
                //             ->setFillType(Fill::FILL_SOLID)
                //             ->getStartColor()->setARGB($markedColor);
                //     }
                // }
                // $currentAddress = $this->nextColAddress($currentAddress);

                // $currentAddress = 'L' . $index;
                // if ($sv->thuchanhlan3 != -1) {
                //     $sheet->getStyle($currentAddress)->applyFromArray($centerStyle);
                //     $sheet->setCellValue($currentAddress, $sv->thuchanhlan3);
                //     if ($sv->thuchanhlan3 < 5) {
                //         $sheet->getStyle($currentAddress)->getFill()
                //             ->setFillType(Fill::FILL_SOLID)
                //             ->getStartColor()->setARGB($markedColor);
                //     }
                // }
                // $currentAddress = $this->nextColAddress($currentAddress);

                $sheet->setCellValue('I' . $index, $diemkhoaluan == -1 ? '' : $diemkhoaluan);
                $sheet->getStyle('I' . $index)->applyFromArray($centerStyle)->getNumberFormat()->setFormatCode('0.0');
                $sheet->getStyle('I' . $index)->getFont()->getColor()->setRGB($color["default"]);

                if ($sv->svd_loai == 1) {
                    if ($diemkhoaluan < 5) {
                        $cellStyle = $sheet->getStyle('I' . $index);
                        $cellStyle->getFill()
                            ->setFillType(Fill::FILL_SOLID)
                            ->getStartColor()->setARGB($markedColor);
                        // $cellStyle->getFont()->getColor()->setRGB($color["red"]);

                        $ghiChu .= 'ĐTNCĐ(L1:' . number_format($sv->khoaluanlan1, 1);
                        if ($sv->lythuyetlan2 != -1) {
                            $ghiChu .= ', L2:' . number_format($sv->khoaluanlan2, 1);
                        }
                        if ($sv->lythuyetlan3 != -1) {
                            $ghiChu .= ', L3:' . number_format($sv->khoaluanlan3, 1);
                        }
                        $ghiChu .= ')';
                    }
                } else {
                    if ($nienche != 0) {
                        $sheet->setCellValue('H' . $index, $diemlythuyet == -1 ? '' : $diemlythuyet);
                        $sheet->getStyle('H' . $index)->applyFromArray($centerStyle)->getNumberFormat()->setFormatCode('0.0');
                        $sheet->getStyle('H' . $index)->getFont()->getColor()->setRGB($color["default"]);

                        if ($diemlythuyet < 5) {
                            $cellStyle = $sheet->getStyle('H' . $index);
                            $cellStyle->getFill()
                                ->setFillType(Fill::FILL_SOLID)
                                ->getStartColor()->setARGB($markedColor);
                            // $cellStyle->getFont()->getColor()->setRGB($color["red"]);

                            $ghiChu .= 'ĐTNLT(L1:' . number_format($sv->lythuyetlan1, 1);
                            if ($sv->lythuyetlan2 != -1) {
                                $ghiChu .= ', L2:' . number_format($sv->lythuyetlan2, 1);
                            }
                            if ($sv->lythuyetlan3 != -1) {
                                $ghiChu .= ', L3:' . number_format($sv->lythuyetlan3, 1);
                            }
                            $ghiChu .= ')';
                        }

                        $sheet->setCellValue('I' . $index, $diemthuchanh == -1 ? '' : $diemthuchanh);
                        $sheet->getStyle('I' . $index)->applyFromArray($centerStyle)->getNumberFormat()->setFormatCode('0.0');
                        $sheet->getStyle('I' . $index)->getFont()->getColor()->setRGB($color["default"]);

                        if ($diemthuchanh < 5) {
                            $cellStyle = $sheet->getStyle('I' . $index);
                            $cellStyle->getFill()
                                ->setFillType(Fill::FILL_SOLID)
                                ->getStartColor()->setARGB($markedColor);
                            // $cellStyle->getFont()->getColor()->setRGB($color["red"]);

                            if ($ghiChu != '') {
                                $ghiChu .= ',';
                            }
                            $ghiChu .= ' ĐTNTH(L1:' . number_format($sv->thuchanhlan1, 1);
                            if ($sv->thuchanhlan2 != -1) {
                                $ghiChu .= ', L2:' . number_format($sv->thuchanhlan2, 1);
                            }
                            if ($sv->thuchanhlan3 != -1) {
                                $ghiChu .= ', L3:' . number_format($sv->thuchanhlan3, 1);
                            }
                            $ghiChu .= ')';
                        }

                        $sheet->getColumnDimension("L")->setAutoSize(true);
                        $sheet->setCellValue('L' . $index, $ghiChu);
                        $sheet->getStyle('L' . $index)->applyFromArray($normalStyle);
                    } else {
                        if ($sv->svd_loai == 0) {
                            $sheet->setCellValue('J' . $index, $diemchinhtri == -1 ? '' : $diemchinhtri);
                            $sheet->getStyle('J' . $index)->applyFromArray($centerStyle)->getNumberFormat()->setFormatCode('0.0');
                            $sheet->getStyle('J' . $index)->getFont()->getColor()->setRGB($color["default"]);

                            if ($diemchinhtri < 5) {
                                $cellStyle = $sheet->getStyle('J' . $index);
                                $cellStyle->getFill()
                                    ->setFillType(Fill::FILL_SOLID)
                                    ->getStartColor()->setARGB($markedColor);
                                // $cellStyle->getFont()->getColor()->setRGB($color["red"]);

                                $ghiChu .= 'ĐTNLT(L1:' . number_format($sv->chinhtrilan1, 1);
                                if ($sv->lythuyetlan2 != -1) {
                                    $ghiChu .= ', L2:' . number_format($sv->chinhtrilan2, 1);
                                }
                                if ($sv->lythuyetlan3 != -1) {
                                    $ghiChu .= ', L3:' . number_format($sv->chinhtrilan3, 1);
                                }
                                $ghiChu .= ')';
                            }

                            $sheet->setCellValue('K' . $index, $diemlythuyet == -1 ? '' : $diemlythuyet);
                            $sheet->getStyle('K' . $index)->applyFromArray($centerStyle)->getNumberFormat()->setFormatCode('0.0');
                            $sheet->getStyle('K' . $index)->getFont()->getColor()->setRGB($color["default"]);

                            if ($diemlythuyet < 5) {
                                $cellStyle = $sheet->getStyle('K' . $index);
                                $cellStyle->getFill()
                                    ->setFillType(Fill::FILL_SOLID)
                                    ->getStartColor()->setARGB($markedColor);
                                // $cellStyle->getFont()->getColor()->setRGB($color["red"]);

                                if ($ghiChu != '') {
                                    $ghiChu .= ',';
                                }
                                $ghiChu .= ' ĐTNTH(L1:' . number_format($sv->lythuyetlan1, 1);
                                if ($sv->thuchanhlan2 != -1) {
                                    $ghiChu .= ', L2:' . number_format($sv->lythuyetlan2, 1);
                                }
                                if ($sv->thuchanhlan3 != -1) {
                                    $ghiChu .= ', L3:' . number_format($sv->lythuyetlan3, 1);
                                }
                                $ghiChu .= ')';
                            }

                            $sheet->setCellValue('L' . $index, $diemthuchanh == -1 ? '' : $diemthuchanh);
                            $sheet->getStyle('L' . $index)->applyFromArray($centerStyle)->getNumberFormat()->setFormatCode('0.0');
                            $sheet->getStyle('L' . $index)->getFont()->getColor()->setRGB($color["default"]);

                            if ($diemthuchanh < 5) {
                                $cellStyle = $sheet->getStyle('L' . $index);
                                $cellStyle->getFill()
                                    ->setFillType(Fill::FILL_SOLID)
                                    ->getStartColor()->setARGB($markedColor);
                                // $cellStyle->getFont()->getColor()->setRGB($color["red"]);

                                if ($ghiChu != '') {
                                    $ghiChu .= ',';
                                }
                                $ghiChu .= ' ĐTNCT(L1:' . number_format($sv->thuchanhlan1, 1);
                                if ($sv->thuchanhlan2 != -1) {
                                    $ghiChu .= ', L2:' . number_format($sv->thuchanhlan2, 1);
                                }
                                if ($sv->thuchanhlan3 != -1) {
                                    $ghiChu .= ', L3:' . number_format($sv->thuchanhlan3, 1);
                                }
                                $ghiChu .= ')';
                            }
                        }


                        // Ghi chú
                        $ghiChuThiLai = rtrim($sv->toanKhoa->ghiChuThiLai, ', ') . '';

                        if ($nienche != 0) {
                            if ($sv->svxtn_vipham == 1) {
                                $sheet->setCellValue('L' . $index, ($ghiChuThiLai == "" ? $ghiChuThiLai : $ghiChuThiLai . ", ") . $sv->svxtn_ghichu);
                            } else {
                                $sheet->setCellValue('L' . $index, $ghiChuThiLai);
                            }
                            $sheet->getStyle($currentAddress)->applyFromArray($centerStyle);
                        } else {
                            if ($sv->svxtn_vipham == 1) {
                                $sheet->setCellValue('M' . $index, ($ghiChuThiLai == "" ? $ghiChuThiLai : $ghiChuThiLai . ", ") . $sv->svxtn_ghichu);
                            } else {
                                $sheet->setCellValue('M' . $index, $ghiChuThiLai);
                            }
                        }

                        $sheet->getStyle($currentAddress)->applyFromArray($centerStyle);

                        // $sheet->getColumnDimension("N")->setAutoSize(true);
                        // $sheet->setCellValue('N' . $index, $ghiChu);
                        // $sheet->getStyle('N' . $index)->applyFromArray($normalStyle);
                    }
                }

                $index++;
            }

            // Sau khi kết thúc vòng lặp $nn->danhsachsinhvien, thêm một hàng trống
            $sheet->insertNewRowBefore($this->currentRow('A' . $index), 1);
            $sheet->getStyle('A' . $index)->applyFromArray($boldLeftStyle);
            $index++;
        }

        $sheet->setCellValue('A7', mb_strtoupper('ĐỢT XÉT: ' . $dotThi->dotXetTotNghiep[0]->dxtn_ten, 'UTF-8'));

        if ($nganhnghe->hdt_id == 5) {
            $sheet->setCellValue('A5', 'DANH SÁCH HỌC SINH CHƯA ĐẠT TỐT NGHIỆP ');
            $sheet->setCellValue('A6', 'TRÌNH ĐỘ: TRUNG CẤP - HỆ: ' . mb_strtoupper($nganhnghe->kdt_he, 'UTF-8'));
            $sheet->setCellValue('B10', 'MSHS');
        } else {
            $sheet->setCellValue('A5', 'DANH SÁCH SINH VÊN CHƯA ĐẠT TỐT NGHIỆP ');
            $sheet->setCellValue('A6', 'TRÌNH ĐỘ: CAO ĐẲNG - HỆ: ' . mb_strtoupper($nganhnghe->kdt_he, 'UTF-8'));
            $sheet->setCellValue('B10', 'MSSV');
        }
        $sheet->setCellValue('A' . ($index + 1), 'Tổng số: ' . $sohocsinh . ' HSSV');

        $sheet->getStyle('A' . $index)->applyFromArray($boldLeftStyle);

        $result->spreadsheet = $spreadsheet;
        $result->fileName = mb_strtoupper('DS' . ($nganhnghe->hdt_id == 5 ? 'HS' : 'SV') . 'CĐTN - ĐỢT XÉT: ' . $dotThi->dotXetTotNghiep[0]->dxtn_ten, 'UTF-8');
        return $result;
    }


    public function createSheetSVDuDieuKienKhongDuDieuKien($danhSachNganhNgheSinhVien, $loai)
    {
        $result = new \stdClass;
        $spreadsheet = new Spreadsheet();
        $dotThi = DotThi::with('dotXetTotNghiep')->where('qlsv_dotthi.dt_id', '=', $danhSachNganhNgheSinhVien[0]->dt_id)->first();

        $nienche = $danhSachNganhNgheSinhVien[0]->danhsachsinhvien[0]->lh_nienche;
        $nganhnghe = $danhSachNganhNgheSinhVien[0];
        $spreadsheet = IOFactory::load(storage_path($this->template_dssinhviendudieukienthitn));

        $spreadsheet->getDefaultStyle()->getFont()->setName('Times New Roman');
        $spreadsheet->getDefaultStyle()->getFont()->setSize(13);

        $centerBorderStyle = [
            'font' => [
                'bold' => false,
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ]
        ];

        $centerStyle = [
            'font' => [
                'bold' => false,
                'size' => 13,
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => [
                    'argb' => 'FFFFFFFF',
                ],
            ]
        ];

        $normalStyle = [
            'font' => [
                'bold' => false,
                'size' => 13,
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ]
        ];

        $boldStyle = [
            'font' => [
                'bold' => true,
            ]
        ];

        $italicStyle = [
            'font' => [
                'italic' => true,
                'size' => 11,
            ]
        ];

        $boldCenterStyle = [
            'font' => [
                'bold' => true,
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ]
        ];


        $boldLeftStyle = [
            'font' => [
                'bold' => true,
                'size' => 13,
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
            ]
        ];

        $boldCenterStyle2 = [
            'font' => [
                'bold' => true,
                'size' => 13,
                'name' => 'Times New Roman',
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ]
        ];

        $markedColor = 'FFC4CC';
        $sheet = $spreadsheet->getActiveSheet();
        $currentAddress = 'A12';
        $index = 12;
        $sohocsinh = 0;

        //$firstNoteRow = $this->currentRow($currentAddress);
        // dd($danhSachNganhNgheSinhVien);
        foreach ($danhSachNganhNgheSinhVien as $nnIndex => $nn) {

            $sheet->insertNewRowBefore($this->currentRow('A' . $index), 1);
            $sheet->mergeCells('A' . $index . ':H' . $index);
            $sheet->setCellValue('A' . $index, ($nnIndex + 1) . '. Ngành/Nghề ' . $nn->nn_ten);
            $sheet->getStyle('A' . $index)->applyFromArray($boldLeftStyle);
            $index++;

            $ghiChuSVCoDiemTkChuaDat = [];
            $sinhVienIndex = 1;
            foreach ($nn->danhsachsinhvien as $svIndex => $sv) {
                $diemdotthi = DB::table('qlsv_dotthi_diem as d')
                    ->join('qlsv_dotthi_bangdiem as bd', 'bd.dt_bd_id', '=', 'd.dt_bd_id')
                    ->join('qlsv_monhoc as mh', 'mh.mh_id', '=', 'bd.mh_id')
                    ->where('d.sv_id', $sv->sv_id)
                    ->where('bd.lh_id', $sv->lh_id)
                    ->select('d.svd_first', 'd.svd_lan', 'bd.mh_id', 'mh.mh_loai', 'd.svd_loai', 'bd.dt_id', 'd.svd_ghichu')
                    ->get();

                $sv->thuchanhlan1 = -1;
                $sv->lythuyetlan1 = -1;
                $sv->chinhtrilan1 = -1;
                $sv->khoaluanlan1 = -1;

                $sv->thuchanhlan2 = -1;
                $sv->lythuyetlan2 = -1;
                $sv->chinhtrilan2 = -1;
                $sv->khoaluanlan2 = -1;

                $sv->thuchanhlan3 = -1;
                $sv->lythuyetlan3 = -1;
                $sv->chinhtrilan3 = -1;
                $sv->khoaluanlan3 = -1;

                foreach ($diemdotthi as $ddt) {
                    $sv->svd_loai = $ddt->svd_loai;
                    if ($ddt->svd_lan == 1) {
                        if ($ddt->mh_loai == 2) {
                            $sv->chinhtrilan1 = $ddt->svd_first;
                        } else if ($ddt->mh_loai == 3) {
                            $sv->thuchanhlan1 = $ddt->svd_first;
                        } else if ($ddt->mh_loai == 4) {
                            $sv->lythuyetlan1 = $ddt->svd_first;
                        } else if ($ddt->mh_loai == 5) {
                            $sv->khoaluanlan1 = $ddt->svd_first;
                        }
                        $sv->dtidlan1 = $ddt->dt_id;
                    } else if ($ddt->svd_lan == 2) {
                        if ($ddt->mh_loai == 2) {
                            $sv->chinhtrilan2 = $ddt->svd_first;
                        } else if ($ddt->mh_loai == 3) {
                            $sv->thuchanhlan2 = $ddt->svd_first;
                        } else if ($ddt->mh_loai == 4) {
                            $sv->lythuyetlan2 = $ddt->svd_first;
                        } else if ($ddt->mh_loai == 5) {
                            $sv->khoaluanlan2 = $ddt->svd_first;
                        }
                        $sv->dtidlan2 = $ddt->dt_id;
                    } else if ($ddt->svd_lan == 3) {
                        if ($ddt->mh_loai == 2) {
                            $sv->chinhtrilan3 = $ddt->svd_first;
                        } else if ($ddt->mh_loai == 3) {
                            $sv->thuchanhlan3 = $ddt->svd_first;
                        } else if ($ddt->mh_loai == 4) {
                            $sv->lythuyetlan3 = $ddt->svd_first;
                        } else if ($ddt->mh_loai == 5) {
                            $sv->khoaluanlan3 = $ddt->svd_first;
                        }
                        $sv->dtidlan3 = $ddt->dt_id;
                    }
                }

                $diemlythuyet = $sv->lythuyetlan1;
                $diemthuchanh = $sv->thuchanhlan1;
                $diemkhoaluan = $sv->khoaluanlan1;
                $diemchinhtri = $sv->chinhtrilan1;

                if ($sv->khoaluanlan2 != -1 && $sv->dtidlan2 == $nn->dt_id) {
                    $diemkhoaluan = $sv->khoaluanlan2;
                } else if ($sv->khoaluanlan3 != -1 && $sv->dtidlan3 == $nn->dt_id) {
                    $diemkhoaluan = $sv->khoaluanlan3;
                }

                if ($sv->thuchanhlan2 != -1 && $sv->dtidlan2 == $nn->dt_id) {
                    $diemthuchanh = $sv->thuchanhlan2;
                } else if ($sv->thuchanhlan3 != -1 && $sv->dtidlan3 == $nn->dt_id) {
                    $diemthuchanh = $sv->thuchanhlan3;
                }

                if ($sv->lythuyetlan2 != -1 && $sv->dtidlan2 == $nn->dt_id) {
                    $diemlythuyet = $sv->lythuyetlan2;
                } else if ($sv->lythuyetlan3 != -1 && $sv->dtidlan3 == $nn->dt_id) {
                    $diemlythuyet = $sv->lythuyetlan3;
                }

                if ($sv->chinhtrilan2 != -1 && $sv->dtidlan2 == $nn->dt_id) {
                    $diemchinhtri = $sv->chinhtrilan2;
                } else if ($sv->chinhtrilan3 != -1 && $sv->dtidlan3 == $nn->dt_id) {
                    $diemchinhtri = $sv->chinhtrilan3;
                }

                // if ($diemchinhtri >= 5 && $diemlythuyet >= 5 && $diemthuchanh >= 5) {
                //     continue;
                // }

                $sheet->insertNewRowBefore($this->currentRow('A' . $index), 1);
                $sheet->setCellValue('A' . $index, ($sinhVienIndex) . '. ');
                $sinhVienIndex++;
                $sheet->getStyle('A' . $index)->applyFromArray($centerStyle);

                $sheet->setCellValue('B' . $index, $sv->sv_ma);
                $sheet->getStyle('B' . $index)->applyFromArray($centerStyle);

                $sheet->setCellValue('C' . $index, $sv->sv_ho);
                $sheet->getStyle('C' . $index)->applyFromArray($normalStyle);

                $sheet->setCellValue('D' . $index, $sv->sv_ten);
                $sheet->getStyle('D' . $index)->applyFromArray($normalStyle);

                if ($sv->sv_gioitinh == 1) {
                    $sheet->setCellValue('E' . $index, $sv->sv_ngaysinh);
                    $sheet->getStyle('E' . $index)->applyFromArray($normalStyle);
                } else {
                    $sheet->setCellValue('F' . $index, $sv->sv_ngaysinh);
                    $sheet->getStyle('F' . $index)->applyFromArray($normalStyle);
                }

                $sheet->setCellValue('G' . $index, $sv->lh_ten . ' - K' . $sv->kdt_khoa);
                $sheet->getStyle('G' . $index)->applyFromArray($centerStyle);

                $sohocsinh++;

                $ghiChu = "";

                // if ($nienche != 0) {
                //     $sheet->setCellValue('H' . $index, $diemlythuyet == -1 ? '' : $diemlythuyet);
                //     $sheet->getStyle('H' . $index)->applyFromArray($centerStyle);
                //     if ($diemlythuyet < 5) {
                //         // $sheet->getStyle('H' . $index)->getFill()
                //         //     ->setFillType(Fill::FILL_SOLID)
                //         //     ->getStartColor()->setARGB($markedColor);
                //         // $sheet->setCellValue('J' . $index, 'X');
                //         // $sheet->getStyle('J' . $index)->applyFromArray($centerStyle);

                //         $ghiChu .= 'ĐTNLT(L1:' . number_format($sv->lythuyetlan1, 1);
                //         if ($sv->lythuyetlan2 != -1) {
                //             $ghiChu .= ', L2:' . number_format($sv->lythuyetlan2, 1);
                //         }
                //         if ($sv->lythuyetlan3 != -1) {
                //             $ghiChu .= ', L3:' . number_format($sv->lythuyetlan3, 1);
                //         }
                //         $ghiChu .= ')';
                //     }

                //     // $sheet->setCellValue('I' . $index, $diemthuchanh == -1 ? '' : $diemthuchanh);
                //     // $sheet->getStyle('I' . $index)->applyFromArray($centerStyle);
                //     if ($diemthuchanh < 5) {
                //         // $sheet->getStyle('I' . $index)->getFill()
                //         //     ->setFillType(Fill::FILL_SOLID)
                //         //     ->getStartColor()->setARGB($markedColor);
                //         // $sheet->setCellValue('K' . $index, 'X');
                //         // $sheet->getStyle('K' . $index)->applyFromArray($centerStyle);
                //         if ($ghiChu != '') {
                //             $ghiChu .= ',';
                //         }
                //         $ghiChu .= ' ĐTNTH(L1:' . number_format($sv->thuchanhlan1, 1);
                //         if ($sv->thuchanhlan2 != -1) {
                //             $ghiChu .= ', L2:' . number_format($sv->thuchanhlan2, 1);
                //         }
                //         if ($sv->thuchanhlan3 != -1) {
                //             $ghiChu .= ', L3:' . number_format($sv->thuchanhlan3, 1);
                //         }
                //         $ghiChu .= ')';
                //     }

                //     // $sheet->getColumnDimension("L")->setAutoSize(true);
                //     // $sheet->setCellValue('L' . $index, $ghiChu);
                //     // $sheet->getStyle('L' . $index)->applyFromArray($normalStyle);
                // } else {
                //     // $sheet->setCellValue('I' . $index, $diemlythuyet == -1 ? '' : $diemlythuyet);
                //     // $sheet->getStyle('I' . $index)->applyFromArray($centerStyle);
                //     if ($diemlythuyet < 5) {
                //         // $sheet->getStyle('I' . $index)->getFill()
                //         //     ->setFillType(Fill::FILL_SOLID)
                //         //     ->getStartColor()->setARGB($markedColor);
                //         // $sheet->setCellValue('L' . $index, 'X');
                //         // $sheet->getStyle('L' . $index)->applyFromArray($centerStyle);

                //         $ghiChu .= 'ĐTNLT(L1:' . number_format($sv->lythuyetlan1, 1);
                //         if ($sv->lythuyetlan2 != -1) {
                //             $ghiChu .= ', L2:' . number_format($sv->lythuyetlan2, 1);
                //         }
                //         if ($sv->lythuyetlan3 != -1) {
                //             $ghiChu .= ', L3:' . number_format($sv->lythuyetlan3, 1);
                //         }
                //         $ghiChu .= ')';
                //     }

                //     // $sheet->setCellValue('J' . $index, $diemthuchanh == -1 ? '' : $diemthuchanh);
                //     // $sheet->getStyle('J' . $index)->applyFromArray($centerStyle);
                //     if ($diemthuchanh < 5) {
                //         // $sheet->getStyle('J' . $index)->getFill()
                //         //     ->setFillType(Fill::FILL_SOLID)
                //         //     ->getStartColor()->setARGB($markedColor);
                //         // $sheet->setCellValue('M' . $index, 'X');
                //         // $sheet->getStyle('M' . $index)->applyFromArray($centerStyle);
                //         if ($ghiChu != '') {
                //             $ghiChu .= ',';
                //         }
                //         $ghiChu .= ' ĐTNTH(L1:' . number_format($sv->thuchanhlan1, 1);
                //         if ($sv->thuchanhlan2 != -1) {
                //             $ghiChu .= ', L2:' . number_format($sv->thuchanhlan2, 1);
                //         }
                //         if ($sv->thuchanhlan3 != -1) {
                //             $ghiChu .= ', L3:' . number_format($sv->thuchanhlan3, 1);
                //         }
                //         $ghiChu .= ')';
                //     }

                //     // $sheet->setCellValue('H' . $index, $diemchinhtri == -1 ? '' : $diemchinhtri);
                //     // $sheet->getStyle('H' . $index)->applyFromArray($centerStyle);
                //     if ($diemchinhtri < 5) {
                //         // $sheet->getStyle('H' . $index)->getFill()
                //         //     ->setFillType(Fill::FILL_SOLID)
                //         //     ->getStartColor()->setARGB($markedColor);
                //         // $sheet->setCellValue('K' . $index, 'X');
                //         // $sheet->getStyle('K' . $index)->applyFromArray($centerStyle);
                //         if ($ghiChu != '') {
                //             $ghiChu .= ',';
                //         }
                //         $ghiChu .= ' ĐTNCT(L1:' . number_format($sv->chinhtrilan1, 1);
                //         if ($sv->thuchanhlan2 != -1) {
                //             $ghiChu .= ', L2:' . number_format($sv->chinhtrilan2, 1);
                //         }
                //         if ($sv->thuchanhlan3 != -1) {
                //             $ghiChu .= ', L3:' . number_format($sv->chinhtrilan3, 1);
                //         }
                //         $ghiChu .= ')';
                //     }


                //     $ghiChuMonHoc =  $ghiChu != null ? ", " : "";
                //     $ghiChuTempLyDo =  $diemdotthi[0]->svd_ghichu != null ? $ghiChuMonHoc . $diemdotthi[0]->svd_ghichu : "";
                //     $ghiChu .=  $ghiChuTempLyDo;

                //     $sheet->getColumnDimension("H")->setAutoSize(true);
                //     $sheet->setCellValue('H' . $index, $ghiChu);
                //     $sheet->getStyle('H' . $index)->applyFromArray($normalStyle);
                // }

                $sheet->getColumnDimension("H")->setAutoSize(true);
                $sheet->setCellValue('H' . $index, $diemdotthi[0]->svd_ghichu);
                $sheet->getStyle('H' . $index)->applyFromArray($normalStyle);

                if ($sv->diemTkMonChuaDat == true) {
                    array_push($ghiChuSVCoDiemTkChuaDat, $sv->sv_ho . " " . $sv->sv_ten);
                }
                $index++;
            }

            // Sau khi kết thúc vòng lặp $nn->danhsachsinhvien, thêm một hàng trống
            $sheet->insertNewRowBefore($this->currentRow('A' . $index), 1);
            $sheet->getStyle('A' . $index)->applyFromArray($boldLeftStyle);
            $index++;
        }


        $sheet->setCellValue('A7', mb_strtoupper('ĐỢT XÉT: ' . $dotThi->dt_ten, 'UTF-8'));
        $title_content = $loai == 1 ? "DỰ THI" : "CHƯA ĐỦ ĐIỀU KIỆN DỰ THI";
        if ($nganhnghe->hdt_id == 5) {
            $sheet->setCellValue('A5', 'DANH SÁCH HỌC SINH ' . $title_content . ' TỐT NGHIỆP');
            $sheet->setCellValue('A6', 'TRÌNH ĐỘ: TRUNG CẤP - HỆ: ' . strtoupper(mb_strtoupper($nganhnghe->kdt_he, 'UTF-8')));
            $sheet->setCellValue('A' . ($index + 1), 'Tổng số học sinh: ' . $sohocsinh);
            $sheet->setCellValue('B10', 'MÃ SỐ HS');
        } else {
            $sheet->setCellValue('A5', 'DANH SÁCH SINH VIÊN ' . $title_content . ' TỐT NGHIỆP');
            $sheet->setCellValue('A6', 'TRÌNH ĐỘ: CAO ĐẲNG - HỆ: ' . strtoupper(mb_strtoupper($nganhnghe->kdt_he, 'UTF-8')));
            $sheet->setCellValue('A' . ($index + 1), 'Tổng số sinh viên: ' . $sohocsinh);
            $sheet->setCellValue('B10', 'MÃ SỐ SV');
        }
        $sheet->getStyle('A' . $index + 1)->applyFromArray($boldLeftStyle);

        // Thêm quyết định
        if ($loai == 1) {
            if ($dotThi->qd_id != "-1") {
                $ngayQD = Carbon::parse($dotThi->quyetDinh->qd_ngay)->format('d \t\h\á\n\g m \t\h\á\n\g Y');
                $formatQD = "(Theo QĐ số: {$dotThi->quyetDinh->qd_ma}, ngày {$ngayQD} của Hiệu trưởng TCĐCĐ & NN Nam Bộ)";
            } else {
                $formatQD = "(Theo QĐ số: ......, ngày ....... của Hiệu trưởng TCĐCĐ & NN Nam Bộ)";
            }
            $sheet->setCellValue('A8', $formatQD);
            $sheet->getStyle('A8')->applyFromArray($italicStyle);
        }

        if ($loai == 0) {
            if (count($ghiChuSVCoDiemTkChuaDat) > 0) {
                $lastIndex = count($ghiChuSVCoDiemTkChuaDat) - 1;
                $ghiChuSVCoDiemTkChuaDat[$lastIndex] .= '.'; // Thêm dấu chấm vào phần tử cuối cùng
            }
            $finalString_ghiChuSVCoDiemTkChuaDat = implode(', ', $ghiChuSVCoDiemTkChuaDat);

            $sheet->setCellValue('A' . ($index + 2), 'Ghi chú: ' . $finalString_ghiChuSVCoDiemTkChuaDat);
            $sheet->getStyle('A' . $index + 2)->applyFromArray($normalStyle);

            $sheet->setCellValue('G' . ($index + 3), 'HIỆU TRƯỞNG');
            $sheet->getStyle('G' . $index + 3)->applyFromArray($boldCenterStyle2);
        } else {
            $sheet->setCellValue('G' . ($index + 2), 'HIỆU TRƯỞNG');
            $sheet->getStyle('G' . $index + 2)->applyFromArray($boldCenterStyle2);
        }

        // Xóa hàng số 8
        if ($loai == 0) {
            $sheet->removeRow(8);
        }

        $result->spreadsheet = $spreadsheet;
        if ($loai == 1) {
            $fileName = "DSDĐKTTN";
        } else if ($loai == 0) {
            $fileName = "DSCĐKTTN";
        }

        $result->fileName = mb_strtoupper($fileName . ' - ĐỢT XÉT: ' . $dotThi->dt_ten, 'UTF-8');

        return $result;
    }

    public function createSheetDanhSachXetThiTotNghiep($filterLhId, $dt_id)
    {
        $result = new \stdClass;
        $spreadsheet = new Spreadsheet();
        $exportData = $this->getKetQuaHocTapTheoDotThi(123456, $filterLhId, $dt_id);
        $dotThi = DotThi::with('dotXetTotNghiep')->where('qlsv_dotthi.dt_id', '=', $dt_id)->first();
        $lopHoc = $exportData['lopHoc'];
        $nienKhoa = $lopHoc->nienKhoa;
        $khoaDaoTao = $lopHoc->khoaDaoTao;
        $heDaotao = $khoaDaoTao->heDaotao;
        $nganhNghe = $khoaDaoTao->nganhNghe;
        $danhSachNamHoc = $exportData['danhSachNamHoc'];
        $danhSachSinhVien = $exportData['danhSachSinhVien'];
        $danhSachGhiChu = $exportData['notes'];
        $exportSemesters = $exportData['semesters'];
        $spreadsheet = IOFactory::load(storage_path($this->template_danhsachxetthi));
        $spreadsheet->getDefaultStyle()->getFont()->setName('Times New Roman');
        $spreadsheet->getDefaultStyle()->getFont()->setSize(13);
        // $danhSachSinhVien = $danhSachSinhVien->filter(function ($sinhVien){
        //     if($sinhVien->svd_dieukien == 1){
        //         //if($sinhVien->sv_ma == 'TCTP55B06') dd($sinhVien);
        //         return $sinhVien;
        //     }
        // });

        $filter = new Filters;
        // lọc danh sinh viên đạt và không đạt điều kiện thi tốt nghiệp
        $danhSachSinhVien = $filter->LocSinhVienKhongDuDKThiTN($danhSachSinhVien);

        $centerBorderStyle = [
            'font' => [
                'bold' => false,
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'textRotation' => 0,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ]
        ];
        $centerNormalStyle = [
            'font' => [
                'bold' => false,
                'size' => 13
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'textRotation' => 0,
            ],

        ];

        $centerStyle = [
            'font' => [
                'bold' => false,
                'size' => 13,
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'textRotation' => 0,
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => [
                    'argb' => 'FFFFFFFF',
                ],
            ]
        ];

        $normalStyle = [
            'font' => [
                'bold' => false,
                'size' => 13,
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ]
        ];

        $boldStyle = [
            'font' => [
                'bold' => true,
            ]
        ];

        $boldCenterStyle = [
            'font' => [
                'bold' => true,
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ]
        ];

        $boldLeftStyle = [
            'font' => [
                'bold' => true,
                'size' => 13,
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
            ]
        ];

        $thickTopBorderStyle = [
            'borders' => [
                'top' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK,
                ]
            ]
        ];

        $markedColor = 'FFC4CC';

        $sheet = $spreadsheet->getActiveSheet();

        $maxUsedCol = 9;
        // Header danh sách môn học
        $monHocTitleAddress = 'E10';
        $monHocTitleFinalAddress = 'E10';
        $currentAddress = 'E10';
        $thickTopBorderStyleBool = false;

        // số cột phát sinh theo môn học trừ đi 1 (là cột ban đầu)
        $numberInsertCol = $danhSachNamHoc->map(function ($year) {
            // Số môn học + 3 ô (điểm trung bình/ rèn luyện)
            return $year->semesters->map(function ($semester) {
                return $semester->monHoc->count() - 1;
            })->sum();
        })->sum();
        // Thêm cột xếp loại năm
        $numberInsertCol += $danhSachNamHoc->count() + 1;
        $maxUsedCol += $numberInsertCol;
        $sheet->getRowDimension($this->currentRow($currentAddress))->setRowHeight(70);
        $sheet->insertNewColumnBefore($this->currentCol($currentAddress), $numberInsertCol);
        for ($i = 0; $i < $numberInsertCol + 1; $i++) {
            $sheet->getStyle($currentAddress)->applyFromArray($centerBorderStyle);
            $sheet->getStyle($this->nextRowAddress($currentAddress))->applyFromArray($centerBorderStyle);
            $sheet->getStyle($currentAddress)->getAlignment()->setTextRotation(90);
            $sheet->mergeCells($currentAddress . ':' . $this->nextRowAddress($currentAddress));
            $currentAddress = $this->nextColAddress($currentAddress);
        }

        $currentAddress = 'E10';
        foreach ($danhSachNamHoc as $yIndex => $year) {
            foreach ($year->semesters as $sIndex => $semester) {
                $semester->monHoc->each(function ($monHoc) use (&$sheet, &$currentAddress, &$totalTinChi) {
                    if ($monHoc->mh_tichluy) {
                        $totalTinChi += $monHoc->mh_sodonvihoctrinh;
                    }

                    // Mã môn học
                    $sheet->setCellValue($currentAddress, $monHoc->mh_tichluy ? $monHoc->mh_ma : $monHoc->mh_ma . ' (*)');
                    $sheet->getStyle($currentAddress)->getAlignment()->setTextRotation(90);
                    $sheet->getStyle($currentAddress)->applyFromArray([
                        'font' => [
                            'bold' => true
                        ]
                    ]);
                    $sheet->getColumnDimension($this->currentCol($currentAddress))->setWidth(5);

                    // Số tín chỉ của môn học
                    $tinChiAddress = $this->nextRowAddress($this->nextRowAddress($currentAddress));
                    $sheet->setCellValue($tinChiAddress, $monHoc->mh_sodonvihoctrinh);

                    $currentAddress = $this->nextColAddress($currentAddress);
                });
            }
        }
        // $sheet->setCellValue($this->nextRowAddress($this->nextRowAddress($this->nextColAddress($this->nextColAddress($currentAddress))))
        //     , $totalTinChi);

        //end điền header
        $tongsokhongdieukien = 0;

        $sinhVienAddress = 'A13';
        $currentAddress = $sinhVienAddress;
        $sheet->insertNewRowBefore($this->currentRow($currentAddress), $danhSachSinhVien->count() - 1);
        foreach ($danhSachSinhVien->values() as $sIndex => $sinhVien) {

            // 1. Stt
            $sheet->setCellValue($currentAddress, $sIndex + 1);
            $sheet->getStyle($currentAddress)->applyFromArray($centerBorderStyle);
            $currentAddress = $this->nextColAddress($currentAddress);

            // 2. Mã số
            $sheet->setCellValue($currentAddress, $sinhVien->sv_ma);
            $sheet->getStyle($currentAddress)->applyFromArray($normalStyle);
            $currentAddress = $this->nextColAddress($currentAddress);

            // 3. Họ
            $sheet->setCellValue($currentAddress, $sinhVien->sv_ho);
            $sheet->getStyle($currentAddress)->applyFromArray($normalStyle);
            $currentAddress = $this->nextColAddress($currentAddress);

            $sheet->setCellValue($currentAddress, $sinhVien->sv_ten);
            $sheet->getStyle($currentAddress)->applyFromArray($normalStyle);
            $currentAddress = $this->nextColAddress($currentAddress);

            foreach ($sinhVien->years as $yIndex => $year) {
                foreach ($year->semesters as $semester) {
                    // 4. Điểm các môn trong học kỳ
                    foreach ($semester->monHoc as $monHoc) {
                        if ($monHoc->ketQua) {
                            $final = $monHoc->ketQua->display_score;
                            $sheet->setCellValue($currentAddress, $final != null ? number_format($final, 1) : '');
                        }
                        $sheet->getStyle($currentAddress)->getNumberFormat()->setFormatCode('0.0');
                        $sheet->getStyle($currentAddress)->applyFromArray($centerStyle);
                        if ($final < 5) {
                            $sheet->getStyle($currentAddress)->getFill()
                                ->setFillType(Fill::FILL_SOLID)
                                ->getStartColor()->setARGB($markedColor);
                        }
                        $currentAddress = $this->nextColAddress($currentAddress);
                    }
                }
            }

            //Rèn luyên toàn khóa
            if (isset($sinhVien->toanKhoa->diemRenLuyen)) {
                $sheet->getStyle($currentAddress)->applyFromArray($centerStyle);
                $sheet->setCellValue($currentAddress, $sinhVien->toanKhoa->diemRenLuyen);
            }
            $currentAddress = $this->nextColAddress($currentAddress);

            //Tính chỉ tích lủy
            $sheet->getStyle($currentAddress)->applyFromArray($centerStyle);
            $sheet->setCellValue($currentAddress, $sinhVien->toanKhoa->tinChiTichLuy);
            $currentAddress = $this->nextColAddress($currentAddress);

            //Điểm trung bình
            // $sheet->getStyle($currentAddress)->applyFromArray($centerStyle);
            // if (!isset($sinhVien->toanKhoa->avg) || $sinhVien->toanKhoa->avg < 5) {
            //     $sheet->getStyle($currentAddress)->getFill()
            //         ->setFillType(Fill::FILL_SOLID)
            //         ->getStartColor()->setARGB($markedColor);
            // }
            //$sheet->setCellValue($currentAddress, isset($sinhVien->toanKhoa->avg) ? $sinhVien->toanKhoa->avg : '');
            //$currentAddress = $this->nextColAddress($currentAddress);

            // ĐTBCTL	Điểm trung bình chung tích lũy
            $sheet->getStyle($currentAddress)->applyFromArray($centerStyle);
            if (!isset($sinhVien->toanKhoa->tichLuy) || $sinhVien->toanKhoa->tichLuy < 5) {
                $sheet->getStyle($currentAddress)->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setARGB($markedColor);
            }
            $sheet->getStyle($currentAddress)->getNumberFormat()->setFormatCode('0.0');
            $sheet->setCellValue($currentAddress, isset($sinhVien->toanKhoa->tichLuy) ? $sinhVien->toanKhoa->tichLuy : '');
            $currentAddress = $this->nextColAddress($currentAddress);

            //9 số
            if ($sinhVien->datTotNghep == false) {
                $tongsokhongdieukien++;
                $sheet->setCellValue($currentAddress, 'Tháng …năm …');
                $sheet->getStyle($currentAddress)->applyFromArray($normalStyle);
            }
            $currentAddress = $this->nextColAddress($currentAddress);

            // 10. Ghi chú
            $svGhiChu = $sinhVien->notes->map(function ($note) {
                if ($note['type'] == 'DL3') {
                    return $note['key'] . ' (DiemL3:' . number_format($note['value'], 1) . ')';
                } else if ($note['type'] == 'DTL3') {
                    return $note['key'] . ' (DiemThiL3:' . number_format($note['value'], 1) . ')';
                } else if ($note['type'] == 'DL2') {
                    return $note['key'] . ' (DiemL2:' . number_format($note['value'], 1) . ')';
                } else if ($note['type'] == 'DTL2') {
                    return $note['key'] . ' (DiemThiL2:' . number_format($note['value'], 1) . ')';
                } else if ($note['type'] == 'DL1') {
                    return $note['key'] . ' (DiemL1:' . number_format($note['value'], 1) . ')';
                } else if ($note['type'] == 'DTL1') {
                    return $note['key'] . ' (DiemThiL1:' . number_format($note['value'], 1) . ')';
                } else {
                    if (isset($note['value'])) {
                        return $note['type'] . ' (' . $note['value'] . ')';
                    }
                    return $note['type'] . ' (' . $note['key'] . ')';
                }
            })->join(', ');

            $sheet->setCellValue($currentAddress, $svGhiChu);
            $sheet->getStyle($currentAddress)->applyFromArray($normalStyle);


            // set ThickTopBorder ngăn cách giữa sv đạt và không đạt
            if ($sinhVien->datTotNghep == false && $thickTopBorderStyleBool == false) {
                preg_match('/([A-Za-z]+)([0-9]+)/', $currentAddress, $matches);

                // $matches[1] chứa chữ, $matches[2] chứa số
                // $nameCol = $matches[1];
                $numCol = $matches[2];

                $cellRangeThickTopBorder = "A{$numCol}:{$currentAddress}";
                $sheet->getStyle($cellRangeThickTopBorder)->applyFromArray($thickTopBorderStyle);
                $thickTopBorderStyleBool = true;
            }


            $columnDimensionCurrent = substr($currentAddress, 0, -strlen($currentAddress) + strspn(strrev($currentAddress), '0123456789'));
            $sheet->getColumnDimension($columnDimensionCurrent)->setAutoSize(true);
            $currentAddress = $this->nextColAddress($currentAddress);

            $currentAddress = 'A' . ($sIndex + 14);
        }
        //Để phía cuối
        $sheet->setCellValue('A7', mb_strtoupper('LỚP: ' . $lopHoc->lh_ten . ' (MÃ LỚP: ' . $lopHoc->lh_ma . ') - KHÓA ' . $khoaDaoTao->kdt_khoa . ' - NIÊN KHÓA: ' . $lopHoc->nienKhoa->nk_ten, 'UTF-8'));

        $sheet->setCellValue('A8', mb_strtoupper('ĐỢT XÉT: ' . $dotThi->dt_ten, 'UTF-8'));
        $sheet->getStyle('A8')->applyFromArray($centerNormalStyle);

        if ($nganhNghe->hdt_id == 5) {
            $sheet->setCellValue('A5', 'DANH SÁCH HỌC SINH DỰ XÉT THI TỐT NGHIỆP');
            $sheet->setCellValue('A6', mb_strtoupper('TRÌNH ĐỘ: TRUNG CẤP - HỆ: ' . $khoaDaoTao->kdt_he, 'UTF-8'));
            $sheet->setCellValue('B10', 'MSHS');
        } else {
            $sheet->setCellValue('A5', 'DANH SÁCH SINH VIÊN DỰ XÉT THI TỐT NGHIỆP');
            $sheet->setCellValue('A6', mb_strtoupper('TRÌNH ĐỘ: CAO ĐẲNG - HỆ: ' . $khoaDaoTao->kdt_he, 'UTF-8'));
            $sheet->setCellValue('B10', 'MSSV');
        }


        // Thống kê học lực
        // $thongKeHocLuc = null;

        // $thongKeHocLuc = $danhSachSinhVien->countBy(function ($sinhVien) {

        //     if (isset($sinhVien->toanKhoa->avg) && $sinhVien->toanKhoa->avg > 5 && isset($sinhVien->toanKhoa) && isset($sinhVien->toanKhoa->hocLuc)) {
        //         return $sinhVien->toanKhoa->hocLuc;
        //     };
        //     return 'NA';
        // });
        // $thongKeMessage = [];
        // if (isset($thongKeHocLuc['Xuất sắc'])) {
        //     $thongKeMessage[] = 'Xuất sắc: ' . $thongKeHocLuc['Xuất sắc'] . ' ' . ($nganhNghe->hdt_id == 4 ? 'SV' : 'HS');
        // }
        // if (isset($thongKeHocLuc['Giỏi'])) {
        //     $thongKeMessage[] = 'Giỏi: ' . $thongKeHocLuc['Giỏi'] . ' ' . ($nganhNghe->hdt_id == 4 ? 'SV' : 'HS');
        // }
        // if (isset($thongKeHocLuc['Khá'])) {
        //     $thongKeMessage[] = 'Khá: ' . $thongKeHocLuc['Khá'] . ' ' . ($nganhNghe->hdt_id == 4 ? 'SV' : 'HS');
        // }
        // if (isset($thongKeHocLuc['Trung bình khá'])) {
        //     $thongKeMessage[] = 'Trung bình khá: ' . $thongKeHocLuc['Trung bình khá'] . ' ' . ($nganhNghe->hdt_id == 4 ? 'SV' : 'HS');
        // }
        // if (isset($thongKeHocLuc['Trung bình'])) {
        //     $thongKeMessage[] = 'Trung bình: ' . $thongKeHocLuc['Trung bình'] . ' ' . ($nganhNghe->hdt_id == 4 ? 'SV' : 'HS');
        // }

        // $soHocSinhXepLoai = $danhSachSinhVien->count();
        // $khongXepLoai = 0;
        // if (isset($thongKeHocLuc['NA'])) {
        //     $khongXepLoai = $thongKeHocLuc['NA'];
        //     $soHocSinhXepLoai = $soHocSinhXepLoai - $khongXepLoai;
        // }

        $tongsodieukien = $danhSachSinhVien->count() - $tongsokhongdieukien;
        $sheet->setCellValue($currentAddress, 'Tổng số học sinh: ' . $danhSachSinhVien->count());
        $currentAddress = $this->nextRowAddress($currentAddress, true);
        $exportMessage = 'Dự kiến số ' . ($nganhNghe->hdt_id == 4 ? 'sinh viên' : 'học sinh') . ' đủ điều kiện dự thi tốt nghiệp: ' . $tongsodieukien;
        //$exportMessage .= join('; ', $thongKeMessage);
        $sheet->setCellValue($currentAddress, $exportMessage);
        $sheet->getStyle($currentAddress)->applyFromArray($boldStyle);
        $currentAddress = $this->nextRowAddress($currentAddress, true);

        // Không xếp loại học tập: 03 HS.
        $sheet->setCellValue($currentAddress, 'Dự kiến số ' . ($nganhNghe->hdt_id == 4 ? 'sinh viên' : 'học sinh') . ' chưa đủ điều kiện dự thi tốt nghiệp: ' . $tongsokhongdieukien);
        $sheet->getStyle($currentAddress)->applyFromArray($boldStyle);
        $currentAddress = $this->nextRowAddress($currentAddress, true);

        // Ghi chú
        $currentAddress = $this->nextRowAddress($currentAddress, true);

        // Các table ghi chú
        $danhSachGhiChuSplit = null;
        $noteColSplit = [];
        $paddingCol = 0;

        // Lọc bỏ những môn học không muốn hiển thị
        $validValues = ['ĐTBHK1', 'RLHK1', 'ĐTBHK2', 'RLHK2', 'ĐTBN1', 'RLN1', 'ĐTN', 'ĐTBN2', 'RLN2', 'ĐTN', 'ĐTBNTK'];

        $danhSachGhiChu = $danhSachGhiChu->reject(function ($item) use ($validValues) {
            return in_array($item['key'], $validValues);
        });
        $newItems = [
            [
                "key" => "HL",
                "value" => $nganhNghe->hdt_id == 4 ? "Sinh viên liên hệ Khoa đăng ký thi lại" : "Học sinh liên hệ Khoa đăng ký thi lại"
            ],
            [
                "key" => "TL",
                "value" => $nganhNghe->hdt_id == 4 ? "Sinh viên liên hệ Khoa đăng ký học lại" : "Học sinh liên hệ Khoa đăng ký học lại"
            ],
            // Thêm các phần tử khác tại đây
        ];

        $danhSachGhiChu = $danhSachGhiChu->merge($newItems);


        $danhSachGhiChuSplit = $danhSachGhiChu->splitIn(3);
        $paddingCol = round($maxUsedCol / 2) - 3;
        $insertRow = $danhSachGhiChuSplit[0]->count();
        $sheet->insertNewRowBefore($this->currentRow($currentAddress), $insertRow);
        $firstNoteRow = $this->currentRow($currentAddress);
        foreach ($danhSachGhiChuSplit as $sIndex => $notes) {
            $currentAddress = $this->currentCol($currentAddress) . $firstNoteRow;
            foreach ($notes as $note) {
                $richText = new \PhpOffice\PhpSpreadsheet\RichText\RichText();
                $payable = $richText->createTextRun($note['key'] . ': ');
                $payable->getFont()->setBold(true);
                $richText->createTextRun($note['value']);
                $sheet->setCellValue($currentAddress, $richText);
                $currentAddress = $this->nextRowAddress($currentAddress);
            }
            $currentAddress = $this->nextColsAddress($currentAddress, $paddingCol + $sIndex * 2);
        }
        $currentAddress = $this->nextRowAddress($currentAddress, true);

        // Ký tên
        $splitSignCol = 4;
        $paddingSignCol = round($maxUsedCol / $splitSignCol);
        $currentAddress = $this->nextRowAddress($currentAddress, true);
        $firstSignRow = $this->currentRow($currentAddress);

        // Người lập
        // $currentAddress = $this->nextRowsAddress($currentAddress, 5);
        // $sheet->setCellValue($currentAddress, 'Lê Thị Hồng Phương');

        // Phòng công tác sinh viên
        $columnNumber = 14; // Số thứ tự của cột

        // Chuyển đổi số thứ tự cột thành ký tự
        $columnLetter = chr(64 + $columnNumber);

        // Tạo địa chỉ cột và hàng
        $currentAddress = $columnLetter . $firstSignRow;
        // $currentAddress = $this->currentCol($currentAddress) . $firstSignRow;
        // $currentAddress = $this->nextColsAddress($currentAddress, $paddingSignCol);

        // $currentAddress = $this->nextRowAddress($currentAddress, false);
        $sheet->setCellValue($currentAddress, 'THƯ KÍ');
        $sheet->getStyle($currentAddress)->applyFromArray($boldCenterStyle);

        // Phòng công tác sinh viên
        // $currentAddress = $this->currentCol($currentAddress) . $firstSignRow;
        // $currentAddress = $this->nextColsAddress($currentAddress, $paddingSignCol);
        // $sheet->setCellValue($currentAddress, 'PT. PHÒNG CÔNG TÁC');
        // $sheet->getStyle($currentAddress)->applyFromArray($boldCenterStyle);
        // $currentAddress = $this->nextRowAddress($currentAddress);
        // $sheet->setCellValue($currentAddress, 'HỌC SINH, SINH VIÊN');
        // $sheet->getStyle($currentAddress)->applyFromArray($boldCenterStyle);
        // $currentAddress = $this->nextRowsAddress($currentAddress, 4);
        // $sheet->setCellValue($currentAddress, 'Trần Văn Kiểu');
        // $sheet->getStyle($currentAddress)->applyFromArray($centerStyle);

        // Phòng đào tạo
        // $currentAddress = $this->currentCol($currentAddress) . $firstSignRow;
        // $currentAddress = $this->nextColsAddress($currentAddress, $paddingSignCol);
        // $sheet->setCellValue($currentAddress, 'PTP. PHÒNG ĐÀO TẠO');
        // $sheet->getStyle($currentAddress)->applyFromArray($boldCenterStyle);

        // $currentAddress = $this->nextRowsAddress($currentAddress, 5);
        // $sheet->setCellValue($currentAddress, 'Hồ Văn Chương');
        // $sheet->getStyle($currentAddress)->applyFromArray($centerStyle);

        $currentAddress = $this->nextRowAddress($currentAddress, true);

        $result->spreadsheet = $spreadsheet;
        $result->fileName = mb_strtoupper('LỚP: ' . $lopHoc->lh_ten . ' (MÃ LỚP: ' . $lopHoc->lh_ma . ') - NIÊN KHÓA: ' . $khoaDaoTao->kdt_khoa, 'UTF-8');

        return $result;
    }

    public function createSheetMonHocDotThi($filterLhId, $dt_id)
    {
        $result = new \stdClass;
        $spreadsheet = new Spreadsheet();
        // $exportData = $this->getKetQuaHocTapTheoDotThiTN(123456, $filterLhId, $dt_id);

        $dxtn_id = DotThiDotXetTotNghiep::where('dt_id', $dt_id)->first()->dxtn_id;
        $exportData = $this->getKetQuaHocTapTheoDotXetTN(123456, $filterLhId, $dt_id, $dxtn_id);

        $dotThi = DotThi::with('dotXetTotNghiep')->where('qlsv_dotthi.dt_id', '=', $dt_id)->first();
        $lopHoc = $exportData['lopHoc'];

        $nienKhoa = $lopHoc->nienKhoa;
        $khoaDaoTao = $lopHoc->khoaDaoTao;
        $heDaotao = $khoaDaoTao->heDaotao;
        $nganhNghe = $khoaDaoTao->nganhNghe;
        $danhSachNamHoc = $exportData['danhSachNamHoc'];
        $danhSachGhiChu = $exportData['notes'];
        $exportSemesters = $exportData['semesters'];
        $danhSachSinhVien = $exportData['danhSachSinhVien'];


        // $danhSachSinhVien =  $exportData['danhSachSinhVien']->filter(function ($sinhVien){
        //      if($sinhVien->svd_dieukien == 1 && isset($sinhVien->toanKhoa) && $sinhVien->toanKhoa->avg_totnghiep >= 5){
        //          if($sinhVien->sv_ma == 'TCTP55B06') dd($sinhVien);
        //          return $sinhVien;
        //      }

        //     if($sinhVien->svd_dieukien == 1 && isset($sinhVien->toanKhoa)){
        //         //if($sinhVien->sv_ma == 'TCTP55B06') dd($sinhVien);
        //         return $sinhVien;
        //     }
        // });


        $filter = new Filters;
        $danhSachSinhVien = $filter->KetQuaSinhVienThamDuThiTN($danhSachSinhVien, $filterLhId, $dt_id, $lopHoc);

        foreach ($danhSachSinhVien as $key => $sinhvien) {
            // Kiểm tra nếu ghiChu là "Chưa đạt"
            if ($sinhvien->ghiChu == "Chưa đạt") {
                $sinhvien->toanKhoa->avg_totnghiep = 0;
                $sinhvien->toanKhoa->final_xltn = "NA";
            } else if ($sinhvien->svxtn_vipham == 1) {
                unset($danhSachSinhVien[$key]); // Unset the element
                // $sinhvien->toanKhoa->avg_totnghiep = 0;
                // $sinhvien->toanKhoa->final_xltn = "NA";
                // $sinhvien->ghiChu = "(Chưa đạt) lý do: " . $sinhvien->ghiChu;
            } else if ($sinhvien->giamXltn == true) {
                $sinhvien->ghiChu = "Giảm một mức XLTN";
            }
        }

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


        if ($lopHoc->lh_nienche != 0) {
            $spreadsheet = IOFactory::load(storage_path($this->template_diemmonhocdotthitheolop2022));
        } else {
            $spreadsheet = IOFactory::load(storage_path($this->template_diemmonhocdotthitheolop2020));
        }
        $spreadsheet->getDefaultStyle()->getFont()->setName('Times New Roman');
        $spreadsheet->getDefaultStyle()->getFont()->setSize(13);

        $color = [
            'red' => '9C0006',
            'default' => '000000'
        ];
        $centerBorderStyle = [
            'font' => [
                'bold' => false,
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'textRotation' => 0,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ]
        ];

        $boldCenterBorderStyle = [
            'font' => [
                'bold' => true,
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'textRotation' => 0,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ]
        ];

        $centerStyle = [
            'font' => [
                'bold' => false,
                'size' => 13,
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'textRotation' => 0,
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => [
                    'argb' => 'FFFFFFFF',
                ],
            ]
        ];

        $normalStyle = [
            'font' => [
                'bold' => false,
                'size' => 13,
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ]
        ];

        $boldStyle = [
            'font' => [
                'bold' => true,
            ]
        ];

        $boldCenterStyle = [
            'font' => [
                'bold' => true,
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ]
        ];

        $boldLeftStyle = [
            'font' => [
                'bold' => true,
                'size' => 13,
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
            ]
        ];

        $centerBorderThinStyle = [
            'font' => [
                'bold' => false,
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'textRotation' => 0,
            ],
            'borders' => [
                'bottom' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
        ];

        $thickTopBorderStyle = [
            'borders' => [
                'top' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK,
                ]
            ]
        ];

        //$markedColor = 'FFC4CC';
        $markedColor = '00a65a';


        $sheet = $spreadsheet->getActiveSheet();

        $maxUsedCol = 9;
        // Header danh sách môn học
        $monHocTitleAddress = 'E10';
        $monHocTitleFinalAddress = 'E10';
        $currentAddress = 'E10';
        $thickTopBorderStyleBool = false;

        // số cột phát sinh theo môn học trừ đi 1 (là cột ban đầu)
        $numberInsertCol = $danhSachNamHoc->map(function ($year) {
            // Số môn học + 3 ô (điểm trung bình/ rèn luyện)
            return $year->semesters->map(function ($semester) {
                return $semester->monHoc->count() - 1;
            })->sum();
        })->sum();
        // Thêm cột xếp loại năm
        $numberInsertCol += $danhSachNamHoc->count() + 1;
        $maxUsedCol += $numberInsertCol;
        $sheet->getRowDimension($this->currentRow($currentAddress))->setRowHeight(70);
        $sheet->insertNewColumnBefore($this->currentCol($currentAddress), $numberInsertCol);
        for ($i = 0; $i < $numberInsertCol + 1; $i++) {
            $sheet->getStyle($currentAddress)->applyFromArray($centerBorderStyle);
            $sheet->getStyle($this->nextRowAddress($currentAddress))->applyFromArray($centerBorderStyle);
            $sheet->getStyle($currentAddress)->getAlignment()->setTextRotation(90);
            $sheet->mergeCells($currentAddress . ':' . $this->nextRowAddress($currentAddress));
            $currentAddress = $this->nextColAddress($currentAddress);
        }

        $currentAddress = 'E10';
        foreach ($danhSachNamHoc as $yIndex => $year) {
            foreach ($year->semesters as $sIndex => $semester) {
                $semester->monHoc->each(function ($monHoc) use (&$sheet, &$currentAddress, &$totalTinChi) {
                    if ($monHoc->mh_tichluy) {
                        $totalTinChi += $monHoc->mh_sodonvihoctrinh;
                    }

                    // Mã môn học
                    $sheet->setCellValue($currentAddress, $monHoc->mh_tichluy ? $monHoc->mh_ma : $monHoc->mh_ma . ' (*)');
                    $sheet->getStyle($currentAddress)->getAlignment()->setTextRotation(90);
                    $sheet->getStyle($currentAddress)->applyFromArray([
                        'font' => [
                            'bold' => true
                        ]
                    ]);
                    $sheet->getColumnDimension($this->currentCol($currentAddress))->setWidth(5);

                    // Số tín chỉ của môn học
                    $tinChiAddress = $this->nextRowAddress($this->nextRowAddress($currentAddress));
                    $sheet->setCellValue($tinChiAddress, $monHoc->mh_sodonvihoctrinh);

                    $currentAddress = $this->nextColAddress($currentAddress);
                });
            }
        }
        $sheet->setCellValue(
            $this->nextRowAddress($this->nextRowAddress($this->nextColAddress($this->nextColAddress($currentAddress)))),
            $totalTinChi
        );

        //end điền header

        $sinhVienAddress = 'A13';
        $currentAddress = $sinhVienAddress;
        $sheet->insertNewRowBefore($this->currentRow($currentAddress), $danhSachSinhVien->count() - 1);

        foreach ($danhSachSinhVien->values() as $sIndex => $sinhVien) {

            // 1. Stt
            $sheet->setCellValue($currentAddress, $sIndex + 1);
            $sheet->getStyle($currentAddress)->applyFromArray($centerBorderStyle);
            $currentAddress = $this->nextColAddress($currentAddress);

            // 2. Mã số
            $sheet->setCellValue($currentAddress, $sinhVien->sv_ma);
            $sheet->getStyle($currentAddress)->applyFromArray($normalStyle);
            $currentAddress = $this->nextColAddress($currentAddress);

            // 3. Họ
            $sheet->setCellValue($currentAddress, $sinhVien->sv_ho);
            $sheet->getStyle($currentAddress)->applyFromArray($normalStyle);
            $currentAddress = $this->nextColAddress($currentAddress);

            $sheet->setCellValue($currentAddress, $sinhVien->sv_ten);
            $sheet->getStyle($currentAddress)->applyFromArray($normalStyle);
            $currentAddress = $this->nextColAddress($currentAddress);

            foreach ($sinhVien->years as $yIndex => $year) {
                foreach ($year->semesters as $semester) {
                    // 4. Điểm các môn trong học kỳ
                    foreach ($semester->monHoc as $monHoc) {
                        if ($monHoc->ketQua) {
                            $final = $monHoc->ketQua->display_score;
                            $sheet->setCellValue($currentAddress, $final != null ? number_format($final, 1) : '');
                        }
                        $sheet->getStyle($currentAddress)->getNumberFormat()->setFormatCode('0.0');
                        $sheet->getStyle($currentAddress)->applyFromArray($centerStyle);
                        if ($final < 5) {
                            $sheet->getStyle($currentAddress)->getFill()
                                ->setFillType(Fill::FILL_SOLID)
                                ->getStartColor()->setARGB($markedColor);
                        }
                        $currentAddress = $this->nextColAddress($currentAddress);
                    }
                }
            }

            //Rèn luyên toàn khóa
            $sheet->getStyle($currentAddress)->applyFromArray($centerStyle);
            $sheet->setCellValue($currentAddress, $sinhVien->toanKhoa->diemRenLuyen);
            $currentAddress = $this->nextColAddress($currentAddress);

            //Tính chỉ tích lủy
            $sheet->getStyle($currentAddress)->applyFromArray($centerStyle);
            $sheet->setCellValue($currentAddress, $sinhVien->toanKhoa->tinChiTichLuy);
            $currentAddress = $this->nextColAddress($currentAddress);

            // Điểm trung bình
            $sheet->getStyle($currentAddress)->applyFromArray($boldCenterBorderStyle);
            if (!isset($sinhVien->toanKhoa->avg) || $sinhVien->toanKhoa->avg < 5) {
                $sheet->getStyle($currentAddress)->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setARGB($markedColor);
            }
            $sheet->setCellValue($currentAddress, isset($sinhVien->toanKhoa->avg) ? $sinhVien->toanKhoa->avg : '');
            $sheet->getStyle($currentAddress)->getNumberFormat()->setFormatCode('0.0');
            $currentAddress = $this->nextColAddress($currentAddress);

            if ($sinhVien->khoaluanlan1 != -1) {
                $sheet->getStyle($currentAddress)->applyFromArray($centerStyle);
                $sheet->setCellValue($currentAddress, $sinhVien->khoaluanlan1);
                $sheet->getStyle($currentAddress)->getNumberFormat()->setFormatCode('0.0');

                if ($sinhVien->khoaluanlan1 < 5) {
                    $cellStyle = $sheet->getStyle($currentAddress);
                    $cellStyle->getFill()
                        ->setFillType(Fill::FILL_SOLID)
                        ->getStartColor()->setARGB($markedColor);
                    //$cellStyle->getFont()->getColor()->setRGB($color["red"]);
                }
            } else {
                $sheet->getStyle($currentAddress)->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setARGB($markedColor);
            }
            $currentAddress = $this->nextColAddress($currentAddress);

            if ($sinhVien->khoaluanlan2 != -1) {
                $sheet->getStyle($currentAddress)->applyFromArray($centerStyle);
                $sheet->setCellValue($currentAddress, $sinhVien->khoaluanlan2);
                $sheet->getStyle($currentAddress)->getNumberFormat()->setFormatCode('0.0');

                if ($sinhVien->khoaluanlan2 < 5) {
                    $cellStyle = $sheet->getStyle($currentAddress);
                    $cellStyle->getFill()
                        ->setFillType(Fill::FILL_SOLID)
                        ->getStartColor()->setARGB($markedColor);
                    //$cellStyle->getFont()->getColor()->setRGB($color["red"]);
                }
            } else {
                $sheet->getStyle($currentAddress)->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setARGB($markedColor);
            }
            $currentAddress = $this->nextColAddress($currentAddress);

            if ($sinhVien->khoaluanlan3 != -1) {
                $sheet->getStyle($currentAddress)->applyFromArray($centerStyle);
                $sheet->setCellValue($currentAddress, $sinhVien->khoaluanlan3);
                $sheet->getStyle($currentAddress)->getNumberFormat()->setFormatCode('0.0');

                if ($sinhVien->khoaluanlan3 < 5) {
                    $cellStyle = $sheet->getStyle($currentAddress);
                    $cellStyle->getFill()
                        ->setFillType(Fill::FILL_SOLID)
                        ->getStartColor()->setARGB($markedColor);
                    //$cellStyle->getFont()->getColor()->setRGB($color["red"]);
                }
            } else {
                $sheet->getStyle($currentAddress)->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setARGB($markedColor);
            }
            $currentAddress = $this->nextColAddress($currentAddress);

            if ($lopHoc->lh_nienche == 0) {
                if ($sinhVien->chinhtrilan1 != -1) {
                    $sheet->getStyle($currentAddress)->applyFromArray($centerStyle);
                    $sheet->setCellValue($currentAddress, $sinhVien->chinhtrilan1);
                    $sheet->getStyle($currentAddress)->getNumberFormat()->setFormatCode('0.0');

                    if ($sinhVien->chinhtrilan1 < 5) {
                        $cellStyle = $sheet->getStyle($currentAddress);
                        $cellStyle->getFill()
                            ->setFillType(Fill::FILL_SOLID)
                            ->getStartColor()->setARGB($markedColor);
                        //$cellStyle->getFont()->getColor()->setRGB($color["red"]);
                    }
                } else {
                    $sheet->getStyle($currentAddress)->getFill()
                        ->setFillType(Fill::FILL_SOLID)
                        ->getStartColor()->setARGB($markedColor);
                }
                $currentAddress = $this->nextColAddress($currentAddress);
            }

            if ($sinhVien->lythuyetlan1 != -1) {
                $sheet->getStyle($currentAddress)->applyFromArray($centerStyle);
                $sheet->setCellValue($currentAddress, $sinhVien->lythuyetlan1);
                $sheet->getStyle($currentAddress)->getNumberFormat()->setFormatCode('0.0');

                if ($sinhVien->lythuyetlan1 < 5) {
                    $cellStyle = $sheet->getStyle($currentAddress);
                    $cellStyle->getFill()
                        ->setFillType(Fill::FILL_SOLID)
                        ->getStartColor()->setARGB($markedColor);
                    //$cellStyle->getFont()->getColor()->setRGB($color["red"]);
                }
            } else {
                $sheet->getStyle($currentAddress)->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setARGB($markedColor);
            }
            $currentAddress = $this->nextColAddress($currentAddress);

            if ($sinhVien->thuchanhlan1 != -1) {
                $sheet->getStyle($currentAddress)->applyFromArray($centerStyle);
                $sheet->setCellValue($currentAddress, $sinhVien->thuchanhlan1);
                $sheet->getStyle($currentAddress)->getNumberFormat()->setFormatCode('0.0');

                if ($sinhVien->thuchanhlan1 < 5) {
                    $cellStyle = $sheet->getStyle($currentAddress);
                    $cellStyle->getFill()
                        ->setFillType(Fill::FILL_SOLID)
                        ->getStartColor()->setARGB($markedColor);
                    //$cellStyle->getFont()->getColor()->setRGB($color["red"]);
                }
            } else {
                $sheet->getStyle($currentAddress)->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setARGB($markedColor);
            }
            $currentAddress = $this->nextColAddress($currentAddress);

            if ($lopHoc->lh_nienche == 0) {
                if ($sinhVien->chinhtrilan2 != -1) {
                    $sheet->getStyle($currentAddress)->applyFromArray($centerStyle);
                    $sheet->setCellValue($currentAddress, $sinhVien->chinhtrilan2);
                    $sheet->getStyle($currentAddress)->getNumberFormat()->setFormatCode('0.0');

                    if ($sinhVien->chinhtrilan2 < 5) {
                        $cellStyle = $sheet->getStyle($currentAddress);
                        $cellStyle->getFill()
                            ->setFillType(Fill::FILL_SOLID)
                            ->getStartColor()->setARGB($markedColor);
                        //$cellStyle->getFont()->getColor()->setRGB($color["red"]);
                    }
                } else {
                    $sheet->getStyle($currentAddress)->getFill()
                        ->setFillType(Fill::FILL_SOLID)
                        ->getStartColor()->setARGB($markedColor);
                }
                $currentAddress = $this->nextColAddress($currentAddress);
            }

            if ($sinhVien->lythuyetlan2 != -1) {
                $sheet->getStyle($currentAddress)->applyFromArray($centerStyle);
                $sheet->setCellValue($currentAddress, $sinhVien->lythuyetlan2);
                $sheet->getStyle($currentAddress)->getNumberFormat()->setFormatCode('0.0');

                if ($sinhVien->lythuyetlan2 < 5) {
                    $cellStyle = $sheet->getStyle($currentAddress);
                    $cellStyle->getFill()
                        ->setFillType(Fill::FILL_SOLID)
                        ->getStartColor()->setARGB($markedColor);
                    //$cellStyle->getFont()->getColor()->setRGB($color["red"]);
                }
            } else {
                $sheet->getStyle($currentAddress)->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setARGB($markedColor);
            }
            $currentAddress = $this->nextColAddress($currentAddress);

            if ($sinhVien->thuchanhlan2 != -1) {
                $sheet->getStyle($currentAddress)->applyFromArray($centerStyle);
                $sheet->setCellValue($currentAddress, $sinhVien->thuchanhlan2);
                $sheet->getStyle($currentAddress)->getNumberFormat()->setFormatCode('0.0');

                if ($sinhVien->thuchanhlan2 < 5) {
                    $cellStyle = $sheet->getStyle($currentAddress);
                    $cellStyle->getFill()
                        ->setFillType(Fill::FILL_SOLID)
                        ->getStartColor()->setARGB($markedColor);
                    //$cellStyle->getFont()->getColor()->setRGB($color["red"]);
                }
            } else {
                $sheet->getStyle($currentAddress)->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setARGB($markedColor);
            }
            $currentAddress = $this->nextColAddress($currentAddress);

            if ($lopHoc->lh_nienche == 0) {
                if ($sinhVien->chinhtrilan3 != -1) {
                    $sheet->getStyle($currentAddress)->applyFromArray($centerStyle);
                    $sheet->setCellValue($currentAddress, $sinhVien->chinhtrilan3);
                    $sheet->getStyle($currentAddress)->getNumberFormat()->setFormatCode('0.0');

                    if ($sinhVien->chinhtrilan3 < 5) {
                        $cellStyle = $sheet->getStyle($currentAddress);
                        $cellStyle->getFill()
                            ->setFillType(Fill::FILL_SOLID)
                            ->getStartColor()->setARGB($markedColor);
                        //$cellStyle->getFont()->getColor()->setRGB($color["red"]);
                    }
                } else {
                    $sheet->getStyle($currentAddress)->getFill()
                        ->setFillType(Fill::FILL_SOLID)
                        ->getStartColor()->setARGB($markedColor);
                }
                $currentAddress = $this->nextColAddress($currentAddress);
            }

            if ($sinhVien->lythuyetlan3 != -1) {
                $sheet->getStyle($currentAddress)->applyFromArray($centerStyle);
                $sheet->setCellValue($currentAddress, $sinhVien->lythuyetlan3);
                $sheet->getStyle($currentAddress)->getNumberFormat()->setFormatCode('0.0');

                if ($sinhVien->lythuyetlan3 < 5) {
                    $cellStyle = $sheet->getStyle($currentAddress);
                    $cellStyle->getFill()
                        ->setFillType(Fill::FILL_SOLID)
                        ->getStartColor()->setARGB($markedColor);
                    //$cellStyle->getFont()->getColor()->setRGB($color["red"]);
                }
            } else {
                $sheet->getStyle($currentAddress)->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setARGB($markedColor);
            }
            $currentAddress = $this->nextColAddress($currentAddress);

            if ($sinhVien->thuchanhlan3 != -1) {
                $sheet->getStyle($currentAddress)->applyFromArray($centerStyle);
                $sheet->setCellValue($currentAddress, $sinhVien->thuchanhlan3);
                $sheet->getStyle($currentAddress)->getNumberFormat()->setFormatCode('0.0');

                if ($sinhVien->thuchanhlan3 < 5) {
                    $cellStyle = $sheet->getStyle($currentAddress);
                    $cellStyle->getFill()
                        ->setFillType(Fill::FILL_SOLID)
                        ->getStartColor()->setARGB($markedColor);
                    //$cellStyle->getFont()->getColor()->setRGB($color["red"]);
                }
            } else {
                $sheet->getStyle($currentAddress)->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setARGB($markedColor);
            }
            $currentAddress = $this->nextColAddress($currentAddress);

            $sheet->getStyle($currentAddress)->applyFromArray($boldCenterBorderStyle);
            if (isset($sinhVien->toanKhoa->avg_totnghiep) && $sinhVien->toanKhoa->avg_totnghiep != 0) {
                $sheet->setCellValue($currentAddress, $sinhVien->toanKhoa->avg_totnghiep);
                $sheet->getStyle($currentAddress)->getNumberFormat()->setFormatCode('0.0');
                if ($sinhVien->toanKhoa->avg_totnghiep < 5) {
                    $sheet->getStyle($currentAddress)->getFill()
                        ->setFillType(Fill::FILL_SOLID)
                        ->getStartColor()->setARGB($markedColor);
                }
            }
            $currentAddress = $this->nextColAddress($currentAddress);

            $sheet->getStyle($currentAddress)->applyFromArray($boldCenterBorderStyle);
            if (isset($sinhVien->toanKhoa->final_xltn) && $sinhVien->toanKhoa->final_xltn != 'Rớt' && $sinhVien->toanKhoa->avg_totnghiep != 0) {
                if ($sinhVien->datTotNghep) {
                    $sheet->setCellValue($currentAddress, $sinhVien->toanKhoa->final_xltn);
                }
            }

            $currentAddress = $this->nextColAddress($currentAddress);

            //Môn thi lại
            // if ($sinhVien->datTotNghep == false) {
            // } else {
            //     $sheet->setCellValue($currentAddress, "\n");
            // }

            if (isset($sinhVien->notes) && $sinhVien->datTotNghep == true && $sinhVien->svxtn_vipham == NULL) {
                $sheet->setCellValue(
                    $currentAddress,
                    $sinhVien->notes->map(function ($note) {
                        if ($note['type'] == 'DL2') {
                            return $note['key'] . ' (DiemL2:' . number_format($note['value'], 1) . ')';
                        } elseif ($note['type'] == 'DL1') {
                            return $note['key'] . ' (DiemL1:' . number_format($note['value'], 1) . ')';
                        } else {
                            return $note['type'] . ' (' . $note['key'] . ')';
                        }
                    })->join(', ')
                );
            }


            // if(isset($sinhVien->toanKhoa->hocLucTN) && $sinhVien->toanKhoa->hocLucTN == 'Rớt' || $sinhVien->svxtn_vipham == 1) {
            //     echo $sinhVien->ghiChu;
            // }


            $sheet->getStyle($currentAddress)->applyFromArray($normalStyle);
            $spreadsheet->getActiveSheet()->getRowDimension($this->currentRow($currentAddress))->setRowHeight(50);
            $currentAddress = $this->nextColAddress($currentAddress);


            $sheet->setCellValue($currentAddress, $sinhVien->ghiChu);
            $ghiChuThiLai = "";
            // $sinhVien->toanKhoa->hocLucTN = 'NA';
            $sheet->getStyle($currentAddress)->applyFromArray($centerStyle);

            // set ThickTopBorder ngăn cách giữa sv đạt và không đạt
            if (($sinhVien->datTotNghep == false || $sinhVien->svxtn_vipham == 1) && $thickTopBorderStyleBool == false) {
                preg_match('/([A-Za-z]+)([0-9]+)/', $currentAddress, $matches);

                // $matches[1] chứa chữ, $matches[2] chứa số
                // $nameCol = $matches[1];
                $numCol = $matches[2];

                $cellRangeThickTopBorder = "A{$numCol}:{$currentAddress}";
                $sheet->getStyle($cellRangeThickTopBorder)->applyFromArray($thickTopBorderStyle);
                $thickTopBorderStyleBool = true;
            }

            $currentAddress = 'A' . ($sIndex + 14);
        }

        //Để phía cuối
        $sheet->setCellValue('A7', mb_strtoupper('LỚP: ' . $lopHoc->lh_ten . ' (MÃ LỚP: ' . $lopHoc->lh_ma . ') - KHÓA ' . $khoaDaoTao->kdt_khoa . ' - NIÊN KHÓA: ' . $lopHoc->nienKhoa->nk_ten, 'UTF-8'));
        $sheet->setCellValue('A8', mb_strtoupper('ĐỢT XÉT: ' . $dotThi->dotXetTotNghiep[0]->dxtn_ten, 'UTF-8'));

        if ($nganhNghe->hdt_id == 5) {
            $sheet->setCellValue('A5', 'DANH SÁCH HỌC SINH DỰ XÉT TỐT NGHIỆP');
            $sheet->setCellValue('A6', mb_strtoupper('TRÌNH ĐỘ: TRUNG CẤP - HỆ: ' . $khoaDaoTao->kdt_he, 'UTF-8'));
            $sheet->setCellValue('B10', 'MSHS');
        } else {
            $sheet->setCellValue('A5', 'DANH SÁCH SINH VIÊN DỰ XÉT TỐT NGHIỆP');
            $sheet->setCellValue('A6', mb_strtoupper('TRÌNH ĐỘ: CAO ĐẲNG - HỆ: ' . $khoaDaoTao->kdt_he, 'UTF-8'));
            $sheet->setCellValue('B10', 'MSSV');
        }
        $currentAddress = $this->nextRowAddress($currentAddress, true);


        // Tổng số Hs
        $sheet->setCellValue($currentAddress, 'Tổng số học sinh: ' . $danhSachSinhVien->count());
        $currentAddress = $this->nextRowAddress($currentAddress, true);

        // Thống kê học lực
        $thongKeHocLuc = null;

        $thongKeHocLuc = $danhSachSinhVien->countBy(function ($sinhVien) {
            if (isset($sinhVien->toanKhoa) && isset($sinhVien->toanKhoa->final_xltn)) {
                return $sinhVien->toanKhoa->final_xltn;
            };
            return 'NA';
        });

        $thongKeMessage = [];
        if (isset($thongKeHocLuc['Xuất sắc'])) {
            $thongKeMessage[] = 'Xuất sắc: ' . $thongKeHocLuc['Xuất sắc'] . ' ' . ($nganhNghe->hdt_id == 4 ? 'SV' : 'HS');
        }
        if (isset($thongKeHocLuc['Giỏi'])) {
            $thongKeMessage[] = 'Giỏi: ' . $thongKeHocLuc['Giỏi'] . ' ' . ($nganhNghe->hdt_id == 4 ? 'SV' : 'HS');
        }
        if (isset($thongKeHocLuc['Khá'])) {
            $thongKeMessage[] = 'Khá: ' . $thongKeHocLuc['Khá'] . ' ' . ($nganhNghe->hdt_id == 4 ? 'SV' : 'HS');
        }
        if (isset($thongKeHocLuc['Trung bình khá'])) {
            $thongKeMessage[] = 'Trung bình khá: ' . $thongKeHocLuc['Trung bình khá'] . ' ' . ($nganhNghe->hdt_id == 4 ? 'SV' : 'HS');
        }
        if (isset($thongKeHocLuc['Trung bình'])) {
            $thongKeMessage[] = 'Trung bình: ' . $thongKeHocLuc['Trung bình'] . ' ' . ($nganhNghe->hdt_id == 4 ? 'SV' : 'HS');
        }

        $soHocSinhXepLoai = $danhSachSinhVien->count();
        $khongXepLoai = 0;
        if (isset($thongKeHocLuc['Rớt']) || isset($thongKeHocLuc['NA'])) {
            $khongXepLoai = (isset($thongKeHocLuc['NA']) ? $thongKeHocLuc['NA'] : 0) + (isset($thongKeHocLuc['Rớt']) ? $thongKeHocLuc['Rớt'] : 0);
            $soHocSinhXepLoai = $soHocSinhXepLoai - $khongXepLoai;
        }
        $exportMessage = 'Dự kiến số ' . ($nganhNghe->hdt_id == 4 ? 'sinh viên' : 'học sinh') . ' tốt nghiệp: ' . $soHocSinhXepLoai . ' ' . ($nganhNghe->hdt_id == 4 ? 'SV' : 'HS') . ', trong đó: ';
        $exportMessage .= join('; ', $thongKeMessage);
        $sheet->setCellValue($currentAddress, $exportMessage);
        $currentAddress = $this->nextRowAddress($currentAddress, true);

        // Không xếp loại học tập: 03 HS.
        $sheet->setCellValue($currentAddress, 'Dự kiến số ' . ($nganhNghe->hdt_id == 4 ? 'sinh viên' : 'học sinh') . ' chưa đạt tốt nghiệp: ' . $khongXepLoai . ' ' . ($nganhNghe->hdt_id == 4 ? 'SV' : 'HS'));
        $currentAddress = $this->nextRowAddress($currentAddress, true);

        // Ghi chú
        $currentAddress = $this->nextRowAddress($currentAddress, true);

        // Các table ghi chú
        $danhSachGhiChuSplit = null;
        $noteColSplit = [];
        $paddingCol = 0;

        $validValues = ['RLHK2', 'ĐTBN1', 'ĐTBHK1', 'RLHK1', 'ĐTBHK2', 'RLN1', 'ĐTBN2', 'RLN2', 'ĐTN', 'ĐTBN3', 'RLN3', 'ĐTBNTK', 'ĐTBCTL'];
        $additionalItems = collect([
            ['key' => 'ĐTB', 'value' => 'Điểm trung bình chung toàn khóa học'],
            ['key' => 'ĐTNCT', 'value' => 'Điểm thi môn Chính trị'],
            ['key' => 'ĐTNLT', 'value' => 'Điểm thi môn Lý thuyết chuyên môn'],
            ['key' => 'ĐTNTH', 'value' => 'Điểm thi môn Thực hành'],
            ['key' => 'ĐTN', 'value' => 'Điểm đánh giá xếp loại tốt nghiệp'],
            ['key' => 'XLTN', 'value' => 'Xếp loại tốt nghiệp']
        ]);
        $danhSachGhiChu = $danhSachGhiChu->concat($additionalItems);

        $danhSachGhiChuSplit = $danhSachGhiChu->reject(function ($item) use ($validValues) {
            return in_array($item['key'], $validValues);
        })->splitIn(3);


        $paddingCol = round($maxUsedCol / 2) - 3;
        $insertRow = $danhSachGhiChuSplit[0]->count();
        $sheet->insertNewRowBefore($this->currentRow($currentAddress), $insertRow);
        $firstNoteRow = $this->currentRow($currentAddress);
        foreach ($danhSachGhiChuSplit as $sIndex => $notes) {
            $currentAddress = $this->currentCol($currentAddress) . $firstNoteRow;
            foreach ($notes as $note) {
                $richText = new \PhpOffice\PhpSpreadsheet\RichText\RichText();
                $payable = $richText->createTextRun($note['key'] . ': ');
                $payable->getFont()->setBold(true);
                $richText->createTextRun($note['value']);
                $sheet->setCellValue($currentAddress, $richText);
                $currentAddress = $this->nextRowAddress($currentAddress);
            }
            $currentAddress = $this->nextColsAddress($currentAddress, $paddingCol + $sIndex * 2);
        }

        // Ký tên
        $splitSignCol = 4;
        $paddingSignCol = round($maxUsedCol / $splitSignCol);
        $currentAddress = $this->nextRowAddress($currentAddress, true);
        $firstSignRow = $this->currentRow($currentAddress);

        // Người lập
        // $currentAddress = $this->nextRowsAddress($currentAddress, 5);
        // $sheet->setCellValue($currentAddress, 'Lê Thị Hồng Phương');

        // Phòng công tác sinh viên
        $columnNumber = 14; // Số thứ tự của cột

        // Chuyển đổi số thứ tự cột thành ký tự
        $columnLetter = chr(64 + $columnNumber);

        // Tạo địa chỉ cột và hàng
        // $currentAddress = $columnLetter . $firstSignRow;
        // $sheet->setCellValue($currentAddress, 'THƯ KÍ');
        // $sheet->getStyle($currentAddress)->applyFromArray($boldCenterStyle);

        // Phòng công tác sinh viên
        // $currentAddress = $this->currentCol($currentAddress) . $firstSignRow;
        // $currentAddress = $this->nextColsAddress($currentAddress, $paddingSignCol);
        // $sheet->setCellValue($currentAddress, 'PT. KHOA ..............');
        // $sheet->getStyle($currentAddress)->applyFromArray($boldCenterStyle);

        // Phòng công tác sinh viên
        // $currentAddress = $this->currentCol($currentAddress) . $firstSignRow;
        // $currentAddress = $this->nextColsAddress($currentAddress, $paddingSignCol);
        // $sheet->setCellValue($currentAddress, 'PT. PHÒNG CÔNG TÁC');
        // $sheet->getStyle($currentAddress)->applyFromArray($boldCenterStyle);
        // $currentAddress = $this->nextRowAddress($currentAddress);
        // $sheet->setCellValue($currentAddress, 'HỌC SINH, SINH VIÊN');
        // $sheet->getStyle($currentAddress)->applyFromArray($boldCenterStyle);
        // $currentAddress = $this->nextRowsAddress($currentAddress, 4);
        // $sheet->setCellValue($currentAddress, 'Trần Văn Kiểu');
        // $sheet->getStyle($currentAddress)->applyFromArray($centerStyle);

        // Phòng đào tạo
        // $currentAddress = $this->currentCol($currentAddress) . $firstSignRow;
        // $currentAddress = $this->nextColsAddress($currentAddress, $paddingSignCol);
        // $sheet->setCellValue($currentAddress, 'PTP. PHÒNG ĐÀO TẠO');
        // $sheet->getStyle($currentAddress)->applyFromArray($boldCenterStyle);

        // $currentAddress = $this->nextRowsAddress($currentAddress, 5);
        // $sheet->setCellValue($currentAddress, 'Hồ Văn Chương');
        // $sheet->getStyle($currentAddress)->applyFromArray($centerStyle);

        $currentAddress = $this->nextRowAddress($currentAddress, true);

        $result->spreadsheet = $spreadsheet;
        $result->fileName = mb_strtoupper('DSDXTN - LỚP: ' . $lopHoc->lh_ten . ' (MÃ LỚP: ' . $lopHoc->lh_ma . ') - NIÊN KHÓA: ' . $khoaDaoTao->kdt_khoa, 'UTF-8');
        return $result;
    }


    public function createSheetMonHocDotThiThiDatTN($filterLhId, $dt_id)
    {
        $result = new \stdClass;
        $spreadsheet = new Spreadsheet();
        $exportData = $this->getKetQuaHocTapTheoDotThi(123456, $filterLhId, $dt_id);
        $dotThi = DotThi::with('dotXetTotNghiep')->where('qlsv_dotthi.dt_id', '=', $dt_id)->first();
        $lopHoc = $exportData['lopHoc'];

        $nienKhoa = $lopHoc->nienKhoa;
        $khoaDaoTao = $lopHoc->khoaDaoTao;
        $heDaotao = $khoaDaoTao->heDaotao;
        $nganhNghe = $khoaDaoTao->nganhNghe;
        $danhSachNamHoc = $exportData['danhSachNamHoc'];
        $danhSachGhiChu = $exportData['notes'];
        $exportSemesters = $exportData['semesters'];
        $danhSachSinhVien = $exportData['danhSachSinhVien'];


        // $filter = new Filters;
        // // lọc danh sinh viên đạt và không đạt
        // $danhSachSinhVien = $filter->LocSinhVienKhongDatTN($danhSachSinhVien, $lopHoc);

        // // lọc danh sinh viên đạt và không đạt điều kiện thi tốt nghiệp
        // $danhSachSinhVien = $filter->LocSinhVienKhongDuDKThiTN($danhSachSinhVien);
        $filter = new Filters;
        // lọc danh sinh viên đạt và không đạt
        $danhSachSinhVien = $filter->KetQuaSinhVienThiDatTN($danhSachSinhVien, $lopHoc->lh_id, $dt_id, $lopHoc);


        if ($lopHoc->lh_nienche != 0) {
            $spreadsheet = IOFactory::load(storage_path($this->template_diemthidautotnghieptheolop2022));
        } else {
            $spreadsheet = IOFactory::load(storage_path($this->template_diemthidautotnghieptheolop2020));
        }
        $spreadsheet->getDefaultStyle()->getFont()->setName('Times New Roman');
        $spreadsheet->getDefaultStyle()->getFont()->setSize(13);

        $centerBorderStyle = [
            'font' => [
                'bold' => false,
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'textRotation' => 0,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ]
        ];

        $centerStyle = [
            'font' => [
                'bold' => false,
                'size' => 13,
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'textRotation' => 0,
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => [
                    'argb' => 'FFFFFFFF',
                ],
            ]
        ];

        $normalStyle = [
            'font' => [
                'bold' => false,
                'size' => 13,
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ]
        ];

        $boldStyle = [
            'font' => [
                'bold' => true,
            ]
        ];

        $boldCenterStyle = [
            'font' => [
                'bold' => true,
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ]
        ];

        $boldLeftStyle = [
            'font' => [
                'bold' => true,
                'size' => 13,
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
            ]
        ];

        $markedColor = 'FFC4CC';

        $sheet = $spreadsheet->getActiveSheet();

        $maxUsedCol = 9;
        // Header danh sách môn học
        $monHocTitleAddress = 'E10';
        $monHocTitleFinalAddress = 'E10';
        $currentAddress = 'E10';

        // số cột phát sinh theo môn học trừ đi 1 (là cột ban đầu)
        $numberInsertCol = $danhSachNamHoc->map(function ($year) {
            // Số môn học + 3 ô (điểm trung bình/ rèn luyện)
            return $year->semesters->map(function ($semester) {
                return $semester->monHoc->count() - 1;
            })->sum();
        })->sum();
        // Thêm cột xếp loại năm
        $numberInsertCol += $danhSachNamHoc->count() + 1;
        $maxUsedCol += $numberInsertCol;
        $sheet->getRowDimension($this->currentRow($currentAddress))->setRowHeight(70);
        $sheet->insertNewColumnBefore($this->currentCol($currentAddress), $numberInsertCol);
        for ($i = 0; $i < $numberInsertCol + 1; $i++) {
            $sheet->getStyle($currentAddress)->applyFromArray($centerBorderStyle);
            $sheet->getStyle($this->nextRowAddress($currentAddress))->applyFromArray($centerBorderStyle);
            $sheet->getStyle($currentAddress)->getAlignment()->setTextRotation(90);
            $sheet->mergeCells($currentAddress . ':' . $this->nextRowAddress($currentAddress));
            $currentAddress = $this->nextColAddress($currentAddress);
        }

        $currentAddress = 'E10';
        foreach ($danhSachNamHoc as $yIndex => $year) {
            foreach ($year->semesters as $sIndex => $semester) {
                $semester->monHoc->each(function ($monHoc) use (&$sheet, &$currentAddress, &$totalTinChi) {
                    if ($monHoc->mh_tichluy) {
                        $totalTinChi += $monHoc->mh_sodonvihoctrinh;
                    }

                    // Mã môn học
                    $sheet->setCellValue($currentAddress, $monHoc->mh_tichluy ? $monHoc->mh_ma : $monHoc->mh_ma . ' (*)');
                    $sheet->getStyle($currentAddress)->getAlignment()->setTextRotation(90);
                    $sheet->getStyle($currentAddress)->applyFromArray([
                        'font' => [
                            'bold' => true
                        ]
                    ]);
                    $sheet->getColumnDimension($this->currentCol($currentAddress))->setWidth(5);

                    // Số tín chỉ của môn học
                    $tinChiAddress = $this->nextRowAddress($this->nextRowAddress($currentAddress));
                    $sheet->setCellValue($tinChiAddress, $monHoc->mh_sodonvihoctrinh);

                    $currentAddress = $this->nextColAddress($currentAddress);
                });
            }
        }
        $sheet->setCellValue(
            $this->nextRowAddress($this->nextRowAddress($this->nextColAddress($this->nextColAddress($currentAddress)))),
            $totalTinChi
        );

        //end điền header

        $sinhVienAddress = 'A13';
        $currentAddress = $sinhVienAddress;
        $sheet->insertNewRowBefore($this->currentRow($currentAddress), $danhSachSinhVien->count() - 1);

        foreach ($danhSachSinhVien->values() as $sIndex => $sinhVien) {

            // 1. Stt
            $sheet->setCellValue($currentAddress, $sIndex + 1);
            $sheet->getStyle($currentAddress)->applyFromArray($centerBorderStyle);
            $currentAddress = $this->nextColAddress($currentAddress);

            // 2. Mã số
            $sheet->setCellValue($currentAddress, $sinhVien->sv_ma);
            $sheet->getStyle($currentAddress)->applyFromArray($normalStyle);
            $currentAddress = $this->nextColAddress($currentAddress);

            // 3. Họ
            $sheet->setCellValue($currentAddress, $sinhVien->sv_ho);
            $sheet->getStyle($currentAddress)->applyFromArray($normalStyle);
            $currentAddress = $this->nextColAddress($currentAddress);

            $sheet->setCellValue($currentAddress, $sinhVien->sv_ten);
            $sheet->getStyle($currentAddress)->applyFromArray($normalStyle);
            $currentAddress = $this->nextColAddress($currentAddress);

            foreach ($sinhVien->years as $yIndex => $year) {
                foreach ($year->semesters as $semester) {
                    // 4. Điểm các môn trong học kỳ
                    foreach ($semester->monHoc as $monHoc) {
                        if ($monHoc->ketQua) {
                            $final = $monHoc->ketQua->display_score;
                            $sheet->setCellValue($currentAddress, $final != null ? number_format($final, 1) : '');
                        }
                        $sheet->getStyle($currentAddress)->getNumberFormat()->setFormatCode('0.0');
                        $sheet->getStyle($currentAddress)->applyFromArray($centerStyle);
                        if ($final < 5) {
                            $sheet->getStyle($currentAddress)->getFill()
                                ->setFillType(Fill::FILL_SOLID)
                                ->getStartColor()->setARGB($markedColor);
                        }
                        $currentAddress = $this->nextColAddress($currentAddress);
                    }
                }
            }

            //Rèn luyên toàn khóa
            $sheet->getStyle($currentAddress)->applyFromArray($centerStyle);
            $sheet->setCellValue($currentAddress, $sinhVien->toanKhoa->diemRenLuyen);
            $currentAddress = $this->nextColAddress($currentAddress);

            //Tính chỉ tích lủy
            $sheet->getStyle($currentAddress)->applyFromArray($centerStyle);
            $sheet->setCellValue($currentAddress, $sinhVien->toanKhoa->tinChiTichLuy);
            $currentAddress = $this->nextColAddress($currentAddress);

            //Điễm trung bình
            $sheet->getStyle($currentAddress)->applyFromArray($centerStyle);
            if (!isset($sinhVien->toanKhoa->avg) || $sinhVien->toanKhoa->avg < 5) {
                $sheet->getStyle($currentAddress)->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setARGB($markedColor);
            }
            $sheet->setCellValue($currentAddress, isset($sinhVien->toanKhoa->avg) ? $sinhVien->toanKhoa->avg : '');
            $currentAddress = $this->nextColAddress($currentAddress);

            $datTotNghep = true;
            if ($sinhVien->khoaluanlan1 != -1) {
                $sheet->getStyle($currentAddress)->applyFromArray($centerStyle);
                $sheet->setCellValue($currentAddress, $sinhVien->khoaluanlan1);
                if ($sinhVien->khoaluanlan1 < 5) {
                    $datTotNghep = false;
                    $sheet->getStyle($currentAddress)->getFill()
                        ->setFillType(Fill::FILL_SOLID)
                        ->getStartColor()->setARGB($markedColor);
                }
            }
            $currentAddress = $this->nextColAddress($currentAddress);

            if ($sinhVien->khoaluanlan2 != -1) {
                $sheet->getStyle($currentAddress)->applyFromArray($centerStyle);
                $sheet->setCellValue($currentAddress, $sinhVien->khoaluanlan2);
                if ($sinhVien->khoaluanlan2 < 5) {
                    $datTotNghep = false;
                    $sheet->getStyle($currentAddress)->getFill()
                        ->setFillType(Fill::FILL_SOLID)
                        ->getStartColor()->setARGB($markedColor);
                }
            }
            $currentAddress = $this->nextColAddress($currentAddress);

            if ($sinhVien->khoaluanlan3 != -1) {
                $sheet->getStyle($currentAddress)->applyFromArray($centerStyle);
                $sheet->setCellValue($currentAddress, $sinhVien->khoaluanlan3);
                if ($sinhVien->khoaluanlan3 < 5) {
                    $datTotNghep = false;
                    $sheet->getStyle($currentAddress)->getFill()
                        ->setFillType(Fill::FILL_SOLID)
                        ->getStartColor()->setARGB($markedColor);
                }
            }
            $currentAddress = $this->nextColAddress($currentAddress);

            if ($lopHoc->lh_nienche == 0) {
                if ($sinhVien->chinhtrilan1 != -1) {
                    $sheet->getStyle($currentAddress)->applyFromArray($centerStyle);
                    $sheet->setCellValue($currentAddress, $sinhVien->chinhtrilan1);
                    if ($sinhVien->chinhtrilan1 < 5) {
                        $datTotNghep = false;
                        $sheet->getStyle($currentAddress)->getFill()
                            ->setFillType(Fill::FILL_SOLID)
                            ->getStartColor()->setARGB($markedColor);
                    }
                }
                $currentAddress = $this->nextColAddress($currentAddress);
            }

            if ($sinhVien->lythuyetlan1 != -1) {
                $sheet->getStyle($currentAddress)->applyFromArray($centerStyle);
                $sheet->setCellValue($currentAddress, $sinhVien->lythuyetlan1);
                if ($sinhVien->lythuyetlan1 < 5) {
                    $datTotNghep = false;
                    $sheet->getStyle($currentAddress)->getFill()
                        ->setFillType(Fill::FILL_SOLID)
                        ->getStartColor()->setARGB($markedColor);
                }
            }
            $currentAddress = $this->nextColAddress($currentAddress);

            if ($sinhVien->thuchanhlan1 != -1) {
                $sheet->getStyle($currentAddress)->applyFromArray($centerStyle);
                $sheet->setCellValue($currentAddress, $sinhVien->thuchanhlan1);
                if ($sinhVien->thuchanhlan1 < 5) {
                    $datTotNghep = false;
                    $sheet->getStyle($currentAddress)->getFill()
                        ->setFillType(Fill::FILL_SOLID)
                        ->getStartColor()->setARGB($markedColor);
                }
            }
            $currentAddress = $this->nextColAddress($currentAddress);

            if ($lopHoc->lh_nienche == 0) {
                if ($sinhVien->chinhtrilan2 != -1) {
                    $sheet->getStyle($currentAddress)->applyFromArray($centerStyle);
                    $sheet->setCellValue($currentAddress, $sinhVien->chinhtrilan2);
                    if ($sinhVien->chinhtrilan2 < 5) {
                        $datTotNghep = false;
                        $sheet->getStyle($currentAddress)->getFill()
                            ->setFillType(Fill::FILL_SOLID)
                            ->getStartColor()->setARGB($markedColor);
                    }
                }
                $currentAddress = $this->nextColAddress($currentAddress);
            }

            if ($sinhVien->lythuyetlan2 != -1) {
                $sheet->getStyle($currentAddress)->applyFromArray($centerStyle);
                $sheet->setCellValue($currentAddress, $sinhVien->lythuyetlan2);
                if ($sinhVien->lythuyetlan2 < 5) {
                    $datTotNghep = false;
                    $sheet->getStyle($currentAddress)->getFill()
                        ->setFillType(Fill::FILL_SOLID)
                        ->getStartColor()->setARGB($markedColor);
                }
            }
            $currentAddress = $this->nextColAddress($currentAddress);

            if ($sinhVien->thuchanhlan2 != -1) {
                $sheet->getStyle($currentAddress)->applyFromArray($centerStyle);
                $sheet->setCellValue($currentAddress, $sinhVien->thuchanhlan2);
                if ($sinhVien->thuchanhlan2 < 5) {
                    $datTotNghep = false;
                    $sheet->getStyle($currentAddress)->getFill()
                        ->setFillType(Fill::FILL_SOLID)
                        ->getStartColor()->setARGB($markedColor);
                }
            }
            $currentAddress = $this->nextColAddress($currentAddress);

            if ($lopHoc->lh_nienche == 0) {
                if ($sinhVien->chinhtrilan3 != -1) {
                    $sheet->getStyle($currentAddress)->applyFromArray($centerStyle);
                    $sheet->setCellValue($currentAddress, $sinhVien->chinhtrilan3);
                    if ($sinhVien->chinhtrilan3 < 5) {
                        $datTotNghep = false;
                        $sheet->getStyle($currentAddress)->getFill()
                            ->setFillType(Fill::FILL_SOLID)
                            ->getStartColor()->setARGB($markedColor);
                    }
                }
                $currentAddress = $this->nextColAddress($currentAddress);
            }

            if ($sinhVien->lythuyetlan3 != -1) {
                $sheet->getStyle($currentAddress)->applyFromArray($centerStyle);
                $sheet->setCellValue($currentAddress, $sinhVien->lythuyetlan3);
                if ($sinhVien->lythuyetlan3 < 5) {
                    $datTotNghep = false;
                    $sheet->getStyle($currentAddress)->getFill()
                        ->setFillType(Fill::FILL_SOLID)
                        ->getStartColor()->setARGB($markedColor);
                }
            }
            $currentAddress = $this->nextColAddress($currentAddress);

            if ($sinhVien->thuchanhlan3 != -1) {
                $sheet->getStyle($currentAddress)->applyFromArray($centerStyle);
                $sheet->setCellValue($currentAddress, $sinhVien->thuchanhlan3);
                if ($sinhVien->thuchanhlan3 < 5) {
                    $datTotNghep = false;
                    $sheet->getStyle($currentAddress)->getFill()
                        ->setFillType(Fill::FILL_SOLID)
                        ->getStartColor()->setARGB($markedColor);
                }
            }
            $currentAddress = $this->nextColAddress($currentAddress);


            if (isset($sinhVien->toanKhoa->avg_totnghiep)) {
                $sheet->setCellValue($currentAddress, $sinhVien->toanKhoa->avg_totnghiep);
                if ($sinhVien->toanKhoa->avg_totnghiep < 5) {
                    $datTotNghep = false;
                    $sheet->getStyle($currentAddress)->getFill()
                        ->setFillType(Fill::FILL_SOLID)
                        ->getStartColor()->setARGB($markedColor);
                }
            }
            $currentAddress = $this->nextColAddress($currentAddress);

            $sheet->getStyle($currentAddress)->applyFromArray($centerBorderStyle);
            if (isset($sinhVien->toanKhoa->hocLucTN) && $sinhVien->toanKhoa->hocLucTN != 'Rớt') {
                if ($datTotNghep) {
                    // Tạo địa chỉ cột và hàng
                    $columnDimensionCurrent = substr($currentAddress, 0, -strlen($currentAddress) + strspn(strrev($currentAddress), '0123456789'));
                    $sheet->getColumnDimension($columnDimensionCurrent)->setAutoSize(true);
                    $sheet->setCellValue($currentAddress, $sinhVien->toanKhoa->hocLucTN);
                }

                if ($sinhVien->toanKhoa->hocLucTN < 5) {
                    $datTotNghep = false;
                    $sheet->getStyle($currentAddress)->getFill()
                        ->setFillType(Fill::FILL_SOLID)
                        ->getStartColor()->setARGB($markedColor);
                }
            }

            $currentAddress = $this->nextColAddress($currentAddress);

            $ghiChuThiLai = "";

            $currentAddress = 'A' . ($sIndex + 14);
        }

        //Để phía cuối
        $sheet->setCellValue('A7', mb_strtoupper('LỚP: ' . $lopHoc->lh_ten . ' (MÃ LỚP: ' . $lopHoc->lh_ma . ') - KHÓA ' . $khoaDaoTao->kdt_ten . ' - NIÊN KHÓA: ' . $khoaDaoTao->kdt_khoa, 'UTF-8'));
        $sheet->setCellValue('A8', mb_strtoupper('ĐỢT XÉT: ' . $dotThi->dotXetTotNghiep[0]->dxtn_ten, 'UTF-8'));
        if ($nganhNghe->hdt_id == 5) {
            $sheet->setCellValue('A5', 'DANH SÁCH HỌC SINH THI ĐẠT TỐT NGHIỆP');
            $sheet->setCellValue('A6', mb_strtoupper('TRÌNH ĐỘ: TRUNG CẤP - HỆ: ' . $khoaDaoTao->kdt_he, 'UTF-8'));
            $sheet->setCellValue('B10', 'MSHS');
        } else {
            $sheet->setCellValue('A5', 'DANH SÁCH SINH VIÊN THI ĐẠT TỐT NGHIỆP');
            $sheet->setCellValue('A6', mb_strtoupper('TRÌNH ĐỘ: CAO ĐẲNG - HỆ: ' . $khoaDaoTao->kdt_he, 'UTF-8'));
            $sheet->setCellValue('B10', 'MSSV');
        }
        // $currentAddress = $this->nextRowAddress($currentAddress, true);


        // Tổng số Hs
        $sheet->setCellValue($currentAddress, 'Tổng số học sinh: ' . $danhSachSinhVien->count());
        $currentAddress = $this->nextRowAddress($currentAddress, true);

        // Thống kê học lực
        $thongKeHocLuc = null;

        $thongKeHocLuc = $danhSachSinhVien->countBy(function ($sinhVien) {
            if (isset($sinhVien->toanKhoa) && isset($sinhVien->toanKhoa->hocLucTN)) {
                return $sinhVien->toanKhoa->hocLucTN;
            };
            return 'NA';
        });


        $thongKeMessage = [];
        if (isset($thongKeHocLuc['Xuất sắc'])) {
            $thongKeMessage[] = 'Xuất sắc: ' . $thongKeHocLuc['Xuất sắc'] . ' ' . ($nganhNghe->hdt_id == 4 ? 'SV' : 'HS');
        }
        if (isset($thongKeHocLuc['Giỏi'])) {
            $thongKeMessage[] = 'Giỏi: ' . $thongKeHocLuc['Giỏi'] . ' ' . ($nganhNghe->hdt_id == 4 ? 'SV' : 'HS');
        }
        if (isset($thongKeHocLuc['Khá'])) {
            $thongKeMessage[] = 'Khá: ' . $thongKeHocLuc['Khá'] . ' ' . ($nganhNghe->hdt_id == 4 ? 'SV' : 'HS');
        }
        if (isset($thongKeHocLuc['Trung bình khá'])) {
            $thongKeMessage[] = 'Trung bình khá: ' . $thongKeHocLuc['Trung bình khá'] . ' ' . ($nganhNghe->hdt_id == 4 ? 'SV' : 'HS');
        }
        if (isset($thongKeHocLuc['Trung bình'])) {
            $thongKeMessage[] = 'Trung bình: ' . $thongKeHocLuc['Trung bình'] . ' ' . ($nganhNghe->hdt_id == 4 ? 'SV' : 'HS');
        }

        $soHocSinhXepLoai = $danhSachSinhVien->count();
        $khongXepLoai = 0;
        if (isset($thongKeHocLuc['Rớt']) || isset($thongKeHocLuc['NA'])) {
            $khongXepLoai = (isset($thongKeHocLuc['NA']) ? $thongKeHocLuc['NA'] : 0) + (isset($thongKeHocLuc['Rớt']) ? $thongKeHocLuc['Rớt'] : 0);
            $soHocSinhXepLoai = $soHocSinhXepLoai - $khongXepLoai;
        }
        $exportMessage = 'Dự kiến số ' . ($nganhNghe->hdt_id == 4 ? 'sinh viên' : 'học sinh') . ' tốt nghiệp: ' . $soHocSinhXepLoai . ' ' . ($nganhNghe->hdt_id == 4 ? 'SV' : 'HS') . ', trong đó: ';
        $exportMessage .= join('; ', $thongKeMessage);
        $sheet->setCellValue($currentAddress, $exportMessage);
        $currentAddress = $this->nextRowAddress($currentAddress, true);

        // Không xếp loại học tập: 03 HS.
        $sheet->setCellValue($currentAddress, 'Dự kiến số ' . ($nganhNghe->hdt_id == 4 ? 'sinh viên' : 'học sinh') . ' chưa đạt tốt nghiệp: ' . $khongXepLoai . ' ' . ($nganhNghe->hdt_id == 4 ? 'SV' : 'HS'));
        $currentAddress = $this->nextRowAddress($currentAddress, true);

        // Ghi chú
        $currentAddress = $this->nextRowAddress($currentAddress, true);
        $currentAddress = $this->nextRowAddress($currentAddress, true);

        // Các table ghi chú
        $danhSachGhiChuSplit = null;
        $noteColSplit = [];
        $paddingCol = 0;
        $danhSachGhiChuSplit = $danhSachGhiChu->splitIn(3);
        $paddingCol = round($maxUsedCol / 2) - 3;
        $insertRow = $danhSachGhiChuSplit[0]->count();
        $sheet->insertNewRowBefore($this->currentRow($currentAddress), $insertRow);
        $firstNoteRow = $this->currentRow($currentAddress);
        foreach ($danhSachGhiChuSplit as $sIndex => $notes) {
            $currentAddress = $this->currentCol($currentAddress) . $firstNoteRow;
            foreach ($notes as $note) {
                $richText = new \PhpOffice\PhpSpreadsheet\RichText\RichText();
                $payable = $richText->createTextRun($note['key'] . ': ');
                $payable->getFont()->setBold(true);
                $richText->createTextRun($note['value']);
                $sheet->setCellValue($currentAddress, $richText);
                $currentAddress = $this->nextRowAddress($currentAddress);
            }
            $currentAddress = $this->nextColsAddress($currentAddress, $paddingCol + $sIndex * 2);
        }
        $currentAddress = $this->nextRowAddress($currentAddress, true);

        // Ký tên
        $splitSignCol = 4;
        $paddingSignCol = round($maxUsedCol / $splitSignCol);
        $currentAddress = $this->nextRowAddress($currentAddress, true);
        $firstSignRow = $this->currentRow($currentAddress);

        // Người lập
        // $currentAddress = $this->nextRowsAddress($currentAddress, 5);
        // $sheet->setCellValue($currentAddress, 'Lê Thị Hồng Phương');

        // Phòng công tác sinh viên
        $columnNumber = 14; // Số thứ tự của cột

        // Chuyển đổi số thứ tự cột thành ký tự
        $columnLetter = chr(64 + $columnNumber);

        // Tạo địa chỉ cột và hàng
        $currentAddress = $columnLetter . $firstSignRow;
        $sheet->setCellValue($currentAddress, 'THƯ KÍ');
        $sheet->getStyle($currentAddress)->applyFromArray($boldCenterStyle);

        // Phòng công tác sinh viên
        // $currentAddress = $this->currentCol($currentAddress) . $firstSignRow;
        // $currentAddress = $this->nextColsAddress($currentAddress, $paddingSignCol);
        // $sheet->setCellValue($currentAddress, 'PT. KHOA ..............');
        // $sheet->getStyle($currentAddress)->applyFromArray($boldCenterStyle);

        // Phòng công tác sinh viên
        // $currentAddress = $this->currentCol($currentAddress) . $firstSignRow;
        // $currentAddress = $this->nextColsAddress($currentAddress, $paddingSignCol);
        // $sheet->setCellValue($currentAddress, 'PT. PHÒNG CÔNG TÁC');
        // $sheet->getStyle($currentAddress)->applyFromArray($boldCenterStyle);
        // $currentAddress = $this->nextRowAddress($currentAddress);
        // $sheet->setCellValue($currentAddress, 'HỌC SINH, SINH VIÊN');
        // $sheet->getStyle($currentAddress)->applyFromArray($boldCenterStyle);
        // $currentAddress = $this->nextRowsAddress($currentAddress, 4);
        // $sheet->setCellValue($currentAddress, 'Trần Văn Kiểu');
        // $sheet->getStyle($currentAddress)->applyFromArray($centerStyle);

        // Phòng đào tạo
        // $currentAddress = $this->currentCol($currentAddress) . $firstSignRow;
        // $currentAddress = $this->nextColsAddress($currentAddress, $paddingSignCol);
        // $sheet->setCellValue($currentAddress, 'PTP. PHÒNG ĐÀO TẠO');
        // $sheet->getStyle($currentAddress)->applyFromArray($boldCenterStyle);

        // $currentAddress = $this->nextRowsAddress($currentAddress, 5);
        // $sheet->setCellValue($currentAddress, 'Hồ Văn Chương');
        // $sheet->getStyle($currentAddress)->applyFromArray($centerStyle);

        $currentAddress = $this->nextRowAddress($currentAddress, true);

        $result->spreadsheet = $spreadsheet;
        return $result;
    }



    public function createSheetMonHocDotThiFull($filterLhId, $dt_id)
    {
        $result = new \stdClass;
        $spreadsheet = new Spreadsheet();
        // Export data
        // $exportData = $this->getKetQuaHocTapTheoDotThiFull(123456, $filterLhId, $dt_id);
        $dxtn_id = DotThiDotXetTotNghiep::where('dt_id', $dt_id)->first()->dxtn_id;
        $exportData = $this->getKetQuaHocTapTheoDotThiTN(123456, $filterLhId, $dt_id, $dxtn_id);

        // $exportData = $this->getKetQuaHocTapTheoDotXetTN(123456, $filterLhId, $dt_id, $dxtn_id);

        $lopHoc = $exportData['lopHoc'];
        $nienKhoa = $lopHoc->nienKhoa;
        $khoaDaoTao = $lopHoc->khoaDaoTao;
        $heDaotao = $khoaDaoTao->heDaotao;
        $nganhNghe = $khoaDaoTao->nganhNghe;
        $danhSachNamHoc = $exportData['danhSachNamHoc'];
        $danhSachSinhVien = $exportData['danhSachSinhVien'];
        // $danhSachGhiChu = $exportData['notes'];
        $exportSemesters = $exportData['semesters'];
        $dotthi = DotThi::find($dt_id);
        $dxtn = DotXetTotNghiep::find($dxtn_id);

        $note = $exportData['notes'];

        $additionalItems = collect([
            ['key' => 'ĐTB', 'value' => 'Điểm trung bình chung toàn khóa học'],
            ['key' => 'ĐTNCT', 'value' => 'Điểm thi môn Chính trị'],
            ['key' => 'ĐTNLT', 'value' => 'Điểm thi môn Lý thuyết chuyên môn'],
            ['key' => 'ĐTNTH', 'value' => 'Điểm thi môn Thực hành'],
            ['key' => 'ĐTN', 'value' => 'Điểm đánh giá xếp loại tốt nghiệp'],
            ['key' => 'XLTN', 'value' => 'Xếp loại tốt nghiệp']
        ]);

        $danhSachGhiChu = $note->concat($additionalItems);


        $filterSemester = 123456;
        if ($lopHoc->lh_nienche != 0) {
            $spreadsheet = IOFactory::load(storage_path($this->template_diemmonhocdotthitheolopfull2022));
        } else {
            $spreadsheet = IOFactory::load(storage_path($this->template_diemmonhocdotthitheolopfull2020));
        }
        $spreadsheet->getDefaultStyle()->getFont()->setName('Times New Roman');
        $spreadsheet->getDefaultStyle()->getFont()->setSize(13);


        $filter = new Filters();
        $danhSachSinhVien = $filter->LocSinhVienKhongDatTN($danhSachSinhVien, $lopHoc);
        // dd($danhSachSinhVien);


        $nhomSvxtn_khongdattn = collect();
        $nhomSvxtn_vipham = collect();
        $nhomSinhVienKhongDatLoai0 = collect();
        $nhomSinhVienKhongDatLoai1 = collect();
        $nhomSinhVienKhongDatLoai2 = collect();

        // dd($danhSachSinhVien);
        // Lặp qua danh sách sinh viên đã sắp xếp
        foreach ($danhSachSinhVien as $key => $sinhvien) {
            // Số quyết định dự thi tốt nghiệp
            try {
                if ($sinhvien->svd_khongdatloai == null && $sinhvien->svd_dieukien == 1) {
                    $sinhvien->quyetDinhDuThiTN = $dotthi->quyetDinh->qd_ma . "," . date('d/m/Y', strtotime($dotthi->quyetDinh->qd_ngay));
                } else {
                    $sinhvien->quyetDinhDuThiTN = "Chưa đủ ĐK thi TN";
                    $sinhvien->notes = collect();
                }
            } catch (Exception $e) {
                $sinhvien->quyetDinhDuThiTN = "Chưa nhập số quyết định";
            }

            // Học lực tốt nghiệp
            if (isset($sinhvien->toanKhoa->hocLucTN) && $sinhvien->toanKhoa->hocLucTN != 'Rớt' && $sinhvien->svxtn_vipham != 1 && $sinhvien->svd_khongdatloai == null) {
                $sinhvien->toanKhoa->hocLucTN = $sinhvien->toanKhoa->hocLucTN;
            } else {
                $sinhvien->toanKhoa->hocLucTN = "";
            }


            // Số quyết định tốt nghiệp
            try {
                if ($sinhvien->datTotNghep == true && $sinhvien->svxtn_vipham != 1 && $sinhvien->svd_khongdatloai == null) {
                    $sinhvien->quyetDinhTN = $dxtn->quyetDinh->qd_ma . "," . date('d/m/Y', strtotime($dxtn->quyetDinh->qd_ngay));
                } else {
                    $sinhvien->quyetDinhTN = "";
                }
            } catch (Exception $e) {
                $sinhvien->quyetDinhTN = "Chưa nhập số quyết định";
            }

            // Thời gian tối đa hoàn thành chương trình
            if ($sinhvien->datTotNghep == false || $sinhvien->svxtn_vipham == 1 || $sinhvien->svd_khongdatloai != null) {
                if ($sinhvien->svd_dieukien == 1) {
                    $sinhvien->timeHoanThanh = 'Tháng …năm …';
                    $sinhvien->notes = collect();
                } else {
                    $sinhvien->timeHoanThanh = '';
                }
            } else {
                $sinhvien->timeHoanThanh = '';
            }

            // Chưa đủ đk thì avg_totnghiep = 0
            if ($sinhvien->svd_dieukien == 0) {
                $sinhvien->toanKhoa->avg_totnghiep = "";
            }

            // Kiểm tra nếu ghiChu là "Chưa đạt"
            $ghiChuTemp = $sinhvien->svd_ghichu;
            if ($sinhvien->svxtn_vipham == 1) {
                $sinhvien->notes = collect();

                if ($sinhvien->svxtn_vipham != null) {
                    $ghiChuTemp = $sinhvien->svxtn_ghichu;
                }
                $sinhvien->ghiChu = "(Chưa đạt) lý do: " . $ghiChuTemp;
            }

            if ($sinhvien->giamXltn == true) {
                $sinhvien->ghiChu = "Giảm một mức XLTN";
            }


            // Phân nhóm để sắp xếp
            if ($sinhvien->datTotNghep == false && $sinhvien->svd_dieukien == 1) {
                $nhomSvxtn_khongdattn->push($sinhvien);
                unset($danhSachSinhVien[$key]);
            } else if ($sinhvien->datTotNghep == true && $sinhvien->svxtn_vipham == 1) {
                $nhomSvxtn_vipham->push($sinhvien);
                unset($danhSachSinhVien[$key]);
            } else if ($sinhvien->datTotNghep == false && $sinhvien->svd_dieukien == 0) {
                $nhomSinhVienKhongDatLoai0->push($sinhvien);
                unset($danhSachSinhVien[$key]);
            }

            if ($sinhvien->svd_khongdatloai != null) {
                if ($sinhvien->svd_khongdatloai == 1) {
                    $nhomSinhVienKhongDatLoai1->push($sinhvien);
                    unset($danhSachSinhVien[$key]);
                } else if ($sinhvien->svd_khongdatloai == 2) {
                    $nhomSinhVienKhongDatLoai2->push($sinhvien);
                    unset($danhSachSinhVien[$key]);
                }
            }
        }

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


        // Gộp cách nhóm sv lại với nhau
        $danhSachSinhVienDuXetTN = $nhomSvxtn_khongdattn->concat($nhomSvxtn_vipham);
        $lastSinhVien_nhom1_first = $danhSachSinhVienDuXetTN->first();
        if ($lastSinhVien_nhom1_first != null)
            $lastSinhVien_nhom1_first->thickBorderStyle = ["thickBorderStyle" => true, "direction" => "top"]; // Phần tử đầu tiên trong nhóm sẽ có thickBorderStyle

        $danhSachSinhVienThiTN = $nhomSinhVienKhongDatLoai1->concat($nhomSinhVienKhongDatLoai2)->concat($nhomSinhVienKhongDatLoai0);
        $lastSinhVien_nhom2_first = $danhSachSinhVienThiTN->first();
        if ($lastSinhVien_nhom2_first != null)
            $lastSinhVien_nhom2_first->thickBorderStyle = ["thickBorderStyle" => true, "direction" => "top"]; // Phần tử đầu tiên trong nhóm sẽ có thickBorderStyle
        $lastSinhVien_nhom2_last = $danhSachSinhVienThiTN->last();
        if ($lastSinhVien_nhom2_last != null)
            $lastSinhVien_nhom2_last->thickBorderStyle = ["thickBorderStyle" => true, "direction" => "bottom"]; // Phần tử đầu tiên trong nhóm sẽ có thickBorderStyle

        // Gộp toàn bộ các nhóm sv vào 1 ds
        $danhSachSinhVien = $danhSachSinhVien
            ->concat($danhSachSinhVienDuXetTN)
            ->concat($danhSachSinhVienThiTN);

        $centerBorderStyle = [
            'font' => [
                'bold' => false,
                'size' => 13,
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ]
        ];

        $centerStyle = [
            'font' => [
                'bold' => false,
                'size' => 13,
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => [
                    'argb' => 'FFFFFFFF',
                ],
            ]
        ];

        $normalStyle = [
            'font' => [
                'bold' => false,
                'size' => 13,
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ]
        ];

        $boldStyle = [
            'font' => [
                'bold' => true,
                'size' => 13,
            ]
        ];

        $boldLeftStyle = [
            'font' => [
                'bold' => true,
                'size' => 13,
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
                ]
            ]
        ];

        $boldCenterStyle = [
            'font' => [
                'bold' => true,
                'size' => 13,
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ]
        ];

        $boldCenterStyle2 = [
            'font' => [
                'bold' => true,
                'size' => 20,
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ]
        ];

        $thickTopBorderStyle = [
            'borders' => [
                'top' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK,
                ]
            ]
        ];

        $thickBottomBorderStyle = [
            'borders' => [
                'bottom' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK,
                ],
            ],
        ];


        $markedColor = '00a65a';
        $color = [
            'red' => '9C0006',
            'default' => '000000'
        ];

        // $danhSachSinhVien = $danhSachSinhVien->filter(function ($sinhVien){
        //     if($sinhVien->svd_dieukien == 1){
        //         //if($sinhVien->sv_ma == 'TCTP55B06') dd($sinhVien);
        //         return $sinhVien;
        //     }
        // });

        // if ($filterSemester == 123456) {
        //     $danhSachSinhVien = $danhSachSinhVien->sortBy([
        //         function ($a, $b) {
        //             if (isset($a->toanKhoa->avg_totnghiep) && isset($b->toanKhoa->avg_totnghiep)) {
        //                 return $b->toanKhoa->avg_totnghiep <=> $a->toanKhoa->avg_totnghiep;
        //             } else if (isset($b->toanKhoa->avg_totnghiep)) {
        //                 return 1;
        //             } else if (isset($a->toanKhoa->avg_totnghiep)) {
        //                 return -1;
        //             }
        //             return 0;
        //         },
        //         function ($a, $b) {
        //             return $a->sv_ma <=> $b->sv_ma;
        //         }
        //     ]);
        // } else if ($filterSemester > 6 && $filterSemester < 123456) {
        //     $danhSachSinhVien = $danhSachSinhVien->sortBy([
        //         function ($a, $b) {
        //             if (isset($a->years[0]->avg) && isset($b->years[0]->avg)) {
        //                 return $b->years[0]->avg <=> $a->years[0]->avg;
        //             } else if (isset($b->years[0]->avg)) {
        //                 return 1;
        //             } else if (isset($a->years[0]->avg)) {
        //                 return -1;
        //             }
        //             return 0;
        //         },
        //         function ($a, $b) {
        //             return $a->sv_ma <=> $b->sv_ma;
        //         }
        //     ]);
        // } else {
        //     $danhSachSinhVien = $danhSachSinhVien->sortBy([
        //         function ($a, $b) {
        //             if (isset($a->years[0]->semesters[0]->avg) && isset($b->years[0]->semesters[0]->avg)) {
        //                 return $b->years[0]->semesters[0]->avg <=> $a->years[0]->semesters[0]->avg;
        //             } else if (isset($b->years[0]->semesters[0]->avg)) {
        //                 return 1;
        //             } else if (isset($a->years[0]->semesters[0]->avg)) {
        //                 return -1;
        //             }
        //             return 0;
        //         },
        //         function ($a, $b) {
        //             return $a->sv_ma <=> $b->sv_ma;
        //         }
        //     ]);
        // }

        $sheet = $spreadsheet->getActiveSheet();

        // Sheet data
        $sheetData = new \stdClass;
        if ($filterSemester == 123456) {
            $sheetData->title_1 = Str::upper('BẢNG ĐIỂM TỔNG HỢP KẾT QUẢ HỌC TẬP KHÓA HỌC');
            $result->filename = 'KQHT_LOP_' . $lopHoc->lh_ma . '_TOANKHOA.xlsx';
        } else if ($filterSemester > 6) {
            $sheetData->title_1 = Str::upper('BẢNG ĐIỂM TỔNG HỢP KẾT QUẢ HỌC TẬP HỌC NĂM THỨ ' . $exportData['reqYear']);
            $result->filename = 'KQHT_LOP_' . $lopHoc->lh_ma . '_NAM_' . $exportData['reqYear'] . '.xlsx';
        } else {
            $currentHocKy = 1;
            if (count($exportSemesters) == 1) {
                $currentHocKy = intval($exportSemesters[0]);
                if ($currentHocKy > 2) {
                    $currentHocKy = floor($currentHocKy / 2);
                }
            }

            $sheetData->title_1 = Str::upper('BẢNG ĐIỂM TỔNG HỢP KẾT QUẢ HỌC TẬP HỌC KỲ ' . $currentHocKy . ' - NĂM THỨ ' . $exportData['reqYear']);
            $result->filename = 'KQHT_LOP_' . $lopHoc->lh_ma . '_HK' . join(', ', $exportData['semesters']) . '' . '_NAM_' . $exportData['reqYear'] . '.xlsx';
        }
        $sheetData->title_2 = Str::upper('LỚP: ' . $lopHoc->lh_ten . ' (MÃ LỚP: ' . $lopHoc->lh_ma . ') - KHÓA ' . $khoaDaoTao->kdt_khoa);
        $sheetData->title_3 = Str::upper('TRÌNH ĐỘ: ' . $heDaotao->hdt_ten . ' - HỆ: ' . $khoaDaoTao->kdt_he . ' - NIÊN KHÓA: ' . $nienKhoa->nk_ten);

        $maxUsedCol = 9;

        // Set sheet data
        $sheet->setCellValue('B10', $nganhNghe->hdt_id == 4 ? 'MSSV' : 'MSHS');
        $sheet->setCellValue('A5', $sheetData->title_1);
        $sheet->setCellValue('A6', $sheetData->title_2);
        $sheet->setCellValue('A7', $sheetData->title_3);
        // Header danh sách môn học
        $monHocTitleAddress = 'K9';
        $monHocTitleFinalAddress = 'K9';
        $currentAddress = 'K9';

        // số cột phát sinh theo môn học trừ đi 1 (là cột ban đầu)
        $numberInsertCol = $danhSachNamHoc->map(function ($year) {
            // Số môn học + 3 ô (điểm trung bình/ rèn luyện)
            return $year->semesters->map(function ($semester) {
                return $semester->monHoc->count() + 6;
            })->sum();
        })->sum();
        // Thêm cột xếp loại năm
        $numberInsertCol += $danhSachNamHoc->count() - 1;
        $maxUsedCol += $numberInsertCol;

        // Thêm cột
        $sheet->getRowDimension($this->currentRow($currentAddress))->setRowHeight(70);
        $sheet->insertNewColumnBefore($this->currentCol($currentAddress), $numberInsertCol);
        $currentAddress = 'K9';
        $tinChiToanKhoa = 0;
        foreach ($danhSachNamHoc as $yIndex => $year) {
            $tinChiNamHoc = 0;
            foreach ($year->semesters as $sIndex => $semester) {
                $currentHocKy = $sIndex + 1;
                if (count($exportSemesters) == 1) {
                    $currentHocKy = intval($exportSemesters[0]);
                    if ($currentHocKy > 2) {
                        $currentHocKy = floor($currentHocKy / 2);
                    }
                }
                $totalTinChi = 0;
                $semester->monHoc->each(function ($monHoc) use (&$sheet, &$currentAddress, &$totalTinChi) {
                    if ($monHoc->mh_tichluy) {
                        $totalTinChi += $monHoc->mh_sodonvihoctrinh;
                    }
                    // Mã môn học
                    $sheet->setCellValue($currentAddress, $monHoc->mh_tichluy ? $monHoc->mh_ma : $monHoc->mh_ma . ' (*)');
                    $sheet->getStyle($currentAddress)->getAlignment()->setTextRotation(90);
                    $sheet->getColumnDimension($this->currentCol($currentAddress))->setWidth(5);
                    $sheet->mergeCells($currentAddress . ':' . $this->nextRowAddress($currentAddress));

                    // Số tín chỉ của môn học
                    $tinChiAddress = $this->nextRowAddress($this->nextRowAddress($currentAddress));
                    $sheet->setCellValue($tinChiAddress, $monHoc->mh_sodonvihoctrinh);
                    $sheet->getStyle($tinChiAddress)->applyFromArray([
                        'font' => [
                            'bold' => false
                        ]
                    ]);

                    $currentAddress = $this->nextColAddress($currentAddress);
                });

                // Điểm trung bình học kỳ
                $sheet->setCellValue($currentAddress, 'ĐTBCHK' . $currentHocKy);
                $sheet->getStyle($currentAddress)->applyFromArray($boldStyle);
                $sheet->getStyle($currentAddress)->getAlignment()->setTextRotation(90);
                $sheet->getColumnDimension($this->currentCol($currentAddress))->setWidth(5);
                $sheet->mergeCells($currentAddress . ':' . $this->nextRowAddress($currentAddress));

                // Tổng tín chỉ
                $tinChiAddress = $this->nextRowAddress($this->nextRowAddress($currentAddress));
                $sheet->setCellValue($tinChiAddress, $totalTinChi);
                $sheet->mergeCells($currentAddress . ':' . $this->nextRowAddress($currentAddress));
                $tinChiToanKhoa += $totalTinChi;
                $tinChiNamHoc += $totalTinChi;
                $sheet->getStyle($tinChiAddress)->applyFromArray($centerBorderStyle);
                $currentAddress = $this->nextColAddress($currentAddress);

                // Điểm rèn luyện
                $sheet->setCellValue($currentAddress, 'RLHK' . $currentHocKy);
                $sheet->mergeCells($currentAddress . ':' . $this->nextRowAddress($this->nextRowAddress($currentAddress)));
                $sheet->getStyle($currentAddress)->getAlignment()->setTextRotation(90);
                $sheet->getColumnDimension($this->currentCol($currentAddress))->setWidth(5);
                $currentAddress = $this->nextColAddress($currentAddress);

                // Thêm cột xếp loại học kỳ
                // if ($filterSemester <= 6) {
                $sheet->setCellValue($currentAddress, 'XLHK' . $currentHocKy);
                $sheet->mergeCells($currentAddress . ':' . $this->nextRowAddress($this->nextRowAddress($currentAddress)));
                $sheet->getStyle($currentAddress)->getAlignment()->setTextRotation(90);
                $sheet->getColumnDimension($this->currentCol($currentAddress))->setWidth(10);
                $currentAddress = $this->nextColAddress($currentAddress);

                $sheet->setCellValue($currentAddress, 'Các môn TL, HL');
                $sheet->mergeCells($currentAddress . ':' . $this->nextRowAddress($this->nextRowAddress($currentAddress)));
                $sheet->getStyle($currentAddress)->getAlignment()->setTextRotation(90);
                $sheet->getColumnDimension($this->currentCol($currentAddress))->setWidth(20);
                $currentAddress = $this->nextColAddress($currentAddress);

                $sheet->setCellValue($currentAddress, 'TCTL');
                $sheet->mergeCells($currentAddress . ':' . $this->nextRowAddress($this->nextRowAddress($currentAddress)));
                $sheet->getStyle($currentAddress)->getAlignment()->setTextRotation(90);
                $sheet->getColumnDimension($this->currentCol($currentAddress))->setWidth(5);
                $currentAddress = $this->nextColAddress($currentAddress);


                $sheet->setCellValue($currentAddress, 'ĐTBCTL');
                $sheet->mergeCells($currentAddress . ':' . $this->nextRowAddress($this->nextRowAddress($currentAddress)));
                $sheet->getStyle($currentAddress)->getAlignment()->setTextRotation(90);
                $sheet->getColumnDimension($this->currentCol($currentAddress))->setWidth(5);
                $currentAddress = $this->nextColAddress($currentAddress);
                // }
            }

            if ($filterSemester > 6) {
                if ($filterSemester <= 123456) {
                    $labelYear = $filterSemester < 123456 ? $exportData['reqYear'] : $yIndex + 1;
                    // thêm cột điểm trung bình năm
                    $sheet->setCellValue($currentAddress, 'ĐTBN' . $labelYear);
                    $sheet->mergeCells($currentAddress . ':' . $this->nextRowAddress($currentAddress));
                    $sheet->getStyle($currentAddress)->applyFromArray($boldStyle);
                    $sheet->getStyle($currentAddress)->getAlignment()->setTextRotation(90);
                    $sheet->getColumnDimension($this->currentCol($currentAddress))->setWidth(5);
                    // Tổng tín chỉ
                    $tinChiAddress = $this->nextRowAddress($this->nextRowAddress($currentAddress));
                    $sheet->setCellValue($tinChiAddress, $tinChiNamHoc);
                    $currentAddress = $this->nextColAddress($currentAddress);
                }

                if ($filterSemester < 123456) {
                    // thêm cột xếp loại năm học
                    $sheet->setCellValue($currentAddress, 'XLN' . $labelYear);
                    $sheet->mergeCells($currentAddress . ':' . $this->nextRowAddress($currentAddress));
                    $currentAddress = $this->nextColAddress($currentAddress);

                    $sheet->setCellValue($currentAddress, 'RLN' . $labelYear);
                    $sheet->mergeCells($currentAddress . ':' . $this->nextRowAddress($currentAddress));
                    $sheet->getStyle($currentAddress)->getAlignment()->setTextRotation(90);
                    $sheet->getColumnDimension($this->currentCol($currentAddress))->setWidth(5);
                }
            }
        }

        if ($filterSemester == 123456) {
            //     $sheet->setCellValue($currentAddress, 'ĐTBTK');
            //     $sheet->mergeCells($currentAddress . ':' . $this->nextRowAddress($currentAddress));
            //     $sheet->getStyle($currentAddress)->applyFromArray($boldStyle);
            //     $sheet->getStyle($currentAddress)->getAlignment()->setTextRotation(90);
            //     $sheet->getColumnDimension($this->currentCol($currentAddress))->setWidth(5);
            // Tổng tín chỉ
            $tinChiAddress = $this->nextRowAddress($this->nextRowAddress($currentAddress));
            $sheet->setCellValue($tinChiAddress, $tinChiToanKhoa);
            $currentAddress = $this->nextColAddress($currentAddress);

            //     $sheet->setCellValue($currentAddress, 'XLTK');
            //     $sheet->mergeCells($currentAddress . ':' . $this->nextRowAddress($this->nextRowAddress($currentAddress)));
            //     $currentAddress = $this->nextColAddress($currentAddress);

            //     $sheet->setCellValue($currentAddress, 'RLTK');
            //     $sheet->mergeCells($currentAddress . ':' . $this->nextRowAddress($this->nextRowAddress($this->nextRowAddress($currentAddress))));
            //     $sheet->getStyle($currentAddress)->getAlignment()->setTextRotation(90);
            //     $sheet->getColumnDimension($this->currentCol($currentAddress))->setWidth(5);
        }

        $sinhVienAddress = 'A12';
        $currentAddress = $sinhVienAddress;
        $sheet->insertNewRowBefore($this->currentRow($currentAddress), $danhSachSinhVien->count());
        foreach ($danhSachSinhVien as $sIndex => $sinhVien) {
            // if($sinhVien->svd_dieukien != 1){
            //     continue;
            // }
            // 1. Stt
            $sheet->setCellValue($currentAddress, $sIndex + 1);
            $sheet->getStyle($currentAddress)->applyFromArray($centerBorderStyle);
            $currentAddress = $this->nextColAddress($currentAddress);

            // 2. Mã số
            $sheet->setCellValue($currentAddress, $sinhVien->sv_ma);
            $sheet->getStyle($currentAddress)->applyFromArray($normalStyle);
            $currentAddress = $this->nextColAddress($currentAddress);

            // 3. Họ
            $sheet->setCellValue($currentAddress, $sinhVien->sv_ho);
            $sheet->getStyle($currentAddress)->applyFromArray($normalStyle);
            $currentAddress = $this->nextColAddress($currentAddress);

            $sheet->setCellValue($currentAddress, $sinhVien->sv_ten);
            $sheet->getStyle($currentAddress)->applyFromArray($normalStyle);
            $currentAddress = $this->nextColAddress($currentAddress);

            //4 nam / nữ
            $sheet->setCellValue($currentAddress, $sinhVien->sv_gioitinh == 1 ? $sinhVien->sv_ngaysinh : '');
            $sheet->getStyle($currentAddress)->applyFromArray($centerStyle);
            $currentAddress = $this->nextColAddress($currentAddress);

            $sheet->setCellValue($currentAddress, $sinhVien->sv_gioitinh == 0 ? $sinhVien->sv_ngaysinh : '');
            $sheet->getStyle($currentAddress)->applyFromArray($centerStyle);
            $currentAddress = $this->nextColAddress($currentAddress);

            //5 dân tộc
            $sheet->setCellValue($currentAddress, $sinhVien->sv_dantoc);
            $sheet->getStyle($currentAddress)->applyFromArray($centerStyle);
            $currentAddress = $this->nextColAddress($currentAddress);

            //6 địa chỉ
            $sheet->setCellValue($currentAddress, $sinhVien->sv_diachi);
            $sheet->getStyle($currentAddress)->applyFromArray($normalStyle);
            $currentAddress = $this->nextColAddress($currentAddress);

            //7 trình độ văn hóa
            $sheet->setCellValue($currentAddress, $sinhVien->sv_trinhdo);
            $sheet->getStyle($currentAddress)->applyFromArray($centerStyle);
            $currentAddress = $this->nextColAddress($currentAddress);

            //7 sđt
            $sheet->setCellValue($currentAddress, $sinhVien->sv_sdt);
            $sheet->getStyle($currentAddress)->applyFromArray($centerStyle);
            $currentAddress = $this->nextColAddress($currentAddress);

            foreach ($sinhVien->years as $yIndex => $year) {
                foreach ($year->semesters as $semester) {
                    // 4. Điểm các môn trong học kỳ
                    foreach ($semester->monHoc as $monHoc) {
                        $final = -1;
                        if ($monHoc->ketQua) {
                            $final = $monHoc->ketQua->display_score;
                            $sheet->setCellValue($currentAddress, $final != null ? number_format($final, 1) : '');
                        }
                        $sheet->getStyle($currentAddress)->getNumberFormat()->setFormatCode('0.0');
                        $sheet->getStyle($currentAddress)->applyFromArray($centerBorderStyle);
                        // if (!isset($monHoc->ketQua) || $final < 5) {
                        if ($final < 5) {
                            $sheet->getStyle($currentAddress)->getFill()
                                ->setFillType(Fill::FILL_SOLID)
                                ->getStartColor()->setARGB($markedColor);
                        }
                        $currentAddress = $this->nextColAddress($currentAddress);
                    }
                    // 5. Điểm trung bình học kỳ
                    if (isset($semester->avg)) {
                        $sheet->setCellValue($currentAddress, $semester->avg);
                    }
                    $sheet->getStyle($currentAddress)->applyFromArray($centerBorderStyle);
                    $sheet->getStyle($currentAddress)->applyFromArray($boldStyle);
                    $sheet->getStyle($currentAddress)->getNumberFormat()->setFormatCode('0.0');
                    $currentAddress = $this->nextColAddress($currentAddress);

                    // 6. Điểm rèn luyện trong học kỳ
                    if (isset($semester->diemRenLuyen)) {
                        $sheet->setCellValue($currentAddress, $semester->diemRenLuyen);
                    }
                    $sheet->getStyle($currentAddress)->applyFromArray($centerBorderStyle);
                    if (isset($semester->diemRenLuyen) && $semester->diemRenLuyen == 0) {
                        $sheet->getStyle($currentAddress)->getFill()
                            ->setFillType(Fill::FILL_SOLID)
                            ->getStartColor()->setARGB($markedColor);
                    }
                    $currentAddress = $this->nextColAddress($currentAddress);

                    // if ($filterSemester <= 6) {
                    // 7.1. Xếp loại học kỳ
                    if (isset($semester->hocLuc)) {
                        $sheet->setCellValue($currentAddress, $semester->hocLuc);
                    }
                    $sheet->getStyle($currentAddress)->applyFromArray($centerBorderStyle);
                    $sheet->getStyle($currentAddress)->getAlignment()->setTextRotation(0);
                    $currentAddress = $this->nextColAddress($currentAddress);

                    // Các môn TL, HL
                    if (isset($semester->notes)) {
                        $notesTLHL = $semester->notes->map(function ($note) {
                            if (isset($note['value'])) {
                                return $note['type'] . ' (' . $note['value'] . ')';
                            }
                            return $note['type'] . ' (' . $note['key'] . ')';
                        })->join(', ');

                        $sheet->setCellValue($currentAddress, $notesTLHL);
                    }
                    $sheet->getStyle($currentAddress)->applyFromArray($centerBorderStyle);
                    $sheet->getStyle($currentAddress)->getAlignment()->setTextRotation(0);
                    $currentAddress = $this->nextColAddress($currentAddress);

                    // 8.1. Tổng số tín chỉ tích lũy
                    if (isset($semester->tinChiTichLuy)) {
                        $sheet->setCellValue($currentAddress, $semester->tinChiTichLuy);
                    }
                    $sheet->getStyle($currentAddress)->applyFromArray($centerBorderStyle);
                    $sheet->getStyle($currentAddress)->getAlignment()->setTextRotation(0);
                    $currentAddress = $this->nextColAddress($currentAddress);

                    // 9.1. Điểm trung bình chung tích lũy
                    if (isset($semester->tichLuy)) {
                        $sheet->setCellValue($currentAddress, $semester->tichLuy);
                    }
                    $sheet->getStyle($currentAddress)->applyFromArray($centerBorderStyle);
                    $sheet->getStyle($currentAddress)->getAlignment()->setTextRotation(0);
                    $sheet->getStyle($currentAddress)->getNumberFormat()->setFormatCode('0.0');
                    $currentAddress = $this->nextColAddress($currentAddress);
                    // }
                }

                if ($filterSemester > 6) {
                    if ($filterSemester <= 123456) {
                        // Điểm trung bình năm
                        if (isset($year->avg)) {
                            $sheet->setCellValue($currentAddress, $year->avg);
                        }
                        $sheet->getStyle($currentAddress)->applyFromArray($centerBorderStyle);
                        $currentAddress = $this->nextColAddress($currentAddress);
                    }

                    if ($filterSemester < 123456) {
                        // 7.2. Xếp loại năm
                        if (isset($year->hocLuc)) {
                            $sheet->setCellValue($currentAddress, $year->hocLuc);
                        }
                        $sheet->getStyle($currentAddress)->applyFromArray($centerBorderStyle);
                        $sheet->getColumnDimension($this->currentCol($currentAddress))->setWidth(20);
                        $currentAddress = $this->nextColAddress($currentAddress);

                        // Rèn luyện năm
                        if (isset($year->diemRenLuyen)) {
                            $sheet->setCellValue($currentAddress, round($year->diemRenLuyen));
                        }
                        $sheet->getStyle($currentAddress)->getAlignment()->setTextRotation(0);
                        $sheet->getStyle($currentAddress)->applyFromArray($centerBorderStyle);
                        $currentAddress = $this->nextColAddress($currentAddress);

                        // 8.2. Tổng số tín chỉ tích lũy
                        if (isset($year->tinChiTichLuy)) {
                            $sheet->setCellValue($currentAddress, $year->tinChiTichLuy);
                        }
                        $sheet->getStyle($currentAddress)->getAlignment()->setTextRotation(0);
                        $sheet->getStyle($currentAddress)->applyFromArray($centerBorderStyle);
                        $currentAddress = $this->nextColAddress($currentAddress);

                        // 9.2. Điểm trung bình chung tích lũy
                        if (isset($year->tichLuy)) {
                            $sheet->setCellValue($currentAddress, $year->tichLuy);
                        }
                        $sheet->getStyle($currentAddress)->getAlignment()->setTextRotation(0);
                        $sheet->getStyle($currentAddress)->applyFromArray($centerBorderStyle);
                        $sheet->getStyle($currentAddress)->getNumberFormat()->setFormatCode('0.0');
                        $currentAddress = $this->nextColAddress($currentAddress);

                        // 10. Ghi chú
                        // if ($filterSemester <= 4) {
                        //     $svGhiChu = $semester->notes->map(function ($note) {
                        //         if ($note['type'] == 'DL2') {
                        //             return $note['key'] . ' (L2:' . $note['value'] . ')';
                        //         } else {
                        //             return $note['type'] . ' (' . $note['key'] . ')';
                        //         }
                        //     })->join(', ');
                        //     $sheet->setCellValue($currentAddress, $svGhiChu);
                        //     $sheet->getStyle($currentAddress)->applyFromArray($normalStyle);
                        //     $currentAddress = $this->nextColAddress($currentAddress);
                        // }

                        // Điều chỉnh tiến độ học
                        if (isset($year->avg) && $year->avg <= 5 && $year->avg >= 4) {
                            $sheet->setCellValue($currentAddress, 'X');
                        }
                        $sheet->getStyle($currentAddress)->applyFromArray($centerBorderStyle);
                        $currentAddress = $this->nextColAddress($currentAddress);

                        // Buộc thôi học
                        $dtbcCondition = isset($year->avg) && $year->avg < 4;
                        $dtctlCondition = isset($year->tichLuy) && $year->tichLuy < 4;
                        if ($dtbcCondition || $dtctlCondition) {
                            $sheet->setCellValue($currentAddress, 'X');
                        }
                        $sheet->getStyle($currentAddress)->applyFromArray($centerBorderStyle);
                        $currentAddress = $this->nextColAddress($currentAddress);
                    }
                }
            }

            if ($filterSemester == 123456) {

                // Rèn luyện năm
                if (isset($sinhVien->toanKhoa->diemRenLuyen)) {
                    $sheet->setCellValue($currentAddress, round($sinhVien->toanKhoa->diemRenLuyen));
                }
                $sheet->getStyle($currentAddress)->getAlignment()->setTextRotation(0);
                $sheet->getStyle($currentAddress)->applyFromArray($centerBorderStyle);
                $currentAddress = $this->nextColAddress($currentAddress);

                // // 7.2. Xếp loại năm
                // if (isset($sinhVien->toanKhoa->hocLuc)) {
                //     $sheet->setCellValue($currentAddress, $sinhVien->toanKhoa->hocLuc);
                // }
                // $sheet->getStyle($currentAddress)->applyFromArray($centerBorderStyle);
                // $sheet->getColumnDimension($this->currentCol($currentAddress))->setWidth(20);
                // $currentAddress = $this->nextColAddress($currentAddress);



                // 8.2. Tổng số tín chỉ tích lũy
                if (isset($sinhVien->toanKhoa->tinChiTichLuy)) {
                    $sheet->setCellValue($currentAddress, $sinhVien->toanKhoa->tinChiTichLuy);
                }
                $sheet->getStyle($currentAddress)->getAlignment()->setTextRotation(0);
                $sheet->getStyle($currentAddress)->applyFromArray($centerBorderStyle);
                $currentAddress = $this->nextColAddress($currentAddress);

                // 9.2. Điểm trung bình chung tích lũy
                if (isset($sinhVien->toanKhoa->tichLuy)) {
                    $sheet->setCellValue($currentAddress, $sinhVien->toanKhoa->tichLuy);
                }
                $sheet->getStyle($currentAddress)->getAlignment()->setTextRotation(0);
                $sheet->getStyle($currentAddress)->applyFromArray($centerBorderStyle);
                $sheet->getStyle($currentAddress)->getNumberFormat()->setFormatCode('0.0');
                $currentAddress = $this->nextColAddress($currentAddress);

                // Điểm trung bình năm
                if (isset($sinhVien->toanKhoa->avg)) {
                    $sheet->setCellValue($currentAddress, number_format($sinhVien->toanKhoa->avg, 1) . '');
                }
                $sheet->getStyle($currentAddress)->applyFromArray($centerBorderStyle);
                $sheet->getStyle($currentAddress)->applyFromArray($boldStyle);
                $currentAddress = $this->nextColAddress($currentAddress);

                // Số quyết định dự thi tốt nghiệp
                $sheet->setCellValue($currentAddress, $sinhVien->quyetDinhDuThiTN);
                $sheet->getStyle($currentAddress)->applyFromArray($centerBorderStyle);
                $currentAddress = $this->nextColAddress($currentAddress);


                // Điểm đợt thi
                if ($sinhVien->khoaluanlan1 != -1) {
                    $sheet->getStyle($currentAddress)->applyFromArray($centerStyle);
                    $sheet->setCellValue($currentAddress, $sinhVien->khoaluanlan1); // Sử dụng number_format để định dạng số thập phân
                    $sheet->getStyle($currentAddress)->getNumberFormat()->setFormatCode('0.0');
                    $sheet->getStyle($currentAddress)->getFont()->getColor()->setRGB($color["default"]);

                    if ($sinhVien->khoaluanlan1 < 5) {
                        $cellStyle = $sheet->getStyle($currentAddress);
                        $cellStyle->getFill()
                            ->setFillType(Fill::FILL_SOLID)
                            ->getStartColor()->setARGB($markedColor);
                        $cellStyle->getFont()->getColor()->setRGB($color["red"]);
                    }
                } else {
                    $sheet->getStyle($currentAddress)->getFill()
                        ->setFillType(Fill::FILL_SOLID)
                        ->getStartColor()->setARGB($markedColor);
                }
                $currentAddress = $this->nextColAddress($currentAddress);

                if ($sinhVien->khoaluanlan2 != -1) {
                    $sheet->getStyle($currentAddress)->applyFromArray($centerStyle);
                    $sheet->setCellValue($currentAddress, $sinhVien->khoaluanlan2);
                    $sheet->getStyle($currentAddress)->getNumberFormat()->setFormatCode('0.0');
                    $sheet->getStyle($currentAddress)->getFont()->getColor()->setRGB($color["default"]);

                    if ($sinhVien->khoaluanlan2 < 5) {
                        $cellStyle = $sheet->getStyle($currentAddress);
                        $cellStyle->getFill()
                            ->setFillType(Fill::FILL_SOLID)
                            ->getStartColor()->setARGB($markedColor);
                        $cellStyle->getFont()->getColor()->setRGB($color["default"]);
                    }
                } else {
                    $sheet->getStyle($currentAddress)->getFill()
                        ->setFillType(Fill::FILL_SOLID)
                        ->getStartColor()->setARGB($markedColor);
                }
                $currentAddress = $this->nextColAddress($currentAddress);

                if ($sinhVien->khoaluanlan3 != -1) {
                    $sheet->getStyle($currentAddress)->applyFromArray($centerStyle);
                    $sheet->setCellValue($currentAddress, $sinhVien->khoaluanlan3);
                    $sheet->getStyle($currentAddress)->getNumberFormat()->setFormatCode('0.0');
                    $sheet->getStyle($currentAddress)->getFont()->getColor()->setRGB($color["default"]);

                    if ($sinhVien->khoaluanlan3 < 5) {
                        $cellStyle = $sheet->getStyle($currentAddress);
                        $cellStyle->getFill()
                            ->setFillType(Fill::FILL_SOLID)
                            ->getStartColor()->setARGB($markedColor);
                        $cellStyle->getFont()->getColor()->setRGB($color["default"]);
                    }
                } else {
                    $sheet->getStyle($currentAddress)->getFill()
                        ->setFillType(Fill::FILL_SOLID)
                        ->getStartColor()->setARGB($markedColor);
                }
                $currentAddress = $this->nextColAddress($currentAddress);

                if ($lopHoc->lh_nienche == 0) {
                    if ($sinhVien->chinhtrilan1 != -1) {
                        $sheet->getStyle($currentAddress)->applyFromArray($centerStyle);
                        $sheet->setCellValue($currentAddress, $sinhVien->chinhtrilan1);
                        $sheet->getStyle($currentAddress)->getNumberFormat()->setFormatCode('0.0');
                        $sheet->getStyle($currentAddress)->getFont()->getColor()->setRGB($color["default"]);

                        if ($sinhVien->chinhtrilan1 < 5) {
                            $cellStyle = $sheet->getStyle($currentAddress);
                            $cellStyle->getFill()
                                ->setFillType(Fill::FILL_SOLID)
                                ->getStartColor()->setARGB($markedColor);
                            $cellStyle->getFont()->getColor()->setRGB($color["default"]);
                        }
                    } else {
                        $sheet->getStyle($currentAddress)->getFill()
                            ->setFillType(Fill::FILL_SOLID)
                            ->getStartColor()->setARGB($markedColor);
                    }
                    $currentAddress = $this->nextColAddress($currentAddress);
                }

                if ($sinhVien->lythuyetlan1 != -1) {
                    $sheet->getStyle($currentAddress)->applyFromArray($centerStyle);
                    $sheet->setCellValue($currentAddress, $sinhVien->lythuyetlan1);
                    $sheet->getStyle($currentAddress)->getNumberFormat()->setFormatCode('0.0');
                    $sheet->getStyle($currentAddress)->getFont()->getColor()->setRGB($color["default"]);

                    if ($sinhVien->lythuyetlan1 < 5) {
                        $cellStyle = $sheet->getStyle($currentAddress);
                        $cellStyle->getFill()
                            ->setFillType(Fill::FILL_SOLID)
                            ->getStartColor()->setARGB($markedColor);
                        $cellStyle->getFont()->getColor()->setRGB($color["default"]);
                    }
                } else {
                    $sheet->getStyle($currentAddress)->getFill()
                        ->setFillType(Fill::FILL_SOLID)
                        ->getStartColor()->setARGB($markedColor);
                }
                $currentAddress = $this->nextColAddress($currentAddress);

                if ($sinhVien->thuchanhlan1 != -1) {
                    $sheet->getStyle($currentAddress)->applyFromArray($centerStyle);
                    $sheet->setCellValue($currentAddress, $sinhVien->thuchanhlan1);
                    $sheet->getStyle($currentAddress)->getNumberFormat()->setFormatCode('0.0');
                    $sheet->getStyle($currentAddress)->getFont()->getColor()->setRGB($color["default"]);

                    if ($sinhVien->thuchanhlan1 < 5) {
                        $cellStyle = $sheet->getStyle($currentAddress);
                        $cellStyle->getFill()
                            ->setFillType(Fill::FILL_SOLID)
                            ->getStartColor()->setARGB($markedColor);
                        $cellStyle->getFont()->getColor()->setRGB($color["default"]);
                    }
                } else {
                    $sheet->getStyle($currentAddress)->getFill()
                        ->setFillType(Fill::FILL_SOLID)
                        ->getStartColor()->setARGB($markedColor);
                }
                $currentAddress = $this->nextColAddress($currentAddress);

                if ($lopHoc->lh_nienche == 0) {
                    if ($sinhVien->chinhtrilan2 != -1) {
                        $sheet->getStyle($currentAddress)->applyFromArray($centerStyle);
                        $sheet->setCellValue($currentAddress, $sinhVien->chinhtrilan2);
                        $sheet->getStyle($currentAddress)->getNumberFormat()->setFormatCode('0.0');
                        $sheet->getStyle($currentAddress)->getFont()->getColor()->setRGB($color["default"]);

                        if ($sinhVien->chinhtrilan2 < 5) {
                            $cellStyle = $sheet->getStyle($currentAddress);
                            $cellStyle->getFill()
                                ->setFillType(Fill::FILL_SOLID)
                                ->getStartColor()->setARGB($markedColor);
                            $cellStyle->getFont()->getColor()->setRGB($color["default"]);
                        }
                    } else {
                        $sheet->getStyle($currentAddress)->getFill()
                            ->setFillType(Fill::FILL_SOLID)
                            ->getStartColor()->setARGB($markedColor);
                    }
                    $currentAddress = $this->nextColAddress($currentAddress);
                }

                if ($sinhVien->lythuyetlan2 != -1) {
                    $sheet->getStyle($currentAddress)->applyFromArray($centerStyle);
                    $sheet->setCellValue($currentAddress, $sinhVien->lythuyetlan2);
                    $sheet->getStyle($currentAddress)->getNumberFormat()->setFormatCode('0.0');
                    $sheet->getStyle($currentAddress)->getFont()->getColor()->setRGB($color["default"]);

                    if ($sinhVien->lythuyetlan2 < 5) {
                        $cellStyle = $sheet->getStyle($currentAddress);
                        $cellStyle->getFill()
                            ->setFillType(Fill::FILL_SOLID)
                            ->getStartColor()->setARGB($markedColor);
                        $cellStyle->getFont()->getColor()->setRGB($color["default"]);
                    }
                } else {
                    $sheet->getStyle($currentAddress)->getFill()
                        ->setFillType(Fill::FILL_SOLID)
                        ->getStartColor()->setARGB($markedColor);
                }
                $currentAddress = $this->nextColAddress($currentAddress);

                if ($sinhVien->thuchanhlan2 != -1) {
                    $sheet->getStyle($currentAddress)->applyFromArray($centerStyle);
                    $sheet->setCellValue($currentAddress, $sinhVien->thuchanhlan2);
                    $sheet->getStyle($currentAddress)->getNumberFormat()->setFormatCode('0.0');
                    $sheet->getStyle($currentAddress)->getFont()->getColor()->setRGB($color["default"]);

                    if ($sinhVien->thuchanhlan2 < 5) {
                        $cellStyle = $sheet->getStyle($currentAddress);
                        $cellStyle->getFill()
                            ->setFillType(Fill::FILL_SOLID)
                            ->getStartColor()->setARGB($markedColor);
                        $cellStyle->getFont()->getColor()->setRGB($color["default"]);
                    }
                } else {
                    $sheet->getStyle($currentAddress)->getFill()
                        ->setFillType(Fill::FILL_SOLID)
                        ->getStartColor()->setARGB($markedColor);
                }
                $currentAddress = $this->nextColAddress($currentAddress);

                if ($lopHoc->lh_nienche == 0) {
                    if ($sinhVien->chinhtrilan3 != -1) {
                        $sheet->getStyle($currentAddress)->applyFromArray($centerStyle);
                        $sheet->setCellValue($currentAddress, $sinhVien->chinhtrilan3);
                        $sheet->getStyle($currentAddress)->getNumberFormat()->setFormatCode('0.0');
                        $cellStyle->getFont()->getColor()->setRGB($color["default"]);

                        if ($sinhVien->chinhtrilan3 < 5) {
                            $cellStyle = $sheet->getStyle($currentAddress);
                            $cellStyle->getFill()
                                ->setFillType(Fill::FILL_SOLID)
                                ->getStartColor()->setARGB($markedColor);
                            $cellStyle->getFont()->getColor()->setRGB($color["default"]);
                        }
                    } else {
                        $sheet->getStyle($currentAddress)->getFill()
                            ->setFillType(Fill::FILL_SOLID)
                            ->getStartColor()->setARGB($markedColor);
                    }
                    $currentAddress = $this->nextColAddress($currentAddress);
                }

                if ($sinhVien->lythuyetlan3 != -1) {
                    $sheet->getStyle($currentAddress)->applyFromArray($centerStyle);
                    $sheet->setCellValue($currentAddress, $sinhVien->lythuyetlan3);
                    $sheet->getStyle($currentAddress)->getNumberFormat()->setFormatCode('0.0');
                    $cellStyle->getFont()->getColor()->setRGB($color["default"]);

                    if ($sinhVien->lythuyetlan3 < 5) {
                        $cellStyle = $sheet->getStyle($currentAddress);
                        $cellStyle->getFill()
                            ->setFillType(Fill::FILL_SOLID)
                            ->getStartColor()->setARGB($markedColor);
                        $cellStyle->getFont()->getColor()->setRGB($color["default"]);
                    }
                } else {
                    $sheet->getStyle($currentAddress)->getFill()
                        ->setFillType(Fill::FILL_SOLID)
                        ->getStartColor()->setARGB($markedColor);
                }
                $currentAddress = $this->nextColAddress($currentAddress);

                if ($sinhVien->thuchanhlan3 != -1) {
                    $sheet->getStyle($currentAddress)->applyFromArray($centerStyle);
                    $sheet->setCellValue($currentAddress, $sinhVien->thuchanhlan3);
                    $sheet->getStyle($currentAddress)->getNumberFormat()->setFormatCode('0.0');
                    $cellStyle->getFont()->getColor()->setRGB($color["default"]);

                    if ($sinhVien->thuchanhlan3 < 5) {
                        $cellStyle = $sheet->getStyle($currentAddress);
                        $cellStyle->getFill()
                            ->setFillType(Fill::FILL_SOLID)
                            ->getStartColor()->setARGB($markedColor);
                        $cellStyle->getFont()->getColor()->setRGB($color["default"]);
                    }
                } else {
                    $sheet->getStyle($currentAddress)->getFill()
                        ->setFillType(Fill::FILL_SOLID)
                        ->getStartColor()->setARGB($markedColor);
                }
                $currentAddress = $this->nextColAddress($currentAddress);

                $sheet->getStyle($currentAddress)->applyFromArray($centerBorderStyle);
                if (isset($sinhVien->toanKhoa->avg_totnghiep)) {
                    $sheet->setCellValue($currentAddress, $sinhVien->toanKhoa->avg_totnghiep);
                    $sheet->getStyle($currentAddress)->getNumberFormat()->setFormatCode('0.0');

                    if ($sinhVien->toanKhoa->avg_totnghiep < 5) {
                        $sheet->getStyle($currentAddress)->getFill()
                            ->setFillType(Fill::FILL_SOLID)
                            ->getStartColor()->setARGB($markedColor);
                    }
                }
                $currentAddress = $this->nextColAddress($currentAddress);

                //Các môn thi học lại
                $svGhiChu = $sinhVien->notes->map(function ($note) {
                    if ($note['type'] == 'DL3') {
                        return $note['key'] . ' (DiemL3:' . number_format($note['value'], 1) . ')';
                    } else if ($note['type'] == 'DTL3') {
                        return $note['key'] . ' (DiemThiL3:' . number_format($note['value'], 1) . ')';
                    } else if ($note['type'] == 'DL2') {
                        return $note['key'] . ' (DiemL2:' . number_format($note['value'], 1) . ')';
                    } else if ($note['type'] == 'DTL2') {
                        return $note['key'] . ' (DiemThiL2:' . number_format($note['value'], 1) . ')';
                    } else if ($note['type'] == 'DL1') {
                        return $note['key'] . ' (DiemL1:' . number_format($note['value'], 1) . ')';
                    } else if ($note['type'] == 'DTL1') {
                        return $note['key'] . ' (DiemThiL1:' . number_format($note['value'], 1) . ')';
                    } else {
                        if (isset($note['value'])) {
                            return $note['type'] . ' (' . $note['value'] . ')';
                        }
                        return $note['type'] . ' (' . $note['key'] . ')';
                    }
                })->join(', ');


                $sheet->setCellValue($currentAddress, $svGhiChu);
                // $sheet->setCellValue($currentAddress, $sinhVien->toanKhoa->ghiChuThiLai);
                $spreadsheet->getActiveSheet()->getRowDimension($this->currentRow($currentAddress))->setRowHeight(50);
                $sheet->getStyle($currentAddress)->applyFromArray($boldStyle);
                $currentAddress = $this->nextColAddress($currentAddress);

                // Học lực tốt nghiệp
                $sheet->setCellValue($currentAddress, $sinhVien->toanKhoa->hocLucTN);
                $sheet->getStyle($currentAddress)->applyFromArray($centerBorderStyle);
                $currentAddress = $this->nextColAddress($currentAddress);


                // Số quyết định tốt nghiệp
                $sheet->setCellValue($currentAddress, $sinhVien->quyetDinhTN);
                $sheet->getStyle($currentAddress)->applyFromArray($centerBorderStyle);
                $currentAddress = $this->nextColAddress($currentAddress);


                // Thời gian tối đa hoàn thành chương trình
                $sheet->setCellValue($currentAddress, $sinhVien->timeHoanThanh);
                $sheet->getStyle($currentAddress)->applyFromArray($normalStyle);
                $currentAddress = $this->nextColAddress($currentAddress);
            }



            $svGhiChu = $sinhVien->ghiChu;

            if ($sinhVien->svd_dieukien == 1) {
                $sheet->setCellValue($currentAddress, $svGhiChu);
            } else {
                $sheet->setCellValue($currentAddress, "");
            }
            $sheet->getStyle($currentAddress)->applyFromArray($normalStyle);

            // set ThickTopBorder ngăn cách giữa sv đạt và không đạt
            if (isset($sinhVien->thickBorderStyle["thickBorderStyle"]) && $sinhVien->thickBorderStyle["thickBorderStyle"] == true) {
                if ($sinhVien->thickBorderStyle["direction"] == "top") {
                    $applyThickTopBorderStyle = $thickTopBorderStyle;
                } else if ($sinhVien->thickBorderStyle["direction"] == "bottom") {
                    $applyThickTopBorderStyle = $thickBottomBorderStyle;
                }


                preg_match('/([A-Za-z]+)([0-9]+)/', $currentAddress, $matches);
                // $matches[1] chứa chữ, $matches[2] chứa số
                // $nameCol = $matches[1];
                $numCol = $matches[2];

                $cellRangeThickTopBorder = "A{$numCol}:{$currentAddress}";
                $sheet->getStyle($cellRangeThickTopBorder)->applyFromArray($applyThickTopBorderStyle);
            }

            $currentAddress = $this->nextRowAddress($currentAddress, true);
        }

        $currentAddress = $this->nextRowAddress($currentAddress);
        $currentAddress = $this->nextRowAddress($currentAddress, true);


        // Tổng số học sinh
        $sheet->setCellValue($currentAddress, 'Tổng số ' . ($nganhNghe->hdt_id == 4 ? 'sinh viên' : 'học sinh') . ' được xét: ' . $danhSachSinhVien->count());
        $currentAddress = $this->nextRowAddress($currentAddress, true);

        // Thống kê học lực
        $thongKeHocLuc = null;

        $thongKeHocLuc = $danhSachSinhVien->countBy(function ($sinhVien) {
            if (isset($sinhVien->toanKhoa) && isset($sinhVien->toanKhoa->hocLucTN)) {
                return $sinhVien->toanKhoa->hocLucTN;
            };
            return 'NA';
        });

        $thongKeMessage = [];
        if (isset($thongKeHocLuc['Xuất sắc'])) {
            $thongKeMessage[] = 'Xuất sắc: ' . $thongKeHocLuc['Xuất sắc'] . ' ' . ($nganhNghe->hdt_id == 4 ? 'SV' : 'HS');
        }
        if (isset($thongKeHocLuc['Giỏi'])) {
            $thongKeMessage[] = 'Giỏi: ' . $thongKeHocLuc['Giỏi'] . ' ' . ($nganhNghe->hdt_id == 4 ? 'SV' : 'HS');
        }
        if (isset($thongKeHocLuc['Khá'])) {
            $thongKeMessage[] = 'Khá: ' . $thongKeHocLuc['Khá'] . ' ' . ($nganhNghe->hdt_id == 4 ? 'SV' : 'HS');
        }
        if (isset($thongKeHocLuc['Trung bình khá'])) {
            $thongKeMessage[] = 'Trung bình khá: ' . $thongKeHocLuc['Trung bình khá'] . ' ' . ($nganhNghe->hdt_id == 4 ? 'SV' : 'HS');
        }
        if (isset($thongKeHocLuc['Trung bình'])) {
            $thongKeMessage[] = 'Trung bình: ' . $thongKeHocLuc['Trung bình'] . ' ' . ($nganhNghe->hdt_id == 4 ? 'SV' : 'HS');
        }
        if (isset($thongKeHocLuc['Yếu'])) {
            $thongKeMessage[] = 'Yếu: ' . $thongKeHocLuc['Yếu'] . ' ' . ($nganhNghe->hdt_id == 4 ? 'SV' : 'HS');
        }


        // $soHocSinhXepLoai = $danhSachSinhVien->count();
        // $khongXepLoai = 0;
        // if (isset($thongKeHocLuc['NA']) || isset($thongKeHocLuc['Rớt'])) {
        //     $khongXepLoai = (isset($thongKeHocLuc['NA']) ? $thongKeHocLuc['NA'] : 0) + (isset($thongKeHocLuc['Rớt']) ? $thongKeHocLuc['Rớt'] : 0);
        //     $soHocSinhXepLoai = $soHocSinhXepLoai - $khongXepLoai;
        // }
        // $exportMessage = 'Xếp loại học tập học kỳ ' . $filterSemester . ': ' . $soHocSinhXepLoai . ' '.($nganhNghe->hdt_id == 4 ? 'SV' : 'HS').', trong đó: ';
        // if ($filterSemester > 6) {
        //     $exportMessage = 'Xếp loại học tập năm ' . $exportData['reqYear'] . ': ' . $soHocSinhXepLoai . ' '.($nganhNghe->hdt_id == 4 ? 'SV' : 'HS').', trong đó: ';
        // }

        $khongXepLoai = $danhSachSinhVienDuXetTN->count() + $danhSachSinhVienThiTN->count(); // ds sv đã tách ra để sort
        $soHocSinhXepLoai = $danhSachSinhVien->count();
        $soHocSinhXepLoai = $soHocSinhXepLoai - $khongXepLoai;
        // if (isset($thongKeHocLuc['Rớt']) || isset($thongKeHocLuc['NA'])) {
        //     $khongXepLoai = (isset($thongKeHocLuc['NA']) ? $thongKeHocLuc['NA'] : 0) + (isset($thongKeHocLuc['Rớt']) ? $thongKeHocLuc['Rớt'] : 0);
        //     $soHocSinhXepLoai = $soHocSinhXepLoai - $khongXepLoai;
        // }
        $exportMessage = 'Dự kiến số ' . ($nganhNghe->hdt_id == 4 ? 'sinh viên' : 'học sinh') . ' học sinh đủ điều kiện công nhận tốt nghiệp: ' . $soHocSinhXepLoai . ' ' . ($nganhNghe->hdt_id == 4 ? 'SV' : 'HS') . ', trong đó: ';
        $exportMessage .= join('; ', $thongKeMessage);
        $sheet->setCellValue($currentAddress, $exportMessage);
        $currentAddress = $this->nextRowAddress($currentAddress, true);

        // Không xếp loại học tập: 03 HS.
        $sheet->setCellValue($currentAddress, 'Dự kiến số ' . ($nganhNghe->hdt_id == 4 ? 'sinh viên' : 'học sinh') . ' học sinh chưa đủ điều kiện công nhận tốt nghiệp: ' . $khongXepLoai . ' ' . ($nganhNghe->hdt_id == 4 ? 'SV' : 'HS'));
        $currentAddress = $this->nextRowAddress($currentAddress, true);

        //Để phía cuối
        $sheet->setCellValue('A6', mb_strtoupper('LỚP: ' . $lopHoc->lh_ten . ' (MÃ LỚP: ' . $lopHoc->lh_ma . ') - KHÓA ' . $khoaDaoTao->kdt_khoa, 'UTF-8'));
        if ($nganhNghe->hdt_id == 5) {
            $sheet->setCellValue('A7', mb_strtoupper('TRÌNH ĐỘ: TRUNG CẤP - HỆ: ' . $khoaDaoTao->kdt_he . ' - NIÊN KHÓA: ' . $lopHoc->nienKhoa->nk_ten, 'UTF-8'));
            $sheet->setCellValue('B10', 'MSHS');
        } else {
            $sheet->setCellValue('A7', mb_strtoupper('TRÌNH ĐỘ: CAO ĐẲNG - HỆ: ' . $khoaDaoTao->kdt_he . ' - NIÊN KHÓA: ' . $lopHoc->nienKhoa->nk_ten, 'UTF-8'));
            $sheet->setCellValue('B10', 'MSSV');
        }

        // Ghi chú
        $currentAddress = $this->nextRowAddress($currentAddress, true);
        $currentAddress = $this->nextRowAddress($currentAddress, true);

        // Các table ghi chú
        $danhSachGhiChuSplit = null;
        $noteColSplit = [];
        $paddingCol = 0;
        if ($filterSemester > 6) {
            $danhSachGhiChuSplit = $danhSachGhiChu->splitIn(3);
            $paddingCol = round($maxUsedCol / 2) - 3;
        } else {
            $danhSachGhiChuSplit = $danhSachGhiChu->splitIn(2);
            $paddingCol = round($maxUsedCol / 1) - 5;
        }
        $insertRow = $danhSachGhiChuSplit[0]->count();
        $sheet->insertNewRowBefore($this->currentRow($currentAddress), $insertRow);
        $firstNoteRow = $this->currentRow($currentAddress);
        foreach ($danhSachGhiChuSplit as $sIndex => $notes) {
            $currentAddress = $this->currentCol($currentAddress) . $firstNoteRow;
            foreach ($notes as $note) {
                $richText = new \PhpOffice\PhpSpreadsheet\RichText\RichText();
                $payable = $richText->createTextRun($note['key'] . ': ');
                $payable->getFont()->setBold(true);
                $richText->createTextRun($note['value']);
                $sheet->setCellValue($currentAddress, $richText);
                $currentAddress = $this->nextRowAddress($currentAddress);
            }
            $currentAddress = $this->nextColsAddress($currentAddress, $paddingCol + $sIndex * 2);
        }


        // Ký tên
        $splitSignCol = $filterSemester > 4 ? 4 : 3;
        $paddingSignCol = round($maxUsedCol / $splitSignCol);
        $currentAddress = $this->nextRowAddress($currentAddress, true);
        if ($filterSemester > 6) {
            $currentAddress = $this->nextRowAddress($currentAddress, true);
        }
        $firstSignRow = $this->currentRow($currentAddress);

        // Người lập
        // $currentAddress = $this->nextRowsAddress($currentAddress, 5);
        // $sheet->setCellValue($currentAddress, 'Lê Thị Hồng Phương');

        // if ($filterSemester > 6) {
        //     // Phòng công tác sinh viên
        //     $currentAddress = $this->currentCol($currentAddress) . $firstSignRow;
        //     $currentAddress = $this->nextColsAddress($currentAddress, $paddingSignCol);
        //     $sheet->setCellValue($currentAddress, 'PT. KHOA ..............');
        //     $sheet->getStyle($currentAddress)->applyFromArray($boldCenterStyle);
        // }

        // Phòng công tác sinh viên
        $currentAddress = $this->currentCol($currentAddress) . $firstSignRow;
        $currentAddress = $this->nextColsAddress($currentAddress, $paddingSignCol);
        $sheet->setCellValue($currentAddress, 'T. PHÒNG ĐÀO TẠO');
        $sheet->getStyle($currentAddress)->applyFromArray($boldCenterStyle2);


        $currentAddress = $this->nextRowAddress($currentAddress);
        $sheet->setCellValue($currentAddress, 'VÀ CÔNG TÁC HSSV');
        $sheet->getStyle($currentAddress)->applyFromArray($boldCenterStyle2);

        // $currentAddress = $this->nextRowsAddress($currentAddress, 4);
        // $sheet->setCellValue($currentAddress, 'Trần Văn Kiểu');
        // $sheet->getStyle($currentAddress)->applyFromArray($centerStyle);

        // Phòng đào tạo
        // $currentAddress = $this->currentCol($currentAddress) . $firstSignRow;
        // $currentAddress = $this->nextColsAddress($currentAddress, $paddingSignCol);
        // $sheet->setCellValue($currentAddress, 'PTP. PHÒNG ĐÀO TẠO');
        // $sheet->getStyle($currentAddress)->applyFromArray($boldCenterStyle);
        // $currentAddress = $this->nextRowsAddress($currentAddress, 5);
        // $sheet->setCellValue($currentAddress, 'Hồ Văn Chương');
        // $sheet->getStyle($currentAddress)->applyFromArray($centerStyle);

        $currentAddress = $this->nextRowAddress($currentAddress, true);

        $result->spreadsheet = $spreadsheet;
        $result->fileName = mb_strtoupper('BĐTHKQHTKH - LỚP: ' . $lopHoc->lh_ten . ' (MÃ LỚP: ' . $lopHoc->lh_ma . ') - NIÊN KHÓA: ' . $khoaDaoTao->kdt_khoa, 'UTF-8');
        return $result;
    }

    /**
     * Export to disk file function
     * @param [type] $filterSemester
     * @param [type] $filterLhId
     * @param [type] $exportPath
     * @return void
     * @author ttdat
     * @version 1.0
     */
    public function export($filterSemester, $filterLhId, $exportPath)
    {
        $result = $this->createSheet($filterSemester, $filterLhId);
        $writer = new Xlsx($result->spreadsheet);
        $writer->save($exportPath);
        return $exportPath;
    }



    /**
     * Create document
     * @param [type] $filterSemester
     * @param [type] $filterLhId
     * @return void
     * @author ttdat
     * @version 1.0
     */
    public function createSheet($filterSemester, $filterLhId)
    {
        $result = new \stdClass;
        $spreadsheet = new Spreadsheet();
        if ($filterSemester == 123456) {
            $spreadsheet = IOFactory::load(storage_path($this->template_all));
        } else if ($filterSemester > 6) {
            $spreadsheet = IOFactory::load(storage_path($this->template_year));
        } else {
            $spreadsheet = IOFactory::load(storage_path($this->template_semester));
        }
        $spreadsheet->getDefaultStyle()->getFont()->setName('Times New Roman');
        $spreadsheet->getDefaultStyle()->getFont()->setSize(13);

        $centerBorderStyle = [
            'font' => [
                'bold' => false,
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ]
        ];

        $centerStyle = [
            'font' => [
                'bold' => false,
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => [
                    'argb' => 'FFFFFFFF',
                ],
            ]
        ];

        $normalStyle = [
            'font' => [
                'bold' => false,
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ]
        ];

        $boldStyle = [
            'font' => [
                'bold' => true,
            ]
        ];

        $boldCenterStyle = [
            'font' => [
                'bold' => true,
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ]
        ];

        $markedColor = 'FFC4CC';

        // Export data
        $exportData = $this->getKetQuaHocTap($filterSemester, $filterLhId, 0, true);
        $lopHoc = $exportData['lopHoc'];
        $nienKhoa = $lopHoc->nienKhoa;
        $khoaDaoTao = $lopHoc->khoaDaoTao;
        $heDaotao = $khoaDaoTao->heDaotao;
        $nganhNghe = $khoaDaoTao->nganhNghe;
        $danhSachNamHoc = $exportData['danhSachNamHoc'];
        $danhSachSinhVien = $exportData['danhSachSinhVien'];
        // $danhSachGhiChu = $exportData['notes'];
        $exportSemesters = $exportData['semesters'];

        $danhSachGhiChu = $exportData['notes'];
        // Thêm ghi chú
        $additionalItems = collect([
            ['key' => 'TL', 'value' => 'Thi lại'],
            ['key' => 'HL', 'value' => 'Học lại'],
        ]);
        $danhSachGhiChu = $danhSachGhiChu->concat($additionalItems);



        // Nếu toàn khóa
        if ($filterSemester == 123456) {
            
            // Lặp qua danh sách sinh viên
            foreach ($danhSachSinhVien as $sinhVien) {
                // Kiểm tra xem $sinhVien có thuộc tính toanKhoa và avg đã được định nghĩa không
                if (isset($sinhVien->toanKhoa->avg)) {
                    // Xác định không còn sinh viên nào nợ môn nên avg = tichLuy
                    $sinhVien->toanKhoa->avg = $sinhVien->toanKhoa->tichLuy;
                }
            }

            $danhSachSinhVien = $danhSachSinhVien->sortBy([
                function ($a, $b) {
                    if (isset($a->toanKhoa->avg) && isset($b->toanKhoa->avg)) {
                        return $b->toanKhoa->avg <=> $a->toanKhoa->avg;
                    } else if (isset($b->toanKhoa->avg)) {
                        return 1;
                    } else if (isset($a->toanKhoa->avg)) {
                        return -1;
                    }
                    return 0;
                },
                function ($a, $b) {
                    return $a->sv_ma <=> $b->sv_ma;
                }
            ]);
        } else if ($filterSemester > 6 && $filterSemester < 123456) {
            $danhSachSinhVien = $danhSachSinhVien->sortBy([
                function ($a, $b) {
                    if (isset($a->years[0]->avg) && isset($b->years[0]->avg)) {
                        return $b->years[0]->avg <=> $a->years[0]->avg;
                    } else if (isset($b->years[0]->avg)) {
                        return 1;
                    } else if (isset($a->years[0]->avg)) {
                        return -1;
                    }
                    return 0;
                },
                function ($a, $b) {
                    return $a->sv_ma <=> $b->sv_ma;
                }
            ]);
        } else {
            $danhSachSinhVien = $danhSachSinhVien->sortBy([
                function ($a, $b) {
                    if (isset($a->years[0]->semesters[0]->avg) && isset($b->years[0]->semesters[0]->avg)) {
                        return $b->years[0]->semesters[0]->avg <=> $a->years[0]->semesters[0]->avg;
                    } else if (isset($b->years[0]->semesters[0]->avg)) {
                        return 1;
                    } else if (isset($a->years[0]->semesters[0]->avg)) {
                        return -1;
                    }
                    return 0;
                },
                function ($a, $b) {
                    return $a->sv_ma <=> $b->sv_ma;
                }
            ]);
        }

        $sheet = $spreadsheet->getActiveSheet();

        // Sheet data
        $sheetData = new \stdClass;
        if ($filterSemester == 123456) {
            $sheetData->title_1 = Str::upper('BẢNG ĐIỂM TỔNG HỢP KẾT QUẢ HỌC TẬP TOÀN KHÓA');
            $result->filename = 'KQHT_LOP_' . $lopHoc->lh_ma . '_TOANKHOA.xlsx';
        } else if ($filterSemester > 6) {
            $sheetData->title_1 = Str::upper('BẢNG ĐIỂM TỔNG HỢP KẾT QUẢ HỌC TẬP NĂM THỨ ' . $exportData['reqYear']);
            $result->filename = 'KQHT_LOP_' . $lopHoc->lh_ma . '_NAM_' . $exportData['reqYear'] . '.xlsx';
        } else {
            $currentHocKy = 1;
            // if (count($exportSemesters) == 1) {
            //     $currentHocKy = intval($exportSemesters[0]);
            //     if ($currentHocKy > 2) {
            //         $currentHocKy = floor($currentHocKy / 2);
            //     }
            // }

            if (count($exportSemesters) == 1) {
                $currentHocKy = intval($exportSemesters[0]);
            }

            // $sheetData->title_1 = Str::upper('BẢNG ĐIỂM TỔNG HỢP KẾT QUẢ HỌC TẬP HỌC KỲ ' . $currentHocKy . ' - NĂM THỨ ' . $exportData['reqYear']);
            $sheetData->title_1 = Str::upper('BẢNG ĐIỂM TỔNG HỢP KẾT QUẢ HỌC TẬP HỌC KỲ ' . $currentHocKy);
            $result->filename = 'KQHT_LOP_' . $lopHoc->lh_ma . '_HK' . join(', ', $exportData['semesters']) . '' . '_NAM_' . $exportData['reqYear'] . '.xlsx';
        }
        $sheetData->title_2 = Str::upper('TRÌNH ĐỘ: ' . $heDaotao->hdt_ten . ' - HỆ: ' . $khoaDaoTao->kdt_he);
        $sheetData->title_3 = Str::upper('LỚP: ' . $lopHoc->lh_ten . ' (MÃ LỚP: ' . $lopHoc->lh_ma . ') - KHÓA ' . $khoaDaoTao->kdt_khoa . ' - NIÊN KHÓA: ' . $nienKhoa->nk_ten);

        $maxUsedCol = 9;

        // Set sheet data
        $sheet->setCellValue('B9', $nganhNghe->hdt_id == 4 ? 'MSSV' : 'MSHS');
        $sheet->setCellValue('A5', $sheetData->title_1);
        $sheet->setCellValue('A6', $sheetData->title_2);
        $sheet->setCellValue('A7', $sheetData->title_3);
        // Header danh sách môn học
        $monHocTitleAddress = 'E9';
        $monHocTitleFinalAddress = 'E9';
        $currentAddress = 'E9';

        // số cột phát sinh theo môn học trừ đi 1 (là cột ban đầu)
        $numberInsertCol = $danhSachNamHoc->map(function ($year) {
            // Số môn học + 3 ô (điểm trung bình/ rèn luyện)
            return $year->semesters->map(function ($semester) {
                return $semester->monHoc->count() + 2;
            })->sum();
        })->sum();
        // Thêm cột xếp loại năm
        if ($filterSemester > 6 && $filterSemester < 123456) {
            // Phát sinh 2 cột TBN và RLN
            $numberInsertCol += 2 * $danhSachNamHoc->count();
        } else if ($filterSemester == 123456) {
            $numberInsertCol += 2 + $danhSachNamHoc->count();
        }
        $maxUsedCol += $numberInsertCol;

        // Thêm cột
        $sheet->getRowDimension($this->currentRow($currentAddress))->setRowHeight(70);
        $sheet->insertNewColumnBefore($this->currentCol($currentAddress), $numberInsertCol);
        $tinChiToanKhoa = 0;
        foreach ($danhSachNamHoc as $yIndex => $year) {
            $tinChiNamHoc = 0;
            foreach ($year->semesters as $sIndex => $semester) {
                if (count($exportSemesters) == 1) {
                    $currentHocKy = intval($exportSemesters[0]);
                } else {
                    // $currentHocKy = $sIndex + 1;
                    $currentHocKy = intval($exportSemesters[$sIndex]);
                }


                // if (count($exportSemesters) == 1) {
                //     $currentHocKy = intval($exportSemesters[0]);
                //     if ($currentHocKy > 2) {
                //         $currentHocKy = floor($currentHocKy / 2);
                //     }
                // }

                $totalTinChi = 0;
                $semester->monHoc->each(function ($monHoc) use (&$sheet, &$currentAddress, &$totalTinChi) {
                    if ($monHoc->mh_tichluy) {
                        $totalTinChi += $monHoc->mh_sodonvihoctrinh;
                    }

                    // Mã môn học
                    $sheet->setCellValue($currentAddress, $monHoc->mh_tichluy ? $monHoc->mh_ma : $monHoc->mh_ma . ' (*)');
                    $sheet->getStyle($currentAddress)->getAlignment()->setTextRotation(90);
                    $sheet->getColumnDimension($this->currentCol($currentAddress))->setWidth(5);

                    // Số tín chỉ của môn học
                    $tinChiAddress = $this->nextRowAddress($currentAddress);
                    $sheet->setCellValue($tinChiAddress, $monHoc->mh_sodonvihoctrinh);
                    $sheet->getStyle($tinChiAddress)->applyFromArray([
                        'font' => [
                            'bold' => false
                        ]
                    ]);

                    $currentAddress = $this->nextColAddress($currentAddress);
                });
                // Điểm trung bình học kỳ
                $sheet->setCellValue($currentAddress, 'ĐTBCHK' . $currentHocKy);
                $sheet->getStyle($currentAddress)->applyFromArray($boldStyle);
                $sheet->getStyle($currentAddress)->getAlignment()->setTextRotation(90);
                $sheet->getColumnDimension($this->currentCol($currentAddress))->setWidth(5);

                // Tổng tín chỉ
                $tinChiAddress = $this->nextRowAddress($currentAddress);
                $sheet->setCellValue($tinChiAddress, $totalTinChi);
                $tinChiToanKhoa += $totalTinChi;
                $tinChiNamHoc += $totalTinChi;
                $sheet->getStyle($tinChiAddress)->applyFromArray($centerBorderStyle);
                $currentAddress = $this->nextColAddress($currentAddress);

                // Điểm rèn luyện
                $sheet->setCellValue($currentAddress, 'RLHK' . $currentHocKy);
                $sheet->mergeCells($currentAddress . ':' . $this->nextRowAddress($currentAddress));
                $sheet->getStyle($currentAddress)->getAlignment()->setTextRotation(90);
                $sheet->getColumnDimension($this->currentCol($currentAddress))->setWidth(5);
                $currentAddress = $this->nextColAddress($currentAddress);

                // Thêm cột xếp loại học kỳ
                if ($filterSemester <= 6) {
                    $sheet->setCellValue($currentAddress, 'XLHK' . $currentHocKy);
                    $sheet->mergeCells($currentAddress . ':' . $this->nextRowAddress($currentAddress));
                    $sheet->getStyle($currentAddress)->getAlignment()->setTextRotation(0);
                    $sheet->getColumnDimension($this->currentCol($currentAddress))->setWidth(20);
                    $currentAddress = $this->nextColAddress($currentAddress);
                }
            }

            if ($filterSemester > 6) {
                if ($filterSemester <= 123456) {
                    $labelYear = $filterSemester < 123456 ? $exportData['reqYear'] : $yIndex + 1;
                    // thêm cột điểm trung bình năm
                    $sheet->setCellValue($currentAddress, 'ĐTBN' . $labelYear);
                    $sheet->getStyle($currentAddress)->applyFromArray($boldStyle);
                    $sheet->getStyle($currentAddress)->getAlignment()->setTextRotation(90);
                    $sheet->getColumnDimension($this->currentCol($currentAddress))->setWidth(5);
                    // Tổng tín chỉ
                    $tinChiAddress = $this->nextRowAddress($currentAddress);
                    $sheet->setCellValue($tinChiAddress, $tinChiNamHoc);
                    $currentAddress = $this->nextColAddress($currentAddress);
                }

                if ($filterSemester < 123456) {
                    // thêm cột xếp loại năm học
                    $sheet->setCellValue($currentAddress, 'XLN' . $labelYear);
                    $sheet->mergeCells($currentAddress . ':' . $this->nextRowAddress($currentAddress));
                    $currentAddress = $this->nextColAddress($currentAddress);

                    $sheet->setCellValue($currentAddress, 'RLN' . $labelYear);
                    $sheet->mergeCells($currentAddress . ':' . $this->nextRowAddress($currentAddress));
                    $sheet->getStyle($currentAddress)->getAlignment()->setTextRotation(90);
                    $sheet->getColumnDimension($this->currentCol($currentAddress))->setWidth(5);
                }
            }
        }

        if ($filterSemester == 123456) {
            $sheet->setCellValue($currentAddress, 'ĐTBTK');
            $sheet->getStyle($currentAddress)->applyFromArray($boldStyle);
            $sheet->getStyle($currentAddress)->getAlignment()->setTextRotation(90);
            $sheet->getColumnDimension($this->currentCol($currentAddress))->setWidth(5);
            // Tổng tín chỉ
            $tinChiAddress = $this->nextRowAddress($currentAddress);
            $sheet->setCellValue($tinChiAddress, $tinChiToanKhoa);
            // $sheet->getStyle($tinChiAddress)->applyFromArray($centerBorderStyle);
            $currentAddress = $this->nextColAddress($currentAddress);

            $sheet->setCellValue($currentAddress, 'XLTK');
            $sheet->mergeCells($currentAddress . ':' . $this->nextRowAddress($currentAddress));
            $currentAddress = $this->nextColAddress($currentAddress);

            $sheet->setCellValue($currentAddress, 'RLTK');
            $sheet->mergeCells($currentAddress . ':' . $this->nextRowAddress($currentAddress));
            $sheet->getStyle($currentAddress)->getAlignment()->setTextRotation(90);
            $sheet->getColumnDimension($this->currentCol($currentAddress))->setWidth(5);
        }

        $sinhVienAddress = 'A11';
        $currentAddress = $sinhVienAddress;
        $sheet->insertNewRowBefore($this->currentRow($currentAddress), $danhSachSinhVien->count() - 1);
        foreach ($danhSachSinhVien as $sIndex => $sinhVien) {
            // 1. Stt
            $sheet->setCellValue($currentAddress, $sIndex + 1);
            $sheet->getStyle($currentAddress)->applyFromArray($centerBorderStyle);
            $currentAddress = $this->nextColAddress($currentAddress);

            // 2. Mã số
            $sheet->setCellValue($currentAddress, $sinhVien->sv_ma);
            $sheet->getStyle($currentAddress)->applyFromArray($normalStyle);
            $currentAddress = $this->nextColAddress($currentAddress);

            // 3. Họ
            $sheet->setCellValue($currentAddress, $sinhVien->sv_ho);
            $sheet->getStyle($currentAddress)->applyFromArray($normalStyle);
            $currentAddress = $this->nextColAddress($currentAddress);

            $sheet->setCellValue($currentAddress, $sinhVien->sv_ten);
            $sheet->getStyle($currentAddress)->applyFromArray($normalStyle);
            $currentAddress = $this->nextColAddress($currentAddress);

            foreach ($sinhVien->years as $yIndex => $year) {
                foreach ($year->semesters as $semester) {
                    // 4. Điểm các môn trong học kỳ
                    foreach ($semester->monHoc as $monHoc) {
                        $final = -1;
                        if ($monHoc->ketQua) {
                            $final = $monHoc->ketQua->display_score;
                            $sheet->setCellValue($currentAddress, $final != null ? number_format($final, 1) : '');
                        }
                        $sheet->getStyle($currentAddress)->getNumberFormat()->setFormatCode('0.0');
                        $sheet->getStyle($currentAddress)->applyFromArray($centerBorderStyle);
                        // if (!isset($monHoc->ketQua) || $final < 5) {
                        if (isset($monHoc->ketQua->passed) && !$monHoc->ketQua->passed) {
                            $sheet->getStyle($currentAddress)->getFill()
                                ->setFillType(Fill::FILL_SOLID)
                                ->getStartColor()->setARGB($markedColor);
                        }
                        $currentAddress = $this->nextColAddress($currentAddress);
                    }
                    // 5. Điểm trung bình học kỳ
                    if (isset($semester->avg)) {
                        $sheet->setCellValue($currentAddress, $semester->avg);
                    }
                    $sheet->getStyle($currentAddress)->applyFromArray($centerBorderStyle);
                    $sheet->getStyle($currentAddress)->applyFromArray($boldStyle);
                    $sheet->getStyle($currentAddress)->getNumberFormat()->setFormatCode('0.0');
                    $currentAddress = $this->nextColAddress($currentAddress);

                    // 6. Điểm rèn luyện trong học kỳ
                    if (isset($semester->diemRenLuyen)) {
                        $sheet->setCellValue($currentAddress, $semester->diemRenLuyen);
                    }
                    $sheet->getStyle($currentAddress)->applyFromArray($centerBorderStyle);
                    if (isset($semester->diemRenLuyen) && $semester->diemRenLuyen == 0) {
                        $sheet->getStyle($currentAddress)->getFill()
                            ->setFillType(Fill::FILL_SOLID)
                            ->getStartColor()->setARGB($markedColor);
                    }
                    $currentAddress = $this->nextColAddress($currentAddress);

                    if ($filterSemester <= 6) {
                        // 7.1. Xếp loại học kỳ
                        if (isset($semester->hocLuc)) {
                            $sheet->setCellValue($currentAddress, $semester->hocLuc);
                        }
                        $sheet->getStyle($currentAddress)->applyFromArray($centerBorderStyle);
                        $sheet->getStyle($currentAddress)->getAlignment()->setTextRotation(0);
                        $currentAddress = $this->nextColAddress($currentAddress);

                        // 8.1. Tổng số tín chỉ tích lũy
                        if (isset($semester->tinChiTichLuy)) {
                            $sheet->setCellValue($currentAddress, $semester->tinChiTichLuy);
                        }
                        $sheet->getStyle($currentAddress)->applyFromArray($centerBorderStyle);
                        $sheet->getStyle($currentAddress)->getAlignment()->setTextRotation(0);
                        $currentAddress = $this->nextColAddress($currentAddress);

                        // 9.1. Điểm trung bình chung tích lũy
                        if (isset($semester->tichLuy)) {
                            $sheet->setCellValue($currentAddress, $semester->tichLuy);
                        }
                        $sheet->getStyle($currentAddress)->applyFromArray($centerBorderStyle);
                        $sheet->getStyle($currentAddress)->getAlignment()->setTextRotation(0);
                        $sheet->getStyle($currentAddress)->getNumberFormat()->setFormatCode('0.0');
                        $currentAddress = $this->nextColAddress($currentAddress);
                    }
                }

                if ($filterSemester > 6) {
                    if ($filterSemester <= 123456) {
                        // Điểm trung bình năm
                        if (isset($year->avg)) {
                            $sheet->setCellValue($currentAddress, $year->avg);
                        }
                        $sheet->getStyle($currentAddress)->getNumberFormat()->setFormatCode('0.0');
                        $sheet->getStyle($currentAddress)->applyFromArray($centerBorderStyle);
                        $currentAddress = $this->nextColAddress($currentAddress);
                    }

                    if ($filterSemester < 123456) {
                        // 7.2. Xếp loại năm
                        if (isset($year->hocLuc)) {
                            $sheet->setCellValue($currentAddress, $year->hocLuc);
                        }
                        $sheet->getStyle($currentAddress)->applyFromArray($centerBorderStyle);
                        $sheet->getColumnDimension($this->currentCol($currentAddress))->setWidth(20);
                        $currentAddress = $this->nextColAddress($currentAddress);

                        // Rèn luyện năm
                        if (isset($year->diemRenLuyen)) {
                            $sheet->setCellValue($currentAddress, round($year->diemRenLuyen));
                        }
                        $sheet->getStyle($currentAddress)->getAlignment()->setTextRotation(0);
                        $sheet->getStyle($currentAddress)->applyFromArray($centerBorderStyle);
                        $currentAddress = $this->nextColAddress($currentAddress);

                        // 8.2. Tổng số tín chỉ tích lũy
                        if (isset($year->tinChiTichLuy)) {
                            $sheet->setCellValue($currentAddress, $year->tinChiTichLuy);
                        }
                        $sheet->getStyle($currentAddress)->getAlignment()->setTextRotation(0);
                        $sheet->getStyle($currentAddress)->applyFromArray($centerBorderStyle);
                        $currentAddress = $this->nextColAddress($currentAddress);

                        // 9.2. Điểm trung bình chung tích lũy
                        if (isset($year->tichLuy)) {
                            $sheet->setCellValue($currentAddress, $year->tichLuy);
                        }
                        $sheet->getStyle($currentAddress)->getAlignment()->setTextRotation(0);
                        $sheet->getStyle($currentAddress)->applyFromArray($centerBorderStyle);
                        $sheet->getStyle($currentAddress)->getNumberFormat()->setFormatCode('0.0');
                        $currentAddress = $this->nextColAddress($currentAddress);

                        // 10. Ghi chú
                        // if ($filterSemester <= 4) {
                        //     $svGhiChu = $semester->notes->map(function ($note) {
                        //         if ($note['type'] == 'DL2') {
                        //             return $note['key'] . ' (L2:' . $note['value'] . ')';
                        //         } else {
                        //             return $note['type'] . ' (' . $note['key'] . ')';
                        //         }
                        //     })->join(', ');
                        //     $sheet->setCellValue($currentAddress, $svGhiChu);
                        //     $sheet->getStyle($currentAddress)->applyFromArray($normalStyle);
                        //     $currentAddress = $this->nextColAddress($currentAddress);
                        // }

                        // Điều chỉnh tiến độ học
                        if (isset($year->avg) && $year->avg <= 5 && $year->avg >= 4) {
                            $sheet->setCellValue($currentAddress, 'X');
                        }
                        $sheet->getStyle($currentAddress)->applyFromArray($centerBorderStyle);
                        $currentAddress = $this->nextColAddress($currentAddress);

                        // Buộc thôi học
                        $dtbcCondition = isset($year->avg) && $year->avg < 4;
                        $dtctlCondition = isset($year->tichLuy) && $year->tichLuy < 4;
                        if ($dtbcCondition || $dtctlCondition) {
                            $sheet->setCellValue($currentAddress, 'X');
                        }
                        $sheet->getStyle($currentAddress)->applyFromArray($centerBorderStyle);
                        $currentAddress = $this->nextColAddress($currentAddress);
                    }
                }
            }

            if ($filterSemester == 123456) {
                // Điểm trung bình năm
                if (isset($sinhVien->toanKhoa->avg)) {
                    $sheet->setCellValue($currentAddress, number_format($sinhVien->toanKhoa->avg, 1) . '');
                }
                $sheet->getStyle($currentAddress)->applyFromArray($centerBorderStyle);
                $sheet->getStyle($currentAddress)->applyFromArray($boldStyle);
                $currentAddress = $this->nextColAddress($currentAddress);

                // 7.2. Xếp loại năm
                if (isset($sinhVien->toanKhoa->hocLuc)) {
                    $sheet->setCellValue($currentAddress, $sinhVien->toanKhoa->hocLuc);
                }
                $sheet->getStyle($currentAddress)->applyFromArray($centerBorderStyle);
                $sheet->getColumnDimension($this->currentCol($currentAddress))->setWidth(20);
                $currentAddress = $this->nextColAddress($currentAddress);

                // Rèn luyện năm
                if (isset($sinhVien->toanKhoa->diemRenLuyen)) {
                    $sheet->setCellValue($currentAddress, round($sinhVien->toanKhoa->diemRenLuyen));
                }
                $sheet->getStyle($currentAddress)->getAlignment()->setTextRotation(0);
                $sheet->getStyle($currentAddress)->applyFromArray($centerBorderStyle);
                $currentAddress = $this->nextColAddress($currentAddress);

                // 8.2. Tổng số tín chỉ tích lũy
                if (isset($sinhVien->toanKhoa->tinChiTichLuy)) {
                    $sheet->setCellValue($currentAddress, $sinhVien->toanKhoa->tinChiTichLuy);
                }
                $sheet->getStyle($currentAddress)->getAlignment()->setTextRotation(0);
                $sheet->getStyle($currentAddress)->applyFromArray($centerBorderStyle);
                $currentAddress = $this->nextColAddress($currentAddress);

                // 9.2. Điểm trung bình chung tích lũy
                if (isset($sinhVien->toanKhoa->tichLuy)) {
                    $sheet->setCellValue($currentAddress, $sinhVien->toanKhoa->tichLuy);
                }
                $sheet->getStyle($currentAddress)->getAlignment()->setTextRotation(0);
                $sheet->getStyle($currentAddress)->applyFromArray($centerBorderStyle);
                $sheet->getStyle($currentAddress)->getNumberFormat()->setFormatCode('0.0');
                $currentAddress = $this->nextColAddress($currentAddress);
            }

            // 10. Ghi chú
            $svGhiChu = $sinhVien->notes->map(function ($note) {
                if ($note['type'] == 'DL3') {
                    return $note['key'] . ' (DiemL3:' . number_format($note['value'], 1) . ')';
                } else if ($note['type'] == 'DTL3') {
                    return $note['key'] . ' (DiemThiL3:' . number_format($note['value'], 1) . ')';
                } else if ($note['type'] == 'DL2') {
                    return $note['key'] . ' (DiemL2:' . number_format($note['value'], 1) . ')';
                } else if ($note['type'] == 'DTL2') {
                    return $note['key'] . ' (DiemThiL2:' . number_format($note['value'], 1) . ')';
                } else if ($note['type'] == 'DL1') {
                    return $note['key'] . ' (DiemL1:' . number_format($note['value'], 1) . ')';
                } else if ($note['type'] == 'DTL1') {
                    return $note['key'] . ' (DiemThiL1:' . number_format($note['value'], 1) . ')';
                } else {
                    if (isset($note['value'])) {
                        return $note['type'] . ' (' . $note['value'] . ')';
                    }
                    return $note['type'] . ' (' . $note['key'] . ')';
                }
            })->join(', ');
            $sheet->setCellValue($currentAddress, $svGhiChu);
            $sheet->getStyle($currentAddress)->applyFromArray($normalStyle);
            $currentAddress = $this->nextColAddress($currentAddress);

            $currentAddress = $this->nextRowAddress($currentAddress, true);
        }

        // Tổng số học sinh
        $sheet->setCellValue($currentAddress, 'Tổng số ' . ($nganhNghe->hdt_id == 4 ? 'sinh viên' : 'học sinh') . ': ' . $danhSachSinhVien->count());
        $currentAddress = $this->nextRowAddress($currentAddress, true);

        // Thống kê học lực
        $thongKeHocLuc = null;

        if ($filterSemester == 123456) {
            $thongKeHocLuc = $danhSachSinhVien->countBy(function ($sinhVien) {
                if (isset($sinhVien->toanKhoa) && isset($sinhVien->toanKhoa->hocLuc)) {
                    return $sinhVien->toanKhoa->hocLuc;
                };
                return 'NA';
            });
        } else if ($filterSemester > 4 && $filterSemester < 123456) {
            $thongKeHocLuc = $danhSachSinhVien->countBy(function ($sinhVien) {
                if (isset($sinhVien->years[0]) && isset($sinhVien->years[0]->hocLuc)) {
                    return $sinhVien->years[0]->hocLuc;
                };
                return 'NA';
            });
        } else {
            $thongKeHocLuc = $danhSachSinhVien->countBy(function ($sinhVien) {
                if (!$sinhVien->years[0]->semesters->isEmpty() && isset($sinhVien->years[0]->semesters[0]->hocLuc) && isset($sinhVien->years[0]->semesters[0]->hocLuc)) {
                    return $sinhVien->years[0]->semesters[0]->hocLuc;
                };
                return 'NA';
            });
        }

        $thongKeMessage = [];
        if (isset($thongKeHocLuc['Xuất sắc'])) {
            $thongKeMessage[] = 'Xuất sắc: ' . $thongKeHocLuc['Xuất sắc'] . ' ' . ($nganhNghe->hdt_id == 4 ? 'SV' : 'HS');
        }
        if (isset($thongKeHocLuc['Giỏi'])) {
            $thongKeMessage[] = 'Giỏi: ' . $thongKeHocLuc['Giỏi'] . ' ' . ($nganhNghe->hdt_id == 4 ? 'SV' : 'HS');
        }
        if (isset($thongKeHocLuc['Khá'])) {
            $thongKeMessage[] = 'Khá: ' . $thongKeHocLuc['Khá'] . ' ' . ($nganhNghe->hdt_id == 4 ? 'SV' : 'HS');
        }
        if (isset($thongKeHocLuc['Trung bình khá'])) {
            $thongKeMessage[] = 'Trung bình khá: ' . $thongKeHocLuc['Trung bình khá'] . ' ' . ($nganhNghe->hdt_id == 4 ? 'SV' : 'HS');
        }
        if (isset($thongKeHocLuc['Trung bình'])) {
            $thongKeMessage[] = 'Trung bình: ' . $thongKeHocLuc['Trung bình'] . ' ' . ($nganhNghe->hdt_id == 4 ? 'SV' : 'HS');
        }
        if (isset($thongKeHocLuc['Yếu'])) {
            $thongKeMessage[] = 'Yếu: ' . $thongKeHocLuc['Yếu'] . ' ' . ($nganhNghe->hdt_id == 4 ? 'SV' : 'HS');
        }

        $soHocSinhXepLoai = $danhSachSinhVien->count();
        $khongXepLoai = 0;
        if (isset($thongKeHocLuc['NA'])) {
            $khongXepLoai = $thongKeHocLuc['NA'];
            $soHocSinhXepLoai = $soHocSinhXepLoai - $khongXepLoai;
        }
        $exportMessage = 'Xếp loại học tập học kỳ ' . $filterSemester . ': ' . $soHocSinhXepLoai . ' ' . ($nganhNghe->hdt_id == 4 ? 'SV' : 'HS') . ', trong đó: ';
        if ($filterSemester > 6) {
            $exportMessage = 'Xếp loại học tập năm ' . $exportData['reqYear'] . ': ' . $soHocSinhXepLoai . ' ' . ($nganhNghe->hdt_id == 4 ? 'SV' : 'HS') . ', trong đó: ';
        }
        $exportMessage .= join('; ', $thongKeMessage);
        $sheet->setCellValue($currentAddress, $exportMessage);
        $currentAddress = $this->nextRowAddress($currentAddress, true);

        // Không xếp loại học tập: 03 HS.
        $sheet->setCellValue($currentAddress, 'Không xếp loại học tập: ' . $khongXepLoai . ' ' . ($nganhNghe->hdt_id == 4 ? 'SV' : 'HS'));
        $currentAddress = $this->nextRowAddress($currentAddress, true);

        // Ghi chú
        $currentAddress = $this->nextRowAddress($currentAddress, true);
        $currentAddress = $this->nextRowAddress($currentAddress, true);

        // Các table ghi chú
        $danhSachGhiChuSplit = null;
        $noteColSplit = [];
        $paddingCol = 0;
        if ($filterSemester > 6) {
            $danhSachGhiChuSplit = $danhSachGhiChu->splitIn(3);
            $paddingCol = round($maxUsedCol / 2) - 3;
        } else {
            $danhSachGhiChuSplit = $danhSachGhiChu->splitIn(2);
            $paddingCol = round($maxUsedCol / 1) - 5;
        }
        $insertRow = $danhSachGhiChuSplit[0]->count();
        $sheet->insertNewRowBefore($this->currentRow($currentAddress), $insertRow);
        $firstNoteRow = $this->currentRow($currentAddress);
        foreach ($danhSachGhiChuSplit as $sIndex => $notes) {
            $currentAddress = $this->currentCol($currentAddress) . $firstNoteRow;
            foreach ($notes as $note) {
                $richText = new \PhpOffice\PhpSpreadsheet\RichText\RichText();
                $payable = $richText->createTextRun($note['key'] . ': ');
                $payable->getFont()->setBold(true);
                $richText->createTextRun($note['value']);
                $sheet->setCellValue($currentAddress, $richText);
                $currentAddress = $this->nextRowAddress($currentAddress);
            }
            $currentAddress = $this->nextColsAddress($currentAddress, $paddingCol + $sIndex * 2);
        }

        // Xuất theo học kỳ
        if (strlen($filterSemester) == 1) {
            $currentAddress = $this->nextRowAddress($currentAddress, true);
            $currentAddress = $this->nextRowAddress($currentAddress, true);
        }
        // Xuất theo năm học hoặc xuất toàn khóa
         else if (strlen($filterSemester) > 2 || strlen($filterSemester) == 6) {
            $currentAddress = $this->nextRowAddress($currentAddress, true);
        } 

        // Ký tên
        $splitSignCol = $filterSemester > 4 ? 4 : 3;
        $paddingSignCol = round($maxUsedCol / $splitSignCol);
        $currentAddress = $this->nextRowAddress($currentAddress, true);
        if ($filterSemester > 6) {
            $currentAddress = $this->nextRowAddress($currentAddress, true);
        }
        $firstSignRow = $this->currentRow($currentAddress);

        // Người lập
        // $currentAddress = $this->nextRowsAddress($currentAddress, 5);
        // $sheet->setCellValue($currentAddress, 'Lê Thị Hồng Phương');

        // if ($filterSemester > 6) {
        //     // Phòng công tác sinh viên
        //     $currentAddress = $this->currentCol($currentAddress) . $firstSignRow;
        //     $currentAddress = $this->nextColsAddress($currentAddress, $paddingSignCol);
        //     $sheet->setCellValue($currentAddress, 'PT. KHOA ..............');
        //     $sheet->getStyle($currentAddress)->applyFromArray($boldCenterStyle);
        // }

        
        // Phòng công tác sinh viên
        $currentAddress = $this->currentCol($currentAddress) . $firstSignRow;
        $currentAddress = $this->nextColsAddress($currentAddress, $paddingSignCol);
        $sheet->setCellValue($currentAddress, 'PHÒNG ĐÀO TẠO VÀ CÔNG TÁC');
        $sheet->getStyle($currentAddress)->applyFromArray($boldCenterStyle);
        $currentAddress = $this->nextRowAddress($currentAddress);
        $sheet->setCellValue($currentAddress, 'HỌC SINH, SINH VIÊN');
        $sheet->getStyle($currentAddress)->applyFromArray($boldCenterStyle);
        // $currentAddress = $this->nextRowsAddress($currentAddress, 4);
        // $sheet->setCellValue($currentAddress, 'Trần Văn Kiểu');
        // $sheet->getStyle($currentAddress)->applyFromArray($centerStyle);

        // Phòng đào tạo
        // $currentAddress = $this->currentCol($currentAddress) . $firstSignRow;
        // $currentAddress = $this->nextColsAddress($currentAddress, $paddingSignCol);
        // $sheet->setCellValue($currentAddress, 'PTP. PHÒNG ĐÀO TẠO');
        // $sheet->getStyle($currentAddress)->applyFromArray($boldCenterStyle);


        // $currentAddress = $this->nextRowsAddress($currentAddress, 5);
        // $sheet->setCellValue($currentAddress, 'Hồ Văn Chương');
        // $sheet->getStyle($currentAddress)->applyFromArray($centerStyle);

        $currentAddress = $this->nextRowAddress($currentAddress, true);

        $result->spreadsheet = $spreadsheet;
        return $result;
    }

    public function getDanhSachDiemMonHocTichLuy($svId, $kdtId, $lhId, $semesters)
    {
        $danhSachDiemMonHocTichLuy = DB::table('qlsv_sinhvien as sv')
            ->join('qlsv_sinhvien_diem as svd', 'svd.sv_id', '=', 'sv.sv_id')
            ->join('qlsv_bangdiem as bd', 'bd.bd_id', '=', 'svd.bd_id')
            ->join('qlsv_monhoc as mh', 'mh.mh_id', '=', 'bd.mh_id')
            ->join('qlsv_khoadaotao_monhoc as kdtm', function ($join) {
                $join->on('kdtm.mh_id', '=', 'mh.mh_id')
                    ->on('bd.kdt_hocky', '=', 'kdtm.kdt_mh_hocky');
            })
            ->where('mh_tichluy', 1)
            ->where('bd.lh_id', $lhId)
            ->whereIn('bd.kdt_hocky', $semesters)
            ->where(function ($builder) {
                $builder->whereRaw('(svd.svd_first >= 5 AND (svd.svd_exam_first IS NULL OR svd.svd_exam_first >= 5))')
                    ->orWhereRaw('(svd.svd_second >= 5 AND (svd.svd_exam_second IS NULL OR svd.svd_exam_second >= 5))')
                    ->orWhereRaw('(svd.svd_third >= 5 AND (svd.svd_exam_third IS NULL OR svd.svd_exam_third >= 5))');
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

    public function getKetQuaHocTapTheoDotThi($semester, $lhId, $dt_id, $svId = 0)
    {
        $lopHocDau = LopHoc::with('khoaDaoTao', 'nienKhoa', 'khoaDaoTao.heDaoTao')->find($lhId);
        $quyChe2022 = $lopHocDau->lh_nienche == 1;

        $data = $this->getKetQuaHocTap($semester, $lhId, $svId, true);
        //$modalDotThi = DotThi::find($dt_id);
        $data['danhSachSinhVien'] = $data['danhSachSinhVien']->reject(function ($item, $key) use ($lhId, $dt_id) {
            return DB::select(
                'select d.* from qlsv_dotthi_diem d inner join qlsv_dotthi_bangdiem bd on bd.dt_bd_id = d.dt_bd_id  where bd.lh_id = ? AND bd.dt_id = ? AND d.sv_id = ?',
                [$lhId, $dt_id, $item['sv_id']]
            ) == null;
        });

        $numberExamPresent = DB::table('qlsv_dotthi_diem as dtd')
            ->join('qlsv_dotthi_bangdiem as bd', 'bd.dt_bd_id', '=', 'dtd.dt_bd_id')
            ->where('bd.dt_id', $dt_id)
            ->where('bd.lh_id', $lhId)
            ->max('dtd.svd_lan');

        $numberExamreCently = DB::table('qlsv_dotthi_diem as dtd')
            ->join('qlsv_dotthi_bangdiem as dtbd', 'dtd.dt_bd_id', '=', 'dtbd.dt_bd_id')
            ->where('dtbd.lh_id', $lhId)
            ->where('dtd.dt_id', '<=', $dt_id)
            ->select('dtd.dt_id')
            ->distinct()
            ->orderBy('dtd.dt_id', 'desc')
            ->limit(3)
            ->pluck('dt_id')
            ->toArray();

        //tính điểm môn học
        foreach ($data['danhSachSinhVien'] as $sinhVien) {
            //-1 là chưa đi thi null là vắng thi
            // $TBToanKhoa = isset($sinhVien->toanKhoa->avg) ? $sinhVien->toanKhoa->avg : -1;
            $diemdotthi = DB::table('qlsv_dotthi_diem as d')
                ->join('qlsv_dotthi_bangdiem as bd', 'bd.dt_bd_id', '=', 'd.dt_bd_id')
                ->join('qlsv_monhoc as mh', 'mh.mh_id', '=', 'bd.mh_id')
                ->where('d.sv_id', $sinhVien->sv_id)
                ->whereIn('d.dt_id', $numberExamreCently)
                ->select('d.svd_first', 'd.svd_lan', 'bd.mh_id', 'd.svd_ghichu', 'mh.mh_loai', 'd.svd_loai', 'd.svd_dieukien', 'd.svd_khongdatloai', 'd.svd_lan', 'bd.dt_id')
                ->get();


            // Số lần
            $sinhVien->svd_lanthihientai = $numberExamPresent;

            //2 chinhs tri, 3 thuc hanh, 4 ly thuyet, 5 bao ve
            $sinhVien->thuchanhlan1 = -1;
            $sinhVien->lythuyetlan1 = -1;
            $sinhVien->chinhtrilan1 = -1;
            $sinhVien->khoaluanlan1 = -1;

            $sinhVien->thuchanhlan2 = -1;
            $sinhVien->lythuyetlan2 = -1;
            $sinhVien->chinhtrilan2 = -1;
            $sinhVien->khoaluanlan2 = -1;

            $sinhVien->thuchanhlan3 = -1;
            $sinhVien->lythuyetlan3 = -1;
            $sinhVien->chinhtrilan3 = -1;
            $sinhVien->khoaluanlan3 = -1;

            foreach ($diemdotthi as $ddt) {
                $sinhVien->svd_ghichu = $ddt->svd_ghichu;
                $sinhVien->svd_loai = $ddt->svd_loai;
                $sinhVien->svd_khongdatloai = $ddt->svd_khongdatloai;

                if ($ddt->dt_id == $dt_id) {
                    $sinhVien->svd_dieukien = $ddt->svd_dieukien;
                }
                if ($ddt->svd_lan == 1) {
                    if ($ddt->mh_loai == 2) {
                        $sinhVien->chinhtrilan1 = $ddt->svd_first;
                    } else if ($ddt->mh_loai == 3) {
                        $sinhVien->thuchanhlan1 = $ddt->svd_first;
                    } else if ($ddt->mh_loai == 4) {
                        $sinhVien->lythuyetlan1 = $ddt->svd_first;
                    } else if ($ddt->mh_loai == 5) {
                        $sinhVien->khoaluanlan1 = $ddt->svd_first;
                    }
                } else if ($ddt->svd_lan == 2) {
                    if ($ddt->mh_loai == 2) {
                        $sinhVien->chinhtrilan2 = $ddt->svd_first;
                    } else if ($ddt->mh_loai == 3) {
                        $sinhVien->thuchanhlan2 = $ddt->svd_first;
                    } else if ($ddt->mh_loai == 4) {
                        $sinhVien->lythuyetlan2 = $ddt->svd_first;
                    } else if ($ddt->mh_loai == 5) {
                        $sinhVien->khoaluanlan2 = $ddt->svd_first;
                    }
                } else if ($ddt->svd_lan == 3) {
                    if ($ddt->mh_loai == 2) {
                        $sinhVien->chinhtrilan3 = $ddt->svd_first;
                    } else if ($ddt->mh_loai == 3) {
                        $sinhVien->thuchanhlan3 = $ddt->svd_first;
                    } else if ($ddt->mh_loai == 4) {
                        $sinhVien->lythuyetlan3 = $ddt->svd_first;
                    } else if ($ddt->mh_loai == 5) {
                        $sinhVien->khoaluanlan3 = $ddt->svd_first;
                    }
                }
            }

            $diemchinhtri = $sinhVien->chinhtrilan1;
            $diemlythuyet = $sinhVien->lythuyetlan1;
            $diemthuchanh = $sinhVien->thuchanhlan1;
            $diemkhoaluan = $sinhVien->khoaluanlan1;


            if ($sinhVien->khoaluanlan2 != -1) {
                $diemkhoaluan = $sinhVien->khoaluanlan2;
            } else if ($sinhVien->khoaluanlan3 != -1) {
                $diemkhoaluan = $sinhVien->khoaluanlan3;
            }

            if ($sinhVien->thuchanhlan2 != -1) {
                $diemthuchanh = $sinhVien->thuchanhlan2;
            } else if ($sinhVien->thuchanhlan3 != -1) {
                $diemthuchanh = $sinhVien->thuchanhlan3;
            }

            if ($sinhVien->lythuyetlan2 != -1) {
                $diemlythuyet = $sinhVien->lythuyetlan2;
            } else if ($sinhVien->lythuyetlan3 != -1) {
                $diemlythuyet = $sinhVien->lythuyetlan3;
            }

            if ($sinhVien->lythuyetlan2 != -1) {
                $diemlythuyet = $sinhVien->lythuyetlan2;
            } else if ($sinhVien->lythuyetlan3 != -1) {
                $diemlythuyet = $sinhVien->lythuyetlan3;
            }

            $sinhVien->toanKhoa->ghiChuThiLai = "";

            if ($diemchinhtri < 5 && $diemchinhtri != -1 && $diemkhoaluan == -1) {
                $sinhVien->toanKhoa->ghiChuThiLai .= 'ĐTNCT(L1:' . number_format($sinhVien->chinhtrilan1, 1);
                if ($sinhVien->chinhtrilan2 != -1) {
                    $sinhVien->toanKhoa->ghiChuThiLai .= ', L2:' . number_format($sinhVien->chinhtrilan2, 1);
                }
                if ($sinhVien->chinhtrilan3 != -1) {
                    $sinhVien->toanKhoa->ghiChuThiLai .= ', L3:' . number_format($sinhVien->chinhtrilan3, 1);
                }
                $sinhVien->toanKhoa->ghiChuThiLai .= '), ';
            }

            if ($diemlythuyet < 5 && $diemkhoaluan == -1) {
                $sinhVien->toanKhoa->ghiChuThiLai .= 'ĐTNLT(L1:' . number_format($sinhVien->lythuyetlan1, 1);
                if ($sinhVien->lythuyetlan2 != -1) {
                    $sinhVien->toanKhoa->ghiChuThiLai .= ', L2:' . number_format($sinhVien->lythuyetlan2, 1);
                }
                if ($sinhVien->lythuyetlan3 != -1) {
                    $sinhVien->toanKhoa->ghiChuThiLai .= ', L3:' . number_format($sinhVien->lythuyetlan3, 1);
                }
                $sinhVien->toanKhoa->ghiChuThiLai .= '), ';
            }

            if ($diemthuchanh < 5 && $diemkhoaluan == -1) {
                $sinhVien->toanKhoa->ghiChuThiLai .= 'ĐTNTH(L1:' . number_format($sinhVien->thuchanhlan1, 1);
                if ($sinhVien->thuchanhlan2 != -1) {
                    $sinhVien->toanKhoa->ghiChuThiLai .= ', L2:' . number_format($sinhVien->thuchanhlan2, 1);
                }
                if ($sinhVien->thuchanhlan3 != -1) {
                    $sinhVien->toanKhoa->ghiChuThiLai .= ', L3:' . number_format($sinhVien->thuchanhlan3, 1);
                }
                $sinhVien->toanKhoa->ghiChuThiLai .= '), ';
            }

            if ($diemkhoaluan < 5 && $diemkhoaluan != -1) {
                $sinhVien->toanKhoa->ghiChuThiLai .= 'ĐTNCD(L1:' . number_format($sinhVien->khoaluanlan1, 1);
                if ($sinhVien->khoaluanlan2 != -1) {
                    $sinhVien->toanKhoa->ghiChuThiLai .= ', L2:' . number_format($sinhVien->khoaluanlan2, 1);
                }
                if ($sinhVien->khoaluanlan3 != -1) {
                    $sinhVien->toanKhoa->ghiChuThiLai .= ', L3:' . number_format($sinhVien->khoaluanlan3, 1);
                }
                $sinhVien->toanKhoa->ghiChuThiLai .= '), ';
            }

            if (isset($sinhVien->toanKhoa->avg)) {
                if ($diemkhoaluan != -1) {
                    $sinhVien->toanKhoa->avg_totnghiep = number_format((($sinhVien->toanKhoa->avg * 3) + ($diemkhoaluan * 2)) / 5, 1);
                } else {
                    $sinhVien->toanKhoa->avg_totnghiep = number_format(($diemlythuyet + ($diemthuchanh * 2) + ($sinhVien->toanKhoa->avg * 3)) / 6, 1);
                }
                $sinhVien->toanKhoa->hocLucTN = $this->getHocLucByDiem($sinhVien->toanKhoa->avg_totnghiep, $quyChe2022);
            }
        }
        return $data;
    }

    public function getKetQuaHocTapTheoDotThiFull($semester, $lhId, $dt_id, $svId = 0)
    {
        $lopHocDau = LopHoc::with('khoaDaoTao', 'nienKhoa', 'khoaDaoTao.heDaoTao')->find($lhId);
        $quyChe2022 = $lopHocDau->lh_nienche == 1;
        $data = $this->getKetQuaHocTap($semester, $lhId);

        // check sv bị xóa tên sẽ không được thêm vào đợt thi
        foreach ($data['danhSachSinhVien'] as $key => $sinhVien) {
            $svCoQdXoaTen = DB::table('qlsv_sinhvien_quyetdinh as svqd')
                ->join('qlsv_quyetdinh as qd', 'qd.qd_id', 'svqd.qd_id')
                ->where('svqd.sv_id', $sinhVien->sv_id)
                ->where('qd.qd_loai', 2)
                ->get();

            if ($svCoQdXoaTen->count() > 0) {
                $data['danhSachSinhVien']->forget($key);
            }
        }


        $numberExamPresent = DB::table('qlsv_dotthi_diem as dtd')
            ->join('qlsv_dotthi_bangdiem as bd', 'bd.dt_bd_id', '=', 'dtd.dt_bd_id')
            ->where('bd.dt_id', $dt_id)
            ->where('bd.lh_id', $lhId)
            ->max('dtd.svd_lan');

        $numberExamCurrent = DB::table('qlsv_dotthi_diem as dtd')
            ->join('qlsv_dotthi_bangdiem as dtbd', 'dtd.dt_bd_id', '=', 'dtbd.dt_bd_id')
            ->where('dtbd.lh_id', $lhId)
            ->where('dtd.svd_lan', '<=', $numberExamPresent)
            ->select('dtd.dt_id')
            ->distinct()
            ->orderBy('dtd.dt_id', 'desc')
            ->limit($numberExamPresent)
            ->pluck('dt_id')
            ->toArray();


        $dxtn_id = DotThiDotXetTotNghiep::where('dt_id', $dt_id)->first()->dxtn_id;
        //tính điểm môn học
        foreach ($data['danhSachSinhVien'] as $sinhVien) {
            //-1 là chưa đi thi null là vắng thi
            // $TBToanKhoa = isset($sinhVien->toanKhoa->avg) ? $sinhVien->toanKhoa->avg : -1;
            $diemdotthi = DB::table('qlsv_dotthi_diem as d')
                ->join('qlsv_dotthi_bangdiem as bd', 'bd.dt_bd_id', '=', 'd.dt_bd_id')
                ->join('qlsv_monhoc as mh', 'mh.mh_id', '=', 'bd.mh_id')
                ->where('d.sv_id', $sinhVien->sv_id)
                ->whereIn('bd.dt_id', $numberExamCurrent)
                ->select('d.svd_first', 'd.svd_lan', 'bd.mh_id', 'd.svd_ghichu', 'mh.mh_loai', 'd.svd_loai', 'd.svd_dieukien', 'd.svd_khongdatloai', 'd.svd_lan', 'bd.dt_id')
                ->get();

            // Số lần
            $sinhVien->svd_lanthihientai = $numberExamCurrent;

            $dxtn_sv = DB::table('qlsv_dotxettotnghiep_sinhvien as dxtn_sv')
                ->join('qlsv_sinhvien as sv', function ($join) {
                    $join->on('sv.sv_id', '=', 'dxtn_sv.sv_id');
                })
                ->join('qlsv_lophoc as lh', function ($join) {
                    $join->on('lh.lh_id', '=', 'dxtn_sv.lh_id');
                })
                ->join('qlsv_khoadaotao as kdt', function ($join) {
                    $join->on('kdt.kdt_id', '=', 'lh.kdt_id');
                })
                ->where('dxtn_sv.lh_id', '=', $lhId)
                ->where('dxtn_sv.dxtn_id', '=', $dxtn_id)
                ->where('dxtn_sv.sv_id', $sinhVien->sv_id)
                ->select(DB::raw('DISTINCT dxtn_sv.svxtn_dattn, dxtn_sv.svxtn_vipham, dxtn_sv.svxtn_ghichu'))
                ->first();


            if ($dxtn_sv) {
                $sinhVien->svxtn_dattn = $dxtn_sv->svxtn_dattn;
                $sinhVien->svxtn_vipham = $dxtn_sv->svxtn_vipham;
                $sinhVien->svxtn_ghichu = $dxtn_sv->svxtn_ghichu;
            }
            // else {
            //     if ($dxtn_sv == null) {
            //         dd($sinhVien);
            //     }
            //     $sinhVien->svxtn_dattn = 0;
            //     $sinhVien->svxtn_vipham = NULL;
            //     $sinhVien->svxtn_ghichu = NULL;
            // }

            //2 chinhs tri, 3 thuc hanh, 4 ly thuyet, 5 bao ve
            $sinhVien->thuchanhlan1 = -1;
            $sinhVien->lythuyetlan1 = -1;
            $sinhVien->chinhtrilan1 = -1;
            $sinhVien->khoaluanlan1 = -1;

            $sinhVien->thuchanhlan2 = -1;
            $sinhVien->lythuyetlan2 = -1;
            $sinhVien->chinhtrilan2 = -1;
            $sinhVien->khoaluanlan2 = -1;

            $sinhVien->thuchanhlan3 = -1;
            $sinhVien->lythuyetlan3 = -1;
            $sinhVien->chinhtrilan3 = -1;
            $sinhVien->khoaluanlan3 = -1;

            foreach ($diemdotthi as $ddt) {
                $sinhVien->svd_ghichu = $ddt->svd_ghichu;
                $sinhVien->svd_loai = $ddt->svd_loai;
                $sinhVien->svd_khongdatloai = $ddt->svd_khongdatloai;

                if ($ddt->dt_id == $dt_id) {
                    $sinhVien->svd_dieukien = $ddt->svd_dieukien;
                }
                if ($ddt->svd_lan == 1) {
                    if ($ddt->mh_loai == 2) {
                        $sinhVien->chinhtrilan1 = $ddt->svd_first;
                    } else if ($ddt->mh_loai == 3) {
                        $sinhVien->thuchanhlan1 = $ddt->svd_first;
                    } else if ($ddt->mh_loai == 4) {
                        $sinhVien->lythuyetlan1 = $ddt->svd_first;
                    } else if ($ddt->mh_loai == 5) {
                        $sinhVien->khoaluanlan1 = $ddt->svd_first;
                    }
                } else if ($ddt->svd_lan == 2) {
                    if ($ddt->mh_loai == 2) {
                        $sinhVien->chinhtrilan2 = $ddt->svd_first;
                    } else if ($ddt->mh_loai == 3) {
                        $sinhVien->thuchanhlan2 = $ddt->svd_first;
                    } else if ($ddt->mh_loai == 4) {
                        $sinhVien->lythuyetlan2 = $ddt->svd_first;
                    } else if ($ddt->mh_loai == 5) {
                        $sinhVien->khoaluanlan2 = $ddt->svd_first;
                    }
                } else if ($ddt->svd_lan == 3) {
                    if ($ddt->mh_loai == 2) {
                        $sinhVien->chinhtrilan3 = $ddt->svd_first;
                    } else if ($ddt->mh_loai == 3) {
                        $sinhVien->thuchanhlan3 = $ddt->svd_first;
                    } else if ($ddt->mh_loai == 4) {
                        $sinhVien->lythuyetlan3 = $ddt->svd_first;
                    } else if ($ddt->mh_loai == 5) {
                        $sinhVien->khoaluanlan3 = $ddt->svd_first;
                    }
                }
            }

            $diemchinhtri = $sinhVien->chinhtrilan1;
            $diemlythuyet = $sinhVien->lythuyetlan1;
            $diemthuchanh = $sinhVien->thuchanhlan1;
            $diemkhoaluan = $sinhVien->khoaluanlan1;


            if ($sinhVien->khoaluanlan2 != -1) {
                $diemkhoaluan = $sinhVien->khoaluanlan2;
            } else if ($sinhVien->khoaluanlan3 != -1) {
                $diemkhoaluan = $sinhVien->khoaluanlan3;
            }

            if ($sinhVien->thuchanhlan2 != -1) {
                $diemthuchanh = $sinhVien->thuchanhlan2;
            } else if ($sinhVien->thuchanhlan3 != -1) {
                $diemthuchanh = $sinhVien->thuchanhlan3;
            }

            if ($sinhVien->lythuyetlan2 != -1) {
                $diemlythuyet = $sinhVien->lythuyetlan2;
            } else if ($sinhVien->lythuyetlan3 != -1) {
                $diemlythuyet = $sinhVien->lythuyetlan3;
            }

            if ($sinhVien->lythuyetlan2 != -1) {
                $diemlythuyet = $sinhVien->lythuyetlan2;
            } else if ($sinhVien->lythuyetlan3 != -1) {
                $diemlythuyet = $sinhVien->lythuyetlan3;
            }

            $sinhVien->toanKhoa->ghiChuThiLai = "";

            if ($diemchinhtri < 5 && $diemchinhtri != -1 && $diemkhoaluan == -1) {
                $sinhVien->toanKhoa->ghiChuThiLai .= 'ĐTNCT(L1:' . number_format($sinhVien->chinhtrilan1, 1);
                if ($sinhVien->chinhtrilan2 != -1) {
                    $sinhVien->toanKhoa->ghiChuThiLai .= ', L2:' . number_format($sinhVien->chinhtrilan2, 1);
                }
                if ($sinhVien->chinhtrilan3 != -1) {
                    $sinhVien->toanKhoa->ghiChuThiLai .= ', L3:' . number_format($sinhVien->chinhtrilan3, 1);
                }
                $sinhVien->toanKhoa->ghiChuThiLai .= '), ';
            }

            if ($diemlythuyet < 5 && $diemkhoaluan == -1) {
                $sinhVien->toanKhoa->ghiChuThiLai .= 'ĐTNLT(L1:' . number_format($sinhVien->lythuyetlan1, 1);
                if ($sinhVien->lythuyetlan2 != -1) {
                    $sinhVien->toanKhoa->ghiChuThiLai .= ', L2:' . number_format($sinhVien->lythuyetlan2, 1);
                }
                if ($sinhVien->lythuyetlan3 != -1) {
                    $sinhVien->toanKhoa->ghiChuThiLai .= ', L3:' . number_format($sinhVien->lythuyetlan3, 1);
                }
                $sinhVien->toanKhoa->ghiChuThiLai .= '), ';
            }

            if ($diemthuchanh < 5 && $diemkhoaluan == -1) {
                $sinhVien->toanKhoa->ghiChuThiLai .= 'ĐTNTH(L1:' . number_format($sinhVien->thuchanhlan1, 1);
                if ($sinhVien->thuchanhlan2 != -1) {
                    $sinhVien->toanKhoa->ghiChuThiLai .= ', L2:' . number_format($sinhVien->thuchanhlan2, 1);
                }
                if ($sinhVien->thuchanhlan3 != -1) {
                    $sinhVien->toanKhoa->ghiChuThiLai .= ', L3:' . number_format($sinhVien->thuchanhlan3, 1);
                }
                $sinhVien->toanKhoa->ghiChuThiLai .= '), ';
            }

            if ($diemkhoaluan < 5 && $diemkhoaluan != -1) {
                $sinhVien->toanKhoa->ghiChuThiLai .= 'ĐTNCD(L1:' . number_format($sinhVien->khoaluanlan1, 1);
                if ($sinhVien->khoaluanlan2 != -1) {
                    $sinhVien->toanKhoa->ghiChuThiLai .= ', L2:' . number_format($sinhVien->khoaluanlan2, 1);
                }
                if ($sinhVien->khoaluanlan3 != -1) {
                    $sinhVien->toanKhoa->ghiChuThiLai .= ', L3:' . number_format($sinhVien->khoaluanlan3, 1);
                }
                $sinhVien->toanKhoa->ghiChuThiLai .= '), ';
            }

            if (isset($sinhVien->toanKhoa->avg)) {
                $filter = new Filters;
                $SvDatThiTN = $filter->SvDatThiTN($lhId, $sinhVien);
                if ($SvDatThiTN == 1) {
                    $sinhVien->toanKhoa->avg = $sinhVien->toanKhoa->tichLuy;
                }

                if ($diemkhoaluan != -1) {
                    $sinhVien->toanKhoa->avg_totnghiep = number_format((($sinhVien->toanKhoa->avg * 3) + ($diemkhoaluan * 2)) / 5, 1);
                } else {
                    $sinhVien->toanKhoa->avg_totnghiep = number_format(($diemlythuyet + ($diemthuchanh * 2) + ($sinhVien->toanKhoa->avg * 3)) / 6, 1);
                }
                $sinhVien->toanKhoa->hocLucTN = $this->getHocLucByDiem($sinhVien->toanKhoa->avg_totnghiep, $quyChe2022);
            }
        }

        return $data;
    }

    public function getKetQuaHocTapTheoDotThiTest($semester, $lhId, $dt_id, $dxtn_id, $svId = 0)
    {
        $lopHoc = LopHoc::find($lhId);
        $quyChe2022 = $lopHoc->lh_nienche == 1;
        $data = $this->getKetQuaHocTap($semester, $lhId);
        //$modalDotThi = DotThi::find($dt_id);
        // $data['danhSachSinhVien'] = $data['danhSachSinhVien']->reject(function ($item, $key) use ($lhId, $dt_id) {
        //     return DB::select(
        //         'select d.* from qlsv_dotthi_diem d inner join qlsv_dotthi_bangdiem bd on bd.dt_bd_id = d.dt_bd_id  where bd.lh_id = ? AND bd.dt_id = ? AND d.sv_id = ?',
        //         [$lhId, $dt_id, $item['sv_id']]
        //     ) == null;
        // });

        $data['danhSachSinhVien'] = $data['danhSachSinhVien']->reject(function ($item, $key) use ($dxtn_id) {
            return DB::select(
                'select distinct dxtn_sv.* from qlsv_dotxettotnghiep_sinhvien as dxtn_sv where dxtn_sv.dxtn_id = ? AND dxtn_sv.sv_id = ?',
                [$dxtn_id, $item['sv_id']]
            ) == null;
        });


        // SELECT MAX(dtd.svd_lan) FROM qlsv_dotthi_diem dtd
        // JOIN qlsv_dotthi_bangdiem dtbd ON dtd.dt_id = dtbd.dt_id
        // WHERE dtd.dt_id = 1 AND dtbd.lh_id = 20;
        // -- 2


        // SELECT DISTINCT dtd.dt_id FROM qlsv_dotthi_diem dtd
        // JOIN qlsv_dotthi_bangdiem dtbd ON dtd.dt_id = dtbd.dt_id
        // WHERE dtbd.lh_id = 20 AND dtd.dt_id <= 3
        // ORDER BY dtd.dt_id desc
        // LIMIT 3;
        // -- 7 10

        // SELECT * FROM qlsv_dotthi_diem dtd
        // JOIN qlsv_dotthi_bangdiem dtbd ON dtd.dt_id = dtbd.dt_id
        // WHERE dtd.dt_id = 4 AND dtbd.lh_id = 20;


        $numberExamPresent = DB::table('qlsv_dotthi_diem as dtd')
            ->join('qlsv_dotthi_bangdiem as bd', 'bd.dt_bd_id', '=', 'dtd.dt_bd_id')
            ->where('bd.dt_id', $dt_id)
            ->where('bd.lh_id', $lhId)
            ->max('dtd.svd_lan');

        $numberExamreCently = DB::table('qlsv_dotthi_diem as dtd')
            ->join('qlsv_dotthi_bangdiem as dtbd', 'dtd.dt_bd_id', '=', 'dtbd.dt_bd_id')
            ->where('dtbd.lh_id', $lhId)
            ->where('dtd.dt_id', '<=', $dt_id)
            ->select('dtd.dt_id')
            ->distinct()
            ->orderBy('dtd.dt_id', 'desc')
            ->limit(3)
            ->pluck('dt_id')
            ->toArray();


        $dxtn_id = DotThiDotXetTotNghiep::where('dt_id', $dt_id)->first()->dxtn_id;
        //tính điểm môn học
        foreach ($data['danhSachSinhVien'] as $index => $sinhVien) {
            //-1 là chưa đi thi null là vắng thi
            // $TBToanKhoa = isset($sinhVien->toanKhoa->avg) ? $sinhVien->toanKhoa->avg : -1;
            $diemdotthi = DB::table('qlsv_dotthi_diem as d')
                ->join('qlsv_dotthi_bangdiem as bd', 'bd.dt_bd_id', '=', 'd.dt_bd_id')
                ->join('qlsv_monhoc as mh', 'mh.mh_id', '=', 'bd.mh_id')
                ->where('d.sv_id', $sinhVien->sv_id)
                ->whereIn('d.dt_id', $numberExamreCently)
                ->select('d.svd_first', 'd.svd_lan', 'bd.mh_id', 'd.svd_ghichu', 'mh.mh_loai', 'd.svd_loai', 'd.svd_dieukien', 'd.svd_khongdatloai', 'd.svd_lan', 'bd.dt_id')
                ->get();

            // Số lần
            $sinhVien->svd_lanthihientai = $numberExamPresent;

            $dxtn_sv = DB::table('qlsv_dotxettotnghiep_sinhvien as dxtn_sv')
                ->join('qlsv_sinhvien as sv', function ($join) {
                    $join->on('sv.sv_id', '=', 'dxtn_sv.sv_id');
                })
                ->join('qlsv_lophoc as lh', function ($join) {
                    $join->on('lh.lh_id', '=', 'dxtn_sv.lh_id');
                })
                ->join('qlsv_khoadaotao as kdt', function ($join) {
                    $join->on('kdt.kdt_id', '=', 'lh.kdt_id');
                })
                ->where('dxtn_sv.lh_id', '=', $lhId)
                ->where('dxtn_sv.dxtn_id', '=', $dxtn_id)
                ->where('dxtn_sv.sv_id', $sinhVien->sv_id)
                // ->whereRaw('dxtn_sv.svxtn_dattn = 1')
                ->select(DB::raw('DISTINCT dxtn_sv.svxtn_dattn, dxtn_sv.temp_xltn, dxtn_sv.final_xltn, dxtn_sv.svxtn_vipham, dxtn_sv.svxtn_ghichu'))
                ->first();


            if ($dxtn_sv) {
                $sinhVien->temp_xltn = $dxtn_sv->temp_xltn;
                $sinhVien->final_xltn = $dxtn_sv->final_xltn;
                $sinhVien->svxtn_dattn = $dxtn_sv->svxtn_dattn;
                $sinhVien->svxtn_vipham = $dxtn_sv->svxtn_vipham;
                $sinhVien->svxtn_ghichu = $dxtn_sv->svxtn_ghichu;
            }

            //2 chinhs tri, 3 thuc hanh, 4 ly thuyet, 5 bao ve
            $sinhVien->thuchanhlan1 = -1;
            $sinhVien->lythuyetlan1 = -1;
            $sinhVien->chinhtrilan1 = -1;
            $sinhVien->khoaluanlan1 = -1;

            $sinhVien->thuchanhlan2 = -1;
            $sinhVien->lythuyetlan2 = -1;
            $sinhVien->chinhtrilan2 = -1;
            $sinhVien->khoaluanlan2 = -1;

            $sinhVien->thuchanhlan3 = -1;
            $sinhVien->lythuyetlan3 = -1;
            $sinhVien->chinhtrilan3 = -1;
            $sinhVien->khoaluanlan3 = -1;

            foreach ($diemdotthi as $ddt) {
                $sinhVien->svd_ghichu = $ddt->svd_ghichu;
                $sinhVien->svd_loai = $ddt->svd_loai;
                $sinhVien->svd_khongdatloai = $ddt->svd_khongdatloai;

                if ($ddt->dt_id == $dt_id) {
                    $sinhVien->svd_dieukien = $ddt->svd_dieukien;
                }
                if ($ddt->svd_lan == 1) {
                    if ($ddt->mh_loai == 2) {
                        $sinhVien->chinhtrilan1 = $ddt->svd_first;
                    } else if ($ddt->mh_loai == 3) {
                        $sinhVien->thuchanhlan1 = $ddt->svd_first;
                    } else if ($ddt->mh_loai == 4) {
                        $sinhVien->lythuyetlan1 = $ddt->svd_first;
                    } else if ($ddt->mh_loai == 5) {
                        $sinhVien->khoaluanlan1 = $ddt->svd_first;
                    }
                } else if ($ddt->svd_lan == 2) {
                    if ($ddt->mh_loai == 2) {
                        $sinhVien->chinhtrilan2 = $ddt->svd_first;
                    } else if ($ddt->mh_loai == 3) {
                        $sinhVien->thuchanhlan2 = $ddt->svd_first;
                    } else if ($ddt->mh_loai == 4) {
                        $sinhVien->lythuyetlan2 = $ddt->svd_first;
                    } else if ($ddt->mh_loai == 5) {
                        $sinhVien->khoaluanlan2 = $ddt->svd_first;
                    }
                } else if ($ddt->svd_lan == 3) {
                    if ($ddt->mh_loai == 2) {
                        $sinhVien->chinhtrilan3 = $ddt->svd_first;
                    } else if ($ddt->mh_loai == 3) {
                        $sinhVien->thuchanhlan3 = $ddt->svd_first;
                    } else if ($ddt->mh_loai == 4) {
                        $sinhVien->lythuyetlan3 = $ddt->svd_first;
                    } else if ($ddt->mh_loai == 5) {
                        $sinhVien->khoaluanlan3 = $ddt->svd_first;
                    }
                }
            }

            $diemchinhtri = $sinhVien->chinhtrilan1;
            $diemlythuyet = $sinhVien->lythuyetlan1;
            $diemthuchanh = $sinhVien->thuchanhlan1;
            $diemkhoaluan = $sinhVien->khoaluanlan1;


            if ($sinhVien->khoaluanlan2 != -1) {
                $diemkhoaluan = $sinhVien->khoaluanlan2;
            } else if ($sinhVien->khoaluanlan3 != -1) {
                $diemkhoaluan = $sinhVien->khoaluanlan3;
            }

            if ($sinhVien->thuchanhlan2 != -1) {
                $diemthuchanh = $sinhVien->thuchanhlan2;
            } else if ($sinhVien->thuchanhlan3 != -1) {
                $diemthuchanh = $sinhVien->thuchanhlan3;
            }

            if ($sinhVien->lythuyetlan2 != -1) {
                $diemlythuyet = $sinhVien->lythuyetlan2;
            } else if ($sinhVien->lythuyetlan3 != -1) {
                $diemlythuyet = $sinhVien->lythuyetlan3;
            }

            if ($sinhVien->lythuyetlan2 != -1) {
                $diemlythuyet = $sinhVien->lythuyetlan2;
            } else if ($sinhVien->lythuyetlan3 != -1) {
                $diemlythuyet = $sinhVien->lythuyetlan3;
            }

            $sinhVien->toanKhoa->ghiChuThiLai = "";

            if ($diemchinhtri < 5 && $diemchinhtri != -1 && $diemkhoaluan == -1) {
                $sinhVien->toanKhoa->ghiChuThiLai .= 'ĐTNCT(L1:' . number_format($sinhVien->chinhtrilan1, 1);
                if ($sinhVien->chinhtrilan2 != -1) {
                    $sinhVien->toanKhoa->ghiChuThiLai .= ', L2:' . number_format($sinhVien->chinhtrilan2, 1);
                }
                if ($sinhVien->chinhtrilan3 != -1) {
                    $sinhVien->toanKhoa->ghiChuThiLai .= ', L3:' . number_format($sinhVien->chinhtrilan3, 1);
                }
                $sinhVien->toanKhoa->ghiChuThiLai .= '), ';
            }

            if ($diemlythuyet < 5 && $diemkhoaluan == -1) {
                $sinhVien->toanKhoa->ghiChuThiLai .= 'ĐTNLT(L1:' . number_format($sinhVien->lythuyetlan1, 1);
                if ($sinhVien->lythuyetlan2 != -1) {
                    $sinhVien->toanKhoa->ghiChuThiLai .= ', L2:' . number_format($sinhVien->lythuyetlan2, 1);
                }
                if ($sinhVien->lythuyetlan3 != -1) {
                    $sinhVien->toanKhoa->ghiChuThiLai .= ', L3:' . number_format($sinhVien->lythuyetlan3, 1);
                }
                $sinhVien->toanKhoa->ghiChuThiLai .= '), ';
            }

            if ($diemthuchanh < 5 && $diemkhoaluan == -1) {
                $sinhVien->toanKhoa->ghiChuThiLai .= 'ĐTNTH(L1:' . number_format($sinhVien->thuchanhlan1, 1);
                if ($sinhVien->thuchanhlan2 != -1) {
                    $sinhVien->toanKhoa->ghiChuThiLai .= ', L2:' . number_format($sinhVien->thuchanhlan2, 1);
                }
                if ($sinhVien->thuchanhlan3 != -1) {
                    $sinhVien->toanKhoa->ghiChuThiLai .= ', L3:' . number_format($sinhVien->thuchanhlan3, 1);
                }
                $sinhVien->toanKhoa->ghiChuThiLai .= '), ';
            }

            if ($diemkhoaluan < 5 && $diemkhoaluan != -1) {
                $sinhVien->toanKhoa->ghiChuThiLai .= 'ĐTNCD(L1:' . number_format($sinhVien->khoaluanlan1, 1);
                if ($sinhVien->khoaluanlan2 != -1) {
                    $sinhVien->toanKhoa->ghiChuThiLai .= ', L2:' . number_format($sinhVien->khoaluanlan2, 1);
                }
                if ($sinhVien->khoaluanlan3 != -1) {
                    $sinhVien->toanKhoa->ghiChuThiLai .= ', L3:' . number_format($sinhVien->khoaluanlan3, 1);
                }
                $sinhVien->toanKhoa->ghiChuThiLai .= '), ';
            }

            if (isset($sinhVien->toanKhoa->avg)) {
                $sinhVien->toanKhoa->avg = $sinhVien->toanKhoa->tichLuy;
                if ($diemkhoaluan != -1) {
                    $sinhVien->toanKhoa->avg_totnghiep = number_format((($sinhVien->toanKhoa->avg * 3) + ($diemkhoaluan * 2)) / 5, 1);
                } else {
                    $sinhVien->toanKhoa->avg_totnghiep = number_format(($diemlythuyet + ($diemthuchanh * 2) + ($sinhVien->toanKhoa->avg * 3)) / 6, 1);
                }

                //$sinhVien->toanKhoa->hocLucTN = $this->getHocLucByDiem($sinhVien->toanKhoa->avg_totnghiep, $quyChe2022);
                $sinhVien->toanKhoa->hocLucTN = $this->getHocLucByDB($sinhVien->final_xltn, $quyChe2022);

                $sinhVien->giamXltn = false;
                if ($sinhVien->final_xltn > $sinhVien->temp_xltn) {
                    $sinhVien->giamXltn = true;
                }
            }
        }

        return $data;
    }

    public function getKetQuaHocTapCapNhatDotXetChoDotThi($semester, $lhId, $dt_id, $svId = 0)
    {
        $lopHoc = LopHoc::find($lhId);
        $quyChe2022 = $lopHoc->lh_nienche == 1;

        $data = $this->getKetQuaHocTap($semester, $lhId, $svId, true);

        //$modalDotThi = DotThi::find($dt_id);
        $data['danhSachSinhVien'] = $data['danhSachSinhVien']->reject(function ($item, $key) use ($lhId, $dt_id) {
            return DB::select(
                'select d.* from qlsv_dotthi_diem d inner join qlsv_dotthi_bangdiem bd on bd.dt_bd_id = d.dt_bd_id  where bd.lh_id = ? AND bd.dt_id = ? AND d.sv_id = ?',
                [$lhId, $dt_id, $item['sv_id']]
            ) == null;
        });

        // SELECT MAX(dtd.svd_lan) FROM qlsv_dotthi_diem dtd
        // JOIN qlsv_dotthi_bangdiem dtbd ON dtd.dt_id = dtbd.dt_id
        // WHERE dtd.dt_id = 1 AND dtbd.lh_id = 20;
        // -- 2


        // SELECT DISTINCT dtd.dt_id FROM qlsv_dotthi_diem dtd
        // JOIN qlsv_dotthi_bangdiem dtbd ON dtd.dt_id = dtbd.dt_id
        // WHERE dtbd.lh_id = 20 AND dtd.dt_id <= 3
        // ORDER BY dtd.dt_id desc
        // LIMIT 3;
        // -- 7 10

        // SELECT * FROM qlsv_dotthi_diem dtd
        // JOIN qlsv_dotthi_bangdiem dtbd ON dtd.dt_id = dtbd.dt_id
        // WHERE dtd.dt_id = 4 AND dtbd.lh_id = 20;


        $numberExamPresent = DB::table('qlsv_dotthi_diem as dtd')
            ->join('qlsv_dotthi_bangdiem as bd', 'bd.dt_bd_id', '=', 'dtd.dt_bd_id')
            ->where('bd.dt_id', $dt_id)
            ->where('bd.lh_id', $lhId)
            ->max('dtd.svd_lan');

        $numberExamreCently = DB::table('qlsv_dotthi_diem as dtd')
            ->join('qlsv_dotthi_bangdiem as dtbd', 'dtd.dt_bd_id', '=', 'dtbd.dt_bd_id')
            ->where('dtbd.lh_id', $lhId)
            ->where('dtd.dt_id', '<=', $dt_id)
            ->select('dtd.dt_id')
            ->distinct()
            ->orderBy('dtd.dt_id', 'desc')
            ->limit(3)
            ->pluck('dt_id')
            ->toArray();


        $dxtn_id = DotThiDotXetTotNghiep::where('dt_id', $dt_id)->first()->dxtn_id;
        //tính điểm môn học
        foreach ($data['danhSachSinhVien'] as $index => $sinhVien) {
            //-1 là chưa đi thi null là vắng thi
            // $TBToanKhoa = isset($sinhVien->toanKhoa->avg) ? $sinhVien->toanKhoa->avg : -1;
            $diemdotthi = DB::table('qlsv_dotthi_diem as d')
                ->join('qlsv_dotthi_bangdiem as bd', 'bd.dt_bd_id', '=', 'd.dt_bd_id')
                ->join('qlsv_monhoc as mh', 'mh.mh_id', '=', 'bd.mh_id')
                ->where('d.sv_id', $sinhVien->sv_id)
                ->whereIn('d.dt_id', $numberExamreCently)
                ->select('d.svd_first', 'd.svd_lan', 'bd.mh_id', 'd.svd_ghichu', 'mh.mh_loai', 'd.svd_loai', 'd.svd_dieukien', 'd.svd_khongdatloai', 'd.svd_lan', 'bd.dt_id')
                ->get();


            // Số lần
            $sinhVien->svd_lanthihientai = $numberExamPresent;

            $dxtn_sv = DB::table('qlsv_dotxettotnghiep_sinhvien as dxtn_sv')
                ->join('qlsv_sinhvien as sv', function ($join) {
                    $join->on('sv.sv_id', '=', 'dxtn_sv.sv_id');
                })
                ->join('qlsv_lophoc as lh', function ($join) {
                    $join->on('lh.lh_id', '=', 'dxtn_sv.lh_id');
                })
                ->join('qlsv_khoadaotao as kdt', function ($join) {
                    $join->on('kdt.kdt_id', '=', 'lh.kdt_id');
                })
                ->where('dxtn_sv.lh_id', '=', $lhId)
                ->where('dxtn_sv.dxtn_id', '=', $dxtn_id)
                ->where('dxtn_sv.sv_id', $sinhVien->sv_id)
                // ->whereRaw('dxtn_sv.svxtn_dattn = 1')
                ->select(DB::raw('DISTINCT dxtn_sv.svxtn_dattn, dxtn_sv.temp_xltn, dxtn_sv.final_xltn, dxtn_sv.ghichu_tlhl, dxtn_sv.svxtn_vipham, dxtn_sv.svxtn_ghichu'))
                ->first();


            if ($dxtn_sv) {
                $sinhVien->temp_xltn = $dxtn_sv->temp_xltn;
                $sinhVien->final_xltn = $dxtn_sv->final_xltn;
                $sinhVien->svxtn_dattn = $dxtn_sv->svxtn_dattn;
                $sinhVien->toanKhoa->ghichu_tlhl = $dxtn_sv->ghichu_tlhl;
                $sinhVien->svxtn_vipham = $dxtn_sv->svxtn_vipham;
                $sinhVien->svxtn_ghichu = $dxtn_sv->svxtn_ghichu;
            }

            //2 chinhs tri, 3 thuc hanh, 4 ly thuyet, 5 bao ve
            $sinhVien->thuchanhlan1 = -1;
            $sinhVien->lythuyetlan1 = -1;
            $sinhVien->chinhtrilan1 = -1;
            $sinhVien->khoaluanlan1 = -1;

            $sinhVien->thuchanhlan2 = -1;
            $sinhVien->lythuyetlan2 = -1;
            $sinhVien->chinhtrilan2 = -1;
            $sinhVien->khoaluanlan2 = -1;

            $sinhVien->thuchanhlan3 = -1;
            $sinhVien->lythuyetlan3 = -1;
            $sinhVien->chinhtrilan3 = -1;
            $sinhVien->khoaluanlan3 = -1;

            foreach ($diemdotthi as $ddt) {
                $sinhVien->svd_ghichu = $ddt->svd_ghichu;
                $sinhVien->svd_loai = $ddt->svd_loai;
                $sinhVien->svd_khongdatloai = $ddt->svd_khongdatloai;

                if ($ddt->dt_id == $dt_id) {
                    $sinhVien->svd_dieukien = $ddt->svd_dieukien;
                }
                if ($ddt->svd_lan == 1) {
                    if ($ddt->mh_loai == 2) {
                        $sinhVien->chinhtrilan1 = $ddt->svd_first;
                    } else if ($ddt->mh_loai == 3) {
                        $sinhVien->thuchanhlan1 = $ddt->svd_first;
                    } else if ($ddt->mh_loai == 4) {
                        $sinhVien->lythuyetlan1 = $ddt->svd_first;
                    } else if ($ddt->mh_loai == 5) {
                        $sinhVien->khoaluanlan1 = $ddt->svd_first;
                    }
                } else if ($ddt->svd_lan == 2) {
                    if ($ddt->mh_loai == 2) {
                        $sinhVien->chinhtrilan2 = $ddt->svd_first;
                    } else if ($ddt->mh_loai == 3) {
                        $sinhVien->thuchanhlan2 = $ddt->svd_first;
                    } else if ($ddt->mh_loai == 4) {
                        $sinhVien->lythuyetlan2 = $ddt->svd_first;
                    } else if ($ddt->mh_loai == 5) {
                        $sinhVien->khoaluanlan2 = $ddt->svd_first;
                    }
                } else if ($ddt->svd_lan == 3) {
                    if ($ddt->mh_loai == 2) {
                        $sinhVien->chinhtrilan3 = $ddt->svd_first;
                    } else if ($ddt->mh_loai == 3) {
                        $sinhVien->thuchanhlan3 = $ddt->svd_first;
                    } else if ($ddt->mh_loai == 4) {
                        $sinhVien->lythuyetlan3 = $ddt->svd_first;
                    } else if ($ddt->mh_loai == 5) {
                        $sinhVien->khoaluanlan3 = $ddt->svd_first;
                    }
                }
            }

            $diemchinhtri = $sinhVien->chinhtrilan1;
            $diemlythuyet = $sinhVien->lythuyetlan1;
            $diemthuchanh = $sinhVien->thuchanhlan1;
            $diemkhoaluan = $sinhVien->khoaluanlan1;


            if ($sinhVien->khoaluanlan2 != -1) {
                $diemkhoaluan = $sinhVien->khoaluanlan2;
            } else if ($sinhVien->khoaluanlan3 != -1) {
                $diemkhoaluan = $sinhVien->khoaluanlan3;
            }

            if ($sinhVien->thuchanhlan2 != -1) {
                $diemthuchanh = $sinhVien->thuchanhlan2;
            } else if ($sinhVien->thuchanhlan3 != -1) {
                $diemthuchanh = $sinhVien->thuchanhlan3;
            }

            if ($sinhVien->lythuyetlan2 != -1) {
                $diemlythuyet = $sinhVien->lythuyetlan2;
            } else if ($sinhVien->lythuyetlan3 != -1) {
                $diemlythuyet = $sinhVien->lythuyetlan3;
            }

            if ($sinhVien->lythuyetlan2 != -1) {
                $diemlythuyet = $sinhVien->lythuyetlan2;
            } else if ($sinhVien->lythuyetlan3 != -1) {
                $diemlythuyet = $sinhVien->lythuyetlan3;
            }

            $sinhVien->toanKhoa->ghiChuThiLai = "";
            $sinhVien->toanKhoa->ghiChuThiLaiArray = collect([]);

            if ($diemchinhtri < 5 && $diemchinhtri != -1 && $diemkhoaluan == -1) {
                $sinhVien->toanKhoa->ghiChuThiLai .= 'ĐTNCT(L1:' . number_format($sinhVien->chinhtrilan1, 1);
                $keyMonTLTemp = 'ĐTNCT(L1)';
                if ($sinhVien->chinhtrilan2 != -1) {
                    $sinhVien->toanKhoa->ghiChuThiLai .= ', L2:' . number_format($sinhVien->chinhtrilan2, 1);
                    $keyMonTLTemp = 'ĐTNCT(L2)';
                }
                if ($sinhVien->chinhtrilan3 != -1) {
                    $sinhVien->toanKhoa->ghiChuThiLai .= ', L3:' . number_format($sinhVien->chinhtrilan3, 1);
                    $keyMonTLTemp = 'ĐTNCT(L3)';
                }
                $sinhVien->toanKhoa->ghiChuThiLai .= '), ';
                $sinhVien->toanKhoa->ghiChuThiLaiArray->push([
                    'type' => 'TL',
                    'key' => $keyMonTLTemp,
                    'mh_id' => 885,
                    'mh_tichluy' => 0
                ]);
            }

            if ($diemlythuyet < 5 && $diemkhoaluan == -1) {
                $sinhVien->toanKhoa->ghiChuThiLai .= 'ĐTNLT(L1:' . number_format($sinhVien->lythuyetlan1, 1);
                $keyMonTLTemp = 'ĐTNLT(L1)';

                if ($sinhVien->lythuyetlan2 != -1) {
                    $sinhVien->toanKhoa->ghiChuThiLai .= ', L2:' . number_format($sinhVien->lythuyetlan2, 1);
                    $keyMonTLTemp = 'ĐTNLT(L2)';
                }
                if ($sinhVien->lythuyetlan3 != -1) {
                    $sinhVien->toanKhoa->ghiChuThiLai .= ', L3:' . number_format($sinhVien->lythuyetlan3, 1);
                    $keyMonTLTemp = 'ĐTNLT(L3)';
                }
                $sinhVien->toanKhoa->ghiChuThiLai .= '), ';
                $sinhVien->toanKhoa->ghiChuThiLaiArray->push([
                    'type' => 'TL',
                    'key' => $keyMonTLTemp,
                    'mh_id' => 887,
                    'mh_tichluy' => 0

                ]);
            }

            if ($diemthuchanh < 5 && $diemkhoaluan == -1) {
                $sinhVien->toanKhoa->ghiChuThiLai .= 'ĐTNTH(L1:' . number_format($sinhVien->thuchanhlan1, 1);
                $keyMonTLTemp = 'ĐTNTH(L1)';

                if ($sinhVien->thuchanhlan2 != -1) {
                    $sinhVien->toanKhoa->ghiChuThiLai .= ', L2:' . number_format($sinhVien->thuchanhlan2, 1);
                    $keyMonTLTemp = 'ĐTNTH(L2)';
                }
                if ($sinhVien->thuchanhlan3 != -1) {
                    $sinhVien->toanKhoa->ghiChuThiLai .= ', L3:' . number_format($sinhVien->thuchanhlan3, 1);
                    $keyMonTLTemp = 'ĐTNTH(L3)';
                }
                $sinhVien->toanKhoa->ghiChuThiLai .= '), ';
                $sinhVien->toanKhoa->ghiChuThiLaiArray->push([
                    'type' => 'TL',
                    'key' => $keyMonTLTemp,
                    'mh_id' => 886,
                    'mh_tichluy' => 0

                ]);
            }

            if ($diemkhoaluan < 5 && $diemkhoaluan != -1) {
                $sinhVien->toanKhoa->ghiChuThiLai .= 'ĐTNCD(L1:' . number_format($sinhVien->khoaluanlan1, 1);
                $keyMonTLTemp = 'ĐTNCD(L1)';

                if ($sinhVien->khoaluanlan2 != -1) {
                    $sinhVien->toanKhoa->ghiChuThiLai .= ', L2:' . number_format($sinhVien->khoaluanlan2, 1);
                    $keyMonTLTemp = 'ĐTNCD(L2)';
                }
                if ($sinhVien->khoaluanlan3 != -1) {
                    $sinhVien->toanKhoa->ghiChuThiLai .= ', L3:' . number_format($sinhVien->khoaluanlan3, 1);
                    $keyMonTLTemp = 'ĐTNCD(L3)';
                }
                $sinhVien->toanKhoa->ghiChuThiLai .= '), ';
                $sinhVien->toanKhoa->ghiChuThiLaiArray->push([
                    'type' => 'TL',
                    'key' => $keyMonTLTemp,
                    'mh_id' => 894,
                    'mh_tichluy' => 0
                ]);
            }

            if (isset($sinhVien->toanKhoa->avg)) {
                $sinhVien->toanKhoa->avg = $sinhVien->toanKhoa->tichLuy;
                if ($diemkhoaluan != -1) {
                    $sinhVien->toanKhoa->avg_totnghiep = number_format((($sinhVien->toanKhoa->avg * 3) + ($diemkhoaluan * 2)) / 5, 1);
                } else {
                    $sinhVien->toanKhoa->avg_totnghiep = number_format(($diemlythuyet + ($diemthuchanh * 2) + ($sinhVien->toanKhoa->avg * 3)) / 6, 1);
                }
                $sinhVien->toanKhoa->hocLucTN = $this->getHocLucByDiem($sinhVien->toanKhoa->avg_totnghiep, $quyChe2022);
            }
        }


        return $data;
    }


    public function getKetQuaHocTapTheoDotThiTN($semester, $lhId, $dt_id, $dxtn_id, $svId = 0)
    {
        $lopHoc = LopHoc::find($lhId);
        $quyChe2022 = $lopHoc->lh_nienche == 1;

        $data = $this->getKetQuaHocTap($semester, $lhId, $svId, true);

        // Ds sv trong dxtn hiện tại
        $dsSVDaDatTN = $data['danhSachSinhVien']->reject(function ($item, $key) use ($dxtn_id) {
            return DB::select(
                'select distinct dxtn_sv.* from qlsv_dotxettotnghiep_sinhvien as dxtn_sv where dxtn_sv.dxtn_id = ? AND dxtn_sv.sv_id = ?',
                [$dxtn_id, $item['sv_id']]
            ) == null;
        })->map(function ($item) {
            // Thêm thuộc tính "svd_dieukien" cho các sinh viên
            $item['svd_dieukien'] = 1;
            return $item;
        });

        //$modalDotThi = DotThi::find($dt_id);
        $data['danhSachSinhVien'] = $data['danhSachSinhVien']->reject(function ($item, $key) use ($lhId, $dt_id) {
            return DB::select(
                'select d.* from qlsv_dotthi_diem d inner join qlsv_dotthi_bangdiem bd on bd.dt_bd_id = d.dt_bd_id  where bd.lh_id = ? AND bd.dt_id = ? AND d.sv_id = ?',
                [$lhId, $dt_id, $item['sv_id']]
            ) == null;
        });

        $data['danhSachSinhVien'] = $data['danhSachSinhVien']->concat($dsSVDaDatTN)->unique('sv_id');


        // SELECT MAX(dtd.svd_lan) FROM qlsv_dotthi_diem dtd
        // JOIN qlsv_dotthi_bangdiem dtbd ON dtd.dt_id = dtbd.dt_id
        // WHERE dtd.dt_id = 1 AND dtbd.lh_id = 20;
        // -- 2


        // SELECT DISTINCT dtd.dt_id FROM qlsv_dotthi_diem dtd
        // JOIN qlsv_dotthi_bangdiem dtbd ON dtd.dt_id = dtbd.dt_id
        // WHERE dtbd.lh_id = 20 AND dtd.dt_id <= 3
        // ORDER BY dtd.dt_id desc
        // LIMIT 3;
        // -- 7 10

        // SELECT * FROM qlsv_dotthi_diem dtd
        // JOIN qlsv_dotthi_bangdiem dtbd ON dtd.dt_id = dtbd.dt_id
        // WHERE dtd.dt_id = 4 AND dtbd.lh_id = 20;


        $numberExamPresent = DB::table('qlsv_dotthi_diem as dtd')
            ->join('qlsv_dotthi_bangdiem as bd', 'bd.dt_bd_id', '=', 'dtd.dt_bd_id')
            ->where('bd.dt_id', $dt_id)
            ->where('bd.lh_id', $lhId)
            ->max('dtd.svd_lan');

        $numberExamreCently = DB::table('qlsv_dotthi_diem as dtd')
            ->join('qlsv_dotthi_bangdiem as dtbd', 'dtd.dt_bd_id', '=', 'dtbd.dt_bd_id')
            ->where('dtbd.lh_id', $lhId)
            ->where('dtd.dt_id', '<=', $dt_id)
            ->select('dtd.dt_id')
            ->distinct()
            ->orderBy('dtd.dt_id', 'desc')
            ->limit(3)
            ->pluck('dt_id')
            ->toArray();


        $dxtn_id = DotThiDotXetTotNghiep::where('dt_id', $dt_id)->first()->dxtn_id;
        //tính điểm môn học
        foreach ($data['danhSachSinhVien'] as $index => $sinhVien) {
            //-1 là chưa đi thi null là vắng thi
            // $TBToanKhoa = isset($sinhVien->toanKhoa->avg) ? $sinhVien->toanKhoa->avg : -1;
            $diemdotthi = DB::table('qlsv_dotthi_diem as d')
                ->join('qlsv_dotthi_bangdiem as bd', 'bd.dt_bd_id', '=', 'd.dt_bd_id')
                ->join('qlsv_monhoc as mh', 'mh.mh_id', '=', 'bd.mh_id')
                ->where('d.sv_id', $sinhVien->sv_id)
                ->whereIn('d.dt_id', $numberExamreCently)
                ->select('d.svd_first', 'd.svd_lan', 'bd.mh_id', 'd.svd_ghichu', 'mh.mh_loai', 'd.svd_loai', 'd.svd_dieukien', 'd.svd_khongdatloai', 'd.svd_lan', 'bd.dt_id')
                ->get();


            // Số lần
            $sinhVien->svd_lanthihientai = $numberExamPresent;

            $dxtn_sv = DB::table('qlsv_dotxettotnghiep_sinhvien as dxtn_sv')
                ->join('qlsv_sinhvien as sv', function ($join) {
                    $join->on('sv.sv_id', '=', 'dxtn_sv.sv_id');
                })
                ->join('qlsv_lophoc as lh', function ($join) {
                    $join->on('lh.lh_id', '=', 'dxtn_sv.lh_id');
                })
                ->join('qlsv_khoadaotao as kdt', function ($join) {
                    $join->on('kdt.kdt_id', '=', 'lh.kdt_id');
                })
                ->where('dxtn_sv.lh_id', '=', $lhId)
                ->where('dxtn_sv.dxtn_id', '=', $dxtn_id)
                ->where('dxtn_sv.sv_id', $sinhVien->sv_id)
                // ->whereRaw('dxtn_sv.svxtn_dattn = 1')
                ->select(DB::raw('DISTINCT dxtn_sv.svxtn_dattn, dxtn_sv.temp_xltn, dxtn_sv.final_xltn, dxtn_sv.ghichu_tlhl, dxtn_sv.svxtn_vipham, dxtn_sv.svxtn_ghichu'))
                ->first();


            if ($dxtn_sv) {
                $sinhVien->temp_xltn = $dxtn_sv->temp_xltn;
                $sinhVien->final_xltn = $dxtn_sv->final_xltn;
                $sinhVien->svxtn_dattn = $dxtn_sv->svxtn_dattn;
                $sinhVien->toanKhoa->ghichu_tlhl = $dxtn_sv->ghichu_tlhl;
                $sinhVien->svxtn_vipham = $dxtn_sv->svxtn_vipham;
                $sinhVien->svxtn_ghichu = $dxtn_sv->svxtn_ghichu;
            }

            //2 chinhs tri, 3 thuc hanh, 4 ly thuyet, 5 bao ve
            $sinhVien->thuchanhlan1 = -1;
            $sinhVien->lythuyetlan1 = -1;
            $sinhVien->chinhtrilan1 = -1;
            $sinhVien->khoaluanlan1 = -1;

            $sinhVien->thuchanhlan2 = -1;
            $sinhVien->lythuyetlan2 = -1;
            $sinhVien->chinhtrilan2 = -1;
            $sinhVien->khoaluanlan2 = -1;

            $sinhVien->thuchanhlan3 = -1;
            $sinhVien->lythuyetlan3 = -1;
            $sinhVien->chinhtrilan3 = -1;
            $sinhVien->khoaluanlan3 = -1;

            foreach ($diemdotthi as $ddt) {
                $sinhVien->svd_ghichu = $ddt->svd_ghichu;
                $sinhVien->svd_loai = $ddt->svd_loai;
                $sinhVien->svd_khongdatloai = $ddt->svd_khongdatloai;

                if ($ddt->dt_id == $dt_id) {
                    $sinhVien->svd_dieukien = $ddt->svd_dieukien;
                }
                if ($ddt->svd_lan == 1) {
                    if ($ddt->mh_loai == 2) {
                        $sinhVien->chinhtrilan1 = $ddt->svd_first;
                    } else if ($ddt->mh_loai == 3) {
                        $sinhVien->thuchanhlan1 = $ddt->svd_first;
                    } else if ($ddt->mh_loai == 4) {
                        $sinhVien->lythuyetlan1 = $ddt->svd_first;
                    } else if ($ddt->mh_loai == 5) {
                        $sinhVien->khoaluanlan1 = $ddt->svd_first;
                    }
                } else if ($ddt->svd_lan == 2) {
                    if ($ddt->mh_loai == 2) {
                        $sinhVien->chinhtrilan2 = $ddt->svd_first;
                    } else if ($ddt->mh_loai == 3) {
                        $sinhVien->thuchanhlan2 = $ddt->svd_first;
                    } else if ($ddt->mh_loai == 4) {
                        $sinhVien->lythuyetlan2 = $ddt->svd_first;
                    } else if ($ddt->mh_loai == 5) {
                        $sinhVien->khoaluanlan2 = $ddt->svd_first;
                    }
                } else if ($ddt->svd_lan == 3) {
                    if ($ddt->mh_loai == 2) {
                        $sinhVien->chinhtrilan3 = $ddt->svd_first;
                    } else if ($ddt->mh_loai == 3) {
                        $sinhVien->thuchanhlan3 = $ddt->svd_first;
                    } else if ($ddt->mh_loai == 4) {
                        $sinhVien->lythuyetlan3 = $ddt->svd_first;
                    } else if ($ddt->mh_loai == 5) {
                        $sinhVien->khoaluanlan3 = $ddt->svd_first;
                    }
                }
            }

            $diemchinhtri = $sinhVien->chinhtrilan1;
            $diemlythuyet = $sinhVien->lythuyetlan1;
            $diemthuchanh = $sinhVien->thuchanhlan1;
            $diemkhoaluan = $sinhVien->khoaluanlan1;


            if ($sinhVien->khoaluanlan2 != -1) {
                $diemkhoaluan = $sinhVien->khoaluanlan2;
            } else if ($sinhVien->khoaluanlan3 != -1) {
                $diemkhoaluan = $sinhVien->khoaluanlan3;
            }

            if ($sinhVien->thuchanhlan2 != -1) {
                $diemthuchanh = $sinhVien->thuchanhlan2;
            } else if ($sinhVien->thuchanhlan3 != -1) {
                $diemthuchanh = $sinhVien->thuchanhlan3;
            }

            if ($sinhVien->lythuyetlan2 != -1) {
                $diemlythuyet = $sinhVien->lythuyetlan2;
            } else if ($sinhVien->lythuyetlan3 != -1) {
                $diemlythuyet = $sinhVien->lythuyetlan3;
            }

            if ($sinhVien->lythuyetlan2 != -1) {
                $diemlythuyet = $sinhVien->lythuyetlan2;
            } else if ($sinhVien->lythuyetlan3 != -1) {
                $diemlythuyet = $sinhVien->lythuyetlan3;
            }

            $sinhVien->toanKhoa->ghiChuThiLai = "";
            $sinhVien->toanKhoa->ghiChuThiLaiArray = collect([]);

            if ($diemchinhtri < 5 && $diemchinhtri != -1 && $diemkhoaluan == -1) {
                $sinhVien->toanKhoa->ghiChuThiLai .= 'ĐTNCT(L1:' . number_format($sinhVien->chinhtrilan1, 1);
                $keyMonTLTemp = 'ĐTNCT(L1)';
                if ($sinhVien->chinhtrilan2 != -1) {
                    $sinhVien->toanKhoa->ghiChuThiLai .= ', L2:' . number_format($sinhVien->chinhtrilan2, 1);
                    $keyMonTLTemp = 'ĐTNCT(L2)';
                }
                if ($sinhVien->chinhtrilan3 != -1) {
                    $sinhVien->toanKhoa->ghiChuThiLai .= ', L3:' . number_format($sinhVien->chinhtrilan3, 1);
                    $keyMonTLTemp = 'ĐTNCT(L3)';
                }
                $sinhVien->toanKhoa->ghiChuThiLai .= '), ';
                $sinhVien->toanKhoa->ghiChuThiLaiArray->push([
                    'type' => 'TL',
                    'key' => $keyMonTLTemp,
                    'mh_id' => 885
                ]);
            }

            if ($diemlythuyet < 5 && $diemkhoaluan == -1) {
                $sinhVien->toanKhoa->ghiChuThiLai .= 'ĐTNLT(L1:' . number_format($sinhVien->lythuyetlan1, 1);
                $keyMonTLTemp = 'ĐTNLT(L1)';

                if ($sinhVien->lythuyetlan2 != -1) {
                    $sinhVien->toanKhoa->ghiChuThiLai .= ', L2:' . number_format($sinhVien->lythuyetlan2, 1);
                    $keyMonTLTemp = 'ĐTNLT(L2)';
                }
                if ($sinhVien->lythuyetlan3 != -1) {
                    $sinhVien->toanKhoa->ghiChuThiLai .= ', L3:' . number_format($sinhVien->lythuyetlan3, 1);
                    $keyMonTLTemp = 'ĐTNLT(L3)';
                }
                $sinhVien->toanKhoa->ghiChuThiLai .= '), ';
                $sinhVien->toanKhoa->ghiChuThiLaiArray->push([
                    'type' => 'TL',
                    'key' => $keyMonTLTemp,
                    'mh_id' => 887
                ]);
            }

            if ($diemthuchanh < 5 && $diemkhoaluan == -1) {
                $sinhVien->toanKhoa->ghiChuThiLai .= 'ĐTNTH(L1:' . number_format($sinhVien->thuchanhlan1, 1);
                $keyMonTLTemp = 'ĐTNTH(L1)';

                if ($sinhVien->thuchanhlan2 != -1) {
                    $sinhVien->toanKhoa->ghiChuThiLai .= ', L2:' . number_format($sinhVien->thuchanhlan2, 1);
                    $keyMonTLTemp = 'ĐTNTH(L2)';
                }
                if ($sinhVien->thuchanhlan3 != -1) {
                    $sinhVien->toanKhoa->ghiChuThiLai .= ', L3:' . number_format($sinhVien->thuchanhlan3, 1);
                    $keyMonTLTemp = 'ĐTNTH(L3)';
                }
                $sinhVien->toanKhoa->ghiChuThiLai .= '), ';
                $sinhVien->toanKhoa->ghiChuThiLaiArray->push([
                    'type' => 'TL',
                    'key' => $keyMonTLTemp,
                    'mh_id' => 886
                ]);
            }

            if ($diemkhoaluan < 5 && $diemkhoaluan != -1) {
                $sinhVien->toanKhoa->ghiChuThiLai .= 'ĐTNCD(L1:' . number_format($sinhVien->khoaluanlan1, 1);
                $keyMonTLTemp = 'ĐTNCD(L1)';

                if ($sinhVien->khoaluanlan2 != -1) {
                    $sinhVien->toanKhoa->ghiChuThiLai .= ', L2:' . number_format($sinhVien->khoaluanlan2, 1);
                    $keyMonTLTemp = 'ĐTNCD(L2)';
                }
                if ($sinhVien->khoaluanlan3 != -1) {
                    $sinhVien->toanKhoa->ghiChuThiLai .= ', L3:' . number_format($sinhVien->khoaluanlan3, 1);
                    $keyMonTLTemp = 'ĐTNCD(L3)';
                }
                $sinhVien->toanKhoa->ghiChuThiLai .= '), ';
                $sinhVien->toanKhoa->ghiChuThiLaiArray->push([
                    'type' => 'TL',
                    'key' => $keyMonTLTemp,
                    'mh_id' => 894
                ]);
            }

            if (isset($sinhVien->toanKhoa->avg)) {
                // Xác định không còn sv nào nợ môn nên avg = tichLuy
                $sinhVien->toanKhoa->avg = $sinhVien->toanKhoa->tichLuy;
                if ($diemkhoaluan != -1) {
                    $sinhVien->toanKhoa->avg_totnghiep = number_format((($sinhVien->toanKhoa->avg * 3) + ($diemkhoaluan * 2)) / 5, 1);
                } else {
                    $sinhVien->toanKhoa->avg_totnghiep = number_format(($diemlythuyet + ($diemthuchanh * 2) + ($sinhVien->toanKhoa->avg * 3)) / 6, 1);
                }
                // $sinhVien->toanKhoa->hocLucTN = $this->getHocLucByDiem($sinhVien->toanKhoa->avg_totnghiep, $quyChe2022);

                // 1 < 2
                $sinhVien->giamXltn = false;
                if ($sinhVien->final_xltn > $sinhVien->temp_xltn) {
                    $sinhVien->giamXltn = true;
                }

                $sinhVien->toanKhoa->hocLucTN = $this->getHocLucByDB($sinhVien->final_xltn, $quyChe2022);
            }
            $sinhVien->notes = $sinhVien->notes->concat($sinhVien->toanKhoa->ghiChuThiLaiArray);
        }

        // dd($data["danhSachSinhVien"][4]->toanKhoa->avg_totnghiep);

        return $data;
    }



    public function getKetQuaHocTapTheoDotXetTN($semester, $lhId, $dt_id, $dxtn_id, $svId = 0)
    {
        $lopHoc = LopHoc::find($lhId);
        $quyChe2022 = $lopHoc->lh_nienche == 1;

        $data = $this->getKetQuaHocTap($semester, $lhId, $svId, true);
        //$modalDotThi = DotThi::find($dt_id);
        $data['danhSachSinhVien'] = $data['danhSachSinhVien']->reject(function ($item, $key) use ($dxtn_id) {
            return DB::select(
                'select distinct dxtn_sv.* from qlsv_dotxettotnghiep_sinhvien as dxtn_sv where dxtn_sv.dxtn_id = ? AND dxtn_sv.sv_id = ?',
                [$dxtn_id, $item['sv_id']]
            ) == null;
        });

        // SELECT MAX(dtd.svd_lan) FROM qlsv_dotthi_diem dtd
        // JOIN qlsv_dotthi_bangdiem dtbd ON dtd.dt_id = dtbd.dt_id
        // WHERE dtd.dt_id = 1 AND dtbd.lh_id = 20;
        // -- 2


        // SELECT DISTINCT dtd.dt_id FROM qlsv_dotthi_diem dtd
        // JOIN qlsv_dotthi_bangdiem dtbd ON dtd.dt_id = dtbd.dt_id
        // WHERE dtbd.lh_id = 20 AND dtd.dt_id <= 3
        // ORDER BY dtd.dt_id desc
        // LIMIT 3;
        // -- 7 10

        // SELECT * FROM qlsv_dotthi_diem dtd
        // JOIN qlsv_dotthi_bangdiem dtbd ON dtd.dt_id = dtbd.dt_id
        // WHERE dtd.dt_id = 4 AND dtbd.lh_id = 20;


        $numberExamPresent = DB::table('qlsv_dotthi_diem as dtd')
            ->join('qlsv_dotthi_bangdiem as bd', 'bd.dt_bd_id', '=', 'dtd.dt_bd_id')
            ->where('bd.dt_id', $dt_id)
            ->where('bd.lh_id', $lhId)
            ->max('dtd.svd_lan');

        $numberExamreCently = DB::table('qlsv_dotthi_diem as dtd')
            ->join('qlsv_dotthi_bangdiem as dtbd', 'dtd.dt_bd_id', '=', 'dtbd.dt_bd_id')
            ->where('dtbd.lh_id', $lhId)
            ->where('dtd.dt_id', '<=', $dt_id)
            ->select('dtd.dt_id')
            ->distinct()
            ->orderBy('dtd.dt_id', 'desc')
            ->limit(3)
            ->pluck('dt_id')
            ->toArray();


        $dxtn_id = DotThiDotXetTotNghiep::where('dt_id', $dt_id)->first()->dxtn_id;
        //tính điểm môn học
        foreach ($data['danhSachSinhVien'] as $index => $sinhVien) {
            //-1 là chưa đi thi null là vắng thi
            // $TBToanKhoa = isset($sinhVien->toanKhoa->avg) ? $sinhVien->toanKhoa->avg : -1;
            $diemdotthi = DB::table('qlsv_dotthi_diem as d')
                ->join('qlsv_dotthi_bangdiem as bd', 'bd.dt_bd_id', '=', 'd.dt_bd_id')
                ->join('qlsv_monhoc as mh', 'mh.mh_id', '=', 'bd.mh_id')
                ->where('d.sv_id', $sinhVien->sv_id)
                ->whereIn('d.dt_id', $numberExamreCently)
                ->select('d.svd_first', 'd.svd_lan', 'bd.mh_id', 'd.svd_ghichu', 'mh.mh_loai', 'd.svd_loai', 'd.svd_dieukien', 'd.svd_khongdatloai', 'd.svd_lan', 'bd.dt_id')
                ->get();



            // Số lần
            $sinhVien->svd_lanthihientai = $numberExamPresent;

            $dxtn_sv = DB::table('qlsv_dotxettotnghiep_sinhvien as dxtn_sv')
                ->join('qlsv_sinhvien as sv', function ($join) {
                    $join->on('sv.sv_id', '=', 'dxtn_sv.sv_id');
                })
                ->join('qlsv_lophoc as lh', function ($join) {
                    $join->on('lh.lh_id', '=', 'dxtn_sv.lh_id');
                })
                ->join('qlsv_khoadaotao as kdt', function ($join) {
                    $join->on('kdt.kdt_id', '=', 'lh.kdt_id');
                })
                ->where('dxtn_sv.lh_id', '=', $lhId)
                ->where('dxtn_sv.dxtn_id', '=', $dxtn_id)
                ->where('dxtn_sv.sv_id', $sinhVien->sv_id)
                // ->whereRaw('dxtn_sv.svxtn_dattn = 1')
                ->select(DB::raw('DISTINCT dxtn_sv.svxtn_dattn, dxtn_sv.temp_xltn, dxtn_sv.final_xltn, dxtn_sv.ghichu_tlhl, dxtn_sv.svxtn_vipham, dxtn_sv.svxtn_ghichu'))
                ->first();


            if ($dxtn_sv) {
                $sinhVien->toanKhoa->temp_xltn = $dxtn_sv->temp_xltn;
                $sinhVien->toanKhoa->final_xltn = $dxtn_sv->final_xltn;
                $sinhVien->toanKhoa->ghichu_tlhl = $dxtn_sv->ghichu_tlhl;
                $sinhVien->svxtn_dattn = $dxtn_sv->svxtn_dattn;
                $sinhVien->svxtn_vipham = $dxtn_sv->svxtn_vipham;
                $sinhVien->svxtn_ghichu = $dxtn_sv->svxtn_ghichu;
            }

            //2 chinhs tri, 3 thuc hanh, 4 ly thuyet, 5 bao ve
            $sinhVien->thuchanhlan1 = -1;
            $sinhVien->lythuyetlan1 = -1;
            $sinhVien->chinhtrilan1 = -1;
            $sinhVien->khoaluanlan1 = -1;

            $sinhVien->thuchanhlan2 = -1;
            $sinhVien->lythuyetlan2 = -1;
            $sinhVien->chinhtrilan2 = -1;
            $sinhVien->khoaluanlan2 = -1;

            $sinhVien->thuchanhlan3 = -1;
            $sinhVien->lythuyetlan3 = -1;
            $sinhVien->chinhtrilan3 = -1;
            $sinhVien->khoaluanlan3 = -1;

            foreach ($diemdotthi as $ddt) {
                $sinhVien->svd_ghichu = $ddt->svd_ghichu;
                $sinhVien->svd_loai = $ddt->svd_loai;
                $sinhVien->svd_khongdatloai = $ddt->svd_khongdatloai;

                if ($ddt->dt_id == $dt_id) {
                    $sinhVien->svd_dieukien = $ddt->svd_dieukien;
                }
                if ($ddt->svd_lan == 1) {
                    if ($ddt->mh_loai == 2) {
                        $sinhVien->chinhtrilan1 = $ddt->svd_first;
                    } else if ($ddt->mh_loai == 3) {
                        $sinhVien->thuchanhlan1 = $ddt->svd_first;
                    } else if ($ddt->mh_loai == 4) {
                        $sinhVien->lythuyetlan1 = $ddt->svd_first;
                    } else if ($ddt->mh_loai == 5) {
                        $sinhVien->khoaluanlan1 = $ddt->svd_first;
                    }
                } else if ($ddt->svd_lan == 2) {
                    if ($ddt->mh_loai == 2) {
                        $sinhVien->chinhtrilan2 = $ddt->svd_first;
                    } else if ($ddt->mh_loai == 3) {
                        $sinhVien->thuchanhlan2 = $ddt->svd_first;
                    } else if ($ddt->mh_loai == 4) {
                        $sinhVien->lythuyetlan2 = $ddt->svd_first;
                    } else if ($ddt->mh_loai == 5) {
                        $sinhVien->khoaluanlan2 = $ddt->svd_first;
                    }
                } else if ($ddt->svd_lan == 3) {
                    if ($ddt->mh_loai == 2) {
                        $sinhVien->chinhtrilan3 = $ddt->svd_first;
                    } else if ($ddt->mh_loai == 3) {
                        $sinhVien->thuchanhlan3 = $ddt->svd_first;
                    } else if ($ddt->mh_loai == 4) {
                        $sinhVien->lythuyetlan3 = $ddt->svd_first;
                    } else if ($ddt->mh_loai == 5) {
                        $sinhVien->khoaluanlan3 = $ddt->svd_first;
                    }
                }
            }

            $diemchinhtri = $sinhVien->chinhtrilan1;
            $diemlythuyet = $sinhVien->lythuyetlan1;
            $diemthuchanh = $sinhVien->thuchanhlan1;
            $diemkhoaluan = $sinhVien->khoaluanlan1;


            if ($sinhVien->khoaluanlan2 != -1) {
                $diemkhoaluan = $sinhVien->khoaluanlan2;
            } else if ($sinhVien->khoaluanlan3 != -1) {
                $diemkhoaluan = $sinhVien->khoaluanlan3;
            }

            if ($sinhVien->thuchanhlan2 != -1) {
                $diemthuchanh = $sinhVien->thuchanhlan2;
            } else if ($sinhVien->thuchanhlan3 != -1) {
                $diemthuchanh = $sinhVien->thuchanhlan3;
            }

            if ($sinhVien->lythuyetlan2 != -1) {
                $diemlythuyet = $sinhVien->lythuyetlan2;
            } else if ($sinhVien->lythuyetlan3 != -1) {
                $diemlythuyet = $sinhVien->lythuyetlan3;
            }

            if ($sinhVien->lythuyetlan2 != -1) {
                $diemlythuyet = $sinhVien->lythuyetlan2;
            } else if ($sinhVien->lythuyetlan3 != -1) {
                $diemlythuyet = $sinhVien->lythuyetlan3;
            }

            $sinhVien->toanKhoa->ghiChuThiLai = "";
            $sinhVien->toanKhoa->ghiChuThiLaiArray = collect([]);

            if ($diemchinhtri < 5 && $diemchinhtri != -1 && $diemkhoaluan == -1) {
                $sinhVien->toanKhoa->ghiChuThiLai .= 'ĐTNCT(L1:' . number_format($sinhVien->chinhtrilan1, 1);
                $keyMonTLTemp = 'ĐTNCT(L1)';
                if ($sinhVien->chinhtrilan2 != -1) {
                    $sinhVien->toanKhoa->ghiChuThiLai .= ', L2:' . number_format($sinhVien->chinhtrilan2, 1);
                    $keyMonTLTemp = 'ĐTNCT(L2)';
                }
                if ($sinhVien->chinhtrilan3 != -1) {
                    $sinhVien->toanKhoa->ghiChuThiLai .= ', L3:' . number_format($sinhVien->chinhtrilan3, 1);
                    $keyMonTLTemp = 'ĐTNCT(L3)';
                }
                $sinhVien->toanKhoa->ghiChuThiLai .= '), ';
                $sinhVien->toanKhoa->ghiChuThiLaiArray->push([
                    'type' => 'TL',
                    'key' => $keyMonTLTemp,
                    'mh_id' => 885
                ]);
            }

            if ($diemlythuyet < 5 && $diemkhoaluan == -1) {
                $sinhVien->toanKhoa->ghiChuThiLai .= 'ĐTNLT(L1:' . number_format($sinhVien->lythuyetlan1, 1);
                $keyMonTLTemp = 'ĐTNLT(L1)';

                if ($sinhVien->lythuyetlan2 != -1) {
                    $sinhVien->toanKhoa->ghiChuThiLai .= ', L2:' . number_format($sinhVien->lythuyetlan2, 1);
                    $keyMonTLTemp = 'ĐTNLT(L2)';
                }
                if ($sinhVien->lythuyetlan3 != -1) {
                    $sinhVien->toanKhoa->ghiChuThiLai .= ', L3:' . number_format($sinhVien->lythuyetlan3, 1);
                    $keyMonTLTemp = 'ĐTNLT(L3)';
                }
                $sinhVien->toanKhoa->ghiChuThiLai .= '), ';
                $sinhVien->toanKhoa->ghiChuThiLaiArray->push([
                    'type' => 'TL',
                    'key' => $keyMonTLTemp,
                    'mh_id' => 887
                ]);
            }

            if ($diemthuchanh < 5 && $diemkhoaluan == -1) {
                $sinhVien->toanKhoa->ghiChuThiLai .= 'ĐTNTH(L1:' . number_format($sinhVien->thuchanhlan1, 1);
                $keyMonTLTemp = 'ĐTNTH(L1)';

                if ($sinhVien->thuchanhlan2 != -1) {
                    $sinhVien->toanKhoa->ghiChuThiLai .= ', L2:' . number_format($sinhVien->thuchanhlan2, 1);
                    $keyMonTLTemp = 'ĐTNTH(L2)';
                }
                if ($sinhVien->thuchanhlan3 != -1) {
                    $sinhVien->toanKhoa->ghiChuThiLai .= ', L3:' . number_format($sinhVien->thuchanhlan3, 1);
                    $keyMonTLTemp = 'ĐTNTH(L3)';
                }
                $sinhVien->toanKhoa->ghiChuThiLai .= '), ';
                $sinhVien->toanKhoa->ghiChuThiLaiArray->push([
                    'type' => 'TL',
                    'key' => $keyMonTLTemp,
                    'mh_id' => 886
                ]);
            }

            if ($diemkhoaluan < 5 && $diemkhoaluan != -1) {
                $sinhVien->toanKhoa->ghiChuThiLai .= 'ĐTNCD(L1:' . number_format($sinhVien->khoaluanlan1, 1);
                $keyMonTLTemp = 'ĐTNCD(L1)';

                if ($sinhVien->khoaluanlan2 != -1) {
                    $sinhVien->toanKhoa->ghiChuThiLai .= ', L2:' . number_format($sinhVien->khoaluanlan2, 1);
                    $keyMonTLTemp = 'ĐTNCD(L2)';
                }
                if ($sinhVien->khoaluanlan3 != -1) {
                    $sinhVien->toanKhoa->ghiChuThiLai .= ', L3:' . number_format($sinhVien->khoaluanlan3, 1);
                    $keyMonTLTemp = 'ĐTNCD(L3)';
                }
                $sinhVien->toanKhoa->ghiChuThiLai .= '), ';
                $sinhVien->toanKhoa->ghiChuThiLaiArray->push([
                    'type' => 'TL',
                    'key' => $keyMonTLTemp,
                    'mh_id' => 894
                ]);
            }

            if (isset($sinhVien->toanKhoa->avg)) {
                $sinhVien->toanKhoa->avg = $sinhVien->toanKhoa->tichLuy;
                if ($diemkhoaluan != -1) {
                    $sinhVien->toanKhoa->avg_totnghiep = number_format((($sinhVien->toanKhoa->avg * 3) + ($diemkhoaluan * 2)) / 5, 1);
                } else {
                    $sinhVien->toanKhoa->avg_totnghiep = number_format(($diemlythuyet + ($diemthuchanh * 2) + ($sinhVien->toanKhoa->avg * 3)) / 6, 1);
                }

                //$sinhVien->toanKhoa->hocLucTN = $this->getHocLucByDiem($sinhVien->toanKhoa->avg_totnghiep, $quyChe2022);
                // 1 < 2
                $sinhVien->giamXltn = false;
                if ($sinhVien->toanKhoa->final_xltn > $sinhVien->toanKhoa->temp_xltn) {
                    $sinhVien->giamXltn = true;
                }

                $sinhVien->toanKhoa->temp_xltn = $this->getHocLucByDB($sinhVien->toanKhoa->temp_xltn, $quyChe2022);
                $sinhVien->toanKhoa->final_xltn = $this->getHocLucByDB($sinhVien->toanKhoa->final_xltn, $quyChe2022);
            }

            // $jsonStringGhichu_tlhl = $sinhVien->toanKhoa->ghichu_tlhl; // Chuỗi JSON
            // // Phân tích chuỗi JSON thành mảng
            // $arrayDataGhichu_tlhl = json_decode($jsonStringGhichu_tlhl, true);
            // // Tạo một Collection từ mảng
            // $collectionGhichu_tlhl = collect($arrayDataGhichu_tlhl);
            // // In ra CollectionGhichu_tlhl để kiểm tra kết quả
            // $sinhVien->notes = $collectionGhichu_tlhl;

            $sinhVien->notes = $sinhVien->notes->concat($sinhVien->toanKhoa->ghiChuThiLaiArray);
        }

        return $data;
    }

    public function getKetQuaHocTapSVToanKhoa($semester, $lhId, $svId = 0)
    {
        $lopHoc = LopHoc::find($lhId);
        $quyChe2022 = $lopHoc->lh_nienche == 1;

        $data = $this->getKetQuaHocTap($semester, $lhId, $svId, true);


        // $numberExamPresent = DB::table('qlsv_dotthi_diem as dtd')
        //     ->join('qlsv_dotthi_bangdiem as bd', 'bd.dt_bd_id', '=', 'dtd.dt_bd_id')
        //     ->where('bd.dt_id', $dt_id)
        //     ->where('bd.lh_id', $lhId)
        //     ->max('dtd.svd_lan');


        //tính điểm môn học
        foreach ($data['danhSachSinhVien'] as $index => $sinhVien) {
            // Tìm đợt thi gần nhất
            $diemdotthi = array();
            $ObjDt_id = DB::table('qlsv_dotxettotnghiep_sinhvien as dxtn_sv')
                ->join('qlsv_dotthi_dotxettotnghiep as dxtn', 'dxtn.dxtn_id', '=', 'dxtn_sv.dxtn_id')
                ->where('dxtn_sv.lh_id', $lhId)
                ->where('dxtn_sv.sv_id', $svId)
                ->select('dxtn.dt_id')
                ->orderBy('dxtn_sv.dxtn_sv_id', 'desc')
                ->first();
            if ($ObjDt_id != null) {
                $dt_id = $ObjDt_id->dt_id;
                $numberExamreCently = DB::table('qlsv_dotthi_diem as dtd')
                    ->join('qlsv_dotthi_bangdiem as dtbd', 'dtd.dt_bd_id', '=', 'dtbd.dt_bd_id')
                    ->where('dtbd.lh_id', $lhId)
                    ->where('dtd.dt_id', '<=', $dt_id)
                    ->select('dtd.dt_id')
                    ->distinct()
                    ->orderBy('dtd.dt_id', 'desc')
                    ->limit(3)
                    ->pluck('dt_id')
                    ->toArray();

                //-1 là chưa đi thi null là vắng thi
                // $TBToanKhoa = isset($sinhVien->toanKhoa->avg) ? $sinhVien->toanKhoa->avg : -1;
                $diemdotthi = DB::table('qlsv_dotthi_diem as d')
                    ->join('qlsv_dotthi_bangdiem as bd', 'bd.dt_bd_id', '=', 'd.dt_bd_id')
                    ->join('qlsv_monhoc as mh', 'mh.mh_id', '=', 'bd.mh_id')
                    ->where('d.sv_id', $sinhVien->sv_id)
                    ->whereIn('d.dt_id', $numberExamreCently)
                    ->select('d.svd_first', 'd.svd_lan', 'bd.mh_id', 'd.svd_ghichu', 'mh.mh_loai', 'd.svd_loai', 'd.svd_dieukien', 'd.svd_khongdatloai', 'd.svd_lan', 'bd.dt_id')
                    ->get();
            }

            // Số lần
            // $sinhVien->svd_lanthihientai = $numberExamPresent;


            $dxtn_sv = DB::table('qlsv_dotxettotnghiep_sinhvien as dxtn_sv')
                ->where('dxtn_sv.sv_id', '=', $sinhVien->sv_id)
                ->where('dxtn_sv.svxtn_dattn', '=', 1)
                ->select(DB::raw('DISTINCT dxtn_sv.svxtn_dattn, dxtn_sv.temp_xltn, dxtn_sv.final_xltn, dxtn_sv.ghichu_tlhl, dxtn_sv.svxtn_vipham, dxtn_sv.svxtn_ghichu, dxtn_sv.dxtn_sv_id'))
                ->orderBy('dxtn_sv.dxtn_sv_id', 'desc')
                ->first();

            if ($dxtn_sv) {
                $sinhVien->toanKhoa->temp_xltn = $dxtn_sv->temp_xltn;
                $sinhVien->toanKhoa->final_xltn = $dxtn_sv->final_xltn;
                $sinhVien->svxtn_dattn = $dxtn_sv->svxtn_dattn;
                $sinhVien->svxtn_vipham = $dxtn_sv->svxtn_vipham;
                $sinhVien->svxtn_ghichu = $dxtn_sv->svxtn_ghichu;
            }
            

            //2 chinhs tri, 3 thuc hanh, 4 ly thuyet, 5 bao ve
            $sinhVien->thuchanhlan1 = -1;
            $sinhVien->lythuyetlan1 = -1;
            $sinhVien->chinhtrilan1 = -1;
            $sinhVien->khoaluanlan1 = -1;

            $sinhVien->thuchanhlan2 = -1;
            $sinhVien->lythuyetlan2 = -1;
            $sinhVien->chinhtrilan2 = -1;
            $sinhVien->khoaluanlan2 = -1;

            $sinhVien->thuchanhlan3 = -1;
            $sinhVien->lythuyetlan3 = -1;
            $sinhVien->chinhtrilan3 = -1;
            $sinhVien->khoaluanlan3 = -1;

            foreach ($diemdotthi as $ddt) {
                $sinhVien->svd_ghichu = $ddt->svd_ghichu;
                $sinhVien->svd_loai = $ddt->svd_loai;
                $sinhVien->svd_khongdatloai = $ddt->svd_khongdatloai;

                if ($ddt->dt_id == $dt_id) {
                    $sinhVien->svd_dieukien = $ddt->svd_dieukien;
                }
                if ($ddt->svd_lan == 1) {
                    if ($ddt->mh_loai == 2) {
                        $sinhVien->chinhtrilan1 = $ddt->svd_first;
                    } else if ($ddt->mh_loai == 3) {
                        $sinhVien->thuchanhlan1 = $ddt->svd_first;
                    } else if ($ddt->mh_loai == 4) {
                        $sinhVien->lythuyetlan1 = $ddt->svd_first;
                    } else if ($ddt->mh_loai == 5) {
                        $sinhVien->khoaluanlan1 = $ddt->svd_first;
                    }
                } else if ($ddt->svd_lan == 2) {
                    if ($ddt->mh_loai == 2) {
                        $sinhVien->chinhtrilan2 = $ddt->svd_first;
                    } else if ($ddt->mh_loai == 3) {
                        $sinhVien->thuchanhlan2 = $ddt->svd_first;
                    } else if ($ddt->mh_loai == 4) {
                        $sinhVien->lythuyetlan2 = $ddt->svd_first;
                    } else if ($ddt->mh_loai == 5) {
                        $sinhVien->khoaluanlan2 = $ddt->svd_first;
                    }
                } else if ($ddt->svd_lan == 3) {
                    if ($ddt->mh_loai == 2) {
                        $sinhVien->chinhtrilan3 = $ddt->svd_first;
                    } else if ($ddt->mh_loai == 3) {
                        $sinhVien->thuchanhlan3 = $ddt->svd_first;
                    } else if ($ddt->mh_loai == 4) {
                        $sinhVien->lythuyetlan3 = $ddt->svd_first;
                    } else if ($ddt->mh_loai == 5) {
                        $sinhVien->khoaluanlan3 = $ddt->svd_first;
                    }
                }
            }

            $diemchinhtri = $sinhVien->chinhtrilan1;
            $diemlythuyet = $sinhVien->lythuyetlan1;
            $diemthuchanh = $sinhVien->thuchanhlan1;
            $diemkhoaluan = $sinhVien->khoaluanlan1;


            if ($sinhVien->khoaluanlan2 != -1) {
                $diemkhoaluan = $sinhVien->khoaluanlan2;
            } else if ($sinhVien->khoaluanlan3 != -1) {
                $diemkhoaluan = $sinhVien->khoaluanlan3;
            }

            if ($sinhVien->thuchanhlan2 != -1) {
                $diemthuchanh = $sinhVien->thuchanhlan2;
            } else if ($sinhVien->thuchanhlan3 != -1) {
                $diemthuchanh = $sinhVien->thuchanhlan3;
            }

            if ($sinhVien->lythuyetlan2 != -1) {
                $diemlythuyet = $sinhVien->lythuyetlan2;
            } else if ($sinhVien->lythuyetlan3 != -1) {
                $diemlythuyet = $sinhVien->lythuyetlan3;
            }

            if ($sinhVien->lythuyetlan2 != -1) {
                $diemlythuyet = $sinhVien->lythuyetlan2;
            } else if ($sinhVien->lythuyetlan3 != -1) {
                $diemlythuyet = $sinhVien->lythuyetlan3;
            }

            $sinhVien->toanKhoa->ghiChuThiLai = "";
            $sinhVien->toanKhoa->ghiChuThiLaiArray = collect([]);

            if ($diemchinhtri < 5 && $diemchinhtri != -1 && $diemkhoaluan == -1) {
                $sinhVien->toanKhoa->ghiChuThiLai .= 'ĐTNCT(L1:' . number_format($sinhVien->chinhtrilan1, 1);
                $keyMonTLTemp = 'ĐTNCT(L1)';
                if ($sinhVien->chinhtrilan2 != -1) {
                    $sinhVien->toanKhoa->ghiChuThiLai .= ', L2:' . number_format($sinhVien->chinhtrilan2, 1);
                    $keyMonTLTemp = 'ĐTNCT(L2)';
                }
                if ($sinhVien->chinhtrilan3 != -1) {
                    $sinhVien->toanKhoa->ghiChuThiLai .= ', L3:' . number_format($sinhVien->chinhtrilan3, 1);
                    $keyMonTLTemp = 'ĐTNCT(L3)';
                }
                $sinhVien->toanKhoa->ghiChuThiLai .= '), ';
                $sinhVien->toanKhoa->ghiChuThiLaiArray->push([
                    'type' => 'TL',
                    'key' => $keyMonTLTemp,
                    'mh_id' => 885
                ]);
            }

            if ($diemlythuyet < 5 && $diemkhoaluan == -1) {
                $sinhVien->toanKhoa->ghiChuThiLai .= 'ĐTNLT(L1:' . number_format($sinhVien->lythuyetlan1, 1);
                $keyMonTLTemp = 'ĐTNLT(L1)';

                if ($sinhVien->lythuyetlan2 != -1) {
                    $sinhVien->toanKhoa->ghiChuThiLai .= ', L2:' . number_format($sinhVien->lythuyetlan2, 1);
                    $keyMonTLTemp = 'ĐTNLT(L2)';
                }
                if ($sinhVien->lythuyetlan3 != -1) {
                    $sinhVien->toanKhoa->ghiChuThiLai .= ', L3:' . number_format($sinhVien->lythuyetlan3, 1);
                    $keyMonTLTemp = 'ĐTNLT(L3)';
                }
                $sinhVien->toanKhoa->ghiChuThiLai .= '), ';
                $sinhVien->toanKhoa->ghiChuThiLaiArray->push([
                    'type' => 'TL',
                    'key' => $keyMonTLTemp,
                    'mh_id' => 887
                ]);
            }

            if ($diemthuchanh < 5 && $diemkhoaluan == -1) {
                $sinhVien->toanKhoa->ghiChuThiLai .= 'ĐTNTH(L1:' . number_format($sinhVien->thuchanhlan1, 1);
                $keyMonTLTemp = 'ĐTNTH(L1)';

                if ($sinhVien->thuchanhlan2 != -1) {
                    $sinhVien->toanKhoa->ghiChuThiLai .= ', L2:' . number_format($sinhVien->thuchanhlan2, 1);
                    $keyMonTLTemp = 'ĐTNTH(L2)';
                }
                if ($sinhVien->thuchanhlan3 != -1) {
                    $sinhVien->toanKhoa->ghiChuThiLai .= ', L3:' . number_format($sinhVien->thuchanhlan3, 1);
                    $keyMonTLTemp = 'ĐTNTH(L3)';
                }
                $sinhVien->toanKhoa->ghiChuThiLai .= '), ';
                $sinhVien->toanKhoa->ghiChuThiLaiArray->push([
                    'type' => 'TL',
                    'key' => $keyMonTLTemp,
                    'mh_id' => 886
                ]);
            }

            if ($diemkhoaluan < 5 && $diemkhoaluan != -1) {
                $sinhVien->toanKhoa->ghiChuThiLai .= 'ĐTNCD(L1:' . number_format($sinhVien->khoaluanlan1, 1);
                $keyMonTLTemp = 'ĐTNCD(L1)';

                if ($sinhVien->khoaluanlan2 != -1) {
                    $sinhVien->toanKhoa->ghiChuThiLai .= ', L2:' . number_format($sinhVien->khoaluanlan2, 1);
                    $keyMonTLTemp = 'ĐTNCD(L2)';
                }
                if ($sinhVien->khoaluanlan3 != -1) {
                    $sinhVien->toanKhoa->ghiChuThiLai .= ', L3:' . number_format($sinhVien->khoaluanlan3, 1);
                    $keyMonTLTemp = 'ĐTNCD(L3)';
                }
                $sinhVien->toanKhoa->ghiChuThiLai .= '), ';
                $sinhVien->toanKhoa->ghiChuThiLaiArray->push([
                    'type' => 'TL',
                    'key' => $keyMonTLTemp,
                    'mh_id' => 894
                ]);
            }

            if (isset($sinhVien->toanKhoa->avg)) {
                // $sinhVien->toanKhoa->avg = $sinhVien->toanKhoa->tichLuy;
                // if ($diemkhoaluan != -1) {
                //     $sinhVien->toanKhoa->avg_totnghiep = number_format((($sinhVien->toanKhoa->avg * 3) + ($diemkhoaluan * 2)) / 5, 1);
                // } else {
                //     $sinhVien->toanKhoa->avg_totnghiep = number_format(($diemlythuyet + ($diemthuchanh * 2) + ($sinhVien->toanKhoa->avg * 3)) / 6, 1);
                // }

                //$sinhVien->toanKhoa->hocLucTN = $this->getHocLucByDiem($sinhVien->toanKhoa->avg_totnghiep, $quyChe2022);
                
                // Trường họp lớp đã tạo bảng điểm thi TN
                if ($dxtn_sv) {
                    $sinhVien->giamXltn = false;
                    // 1 < 2
                    if ($sinhVien->toanKhoa->final_xltn > $sinhVien->toanKhoa->temp_xltn) {
                        $sinhVien->giamXltn = true;
                    }

                    $sinhVien->toanKhoa->hocLucTN = $this->getHocLucByDB($sinhVien->toanKhoa->final_xltn, $quyChe2022);
                }

                //$sinhVien->toanKhoa->temp_xltn = $this->getHocLucByDB($sinhVien->toanKhoa->temp_xltn, $quyChe2022);
            }

            // $jsonStringGhichu_tlhl = $sinhVien->toanKhoa->ghichu_tlhl; // Chuỗi JSON
            // // Phân tích chuỗi JSON thành mảng
            // $arrayDataGhichu_tlhl = json_decode($jsonStringGhichu_tlhl, true);
            // // Tạo một Collection từ mảng
            // $collectionGhichu_tlhl = collect($arrayDataGhichu_tlhl);
            // // In ra CollectionGhichu_tlhl để kiểm tra kết quả
            // $sinhVien->notes = $collectionGhichu_tlhl;

            $sinhVien->notes = $sinhVien->notes->concat($sinhVien->toanKhoa->ghiChuThiLaiArray);
        }
        return $data;
    }


    public function getKetQuaHocTapTheoDotThiBySinhVien($semester, $lhId, $dt_id, $svId = 0)
    {
        $lopHoc = LopHoc::find($lhId);
        $quyChe2022 = $lopHoc->lh_nienche == 1;
        $data = $this->getKetQuaHocTap($semester, $lhId, $svId, true);

        $numberExamPresent = DB::table('qlsv_dotthi_diem as dtd')
            ->join('qlsv_dotthi_bangdiem as bd', 'bd.dt_bd_id', '=', 'dtd.dt_bd_id')
            ->where('bd.dt_id', $dt_id)
            ->where('bd.lh_id', $lhId)
            ->max('dtd.svd_lan');

        //tính điểm môn học
        foreach ($data['danhSachSinhVien'] as $sinhVien) {
            //-1 là chưa đi thi null là vắng thi
            // $TBToanKhoa = isset($sinhVien->toanKhoa->avg) ? $sinhVien->toanKhoa->avg : -1;
            $diemdotthi = DB::table('qlsv_dotthi_diem as d')
                ->join('qlsv_dotthi_bangdiem as bd', 'bd.dt_bd_id', '=', 'd.dt_bd_id')
                ->join('qlsv_monhoc as mh', 'mh.mh_id', '=', 'bd.mh_id')
                ->where('d.sv_id', $sinhVien->sv_id)
                ->where('d.dt_id', $dt_id)
                ->select('d.svd_first', 'd.svd_lan', 'bd.mh_id', 'd.svd_ghichu', 'mh.mh_loai', 'd.svd_loai', 'd.svd_dieukien', 'd.svd_khongdatloai', 'bd.dt_id')
                ->get();

            // Số lần
            $sinhVien->svd_lanthihientai = $numberExamPresent;

            //2 chinhs tri, 3 thuc hanh, 4 ly thuyet, 5 bao ve
            $sinhVien->thuchanhlan1 = -1;
            $sinhVien->lythuyetlan1 = -1;
            $sinhVien->chinhtrilan1 = -1;
            $sinhVien->khoaluanlan1 = -1;

            $sinhVien->thuchanhlan2 = -1;
            $sinhVien->lythuyetlan2 = -1;
            $sinhVien->chinhtrilan2 = -1;
            $sinhVien->khoaluanlan2 = -1;

            $sinhVien->thuchanhlan3 = -1;
            $sinhVien->lythuyetlan3 = -1;
            $sinhVien->chinhtrilan3 = -1;
            $sinhVien->khoaluanlan3 = -1;

            foreach ($diemdotthi as $ddt) {
                $sinhVien->svd_ghichu = $ddt->svd_ghichu;
                $sinhVien->svd_loai = $ddt->svd_loai;
                $sinhVien->svd_khongdatloai = $ddt->svd_khongdatloai;

                if ($ddt->dt_id == $dt_id) {
                    $sinhVien->svd_dieukien = $ddt->svd_dieukien;
                }
                if ($ddt->svd_lan == 1) {
                    if ($ddt->mh_loai == 2) {
                        $sinhVien->chinhtrilan1 = $ddt->svd_first;
                    } else if ($ddt->mh_loai == 3) {
                        $sinhVien->thuchanhlan1 = $ddt->svd_first;
                    } else if ($ddt->mh_loai == 4) {
                        $sinhVien->lythuyetlan1 = $ddt->svd_first;
                    } else if ($ddt->mh_loai == 5) {
                        $sinhVien->khoaluanlan1 = $ddt->svd_first;
                    }
                } else if ($ddt->svd_lan == 2) {
                    if ($ddt->mh_loai == 2) {
                        $sinhVien->chinhtrilan2 = $ddt->svd_first;
                    } else if ($ddt->mh_loai == 3) {
                        $sinhVien->thuchanhlan2 = $ddt->svd_first;
                    } else if ($ddt->mh_loai == 4) {
                        $sinhVien->lythuyetlan2 = $ddt->svd_first;
                    } else if ($ddt->mh_loai == 5) {
                        $sinhVien->khoaluanlan2 = $ddt->svd_first;
                    }
                } else if ($ddt->svd_lan == 3) {
                    if ($ddt->mh_loai == 2) {
                        $sinhVien->chinhtrilan3 = $ddt->svd_first;
                    } else if ($ddt->mh_loai == 3) {
                        $sinhVien->thuchanhlan3 = $ddt->svd_first;
                    } else if ($ddt->mh_loai == 4) {
                        $sinhVien->lythuyetlan3 = $ddt->svd_first;
                    } else if ($ddt->mh_loai == 5) {
                        $sinhVien->khoaluanlan3 = $ddt->svd_first;
                    }
                }
            }

            $diemchinhtri = $sinhVien->chinhtrilan1;
            $diemlythuyet = $sinhVien->lythuyetlan1;
            $diemthuchanh = $sinhVien->thuchanhlan1;
            $diemkhoaluan = $sinhVien->khoaluanlan1;


            if ($sinhVien->khoaluanlan2 != -1) {
                $diemkhoaluan = $sinhVien->khoaluanlan2;
            } else if ($sinhVien->khoaluanlan3 != -1) {
                $diemkhoaluan = $sinhVien->khoaluanlan3;
            }

            if ($sinhVien->thuchanhlan2 != -1) {
                $diemthuchanh = $sinhVien->thuchanhlan2;
            } else if ($sinhVien->thuchanhlan3 != -1) {
                $diemthuchanh = $sinhVien->thuchanhlan3;
            }

            if ($sinhVien->lythuyetlan2 != -1) {
                $diemlythuyet = $sinhVien->lythuyetlan2;
            } else if ($sinhVien->lythuyetlan3 != -1) {
                $diemlythuyet = $sinhVien->lythuyetlan3;
            }

            if ($sinhVien->lythuyetlan2 != -1) {
                $diemlythuyet = $sinhVien->lythuyetlan2;
            } else if ($sinhVien->lythuyetlan3 != -1) {
                $diemlythuyet = $sinhVien->lythuyetlan3;
            }

            $sinhVien->toanKhoa->ghiChuThiLai = "";
            $sinhVien->toanKhoa->ghiChuThiLaiArray = collect([]);


            if ($diemchinhtri < 5 && $diemchinhtri != -1 && $diemkhoaluan == -1) {
                $sinhVien->toanKhoa->ghiChuThiLai .= 'ĐTNCT(L1:' . number_format($sinhVien->chinhtrilan1, 1);
                if ($sinhVien->chinhtrilan2 != -1) {
                    $sinhVien->toanKhoa->ghiChuThiLai .= ', L2:' . number_format($sinhVien->chinhtrilan2, 1);
                }
                if ($sinhVien->chinhtrilan3 != -1) {
                    $sinhVien->toanKhoa->ghiChuThiLai .= ', L3:' . number_format($sinhVien->chinhtrilan3, 1);
                }
                $sinhVien->toanKhoa->ghiChuThiLai .= '), ';

                $sinhVien->toanKhoa->ghiChuThiLaiArray->push([
                    'type' => 'TL',
                    'key' => '(ĐTNCT)',
                    'mh_id' => 885
                ]);
            }

            if ($diemlythuyet < 5 && $diemkhoaluan == -1) {
                $sinhVien->toanKhoa->ghiChuThiLai .= 'ĐTNLT(L1:' . number_format($sinhVien->lythuyetlan1, 1);
                if ($sinhVien->lythuyetlan2 != -1) {
                    $sinhVien->toanKhoa->ghiChuThiLai .= ', L2:' . number_format($sinhVien->lythuyetlan2, 1);
                }
                if ($sinhVien->lythuyetlan3 != -1) {
                    $sinhVien->toanKhoa->ghiChuThiLai .= ', L3:' . number_format($sinhVien->lythuyetlan3, 1);
                }
                $sinhVien->toanKhoa->ghiChuThiLai .= '), ';

                $sinhVien->toanKhoa->ghiChuThiLaiArray->push([
                    'type' => 'TL',
                    'key' => '(ĐTNLT)',
                    'mh_id' => 887
                ]);
            }

            if ($diemthuchanh < 5 && $diemkhoaluan == -1) {
                $sinhVien->toanKhoa->ghiChuThiLai .= 'ĐTNTH(L1:' . number_format($sinhVien->thuchanhlan1, 1);
                if ($sinhVien->thuchanhlan2 != -1) {
                    $sinhVien->toanKhoa->ghiChuThiLai .= ', L2:' . number_format($sinhVien->thuchanhlan2, 1);
                }
                if ($sinhVien->thuchanhlan3 != -1) {
                    $sinhVien->toanKhoa->ghiChuThiLai .= ', L3:' . number_format($sinhVien->thuchanhlan3, 1);
                }
                $sinhVien->toanKhoa->ghiChuThiLai .= '), ';

                $sinhVien->toanKhoa->ghiChuThiLaiArray->push([
                    'type' => 'TL',
                    'key' => '(ĐTNTH)',
                    'mh_id' => 886
                ]);
            }

            if ($diemkhoaluan < 5 && $diemkhoaluan != -1) {
                $sinhVien->toanKhoa->ghiChuThiLai .= 'ĐTNCD(L1:' . number_format($sinhVien->khoaluanlan1, 1);
                if ($sinhVien->khoaluanlan2 != -1) {
                    $sinhVien->toanKhoa->ghiChuThiLai .= ', L2:' . number_format($sinhVien->khoaluanlan2, 1);
                }
                if ($sinhVien->khoaluanlan3 != -1) {
                    $sinhVien->toanKhoa->ghiChuThiLai .= ', L3:' . number_format($sinhVien->khoaluanlan3, 1);
                }
                $sinhVien->toanKhoa->ghiChuThiLai .= '), ';

                $sinhVien->toanKhoa->ghiChuThiLaiArray->push([
                    'type' => 'TL',
                    'key' => '(ĐTNCD)',
                    'mh_id' => 894
                ]);
            }

            if (isset($sinhVien->toanKhoa->avg)) {
                $sinhVien->toanKhoa->avg = $sinhVien->toanKhoa->tichLuy;
                if ($diemkhoaluan != -1) {
                    $sinhVien->toanKhoa->avg_totnghiep = number_format((($sinhVien->toanKhoa->avg * 3) + ($diemkhoaluan * 2)) / 5, 1);
                } else {
                    $sinhVien->toanKhoa->avg_totnghiep = number_format(($diemlythuyet + ($diemthuchanh * 2) + ($sinhVien->toanKhoa->avg * 3)) / 6, 1);
                }
                $sinhVien->toanKhoa->hocLucTN = $this->getHocLucByDiem($sinhVien->toanKhoa->avg_totnghiep, $quyChe2022);
            }
        }

        return $data;
    }

    public function getKetQuaHocTapSinhVienChuaDuocXetTN($semester, $lhId, $dt_id_current, $svId = 0)
    {
        $lopHoc = LopHoc::find($lhId);
        $quyChe2022 = $lopHoc->lh_nienche == 1;
        $data = $this->getKetQuaHocTap($semester, $lhId, $svId);
        //$modalDotThi = DotThi::find($dt_id);

        // Tìm đợt thi đầu tiêu sinh vien chưa TN
        $dxtn_first_sv_chuadattn = DB::table('qlsv_dotxettotnghiep_sinhvien as dxtn_sv')
            ->join('qlsv_dotthi_dotxettotnghiep as dtdxtn', function ($join) {
                $join->on('dtdxtn.dxtn_id', '=', 'dxtn_sv.dxtn_id');
            })
            ->where('dxtn_sv.lh_id', '=', $lhId)
            ->where('dxtn_sv.sv_id', $svId)
            ->select(DB::raw('DISTINCT dtdxtn.dt_id'))
            ->first();


        $dt_id = $dxtn_first_sv_chuadattn->dt_id;

        $numberExamPresent = DB::table('qlsv_dotthi_diem as dtd')
            ->join('qlsv_dotthi_bangdiem as bd', 'bd.dt_bd_id', '=', 'dtd.dt_bd_id')
            ->where('bd.dt_id', $dt_id)
            ->where('bd.lh_id', $lhId)
            ->max('dtd.svd_lan');

        $numberExamreCently = DB::table('qlsv_dotthi_diem as dtd')
            ->join('qlsv_dotthi_bangdiem as dtbd', 'dtd.dt_bd_id', '=', 'dtbd.dt_bd_id')
            ->where('dtbd.lh_id', $lhId)
            ->where('dtbd.dt_id', '<=', $numberExamPresent)
            ->select('dtbd.dt_id')
            ->distinct()
            ->orderBy('dtbd.dt_id', 'desc')
            ->limit($numberExamPresent)
            ->pluck('dt_id')
            ->toArray();

        $dxtn_id = DotThiDotXetTotNghiep::where('dt_id', $dt_id)->first()->dxtn_id;
        //tính điểm môn học
        foreach ($data['danhSachSinhVien'] as $sinhVien) {
            //-1 là chưa đi thi null là vắng thi
            // $TBToanKhoa = isset($sinhVien->toanKhoa->avg) ? $sinhVien->toanKhoa->avg : -1;
            $diemdotthi = DB::table('qlsv_dotthi_diem as d')
                ->join('qlsv_dotthi_bangdiem as bd', 'bd.dt_bd_id', '=', 'd.dt_bd_id')
                ->join('qlsv_monhoc as mh', 'mh.mh_id', '=', 'bd.mh_id')
                ->where('d.sv_id', $svId)
                ->whereIn('bd.dt_id', $numberExamreCently)
                ->select('d.svd_first', 'd.svd_lan', 'bd.mh_id', 'd.svd_ghichu', 'mh.mh_loai', 'd.svd_loai', 'd.svd_dieukien', 'd.svd_khongdatloai', 'd.svd_lan', 'bd.dt_id')
                ->get();

            // Số lần
            $sinhVien->svd_lanthihientai = $numberExamPresent;

            $dxtn_sv = DB::table('qlsv_dotxettotnghiep_sinhvien as dxtn_sv')
                ->join('qlsv_sinhvien as sv', function ($join) {
                    $join->on('sv.sv_id', '=', 'dxtn_sv.sv_id');
                })
                ->join('qlsv_lophoc as lh', function ($join) {
                    $join->on('lh.lh_id', '=', 'dxtn_sv.lh_id');
                })
                ->join('qlsv_khoadaotao as kdt', function ($join) {
                    $join->on('kdt.kdt_id', '=', 'lh.kdt_id');
                })
                ->where('dxtn_sv.lh_id', '=', $lhId)
                ->where('dxtn_sv.dxtn_id', '=', $dxtn_id)
                ->where('dxtn_sv.sv_id', $svId)
                // ->whereRaw('dxtn_sv.svxtn_dattn = 1')
                ->select(DB::raw('DISTINCT dxtn_sv.svxtn_dattn, dxtn_sv.svxtn_vipham, dxtn_sv.svxtn_ghichu'))
                ->first();


            if ($dxtn_sv) {
                $sinhVien->svxtn_dattn = $dxtn_sv->svxtn_dattn;
                $sinhVien->svxtn_vipham = $dxtn_sv->svxtn_vipham;
                $sinhVien->svxtn_ghichu = $dxtn_sv->svxtn_ghichu;
            }

            //2 chinhs tri, 3 thuc hanh, 4 ly thuyet, 5 bao ve
            $sinhVien->thuchanhlan1 = -1;
            $sinhVien->lythuyetlan1 = -1;
            $sinhVien->chinhtrilan1 = -1;
            $sinhVien->khoaluanlan1 = -1;

            $sinhVien->thuchanhlan2 = -1;
            $sinhVien->lythuyetlan2 = -1;
            $sinhVien->chinhtrilan2 = -1;
            $sinhVien->khoaluanlan2 = -1;

            $sinhVien->thuchanhlan3 = -1;
            $sinhVien->lythuyetlan3 = -1;
            $sinhVien->chinhtrilan3 = -1;
            $sinhVien->khoaluanlan3 = -1;

            foreach ($diemdotthi as $ddt) {
                $sinhVien->svd_ghichu = $ddt->svd_ghichu;
                $sinhVien->svd_loai = $ddt->svd_loai;
                $sinhVien->svd_khongdatloai = $ddt->svd_khongdatloai;

                if ($ddt->dt_id == $dt_id) {
                    $sinhVien->svd_dieukien = $ddt->svd_dieukien;
                }
                if ($ddt->svd_lan == 1) {
                    if ($ddt->mh_loai == 2) {
                        $sinhVien->chinhtrilan1 = $ddt->svd_first;
                    } else if ($ddt->mh_loai == 3) {
                        $sinhVien->thuchanhlan1 = $ddt->svd_first;
                    } else if ($ddt->mh_loai == 4) {
                        $sinhVien->lythuyetlan1 = $ddt->svd_first;
                    } else if ($ddt->mh_loai == 5) {
                        $sinhVien->khoaluanlan1 = $ddt->svd_first;
                    }
                } else if ($ddt->svd_lan == 2) {
                    if ($ddt->mh_loai == 2) {
                        $sinhVien->chinhtrilan2 = $ddt->svd_first;
                    } else if ($ddt->mh_loai == 3) {
                        $sinhVien->thuchanhlan2 = $ddt->svd_first;
                    } else if ($ddt->mh_loai == 4) {
                        $sinhVien->lythuyetlan2 = $ddt->svd_first;
                    } else if ($ddt->mh_loai == 5) {
                        $sinhVien->khoaluanlan2 = $ddt->svd_first;
                    }
                } else if ($ddt->svd_lan == 3) {
                    if ($ddt->mh_loai == 2) {
                        $sinhVien->chinhtrilan3 = $ddt->svd_first;
                    } else if ($ddt->mh_loai == 3) {
                        $sinhVien->thuchanhlan3 = $ddt->svd_first;
                    } else if ($ddt->mh_loai == 4) {
                        $sinhVien->lythuyetlan3 = $ddt->svd_first;
                    } else if ($ddt->mh_loai == 5) {
                        $sinhVien->khoaluanlan3 = $ddt->svd_first;
                    }
                }
            }

            $diemchinhtri = $sinhVien->chinhtrilan1;
            $diemlythuyet = $sinhVien->lythuyetlan1;
            $diemthuchanh = $sinhVien->thuchanhlan1;
            $diemkhoaluan = $sinhVien->khoaluanlan1;


            if ($sinhVien->khoaluanlan2 != -1) {
                $diemkhoaluan = $sinhVien->khoaluanlan2;
            } else if ($sinhVien->khoaluanlan3 != -1) {
                $diemkhoaluan = $sinhVien->khoaluanlan3;
            }

            if ($sinhVien->thuchanhlan2 != -1) {
                $diemthuchanh = $sinhVien->thuchanhlan2;
            } else if ($sinhVien->thuchanhlan3 != -1) {
                $diemthuchanh = $sinhVien->thuchanhlan3;
            }

            if ($sinhVien->lythuyetlan2 != -1) {
                $diemlythuyet = $sinhVien->lythuyetlan2;
            } else if ($sinhVien->lythuyetlan3 != -1) {
                $diemlythuyet = $sinhVien->lythuyetlan3;
            }

            if ($sinhVien->lythuyetlan2 != -1) {
                $diemlythuyet = $sinhVien->lythuyetlan2;
            } else if ($sinhVien->lythuyetlan3 != -1) {
                $diemlythuyet = $sinhVien->lythuyetlan3;
            }

            $sinhVien->toanKhoa->ghiChuThiLai = "";

            if ($diemchinhtri < 5 && $diemchinhtri != -1 && $diemkhoaluan == -1) {
                $sinhVien->toanKhoa->ghiChuThiLai .= 'ĐTNCT(L1:' . number_format($sinhVien->chinhtrilan1, 1);
                if ($sinhVien->chinhtrilan2 != -1) {
                    $sinhVien->toanKhoa->ghiChuThiLai .= ', L2:' . number_format($sinhVien->chinhtrilan2, 1);
                }
                if ($sinhVien->chinhtrilan3 != -1) {
                    $sinhVien->toanKhoa->ghiChuThiLai .= ', L3:' . number_format($sinhVien->chinhtrilan3, 1);
                }
                $sinhVien->toanKhoa->ghiChuThiLai .= '), ';
            }

            if ($diemlythuyet < 5 && $diemkhoaluan == -1) {
                $sinhVien->toanKhoa->ghiChuThiLai .= 'ĐTNLT(L1:' . number_format($sinhVien->lythuyetlan1, 1);
                if ($sinhVien->lythuyetlan2 != -1) {
                    $sinhVien->toanKhoa->ghiChuThiLai .= ', L2:' . number_format($sinhVien->lythuyetlan2, 1);
                }
                if ($sinhVien->lythuyetlan3 != -1) {
                    $sinhVien->toanKhoa->ghiChuThiLai .= ', L3:' . number_format($sinhVien->lythuyetlan3, 1);
                }
                $sinhVien->toanKhoa->ghiChuThiLai .= '), ';
            }

            if ($diemthuchanh < 5 && $diemkhoaluan == -1) {
                $sinhVien->toanKhoa->ghiChuThiLai .= 'ĐTNTH(L1:' . number_format($sinhVien->thuchanhlan1, 1);
                if ($sinhVien->thuchanhlan2 != -1) {
                    $sinhVien->toanKhoa->ghiChuThiLai .= ', L2:' . number_format($sinhVien->thuchanhlan2, 1);
                }
                if ($sinhVien->thuchanhlan3 != -1) {
                    $sinhVien->toanKhoa->ghiChuThiLai .= ', L3:' . number_format($sinhVien->thuchanhlan3, 1);
                }
                $sinhVien->toanKhoa->ghiChuThiLai .= '), ';
            }

            if ($diemkhoaluan < 5 && $diemkhoaluan != -1) {
                $sinhVien->toanKhoa->ghiChuThiLai .= 'ĐTNCD(L1:' . number_format($sinhVien->khoaluanlan1, 1);
                if ($sinhVien->khoaluanlan2 != -1) {
                    $sinhVien->toanKhoa->ghiChuThiLai .= ', L2:' . number_format($sinhVien->khoaluanlan2, 1);
                }
                if ($sinhVien->khoaluanlan3 != -1) {
                    $sinhVien->toanKhoa->ghiChuThiLai .= ', L3:' . number_format($sinhVien->khoaluanlan3, 1);
                }
                $sinhVien->toanKhoa->ghiChuThiLai .= '), ';
            }

            if (isset($sinhVien->toanKhoa->avg)) {
                if ($diemkhoaluan != -1) {
                    $sinhVien->toanKhoa->avg_totnghiep = number_format((($sinhVien->toanKhoa->avg * 3) + ($diemkhoaluan * 2)) / 5, 1);
                } else {
                    $sinhVien->toanKhoa->avg_totnghiep = number_format(($diemlythuyet + ($diemthuchanh * 2) + ($sinhVien->toanKhoa->avg * 3)) / 6, 1);
                }
                $sinhVien->toanKhoa->hocLucTN = $this->getHocLucByDiem($sinhVien->toanKhoa->avg_totnghiep, $quyChe2022);
            }
        }

        return $data;
    }


    public function getHocLucByDiem($diem, $quyche)
    {
        $hocLuc = '';
        // $quyche == true là quy chế năm 2022
        // $quyche == false là quy chế năm 2020
        if ($quyche == true) {
            if ($diem >= 9) {
                $hocLuc = 'Xuất sắc';
            } else if ($diem >= 8 && $diem <= 8.9) {
                $hocLuc = 'Giỏi';
            } else if ($diem >= 7 && $diem <= 7.9) {
                $hocLuc = 'Khá';
            } else if ($diem >= 5 && $diem <= 6.9) {
                $hocLuc = 'Trung bình';
            } else if ($diem <= 4.9) {
                $hocLuc = 'Yếu';
            } else {
                $hocLuc = 'Rớt';
            }
        } else {
            if ($diem >= 9) {
                $hocLuc = 'Xuất sắc';
            } else if ($diem >= 8 && $diem <= 8.9) {
                $hocLuc = 'Giỏi';
            } else if ($diem >= 7 && $diem <= 7.9) {
                $hocLuc = 'Khá';
            } else if ($diem >= 6 && $diem <= 6.9) {
                $hocLuc = 'Trung bình khá';
            } else if ($diem >= 5 && $diem <= 5.9) {
                $hocLuc = 'Trung bình';
            } else if ($diem <= 4.9) {
                $hocLuc = 'Yếu';
            } else {
                $hocLuc = 'Rớt';
            }
        }

        return $hocLuc;
    }

    public function setHocLucByDB($value, $quyche)
    {
        $hocLuc = '';
        // $quyche == true là quy chế năm 2022
        // $quyche == false là quy chế năm 2020
        if ($quyche == true) {
            if ($value == 'Xuất sắc') {
                $hocLuc = 1;
            } else if ($value == 'Giỏi') {
                $hocLuc = 2;
            } else if ($value == 'Khá') {
                $hocLuc = 3;
            } else if ($value == 'Trung bình') {
                $hocLuc = 5;
            } else if ($value == 'Yếu') {
                $hocLuc = 0;
            } else {
                $hocLuc = -1;
            }
        } else {
            if ($value == 'Xuất sắc') {
                $hocLuc = 1;
            } else if ($value == 'Giỏi') {
                $hocLuc = 2;
            } else if ($value == 'Khá') {
                $hocLuc = 3;
            } else if ($value == 'Trung bình khá') {
                $hocLuc = 4;
            } else if ($value == 'Trung bình') {
                $hocLuc = 5;
            } else if ($value == 'Yếu') {
                $hocLuc = 0;
            } else {
                $hocLuc = -1;
            }
        }

        return $hocLuc;
    }

    public function getHocLucByDB($value, $quyche)
    {
        $hocLuc = '';
        // $quyche == true là quy chế năm 2022
        // $quyche == false là quy chế năm 2020
        if ($quyche == true) {
            if ($value == 1) {
                $hocLuc = 'Xuất sắc';
            } else if ($value == 2) {
                $hocLuc = 'Giỏi';
            } else if ($value == 3) {
                $hocLuc = 'Khá';
            } else if ($value == 5) {
                $hocLuc = 'Trung bình';
            } else if ($value == 'Yếu') {
                $hocLuc = 0;
            } else {
                $hocLuc = -1;
            }
        } else {
            if ($value == 1) {
                $hocLuc = 'Xuất sắc';
            } else if ($value == 2) {
                $hocLuc = 'Giỏi';
            } else if ($value == 3) {
                $hocLuc = 'Khá';
            } else if ($value == 4) {
                $hocLuc = 'Trung bình khá';
            } else if ($value == 5) {
                $hocLuc = 'Trung bình';
            } else if ($value == 'Yếu') {
                $hocLuc = 0;
            } else {
                $hocLuc = -1;
            }
        }

        return $hocLuc;
    }

    public function getKetQuaHocTap($semester, $lhId, $svId = 0, $dxtn = false)
    {
        $reqSemester = $semester ?: 1;
        $reqYear = 1;
        $semesters = [$reqSemester];
        if ($reqSemester <= 2) {
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
        } else if ($reqSemester == 123456) {
            $reqYear = 2;
            $semesters = [1, 2, 3, 4, 5, 6];
        } else {
            abort(404);
        }

        $lopHoc = LopHoc::find($lhId);
        $quyChe2022 = $lopHoc->lh_nienche == 1;
        //$khoaDaoTao = $lopHoc->khoaDaoTao;
        if (!$lopHoc) {
            abort(404);
        }
        $danhSachSinhVien = $lopHoc->sinhVien();
        if ($svId) {
            $danhSachSinhVien->where('qlsv_sinhvien.sv_id', $svId);
        }
        $danhSachSinhVien = $danhSachSinhVien->orderBy('sv_ma', 'asc')->get();

        $danhSachSinhVien = $danhSachSinhVien->filter(function ($sinhVien) use ($semesters) {
            $passXoaTen = false;
            $passTotNghiep = false;
            if (!$sinhVien->quyetDinhXoaTen()->exists() || $sinhVien->quyetDinhXoaTen->first()->pivot->qd_hocky > $semesters[0]) {
                $passXoaTen = true;
            }

            if (!$sinhVien->quyetDinhTotNghiep()->exists() || $sinhVien->quyetDinhTotNghiep->first()->pivot->qd_hocky > $semesters[0]) {
                $passTotNghiep = true;
            }
            return $passXoaTen && $passTotNghiep;
        })->values()->all();

        $danhSachSinhVien = collect($danhSachSinhVien);

        $danhSachMonHoc = DB::table('qlsv_lophoc as lh')
            ->join('qlsv_lophoc_monhoc as lh_mh', function ($join) use ($semesters) {
                $join->on('lh_mh.lh_id', '=', 'lh.lh_id')
                    ->whereIn('lh_mh.lh_mh_hocky', $semesters);
            })
            ->join('qlsv_monhoc as mh', 'mh.mh_id', '=', 'lh_mh.mh_id')
            ->where('lh.lh_id', $lhId)
            ->whereRaw('mh.mh_loai = 1')
            ->select('mh.mh_id', 'mh.mh_ma', 'mh.mh_ten', 'mh.mh_sodonvihoctrinh', 'mh.mh_tichluy', 'lh_mh.lh_mh_hocky', 'mh.mh_loai')
            ->orderBy('lh_mh.lh_mh_hocky', 'asc')
            ->orderBy('lh_mh.lh_mh_index', 'asc')
            ->get();

        //dd($danhSachMonHoc);

        $bangDiem = DB::table('qlsv_bangdiem as bd')
            ->join('qlsv_sinhvien_diem as svd', 'svd.bd_id', '=', 'bd.bd_id')
            ->join('qlsv_lophoc_monhoc as lh_mh', 'lh_mh.mh_id', '=', 'bd.mh_id')
            ->whereIn('bd.kdt_hocky', $semesters)
            ->where('bd.lh_id', $lhId)
            ->whereRaw("(svd.sv_id = $svId OR $svId = 0)")
            ->where('bd.bd_type', BangDiemType::BANGDIEM_MONHOC)
            ->select(
                'svd.sv_id',
                'bd.mh_id',
                'svd.svd_dulop',
                'svd.svd_first',
                'svd.svd_second',
                'svd.svd_second_hocky',
                'svd.svd_third',
                'svd.svd_third_hocky',
                'svd.svd_final',
                'bd.kdt_hocky',
                'lh_mh.lh_mh_hocky',
                'svd.svd_ghichu',
                'svd.svd_exam_first',
                'svd.svd_exam_second',
                'svd.svd_exam_third'
            )
            ->get();


        $bangDiemRenLuyen = DB::table('qlsv_bangdiem as bd')
            ->join('qlsv_sinhvien_diem as svd', 'svd.bd_id', '=', 'bd.bd_id')
            ->whereIn('bd.kdt_hocky', $semesters)
            ->where('bd.lh_id', $lhId)
            ->whereRaw("(svd.sv_id = $svId OR $svId = 0)")
            ->where('bd.bd_type', BangDiemType::BANGDIEM_HOCKY)
            ->select('svd.sv_id', 'svd.svd_final', 'bd.kdt_hocky')
            ->get();


        foreach ($danhSachSinhVien as $sinhVien) {
            $svId = $sinhVien->sv_id;
            $sinhVien->semesters = collect([]);
            $notes = collect();

            foreach ($semesters as $seIndex => $hocKy) {
                $semester = new \stdClass;
                $semester->number = $semesters[$seIndex];
                $semester->monHoc = collect();
                $semester->notes = collect();

                $diemRenLuyen = $bangDiemRenLuyen->first(function ($svd) use ($svId, $hocKy) {
                    return $svd->sv_id == $svId && $svd->kdt_hocky == $hocKy;
                });

                foreach ($danhSachMonHoc as $monHoc) {
                    if ($monHoc->lh_mh_hocky == $hocKy) {
                        $temp = clone $monHoc;
                        $functionThemGhi = function ($type, $diem) use ($semester, $monHoc) {
                            $semester->notes->push([
                                'type' => $type,
                                'key' => $monHoc->mh_ma,
                                'value' => $diem
                            ]);
                        };

                        $ketQua = $bangDiem->first(function ($svd) use ($semester, $svId, $temp, $hocKy) {
                            return ($svd->sv_id == $svId && $svd->mh_id == $temp->mh_id && $svd->kdt_hocky == $hocKy);
                            //return ($svd->sv_id == $svId && $svd->mh_id == $temp->mh_id && $svd->lh_mh_hocky ==  (int)$hocKy);
                        });

                        $temp->ketQua = $ketQua;


                        if (isset($temp->ketQua)) {
                            if ($temp->ketQua->svd_first) {
                                $temp->ketQua->svd_first = number_format($temp->ketQua->svd_first, 1);
                            }

                            // Lan 1
                            $coThilan1 = isset($temp->ketQua->svd_exam_first);
                            $coDiemTongKetLan1 = isset($temp->ketQua->svd_first);
                            $datDiemThiLan1 = $coThilan1 && $temp->ketQua->svd_exam_first >= 5;
                            $datDiemTongKetLan1 = $coDiemTongKetLan1 && $temp->ketQua->svd_first >= 5;

                            // Lan 2
                            $coThilan2 = isset($temp->ketQua->svd_exam_second);
                            $coDiemTongKetLan2 = isset($temp->ketQua->svd_second);
                            $datDiemThiLan2 = $coThilan2 && $temp->ketQua->svd_exam_second >= 5;
                            $datDiemTongKetLan2 = $coDiemTongKetLan2 && $temp->ketQua->svd_second >= 5;
                            // Lan 3
                            $coThilan3 = isset($temp->ketQua->svd_exam_third);
                            $coDiemTongKetLan3 = isset($temp->ketQua->svd_third);
                            $datDiemThiLan3 = $coThilan3 && $temp->ketQua->svd_exam_third >= 5;
                            $datDiemTongKetLan3 = $coDiemTongKetLan3 && $temp->ketQua->svd_third >= 5;
                            $hocKyXem = $hocKy;
                            if ($reqSemester <= 6) {
                                // Không thay đổi giả trị
                            } else {
                                $hocKyXem = end($semesters);
                            }

                            // Mục tiêu 1: Tìm điểm hiển thị: hiển thị điểm lần 1, lần 2 hay lần 3?
                            if ($coDiemTongKetLan3 && $temp->ketQua->svd_third_hocky <= $hocKyXem) {
                                // Điểm lần 3, ưu tiên check trước
                                // Các học kỳ trước => hiển thị điểm cải thiện học kỳ trước
                                $temp->ketQua->display_score = $temp->ketQua->svd_third;
                                $temp->ketQua->exam_core = $temp->ketQua->svd_exam_third;
                            } else if ($coDiemTongKetLan2 && $temp->ketQua->svd_second_hocky <= $hocKyXem) {
                                // Điểm lần 2
                                // Các học kỳ trước => hiển thị điểm cải thiện học kỳ trước
                                $temp->ketQua->display_score = $temp->ketQua->svd_second;
                                $temp->ketQua->exam_core = $temp->ketQua->svd_exam_second;
                            } else {
                                // Mặc định hiển thị điểm lần 1
                                $temp->ketQua->display_score = $temp->ketQua->svd_first;
                                $temp->ketQua->exam_core = $temp->ketQua->svd_exam_first;
                            }

                            // Mục tiêu 2: xác định tô đỏ
                            $temp->ketQua->passed = true;
                            if ($quyChe2022) {
                                if ($temp->ketQua->display_score < 5 || ($temp->ketQua->exam_core != null && $temp->ketQua->exam_core < 5)) {
                                    $temp->ketQua->passed = false;
                                }
                            } else {
                                if ($temp->ketQua->display_score < 5) {
                                    $temp->ketQua->passed = false;
                                }
                            }

                            // Mục tiêu 3: Xác định ghi chú
                            $skipNoteHL = false;
                            $skipNoteTL = false;

                            if (isset($temp->ketQua) && $temp->ketQua->svd_ghichu == 'Học lại') {
                                $semester->notes->push([
                                    'type' => 'HL',
                                    'key' => $monHoc->mh_ma,
                                    'mh_id' => $monHoc->mh_id,
                                    'mh_tichluy' => $monHoc->mh_tichluy
                                ]);
                                $skipNoteHL = true;
                            } else if (isset($temp->ketQua) && $temp->ketQua->svd_ghichu == 'Thi lại') {
                                // dxtn - đợt xét tốt nghiệp = true: để hiển thị ra hết các ghi chú "Thi lại" từ trước đến nay
                                if ($dxtn == false) {
                                    // Kiểm tra nếu đã thi đạt môn cãi thiện rồi thì khỏi hiện thị môn thi lại
                                    if ($temp->ketQua->passed == true) {
                                        $skipNoteTL = true;
                                    } else {
                                        $semester->notes->push([
                                            'type' => 'TL',
                                            'key' => $monHoc->mh_ma,
                                            'mh_id' => $monHoc->mh_id,
                                            'mh_tichluy' => $monHoc->mh_tichluy
                                        ]);
                                        $skipNoteTL = false;
                                    }
                                } else {
                                    $semester->notes->push([
                                        'type' => 'TL',
                                        'key' => $monHoc->mh_ma,
                                        'mh_id' => $monHoc->mh_id,
                                        'mh_tichluy' => $monHoc->mh_tichluy
                                    ]);
                                    $skipNoteTL = true;
                                }
                            }

                            if ($coDiemTongKetLan3) {
                                // Lấy điểm lần 3 thì:
                                // Thêm ghi chú lần 3
                                if ($coDiemTongKetLan2) {
                                    $functionThemGhi('DL2', $temp->ketQua->svd_exam_second);
                                }
                                if ($quyChe2022 && $coThilan3) {
                                    // Điểm thi không đạt
                                    if (!$datDiemThiLan3) {
                                        if (!$skipNoteTL && !$skipNoteHL) {
                                            $semester->notes->push([
                                                'type' => 'TL',
                                                'key' => $monHoc->mh_ma,
                                                'mh_id' => $monHoc->mh_id,
                                                'mh_tichluy' => $monHoc->mh_tichluy
                                            ]);
                                        }

                                        // Hiển thị điểm thi lại
                                        if ($temp->ketQua->svd_exam_third > 0 && !$skipNoteHL) {
                                            $functionThemGhi('DTL3', $temp->ketQua->svd_exam_third);
                                        }
                                    }
                                }
                                //$temp->ketQua->svd_second_hocky == $hocKyXem
                            } else if ($coDiemTongKetLan2) {
                                // Lấy điểm lần 2 thì:
                                // Thêm ghi chú lần 2
                                $functionThemGhi('DL1', $temp->ketQua->svd_exam_first);
                                if ($quyChe2022 && $coThilan2) {
                                    // Điểm thi không đạt
                                    if (!$datDiemThiLan2) {
                                        if (!$skipNoteTL && !$skipNoteHL) {
                                            $semester->notes->push([
                                                'type' => 'TL',
                                                'key' => $monHoc->mh_ma,
                                                'mh_id' => $monHoc->mh_id,
                                                'mh_tichluy' => $monHoc->mh_tichluy
                                            ]);
                                        }

                                        // Hiển thị điểm thi lại
                                        if ($temp->ketQua->svd_exam_second > 0 && !$skipNoteHL) {
                                            $functionThemGhi('DTL2', $temp->ketQua->svd_exam_second);
                                        }
                                    }
                                }
                            } else if ($coDiemTongKetLan1) {
                                // Lấy điểm lần 1 thì:
                                // Thêm ghi chú lần 1
                                // Không ghi chú

                                // Điểm thi không đạt
                                if ($quyChe2022 && $coThilan1) {
                                    // if($monHoc->mh_ma == "MH 08") {
                                    //     dd($temp);
                                    // }


                                    if (!$datDiemThiLan1) {
                                        if (!$skipNoteTL && !$skipNoteHL) {
                                            $semester->notes->push([
                                                'type' => 'TL',
                                                'key' => $monHoc->mh_ma,
                                                'mh_id' => $monHoc->mh_id,
                                                'mh_tichluy' => $monHoc->mh_tichluy,
                                            ]);
                                        }

                                        // Hiển thị điểm thi lại
                                        if ($temp->ketQua->svd_exam_first > 0 && !$skipNoteHL) {
                                            $functionThemGhi('DTL1', $temp->ketQua->svd_exam_first);
                                        }
                                    }
                                }
                            }
                        }

                        $semester->monHoc->push($temp);
                    }
                }

                $tinhAvg = $semester->monHoc->every(function ($monHoc) {
                    return isset($monHoc->ketQua);
                });


                if ($diemRenLuyen) {
                    $sumDiem = 0;
                    $sumTinChi = 0;
                    $sumDiemTl = 0;
                    $sumTinChiTl = 0;
                    $semester->monHoc->each(function ($monHoc) use (&$sumDiem, &$sumTinChi, &$sumDiemTl, &$sumTinChiTl) {
                        if ($monHoc->mh_tichluy) {
                            $sumTinChi += $monHoc->mh_sodonvihoctrinh;
                            if ($monHoc->ketQua) {
                                $svdDiem = null;
                                if ($monHoc->ketQua->svd_second || $monHoc->ketQua->svd_first == null) {
                                    $svdDiem = $monHoc->ketQua->svd_second;
                                } else {
                                    $svdDiem = $monHoc->ketQua->svd_first;
                                }
                                $sumDiem += $svdDiem * $monHoc->mh_sodonvihoctrinh;
                            }
                        }
                    });

                    
                    if ($sumTinChi) {
                        $semester->avg = number_format($sumDiem / $sumTinChi, 1);
                    }

                    $curSemester = $hocKy;
                    $semesterSumTinChi = [];
                    for ($tI = 1; $tI <= $curSemester; $tI++) {
                        $semesterSumTinChi[] = $tI;
                    }
                    $danhSachDiemMonHocTichLuy = $this->getDanhSachDiemMonHocTichLuy($svId, $lopHoc->kdt_id, $lopHoc->lh_id, $semesterSumTinChi);
                    $danhSachDiemMonHocTichLuy = $danhSachDiemMonHocTichLuy->filter(function ($item) use ($hocKy) {
                        // Mặc định lấy điểm lần 1
                        $item->score = $item->svd_first;
                        if ($item->svd_third >= 5 && $item->svd_third_hocky <= $hocKy) {
                            // Nếu là các học kỳ trước => hiển thị điểm cải thiện học kỳ trước
                            $item->score = $item->svd_third;
                        } else if ($item->svd_second >= 5 && $item->svd_second_hocky <= $hocKy) {
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
                    $semester->tinChiTichLuy = $tinChiTichLuy;
                    $semester->tichLuy = $tinChiTichLuy == 0 ? number_format($tinChiTichLuy, 1) : number_format($tichLuy / $tinChiTichLuy, 1);

                    $semester->diemRenLuyen = $diemRenLuyen->svd_final;
                    if ($semester->diemRenLuyen > 0) {
                        $semester->hocLuc = getHocLuc(isset($semester->avg) ? $semester->avg : 0);
                    }
                }

                $sinhVien->semesters->push($semester);
                $notes = $notes->merge($semester->notes);

                // if ($semester->number >= $khoaDaoTao->kdt_hocky) {
                //     break;
                // }
                if ($semester->number >= $lopHoc->lh_hocky) {
                    break;
                }
            }

            // Điểm năm
            $chunkSemester = $sinhVien->semesters->chunk(2);
            $years = collect();
            foreach ($chunkSemester as $csIndex => $twoSemester) {
                $year = new \stdClass;
                $curSemester = $twoSemester->last()->number;
                $tinhDiemNam = $twoSemester->every(function ($semester) {
                    return isset($semester->diemRenLuyen);
                });

                if ($tinhDiemNam) {
                    $year->avg = number_format($twoSemester->filter(function ($semester) {
                        return isset($semester->avg);
                    })
                        ->map(function ($semester) {
                            return $semester->avg;
                        })
                        ->avg(), 1);
                }

                // Tính tín chỉ tích lũy năm
                $semesterSumTinChi = [];
                for ($tI = 1; $tI <= $curSemester; $tI++) {
                    $semesterSumTinChi[] = $tI;
                }
                $danhSachDiemMonHocTichLuy = $this->getDanhSachDiemMonHocTichLuy($svId, $lopHoc->kdt_id, $lopHoc->lh_id, $semesterSumTinChi);

                $danhSachDiemMonHocTichLuy = $danhSachDiemMonHocTichLuy->filter(function ($item) use ($twoSemester) {
                    // Mặc định hiển thị điểm lần 1
                    $item->score = $item->svd_first;
                    if ($item->svd_third >= 5 && $item->svd_third_hocky <= $twoSemester->last()->number) {
                        // Nếu ở các học kỳ trước => hiển thị điểm cải thiện học kỳ trước
                        $item->score = $item->svd_third;
                    } else if ($item->svd_second >= 5 && $item->svd_second_hocky <= $twoSemester->last()->number) {
                        // Nếu ở các học kỳ trước => hiển thị điểm cải thiện học kỳ trước
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
                $year->tinChiTichLuy = $tinChiTichLuy;
                $year->tichLuy = $tinChiTichLuy == 0 ? number_format($tinChiTichLuy, 1) : number_format($tichLuy / $tinChiTichLuy, 1);
                // End tính tín chỉ tích lũy năm

                $diemRenLuyen = number_format($twoSemester->filter(function ($semester) {
                    return isset($semester->diemRenLuyen);
                })->map(function ($semester) {
                    return $semester->diemRenLuyen;
                })
                    ->avg(), 0);

                if ($diemRenLuyen) {
                    $year->diemRenLuyen = $diemRenLuyen;

                    $tinhHocLucNam = $twoSemester->every(function ($semester) {
                        return isset($semester->hocLuc);
                    });
                    if ($tinhHocLucNam) {
                        $year->hocLuc = getHocLuc(isset($year->avg) ? $year->avg : 0);
                    }
                }
                $year->semesters = $twoSemester;
                $years->push($year);
            }
            // End điểm năm

            // Điểm toàn khóa
            $toanKhoa = new \stdClass;
            $tinhDiemToanKhoa = $years->every(function ($year) {
                return isset($year->avg);
            });

            if ($tinhDiemToanKhoa) {
                // dd($years);

                $sumDiem = 0;
                $sumTinChi = 0;

                $years->each(function ($year) use (&$sumDiem, &$sumTinChi) {
                    $year->semesters->each(function ($semester) use (&$sumDiem, &$sumTinChi) {
                        $semester->monHoc->each(function ($monHoc) use (&$sumDiem, &$sumTinChi) {
                            if ($monHoc->mh_tichluy) {
                                $sumTinChi += $monHoc->mh_sodonvihoctrinh;
                                if ($monHoc->ketQua) {
                                    $sumDiem += $monHoc->ketQua->svd_first * $monHoc->mh_sodonvihoctrinh;
                                }
                            }
                        });
                    });
                });

                if ($sumTinChi) {
                    $toanKhoa->avg = number_format($sumDiem / $sumTinChi, 1);
                }

                // $toanKhoa->avg = number_format($years->map(function ($year) {
                //         return $year->avg;
                //     })
                //     ->avg(), 1);
            }

            // Tính tín chỉ tích lũy toàn khóa
            $curSemester = collect($semesters)->last();
            $semesterSumTinChi = [];
            for ($tI = 1; $tI <= $curSemester; $tI++) {
                $semesterSumTinChi[] = $tI;
            }
            $danhSachDiemMonHocTichLuy = $this->getDanhSachDiemMonHocTichLuy($svId, $lopHoc->kdt_id, $lopHoc->lh_id, $semesterSumTinChi);
            $danhSachDiemMonHocTichLuy = $danhSachDiemMonHocTichLuy->filter(function ($item) {
                if ($item->svd_third >= 5) {
                    $item->score = $item->svd_third;
                } else if ($item->svd_second >= 5) {
                    $item->score = $item->svd_second;
                } else {
                    $item->score = $item->svd_first;
                }
                return $item->score >= 5;
            });
            $tichLuy = $danhSachDiemMonHocTichLuy->map(function ($item) {
                return $item->score * $item->mh_sodonvihoctrinh;
            })->sum();

            $tinChiTichLuy = $danhSachDiemMonHocTichLuy->map(function ($item) {
                return $item->mh_sodonvihoctrinh;
            })->sum();
            $toanKhoa->tinChiTichLuy = $tinChiTichLuy;
            $toanKhoa->tichLuy = $tinChiTichLuy == 0 ? number_format($tinChiTichLuy, 1) : number_format($tichLuy / $tinChiTichLuy, 1);

            $diemRenLuyen = number_format($years->map(function ($year) {
                return isset($year->diemRenLuyen) ? $year->diemRenLuyen : 0;
            })
                ->avg(), 0);
            if ($diemRenLuyen) {
                $toanKhoa->diemRenLuyen = $diemRenLuyen;

                $tinhHocLucToanKhoa = $years->every(function ($year) {
                    return isset($year->hocLuc);
                });
                if ($tinhHocLucToanKhoa) {
                    $toanKhoa->hocLuc = getHocLuc(isset($toanKhoa->avg) ? $toanKhoa->avg : 0);
                }
            }
            // End điểm toàn khóa

            $sinhVien->years = $years;
            $sinhVien->toanKhoa = $toanKhoa;
            $sinhVien->notes = $notes;
        }


        $sumTinChi = 0;
        $danhSachNamHoc = collect();
        if (!$danhSachMonHoc->isEmpty()) {
            $chunkSemester = collect($semesters)->chunk(2);
            foreach ($chunkSemester as $yIndex => $twoSemester) {
                $year = new \stdClass;
                $year->semesters = collect();
                $year->number = $yIndex + 1;
                $year->sumTinChi = 0;
                $curSemester = collect($twoSemester)->last();
                foreach ($twoSemester as $hocKy) {
                    $objHocKy = new \stdClass;
                    $objHocKy->monHoc = collect();
                    $objHocKy->sumTinChi = 0;
                    foreach ($danhSachMonHoc as $monHoc) {
                        if ($monHoc->lh_mh_hocky == $hocKy) {
                            $objHocKy->monHoc->push($monHoc);
                            if ($monHoc->mh_tichluy) {
                                $objHocKy->sumTinChi += $monHoc->mh_sodonvihoctrinh;
                                $sumTinChi += $monHoc->mh_sodonvihoctrinh;
                            }
                        }
                    }
                    $year->semesters->push($objHocKy);
                    if ($hocKy >= $lopHoc->lh_hocky) {
                        break;
                    }
                }
                $danhSachNamHoc->push($year);
                if ($curSemester >= $lopHoc->lh_hocky) {
                    break;
                }
            }
        }


        $ghiChu = $danhSachMonHoc->map(function ($item) {
            return [
                'key' => $item->mh_tichluy ? $item->mh_ma : $item->mh_ma . ' (*)',
                'value' => $item->mh_ten,
            ];
        });

        $staticGhiChu = collect();

        foreach ($semesters as $sIndex => $hocKy) {
            if ($sIndex == 2) {
                break;
            }

            // Nếu mảng $semesters chỉ có 1 học kỳ thì hiển thị 1 học kỳ tương ứng
            if (count($semesters) == 1) {
                $hocKyNote = $semesters[0];
            } else {
                // $hocKyNote = $sIndex + 1;
                $hocKyNote = $semesters[$sIndex];
            }
            $staticGhiChu->push([
                'key' => 'ĐTBHK' . $hocKyNote,
                'value' => 'Điểm trung bình chung học kỳ ' . $hocKyNote
            ]);
            $staticGhiChu->push([
                'key' => 'RLHK' . $hocKyNote,
                'value' => 'Kết quả rèn luyện học kỳ ' . $hocKyNote,
            ]);
            if ($reqSemester <= 4) {
                $staticGhiChu->push([
                    'key' => 'XLHK' . $hocKyNote,
                    'value' => 'Xếp loại học kỳ ' . $hocKyNote,
                ]);
            }
        }

        if ($reqSemester == 12 || $reqSemester == 34) {
            $staticGhiChu->push([
                'key' => 'ĐTBN' . $reqYear,
                'value' => 'Điểm chung bình chung năm thứ ' . $reqYear,
            ]);
            $staticGhiChu->push([
                'key' => 'RLN' . $reqYear,
                'value' => 'Kết quả rèn luyện năm thứ ' . $reqYear,
            ]);
        } else if ($reqSemester == 123456) {
            foreach ($danhSachNamHoc as $yIndex => $year) {
                $namHoc = $yIndex + 1;
                $staticGhiChu->push([
                    'key' => 'ĐTBN' . $namHoc,
                    'value' => 'Điểm chung bình chung năm thứ ' . $namHoc,
                ]);
                $staticGhiChu->push([
                    'key' => 'RLN' . $namHoc,
                    'value' => 'Kết quả rèn luyện năm thứ ' . $namHoc,
                ]);

                $staticGhiChu->push([
                    'key' => 'ĐTN',
                    'value' => 'Điểm tốt nghiệp',
                ]);
            }

            $staticGhiChu->push([
                'key' => 'ĐTBNTK',
                'value' => 'Điểm chung bình chung toàn khóa',
            ]);
            $staticGhiChu->push([
                'key' => 'RLTK',
                'value' => 'Kết quả rèn luyện toàn khóa'
            ]);
        }

        $finalGhiChu = [
            [
                'key' => 'TCTL',
                'value' => 'Tổng số tín chỉ tích lũy',
            ],
            [
                'key' => 'ĐTBCTL',
                'value' => 'Điểm trung bình chung tích lũy',
            ],
        ];

        $notes = $ghiChu->merge($staticGhiChu->merge($finalGhiChu));

        return [
            'reqSemester' => $reqSemester,
            'reqYear' => $reqYear,
            'lopHoc' => $lopHoc,
            'danhSachSinhVien' => $danhSachSinhVien,
            'danhSachNamHoc' => $danhSachNamHoc,
            'danhSachHocKy' => $danhSachNamHoc,
            'semesters' => $semesters,
            'notes' => $notes,
            'sumTinChi' => $sumTinChi,
        ];
    }

    public function checkSVDatTN($semester, $lhId, $dt_id, $svId = 0)
    {
        $lopHoc = LopHoc::find($lhId);
        $quyChe2022 = $lopHoc->lh_nienche == 1;
        $data = $this->getKetQuaHocTapDatTN($semester, $lhId);
        //$modalDotThi = DotThi::find($dt_id);
        $data['danhSachSinhVien'] = $data['danhSachSinhVien']->reject(function ($item, $key) use ($lhId, $dt_id) {
            return DB::select(
                'select d.* from qlsv_dotthi_diem d inner join qlsv_dotthi_bangdiem bd on bd.dt_bd_id = d.dt_bd_id  where bd.lh_id = ? AND bd.dt_id = ? AND d.sv_id = ?',
                [$lhId, $dt_id, $item['sv_id']]
            ) == null;
        });

        //tính điểm môn học
        foreach ($data['danhSachSinhVien'] as $sinhVien) {
            //-1 là chưa đi thi null là vắng thi
            // $TBToanKhoa = isset($sinhVien->toanKhoa->avg) ? $sinhVien->toanKhoa->avg : -1;
            $diemdotthi = DB::table('qlsv_dotthi_diem as d')
                ->join('qlsv_dotthi_bangdiem as bd', 'bd.dt_bd_id', '=', 'd.dt_bd_id')
                ->join('qlsv_monhoc as mh', 'mh.mh_id', '=', 'bd.mh_id')
                ->where('d.sv_id', $sinhVien->sv_id)
                ->select('d.svd_first', 'd.svd_lan', 'bd.mh_id', 'd.svd_ghichu', 'mh.mh_loai', 'd.svd_loai', 'd.svd_dieukien', 'd.svd_khongdatloai', 'bd.dt_id')
                ->get();

            //2 chinhs tri, 3 thuc hanh, 4 ly thuyet, 5 bao ve
            $sinhVien->thuchanhlan1 = -1;
            $sinhVien->lythuyetlan1 = -1;
            $sinhVien->chinhtrilan1 = -1;
            $sinhVien->khoaluanlan1 = -1;

            $sinhVien->thuchanhlan2 = -1;
            $sinhVien->lythuyetlan2 = -1;
            $sinhVien->chinhtrilan2 = -1;
            $sinhVien->khoaluanlan2 = -1;

            $sinhVien->thuchanhlan3 = -1;
            $sinhVien->lythuyetlan3 = -1;
            $sinhVien->chinhtrilan3 = -1;
            $sinhVien->khoaluanlan3 = -1;

            foreach ($diemdotthi as $ddt) {
                $sinhVien->svd_ghichu = $ddt->svd_ghichu;
                $sinhVien->svd_loai = $ddt->svd_loai;
                $sinhVien->svd_khongdatloai = $ddt->svd_khongdatloai;

                if ($ddt->dt_id == $dt_id) {
                    $sinhVien->svd_dieukien = $ddt->svd_dieukien;
                }
                if ($ddt->svd_lan == 1) {
                    if ($ddt->mh_loai == 2) {
                        $sinhVien->chinhtrilan1 = $ddt->svd_first;
                    } else if ($ddt->mh_loai == 3) {
                        $sinhVien->thuchanhlan1 = $ddt->svd_first;
                    } else if ($ddt->mh_loai == 4) {
                        $sinhVien->lythuyetlan1 = $ddt->svd_first;
                    } else if ($ddt->mh_loai == 5) {
                        $sinhVien->khoaluanlan1 = $ddt->svd_first;
                    }
                } else if ($ddt->svd_lan == 2) {
                    if ($ddt->mh_loai == 2) {
                        $sinhVien->chinhtrilan2 = $ddt->svd_first;
                    } else if ($ddt->mh_loai == 3) {
                        $sinhVien->thuchanhlan2 = $ddt->svd_first;
                    } else if ($ddt->mh_loai == 4) {
                        $sinhVien->lythuyetlan2 = $ddt->svd_first;
                    } else if ($ddt->mh_loai == 5) {
                        $sinhVien->khoaluanlan2 = $ddt->svd_first;
                    }
                } else if ($ddt->svd_lan == 3) {
                    if ($ddt->mh_loai == 2) {
                        $sinhVien->chinhtrilan3 = $ddt->svd_first;
                    } else if ($ddt->mh_loai == 3) {
                        $sinhVien->thuchanhlan3 = $ddt->svd_first;
                    } else if ($ddt->mh_loai == 4) {
                        $sinhVien->lythuyetlan3 = $ddt->svd_first;
                    } else if ($ddt->mh_loai == 5) {
                        $sinhVien->khoaluanlan3 = $ddt->svd_first;
                    }
                }
            }

            $diemchinhtri = $sinhVien->chinhtrilan1;
            $diemlythuyet = $sinhVien->lythuyetlan1;
            $diemthuchanh = $sinhVien->thuchanhlan1;
            $diemkhoaluan = $sinhVien->khoaluanlan1;


            if ($sinhVien->khoaluanlan2 != -1) {
                $diemkhoaluan = $sinhVien->khoaluanlan2;
            } else if ($sinhVien->khoaluanlan3 != -1) {
                $diemkhoaluan = $sinhVien->khoaluanlan3;
            }

            if ($sinhVien->thuchanhlan2 != -1) {
                $diemthuchanh = $sinhVien->thuchanhlan2;
            } else if ($sinhVien->thuchanhlan3 != -1) {
                $diemthuchanh = $sinhVien->thuchanhlan3;
            }

            if ($sinhVien->lythuyetlan2 != -1) {
                $diemlythuyet = $sinhVien->lythuyetlan2;
            } else if ($sinhVien->lythuyetlan3 != -1) {
                $diemlythuyet = $sinhVien->lythuyetlan3;
            }

            if ($sinhVien->lythuyetlan2 != -1) {
                $diemlythuyet = $sinhVien->lythuyetlan2;
            } else if ($sinhVien->lythuyetlan3 != -1) {
                $diemlythuyet = $sinhVien->lythuyetlan3;
            }

            $sinhVien->toanKhoa->ghiChuThiLai = "";

            if ($diemchinhtri < 5 && $diemchinhtri != -1 && $diemkhoaluan == -1) {
                $sinhVien->toanKhoa->ghiChuThiLai .= 'ĐTNCT(L1:' . number_format($sinhVien->chinhtrilan1, 1);
                if ($sinhVien->chinhtrilan2 != -1) {
                    $sinhVien->toanKhoa->ghiChuThiLai .= ', L2:' . number_format($sinhVien->chinhtrilan2, 1);
                }
                if ($sinhVien->chinhtrilan3 != -1) {
                    $sinhVien->toanKhoa->ghiChuThiLai .= ', L3:' . number_format($sinhVien->chinhtrilan3, 1);
                }
                $sinhVien->toanKhoa->ghiChuThiLai .= '), ';
            }

            if ($diemlythuyet < 5 && $diemkhoaluan == -1) {
                $sinhVien->toanKhoa->ghiChuThiLai .= 'ĐTNLT(L1:' . number_format($sinhVien->lythuyetlan1, 1);
                if ($sinhVien->lythuyetlan2 != -1) {
                    $sinhVien->toanKhoa->ghiChuThiLai .= ', L2:' . number_format($sinhVien->lythuyetlan2, 1);
                }
                if ($sinhVien->lythuyetlan3 != -1) {
                    $sinhVien->toanKhoa->ghiChuThiLai .= ', L3:' . number_format($sinhVien->lythuyetlan3, 1);
                }
                $sinhVien->toanKhoa->ghiChuThiLai .= '), ';
            }

            if ($diemthuchanh < 5 && $diemkhoaluan == -1) {
                $sinhVien->toanKhoa->ghiChuThiLai .= 'ĐTNTH(L1:' . number_format($sinhVien->thuchanhlan1, 1);
                if ($sinhVien->thuchanhlan2 != -1) {
                    $sinhVien->toanKhoa->ghiChuThiLai .= ', L2:' . number_format($sinhVien->thuchanhlan2, 1);
                }
                if ($sinhVien->thuchanhlan3 != -1) {
                    $sinhVien->toanKhoa->ghiChuThiLai .= ', L3:' . number_format($sinhVien->thuchanhlan3, 1);
                }
                $sinhVien->toanKhoa->ghiChuThiLai .= '), ';
            }

            if ($diemkhoaluan < 5 && $diemkhoaluan != -1) {
                $sinhVien->toanKhoa->ghiChuThiLai .= 'ĐTNCD(L1:' . number_format($sinhVien->khoaluanlan1, 1);
                if ($sinhVien->khoaluanlan2 != -1) {
                    $sinhVien->toanKhoa->ghiChuThiLai .= ', L2:' . number_format($sinhVien->khoaluanlan2, 1);
                }
                if ($sinhVien->khoaluanlan3 != -1) {
                    $sinhVien->toanKhoa->ghiChuThiLai .= ', L3:' . number_format($sinhVien->khoaluanlan3, 1);
                }
                $sinhVien->toanKhoa->ghiChuThiLai .= '), ';
            }

            if (isset($sinhVien->toanKhoa->avg)) {
                if ($diemkhoaluan != -1) {
                    $sinhVien->toanKhoa->avg_totnghiep = number_format((($sinhVien->toanKhoa->avg * 3) + ($diemkhoaluan * 2)) / 5, 1);
                } else {
                    $sinhVien->toanKhoa->avg_totnghiep = number_format(($diemlythuyet + ($diemthuchanh * 2) + ($sinhVien->toanKhoa->avg * 3)) / 6, 1);
                }
                $sinhVien->toanKhoa->hocLucTN = $this->getHocLucByDiem($sinhVien->toanKhoa->avg_totnghiep, $quyChe2022);
            }
        }

        return $data;
    }

    public function getKetQuaHocTapDatTN($semester, $lhId, $svId = 0)
    {
        $reqSemester = $semester ?: 1;
        $reqYear = 1;
        $semesters = [$reqSemester];
        if ($reqSemester <= 2) {
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
        } else if ($reqSemester == 123456) {
            $reqYear = 2;
            $semesters = [1, 2, 3, 4, 5, 6];
        } else {
            abort(404);
        }

        $lopHoc = LopHoc::find($lhId);
        $quyChe2022 = $lopHoc->lh_nienche == 1;
        $khoaDaoTao = $lopHoc->khoaDaoTao;
        if (!$lopHoc) {
            abort(404);
        }
        $danhSachSinhVien = $lopHoc->sinhVien();
        if ($svId) {
            $danhSachSinhVien->where('qlsv_sinhvien.sv_id', $svId);
        }
        $danhSachSinhVien = $danhSachSinhVien->orderBy('sv_ma', 'asc')->get();

        $danhSachSinhVien = $danhSachSinhVien->filter(function ($sinhVien) use ($semesters) {
            $passXoaTen = false;
            $passTotNghiep = false;
            if (!$sinhVien->quyetDinhXoaTen()->exists() || $sinhVien->quyetDinhXoaTen->first()->pivot->qd_hocky >= $semesters[0]) {
                $passXoaTen = true;
            }

            if (!$sinhVien->quyetDinhTotNghiep()->exists() || $sinhVien->quyetDinhTotNghiep->first()->pivot->qd_hocky >= $semesters[0]) {
                $passTotNghiep = true;
            }
            return $passXoaTen && $passTotNghiep;
        })->values()->all();

        $danhSachSinhVien = collect($danhSachSinhVien);

        $danhSachMonHoc = DB::table('qlsv_lophoc as lh')
            ->join('qlsv_khoadaotao_monhoc as kdt', function ($join) use ($semesters) {
                $join->on('kdt.kdt_id', '=', 'lh.kdt_id')
                    ->whereIn('kdt.kdt_mh_hocky', $semesters);
            })
            ->join('qlsv_monhoc as mh', 'mh.mh_id', '=', 'kdt.mh_id')
            ->where('lh.lh_id', $lhId)
            ->whereRaw('mh.mh_loai = 1')
            ->select('mh.mh_id', 'mh.mh_ma', 'mh.mh_ten', 'mh.mh_sodonvihoctrinh', 'mh.mh_tichluy', 'kdt.kdt_mh_hocky', 'mh.mh_loai')
            ->orderBy('kdt.kdt_mh_hocky', 'asc')
            ->orderBy('kdt.kdt_mh_index', 'asc')
            ->get();

        $bangDiem = DB::table('qlsv_bangdiem as bd')
            ->join('qlsv_sinhvien_diem as svd', 'svd.bd_id', '=', 'bd.bd_id')
            ->whereIn('bd.kdt_hocky', $semesters)
            ->where('bd.lh_id', $lhId)
            ->whereRaw("(svd.sv_id = $svId OR $svId = 0)")
            ->where('bd.bd_type', BangDiemType::BANGDIEM_MONHOC)
            ->select(
                'svd.sv_id',
                'bd.mh_id',
                'svd.svd_dulop',
                'svd.svd_first',
                'svd.svd_second',
                'svd.svd_second_hocky',
                'svd.svd_third',
                'svd.svd_third_hocky',
                'svd.svd_final',
                'bd.kdt_hocky',
                'svd.svd_ghichu',
                'svd.svd_exam_first',
                'svd.svd_exam_second',
                'svd.svd_exam_third'
            )
            ->get();

        $bangDiemRenLuyen = DB::table('qlsv_bangdiem as bd')
            ->join('qlsv_sinhvien_diem as svd', 'svd.bd_id', '=', 'bd.bd_id')
            ->whereIn('bd.kdt_hocky', $semesters)
            ->where('bd.lh_id', $lhId)
            ->whereRaw("(svd.sv_id = $svId OR $svId = 0)")
            ->where('bd.bd_type', BangDiemType::BANGDIEM_HOCKY)
            ->select('svd.sv_id', 'svd.svd_final', 'bd.kdt_hocky')
            ->get();

        foreach ($danhSachSinhVien as $sinhVien) {
            $svId = $sinhVien->sv_id;
            $sinhVien->semesters = collect([]);
            $notes = collect();
            foreach ($semesters as $seIndex => $hocKy) {
                $semester = new \stdClass;
                $semester->number = $semesters[$seIndex];
                $semester->monHoc = collect();
                $semester->notes = collect();

                $diemRenLuyen = $bangDiemRenLuyen->first(function ($svd) use ($svId, $hocKy) {
                    return $svd->sv_id == $svId && $svd->kdt_hocky == $hocKy;
                });

                foreach ($danhSachMonHoc as $monHoc) {
                    if ($monHoc->kdt_mh_hocky == $hocKy) {
                        $temp = clone $monHoc;
                        $functionThemGhi = function ($type, $diem) use ($semester, $monHoc) {
                            $semester->notes->push([
                                'type' => $type,
                                'key' => $monHoc->mh_ma,
                                'value' => $diem
                            ]);
                        };
                        $ketQua = $bangDiem->first(function ($svd) use ($semester, $svId, $temp, $hocKy) {
                            return ($svd->sv_id == $svId && $svd->mh_id == $temp->mh_id && $svd->kdt_hocky == $hocKy);
                        });
                        $temp->ketQua = $ketQua;
                        if (isset($temp->ketQua)) {
                            if ($temp->ketQua->svd_first) {
                                $temp->ketQua->svd_first = number_format($temp->ketQua->svd_first, 1);
                            }

                            // Lan 1
                            $coThilan1 = isset($temp->ketQua->svd_exam_first);
                            $coDiemTongKetLan1 = isset($temp->ketQua->svd_first);
                            $datDiemThiLan1 = $coThilan1 && $temp->ketQua->svd_exam_first >= 5;
                            $datDiemTongKetLan1 = $coDiemTongKetLan1 && $temp->ketQua->svd_first >= 5;

                            // Lan 2
                            $coThilan2 = isset($temp->ketQua->svd_exam_second);
                            $coDiemTongKetLan2 = isset($temp->ketQua->svd_second);
                            $datDiemThiLan2 = $coThilan2 && $temp->ketQua->svd_exam_second >= 5;
                            $datDiemTongKetLan2 = $coDiemTongKetLan2 && $temp->ketQua->svd_second >= 5;
                            // Lan 3
                            $coThilan3 = isset($temp->ketQua->svd_exam_third);
                            $coDiemTongKetLan3 = isset($temp->ketQua->svd_third);
                            $datDiemThiLan3 = $coThilan3 && $temp->ketQua->svd_exam_third >= 5;
                            $datDiemTongKetLan3 = $coDiemTongKetLan3 && $temp->ketQua->svd_third >= 5;
                            $hocKyXem = $hocKy;
                            if ($reqSemester <= 6) {
                                // Không thay đổi giả trị
                            } else {
                                $hocKyXem = end($semesters);
                            }

                            // Mục tiêu 1: Tìm điểm hiển thị: hiển thị điểm lần 1, lần 2 hay lần 3?
                            if ($coDiemTongKetLan3 && $temp->ketQua->svd_third_hocky <= $hocKyXem) {
                                // Điểm lần 3, ưu tiên check trước
                                // Các học kỳ trước => hiển thị điểm cải thiện học kỳ trước
                                $temp->ketQua->display_score = $temp->ketQua->svd_third;
                                $temp->ketQua->exam_core = $temp->ketQua->svd_exam_third;
                            } else if ($coDiemTongKetLan2 && $temp->ketQua->svd_second_hocky <= $hocKyXem) {
                                // Điểm lần 2
                                // Các học kỳ trước => hiển thị điểm cải thiện học kỳ trước
                                $temp->ketQua->display_score = $temp->ketQua->svd_second;
                                $temp->ketQua->exam_core = $temp->ketQua->svd_exam_second;
                            } else {
                                // Mặc định hiển thị điểm lần 1
                                $temp->ketQua->display_score = $temp->ketQua->svd_first;
                                $temp->ketQua->exam_core = $temp->ketQua->svd_exam_first;
                            }

                            // Mục tiêu 2: xác định tô đỏ
                            $temp->ketQua->passed = true;
                            if ($quyChe2022) {
                                if ($temp->ketQua->display_score < 5 || ($temp->ketQua->exam_core != null && $temp->ketQua->exam_core < 5)) {
                                    $temp->ketQua->passed = false;
                                }
                            } else {
                                if ($temp->ketQua->display_score < 5) {
                                    $temp->ketQua->passed = false;
                                }
                            }

                            // Mục tiêu 3: Xác định ghi chú
                            $skipNoteHL = false;
                            $skipNoteTL = false;

                            if (isset($temp->ketQua) && $temp->ketQua->svd_ghichu == 'Học lại') {
                                $semester->notes->push([
                                    'type' => 'HL',
                                    'key' => $monHoc->mh_ma
                                ]);
                                $skipNoteHL = true;
                            } else if (isset($temp->ketQua) && $temp->ketQua->svd_ghichu == 'Thi lại') {
                                $semester->notes->push([
                                    'type' => 'TL',
                                    'key' => $monHoc->mh_ma
                                ]);
                                $skipNoteTL = true;
                            }

                            if ($coDiemTongKetLan3) {
                                // Lấy điểm lần 3 thì:
                                // Thêm ghi chú lần 3
                                // if ($coDiemTongKetLan2) {
                                //     $functionThemGhi('DL2', $temp->ketQua->svd_second);
                                // }
                                if ($quyChe2022 && $coThilan3) {
                                    // Điểm thi không đạt
                                    if (!$datDiemThiLan3) {
                                        if (!$skipNoteTL && !$skipNoteHL) {
                                            $semester->notes->push([
                                                'type' => 'TL',
                                                'key' => $monHoc->mh_ma
                                            ]);
                                        }

                                        // Hiển thị điểm thi lại
                                        if ($temp->ketQua->svd_exam_third > 0 && !$skipNoteHL) {
                                            $functionThemGhi('DTL3', $temp->ketQua->svd_exam_third);
                                        }
                                    }
                                }
                                //$temp->ketQua->svd_second_hocky == $hocKyXem
                            } else if ($coDiemTongKetLan2) {
                                // Lấy điểm lần 2 thì:
                                // Thêm ghi chú lần 2
                                // $functionThemGhi('DL1', $temp->ketQua->svd_first);
                                if ($quyChe2022 && $coThilan2) {
                                    // Điểm thi không đạt
                                    if (!$datDiemThiLan2) {
                                        if (!$skipNoteTL && !$skipNoteHL) {
                                            $semester->notes->push([
                                                'type' => 'TL',
                                                'key' => $monHoc->mh_ma
                                            ]);
                                        }

                                        // Hiển thị điểm thi lại
                                        if ($temp->ketQua->svd_exam_second > 0 && !$skipNoteHL) {
                                            $functionThemGhi('DTL2', $temp->ketQua->svd_exam_second);
                                        }
                                    }
                                }
                            } else if ($coDiemTongKetLan1) {
                                // Lấy điểm lần 1 thì:
                                // Thêm ghi chú lần 1
                                // Không ghi chú

                                // Điểm thi không đạt
                                if ($quyChe2022 && $coThilan1) {
                                    if (!$datDiemThiLan1) {
                                        if (!$skipNoteTL && !$skipNoteHL) {
                                            $semester->notes->push([
                                                'type' => 'TL',
                                                'key' => $monHoc->mh_ma
                                            ]);
                                        }

                                        // Hiển thị điểm thi lại
                                        if ($temp->ketQua->svd_exam_first > 0 && !$skipNoteHL) {
                                            $functionThemGhi('DTL1', $temp->ketQua->svd_exam_first);
                                        }
                                    }
                                }
                            }
                        }

                        $semester->monHoc->push($temp);
                    }
                }
                $tinhAvg = $semester->monHoc->every(function ($monHoc) {
                    return isset($monHoc->ketQua);
                });

                if ($diemRenLuyen) {
                    $sumDiem = 0;
                    $sumTinChi = 0;
                    $sumDiemTl = 0;
                    $sumTinChiTl = 0;
                    $semester->monHoc->each(function ($monHoc) use (&$sumDiem, &$sumTinChi, &$sumDiemTl, &$sumTinChiTl) {
                        if ($monHoc->mh_tichluy) {
                            $sumTinChi += $monHoc->mh_sodonvihoctrinh;
                            if ($monHoc->ketQua) {
                                $sumDiem += $monHoc->ketQua->svd_first * $monHoc->mh_sodonvihoctrinh;
                            }
                        }
                    });
                    if ($sumTinChi) {
                        $semester->avg = number_format($sumDiem / $sumTinChi, 1);
                    }

                    $curSemester = $hocKy;
                    $semesterSumTinChi = [];
                    for ($tI = 1; $tI <= $curSemester; $tI++) {
                        $semesterSumTinChi[] = $tI;
                    }
                    $danhSachDiemMonHocTichLuy = $this->getDanhSachDiemMonHocTichLuy($svId, $lopHoc->kdt_id, $lopHoc->lh_id, $semesterSumTinChi);
                    $danhSachDiemMonHocTichLuy = $danhSachDiemMonHocTichLuy->filter(function ($item) use ($hocKy) {
                        // Mặc định lấy điểm lần 1
                        $item->score = $item->svd_first;
                        if ($item->svd_third >= 5 && $item->svd_third_hocky <= $hocKy) {
                            // Nếu là các học kỳ trước => hiển thị điểm cải thiện học kỳ trước
                            $item->score = $item->svd_third;
                        } else if ($item->svd_second >= 5 && $item->svd_second_hocky <= $hocKy) {
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
                    $semester->tinChiTichLuy = $tinChiTichLuy;
                    $semester->tichLuy = $tinChiTichLuy == 0 ? number_format($tinChiTichLuy, 1) : number_format($tichLuy / $tinChiTichLuy, 1);

                    $semester->diemRenLuyen = $diemRenLuyen->svd_final;
                    if ($semester->diemRenLuyen > 0) {
                        $semester->hocLuc = getHocLuc(isset($semester->avg) ? $semester->avg : 0);
                    }
                }

                $sinhVien->semesters->push($semester);
                $notes = $notes->merge($semester->notes);

                if ($semester->number >= $khoaDaoTao->kdt_hocky) {
                    break;
                }
            }

            // Điểm năm
            $chunkSemester = $sinhVien->semesters->chunk(2);
            $years = collect();
            foreach ($chunkSemester as $csIndex => $twoSemester) {
                $year = new \stdClass;
                $curSemester = $twoSemester->last()->number;
                $tinhDiemNam = $twoSemester->every(function ($semester) {
                    return isset($semester->diemRenLuyen);
                });

                if ($tinhDiemNam) {
                    $year->avg = number_format($twoSemester->filter(function ($semester) {
                        return isset($semester->avg);
                    })
                        ->map(function ($semester) {
                            return $semester->avg;
                        })
                        ->avg(), 1);
                }

                // Tính tín chỉ tích lũy năm
                $semesterSumTinChi = [];
                for ($tI = 1; $tI <= $curSemester; $tI++) {
                    $semesterSumTinChi[] = $tI;
                }
                $danhSachDiemMonHocTichLuy = $this->getDanhSachDiemMonHocTichLuy($svId, $lopHoc->kdt_id, $lopHoc->lh_id, $semesterSumTinChi);

                $danhSachDiemMonHocTichLuy = $danhSachDiemMonHocTichLuy->filter(function ($item) use ($twoSemester) {
                    // Mặc định hiển thị điểm lần 1
                    $item->score = $item->svd_first;
                    if ($item->svd_third >= 5 && $item->svd_third_hocky <= $twoSemester->last()->number) {
                        // Nếu ở các học kỳ trước => hiển thị điểm cải thiện học kỳ trước
                        $item->score = $item->svd_third;
                    } else if ($item->svd_second >= 5 && $item->svd_second_hocky <= $twoSemester->last()->number) {
                        // Nếu ở các học kỳ trước => hiển thị điểm cải thiện học kỳ trước
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
                $year->tinChiTichLuy = $tinChiTichLuy;
                $year->tichLuy = $tinChiTichLuy == 0 ? number_format($tinChiTichLuy, 1) : number_format($tichLuy / $tinChiTichLuy, 1);
                // End tính tín chỉ tích lũy năm

                $diemRenLuyen = number_format($twoSemester->filter(function ($semester) {
                    return isset($semester->diemRenLuyen);
                })->map(function ($semester) {
                    return $semester->diemRenLuyen;
                })
                    ->avg(), 0);

                if ($diemRenLuyen) {
                    $year->diemRenLuyen = $diemRenLuyen;

                    $tinhHocLucNam = $twoSemester->every(function ($semester) {
                        return isset($semester->hocLuc);
                    });
                    if ($tinhHocLucNam) {
                        $year->hocLuc = getHocLuc(isset($year->avg) ? $year->avg : 0);
                    }
                }
                $year->semesters = $twoSemester;
                $years->push($year);
            }
            // End điểm năm

            // Điểm toàn khóa
            $toanKhoa = new \stdClass;
            $tinhDiemToanKhoa = $years->every(function ($year) {
                return isset($year->avg);
            });

            if ($tinhDiemToanKhoa) {
                // dd($years);

                $sumDiem = 0;
                $sumTinChi = 0;

                $years->each(function ($year) use (&$sumDiem, &$sumTinChi) {
                    $year->semesters->each(function ($semester) use (&$sumDiem, &$sumTinChi) {
                        $semester->monHoc->each(function ($monHoc) use (&$sumDiem, &$sumTinChi) {
                            if ($monHoc->mh_tichluy) {
                                $sumTinChi += $monHoc->mh_sodonvihoctrinh;
                                if ($monHoc->ketQua) {
                                    $sumDiem += $monHoc->ketQua->svd_first * $monHoc->mh_sodonvihoctrinh;
                                }
                            }
                        });
                    });
                });

                if ($sumTinChi) {
                    $toanKhoa->avg = number_format($sumDiem / $sumTinChi, 1);
                }

                // $toanKhoa->avg = number_format($years->map(function ($year) {
                //         return $year->avg;
                //     })
                //     ->avg(), 1);
            }

            // Tính tín chỉ tích lũy toàn khóa
            $curSemester = collect($semesters)->last();
            $semesterSumTinChi = [];
            for ($tI = 1; $tI <= $curSemester; $tI++) {
                $semesterSumTinChi[] = $tI;
            }
            $danhSachDiemMonHocTichLuy = $this->getDanhSachDiemMonHocTichLuy($svId, $lopHoc->kdt_id, $lopHoc->lh_id, $semesterSumTinChi);
            $danhSachDiemMonHocTichLuy = $danhSachDiemMonHocTichLuy->filter(function ($item) {
                if ($item->svd_third >= 5) {
                    $item->score = $item->svd_third;
                } else if ($item->svd_second >= 5) {
                    $item->score = $item->svd_second;
                } else {
                    $item->score = $item->svd_first;
                }
                return $item->score >= 5;
            });
            $tichLuy = $danhSachDiemMonHocTichLuy->map(function ($item) {
                return $item->score * $item->mh_sodonvihoctrinh;
            })->sum();

            $tinChiTichLuy = $danhSachDiemMonHocTichLuy->map(function ($item) {
                return $item->mh_sodonvihoctrinh;
            })->sum();
            $toanKhoa->tinChiTichLuy = $tinChiTichLuy;
            $toanKhoa->tichLuy = $tinChiTichLuy == 0 ? number_format($tinChiTichLuy, 1) : number_format($tichLuy / $tinChiTichLuy, 1);

            $diemRenLuyen = number_format($years->map(function ($year) {
                return isset($year->diemRenLuyen) ? $year->diemRenLuyen : 0;
            })
                ->avg(), 0);
            if ($diemRenLuyen) {
                $toanKhoa->diemRenLuyen = $diemRenLuyen;

                $tinhHocLucToanKhoa = $years->every(function ($year) {
                    return isset($year->hocLuc);
                });
                if ($tinhHocLucToanKhoa) {
                    $toanKhoa->hocLuc = getHocLuc(isset($toanKhoa->avg) ? $toanKhoa->avg : 0);
                }
            }
            // End điểm toàn khóa

            $sinhVien->years = $years;
            $sinhVien->toanKhoa = $toanKhoa;
            $sinhVien->notes = $notes;
        }




        return [
            'reqSemester' => $reqSemester,
            'reqYear' => $reqYear,
            'lopHoc' => $lopHoc,
            'danhSachSinhVien' => $danhSachSinhVien,

            'semesters' => $semesters,
            'notes' => $notes,
            'sumTinChi' => $sumTinChi,
        ];
    }
}
