<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\NienKhoa;
use App\Http\Requests\NienKhoaEditRequest;

class NienKhoaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('qlsv.nienkhoa.nienkhoa_list');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('qlsv.nienkhoa.nienkhoa_create');
    }

    public function paginate()
    {
        $danhSachNienKhoa = NienKhoa::withExists('lopHoc')->orderBy('nk_id', 'desc')->paginate(10)
                ->setPath(route('nien-khoa.index'))
                ->onEachSide(2);
        return response()->json($danhSachNienKhoa);
    }

    public function getAllNienKhoa()
    {
        $danhSachNienKhoa = NienKhoa::orderBy('nk_id', 'desc')->get();
        return response()->json($danhSachNienKhoa);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(NienKhoaEditRequest $request)
    {
        $passedData = $request->only(['nk_ten']);
        $model = new NienKhoa;
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
    public function getNienKhoa($id)
    {
        $nienKhoaModel = NienKhoa::find($id);
        return response()->json($nienKhoaModel);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(NienKhoaEditRequest $request, $id)
    {
        $passedData = $request->only(['nk_ten']);
        $model = NienKhoa::find($id);
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
        $model = NienKhoa::find($id);
        if (!$model->lopHoc()->exists()) {
            $model->delete();
            return response()->json($model);
        }
        return response()->json($model, 422);
    }
}
