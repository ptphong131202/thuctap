<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeImport;
use Maatwebsite\Excel\Events\AfterImport;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Events\BeforeSheet;

class DiemDotThiImport implements ToCollection, WithEvents
{
    /**
     * Sheet title
     * @var [type]
     * @author ttdat
     * @version 1.0
     */
    protected $sheetTitles;

    public $sheets;

    public $lh_nienche;

    public function __construct(){
        $this->sheetTitles = [];
        $this->sheets = [];
    }

    public function collection(Collection $collection)
    {
        $filtered = [];
        $nextNum = 1;
        $truocnienche = 0;
        foreach ($collection as $row) {
            if($row[7] == "Ghi chú"){
                $truocnienche = 1;
            }
            // count col
            if (count($row) == 0) {
                continue;
            }
            if (intval($row[0]) == $nextNum || intval($row[0]) > 0) {
                // Đúng điều kiện, bắt đầu lấy dữ liệu
                $nextNum++;
                if ($truocnienche == 1) {
                    $filtered[] = [
                        'number' => $row[0],
                        'sv_ma' => $row[1],
                        'sv_ho' => $row[2],
                        'sv_ten' => $row[3],
                        'chinhtri' => $row[4],
                        'lythuyet' => $row[5],
                        'thuchanh' => $row[6],
                        'ghichu' => $row[7],
                    ];
                } else {
                    $filtered[] = [
                        'number' => $row[0],
                        'sv_ma' => $row[1],
                        'sv_ho' => $row[2],
                        'sv_ten' => $row[3],
                        'lythuyet' => $row[4],
                        'thuchanh' => $row[5],
                        'ghichu' => $row[6],
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

    /**
     * Transform a date value into a Carbon object.
     *
     * @return \Carbon\Carbon|null
     */
    public function transformDate($value, $format = 'd/m/Y')
    {
        try {
            return \Carbon\Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value));
        } catch (\ErrorException $e) {
            return \Carbon\Carbon::createFromFormat($format, $value);
        }
    }
}
