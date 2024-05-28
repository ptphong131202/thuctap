<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LopHoc;
use App\Models\SinhVien;
use App\Models\KhoaDaoTao;
use App\Models\HeDaoTao;
use App\Models\Log;
use App\Models\MonHoc;
use App\Models\DotXetTotNghiepSinhVien;
use App\Models\NganhNghe;
use App\Models\NienKhoa;
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

  
    /// 

        public function indexCanBo()
    {
        $infoUser = auth()->user()->load(['canBo']);
        $thongKe = [
            'slSinhVien'   => SinhVien::count(),
            'slMonHoc'     => MonHoc::count(),
            'slKhoaDaoTao' => KhoaDaoTao::count(),
            'slLopHoc'     => LopHoc::count(),
            'slHeDaoTao'   => HeDaoTao::count(),
        ];

        $nganhnghe = [
            'slNganhNghe' => NganhNghe::count(),
            'slNganhNghe_TrungCap' => NganhNghe::where('hdt_id', '4')->count(),
            'slNganhNghe_CaoDang' => NganhNghe::where('hdt_id', '5')->count(),
        ];

        $dotxettotnghiepsinhvien = [
            'sldotxetnghiepsinh' => DotXetTotNghiepSinhVien::count(),
            'sldatotnghiep' => DotXetTotNghiepSinhVien::where('svxtn_dattn', '1')->count(),
            'slchuaxet' => DotXetTotNghiepSinhVien::where('svxtn_dattn', '1')->count(),
        ];
        
        // Truy vấn số lượng lớp học theo từng năm
        $lophocQuery = NienKhoa::selectRaw('qlsv_nienkhoa.nk_ten as ten, COUNT(qlsv_lophoc.nk_id) AS class_count')
        ->join('qlsv_lophoc', 'qlsv_lophoc.nk_id', '=', 'qlsv_nienkhoa.nk_id')
        ->groupBy('qlsv_nienkhoa.nk_ten')
        ->orderBy('qlsv_nienkhoa.nk_id', 'desc')
        ->limit(5)
        ->get();

    
        $totallophocQuery = $lophocQuery->count();

        // Lấy số lượng lớp học lớn nhất
        $maxlh = $lophocQuery->max('class_count');

        // Tạo mảng lophoc với dữ liệu và tổng số năm
        $lophoc = [
            'data' => $lophocQuery,
            'maxlh' => $maxlh,
            'totallophocQuery'=> $totallophocQuery
        ];

        // Lấy danh sách hệ đào tạo
        $dsHeDaoTao = HeDaoTao::all();
        return view('dashboard-canbo', compact(['infoUser', 'thongKe', 'dotxettotnghiepsinhvien', 'nganhnghe', 'lophoc']));
    }

    

}


/*
SELECT sv.*
FROM qlsv_lophoc AS lh
JOIN qlsv_lophoc_monhoc AS lh_mh ON lh.lh_id = lh_mh.lh_id
JOIN qlsv_sinhvien_lophoc AS lhs ON lh.lh_id = lhs.lh_id
JOIN qlsv_sinhvien AS sv ON lhs.sv_id = sv.sv_id
WHERE lh.lh_ma = 'C-NTS15A' 
  AND lh_mh.mh_id = 1518
  AND sv.sv_sdt IS NOT NULL;

*/