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

class ImportScoreCheck implements ToCollection, WithEvents
{
    /**
     * Sheet title
     * @var [type]
     * @author ttdat
     * @version 1.0
     */
    protected $sheetTitles;

    public $sheets;

    public $targetSheet;

    public function __construct($targetSheet = null){
        $this->targetSheet = $targetSheet;
        $this->sheetTitles = [];
        $this->sheets = [];
    }
    
    public function collection(Collection $collection)
    {
        $sheetTitle = $this->sheetTitles[count($this->sheetTitles) - 1];
        if ($this->targetSheet != null && $this->targetSheet != '' && $this->targetSheet !== $sheetTitle) {
            return;
        }

        $firstRow = -1;

        $filtered = [];
        $nextNum = 1;
        foreach ($collection as $indexRow => $row) {
            // count col
            if (count($row) == 0) {
                continue;
            }
            if (intval($row[0]) == $nextNum || intval($row[0]) > 0) {
                // Đúng điều kiện, bắt đầu lấy dữ liệu
                $nextNum++;
                if (count($row) >= 18 && !empty($row[1])) {
                    if ($firstRow == -1) {
                        $firstRow = $indexRow - 3;
                    }

                    $tbkt = null;

                    $importRow = [
                        'number' => $row[0],
                        'sv_ma' => trim($row[1]),
                        'sv_ho' => $row[2],
                        'sv_ten' => $row[3],
                    ];

                    $data = [];
                    $miss = 0;
                    $i = 4;
                    while ($miss <= 3) {
                        if (!isset($row[$i]) || empty($row[$i])) {
                            $miss++;
                        } else {
                            $miss = 0;
                        }
                        if (!isset($row[$i]) || \Str::startsWith($row[$i], '=')) {
                            // $data[] = null;
                        } else {
                            $data[] = [
                                'index' => $i,
                                'title' => $row[$i],
                            ];
                        }
                        $i++;
                    };

                    $importRow['data'] = $data;

                    $filtered[] = $importRow;
                }
            } else if (intval($row[0]) > 1) {
                // Sai điều kiện - dẹp nghỉ
                break;
            }
        }

        $colTitle = [];
        $missCol = 0;
        if ($firstRow > 0) {
            $row = $collection[$firstRow];
            $i = 4;
            while ($missCol <= 3) {
                if (!isset($row[$i]) || empty($row[$i])) {
                    $missCol++;
                } else {
                    $missCol = 0;
                }
                if (isset($row[$i])) {
                    $colTitle[] = [
                        'index' => $i,
                        'title' => $row[$i],
                    ];
                }
                $i++;
            };
        }

        $this->sheets[] = [
            'number' => count($this->sheetTitles),
            'title' => $sheetTitle,
            'rows' => $filtered,
            'cols' => $colTitle
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
