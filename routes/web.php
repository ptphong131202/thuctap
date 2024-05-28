<?php

use App\Http\Controllers\ConfigController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SinhVienController;
use App\Http\Controllers\HeDaoTaoController;
use App\Http\Controllers\NienKhoaController;
use App\Http\Controllers\KhoaDaoTaoController;
use App\Http\Controllers\MonHocController;
use App\Http\Controllers\HocKyController;
use App\Http\Controllers\LopHocController;
use App\Http\Controllers\NhapDiemController;
use App\Http\Controllers\TraCuuDiemController;
use App\Http\Controllers\QuyetDinhController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\NganhNgheController;
use App\Http\Controllers\DotThiController;
use App\Http\Controllers\DotXetTotNghiepController;
use App\Http\Controllers\ExceptionHandlerController;
use App\Http\Controllers\ImportExcelController;
use App\Http\Controllers\LogController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::get('/chart', [ChartController::class, 'index']); // phong url
Route::middleware(['auth'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('home');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::middleware('admin.index')->prefix('he-dao-tao')->group(function () {
        Route::get('/', [HeDaoTaoController::class, 'index'])->name('he-dao-tao.index');
    });

    Route::middleware('admin.index')->prefix('nganh-nghe')->group(function () {
        Route::get('/', [NganhNgheController::class, 'index'])->name('nganh-nghe.index');
    });

    Route::prefix('user')->group(function () {
        Route::middleware('admin.users')->group(function () {
            Route::get('/', [UserController::class, 'index'])->name('user.index');
            Route::get('/create', [UserController::class, 'create'])->name('user.create');
            Route::get('/{user}/edit', [UserController::class, 'edit'])->name('user.edit');
        });
        Route::post('/update-Info-Sv', [UserController::class, 'updateInfoSv'])->name('sinh-vien.updateInfoSv');
        Route::get('/change-password', [UserController::class, 'changePassword'])->name('user.password');
    });

    Route::middleware('admin.index')->prefix('sinh-vien')->group(function () {
        Route::get('/', [SinhVienController::class, 'index'])->name('sinh-vien.index');
    });


    Route::middleware('admin.index')->prefix('nien-khoa')->group(function () {
        Route::get('/', [NienKhoaController::class, 'index'])->name('nien-khoa.index');
    });

    Route::middleware('admin.index')->prefix('dot-thi')->group(function () {
        Route::get('/', [DotThiController::class, 'index'])->name('dot-thi.index');
        Route::get('/{id}', [DotThiController::class, 'xemChiTietDotThi'])->name('dot-thi.detail');
        Route::get('/{id}/danh-sach-sinh-vien', [DotThiController::class, 'xemDanhSachSinhVienThamGiaDotThi'])->name('dot-thi.danhsachsinhvien');
        Route::get('/nhap-diem/{lh_id}/{dt_id}', [DotThiController::class, 'xemChiTietDotThiMonHocLop'])->name('dot-thi.mon-hoc');
        Route::get('/ket-qua-hoc-tap/{lh_id}/{dt_id}', [DotThiController::class, 'ketQuaHocTap'])->name('dot-thi.ket-qua-hoc-tap');
        Route::get('/ket-qua-diem-thi-tn/{lh_id}/{dt_id}', [DotThiController::class, 'ketQuaThiTN'])->name('dot-thi.ket-qua-diemthi-tn');
        Route::get('/nhap-diem-excel/{lh_id}/{dt_id}', [DotThiController::class, 'preNhapDiemExcel'])->name('dot-thi.nhap-diem-excel');
        Route::get('/{dt_id}/danh-sach-sinh-vien/export', [DotThiController::class, 'exportDanhSachThiLai'])->name('danh-sach-sinh-vien-thi-lai-export');
        Route::get('/{dt_id}/diem-dot-thi-theo-lop/export', [DotThiController::class, 'exportDiemDotThiTheoLop'])->name('export-diem-dotthi-theo-lop');
        Route::get('/{dt_id}/danh-sach-xet-thi-tot-nghiep/export', [DotThiController::class, 'exportDanhSachXetThiTotNghiep'])->name('export-danh-sach-xet-thi-tot-nghiep');
        Route::get('/{dt_id}/diem-mon-hoc-dot-thi-theo-lop/export', [DotThiController::class, 'exportBanDiemTungMonVaDotThi'])->name('export-diem-monhoc-dotthi-theo-lop');
        Route::get('/{dt_id}/diem-mon-hoc-dot-thi-theo-lop-full/export', [DotThiController::class, 'exportBanDiemTungMonVaDotThiFull'])->name('export-diem-monhoc-dotthi-theo-lop-full');
    });

    Route::middleware('admin.index')->prefix('dot-xet-tot-nghiep')->group(function () {
        Route::get('/', [DotXetTotNghiepController::class, 'index'])->name('dot-xet-tot-nghiep.index');
        Route::get('/{id}', [DotXetTotNghiepController::class, 'xemChiTietDotXetTotNghiep'])->name('dot-xet-tot-nghiep.detail');
        Route::get('dot-thi/{id}', [DotXetTotNghiepController::class, 'xemChiTietDotThi'])->name('dot-thi.detail');
        Route::get('/nhap-diem/{lh_id}/{dt_id}', [DotXetTotNghiepController::class, 'xemChiTietDotXetTotNghiepMonHocLop'])->name('dot-xet-tot-nghiep.mon-hoc');
        Route::get('/{id}/danh-sach-sinh-vien', [DotXetTotNghiepController::class, 'xemDanhSachSinhVienThamGiaDotXetTN'])->name('dot-xet.danhsachsinhvien');
        Route::post('/themxettotnghiep', [DotXetTotNghiepController::class, 'nhapDotXetTotNghiep'])->name('themdotxettotnghiep');
        Route::get('/ket-qua-hoc-tap/{lh_id}/{dxtn_id}', [DotXetTotNghiepController::class, 'ketQuaHocTap'])->name('dot-xet-tot-nghiep.ket-qua-hoc-tap');
        Route::get('/{dt_id}/danh-sach-xet-tot-nghiep/export', [DotXetTotNghiepController::class, 'exportDanhSachXetTotNghiep'])->name('export-danh-sach-xet-tot-nghiep');

        Route::get('/{dt_id}/ds-dat-diem-thi-tot-nghiep/export', [DotXetTotNghiepController::class, 'exportBanDiemTungMonVaDotThiThiDatTN'])->name('export-ds-dat-diem-thi-tot-nghiep');
        Route::get('/{dxtn_id}/danh-sach-sinh-vien-thi-dat-tn/export', [DotXetTotNghiepController::class, 'exportDanhSachKetQuaXetTNheoDotXetTN'])->name('danh-sach-sinh-vien-dat-tn-export');
    });

    Route::middleware('admin.index')->prefix('khoa-dao-tao')->group(function () {
        Route::get('/', [KhoaDaoTaoController::class, 'index'])->name('khoa-dao-tao.index');
        Route::get('{khoa_dao_tao}/hoc-ky', [KhoaDaoTaoController::class, 'hocKy'])->name('khoa-dao-tao.hoc-ky');
    });

    Route::middleware('admin.index')->prefix('mon-hoc')->group(function () {
        Route::get('/', [MonHocController::class, 'index'])->name('mon-hoc.index');
        Route::get('/ImportExcel', [MonHocController::class, 'preThemMonHocExcel'])->name('mon-hoc.import-excel');
    });

    Route::middleware('admin.index')->prefix('quyet-dinh')->group(function () {
        Route::get('/', [QuyetDinhController::class, 'index'])->name('quyet-dinh.index');
    });

    Route::middleware('admin.index')->prefix('lop-hoc')->group(function () {
        Route::get('/', [LopHocController::class, 'index'])->name('lop-hoc.index');
        Route::get('{lop_hoc}/them-sinh-vien-excel', [LopHocController::class, 'preThemSinhVienExcel'])->name('lop-hoc.them-sinh-vien-excel');
        Route::get('{lop_hoc}', [LopHocController::class, 'chiTiet'])->name('lop-hoc.chi-tiet');
        Route::get('{lop_hoc}/hoc-ky', [LopHocController::class, 'hocKy'])->name('lop-hoc.hoc-ky');
    });

    Route::middleware('admin.score:xemdiem,nhaprenluyen')->prefix('nhap-diem')->group(function () {
        Route::get('/', [NhapDiemController::class, 'index'])->name('nhap-diem.index');
        Route::get('/kiem-tra', [NhapDiemController::class, 'kiemTraDiem'])->name('nhap-diem.kiem-tra');
        Route::get('/{lop_hoc}', [NhapDiemController::class, 'show'])->name('nhap-diem.show');
        Route::get('/{lh_id}/ket-qua-hoc-tap', [NhapDiemController::class, 'ketQuaHocTap'])->name('nhap-diem.ket-qua-hoc-tap');
        Route::get('/{lh_id}/ket-qua-hoc-tap/export', [NhapDiemController::class, 'exportKetQuaHocTap'])->name('nhap-diem.ket-qua-hoc-tap-export');
        Route::get('/ren-luyen/{token}', [NhapDiemController::class, 'nhapDiemRenLuyen'])->name('nhap-diem.ren-luyen.edit');
        Route::get('/mon-hoc/{token}', [NhapDiemController::class, 'nhapDiemMonHoc'])->name('nhap-diem.edit');
        Route::get('/dot-thi/{token}', [NhapDiemController::class, 'nhapDiemDotThi'])->name('nhap-diem.dotthi.edit');
        Route::post('/themdotthi', [NhapDiemController::class, 'nhapDotThi'])->name('themdotthi');
        Route::post('/', [NhapDiemController::class, 'ThongBaoDiem']);  /// phong url

    });


    Route::prefix('tra-cuu-diem')->group(function () {
        // Route::get('/', [TraCuuDiemController::class, 'index'])->name('tra-cuu-diem.index');
        Route::get('/', [TraCuuDiemController::class, 'traCuu'])->name('tra-cuu-diem.tra-cuu');
        Route::get('/{lop_hoc}/hoc-ky/{hoc_ky}', [TraCuuDiemController::class, 'detail'])->name('tra-cuu-diem.detail');
        Route::get('/xuat-danh-sach', [TraCuuDiemController::class, 'xuatKetQuaHocTap'])->name('tra-chu-xuat-ket-qua');
    });


    Route::middleware('admin.index')->prefix('nhat-ky')->group(function () {
        Route::get('/', [LogController::class, 'index'])->name('nhat-ky.index');
        Route::get('/{lh_id}/lop-hoc', [LogController::class, 'dsLopHoc'])->name('nhat-ky.lop-hoc');
        Route::get('{sv_id}/chi-tiet', [LogController::class, 'detail'])->name('nhat-ky.detail');
    });

    Route::get('sample/{filename}', [ImportExcelController::class, 'sample']);
});

Route::middleware([])->prefix('/api')->group(function () {
    Route::middleware('admin.index')->prefix('he-dao-tao')->group(function () {
        Route::get('/', [HeDaoTaoController::class, 'paginate']);
        Route::post('/', [HeDaoTaoController::class, 'store']);
        Route::get('all', [HeDaoTaoController::class, 'getAllHeDaoTao']);
        Route::get('{he_dao_tao}', [HeDaoTaoController::class, 'getHeDaotao']);
        Route::put('{he_dao_tao}', [HeDaoTaoController::class, 'update']);
        Route::delete('{he_dao_tao}', [HeDaoTaoController::class, 'destroy']);
    });

    Route::middleware('admin.index')->prefix('nganh-nghe')->group(function () {
        Route::get('/', [NganhNgheController::class, 'paginate']);
        Route::post('/', [NganhNgheController::class, 'store']);
        Route::get('all', [NganhNgheController::class, 'getAllNganhNghe']);
        Route::get('{nganh_nghe}', [NganhNgheController::class, 'getNganhNghe']);
        Route::put('{nganh_nghe}', [NganhNgheController::class, 'update']);
        Route::delete('{nganh_nghe}', [NganhNgheController::class, 'destroy']);
    });

    Route::prefix('quyet-dinh')->group(function () {
        Route::get('/', [QuyetDinhController::class, 'paginate']);
        Route::post('/', [QuyetDinhController::class, 'store']);
        Route::get('all/{loai}', [QuyetDinhController::class, 'getAllQuyetDinh']);
        Route::get('{quyet_dinh}', [QuyetDinhController::class, 'getQuyetDinh']);
        Route::put('{quyet_dinh}', [QuyetDinhController::class, 'update']);
        Route::delete('{quyet_dinh}', [QuyetDinhController::class, 'destroy']);
        Route::get('/check-used/{quyet_dinh}', [QuyetDinhController::class, 'checkUsed']);
    });

    Route::middleware('admin.index')->prefix('nien-khoa')->group(function () {
        Route::get('/', [NienKhoaController::class, 'paginate']);
        Route::post('/', [NienKhoaController::class, 'store']);
        Route::get('all', [NienKhoaController::class, 'getAllNienKhoa']);
        Route::get('{nien_khoa}', [NienKhoaController::class, 'getNienKhoa']);
        Route::put('{nien_khoa}', [NienKhoaController::class, 'update']);
        Route::delete('{nien_khoa}', [NienKhoaController::class, 'destroy']);
    });

    Route::middleware('admin.index')->prefix('dot-thi')->group(function () {
        Route::get('/', [DotThiController::class, 'paginate']);
        Route::post('/', [DotThiController::class, 'store']);
        Route::get('dongbo', [DotThiController::class, 'dongBoMonHocChoKhoaDaoTao']);
        Route::get('all', [DotThiController::class, 'getAllDotThi']);
        Route::get('all/dot-xet-tn', [DotThiController::class, 'getDotThiChoDotXetTN']);
        Route::get('{dot_thi}', [DotThiController::class, 'getDotThi']);
        Route::put('{dot_thi}', [DotThiController::class, 'update']);
        Route::put('updateQd-trang-thai/{dot_thi}', [DotThiController::class, 'updateQdTrangThai']);
        Route::delete('{dot_thi}', [DotThiController::class, 'destroy']);
        Route::delete('/xoa-lop-hoc/{lh_id}/{dt_id}', [DotThiController::class, 'xoaLopHocKhoiDotThi'])->name('dot-thi.xoa-lop-hoc');
        Route::get('{dot_thi}/dsmon', [DotThiController::class, 'getDsMonHocTheoLop']);
        Route::get('{dt_id}/cap-nhat-trang-thai-sinh-vien', [DotThiController::class, 'capNhatTrangThaiSinhVienThamGia']);
        Route::get('{dt_id}/xoa-sinh-vien-tham-gia', [DotThiController::class, 'xoaSinhVienThamGia']);
        Route::post('/cap-nhat-dot-thi-cho-lop', [DotThiController::class, 'capNhatDotThiChoLop']);
        Route::post('{dt_id}/nhap-diem-excel', [DotThiController::class, 'nhapDiemExcel']);
        Route::get('{dt_id}/danhsachsinhvien', [DotThiController::class, 'getDanhSachSinhVienTheoDotThi']);
        Route::post('{dt_id}/danhsachsinhvien', [DotThiController::class, 'capNhatLoaiThiChoSinhVien']);
    });

    Route::middleware('admin.index')->prefix('dot-xet-tot-nghiep')->group(function () {
        Route::get('/', [DotXetTotNghiepController::class, 'paginate']);
        Route::post('/', [DotXetTotNghiepController::class, 'store']);
        Route::get('all', [DotXetTotNghiepController::class, 'getAllDotXetTotNghiep']);
        Route::get('/dot-thi-tot-nghiep/{dxtn_id}', [DotXetTotNghiepController::class, 'getDotThiTheoDotXet']);
        Route::get('{dot_xet_tot_nghiep}', [DotXetTotNghiepController::class, 'getDotXetTotNghiep']);
        Route::put('{dot_xet_tot_nghiep}', [DotXetTotNghiepController::class, 'update']);
        Route::put('updateQd-trang-thai/{dxtn_id}', [DotXetTotNghiepController::class, 'updateQdTrangThai']);
        Route::delete('{dot_xet_tot_nghiep}', [DotXetTotNghiepController::class, 'destroy']);
        Route::delete('xoa-lop-du-xet/{dxtn_id}', [DotXetTotNghiepController::class, 'destroyDxtnLop']);

        Route::get('{dot_xet_tot_nghiep}/dsmon', [DotXetTotNghiepController::class, 'getDsMonHocTheoLop']);
        Route::post('/cap-nhat-dot-xet-cho-dot-thi', [DotXetTotNghiepController::class, 'capNhatDotXetChoDotThi']);
        Route::get('/cap-nhat-dot-xet-cho-dot-thi1/{dxtn_id}/{dt_id}', [DotXetTotNghiepController::class, 'capNhatDotXetChoDotThi1']);
        Route::post('/cap-nhat-xltn', [DotXetTotNghiepController::class, 'capNhatXltn'])->name('capNhatXltn');

        Route::post('/xoa-dot-thi', [DotXetTotNghiepController::class, 'xoaDotThi']);
        Route::get('{dxtn_id}/danhsachsinhvien', [DotXetTotNghiepController::class, 'getDanhSachSinhVienTheoDotXetTN']);
        Route::get('{dxtn_id}/cap-nhat-trang-thai-sv-dxtn', [DotXetTotNghiepController::class, 'capNhatTrangThaiSinhVienDotXetTN']);
    });


    Route::prefix('lop-hoc')->group(function () {
        Route::get('paginate', [LopHocController::class, 'paginate'])->where('name', '[A-Za-z]+');
        Route::get('dot-thi-lop-hoc/{dt_id}', [LopHocController::class, 'getLopHocTheoDotThi']);
        Route::get('all', [LopHocController::class, 'getAllLopHoc']);
        Route::get('dot-xet-tot-nghiep-lop-hoc/{dxtn_id}', [LopHocController::class, 'getLopHocTheoDotXetTotNghiep']);
        Route::get('{lop_hoc}/ds-sinh-vien', [LopHocController::class, 'getDanhSachSinhVien']);
        Route::post('{lop_hoc}/xoa-sinh-vien', [LopHocController::class, 'xoaSinhVienDanhSachThamGia']);
        Route::post('{lh_id}/them-sinh-vien-excel', [LopHocController::class, 'themSinhVienExcel']);
        Route::post('duplicate/{lop_hoc}', [LopHocController::class, 'duplicate']);

        // Support confin mon hoc
        Route::get('{lop_hoc}/hoc-ky', [LopHocController::class, 'getDanhSachHocKy']);
        Route::post('{lop_hoc}/hoc-ky', [LopHocController::class, 'updateHocKy']);
        Route::post('{lop_hoc}/hoc-ky-tab0', [LopHocController::class, 'updateHocKyTab0']);

        Route::post('/get-sync-mon-hoc-by-kdt', [LopHocController::class, 'getSyncMonHocByKdt']);
        Route::put('{lh_id}/update-sync-mon-hoc-by-kdt', [LopHocController::class, 'updateSyncMonHocByKdt']);
        Route::delete('delete_mh/{mh_id}', [LopHocController::class, 'delete_monHoc']);
    });

    Route::resource('lop-hoc', LopHocController::class)->except([
        'index'
    ]);


    Route::middleware('admin.index')->prefix('sinh-vien')->group(function () {
        Route::get('/', [SinhVienController::class, 'paginate']);
        Route::post('/', [SinhVienController::class, 'store']);
        Route::get('{sinh_vien}', [SinhVienController::class, 'getSinhVien']);
        Route::put('cap-nhat-quyet-dinh-sinh-vien', [SinhVienController::class, 'capNhatQuyetDinh'])->where('name', '[A-Za-z]+');
        Route::post('them-quyet-dinh-thi-tot-nghiep', [SinhVienController::class, 'themQuyetDinhThiTN'])->where('name', '[A-Za-z]+');
        Route::post('them-quyet-dinh-tot-nghiep', [SinhVienController::class, 'themQuyetDinhTN'])->where('name', '[A-Za-z]+');

        Route::put('{sinh_vien}', [SinhVienController::class, 'update']);
        Route::delete('{sinh_vien}', [SinhVienController::class, 'destroy']);
    });

    Route::prefix('user')->group(function () {
        Route::middleware('admin.users')->group(function () {
            Route::get('paginate', [UserController::class, 'paginate']);
            Route::post('/', [UserController::class, 'store']);
            Route::put('/{user}', [UserController::class, 'update']);
            Route::delete('/{user}', [UserController::class, 'destroy']);
        });
        Route::get('/current/permissions', [UserController::class, 'permissions']);
        Route::put('/current/password', [UserController::class, 'updatePassword']);
        Route::get('/{user}', [UserController::class, 'getUser']);
    });

    Route::middleware('admin.index')->prefix('mon-hoc')->group(function () {
        Route::get('/', [MonHocController::class, 'paginate']);
        Route::post('/', [MonHocController::class, 'store']);
        Route::get('all', [MonHocController::class, 'getAllMonHoc']);
        Route::get('all-by-khoa-dao-tao', [MonHocController::class, 'getAllMonHocByKhoaDaoTao']);
        Route::get('{mon_hoc}', [MonHocController::class, 'getMonHoc']);
        Route::put('{mon_hoc}', [MonHocController::class, 'update']);
        Route::delete('{mon_hoc}', [MonHocController::class, 'destroy']);
        Route::put('restore/{mon_hoc}', [MonHocController::class, 'restore']);
        Route::post('/them-excel', [MonHocController::class, 'themMonHocExcel']);

        // Select 2 support route (for beta v0.96)
        Route::get('select2', [MonHocController::class, 'select2Ajax']);
        Route::get('select2/{mon_hoc}', [MonHocController::class, 'select2AjaxSelect']);
    });

    Route::middleware('admin.index')->prefix('khoa-dao-tao')->group(function () {
        Route::get('/', [KhoaDaoTaoController::class, 'paginate']);
        Route::post('/', [KhoaDaoTaoController::class, 'store']);
        Route::get('all', [KhoaDaoTaoController::class, 'getAllKhoaDaoTao']);
        Route::get('{khoa_dao_tao}', [KhoaDaoTaoController::class, 'getKhoaDaoTao']);
        Route::put('{khoa_dao_tao}', [KhoaDaoTaoController::class, 'update']);
        Route::delete('{khoa_dao_tao}', [KhoaDaoTaoController::class, 'destroy']);
        Route::post('duplicate/{khoa_dao_tao}', [KhoaDaoTaoController::class, 'duplicate']);

        // Support confin mon hoc
        Route::get('{khoa_dao_tao}/hoc-ky', [KhoaDaoTaoController::class, 'getDanhSachHocKy']);
        Route::post('{khoa_dao_tao}/hoc-ky', [KhoaDaoTaoController::class, 'updateHocKy']);
        Route::post('/getSyncMonHocTheoNN', [KhoaDaoTaoController::class, 'getSyncMonHocTheoNN']);
        Route::put('{kdt_id}/updateSyncMonHocTheoNN', [KhoaDaoTaoController::class, 'updateSyncMonHocTheoNN']);

    });

    Route::middleware('admin.score:xemdiem,nhaprenluyen')->prefix('nhap-diem')->group(function () {
        Route::get('/', [NhapDiemController::class, 'paginate']);
        Route::get('/{lh_id}/hoc-ky', [NhapDiemController::class, 'getDanhSachHocKy']);
        Route::get('{lh_id}/bang-diem', [NhapDiemController::class, 'getBangDiem']);
        Route::post('{lh_id}/bang-diem', [NhapDiemController::class, 'updateBangDiem']);
        Route::delete('{lh_id}/bang-diem', [NhapDiemController::class, 'destroyBangDiem']);
        Route::get('{lh_id}/bang-diem-dot-thi', [NhapDiemController::class, 'getBangDiemDotThi']);
        Route::post('{lh_id}/bang-diem-dot-thi', [NhapDiemController::class, 'updateBangDiemDotThi']);
        Route::post('{lh_id}/bang-diem-dot-thi-loai0', [NhapDiemController::class, 'updateBangDiemLoai0DotThi']);

        Route::delete('{lh_id}/bang-diem-dot-thi', [NhapDiemController::class, 'destroyBangDiemDotThi']);

        Route::get('{dt_id}/bang-diem-dot-thi-full', [NhapDiemController::class, 'getBangDiemByDotThiFull']);
    });

    Route::prefix('tra-cuu-diem')->group(function () {
        Route::get('/danh-sach-lop', [TraCuuDiemController::class, 'getDanhSachLopHoc']);
    });

    Route::middleware('admin.index')->prefix('cau-hinh')->group(function () {
        Route::put('/{status}/update-config-allow-info-hssv', [ConfigController::class, 'updateConfigInfoHssv']);
    });

    Route::middleware('admin.index')->prefix('nhat-ky')->group(function () {
        Route::get('/', [LogController::class, 'paginate']);
        Route::post('/', [LogController::class, 'store']);
        Route::get('{sv_id}/paginateNhatKy', [LogController::class, 'paginateNhatKy']);
    });

    Route::prefix('excel')->group(function () {
        Route::post('import-score-check', [ImportExcelController::class, 'importScoreCheck']);
        Route::post('import-score', [ImportExcelController::class, 'importScore']);
        Route::post('import-raw-score', [ImportExcelController::class, 'importRawScore']);
        Route::post('import-ren-luyen-score', [ImportExcelController::class, 'importRenLuyenScore']);
        Route::post('import-user', [ImportExcelController::class, 'importUser']);
        Route::post('import-diemdotthi', [ImportExcelController::class, 'importDiemDotThi']);
        Route::post('import-diemdotthi-theomon', [ImportExcelController::class, 'importDiemDotThiTheoMon']);
        Route::post('import-mon-hoc', [ImportExcelController::class, 'importMonHoc']);
    });

    Route::get('/nhap-diem/{lh_id}/ket-qua-hoc-tap/json', [NhapDiemController::class, 'ketQuaHocTapAPI'])->name('nhap-diem.ket-qua-hoc-tap-api');
});

require __DIR__ . '/auth.php';
