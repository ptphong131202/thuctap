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
use App\Models\DotThi;
use App\Models\DotXetTotNghiep;
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

  
    /// T.Phong chỉnh sửa hàm indexCanBo() 
        public function indexCanBo()
    {
        // thông tin cán bộ
        $infoUser = auth()->user()->load(['canBo']);

        // thống kê số lượng
        $thongKe = [
            'slSinhVien'   => SinhVien::count(),
            'slKhoaDaoTao' => KhoaDaoTao::count(),
            'slLopHoc'     => LopHoc::count(),
        ];

        // Thống kê ngành nghề
        $nganhnghe = [
            'slNganhNghe' => NganhNghe::count(),
            'slNganhNghe_TrungCap' => NganhNghe::where('hdt_id', '4')->count(),
            'slNganhNghe_CaoDang' => NganhNghe::where('hdt_id', '5')->count(),
        ];

        // Thống kê đợt xét tốt nghiệp sinh viên
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
            
        // Lấy số lượng lớp học lớn nhất
        $maxlh = $lophocQuery->max('class_count');

        // Tạo mảng lophoc với dữ liệu và tổng số năm
        $lophoc = [
            'data' => $lophocQuery,
            'maxlh' => $maxlh,
        ];

        // chương trình đào tạo
        $khoadaotaotrungcapQuery = KhoaDaoTao::where('hdt_id', 5)
            ->orderBy('kdt_khoa', 'desc') // Sắp xếp theo kdt_khoa giảm dần
            ->take(5) // Giới hạn kết quả trả về là 5
            ->get();
        $khoadaotaocaodangQuery = KhoaDaoTao::where('hdt_id', 4)
            ->orderBy('kdt_khoa', 'desc') // Sắp xếp theo kdt_khoa giảm dần
            ->take(5) // Giới hạn kết quả trả về là 5
            ->get();


        // lấy danh sách đợt thi
        $dotthi = DotThi::selectRaw('dt_tunam as nam, COUNT(dt_tunam) as dotthi_count')
        ->groupBy('dt_tunam')
        ->orderBy('dt_tunam', 'desc')
        ->get();

        $maxnamdotthi = intval($dotthi->max('nam'));
        $maxsldotthi = $dotthi->max('dotthi_count');

        // Tạo mảng chứa số lượng đợt thi của 5 năm từ năm lớn nhất
        $value5nam = [];
        for ($i = 0; $i < 5; $i++) {
            $year = $maxnamdotthi - $i;
            $dotthiForYear = $dotthi->firstWhere('nam', (string)$year);
            $countForYear = $dotthiForYear ? $dotthiForYear->dotthi_count : 0;
            $value5nam[$year] = $countForYear;
        }

        // danh sách đợt thi
        $dsDotThi =  [
            'dotthi' => $dotthi,
            'dotthi_max' => $maxnamdotthi,
            'maxsldotthi' => $maxsldotthi,
            'value5nam' => $value5nam
        ];


        // lấy danh sách đợt xét tốt nghiệp
        $dotxettotnghiep = DotXetTotNghiep::selectRaw('dxtn_tunam as nam, COUNT(dxtn_tunam) as dotxettotnghiep_count')
            ->groupBy('dxtn_tunam')
            ->orderBy('dxtn_tunam', 'desc')
            ->get();

        $maxnamdotxettotnghiep = intval($dotxettotnghiep->max('nam')); // năm xét tốt nghiệp lớn nhất
        $maxsldotxettotnghiep = $dotxettotnghiep->max('dotxettotnghiep_count'); // sl đợt xét tốt nghiệp lớn nhất

        // mảng 5 năm tốt nghiệp
        $value5namtotnghiep = [];
        for ($i = 0; $i < 5; $i++) {
            $year = $maxnamdotxettotnghiep - $i;
            $dotthiForYear = $dotxettotnghiep->firstWhere('nam', (string)$year);
            $countForYear = $dotthiForYear ? $dotthiForYear->dotxettotnghiep_count : 0;
            $value5namtotnghiep[$year] = $countForYear;
        }

        // số lượng tốt nghiệp, đợt thi lớn nhất
        $maxtotnhghiep = max($maxsldotxettotnghiep, $maxsldotthi );

        // sl năm tốt nghiệp, đợt thi lớn nhất
        $maxnamxettotnghiep = max($maxnamdotxettotnghiep, $maxnamdotthi);

        $dsdotxettotnghiep =  [
            'maxsldotxettotnghiep' => $maxsldotxettotnghiep, // số lượng tốt nghiệp lớn nhất
            'maxtotnhghiep' => $maxtotnhghiep, // số lượng tốt nghiệp, đợt thi lớn nhất
            'maxnamxettotnghiep' => $maxnamxettotnghiep, // sl năm tốt nghiệp, đợt thi lớn nhất
            'value5namtotnghiep' => $value5namtotnghiep /// mảng 5 năm tốt nghiệp
        ];


        // lấy danh sách sinh viên
        $danhSachSinhVien = SinhVien::orderBy('sv_ma')
        ->with(['quyetDinhXoaTen', 'quyetDinhTotNghiep', 'quyetDinhThemLop', 'lopHoc', 'user'])
        ->withCount([
            'sinhVienBangDiem' => function ($query) {
                $query->whereNotNull('svd_first');
            },
            'quyetDinhXoaTen as soQuyetDinhXoaTen',  
            'quyetDinhTotNghiep as soQuyetDinhTotNghiep'  
        ])
        ->get();

        // Đếm số sinh viên tốt nghiệp
        $soSinhVienTotNghiep = $danhSachSinhVien->filter(function($sinhVien) {
            return $sinhVien->soQuyetDinhTotNghiep > 0;
        })->count();

        // Đếm số sinh viên xóa tên
        $soSinhVienXoaTen = $danhSachSinhVien->filter(function($sinhVien) {
            return $sinhVien->soQuyetDinhXoaTen > 0;
        })->count();

        return view('dashboard-canbo', compact(['infoUser', 'dsdotxettotnghiep',
                    'thongKe', 'dotxettotnghiepsinhvien', 'dsDotThi', 'soSinhVienTotNghiep',
                    'soSinhVienXoaTen', 'nganhnghe', 'lophoc', 'khoadaotaotrungcapQuery', 
                    'khoadaotaocaodangQuery']));
    }

    

}
