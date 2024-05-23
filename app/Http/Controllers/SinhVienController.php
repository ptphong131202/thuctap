<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SinhVien;
use App\Models\QuyetDinh;
use App\Http\Requests\SinhVienEditRequest;
use App\Http\Requests\QuyetDinhSinhVienEditRequest;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Enums\UserType;
use App\Enums\UserStatus;
use Illuminate\Support\Facades\DB;

class SinhVienController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('qlsv.sinhvien.sinhvien_list');
    }

    public function paginate(Request $request)
    {
        $search = $request->search;
        $danhSachSinhVien = SinhVien::orderBy('sv_ma')->with(['quyetDinhXoaTen','quyetDinhTotNghiep', 'quyetDinhThemLop', 'lopHoc', 'user'])
                ->withCount(['sinhVienBangDiem' => function ($query) {
                    $query->whereNotNull('svd_first');
                }])
                ->where(function ($builder) use ($search) {
                    $builder->whereRaw('lower(qlsv_sinhvien.sv_ma) like lower(?)', "%$search%")
                        ->orWhereRaw('lower(qlsv_sinhvien.sv_ten) like lower(?)', "%$search%");
                })
                ->paginate(10)
                ->setPath(route('sinh-vien.index'))
                ->appends(['search' => $search])
                ->onEachSide(2);
        return response()->json($danhSachSinhVien);
    }

    // public function getAllSinhVien()
    // {
    //     $danhSachSinhVien = SinhVien::orderBy('sv_id', 'desc')->get();
    //     return response()->json($danhSachSinhVien);
    // }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(SinhVienEditRequest $request)
    {
        // Input
        $lh_id = $request->only(['lh_id']);
        $userPassedData = $request->only(['password', 'email']);
        $sinhVienPassedData = $request->only([ 'sv_ma','sv_ten','sv_ho','sv_trinhdo','sv_diachi','sv_gioitinh','sv_ngaysinh','sv_dantoc','sv_ghichu','sv_sdt']);
        $quyetDinhPassedData = $request->only(['qd_id', 'qd_ten', 'qd_ngay', 'qd_ma']);
        $sinhVienModel = new SinhVien;

        DB::transaction(function () use ($userPassedData, $sinhVienPassedData, $sinhVienModel, $lh_id, $quyetDinhPassedData) {
            $userModel = new User;
            $userModel->fill($userPassedData);
            $userModel->password = bcrypt($userModel->password);
            $userModel->type = UserType::SINHVIEN;
            $userModel->status = UserStatus::ACTIVE;
            $userModel->username = $sinhVienPassedData['sv_ma'];
            if ($sinhVienPassedData['sv_ho']) {
                $userModel->name = $sinhVienPassedData['sv_ho'] . ' ' . $sinhVienPassedData['sv_ten'];
            } else {
                $userModel->name = $sinhVienPassedData['sv_ten'];
            }
            $userModel->save();

            $qd_id = $quyetDinhPassedData['qd_id'];
            if($qd_id == 0){
                $modelQD = new QuyetDinh;
                $modelQD->fill($quyetDinhPassedData);
                $modelQD->qd_loai = \App\Enums\LoaiQuyetDinh::THEMLOP;
                $modelQD->save();
                $qd_id = $modelQD->qd_id;
            }else if($qd_id == -1){
                $qd_id = 0;
            }

            $sinhVienModel->fill($sinhVienPassedData);
            $sinhVienModel->user_id = $userModel->user_id;
            $sinhVienModel->save();

            $userModel->permissions()->sync(\App\Enums\Permission::STUDENT);

            if($lh_id != null){
                $sinhVienModel->lopHoc()->sync($lh_id);
            }

            if($sinhVienModel->quyetDinhThemLop()->exists()){
                DB::table('qlsv_sinhvien_quyetdinh')->whereQdId($sinhVienModel->quyetDinhThemLop->first()->qd_id)
                    ->whereSvId($sinhVienModel->sv_id)->delete();
            }
            if($qd_id != 0){
                $sinhVienModel->quyetDinhThemLop()->attach($qd_id);
            }
        });

        return response()->json($sinhVienModel);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getSinhVien($id)
    {
        $sinhVienModel = SinhVien::with(['user:user_id,email','lopHoc:lh_id', 'quyetDinhThemLop:qd_id'])->find($id);
        return response()->json($sinhVienModel);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(SinhVienEditRequest $request, $id)
    {
        // Input
        $lh_id = $request->only(['lh_id']);
        $userPassedData = $request->only(['password', 'email']);
        $sinhVienPassedData = $request->only([ 'sv_ma','sv_ten','sv_ho','sv_trinhdo','sv_diachi','sv_gioitinh','sv_ngaysinh','sv_dantoc','sv_ghichu','sv_sdt']);
        $quyetDinhPassedData = $request->only(['qd_id', 'qd_ten', 'qd_ngay', 'qd_ma']);
        $sinhVienModel = SinhVien::find($id);

        DB::transaction(function () use ($sinhVienModel, $userPassedData, $sinhVienPassedData, $lh_id, $quyetDinhPassedData) {
            $userModel = $sinhVienModel->user;
            $userModel->fill($userPassedData);
            if (isset($userPassedData['password'])) {
                $userModel->password = bcrypt($userModel->password);
            }
            $userModel->type = UserType::SINHVIEN;
            $userModel->username = $sinhVienPassedData['sv_ma'];
            if ($sinhVienPassedData['sv_ho']) {
                $userModel->name = $sinhVienPassedData['sv_ho'] . ' ' . $sinhVienPassedData['sv_ten'];
            } else {
                $userModel->name = $sinhVienPassedData['sv_ten'];
            }
            $userModel->save();

            $qd_id = $quyetDinhPassedData['qd_id'];
            if($qd_id == 0){
                $modelQD = new QuyetDinh;
                $modelQD->fill($quyetDinhPassedData);
                $modelQD->qd_loai = \App\Enums\LoaiQuyetDinh::THEMLOP;
                $modelQD->save();
                $qd_id = $modelQD->qd_id;
            }else if($qd_id == -1){
                $qd_id = 0;
            }

            $sinhVienModel->fill($sinhVienPassedData);
            $sinhVienModel->user_id = $userModel->user_id;
            $sinhVienModel->save();
            $userModel->permissions()->sync(\App\Enums\Permission::STUDENT);

            if($lh_id != null){
                $sinhVienModel->lopHoc()->sync($lh_id);
            }

            if($sinhVienModel->quyetDinhThemLop()->exists()){
                DB::table('qlsv_sinhvien_quyetdinh')->whereQdId($sinhVienModel->quyetDinhThemLop->first()->qd_id)
                    ->whereSvId($sinhVienModel->sv_id)->delete();
            }
            if($qd_id != 0){
                $sinhVienModel->quyetDinhThemLop()->attach($qd_id);
            }
        });

        return response()->json($sinhVienModel);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $model = SinhVien::find($id);
        DB::table('qlsv_sinhvien_quyetdinh')->whereSvId($id)->delete();
        DB::table('qlsv_sinhvien_lophoc')->whereSvId($id)->delete();
        DB::table('qlsv_sinhvien_diem')->whereSvId($id)->delete();
        DB::table('users')->whereUserId($model->user_id)->delete();
        $model->forceDelete();
        return response()->json($model);
    }

    public function capNhatQuyetDinh(QuyetDinhSinhVienEditRequest $request)
    {
        $quyetDinhPassedData = $request->only(['qd_id', 'qd_ten', 'qd_ngay', 'qd_ma']);
        $sinhVienIDsPassedData = $request->ds_sinhvien;
        $loai = $request->loai;
        $hocky = $request->qd_hocky;
        DB::transaction(function () use ($loai, $sinhVienIDsPassedData, $quyetDinhPassedData, $hocky) {
            $qd_id = $quyetDinhPassedData['qd_id'];
            if($qd_id == 0) {
                $modelQD = new QuyetDinh;
                $modelQD->fill($quyetDinhPassedData);
                $modelQD->qd_loai = $loai;
                $modelQD->save();
                $qd_id = $modelQD->qd_id;
            } else if($qd_id == -1) {
                $qd_id = [];
            }

            foreach ($sinhVienIDsPassedData as $sv_id) {
                $modelSV = new SinhVien;
                $modelSV->sv_id = $sv_id;

                if ($loai == \App\Enums\LoaiQuyetDinh::TOTNGHIEP) {
                    if($modelSV->quyetDinhTotNghiep()->exists()){
                        DB::table('qlsv_sinhvien_quyetdinh')->whereQdId($modelSV->quyetDinhTotNghiep->first()->qd_id)
                            ->whereSvId($sv_id)->delete();
                    }
                    $modelSV->quyetDinhTotNghiep()->attach($qd_id, ['qd_hocky' => $hocky]);
                }else {
                    if($modelSV->quyetDinhXoaTen()->exists()){
                        DB::table('qlsv_sinhvien_quyetdinh')->whereQdId($modelSV->quyetDinhXoaTen->first()->qd_id)
                            ->whereSvId($sv_id)->delete();
                    }
                    $modelSV->quyetDinhXoaTen()->attach($qd_id, ['qd_hocky' => $hocky]);
                }
            }
        });
    }

    public function themQuyetDinhThiTN(QuyetDinhSinhVienEditRequest $request)
    {
        $quyetDinhPassedData = $request->only(['qd_id', 'qd_ten', 'qd_ngay', 'qd_ma']);
        $dt_id = $request->dt_id;
        $loai = 3;

        DB::transaction(function () use ($loai, $quyetDinhPassedData, $dt_id) {
            $qd_id = $quyetDinhPassedData['qd_id'];

            if($qd_id == 0) {
                $modelQD = new QuyetDinh;
                $modelQD->fill($quyetDinhPassedData);
                $modelQD->qd_loai = $loai;
                $modelQD->save();
                $qd_id = $modelQD->qd_id;
            } else if($qd_id == -1) {
                $qd_id = [];
            }


            // Kiểm tra nếu có quyết định thì cập nhật quyết định vào bảng
            $qlsv_quyetdinh = DB::table('qlsv_quyetdinh as qd')
            ->where('qd.qd_id', $qd_id)
            ->where('qd.qd_loai', $loai)
            ->get();

            // cập nhật quyết định
            if ($qlsv_quyetdinh) {
            DB::update(
                'update qlsv_dotthi set qd_id = ? , dt_qd_trangthai = 1 where dt_id = ?',
                [$qd_id, $dt_id]
            );
            }
        });
    }

    public function themQuyetDinhTN(QuyetDinhSinhVienEditRequest $request)
    {
        $quyetDinhPassedData = $request->only(['qd_id', 'qd_ten', 'qd_ngay', 'qd_ma']);
        $dxtn_id = $request->dxtn_id;
        $loai = 1;


        DB::transaction(function () use ($loai, $quyetDinhPassedData, $dxtn_id) {
            $qd_id = $quyetDinhPassedData['qd_id'];

            if($qd_id == 0) {
                $modelQD = new QuyetDinh;
                $modelQD->fill($quyetDinhPassedData);
                $modelQD->qd_loai = $loai;
                $modelQD->save();
                $qd_id = $modelQD->qd_id;
            } else if($qd_id == -1) {
                $qd_id = [];
            }


            // Kiểm tra nếu có quyết định thì cập nhật quyết định vào bảng
            $qlsv_quyetdinh = DB::table('qlsv_quyetdinh as qd')
            ->where('qd.qd_id', $qd_id)
            ->where('qd.qd_loai', $loai)
            ->get();

            // cập nhật quyết định
            if ($qlsv_quyetdinh) {
            DB::update(
                'update qlsv_dotxettotnghiep set qd_id = ? , dxtn_qd_trangthai = 1 where dxtn_id = ?',
                [$qd_id, $dxtn_id]
            );
            }
        });
    }
}
