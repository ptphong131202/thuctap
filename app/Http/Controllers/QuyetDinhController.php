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
        $qd_loai = $request->qd_loai;
        if($qd_loai === null || $qd_loai === ''){
            $qd_loai = '';
            $danhSachquyetdinh = QuyetDinh::orderBy('qd_id', 'desc')
                ->withExists(['sinhVien', 'lopHoc'])
                ->where(function ($builder) use ($search) {
                    $builder->whereRaw('lower(qlsv_quyetdinh.qd_ma) like lower(?)', "%$search%")
                        ->orWhereRaw('lower(qlsv_quyetdinh.qd_ten) like lower(?)', "%$search%");
                })
                ->paginate(10)
                ->setPath(route('quyet-dinh.index'))
                ->appends(['search' => $search, 'qd_loai' => $qd_loai])
                ->onEachSide(2);
        }
        else {
            $search = '';
            $danhSachquyetdinh = QuyetDinh::orderBy('qd_id', 'desc')
                ->withExists(['sinhVien', 'lopHoc'])
                ->where(function ($builder) use ($qd_loai) {
                    $builder->orWhereRaw('lower(qlsv_quyetdinh.qd_loai) like lower(?)', "%$qd_loai%");
                })
                ->paginate(10)
                ->setPath(route('quyet-dinh.index'))
                ->appends(['qd_loai' => $qd_loai, 'search' => $search])
                ->onEachSide(2);
        }

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
                    ->select('dxtn_id', 'dxtn_tunam','dxtn_dennam',  'dxtn_hdt_id', 'dxtn_ten')
                    ->first();
                // Thêm dt_id và dt_ten vào quyết định
                if ($dotXetTotNghiep) {
                    $quyetDinh->dxtn_id = $dotXetTotNghiep->dxtn_id;
                    $quyetDinh->dxtn_ten = $dotXetTotNghiep->dxtn_ten;
                    $quyetDinh->dxtn_tunam = $dotXetTotNghiep->dxtn_tunam;
                    $quyetDinh->dxtn_dennam = $dotXetTotNghiep->dxtn_dennam;
                    $quyetDinh->dxtn_hdt_id = $dotXetTotNghiep->dxtn_hdt_id;
                } else {
                    $quyetDinh->dxtn_id = null;
                    $quyetDinh->dxtn_ten = null;
                    $quyetDinh->dxtn_tunam = null;
                    $quyetDinh->dxtn_dennam = null;
                    $quyetDinh->dxtn_hdt_id = null;
                }
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
                    ->select('dt_id', 'dt_ten','dt_tunam', 'dt_dennam', 'dt_hdt_id')
                    ->first();
                
                // Thêm dt_id và dt_ten vào quyết định
                if ($dotThi) {
                    $quyetDinh->dt_id = $dotThi->dt_id;
                    $quyetDinh->dt_ten = $dotThi->dt_ten;
                    $quyetDinh->dt_tunam = $dotThi->dt_tunam;
                    $quyetDinh->dt_dennam = $dotThi->dt_dennam;
                    $quyetDinh->dt_hdt_id = $dotThi->dt_hdt_id;
                } else {
                    $quyetDinh->dt_id = null;
                    $quyetDinh->dt_ten = null;
                    $quyetDinh->dt_tunam = null;
                    $quyetDinh->dt_dennam = null;
                    $quyetDinh->dt_hdt_id = null;
                }
            }
            
        }

        return response()->json($danhSachquyetdinh);
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
