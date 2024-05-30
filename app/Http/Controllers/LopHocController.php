<?php

namespace App\Http\Controllers;

use App\Models\MonHoc;
use Illuminate\Http\Request;
use App\Models\LopHoc;
use App\Models\SinhVien;
use App\Models\QuyetDinh;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\LopHocEditRequest;
use App\Models\User;
use App\Enums\UserType;
use App\Enums\UserStatus;
use App\Models\KhoaDaoTao;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class LopHocController extends Controller
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
        session(['parent_url:lop-hoc' => $request->fullUrl()]);
        return view('qlsv.lophoc.lophoc_list', compact('permissions'));
    }

    public function preThemSinhVienExcel(LopHoc $lopHoc)
    {
        $id = $lopHoc->lh_id;
        return view('qlsv.lophoc.lophoc_import_sv', compact('id'));
    }

    public function themSinhVienExcel(Request $request, $lh_id)
    {
        $validator = \Validator::make($request->all(), [
            'data.*.sv_ma' => 'required|max:255|min:4|unique:users,username,0,user_id',
            'data.*.sv_ho' => 'required|max:255',
            'data.*.sv_ten' => 'required|max:255',
            'data.*.sv_ngaysinh' => 'required',
            'password' => 'required|max:255|min:6',
        ]);
        $validator->setAttributeNames(['password' => 'Mật khẩu']);
        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }
        $dsSinhVien = $request->data;
        $password = $request->password;
        DB::transaction(function () use ($dsSinhVien, $password, $lh_id) {
            foreach ($dsSinhVien as $sinhVienTmp) {
                $sinhVien = new SinhVien;
                $sinhVien->fill($sinhVienTmp);

                $userModel = new User;
                $userModel->password = bcrypt($password);
                $userModel->type = UserType::SINHVIEN;
                $userModel->status = UserStatus::ACTIVE;
                $userModel->username = $sinhVien->sv_ma;
                if ($sinhVien->sv_ho) {
                    $userModel->name = $sinhVien->sv_ho . ' ' . $sinhVien->sv_ten;
                } else {
                    $userModel->name = $sinhVien->sv_ten;
                }
                $userModel->save();
                $userModel->permissions()->sync(\App\Enums\Permission::STUDENT);

                $sinhVien->user_id = $userModel->user_id;
                $sinhVien->sv_ngaysinh = $sinhVienTmp['sv_ngaysinh'];
                $sinhVien->save();
                if ($lh_id != null) {
                    $sinhVien->lopHoc()->sync($lh_id);
                }
            }
        });
    }

    public function getAllLopHoc()
    {
        $danhSach = LopHoc::orderBy('lh_id', 'desc')->get();
        return response()->json($danhSach);
    }

    public function getLopHocTheoDotThi($dt_id, Request $request)
    {
        $search = $request->search;
        $nk_id = $request->nienkhoa;
        $hdt_id = $request->chuongtrinh;
        $danhSach = LopHoc::with('khoaDaoTao', 'nienKhoa', 'quyetDinh')
            ->where(function ($builder) use ($search) {
                $builder->whereRaw('lower(qlsv_lophoc.lh_ma) like lower(?)', "%$search%")
                    ->orWhereRaw('lower(qlsv_lophoc.lh_ten) like lower(?)', "%$search%");
            })
            ->where(function ($builder) use ($dt_id) {
                $builder->whereRaw('qlsv_lophoc.lh_id in (select bd.lh_id from qlsv_dotthi_bangdiem bd where bd.dt_id = ?)', $dt_id);
            })
            ->where(function ($builder) use ($nk_id) {
                if (isset($nk_id) && $nk_id != -1) {
                    $builder->whereRaw('qlsv_lophoc.nk_id = ?', "$nk_id");
                }
            })
            ->whereHas('khoaDaoTao', function ($builder) use ($hdt_id) {
                if (isset($hdt_id) && $hdt_id != -1) {
                    $builder->whereRaw('qlsv_khoadaotao.hdt_id = ?', "$hdt_id");
                }
            })
            ->orderBy('qlsv_lophoc.lh_id', 'desc')
            ->paginate(10)
            ->setPath(route('dot-thi.detail', $dt_id))
            ->appends([
                'search' => $search,
                'nienkhoa' => $nk_id,
                'chuongtrinh' => $hdt_id
            ])
            ->onEachSide(2);
        foreach ($danhSach as $lophoc) {
            $lophoc->monhoc_url = route('dot-thi.mon-hoc', ['lh_id' => $lophoc->lh_id, 'dt_id' => $dt_id]);
            $lophoc->ketqua_url = route('dot-thi.ket-qua-hoc-tap', ['lh_id' => $lophoc->lh_id, 'dt_id' => $dt_id]);
            $lophoc->import_excel_url = route('dot-thi.nhap-diem-excel', ['lh_id' => $lophoc->lh_id, 'dt_id' => $dt_id]);
            $lophoc->export_diem_url = route('export-diem-dotthi-theo-lop', ['lh_id' => $lophoc->lh_id, 'dt_id' => $dt_id]);
            $lophoc->xoa_url = route('dot-thi.xoa-lop-hoc', ['lh_id' => $lophoc->lh_id, 'dt_id' => $dt_id]);
        }
        return response()->json($danhSach);
    }

    public function getLopHocTheoDotXetTotNghiep($dxtn_id, Request $request)
    {
        $search = $request->search;
        $nk_id = $request->nienkhoa;
        $hdt_id = $request->chuongtrinh;

        $danhSach = LopHoc::with('khoaDaoTao', 'nienKhoa', 'quyetDinh', 'dotxettotnghiep_sinhvien')
            ->where(function ($builder) use ($search) {
                $builder->whereRaw('lower(qlsv_lophoc.lh_ma) like lower(?)', "%$search%")
                    ->orWhereRaw('lower(qlsv_lophoc.lh_ten) like lower(?)', "%$search%");
            })
            ->where(function ($builder) use ($dxtn_id) {
                $builder->whereRaw('qlsv_lophoc.lh_id in (select dtdx.lh_id from qlsv_dotxettotnghiep_sinhvien dtdx where dtdx.dxtn_id = ?)', $dxtn_id);
            })
            ->where(function ($builder) use ($nk_id) {
                if (isset($nk_id) && $nk_id != -1) {
                    $builder->whereRaw('qlsv_lophoc.nk_id = ?', "$nk_id");
                }
            })
            ->whereHas('khoaDaoTao', function ($builder) use ($hdt_id) {
                if (isset($hdt_id) && $hdt_id != -1) {
                    $builder->whereRaw('qlsv_khoadaotao.hdt_id = ?', "$hdt_id");
                }
            })
            ->orderBy('qlsv_lophoc.lh_id', 'desc')
            ->paginate(10)
            ->appends([
                'search' => $search,
                'nienkhoa' => $nk_id,
                'chuongtrinh' => $hdt_id
            ])
            ->onEachSide(2);
        foreach ($danhSach as $lophoc) {
            $lophoc->ketqua_url = route('dot-xet-tot-nghiep.ket-qua-hoc-tap', ['lh_id' => $lophoc->lh_id, 'dxtn_id' => $dxtn_id]);
        }
        return response()->json($danhSach);




        // $search = $request->search;
        // $nk_id = $request->nienkhoa;
        // $hdt_id = $request->chuongtrinh;
        // $danhSach = LopHoc::with('khoaDaoTao', 'nienKhoa', 'quyetDinh')
        //     ->where(function ($builder) use ($search) {
        //         $builder->whereRaw('lower(qlsv_lophoc.lh_ma) like lower(?)', "%$search%")
        //             ->orWhereRaw('lower(qlsv_lophoc.lh_ten) like lower(?)', "%$search%");
        //     })
        //     ->where(function ($builder) use ($dxtn_id) {
        //         $builder->whereRaw('qlsv_lophoc.lh_id in (select dtdx.lh_id from qlsv_dotthi_dotxettotnghiep dtdx where dtdx.dxtn_id = ?)', $dxtn_id);
        //     })
        //     ->where(function ($builder) use ($nk_id) {
        //         if (isset($nk_id) && $nk_id != -1) {
        //             $builder->whereRaw('qlsv_lophoc.nk_id = ?', "$nk_id");
        //         }
        //     })
        //     ->whereHas('khoaDaoTao', function ($builder) use ($hdt_id) {
        //         if (isset($hdt_id) && $hdt_id != -1) {
        //             $builder->whereRaw('qlsv_khoadaotao.hdt_id = ?', "$hdt_id");
        //         }
        //     })
        //     ->orderBy('qlsv_lophoc.lh_id', 'desc')
        //     ->paginate(10)
        //     ->appends([
        //         'search' => $search,
        //         'nienkhoa' => $nk_id,
        //         'chuongtrinh' => $hdt_id
        //     ])
        //     ->onEachSide(2);
        // foreach ($danhSach as $lophoc) {
        //     $lophoc->ketqua_url = route('dot-xet-tot-nghiep.ket-qua-hoc-tap', ['lh_id' => $lophoc->lh_id, 'dxtn_id' => $dxtn_id]);
        // }
        // return response()->json($danhSach);
    }

   /*  public function paginate(Request $request)
    {
        $search = $request->search;
        $nk_id = $request->nienkhoa;
        $hdt_id = $request->chuongtrinh;
        $danhSach = LopHoc::with('khoaDaoTao', 'nienKhoa', 'quyetDinh')
            ->withExists('bangDiem')
            ->withCount('sinhVien')
            ->where(function ($builder) use ($search) {
                $builder->whereRaw('lower(qlsv_lophoc.lh_ma) like lower(?)', "%$search%")
                    ->orWhereRaw('lower(qlsv_lophoc.lh_ten) like lower(?)', "%$search%");
            })
            ->where(function ($builder) use ($nk_id) {
                if (isset($nk_id) && $nk_id != -1) {
                    $builder->whereRaw('qlsv_lophoc.nk_id = ?', "$nk_id");
                }
            })
            ->whereHas('khoaDaoTao', function ($builder) use ($hdt_id) {
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
            ->orderBy('lh_id', 'desc')
            ->paginate(10)
            ->setPath(route('lop-hoc.index'))
            ->appends([
                'search' => $search,
                'nienkhoa' => $nk_id,
                'chuongtrinh' => $hdt_id
            ])
            ->onEachSide(2);
        foreach ($danhSach as $lophoc) {
            $lophoc->edit_url = route('lop-hoc.chi-tiet', $lophoc);
            $lophoc->importexcel_url = route('lop-hoc.them-sinh-vien-excel', $lophoc);
            $lophoc->edit_monhoc_url = route('lop-hoc.hoc-ky', $lophoc);
        }
        return response()->json($danhSach);
    } */

    // NGUYEN PHU DINH
    public function paginate(Request $request)
    {
        $search = $request->search;
        $nk_id = $request->nienkhoa;
        $hdt_id = $request->chuongtrinh;
        $danhSach = LopHoc::with(['khoaDaoTao', 'nienKhoa', 'quyetDinh'])
            ->withExists('bangDiem')
            ->withCount('sinhVien')
            ->where(function ($builder) use ($search) {
                $builder->whereRaw('lower(qlsv_lophoc.lh_ma) like lower(?)', "%$search%")
                    ->orWhereRaw('lower(qlsv_lophoc.lh_ten) like lower(?)', "%$search%");
            })
            ->where(function ($builder) use ($nk_id) {
                if (isset($nk_id) && $nk_id != -1) {
                    $builder->whereRaw('qlsv_lophoc.nk_id = ?', "$nk_id");
                }
            })
            ->whereHas('khoaDaoTao', function ($builder) use ($hdt_id) {
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
            ->orderBy('lh_id', 'desc')
            ->paginate(10)
            ->setPath(route('lop-hoc.index'))
            ->appends([
                'search' => $search,
                'nienkhoa' => $nk_id,
                'chuongtrinh' => $hdt_id
            ])
            ->onEachSide(2);

        foreach ($danhSach as $lophoc) {
            $lophoc->edit_url = route('lop-hoc.chi-tiet', $lophoc);
            $lophoc->importexcel_url = route('lop-hoc.them-sinh-vien-excel', $lophoc);
            $lophoc->edit_monhoc_url = route('lop-hoc.hoc-ky', $lophoc);

            // Đếm số lượng sinh viên không có quyết định xóa tên
            $lophoc->sinh_vien_hien_tai = $lophoc->sinhVien()
                ->whereDoesntHave('quyetDinhXoaTen')
                ->count();
        }

        return response()->json($danhSach);
    }

    /**
     * @return void
     * @author hltphat
     * @version 1.0
     */
    public function chiTiet(LopHoc $lopHoc)
    {
        $id = $lopHoc->lh_id;
        $parentUrl = session('parent_url:lop-hoc', '/lop-hoc');
        return view('qlsv.lophoc.lophoc_create', compact(['id', 'parentUrl']));
    }

    /**
     * @return void
     * @author hltphat
     * @version 1.0
     */
    public function xoaSinhVienDanhSachThamGia(Request $request)
    {
        $lh_id = $request->only(['lh_id']);
        $sv_id = $request->only(['sv_id']);
        $lophoc = LopHoc::find($lh_id)->get()->sinhVien()->detach($sv_id);
        return response()->json($lophoc);
    }

    /**
     * @return void
     * @author hltphat
     * @version 1.0
     */
    public function getDanhSachSinhVien(LopHoc $lopHoc)
    {
        $id = $lopHoc->lh_id;
        $danhSach = $lopHoc->sinhVien()
            ->with(['quyetDinhXoaTen', 'quyetDinhTotNghiep', 'quyetDinhThemLop', 'user'])
            ->withCount([
                'sinhVienBangDiem' => function ($query) {
                    $query->whereNotNull('svd_first');
                }
            ])
            ->orderBy('sv_ma')->get();
        return response()->json($danhSach);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('qlsv.lophoc.lophoc_create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(LopHocEditRequest $request)
    {
        $passedData = $request->only(['lh_ten', 'lh_ma', 'kdt_id', 'nk_id', 'lh_ghichu', 'qd_id', 'lh_nienche']);
        $passedDataQD = $request->only(['qd_id', 'qd_ten', 'qd_ma', 'qd_ngay', 'qd_loai']);
        $model = new LopHoc;
        $model->fill($passedData);

        $permissions = auth()->user()->permissions()->get()->map(function ($item) {
            return $item->permission_id;
        });

        // Cao đẳng 6 học kỳ
        $lh_hockyDefault = 6;
        if ($permissions->contains("trungcap")) {
            // trung cấp 4 học kỳ
            $lh_hockyDefault = 4;
        }

        // Gán cứng giá trị lh_hocky
        $model->lh_hocky = $lh_hockyDefault;

        DB::transaction(function () use ($passedDataQD, $model) {
            if ($passedDataQD['qd_id'] == 0) {
                $modelQD = new QuyetDinh;
                $modelQD->fill($passedDataQD);
                $modelQD->qd_loai = 0;
                $modelQD->save();
                $model->qd_id = $modelQD->qd_id;
            } else if ($passedDataQD['qd_id'] == -1) {
                $model->qd_id = 0;
            }

            $model->save();

            // Copy thông tin khóa đào tạo
            $lopHoc = $model;
            $khoaDaoTao = $lopHoc->khoaDaoTao()->first();
            $danhSachMonHoc = DB::table('qlsv_khoadaotao_monhoc')->whereKdtId($khoaDaoTao->kdt_id)->where('kdt_mh_apdung', 1)->get();
            foreach ($danhSachMonHoc as $monHoc) {
                $exists = DB::table('qlsv_lophoc_monhoc')
                    ->whereLhId($lopHoc->lh_id)
                    ->whereMhId($monHoc->mh_id)
                    ->whereLhMhIndex($monHoc->kdt_mh_index)
                    ->whereLhMhHocky($monHoc->kdt_mh_hocky)
                    ->exists();
                if (!$exists) {
                    DB::table('qlsv_lophoc_monhoc')->insert([
                        'lh_id' => $lopHoc->lh_id,
                        'mh_id' => $monHoc->mh_id,
                        'lh_mh_index' => $monHoc->kdt_mh_index,
                        'lh_mh_hocky' => $monHoc->kdt_mh_hocky
                    ]);
                }
            }

            $lopHoc->lh_hocky = $khoaDaoTao->kdt_hocky;
            $lopHoc->save();
        });

        return response()->json($model);
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

    public function getSyncMonHocByKdt(Request $request)
    {
        $dataSync = [];

        DB::transaction(function () use ($request, &$dataSync) {
            $lh_id = $request->lh_id;
            $dsMh_lh_current = $request->dsMh_lh_current;

            // Lớp học
            $lopHocModel = LopHoc::where('lh_id', $lh_id)->get();
            $kdt_id = $lopHocModel[0]->kdt_id;

            // Danh sách môn học khóa đào tạo
            $khoaDaoTaoMonHocModel = DB::table('qlsv_khoadaotao_monhoc')
                ->where('kdt_id', $kdt_id)
                ->where('kdt_mh_apdung', 1)
                ->get();
            $property = 'mh_id';

            $missingSubjects = $this->findMissingSubjects($khoaDaoTaoMonHocModel, $dsMh_lh_current, $property);
            // Sync data học kỳ và môn học
            $hocKy = 5;
            foreach ($missingSubjects as $mhIndex => $monHoc) {
                $monHocModel = DB::table('qlsv_monhoc')
                    ->where('mh_id', $monHoc->mh_id)
                    ->first();

                $dataSync[] = [
                    'mh_id' => $monHoc->mh_id,
                    'mh_sodonvihoctrinh' => $monHocModel->mh_sodonvihoctrinh,
                    'mh_ten' => $monHocModel->mh_ten,
                    'mh_ma' => $monHocModel->mh_ma,
                    'mh_sotiet' => $monHocModel->mh_sotiet,
                    'mh_tichluy' => $monHocModel->mh_tichluy,
                    'kdt_mh_hocky' => $hocKy,
                    'kdt_mh_index' => $mhIndex,
                ];
            }

        });

        return response()
            ->json($dataSync);
    }

    public function updateSyncMonHocByKdt(Request $request, $lh_id)
    {
        DB::transaction(function () use ($request, $lh_id) {
            $listMonHocSync = $request->listMonHocSync;
            $soHocKy = $request->soHocKy;
            $dataSync = [];
            $hocKy = $soHocKy;
            foreach ($listMonHocSync as $mhIndex => $monHoc) {
                $dataSync[] = [
                    'lh_id' => $lh_id,
                    'mh_id' => $monHoc['mh_id'],
                    'lh_mh_index' => $mhIndex,
                    'lh_mh_hocky' => $hocKy,
                ];
            }

            // Lưu dữ liệu
            DB::transaction(function () use ($dataSync) {
                foreach ($dataSync as $monhoc) {
                    DB::table('qlsv_lophoc_monhoc')->insert($monhoc);
                }
            });
        });

        return response()->json();
    }


    public function delete_monHoc($mh_id)
    {
        DB::table('qlsv_lophoc_monhoc')->whereRaw('mh_id = ?', $mh_id)->delete();

        return response()->json();

    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $model = LopHoc::find($id);
        $model->load('khoaDaoTao');
        return response()->json($model);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return $this->show($id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(LopHocEditRequest $request, $id)
    {
        $passedData = $request->only(['lh_ten', 'lh_ma', 'kdt_id', 'nk_id', 'lh_ghichu', 'qd_id', 'lh_nienche']);
        $passedDataQD = $request->only(['qd_id', 'qd_ten', 'qd_ma', 'qd_ngay', 'qd_loai']);
        $model = LopHoc::find($id);
        $model->fill($passedData);

        DB::transaction(function () use ($passedDataQD, $model) {
            if ($passedDataQD['qd_id'] == 0) {
                $modelQD = new QuyetDinh;
                $modelQD->fill($passedDataQD);
                $modelQD->qd_loai = 0;
                $modelQD->save();
                $model->qd_id = $modelQD->qd_id;
            } else if ($passedDataQD['qd_id'] == -1) {
                $model->qd_id = 0;
            }

            $model->save();
        });

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
        $model = LopHoc::find($id);
        $model->delete();
        return response()->json($model);
    }

    public function hocKy(LopHoc $lopHoc)
    {
        $id = $lopHoc->lh_id;
        $parentUrl = session('parent_url:lop-hoc', '/lop-hoc');
        return view('qlsv.lophoc.lophoc_hocky', compact(['id', 'parentUrl']));
    }

    public function getDanhSachHocKy(LopHoc $lopHoc)
    {
        $hocKy = $lopHoc->monHoc()->get();
        foreach ($hocKy as $monhoc) {
            $bangDiem = $monhoc->bangDiem()->whereHas('lopHoc', function ($builder) use ($lopHoc, $monhoc) {
                $builder->whereLhId($lopHoc->lh_id)
                    ->whereKdtHocky($monhoc->pivot->lh_mh_hocky);
            })
                ->select('qlsv_bangdiem.kdt_hocky as kdt_hocky')
                ->first();
            if ($bangDiem) {
                $monhoc->bang_diem_exists = $bangDiem->kdt_hocky == $monhoc->pivot->lh_mh_hocky;
            }
        }
        // return response()->json($hocKy);
        return response()->json($hocKy, 200, [], JSON_NUMERIC_CHECK);
    }

    public function updateHocKy(Request $request, LopHoc $lopHoc)
    {
        // Danh sách học kỳ và môn học
        $passedSemesters = $request->semesters;
        // Sync data học kỳ và môn học
        $dataSync = [];
        $hocKy = 0;
        foreach ($passedSemesters as $semester) {
            if ($semester['mh_ids']) {
                foreach ($semester['mh_ids'] as $mhIndex => $mhId) {
                    if ($hocKy < $semester['lh_mh_hocky']) {
                        $hocKy = $semester['lh_mh_hocky'];
                    }
                    $dataSync[] = [
                        'mh_id' => $mhId,
                        'lh_mh_hocky' => $semester['lh_mh_hocky'],
                        'lh_mh_index' => $mhIndex
                    ];
                }
            }
        }
        // Lưu dữ liệu
        DB::transaction(function () use ($lopHoc, $dataSync, $hocKy) {
            $lopHoc->lh_hocky = $hocKy;
            $lopHoc->save();
            $lopHoc->monHoc()->detach();
            $lopHoc->monHoc()->sync($dataSync);
        });

        return response()->json($dataSync);
    }

    public function updateHocKyTab0(Request $request, LopHoc $lopHoc)
    {
        // Danh sách học kỳ và môn học
        $passedSemesters = $request->semesters;

        // Sync data học kỳ và môn học
        $dataSync = [];
        $hocKy = 0;

        if ($passedSemesters['mh_ids'] && $passedSemesters['lh_mh_hocky']) {
            $maxCount = max(count($passedSemesters['mh_ids']), count($passedSemesters['lh_mh_hocky']));
            for ($i = 0; $i < $maxCount; $i++) {
                $mhId = $passedSemesters['mh_ids'][$i] ?? null;
                $lh_mh_hocky = $passedSemesters['lh_mh_hocky'][$i] ?? null;
                $lh_mh_index = $passedSemesters['lh_mh_index'][$i] ?? null;

                if ($mhId !== null && $lh_mh_hocky !== null) {
                    $dataSync[] = [
                        'mh_id' => $mhId,
                        'lh_mh_hocky' => $lh_mh_hocky,
                        'lh_mh_index' => $lh_mh_index
                    ];
                }
            }
        }

        // Lưu dữ liệu
        DB::transaction(function () use ($lopHoc, $dataSync, $hocKy) {
            $lopHoc->lh_hocky = $hocKy;
            $lopHoc->save();
            $lopHoc->monHoc()->detach();
            $lopHoc->monHoc()->sync($dataSync);
        });

        return response()->json($dataSync);
    }

    public function duplicate(Request $request, $id)
    {
        $passedData = $request->only(['lh_ma', 'lh_ten']);
        $originModel = LopHoc::find($id);
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
                    'lh_mh_index' => $monHoc->pivot->lh_mh_index,
                    'lh_mh_hocky' => $monHoc->pivot->lh_mh_hocky,
                ]);
            }
            $model->monHoc()->sync($relations);
        });

        return response()
            ->json($model);
    }



}
