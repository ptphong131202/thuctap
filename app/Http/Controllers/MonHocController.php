<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MonHoc;
use App\Http\Requests\MonHocEditRequest;
use Illuminate\Support\Facades\DB;

class MonHocController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $permissions = auth()->user()->permissions()->get()->map(function ($item) {
            return $item->permission_id;
        });
        return view('qlsv.monhoc.monhoc_list', compact('permissions'));
    }

    public function preThemMonHocExcel()
    {
        $permissions = auth()->user()->permissions()->get()->map(function ($item) {
            return $item->permission_id;
        });
        return view('qlsv.monhoc.monhoc_import', compact('permissions'));
    }

    public function themMonHocExcel(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'data.*.mh_ma' => 'required|max:255',
            'data.*.mh_ten' => 'required|max:255',
            'data.*.mh_sodonvihoctrinh' => 'required|integer',
            'data.*.mh_sotiet' => 'required|integer',
            'nn_id' => 'required|max:255',
        ]);
        $validator->setAttributeNames(['nn_id' => 'Ngành nghề']);
        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }
        $dsMonHoc = $request->data;
        $hdt_id = $request->hdt_id;
        $nn_id = $request->nn_id;
        DB::transaction(function () use ($dsMonHoc, $hdt_id, $nn_id) {
            foreach ($dsMonHoc as $monHocTmp) {
                $monHoc = new MonHoc;
                $monHoc->fill($monHocTmp);
                $monHoc->hdt_id = $hdt_id;
                $monHoc->nn_id = $nn_id;
                $monHoc->save();
            }
        });
    }

    public function paginate(Request $request)
    {
        $search = $request->search;
        $hdt_id = $request->hedaotao;
        $nn_id = $request->nganhnghe;
        $mh_loai = $request->loai;
        $danhSachMonHoc = MonHoc::withTrashed()
            ->withExists('bangDiem')->orderBy('mh_id', 'desc')
            ->leftJoin('qlsv_nganhnghe', 'qlsv_monhoc.nn_id', '=', 'qlsv_nganhnghe.nn_id')
            ->where(function ($builder) use ($search) {
                $builder->whereRaw('lower(qlsv_monhoc.mh_ma) like lower(?)', "%$search%")
                    ->orWhereRaw('lower(qlsv_monhoc.mh_ten) like lower(?)', "%$search%");
            })
            ->where(function ($builder) use ($hdt_id) {
                if (auth()->user()->hasPermission('caodang') && auth()->user()->hasPermission('trungcap')) {
                    if (isset($hdt_id) && $hdt_id != -1) {
                        $builder->whereRaw('qlsv_monhoc.hdt_id = ?', "$hdt_id");
                    }
                } else {
                    if ($hdt_id == null || $hdt_id == -1) {
                        $builder->whereRaw('qlsv_monhoc.hdt_id = ?', 0)->orWhereRaw('qlsv_monhoc.hdt_id = ?', auth()->user()->hasPermission('caodang') == true ? 4 : 5);
                    } else {
                        $builder->whereRaw('qlsv_monhoc.hdt_id = ?', $hdt_id);
                    }
                }
            })
            ->where(function ($builder) use ($mh_loai) {
                if (isset($mh_loai) && $mh_loai != -1) {
                    $builder->whereRaw('qlsv_monhoc.mh_loai = ?', "$mh_loai");
                }
            })
            ->where(function ($builder) use ($nn_id) {
                if (isset($nn_id) && $nn_id != -1) {
                    $builder->whereRaw('qlsv_monhoc.nn_id = ?', "$nn_id");
                }
            })
            ->select('qlsv_monhoc.*', 'qlsv_nganhnghe.nn_ten')
            ->paginate(10)
            ->setPath(route('mon-hoc.index'))
            ->appends([
                'search' => $search,
                'nganhnghe' => $nn_id,
                'hedaotao' => $hdt_id
            ])
            ->onEachSide(2);

        // return response()->json($danhSachMonHoc);
        return response()->json($danhSachMonHoc, 200, [], JSON_NUMERIC_CHECK);

    }

    public function getAllMonHoc(Request $request)
    {
        $monHocs = MonHoc::orderBy('mh_ten', 'asc');

        if ($request->hdt_id) {
            $monHocs->whereIn('hdt_id', [$request->hdt_id, 0]);
        } else {
            $monHocs->where('hdt_id', 0);
        }

        if ($request->nn_id) {
            $monHocs->whereIn('nn_id', [$request->nn_id, 0]);
        } else {
            $monHocs->where('nn_id', 0);
        }

        $monHocs = $monHocs->get();
        return response()->json($monHocs);
    }

    public function getAllMonHocByKhoaDaoTao(Request $request)
    {
        $monHocs = MonHoc::orderBy('mh_ten', 'asc');

        $monHocs = $monHocs->join('qlsv_khoadaotao_monhoc', 'qlsv_monhoc.mh_id', '=', 'qlsv_khoadaotao_monhoc.mh_id')
            ->where('qlsv_khoadaotao_monhoc.kdt_id', $request->kdt_id)
            ->where('qlsv_khoadaotao_monhoc.kdt_mh_apdung', 1)
            ->get();

        // return response()->json($monHocs);
        return response()->json($monHocs, 200, [], JSON_NUMERIC_CHECK);
    }

    public function select2Ajax(Request $request)
    {
        $search = $request->search;
        $pagi = MonHoc::whereRaw('LOWER(mh_ten) LIKE ?', [strtolower("%$search%")])->orderBy('mh_ten', 'asc')->paginate(10);
        $list = $pagi->map(function ($item) {
            return [
                'id' => $item->mh_id,
                'text' => $item->mh_ten,
            ];
        });
        return response()->json([
            'items' => $list,
            'total_count' => $pagi->total(),
        ]);
    }

    public function select2AjaxSelect(MonHoc $monHoc)
    {
        return [
            'id' => $monHoc->mh_id,
            'text' => $monHoc->mh_ten,
        ];
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('qlsv.monhoc.monhoc_create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(MonHocEditRequest $request)
    {
        $passedData = $request->only(['hdt_id', 'nn_id', 'mh_ma', 'mh_ten', 'mh_ghichu', 'mh_sodonvihoctrinh', 'mh_giangvien', 'mh_sotiet', 'mh_tichluy', 'mh_loai']);
        $model = new MonHoc;
        $model->fill($passedData);
        $model->save();
        return response()
            ->json($model);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getMonHoc($id)
    {
        $model = MonHoc::find($id);
        return response()->json($model, 200, [], JSON_NUMERIC_CHECK);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(MonHocEditRequest $request, $id)
    {
        $passedData = $request->only(['hdt_id', 'nn_id', 'mh_ma', 'mh_ten', 'mh_ghichu', 'mh_sodonvihoctrinh', 'mh_giangvien', 'mh_sotiet', 'mh_tichluy', 'mh_loai']);
        $model = MonHoc::find($id);
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
        $model = MonHoc::withExists('bangDiem')->find($id);
        if (!$model->bang_diem_exists) {
            $model->delete();
            return response()
                ->json($model);
        }
        return response()
            ->json($model, 422);
    }

    public function restore($id)
    {
        $model = MonHoc::withTrashed()->find($id);

        if ($model) {
            if (!$model->bangDiem()->exists()) {
                // Kiểm tra xem có bản ghi liên quan trong bảng bangDiem hay không
                $model->restore(); // Khôi phục bản ghi đã bị xóa
                return response()->json($model);
            }

            return response()->json($model, 422, [], JSON_NUMERIC_CHECK);
        }

        return response()->json(['message' => 'Không tìm thấy bản ghi'], 404);
    }

}
