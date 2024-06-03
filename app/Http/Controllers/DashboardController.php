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
        
        // Lấy danh sách niên khóa
        $NienKhoas = NienKhoa::all();

        // Tạo một mảng để lưu số lượng lớp học theo từng niên khóa
        $soLuongLopHocTheoNienKhoa = [];

        // Lặp qua từng niên khóa
        foreach ($NienKhoas as $nienKhoa) {
            // Đếm số lượng lớp học thuộc niên khóa hiện tại
            $soLuongLopHoc = $nienKhoa->lopHoc()->count();
            
            // Lưu số lượng lớp học vào mảng kết quả với tên niên khóa làm key
            $soLuongLopHocTheoNienKhoa[$nienKhoa->nk_ten] = $soLuongLopHoc;
        }

        
        $lophocs = [];
        foreach ($soLuongLopHocTheoNienKhoa as $tenNienKhoa => $soLuongLopHoc) {
            if ($soLuongLopHoc > 0) {
                $lophocs[] = [
                    'nk_ten' => $tenNienKhoa,
                    'sllop' => $soLuongLopHoc
                ];
            }
        }
        
        // Lấy 5 phần tử cuối cùng từ mảng $lophocs
        $lophocs = array_slice($lophocs, -5);
        $maxLopHoc = max(array_column($lophocs, 'sllop'));

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

        

        $lopHocs = LopHoc::with(['sinhVien', 'sinhVien.quyetDinhTotNghiep', 'sinhVien.quyetDinhXoaTen'])->get();

        // Tạo mảng tạm để ánh xạ nk_id thành nk_ten
        $nienKhoaMap = [];
        foreach ($NienKhoas as $nienKhoa) {
            $nienKhoaMap[$nienKhoa->nk_id] = $nienKhoa->nk_ten;
        }

        // Tạo mảng kết quả
        $kqnienkhoa = [];

        // Lặp qua từng lớp học
        foreach ($lopHocs as $lopHoc) {
            $nk_id = $lopHoc->nk_id;
            $lop_ma = $lopHoc->lh_ma;

            // Lấy nk_ten từ map
            $nk_ten = $nienKhoaMap[$nk_id];

            // Nếu nk_ten chưa tồn tại trong mảng, khởi tạo nó
            if (!isset($danhsachlophocsinhvien[$nk_ten])) {
                $danhsachlophocsinhvien[$nk_ten] = [];
            }

            // Lấy số lượng sinh viên của lớp học
            $soLuongSinhVien = $lopHoc->sinhVien->count();
            $soLuongTotNghiep = $lopHoc->sinhVien->filter(function($sinhVien) {
                return $sinhVien->quyetDinhTotNghiep->isNotEmpty();
            })->count();
            $soLuongXoaTen = $lopHoc->sinhVien->filter(function($sinhVien) {
                return $sinhVien->quyetDinhXoaTen->isNotEmpty();
            })->count();
            $soLuongConLai = $soLuongSinhVien - $soLuongTotNghiep - $soLuongXoaTen;

            // Thêm thông tin lớp học và số lượng sinh viên vào mảng kết quả
            $danhsachlophocsinhvien[$nk_ten][$lop_ma] = [
                'lop_id' => $lopHoc->lh_id,
                'lop_ma' => $lop_ma,
                'lop_ten' => $lopHoc->lh_ten,
                'slsinhvienlop' => $soLuongSinhVien,
                'số lượng sinh viên tốt nghiệp' => $soLuongTotNghiep,
                'số lượng sinh viên xóa tên' => $soLuongXoaTen,
                'số lượng còn lại' => $soLuongConLai,
            ];
        }

        // Đảo ngược danh sách niên khóa
        $reversedNienKhoas = $NienKhoas->reverse();

        $kqnienkhoa = [];

        foreach ($reversedNienKhoas as $nienKhoa) {
            $nk_ten = $nienKhoa->nk_ten;

            // Kiểm tra nếu niên khóa có trong danh sách lớp học sinh viên
            if (isset($danhsachlophocsinhvien[$nk_ten])) {
                $lopHocs = $danhsachlophocsinhvien[$nk_ten];
                $lophocInfo = [];
                $maxSoLuongSinhVien = 0;

                foreach ($lopHocs as $lop_ma => $info) {
                    $lophocInfo[] = [
                        'lop_id' => $info['lop_id'],
                        'lop_ma' => $lop_ma,
                        'lop_ten' => $info['lop_ten'],
                        'slsinhvienlop' => $info['slsinhvienlop'],
                        'số lượng sinh viên tốt nghiệp' => $info['số lượng sinh viên tốt nghiệp'],
                        'số lượng sinh viên xóa tên' => $info['số lượng sinh viên xóa tên'],
                        'số lượng còn lại' => $info['số lượng còn lại']
                    ];

                    // Cập nhật số lượng sinh viên lớn nhất
                    if ($info['slsinhvienlop'] > $maxSoLuongSinhVien) {
                        $maxSoLuongSinhVien = $info['slsinhvienlop'];
                    }
                }

                // Lưu thông tin niên khóa có dữ liệu
                $kqnienkhoa[] = [
                    'nk_ten' => $nk_ten,
                    'số lượng lớp' => count($lopHocs), // Đếm số lượng lớp học
                    'max_số lượng sinh viên' => $maxSoLuongSinhVien, // Số lượng sinh viên lớn nhất trong niên khóa
                    'lophocs' => $lophocInfo
                ];

                break; // Chỉ lưu niên khóa đầu tiên có dữ liệu và thoát khỏi vòng lặp
            }
        }

        // Hiển thị kết quả
        /* foreach ($kqnienkhoa as $nienKhoaInfo) {
            echo "Niên khóa {$nienKhoaInfo['nk_ten']}:\n";
            echo "    Số lượng lớp: {$nienKhoaInfo['số lượng lớp']}\n";
            foreach ($nienKhoaInfo['lophocs'] as $lopInfo) {
                echo "        Lớp {$lopInfo['lop_ma']}:\n";
                echo "            Số lượng sinh viên tốt nghiệp: {$lopInfo['số lượng sinh viên tốt nghiệp']}\n";
                echo "            Số lượng sinh viên xóa tên: {$lopInfo['số lượng sinh viên xóa tên']}\n";
                echo "            Số lượng còn lại: {$lopInfo['số lượng còn lại']}\n";
            }
            echo "\n";
        }
 */


        return view('dashboard-canbo', compact(['infoUser', 'dsdotxettotnghiep', 'danhsachlophocsinhvien',  'maxLopHoc',
        'kqnienkhoa', 'NienKhoas', 'lophocs',
                    'thongKe', 'dotxettotnghiepsinhvien', 'dsDotThi', 'nganhnghe', 'khoadaotaotrungcapQuery', 
                    'khoadaotaocaodangQuery']));
    }

    

}
