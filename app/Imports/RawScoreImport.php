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

class RawScoreImport implements ToCollection, WithEvents
{
    /**
     * Sheet title
     * @var [type]
     * @author ttdat
     * @version 1.0
     */
    protected $sheetTitles;

    public $sheets;

    protected $quyChe2022;

    public function __construct($quyChe2022){
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
        foreach ($collection as $row) {
            // count col
            if (count($row) == 0) {
                continue;
            }
            if (intval($row[0]) == $nextNum || intval($row[0]) > 0) {
                // Đúng điều kiện, bắt đầu lấy dữ liệu
                $nextNum++;
                if (count($row) >= 18 && !empty($row[1])) {
                    $tbkt = null;

                    $filtered[] = [
                        'number' => $row[0],
                        'sv_ma' => trim($row[1]),
                        'sv_ho' => $row[2],
                        'sv_ten' => $row[3],
                        'svd_dulop' => $row[4],
                        'svd_exam_first' => $row[14],
                        'svd_exam_second' => $row[15],
                        'svd_exam_third' => $row[16],

                        'svd_first' => $row[17],
                        'svd_second' => $row[18],
                        'svd_third' => $row[19],
                        'svd_ghichu' => $row[20],
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
                if (count($row) >= 18 && !empty($row[1])) {
                    $tbkt = null;

                    $filtered[] = [
                        'number' => $row[0],
                        'sv_ma' => trim($row[1]),
                        'sv_ho' => $row[2],
                        'sv_ten' => $row[3],
                        'svd_dulop' => $row[4],
                        'svd_first' => $row[16],
                        'svd_second' => $row[17],
                        'svd_ghichu' => $row[18],
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
            BeforeSheet::class => function(BeforeSheet $event) {
                $this->sheetTitles[] = $event->getSheet()->getTitle();
            }
        ];
    }
}
