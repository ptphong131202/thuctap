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
