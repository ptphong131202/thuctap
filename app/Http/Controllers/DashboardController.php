<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LopHoc;
use App\Models\SinhVien;
use App\Models\KhoaDaoTao;
use App\Models\Log;
use App\Models\MonHoc;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        if (auth()->user()->canBo()->exists()) {
            return $this->indexCanBo();
        } else {
            return $this->indexSinhVien();
        }
    }

    public function indexSinhVien()
    {
        $configSv = DB::table('qlsv_config')
            ->where('qlsv_config.name', 'allow-update-info-hssv')
            ->select('status')
            ->first();

        $configSv = $configSv->status;

        $infoUser = auth()->user()->load(['sinhVien', 'sinhVien.lopHoc']);
        $sv_id = $infoUser->sinhVien->sv_id;
        // Ghi log sinh viên vào xem điểm
        if (request()->hasCookie('XSRF-TOKEN')) {
            $sinhVienLog = Log::where('sv_id', $sv_id);
            $token = request()->cookie('XSRF-TOKEN');
            $model = new Log;

            if ($sinhVienLog->get()->count() > 0) {
                if ($sinhVienLog->latest()->first()->token != $token) {
                    $model->fill([
                        'sv_id' => $sv_id,
                        'type' => 1,
                        'status' => 1,
                        'token' => $token
                    ]);
                    $model->save();
                }
            } else {
                $model->fill([
                    'sv_id' => $sv_id,
                    'type' => 1,
                    'status' => 1,
                    'token' => $token
                ]);
                $model->save();
            }
        }

        return view('dashboard', compact(['infoUser', 'configSv']));
    }

    public function indexCanBo()
    {
        $infoUser = auth()->user()->load(['canBo']);
        $thongKe = [
            'slSinhVien'   => SinhVien::count(),
            'slMonHoc'     => MonHoc::count(),
            'slKhoaDaoTao' => KhoaDaoTao::count(),
            'slLopHoc'     => LopHoc::count(),
        ];
        return view('dashboard-canbo', compact(['infoUser', 'thongKe']));
    }
}
