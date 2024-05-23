<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\KhoaDaoTao;
use App\Http\Requests\KhoaDaoTaoEditRequest;
use App\Models\MonHoc;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class KhoaDaoTaoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $permissions = auth()->user()->permissions()->get()->map(function ($item) {
            return $item->permission_id;
        });

        // Lưu url vào session
        session(['parent_url:khoa-dao-tao' => $request->fullUrl()]);

        return view('qlsv.khoadaotao.khoadaotao_list', compact('permissions'));
    }

    public function paginate(Request $request)
    {
        $search = $request->search;
        $hdt_id = $request->hedaotao;
        $nn_id = $request->nganhnghe;
        $danhSach = KhoaDaoTao::with('heDaoTao', 'nganhNghe')
            ->where(function ($builder) use ($search) {
                $builder->whereRaw('lower(qlsv_khoadaotao.kdt_ma) like lower(?)', "%$search%")
                    ->orWhereRaw('lower(qlsv_khoadaotao.kdt_ten) like lower(?)', "%$search%");
            })
            ->where(function ($builder) use ($hdt_id) {
                if (auth()->user()->hasPermission('caodang') && auth()->user()->hasPermission('trungcap')) {
                    if (isset($hdt_id) && $hdt_id != -1) {
                        $builder->whereRaw('qlsv_khoadaotao.hdt_id = ?', $hdt_id);
                    }
                } else {
                    if (auth()->user()->hasPermission('caodang')) {
                        $builder->whereRaw('qlsv_khoadaotao.hdt_id = ?', 4);
                    } else if (auth()->user()->hasPermission('trungcap')) {
                        $builder->whereRaw('qlsv_khoadaotao.hdt_id = ?', 5);
                    }
                }
            })
            ->where(function ($builder) use ($nn_id) {
                if (isset($nn_id) && $nn_id != -1) {
                    $builder->whereRaw('qlsv_khoadaotao.nn_id = ?', "$nn_id");
                }
            })
            ->withExists('lopHoc')
            ->orderBy('kdt_id', 'desc')
            ->paginate(10)
            ->setPath(route('khoa-dao-tao.index'))
            ->appends([
                'search' => $search,
                'nganhnghe' => $nn_id,
                'hedaotao' => $hdt_id
            ])
            ->onEachSide(2);
        foreach ($danhSach as $khoaDaoTao) {
            $khoaDaoTao->edit_url = route('khoa-dao-tao.hoc-ky', $khoaDaoTao);
        }

        return response()
            ->json($danhSach);
    }

    public function hocKy(KhoaDaoTao $khoaDaoTao)
    {
        $id = $khoaDaoTao->kdt_id;
        $parentUrl = session('parent_url:khoa-dao-tao', '/khoa-dao-tao');
        return view('qlsv.khoadaotao.khoadaotao_hocky', compact(['id', 'parentUrl']));
    }

    public function getDanhSachHocKy(KhoaDaoTao $khoaDaoTao)
    {
        $hocKy = $khoaDaoTao->monHoc()->get();
        //$hocKy = $khoaDaoTao->monHoc()->get();
        // foreach ($hocKy as $monhoc) {
        //     $bangDiem = $monhoc->bangDiem()->whereHas('lopHoc', function ($builder) use ($khoaDaoTao, $monhoc) {
        //         $builder->whereKdtId($khoaDaoTao->kdt_id)
        //             ->whereKdtHocky($monhoc->pivot->kdt_mh_hocky);
        //     })
        //         ->select('qlsv_bangdiem.kdt_hocky as kdt_hocky')
        //         ->first();

        //     if ($bangDiem) {
        //         $monhoc->bang_diem_exists = $bangDiem->kdt_hocky == $monhoc->pivot->kdt_mh_hocky;
        //     }
        // }
        // return response()->json($hocKy);
        return response()->json($hocKy, 200, [], JSON_NUMERIC_CHECK);
    }

    public function updateHocKy(Request $request, KhoaDaoTao $khoaDaoTao)
    {
        // Danh sách học kỳ và môn học
        $passedSemesters = $request->semesters;

        // Sync data học kỳ và môn học
        $dataSync = [];
        $hocKy = 0;

        // if ($semester['mh_ids']) {
        //     foreach ($semester['mh_ids'] as $mhIndex => $mhId) {
        //         if ($hocKy < $semester['kdt_mh_hocky']) {
        //             $hocKy = $semester['kdt_mh_hocky'];
        //         }
        //         $dataSync[] = [
        //             'mh_id' => $mhId,
        //             'kdt_mh_hocky' => $semester['kdt_mh_hocky'],
        //             'kdt_mh_index' => $mhIndex,
        //         ];
        //     }
        // }
        // if ($semester['kdt_mh_apdung']) {
        //     foreach ($semester['kdt_mh_apdung'] as $mhIndex => $kdt_mh_apdung) {
        //         $dataSync[] = [
        //             'kdt_mh_apdung' => $kdt_mh_apdung,
        //         ];
        //     }
        // }


        if ($passedSemesters['mh_ids'] && $passedSemesters['kdt_mh_apdung']) {
            $maxCount = max(count($passedSemesters['mh_ids']), count($passedSemesters['kdt_mh_apdung']));
            for ($i = 0; $i < $maxCount; $i++) {
                $mhId = $passedSemesters['mh_ids'][$i] ?? null;
                $kdtMhApdung = $passedSemesters['kdt_mh_apdung'][$i] ?? null;

                if ($mhId !== null && $kdtMhApdung !== null) {
                    $dataSync[] = [
                        'mh_id' => $mhId,
                        'kdt_mh_hocky' => 1,
                        'kdt_mh_index' => 0,
                        'kdt_mh_apdung' => $kdtMhApdung
                    ];
                }
            }
        }


        // Lưu dữ liệu
        DB::transaction(function () use ($khoaDaoTao, $dataSync, $hocKy) {
            $khoaDaoTao->kdt_hocky = $hocKy;
            $khoaDaoTao->save();
            $khoaDaoTao->monHoc()->detach();
            $khoaDaoTao->monHoc()->sync($dataSync);
        });

        return response()->json($dataSync);
    }

    public function getAllKhoaDaoTao(Request $request)
    {
        $hdt_id = intval($request->hedaotao);
        $danhSach = KhoaDaoTao::with('heDaoTao')
            ->where(function ($builder) use ($hdt_id) {
                if (isset($hdt_id) && $hdt_id != -1 && $hdt_id != 0) {
                    $builder->whereRaw('qlsv_khoadaotao.hdt_id = ?', "$hdt_id");
                }
            })->orderBy('kdt_id', 'desc')->get();
        return response()->json($danhSach);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(KhoaDaoTaoEditRequest $request)
    {
        DB::transaction(function () use ($request) {

            $passedData = $request->only(['hdt_id', 'nn_id', 'kdt_ma', 'kdt_ten', 'kdt_khoa', 'kdt_he']);
            $khoaDaoTaoModel = new KhoaDaoTao;
            $khoaDaoTaoModel->fill($passedData);
            $khoaDaoTaoModel->save();

            // Danh sách học kỳ và môn học
            $monHocModel = MonHoc::where('nn_id', $passedData['nn_id'])->get();

            // Sync data học kỳ và môn học
            $dataSync = [];
            $hocKy = 1;
            foreach ($monHocModel as $mhIndex => $monHoc) {
                $dataSync[] = [
                    'mh_id' => $monHoc->mh_id,
                    'kdt_mh_hocky' => $hocKy,
                    'kdt_mh_index' => $mhIndex,
                ];
            }

            // Lưu dữ liệu
            DB::transaction(function () use ($khoaDaoTaoModel, $dataSync, $hocKy) {
                $khoaDaoTaoModel->kdt_hocky = $hocKy;
                $khoaDaoTaoModel->save();
                $khoaDaoTaoModel->monHoc()->detach();
                $khoaDaoTaoModel->monHoc()->sync($dataSync);
            });

            return response()
                ->json($khoaDaoTaoModel);
        });
    }

    public function findMissingSubjects($allSubjects, $currentSubjects, $property)
    {
        // Convert arrays to collections
        $allSubjectsCollection = $allSubjects;
        $currentSubjectsCollection = new Collection($currentSubjects);

        // Get the subjects that are in $allSubjectsCollection but not in $currentSubjectsCollection
        $missingSubjects = $allSubjectsCollection->whereNotIn($property, $currentSubjectsCollection->pluck($property));

        return $missingSubjects->all();
    }

    public function getSyncMonHocTheoNN(Request $request)
    {
        $dataSync = [];

        DB::transaction(function () use ($request, &$dataSync) {
            $kdt_id = $request->kdt_id;
            $DsMh_kdt_current = $request->DsMh_kdt_current;
            $nn_id = $request->nn_id;


            // Danh sách học kỳ và môn học
            $monHocModel = MonHoc::where('nn_id', $nn_id)->get();
            $property = 'mh_id';

            $missingSubjects = $this->findMissingSubjects($monHocModel, $DsMh_kdt_current, $property);
            // dd($missingSubjects);
            // Sync data học kỳ và môn học
            $hocKy = 5;
            foreach ($missingSubjects as $mhIndex => $monHoc) {
                $dataSync[] = [
                    'mh_id' => $monHoc->mh_id,
                    'mh_sodonvihoctrinh' => $monHoc->mh_sodonvihoctrinh,
                    'mh_ten' => $monHoc->mh_ten,
                    'mh_ma' => $monHoc->mh_ma,
                    'mh_sotiet' => $monHoc->mh_sotiet,
                    'mh_tichluy' => $monHoc->mh_tichluy,
                    'kdt_mh_hocky' => $hocKy,
                    'kdt_mh_index' => $mhIndex,
                ];
            }
        });

        return response()
            ->json($dataSync);
    }

    public function updateSyncMonHocTheoNN(Request $request, $kdt_id)
    {
        DB::transaction(function () use ($request, $kdt_id) {
            $listMonHocSync = $request->listMonHocSync;

            $dataSync = [];
            $hocKy = 0;
            foreach ($listMonHocSync as $mhIndex => $monHoc) {
                $dataSync[] = [
                    'kdt_id' => $kdt_id,
                    'mh_id' => $monHoc['mh_id'],
                    'kdt_mh_index' => $mhIndex,
                    'kdt_mh_hocky' => $hocKy,
                    'kdt_mh_apdung' => 0
                ];
            }

            // Lưu dữ liệu
            DB::transaction(function () use ($dataSync) {
                foreach ($dataSync as $monhoc) {
                    DB::table('qlsv_khoadaotao_monhoc')->insert($monhoc);
                }
            });
        });

        return response()->json();
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getKhoaDaoTao($id)
    {
        $model = KhoaDaoTao::with('heDaoTao')->find($id);
        return response()
            ->json($model);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(KhoaDaoTaoEditRequest $request, $id)
    {
        $passedData = $request->only(['hdt_id', 'nn_id', 'kdt_ma', 'kdt_ten', 'kdt_khoa', 'kdt_he']);
        $model = KhoaDaoTao::find($id);
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
        $model = KhoaDaoTao::find($id);
        if (!$model->lopHoc()->exists()) {
            $model->delete();
            return response()
                ->json($model);
        }
        return response()
            ->json($model, 422);
    }

    /**
     * Nhân đôi
     * @param [type] $id
     * @return void
     * @author ttdat
     * @version 1.0
     */
    public function duplicate(KhoaDaoTaoEditRequest $request, $id)
    {
        $passedData = $request->only(['kdt_ma', 'kdt_ten']);
        $originModel = KhoaDaoTao::find($id);
        $model = null;
        DB::transaction(function () use ($originModel, $passedData) {
            $model = $originModel->replicate();
            $model->fill($passedData);
            $model->push();

            $originModel->load('monHoc');
            $relations = collect();
            foreach ($originModel->monHoc as $monHoc) {
                $relations->push([
                    'mh_id' => $monHoc->mh_id,
                    'kdt_mh_index' => $monHoc->pivot->kdt_mh_index,
                    'kdt_mh_hocky' => $monHoc->pivot->kdt_mh_hocky,
                ]);
            }
            $model->monHoc()->sync($relations);
        });

        return response()
            ->json($model);
    }
}
