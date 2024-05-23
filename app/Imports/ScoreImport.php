<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use Maatwebsite\Excel\Events\BeforeImport;
use Maatwebsite\Excel\Events\AfterImport;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Events\BeforeSheet;

class ScoreImport implements ToCollection, WithEvents
{
    /**
     * Sheet title
     * @var [type]
     * @author ttdat
     * @version 1.0
     */
    protected $sheetTitles;

    protected $quyChe2022;

    public $sheets;

    public function __construct($quyChe2022 = false)
    {
        $this->sheetTitles = [];
        $this->sheets = [];
        $this->quyChe2022 = $quyChe2022;
    }

    public function collection(Collection $collection)
    {
        if ($this->quyChe2022) {
            $this->collectionQuyChe2022($collection);
        } else {
            $this->collectionQuyChe2020($collection);
        }
    }

    public function collectionQuyChe2022(Collection $collection)
    {
        $filtered = [];
        $nextNum = 1;
        foreach ($collection as $index => $row) {
            // count col
            if (count($row) == 0) {
                continue;
            }
            // if (intval($row[0]) == $nextNum || intval($row[0]) > 0) {
            if (intval($row[0]) == $nextNum || intval($row[0]) > 0 || $row[0] == "=ROW()-12") {
                // Đúng điều kiện, bắt đầu lấy dữ liệu
                $nextNum++;
                if (count($row) >= 18 && !empty ($row[1])) {
                    $tbkt = null;
                    $svd_first = null;
                    $svd_second = null;
                    $svd_third = null;
                    $svd_ghichu = null;

                    $collectOne = collect([$row[5], $row[6], $row[7], $row[8]])
                        ->filter(function ($item) {
                            return $item == '0' || $item || $item == '0.0';
                        })
                        ->map(function ($item) {
                            return doubleval($item);
                        });
                    $collectTwo = collect([$row[9], $row[10], $row[11], $row[12]])
                        ->filter(function ($item) {
                            return $item == '0' || $item || $item == '0.0';
                        })
                        ->map(function ($item) {
                            return doubleval($item);
                        });

                    $oneCount = $collectOne->count();
                    $twoCount = $collectTwo->count();

                    if ($oneCount && $twoCount) {
                        $sumOne = $collectOne->sum();
                        $sumTwo = $collectTwo->sum();
                        $tbkt = ($sumOne + $sumTwo * 2) / ($oneCount + $twoCount * 2);
                        $tbkt = number_format($tbkt, 1);
                    }
                    if (($tbkt || $tbkt == '0') && !is_null($row[14])) {
                        if (intval($row[4]) >= 80) {
                            // Chuyển đổi $tbkt sang số thực
                            $svd_first = number_format(floatval($tbkt) * 0.4 + floatval($row[14]) * 0.6, 1);
                        } else {
                            $svd_first = "";
                        }
                    }
                    if (($tbkt || $tbkt == '0') && !is_null($row[15])) {
                        // Chỗ này fix cho trường họp sheet không đúng định dạng
                        if (gettype($row[15]) == "string") {
                            break;
                        }
                        if (intval($row[4]) >= 80) {
                            $svd_second = number_format($tbkt * 0.4 + $row[15] * 0.6, 1);
                        } else {
                            $svd_second = "";
                        }
                    }
                    if (($tbkt || $tbkt == '0') && !is_null($row[16]) && is_numeric($row[16])) {
                        if (intval($row[4]) >= 80) {
                            $svd_third = number_format($tbkt * 0.4 + $row[16] * 0.6, 1);
                        } else {
                            $svd_third = "";
                        }
                    }

                    $duLopToiThieu = 92;
                    if (!empty ($row[20]) && str_contains($row[20], 80)) {
                        $duLopToiThieu = 80;
                    }

                    $allEmpty = true;
                    for ($i = 4; $i <= 5; $i++) {
                        if ($i != 13 && ($row[$i] != null || $row[$i] == '0')) {
                            $allEmpty = false;
                            break;
                        }
                    }

                    $datDiemMonHoc = $tbkt >= 5;

                    $coThilan1 = $row[14] != null || $row[14] == "0" || $row[14] == "0.0";
                    $datLan1 = $coThilan1 && $row[14] >= 5;

                    $coThiLan2 = $row[15] != null || $row[15] == "0" || $row[15] == "0.0";
                    $datLan2 = $coThiLan2 && $row[15] >= 5;

                    $coThilan3 = $row[16] != null || $row[16] == "0" || $row[16] == "0.0";
                    $datLan3 = $coThilan3 && $row[16] >= 5;

                    if ($allEmpty) {
                        $svd_ghichu = '';
                    } else if ($duLopToiThieu == 80) {
                        if (intval($row[4]) >= $duLopToiThieu && $datDiemMonHoc && $coThilan1 && !$datLan1) {
                            // Có thi lần 1, đạt điểm lần 1, nhưng điểm môn không đạt
                            $svd_ghichu = 'Thi lại';
                        } else if (intval($row[4]) >= $duLopToiThieu && $datDiemMonHoc && $coThiLan2 && !$datLan2) {
                            // Có thi lần 2, đạt điểm lần 2, nhưng điểm môn không đạt
                            $svd_ghichu = 'Thi lại';
                        } else if (intval($row[4]) >= $duLopToiThieu && $datDiemMonHoc && $coThilan3 && !$datLan3) {
                            // Nếu có thi lần 3 & đạt điểm môn học, tuy nhiên thi lần 3 không đạt thì là học Lại
                            $svd_ghichu = 'Học lại';
                        } else if (($row[4] && intval($row[4]) < $duLopToiThieu) || !$datDiemMonHoc || (intval($row[4]) >= $duLopToiThieu && $datDiemMonHoc && !$datLan1 && !$datLan2 && !$datLan3)) {
                            // Hoc lai
                            $svd_ghichu = 'Học lại';
                        }
                        // if($index == 13) {
                        //     dd($svd_ghichu);
                        // }
                    }

                    $svd_exam_first = null;
                    if ($row[14] != null) {
                        $svd_exam_first = number_format((float) $row[14], 1);
                    }
                    $svd_exam_second = null;
                    if ($row[15] != null) {
                        $svd_exam_second = number_format((float) $row[15], 1);
                    }
                    $svd_exam_third = null;
                    if ($row[16] != null) {
                        $svd_exam_third = number_format((float) $row[16], 1);
                    }


                    $filtered[] = [
                        'number' => $nextNum,
                        'sv_ma' => trim($row[1]),
                        'sv_ho' => $row[2],
                        'sv_ten' => $row[3],
                        'svd_dulop' => $row[4],
                        'svd_first' => $svd_first,
                        'svd_exam_first' => $svd_exam_first,
                        'svd_second' => $svd_second,
                        'svd_exam_second' => $svd_exam_second,
                        'svd_third' => $svd_third,
                        'svd_exam_third' => $svd_exam_third,
                        'svd_ghichu' => $svd_ghichu,
                    ];
                }
            } else if (intval($row[0]) > 1) {
                // Sai điều kiện - dẹp nghỉ
                break;
            }
        }
        $this->sheets[] = [
            'number' => count($this->sheetTitles),
            'title' => $this->sheetTitles[count($this->sheetTitles) - 1],
            'rows' => $filtered
        ];
    }

    public function collectionQuyChe2020(Collection $collection)
    {
        $filtered = [];
        $nextNum = 1;
        foreach ($collection as $row) {
            // count col
            if (count($row) == 0) {
                continue;
            }
            if (intval($row[0]) == $nextNum || intval($row[0]) > 0) {
                // Đúng điều kiện, bắt đầu lấy dữ liệu
                $nextNum++;
                if (count($row) >= 18 && !empty ($row[1])) {
                    $tbkt = null;
                    $svd_first = null;
                    $svd_second = null;
                    $svd_ghichu = null;

                    $collectOne = collect([$row[5], $row[6], $row[7], $row[8]])
                        ->filter(function ($item) {
                            return $item == '0' || $item || $item == '0.0';
                        })
                        ->map(function ($item) {
                            return doubleval($item);
                        });
                    $collectTwo = collect([$row[9], $row[10], $row[11], $row[12]])
                        ->filter(function ($item) {
                            return $item == '0' || $item || $item == '0.0';
                        })
                        ->map(function ($item) {
                            return doubleval($item);
                        });

                    $oneCount = $collectOne->count();
                    $twoCount = $collectTwo->count();

                    if ($oneCount && $twoCount) {
                        $sumOne = $collectOne->sum();
                        $sumTwo = $collectTwo->sum();
                        $tbkt = ($sumOne + $sumTwo * 2) / ($oneCount + $twoCount * 2);
                        $tbkt = number_format($tbkt, 1);
                    }
                    if (($tbkt || $tbkt == '0') && !is_null($row[14]) && (gettype($row[14]) != 'string')) {
                        $svd_first = number_format($tbkt * 0.4 + $row[14] * 0.6, 1);
                    }
                    if (($tbkt || $tbkt == '0') && !is_null($row[15]) && (gettype($row[15]) != 'string')) {
                        $svd_second = number_format($tbkt * 0.4 + $row[15] * 0.6, 1);
                    }

                    $duLopToiThieu = 70;
                    // if (!empty($row[18]) && str_contains($row[18], 70)) {
                    //     $duLopToiThieu = 70;
                    // }


                    $allEmpty = true;
                    for ($i = 4; $i <= 5; $i++) {
                        if ($i != 13 && ($row[$i] != null || $row[$i] == '0')) {
                            $allEmpty = false;
                            break;
                        }
                    }
                    if ($allEmpty) {
                        $svd_ghichu = '';
                    } else if ($duLopToiThieu == 70) {
                        if ($svd_first != null && $tbkt >= 5 && $svd_first < 5 && !$svd_second && intval($row[4]) >= $duLopToiThieu) {
                            // thi lai
                            $svd_ghichu = 'Thi lại';
                        } else if (($row[4] && intval($row[4]) < $duLopToiThieu) || ($tbkt < 5) || ($svd_first < 5 && $svd_second < 5) || ($svd_first >= 5 && !empty ($svd_second) && $svd_second < 5)) {
                            // Hoc lai
                            $svd_ghichu = 'Học lại';
                        }
                    }

                    $filtered[] = [
                        'number' => $row[0],
                        'sv_ma' => trim($row[1]),
                        'sv_ho' => $row[2],
                        'sv_ten' => $row[3],
                        'svd_dulop' => $row[4],
                        'svd_first' => $svd_first,
                        'svd_second' => $svd_second,
                        'svd_ghichu' => $svd_ghichu,
                    ];
                }
            } else if (intval($row[0]) > 1) {
                // Sai điều kiện - dẹp nghỉ
                break;
            }
        }
        $this->sheets[] = [
            'number' => count($this->sheetTitles),
            'title' => $this->sheetTitles[count($this->sheetTitles) - 1],
            'rows' => $filtered
        ];
    }

    public function registerEvents(): array
    {
        return [
            BeforeSheet::class => function (BeforeSheet $event) {
                $this->sheetTitles[] = $event->getSheet()->getTitle();
            }
        ];
    }
}
