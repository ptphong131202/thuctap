<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;

class ConfigController extends Controller
{
    public function updateConfigInfoHssv($status)
    {
        $configSv = DB::table('qlsv_config')
        ->where('qlsv_config.name' ,'allow-update-info-hssv')
        ->update(['status' => $status]);

        return response()->json($configSv);
    }
}
