<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Imports\ScoreImport;
use App\Imports\RawScoreImport;
use App\Imports\SinhVienImport;
use App\Imports\MonHocImport;
use App\Imports\DiemRenLuyenImport;
use App\Imports\DiemDotThiImport;
use App\Imports\ImportScoreCheck;
use Excel;

class ImportExcelController extends Controller
{
    public function importScoreCheck(Request $request)
    {
        ini_set('memory_limit', '-1');
        $importer = new ImportScoreCheck($request->sheet);
        Excel::import($importer, $request->file('excel_file'));
        return response()->json($importer->sheets, 200, array(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE );
    }

    public function importScore(Request $request)
    {
        ini_set('memory_limit', '-1');
        $quyChe2022 = $request->quy_che_2022 == 'true' ? 1 : 0;
        $importer = new ScoreImport($quyChe2022);
        Excel::import($importer, $request->file('excel_file'));
        // Excel::import($importer, storage_path('app/public/excel/2.BD.xls'));
        return response()->json($importer->sheets, 200, array(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE );
    }

    public function importRawScore(Request $request)
    {
        ini_set('memory_limit', '-1');
        $quyChe2022 = $request->quy_che_2022 == 'true' ? 1 : 0;
        $importer = new RawScoreImport($quyChe2022);
        Excel::import($importer, $request->file('excel_file'));
        // Excel::import($importer, storage_path('app/public/excel/4.RDB.xls'));
        return response()->json($importer->sheets, 200, array(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE );
    }

    public function importRenLuyenScore(Request $request)
    {
        ini_set('memory_limit', '-1');
        $importer = new DiemRenLuyenImport;
        Excel::import($importer, $request->file('excel_file'));
        return response()->json($importer->sheets, 200, array(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE );
    }

    public function importDiemDotThi(Request $request)
    {
        ini_set('memory_limit', '-1');
        $importer = new DiemDotThiImport;
        $importer->lh_nienche = $request->lh_nienche;
        Excel::import($importer, $request->file('excel_file'));

        // Excel::import($importer, storage_path('app/public/excel/5.import-sinh-vien-sample.xls'));

        return response()->json($importer->sheets, 200, array(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE );
    }


    public function importDiemDotThiTheoMon(Request $request)
    {
        ini_set('memory_limit', '-1');
        $importer = new DiemRenLuyenImport;
        Excel::import($importer, $request->file('excel_file'));
        return response()->json($importer->sheets, 200, array(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE );
    }

    public function importUser(Request $request)
    {
        ini_set('memory_limit', '-1');
        $importer = new SinhVienImport;
        Excel::import($importer, $request->file('excel_file'));

        // Excel::import($importer, storage_path('app/public/excel/5.import-sinh-vien-sample.xls'));

        return response()->json($importer->sheets, 200, array(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE );
    }

    public function importMonHoc(Request $request)
    {
        ini_set('memory_limit', '-1');
        $importer = new MonHocImport;
        Excel::import($importer, $request->file('excel_file'));
        //Excel::import($importer, storage_path('app/public/excel/7.import-mon-hoc-2.xlsx'));
        return response()->json($importer->sheets, 200, array(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE );
    }

    public function sample($filename)
    {
        ini_set('memory_limit', '-1');
        $file = storage_path('app/sample/' . $filename);
        if (file_exists($file)) {
            return response()->download($file);
        }
        return 'File not exists';
    }
}
