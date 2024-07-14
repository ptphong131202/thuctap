<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\NganhNghe;
use App\Models\MonHoc;

use App\Http\Requests\NganhNgheEditRequest;
use Illuminate\Support\Facades\DB; // P.Dinh ->>>>

class NganhNgheController extends Controller
{
    /**get
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $permissions = auth()->user()->permissions()->get()->map(function ($item) {
            return $item->permission_id;
        });
        return view('qlsv.nganhnghe.nganhnghe_list', compact('permissions'));
    }

    // P.Dinh ->>>>
    public function monhoc($nn_id, $hdt_id)
    {

        $parentUrl = session('parent_url:nganh-nghe', '/nganh-nghe');
        return view('qlsv.nganhnghe.nganhnghe_monhoc', compact(['nn_id', 'hdt_id', 'parentUrl']));

    }



    public function paginate(Request $request)
    {
        $search = $request->search;
        $hdt_id = $request->hedaotao;
        $danhSachNganhNghe = NganhNghe::with('heDaoTao')
            ->withExists(['monHoc', 'khoaDaoTao'])
            ->where(function ($builder) use ($search) {
                $builder->whereRaw('lower(qlsv_nganhnghe.nn_ma) like lower(?)', "%$search%")
                    ->orWhereRaw('lower(qlsv_nganhnghe.nn_ten) like lower(?)', "%$search%");
            })
            ->where(function ($builder) use ($hdt_id) {
                if (auth()->user()->hasPermission('caodang') && auth()->user()->hasPermission('trungcap')) {
                    if (isset($hdt_id) && $hdt_id != -1) {
                        $builder->whereRaw('qlsv_nganhnghe.hdt_id = ?', $hdt_id);
                    }
                } else {
                    if (auth()->user()->hasPermission('caodang')) {
                        $builder->whereRaw('qlsv_nganhnghe.hdt_id = ?', 4);
                    } else if (auth()->user()->hasPermission('trungcap')) {
                        $builder->whereRaw('qlsv_nganhnghe.hdt_id = ?', 5);
                    }
                }
            })
            ->orderBy('nn_id', 'desc')
            ->paginate(10)
            ->setPath(route('nganh-nghe.index'))
            ->appends([
                'search' => $search,
                'hedaotao' => $hdt_id
            ])
            ->onEachSide(2);

        foreach ($danhSachNganhNghe as $nganhNghe) {
            $monhoc_url = route('nganh-nghe.monhoc', [$nganhNghe->nn_id, $nganhNghe->heDaoTao->hdt_id]);
            $nganhNghe->monhoc_url = $monhoc_url;
        }


        return response()
            ->json($danhSachNganhNghe);
    }

    public function getAllNganhNghe(Request $request)
    {
        $hdt_id = intval($request->hedaotao);
        $danhSachNganhNghe = NganhNghe::with('heDaoTao')
            ->where(function ($builder) use ($hdt_id) {
                if (isset($hdt_id) && $hdt_id != -1 && $hdt_id != 0) {
                    $builder->whereRaw('qlsv_nganhnghe.hdt_id = ?', "$hdt_id");
                }
            })
            ->orderBy('nn_id', 'desc')->get();
        return response()->json($danhSachNganhNghe);
    }



    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('qlsv.nganhnghe.nganhnghe_create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(NganhNgheEditRequest $request)
    {
        $passedData = $request->only(['nn_ma', 'nn_ten', 'hdt_id']);
        $model = new NganhNghe;
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
    public function getNganhNghe($id)
    {
        $nganhNgheModel = NganhNghe::with('khoaDaoTao')->find($id);

        if (!$nganhNgheModel) {
            return response()->json(['error' => 'NganhNghe not found'], 404);
        }

        $nganhNgheModel->khoa_dao_tao_exists = $nganhNgheModel->relationLoaded('khoaDaoTao') && count($nganhNgheModel->khoaDaoTao) > 0;

        return response()->json($nganhNgheModel);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(NganhNgheEditRequest $request, $id)
    {
        $passedData = $request->only(['nn_ma', 'nn_ten', 'hdt_id']);
        $model = NganhNghe::find($id);
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
        $model = NganhNghe::find($id);
        $model->delete();
        return response()->json($model);
    }


    public function duplicate(NganhNgheEditRequest $request, $id)
    {
        $passedData = $request->only(['nn_ma', 'nn_ten']);
        $originModel = NganhNghe::find($id);
        $newModel = null;

        DB::transaction(function () use ($originModel, $passedData, &$newModel) {
            // Tạo ngành nghề mới
            $newModel = $originModel->replicate();
            $newModel->fill($passedData);
            $newModel->push();

            // Sao chép tất cả các môn học từ ngành nghề hiện có sang ngành nghề mới
            foreach ($originModel->monHoc as $monhoc) {
                $newMonHoc = $monhoc->replicate();
                $newMonHoc->nn_id = $newModel->getKey(); // Cập nhật nn_id cho môn học mới
                $newMonHoc->push();
            }
        });

        return response()->json($newModel);
    }


}
