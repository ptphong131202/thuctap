<?php
namespace App\Traits;

use Illuminate\Support\Str;

trait ExcelCursor
{
    private $excelCol = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'];

    public function nextCols($col, $next = 1)
    {
        $nextCol = $this->nextCol($col);
        for ($i = 1; $i < $next; $i++) {
            $nextCol = $this->nextCol($nextCol);
        }
        return $nextCol;
    }

    public function nextCol($col)
    {
        $index = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($col) + 1;
        return \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($index);
    }

    public function nextRowAddress($address, $resetCol = false)
    {
        $row = preg_replace('/[^0-9]/', '', $address);
        if ($resetCol) {
            return 'A' . ($row + 1);
        }
        $col = preg_replace('/[^A-Z]/', '', Str::upper($address));
        return $col . ($row + 1);
    }

    public function nextRowsAddress($address, $numrow, $resetCol = false)
    {
        for ($i = 1; $i <= $numrow; $i++) {
            $address = $this->nextRowAddress($address, $resetCol);
        }
        return $address;
    }

    public function nextColAddress($address)
    {
        $row = preg_replace('/[^0-9]/', '', $address);
        $col = preg_replace('/[^A-Z]/', '', Str::upper($address));
        return $this->nextCol($col) . $row;
    }

    public function nextColsAddress($address, $numcol)
    {
        for ($i = 1; $i <= $numcol; $i++) {
            $address = $this->nextColAddress($address);
        }
        return $address;
    }

    public function currentRow($address)
    {
        return preg_replace('/[^0-9]/', '', $address);;
    }

    public function currentCol($address)
    {
        return preg_replace('/[^A-Z]/', '', Str::upper($address));
    }
}