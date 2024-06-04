<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\QuyetDinh;
use App\Http\Requests\QuyetDinhEditRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class QuyetDinhController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('qlsv.quyetdinh.quyetdinh_list');
    }

    // T.Phong chỉnh sửa hàm paginate
    public function paginate(Request $request)
    {
        $search = $request->search;
        $danhSachquyetdinh = QuyetDinh::orderBy('qd_id', 'desc')
                ->withExists(['sinhVien', 'lopHoc'])
                ->where(function ($builder) use ($search) {
                    $builder->whereRaw('lower(qlsv_quyetdinh.qd_ma) like lower(?)', "%$search%")
                        ->orWhereRaw('lower(qlsv_quyetdinh.qd_ten) like lower(?)', "%$search%");
                })
                ->paginate(10)
                ->setPath(route('quyet-dinh.index'))
                ->appends(['search' => $search])
                ->onEachSide(2);

        // Tạo danh sách các quyết định đã được nhóm theo qd_loai
        $dsquyetdinh_loai = [];
        // Nhóm các quyết định theo qd_loai
        if (!isset($dsquyetdinh_loai[$quyetDinh->qd_loai])) {
            $dsquyetdinh_loai[$quyetDinh->qd_loai] = [];
        }
        $dsquyetdinh_loai[$quyetDinh->qd_loai][] = $quyetDinh->qd_ten;


        // Kiểm tra từng phần tử và thêm dxtn_id nếu qd_loai == 1
        foreach ($danhSachquyetdinh->items() as $quyetDinh) {
            if ($quyetDinh->qd_loai == 0) {
                // Join đến bảng qlsv_sinhvien_quyetdinh để đếm số bản ghi
                $count = DB::table('qlsv_lophoc')
                    ->where('qd_id', $quyetDinh->qd_id)
                    ->count();
                
                // Thiết lập giá trị của $checkusser dựa trên kết quả đếm
                $quyetDinh->checklophoc = $count > 0;
            }

            if ($quyetDinh->qd_loai == 1) {
                // Join đến bảng qlsv_dotxettotnghiep để lấy dxtn_id
                $dotXetTotNghiep = DB::table('qlsv_dotxettotnghiep')
                    ->where('qd_id', $quyetDinh->qd_id)
                    ->select('dxtn_id')
                    ->first();
                
                // Thêm dxtn_id vào quyết định
                $quyetDinh->dxtn_id = $dotXetTotNghiep ? $dotXetTotNghiep->dxtn_id : null;
            }

            if ($quyetDinh->qd_loai == 2) {
                // Join đến bảng qlsv_sinhvien_quyetdinh để đếm số bản ghi
                $count = DB::table('qlsv_sinhvien_quyetdinh')
                    ->where('qd_id', $quyetDinh->qd_id)
                    ->count();
                
                // Thiết lập giá trị của $checkusser dựa trên kết quả đếm
                $quyetDinh->checkusser = $count > 0;
            }

            if ($quyetDinh->qd_loai == 3) {
                // Join đến bảng qlsv_dotthi để lấy dt_id
                $dotThi = DB::table('qlsv_dotthi')
                    ->where('qd_id', $quyetDinh->qd_id)
                    ->select('dt_id')
                    ->first();
                
                // Thêm dt_id vào quyết định
                $quyetDinh->dt_id = $dotThi ? $dotThi->dt_id : null;
            }
        }

        // Chuyển danh sách nhóm thành dạng mảng có key là qd_loai và value là danh sách qd_ten
        $groupedDanhSachquyetdinh = [];
        foreach ($dsquyetdinh_loai as $qd_loai => $qd_ten_list) {
            $groupedDanhSachquyetdinh[] = [
                'qd_loai' => $qd_loai,
                'qd_ten' => $qd_ten_list
            ];
        }

        dd($dsquyetdinh_loai);

        return response()->json([
            'danhSachquyetdinh' => $danhSachquyetdinh,
            'dsquyetdinh_loai' => $groupedDanhSachquyetdinh
        ]);
    }

    public function getAllQuyetDinh($loai)
    {
        $danhSachquyetdinh = DB::table('qlsv_quyetdinh as qd')
        ->where('qd.qd_loai', '=', $loai)
        ->whereNotIn('qd.qd_id', function ($query) {
            $query->select('qlsv_dotthi.qd_id')
                ->from('qlsv_quyetdinh')
                ->join('qlsv_dotthi', 'qlsv_dotthi.qd_id', '=', 'qlsv_quyetdinh.qd_id')
                ->where('qlsv_dotthi.dt_ketthuc', '=', 1);
        })
        ->get();


        // $danhSachquyetdinh = QuyetDinh::orderBy('qd_id', 'desc')
        //         ->where('qd_loai', '=', $loai)
        //         ->get();
        return response()->json($danhSachquyetdinh);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(QuyetDinhEditRequest $request)
    {
        $passedData = $request->only(['qd_ten', 'qd_ma', 'qd_ngay', 'qd_loai']);
        $model = new QuyetDinh;
        $model->fill($passedData);
        $model->save();
        return response()->json($model);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getQuyetDinh($id)
    {
        $quyetdinhModel = QuyetDinh::find($id);
        return response()->json($quyetdinhModel);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(QuyetDinhEditRequest $request, $id)
    {
        $passedData = $request->only(['qd_ten', 'qd_ma', 'qd_ngay', 'qd_loai']);
        $model = QuyetDinh::find($id);
        $model->fill($passedData);
        $model->save();
        return response()->json($model);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $model = QuyetDinh::find($id);
        $model->delete();
        \App\Models\LopHoc::whereQdId($id)->update([
            'qd_id' => 0,
        ]);
        \DB::table('qlsv_sinhvien_quyetdinh')->whereQdId($id)->delete();
        return response()->json($model);
    }

    /**
     * @param [type] $id
     * @return void
     * @author ttdat
     * @version 1.0
     */
    public function checkUsed($id) {
        $quyetDinh = QuyetDinh::withExists(['sinhVien', 'lopHoc'])->find($id);
        return response()->json($quyetDinh);
    }
}
