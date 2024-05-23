<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeImport;
use Maatwebsite\Excel\Events\AfterImport;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Events\BeforeSheet;

class MonHocImport implements ToCollection, WithEvents
{
    /**
     * Sheet title
     * @var [type]
     * @author ttdat
     * @version 1.0
     */
    protected $sheetTitles;

    public $sheets;

    public function __construct(){
        $this->sheetTitles = [];
        $this->sheets = [];
    }
    
    /**
    * @param Collection $collection
    */
    public function collection(Collection $collection)
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
                if (count($row) >= 6 && $row[1])
                $filtered[] = [
                    'number' => $row[0],
                    'mh_ma' => $row[1],
                    'mh_ten' => $row[2],
                    'mh_sodonvihoctrinh' => $row[3],
                    'mh_sotiet' => $row[4],
                    'mh_tichluy' => $row[5] == 'x'
                ];
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
