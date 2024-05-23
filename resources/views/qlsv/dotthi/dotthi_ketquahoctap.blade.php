@extends('layouts.admin')

@section('header')
<h1>
    Kết quả học tập lớp {{ $lopHoc->lh_ma }} trong đợt thi {{$dotThi->dt_ten}}
</h1>
<ol class="breadcrumb">
    <li><a href="/"><i class="fa fa-dashboard"></i> Trang chủ</a></li>
    <li class="active">Kết quả học kỳ</li>
</ol>
@endsection

@section('content')
<div class="row">
    <div class="col-md-12 col-sm-12">
        <a href="/dot-thi/{{$dotThi->dt_id}}" style="margin-bottom:6px;"
                title="Trở về danh sách"
                class="btn btn-default">
            <i class="fa fa-share"></i> Trở về danh sách
        </a>
        <div class="box box-widget">
            <div class="box-body">
                <div class="col-lg-4 form-group ">
                    <label>Thông tin sinh viên</label>
                    <input type="text" class="form-control" id="txtThongTin" placeholder="Nhập tên hoặc mã số..." onkeyup="timKiem();"/>
                </div>
                @if (!$danhSachNamHoc->isEmpty() && !$danhSachSinhVien->isEmpty())
                    <div style="text-align: right">
                        {{-- <button onclick="xuatWord();" style="margin-left:6px !important"
                            class="btn btn-primary  no-margin">
                            <i class="fa fa-file-word-o"></i> Xuất word (<span class="sosinhvien">0</span>)
                        </button> --}}
                        {{-- <a href="{{ route('export-diem-monhoc-dotthi-theo-lop-full', ['lh_id'=> $lopHoc->lh_id, 'dt_id'=> $dotThi->dt_id]) }}"
                            class="btn bg-olive  no-margin">
                            <i class="fa fa-file-excel-o"></i> Xuất bảng điểm tổng hợp
                        </a> --}}

                        <a href="{{ route('export-danh-sach-xet-thi-tot-nghiep', ['lh_id'=> $lopHoc->lh_id, 'dt_id'=> $dotThi->dt_id]) }}"
                            class="btn bg-olive  no-margin">
                            <i class="fa fa-file-excel-o"></i> Xuất danh sách dự xét thi tốt nghiệp
                        </a>
                        {{-- <button onclick="$('#modal-chondotthi').modal('show')"
                            class="btn btn-success no-margin">
                            <i class="fa fa-plus"></i> Chọn đợt xét tốt nghiệp (<span class="sosinhvien">0</span>)
                        </button> --}}
                    </div>
                @endif
                @if (!$danhSachNamHoc->isEmpty() && !$danhSachSinhVien->isEmpty())
                <div class="clearfix"></div>
                <div class="table-responsive">

                <table class="table table-striped table-hover table-bordered no-margin " id="table-sinhvien">
                    <thead>
                        <tr>
                            {{-- <th rowspan="2" class="text-center"><input type="checkbox" id="checkall" onchange="checkAllXuatWord()"/></th> --}}
                            {{-- <th rowspan="2" class="text-center w-3">Xuất word</th> --}}
                            <th rowspan="3" class="w-3 text-center">#</th>
                            <th rowspan="3" class="w-10 text-center">MSHS/MSSV</th>
                            <th rowspan="3" colspan="2" class="text-center">Họ và tên</th>
                            @foreach ($danhSachNamHoc as $yIndex => $year)
                                @foreach ($year->semesters as $sIndex => $semester)
                                    @foreach ($semester->monHoc as $monHoc)
                                    <th rowspan="2" class="text-center cursor-help" title="{{ $monHoc->mh_ten }} {{ $monHoc->mh_tichluy ? '' : '(Không tính điểm tích lũy)' }}">{{ $monHoc->mh_ma }}{{ $monHoc->mh_tichluy ? '' : ' (*)' }}</th>
                                    @endforeach
                                    @if ($reqSemester == 123456)
                                        {{-- <th rowspan="2" class="text-center">ĐTBHK{{ $loop->index + 1 }}<span class="mini-label">N{{ $yIndex + 1 }}</span></th>
                                        <th rowspan="3" class="text-center">RLHK{{ $loop->index + 1 }}<span class="mini-label">N{{ $yIndex + 1 }}</span></th> --}}
                                    @elseif ($reqSemester > 4)
                                        {{-- <th rowspan="2" class="text-center">ĐTBHK{{ $loop->index + 1 }}<span class="mini-label">N{{ $reqYear }}</span></th>
                                        <th rowspan="3" class="text-center">RLHK{{ $loop->index + 1 }}<span class="mini-label">N{{ $reqYear }}</span></th> --}}
                                    @else
                                    {{-- <th rowspan="2" class="text-center">ĐTBHK{{ $reqSemester + 2 - (ceil(($reqSemester) / 2) * 2) }}<span class="mini-label">N{{ ceil(($reqSemester) / 2) }}</span></th>
                                    <th rowspan="3" class="text-center">RLHK{{ $reqSemester + 2 - (ceil(($reqSemester) / 2) * 2) }}<span class="mini-label">N{{ ceil(($reqSemester) / 2) }}</span></th>
                                    <th rowspan="3" class="text-center">XLHK{{ $reqSemester + 2 - (ceil(($reqSemester) / 2) * 2) }}<span class="mini-label">N{{ ceil(($reqSemester) / 2) }}</span></th> --}}
                                    <th rowspan="3" class="text-center">TCTL</th>
                                    <th rowspan="3" class="text-center">ĐTBCTL</th>
                                    @endif
                                @endforeach
                                @if ($reqSemester > 6)
                                    @if ($reqSemester == 123456)
                                    {{-- <th rowspan="3" class="text-center">DTB<span class="mini-label">N{{ $yIndex + 1 }}</span></th>
                                    <th rowspan="3" class="text-center">RL<span class="mini-label">N{{ $yIndex + 1 }}</span></th> --}}
                                    @else
                                    {{-- <th rowspan="3" class="text-center">DTB<span class="mini-label">N{{ $reqYear }}</span></th>
                                    <th rowspan="3" class="text-center">RL<span class="mini-label">N{{ $reqYear }}</span></th>
                                    <th rowspan="3" class="text-center">XL<span class="mini-label">N{{ $reqYear }}</span></th> --}}
                                    <th rowspan="3" class="text-center">TCTL</th>
                                    <th rowspan="3" class="text-center">ĐTBCTL</th>
                                    <th rowspan="3" class="text-center"><div style="width: 80px">Điều chỉnh tiến độ học</div></th>
                                    <th rowspan="3" class="text-center"><div style="width: 70px">Buộc thôi học</div></th>
                                    @endif
                                @endif
                            @endforeach
                            @if ($reqSemester == 123456)
                            {{-- <th rowspan="2" class="text-center">ĐTB<span class="mini-label">TK</span></th> --}}
                            <th rowspan="3" class="text-center">RL<span class="mini-label">TK</span></th>
                             {{--<th rowspan="3" class="text-center">XL<span class="mini-label">TK</span></th> --}}
                            <th rowspan="3" class="text-center">TCTL</th>
                            <th rowspan="3" class="text-center">ĐTBC<span class="mini-label">TL</span></th>
                            @endif
                            {{-- <th class="text-center cursor-help" colspan="3" >
                                ĐIỂM CHUYÊN ĐỀ TỐT NGHIỆP
                            </th>
                            <th class="text-center cursor-help" colspan="{{$lopHoc->lh_nienche == 0? 9 : 6}}" >
                                ĐIỂM THI TỐT NGHIỆP
                            </th> --}}
                            {{-- <th class="text-center cursor-help" rowspan="3" >Đ<span class="mini-label">TN</span></th>
                            <th class="text-center cursor-help" rowspan="3" >XL<span class="mini-label">TN</span></th> --}}
                            <th rowspan="3"  class="text-center">Chưa đạt điều kiện</th>
                            <th rowspan="3"  class="text-center">Ghi chú</th>
                        </tr>
                        {{-- <tr>
                            <th class="text-center cursor-help" rowspan="2">Lần 1</th>
                            <th class="text-center cursor-help" rowspan="2">Lần 2</th>
                            <th class="text-center cursor-help" rowspan="2">Lần 3</th>
                            <th class="text-center cursor-help" rowspan="1" colspan="{{$lopHoc->lh_nienche == 0? 3 : 2}}">Lần 1</th>
                            <th class="text-center cursor-help" rowspan="1" colspan="{{$lopHoc->lh_nienche == 0? 3 : 2}}">Lần 2</th>
                            <th class="text-center cursor-help" rowspan="1" colspan="{{$lopHoc->lh_nienche == 0? 3 : 2}}">Lần 3</th>
                        </tr> --}}
                        {{-- <tr>
                            @foreach ($danhSachNamHoc as $year)
                                @foreach ($year->semesters as $semester)
                                    @foreach ($semester->monHoc as $monHoc)
                                    <td rowspan="1" class="text-center">{{ $monHoc->mh_sodonvihoctrinh }}</td>
                                    @endforeach
                                    <th rowspan="1" class="text-center">{{ $semester->sumTinChi }}</th>
                                @endforeach
                            @endforeach
                            @if ($reqSemester == 123456)
                            <th rowspan="1" class="text-center">{{ $sumTinChi }}</th>
                            @endif
                            @if ($lopHoc->lh_nienche == 0)
                            <th class="text-center cursor-help" >Đ<span class="mini-label">TNCT</span></th>
                            <th class="text-center cursor-help" >Đ<span class="mini-label">TNLT</span></th>
                            <th class="text-center cursor-help" >Đ<span class="mini-label">TNTH</span></th>
                            <th class="text-center cursor-help" >Đ<span class="mini-label">TNCT</span></th>
                            <th class="text-center cursor-help" >Đ<span class="mini-label">TNLT</span></th>
                            <th class="text-center cursor-help" >Đ<span class="mini-label">TNTH</span></th>
                            <th class="text-center cursor-help" >Đ<span class="mini-label">TNCT</span></th>
                            <th class="text-center cursor-help" >Đ<span class="mini-label">TNLT</span></th>
                            <th class="text-center cursor-help" >Đ<span class="mini-label">TNTH</span></th>
                            @else
                            <th class="text-center cursor-help" >Đ<span class="mini-label">TNLT</span></th>
                            <th class="text-center cursor-help" >Đ<span class="mini-label">TNTH</span></th>
                            <th class="text-center cursor-help" >Đ<span class="mini-label">TNLT</span></th>
                            <th class="text-center cursor-help" >Đ<span class="mini-label">TNTH</span></th>
                            <th class="text-center cursor-help" >Đ<span class="mini-label">TNLT</span></th>
                            <th class="text-center cursor-help" >Đ<span class="mini-label">TNTH</span></th>
                            @endif
                        </tr> --}}
                    </thead>
                    <tbody>
                        @foreach ($danhSachSinhVien as $sinhVien)
                        <tr class="text-middle no-wrap">
                            <td class="text-center">{{ $loop->index + 1 }}</td>
                            <td class="text-center">{{ $sinhVien->sv_ma }}</td>
                            <td>{{ $sinhVien->sv_ho }}</td>
                            <td>{{ $sinhVien->sv_ten }}</td>
                            @foreach ($sinhVien->years as $year)
                                @foreach ($year->semesters as $semester)
                                    @foreach ($semester->monHoc as $monHoc)
                                    <td class="text-center" style="background-color: {{ $monHoc->ketQua && $monHoc->ketQua->display_score < 5 ? '#f48787' : '' }}">
                                        <span title="{{ $monHoc->ketQua && $monHoc->ketQua->svd_second ? 'Cải thiện ở học kỳ ' . ($monHoc->ketQua->svd_second_hocky + 2 - (ceil(($monHoc->ketQua->svd_second_hocky) / 2) * 2)) . ' năm ' . (ceil(($monHoc->ketQua->svd_second_hocky) / 2)) : '' }}">
                                            {{ $monHoc->ketQua && $monHoc->ketQua->display_score != null ? number_format($monHoc->ketQua->display_score, 1) : '' }}
                                        </span>
                                    </td>
                                    @endforeach
                                    {{-- <td class="text-center">
                                        <b title="{{ isset($semester->tichLuy) ? 'TCTL:' . $semester->tinChiTichLuy . ', ĐTBTL:' . $semester->tichLuy : '' }}">
                                            @isset ($semester->avg)
                                            {{ $semester->avg }}
                                            @endisset
                                        </b>
                                    </td> --}}
                                    {{-- <td class="text-center">
                                        @isset ($semester->diemRenLuyen)
                                        {{ $semester->diemRenLuyen }}
                                        @endisset
                                    </td> --}}
                                    @if ($reqSemester <= 4)
                                        {{-- <td class="text-center">
                                            @isset ($semester->hocLuc)
                                            {{ $semester->hocLuc }}
                                            @endisset
                                        </td> --}}
                                        {{-- <td class="text-center">
                                            @isset ($semester->tinChiTichLuy)
                                            {{ $semester->tinChiTichLuy }}
                                            @endisset
                                        </td> --}}
                                        {{-- <td class="text-center">
                                            @isset ($semester->tichLuy)
                                            {{ $semester->tichLuy }}
                                            @endisset
                                        </td> --}}
                                    @endif
                                @endforeach
                                @if ($reqSemester > 6)
                                    {{-- <td class="text-center">
                                        <b>
                                            @if (isset($year->avg))
                                            {{ $year->avg }}
                                            @endif
                                        </b>
                                    </td> --}}
                                    {{-- <td class="text-center">
                                        @if (isset($year->diemRenLuyen))
                                        {{ $year->diemRenLuyen }}
                                        @endif
                                    </td> --}}
                                    @if ($reqSemester < 123456)
                                        {{-- <td class="text-center">
                                            @if (isset($year->hocLuc))
                                            {{ $year->hocLuc }}
                                            @endif
                                        </td> --}}
                                        <td class="text-center">
                                            @if (isset($year->tinChiTichLuy))
                                            {{ $year->tinChiTichLuy }}
                                            @endif
                                        </td>
                                        {{-- <td class="text-center">
                                            @if (isset($year->tichLuy))
                                            {{ $year->tichLuy }}
                                            @endif
                                        </td> --}}
                                        {{-- <td class="text-center">
                                            @if (isset($year->avg) && $year->avg <= 5 && $year->avg >= 4)
                                            X
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if ((isset($year->avg) && $year->avg < 4) || (isset($year->tichLuy) && $year->tichLuy < 4))
                                            X
                                            @endif
                                        </td> --}}
                                    @endif
                                @endif
                            @endforeach
                            @if ($reqSemester == 123456)
                            <td class="text-center">
                                @if (isset($sinhVien->toanKhoa->diemRenLuyen))
                                {{ $sinhVien->toanKhoa->diemRenLuyen }}
                                @endif
                            </td>
                            {{-- <td class="text-center">
                                @if (isset($sinhVien->toanKhoa->hocLuc))
                                {{ $sinhVien->toanKhoa->hocLuc }}
                                @endif
                            </td> --}}
                            <td class="text-center">
                                @if (isset($sinhVien->toanKhoa->tinChiTichLuy))
                                {{ $sinhVien->toanKhoa->tinChiTichLuy }}
                                @endif
                            </td>
                            <td class="text-center">
                                @if (isset($sinhVien->toanKhoa->tichLuy))
                                {{ $sinhVien->toanKhoa->tichLuy }}
                                @endif
                            </td>
                            {{-- <td class="text-center">
                                <b>
                                    @if (isset($sinhVien->toanKhoa->avg))
                                    {{ $sinhVien->toanKhoa->avg }}
                                    @endif
                                </b>
                            </td> --}}
                            @endif
                            {{-- <td class="text-center" style="background-color: {{ $sinhVien->svd_loai == 1 && $sinhVien->khoaluanlan1 != -1 && ($sinhVien->khoaluanlan1 == null || $sinhVien->khoaluanlan1 < 5) ? '#f48787' : '' }}">
                                {{ $sinhVien->khoaluanlan1 != -1 && $sinhVien->svd_loai == 1 ? $sinhVien->khoaluanlan1 : '' }}
                            </td>
                            <td class="text-center" style="background-color: {{ $sinhVien->svd_loai == 1 && $sinhVien->khoaluanlan2 != -1 && ($sinhVien->khoaluanlan2 == null || $sinhVien->khoaluanlan2 < 5) ? '#f48787' : '' }}">
                                {{ $sinhVien->khoaluanlan2 != -1 && $sinhVien->svd_loai == 1 ? $sinhVien->khoaluanlan2 : '' }}
                            </td>
                            <td class="text-center" style="background-color: {{ $sinhVien->svd_loai == 1 && $sinhVien->khoaluanlan3 != -1 && ($sinhVien->khoaluanlan3 == null || $sinhVien->khoaluanlan3 < 5) ? '#f48787' : '' }}">
                                {{ $sinhVien->khoaluanlan3 != -1 && $sinhVien->svd_loai == 1 ? $sinhVien->khoaluanlan3 : '' }}
                            </td>
                            @if ($lopHoc->lh_nienche == 0)
                            <td class="text-center" style="background-color: {{ $sinhVien->svd_loai == 0 && $sinhVien->chinhtrilan1 != -1 && ($sinhVien->chinhtrilan1 == null || $sinhVien->chinhtrilan1 < 5) ? '#f48787' : '' }}">
                                {{ $sinhVien->chinhtrilan1 != -1 && $sinhVien->svd_loai == 0 ? $sinhVien->chinhtrilan1 : '' }}
                            </td>
                            <td class="text-center" style="background-color: {{ $sinhVien->svd_loai == 0 && $sinhVien->lythuyetlan1 != -1 && ($sinhVien->lythuyetlan1 == null || $sinhVien->lythuyetlan1 < 5) ? '#f48787' : '' }}">
                                {{ $sinhVien->lythuyetlan1 != -1 && $sinhVien->svd_loai == 0 ? $sinhVien->lythuyetlan1 : '' }}
                            </td>
                            <td class="text-center" style="background-color: {{ $sinhVien->svd_loai == 0 && $sinhVien->thuchanhlan1 != -1 && ($sinhVien->thuchanhlan1 == null || $sinhVien->thuchanhlan1 < 5) ? '#f48787' : '' }}">
                                {{ $sinhVien->thuchanhlan1 != -1 && $sinhVien->svd_loai == 0 ? $sinhVien->thuchanhlan1 : '' }}
                            </td>
                            <td class="text-center" style="background-color: {{ $sinhVien->svd_loai == 0 && $sinhVien->chinhtrilan2 != -1 && ($sinhVien->chinhtrilan2 == null || $sinhVien->chinhtrilan2 < 5) ? '#f48787' : '' }}">
                                {{ $sinhVien->chinhtrilan2 != -1 && $sinhVien->svd_loai == 0 ? $sinhVien->chinhtrilan2 : '' }}
                            </td>
                            <td class="text-center" style="background-color: {{ $sinhVien->svd_loai == 0 && $sinhVien->lythuyetlan2 != -1 && ($sinhVien->lythuyetlan2 == null || $sinhVien->lythuyetlan2 < 5) ? '#f48787' : '' }}">
                                {{ $sinhVien->lythuyetlan2 != -1 && $sinhVien->svd_loai == 0 ? $sinhVien->lythuyetlan2 : '' }}
                            </td>
                            <td class="text-center" style="background-color: {{ $sinhVien->svd_loai == 0 && $sinhVien->thuchanhlan2 != -1 && ($sinhVien->thuchanhlan2 == null || $sinhVien->thuchanhlan2 < 5) ? '#f48787' : '' }}">
                                {{ $sinhVien->thuchanhlan2 != -1 && $sinhVien->svd_loai == 0 ? $sinhVien->thuchanhlan2 : '' }}
                            </td>
                            <td class="text-center" style="background-color: {{ $sinhVien->svd_loai == 0 && $sinhVien->chinhtrilan3 != -1 && ($sinhVien->chinhtrilan3 == null || $sinhVien->chinhtrilan3 < 5) ? '#f48787' : '' }}">
                                {{ $sinhVien->chinhtrilan3 != -1 && $sinhVien->svd_loai == 0 ? $sinhVien->chinhtrilan3 : '' }}
                            </td>
                            <td class="text-center" style="background-color: {{ $sinhVien->svd_loai == 0 && $sinhVien->lythuyetlan3 != -1 && ($sinhVien->lythuyetlan3 == null || $sinhVien->lythuyetlan3 < 5) ? '#f48787' : '' }}">
                                {{ $sinhVien->lythuyetlan3 != -1 && $sinhVien->svd_loai == 0 ? $sinhVien->lythuyetlan3 : '' }}
                            </td>
                            <td class="text-center" style="background-color: {{ $sinhVien->svd_loai == 0 && $sinhVien->thuchanhlan3 != -1 && ($sinhVien->thuchanhlan3 == null || $sinhVien->thuchanhlan3 < 5) ? '#f48787' : '' }}">
                                {{ $sinhVien->thuchanhlan3 != -1 && $sinhVien->svd_loai == 0 ? $sinhVien->thuchanhlan3 : '' }}
                            </td>
                            @else
                            <td class="text-center" style="background-color: {{ $sinhVien->svd_loai == 0 && $sinhVien->lythuyetlan1 != -1 && ($sinhVien->lythuyetlan1 == null || $sinhVien->lythuyetlan1 < 5) ? '#f48787' : '' }}">
                                {{ $sinhVien->lythuyetlan1 != -1 && $sinhVien->svd_loai == 0 ? $sinhVien->lythuyetlan1 : '' }}
                            </td>
                            <td class="text-center" style="background-color: {{ $sinhVien->svd_loai == 0 && $sinhVien->thuchanhlan1 != -1 && ($sinhVien->thuchanhlan1 == null || $sinhVien->thuchanhlan1 < 5) ? '#f48787' : '' }}">
                                {{ $sinhVien->thuchanhlan1 != -1 && $sinhVien->svd_loai == 0 ? $sinhVien->thuchanhlan1 : '' }}
                            </td>
                            <td class="text-center" style="background-color: {{ $sinhVien->svd_loai == 0 && $sinhVien->lythuyetlan2 != -1 && ($sinhVien->lythuyetlan2 == null || $sinhVien->lythuyetlan2 < 5) ? '#f48787' : '' }}">
                                {{ $sinhVien->lythuyetlan2 != -1 && $sinhVien->svd_loai == 0 ? $sinhVien->lythuyetlan2 : '' }}
                            </td>
                            <td class="text-center" style="background-color: {{ $sinhVien->svd_loai == 0 && $sinhVien->thuchanhlan2 != -1 && ($sinhVien->thuchanhlan2 == null || $sinhVien->thuchanhlan2 < 5) ? '#f48787' : '' }}">
                                {{ $sinhVien->thuchanhlan2 != -1 && $sinhVien->svd_loai == 0 ? $sinhVien->thuchanhlan2 : '' }}
                            </td>
                            <td class="text-center" style="background-color: {{ $sinhVien->svd_loai == 0 && $sinhVien->lythuyetlan3 != -1 && ($sinhVien->lythuyetlan3 == null || $sinhVien->lythuyetlan3 < 5) ? '#f48787' : '' }}">
                                {{ $sinhVien->lythuyetlan3 != -1 && $sinhVien->svd_loai == 0 ? $sinhVien->lythuyetlan3 : '' }}
                            </td>
                            <td class="text-center" style="background-color: {{ $sinhVien->svd_loai == 0 && $sinhVien->thuchanhlan3 != -1 && ($sinhVien->thuchanhlan3 == null || $sinhVien->thuchanhlan3 < 5) ? '#f48787' : '' }}">
                                {{ $sinhVien->thuchanhlan3 != -1 && $sinhVien->svd_loai == 0 ? $sinhVien->thuchanhlan3 : '' }}
                            </td>
                            @endif --}}

                            {{-- <td>
                                <b>
                                    @if (isset($sinhVien->toanKhoa->avg_totnghiep))
                                    {{ $sinhVien->toanKhoa->avg_totnghiep }}
                                    @endif
                                </b>
                            </td>
                            <td>
                                <b>
                                    @if (isset($sinhVien->toanKhoa->hocLucTN) && $sinhVien->toanKhoa->hocLucTN != 'Rớt')
                                    {{ $sinhVien->toanKhoa->hocLucTN }}
                                    @endif
                                </b>
                            </td> --}}
                            <td>
                                @if (isset($sinhVien->datTotNghep) && $sinhVien->datTotNghep == false)
                                Chưa đạt
                                @endif
                            </td>
                            <td>
                                @isset ($sinhVien->notes)
                                    {{ $sinhVien->notes->map(function ($note) {
                                            if ($note['type'] == 'DL2') {
                                                return $note['key'] . ' (DiemL2:' . number_format($note['value'], 1) . ')';
                                            } else if ($note['type'] == 'DL1') {
                                                return $note['key'] . ' (DiemL1:' . number_format($note['value'], 1) . ')';
                                            } else {
                                                return $note['type'] . ' (' . $note['key'] . ')';
                                            }
                                    })->when($sinhVien->svd_ghichu, function ($collection) use ($sinhVien) {
                                        return $collection->push($sinhVien->svd_ghichu);
                                    })->join(', ') }}
                                @endisset
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                </div>
                @else
                <div class="clearfix"></div>
                <div class="col-lg-12 text-center">
                    Không tìm thấy dữ liệu
                </div>
                @endif
            </div>
        </div>
        @if (!$danhSachNamHoc->isEmpty() && !$danhSachSinhVien->isEmpty())
        <div class="clearfix">
            <b style="font-style: italic; text-decoration: underline;">Ghi chú</b>
        </div>
        <div class="row">
            @foreach ($chunkedNotes as $notes)
            <div class="col-lg-4">
                <div class="box box-widget">
                    <div class="box-body">
                        <table class="table table-striped table-bordered no-margin">
                            <thead>
                                @foreach ($notes as $note)
                                <tr>
                                    <th>{{ $note['key'] }}</th>
                                    <td>{{ $note['value'] }}</td>
                                </tr>
                                @endforeach
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @endif
        <a href="/dot-thi/{{$dotThi->dt_id}}"
                title="Trở về danh sách"
                class="btn btn-default">
            <i class="fa fa-share"></i> Trở về danh sách
        </a>
    </div>
</div>

<div class="modal fade" id="modal-chondotthi">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span></button>
                <h4 class="modal-title">Thêm đợt xét tốt nghiệp</h4>
            </div>
            <div class="modal-body">
                <select class="form-control select2" id="slDotThi" name="slDotThi">
                    @foreach ($danhSachDotXetTotNghiep as $dxtn)
                    <option value="{{$dxtn->dxtn_id}}">{{$dxtn->dxtn_ten}} - {{$dxtn->dxtn_tuthang.'/'.$dxtn->dxtn_tunam}}</option>
                    @endforeach
                </select>
            </div>
            <div class="modal-footer">
                <button type="button" onclick="themDotThi();" class="btn btn-danger">Lưu</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script type="text/javascript">

    $('.select2').select2({
        width: '100%',
        language: {
            noResults: function(){
                return "Không tìm thấy kết quả";
            }
        },
        placeholder: 'Nhập ký tự để tìm kiếm',
    });

    function redirect() {
        window.location = '?semester=' + $('#selectKqht').val()+'&sorttotnghiep='+($('#sortTotNghiep').val() ? $('#sortTotNghiep').val() : false);
    }

    function timKiem(){
        var key = $('#txtThongTin').val();
        var row = $('#table-sinhvien tbody tr');
        row.css('display','none');
        row.each(function(){
            text = $(this).find('td:nth-child(4)').text()+$(this).find('td:nth-child(5)').text()+$(this).find('td:nth-child(6)').text();
            if(text.toLowerCase().includes(key.toLowerCase())){
                $(this).css('display','');
            }
        });
    }

    function checkXuatWord(){
        $('.sosinhvien').text($("input[name='sinhvien[]']:checked").length);
    }

    function checkAllXuatWord(){
        if($('#checkall').is(":checked")){
            $("input[name='sinhvien[]']").prop('checked', true);
        }else{
            $("input[name='sinhvien[]']").prop('checked', false);
        }
        checkXuatWord();
    }

    function xuatWord() {
        arrSinhVien = $("input[name='sinhvien[]']:checked").map(function(){return $(this).val();}).get();
        if(arrSinhVien.length > 0){
            $.each(arrSinhVien,function(index, value){
                var win = window.open("{{ route('tra-chu-xuat-ket-qua') }}?sv_id="+value+"&hoc_ky=123456", '_blank');
                if (win) {
                    win.focus();
                } else {
                    alert('Vui lòng cho phép trang web mở thêm nhiều cửa sổ');
                    return false;
                }
            });
        }else{
            alert('Vui lòng chọn sinh viên muốn xuất thông tin');
        }
    }

    function themDotThi() {
        var sv = $("input[name='sinhvien[]']:checked").map(function(){return $(this).val();}).get();
        var json_sv = [];
        sv.forEach(function(e){
            json_sv.push(JSON.parse($('#chbSV'+e).text()));
        });
        $.ajax({
            type: 'POST',
            url: "{{ route('themdotxettotnghiep') }}",
            data: {
                'dotXetTotNghiep': $('#slDotThi').val(),
                'dotThi': {{$dotThi->dt_id}},
                'lopHoc': {{ $lopHoc->lh_id }},
                '_token': "{{ csrf_token() }}",
                'sinhVien': json_sv,
            },
            success:function(data){
                alert('Thêm đợt xét tốt nghiệp thành công');
                $('#modal-chondotthi').modal('hide');
            }
        });
    }
</script>
@endpush
