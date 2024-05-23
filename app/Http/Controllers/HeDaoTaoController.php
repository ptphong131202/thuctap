<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\HeDaoTao;
use App\Http\Requests\HeDaoTaoEditRequest;

class HeDaoTaoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('qlsv.hedaotao.hedaotao_list');
    }

    public function paginate()
    {
        $danhSachHeDaoTao = HeDaoTao::withExists(['khoaDaoTao', 'nganhNghe'])->orderBy('hdt_id', 'desc')
                ->paginate(10)
                ->setPath(route('he-dao-tao.index'))
                ->onEachSide(2);
        return response()
                    ->json($danhSachHeDaoTao);
    }

    public function getAllHeDaoTao()
    {
        $danhSachHeDaoTao = HeDaoTao::orderBy('hdt_id', 'desc')->get();
        return response()->json($danhSachHeDaoTao);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('qlsv.hedaotao.hedaotao_create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(HeDaoTaoEditRequest $request)
    {
        $passedData = $request->only(['hdt_ten']);
        $model = new HeDaoTao;
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
    public function getHeDaotao($id)
    {
        $heDaoTaoModel = HeDaoTao::find($id);
        return response()
                ->json($heDaoTaoModel);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(HeDaoTaoEditRequest $request, $id)
    {
        $passedData = $request->only(['hdt_ten']);
        $model = HeDaoTao::find($id);
        $model->fill($passedData);
        $model->save();
        return response()
                ->json($model);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $model = HeDaoTao::find($id);
        if (!$model->khoaDaotao()->exists()) {
            $model->delete();
            return response()
                    ->json($model);
        }
        return response()
                    ->json($model, 422);
    }
}
