<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeImport;
use Maatwebsite\Excel\Events\AfterImport;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Events\BeforeSheet;

class SinhVienImport implements ToCollection, WithEvents 
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
                $birthDate = $row[4] ? $row[4] : $row[5];
                if (count($row) > 7 && $row[1]) {
                    $filtered[] = [
                        'number' => $row[0],
                        'sv_ma' => $row[1],
                        'sv_ho' => $row[2],
                        'sv_ten' => $row[3],
                        'sv_gioitinh' => $row[4] ? 1 : 0,
                        'sv_ngaysinh' => $birthDate ? $birthDate : null,
                        'sv_dantoc' => $row[6],
                        'sv_diachi' => isset($row[7]) ? $row[7] : null,
                        'sv_trinhdo' => isset($row[8]) ? $row[8] : null,
                        'sv_sdt' => isset($row[9]) ? $row[9] : null,
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