<?php

namespace App\Http\Controllers;

use App\Models\Log;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use App\Models\DotThi;
use App\Models\LopHoc;
use App\Models\DotXetTotNghiep;
use App\Models\DotThiBangDiem;
use App\Http\Requests\DotThiEditRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Excels\KetQuaHocTap;
use App\Models\DotThiDotXetTotNghiep;
use App\Models\QuyetDinh;
use App\Models\SinhVien;
use App\Models\User;



class LogController extends Controller
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
        session(['parent_url:nhat-ky' => $request->fullUrl()]);
        return view('qlsv.nhatky.nhatky_list', compact('permissions'));
    }


    public function dsLopHoc(Request $request, $lhId)
    {
        $reqSemester = $request->semester ?: 1;
        $export = new KetQuaHocTap;
        $data = $export->getKetQuaHocTap($reqSemester, $lhId, 0, true);

        // dd($data['danhSachSinhVien'][0]);
        //$sinhVien->notes
        $reqSemester = $data['reqSemester'];
        $reqYear = $data['reqYear'];
        $lopHoc = $data['lopHoc'];
        $danhSachSinhVien = $data['danhSachSinhVien'];
        // if ($request->sorttotnghiep) {
        //     foreach ($danhSachSinhVien as $sinhVien) {
        //         //sắp sếp
        //     }
        // }
        $danhSachNamHoc = $data['danhSachNamHoc'];
        $semesters = $data['semesters'];
        $sumTinChi = $data['sumTinChi'];
        $chunkedNotes = $data['notes']->splitIn(3);
        $danhSachDotThi =  DotThi::orderBy('dt_id', 'desc')->get();

        $parentUrl = session('parent_url:nhat-ky', '/nhat-ky');
        // Lưu url vào session
        session(['parent_url:nhat-ky/lop-hoc' => $request->fullUrl()]);

        foreach ($danhSachSinhVien as $sv) {
                    $svLog = Log::where('sv_id', '=', $sv->sv_id)->orderBy('log_id', 'desc')->first();
                        $sv->svLog = $svLog;

        }

        // dd(date("d/m/Y h:m:s", strtotime($danhSachSinhVien[0]->svLog->created_at)));
        return view('qlsv.nhatky.nhatky_lophoc', compact([
            'parentUrl',
            'reqSemester',
            'reqYear',
            'lopHoc',
            'danhSachSinhVien',
            'danhSachNamHoc',
            'semesters',
            'chunkedNotes',
            'sumTinChi',
            'danhSachDotThi',
            'lhId'
        ]));
    }

    public function detail(Request $request, $sv_id)
    {
        $permissions = auth()->user()->permissions()->get()->map(function ($item) {
            return $item->permission_id;
        });
        $sinhvien = SinhVien::find($sv_id);
        $lhId = $sinhvien->lophoc[0]->lh_id;
        $parentUrl = session('parent_url:nhat-ky/lop-hoc', "/nhap-ky/" . $lhId . "/lop-hoc");
        return view('qlsv.nhatky.nhatky_chitiet', compact(['permissions', 'sv_id', 'sinhvien', 'parentUrl']));
    }


    public function paginateNhatKy(Request $request, $sv_id)
    {
        $search = $request->search;
        $dt_thang = $request->tunam;
        $dt_nam = $request->dennam;
        $f_type = $request->f_type;
        if ($dt_thang != null && $dt_nam != null) {
            $dt_date = $dt_nam . "-" . $dt_thang;
        } else {
            $dt_date = null;
        }
        $hdt_id = $request->chuongtrinh;

        $danhSachSinhVien = Log::withExists(['SinhVien'])
            ->with(['SinhVien.LopHoc'])
            ->where('sv_id', '=', $sv_id)
            ->where(function ($builder) use ($dt_date, $f_type) {
                if (isset($dt_date) && $f_type != 1) {
                    $month = date('Y-m', strtotime($dt_date));
                    $builder->whereRaw("DATE_FORMAT(qlsv_log.created_at, '%Y-%m') = ?", [$month]);
                }
            })

            // ->whereHas('SinhVien.LopHoc.KhoaDaoTao.HeDaoTao', function ($builder) use ($hdt_id) {
            //     if (isset($hdt_id) && $hdt_id != -1) {
            //         if ($hdt_id == 4) {
            //             if (auth()->user()->hasPermission('caodang')) {
            //                 $builder->whereRaw('qlsv_hedaotao.dt_hdt_id = ?', 4);
            //             }
            //         } else if ($hdt_id == 5) {
            //             if (auth()->user()->hasPermission('trungcap')) {
            //                 $builder->whereRaw('qlsv_hedaotao.dt_hdt_id = ?', 5);
            //             }
            //         }
            //     } else {
            //         if (auth()->user()->hasPermission('caodang')) {
            //             $builder->whereRaw('qlsv_hedaotao.dt_hdt_id = ?', 4);
            //         } else if (auth()->user()->hasPermission('trungcap')) {
            //             $builder->whereRaw('qlsv_hedaotao.dt_hdt_id = ?', 5);
            //         }
            //     }
            // })
            // ->select('hdt.*')
            ->orderBy('log_id', 'desc')
            ->paginate(10)
            ->setPath(route('nhat-ky.index'))
            ->onEachSide(2);

        // dd($danhSachSinhVien);

        ///dd($danhSachSinhVien[0]->SinhVien->LopHoc[0]->KhoaDaoTao->HeDaoTao);
        return response()->json($danhSachSinhVien);
    }


    public function paginate(Request $request)
    {
        $search = $request->search;
        $nk_id = $request->nienkhoa;
        $hdt_id = $request->chuongtrinh;
        $danhSach = LopHoc::with('khoaDaoTao', 'nienKhoa')
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
            ->setPath(route('nhat-ky.index'))
            ->appends([
                'search' => $search,
                'nienkhoa' => $nk_id,
                'chuongtrinh' => $hdt_id
            ])
            ->onEachSide(2);
        foreach ($danhSach as $lophoc) {
            $lophoc->show_url = route('nhap-diem.show', $lophoc);
            $lophoc->result_url = route('nhap-diem.ket-qua-hoc-tap', $lophoc);
            $lophoc->themdotthi = DB::table('qlsv_lophoc_monhoc as kdt')
                ->join('qlsv_monhoc as mh', 'mh.mh_id', '=', 'kdt.mh_id')
                ->where('kdt.lh_id', $lophoc->lh_id)
                ->selectRaw('SUM(CASE WHEN mh.mh_loai = 5 THEN 1 ELSE 0 END) > 0 as tontaiBC,
                    SUM(CASE WHEN mh.mh_loai = 4 THEN 1 ELSE 0 END) > 0 as tontaiLT,
                    SUM(CASE WHEN mh.mh_loai = 3 THEN 1 ELSE 0 END) > 0 as tontaiTH,
                    SUM(CASE WHEN mh.mh_loai = 2 THEN 1 ELSE 0 END) > 0 as tontaiCT')
                ->first();
        }
        return response()->json($danhSach);
    }
}
