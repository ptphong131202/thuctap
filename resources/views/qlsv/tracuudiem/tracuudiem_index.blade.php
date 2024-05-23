@extends('layouts.admin')

@section('header')
    <h1>
        Tra cứu điểm
    </h1>
    <ol class="breadcrumb">
        <li><a href="/"><i class="fa fa-dashboard"></i> Trang chủ</a></li>
        <li class="active">Tra cứu điểm</li>
    </ol>
@endsection

@section('content')
    <div class="row">
        @if (!$warnings->isEmpty())
            <div class="col-md-12 col-sm-12">
                <div class="alert alert-warning alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    <h4><i class="icon fa fa-warning"></i> Cảnh báo!</h4>
                    {{ $warnings->first()['type'] == 'BTH' ? 'Bạn đã bị buộc thôi học, cần liên hệ Phòng đào tạo để được hỗ trợ.' : 'Bạn cần phải điều chỉnh tiến độ học tập, bạn liên hệ phòng đào tạo để hỗ trợ.' }}
                </div>
            </div>
        @endif
        <div class="col-md-12 col-sm-12">
            <div class="box box-widget">
                <div class="box-body">
                    <form method="get" class="form-horizontal">
                        <div class="row">
                            <div class="col-lg-12">
                                {{-- <div class="col-lg-4 col-lg-offset-2 col-md-6">
                                <div class="form-group">
                                    <label class="col-sm-4 control-label">Năm học</label>
                                    <div class="col-sm-8">
                                        <select name="nien_khoa" class="form-control" value="{{ request()->nien_khoa }}">
                                            <option value="-1" @if (request()->nk_id == -1) selected @endif>Tất cả</option>
                                            @foreach ($danhSachNamHoc as $nienKhoa)
                                                <option value="{{ $nienKhoa->nk_id }}" @if (request()->nien_khoa == $nienKhoa->nk_id) selected @endif>{{ $nienKhoa->nk_ten }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div> --}}
                                <div class="col-lg-3 col-lg-offset-4 col-md-6">
                                    <div class="form-group">
                                        <label class="col-sm-4 control-label">Học kỳ</label>
                                        <div class="col-sm-8">
                                            <select name="hoc_ky" class="form-control">
                                                <optgroup label="Theo học kỳ">
                                                    <option value="1"
                                                        @if (request()->hoc_ky == 1) selected @endif>Học kỳ 1 năm I
                                                    </option>
                                                    <option value="2"
                                                        @if (request()->hoc_ky == 2) selected @endif>Học kỳ 2 năm I
                                                    </option>
                                                    <option value="3"
                                                        @if (request()->hoc_ky == 3) selected @endif>Học kỳ 1 năm II
                                                    </option>
                                                    <option value="4"
                                                        @if (request()->hoc_ky == 4) selected @endif>Học kỳ 2 năm II
                                                    </option>
                                                    <option value="5"
                                                        @if (request()->hoc_ky == 5) selected @endif>Học kỳ 1 năm III
                                                    </option>
                                                    <option value="6"
                                                        @if (request()->hoc_ky == 6) selected @endif>Học kỳ 2 năm III
                                                    </option>
                                                </optgroup>
                                                <optgroup label="Theo năm">
                                                    <option value="12"
                                                        @if (request()->hoc_ky == 12) selected @endif>Năm I</option>
                                                    <option value="34"
                                                        @if (request()->hoc_ky == 34) selected @endif>Năm II</option>
                                                    <option value="56"
                                                        @if (request()->hoc_ky == 56) selected @endif>Năm III</option>
                                                    <option value="123456"
                                                        @if (request()->hoc_ky == 123456) selected @endif>Toàn khóa
                                                    </option>
                                                </optgroup>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-12 text-center">
                                <button type="submit" class="btn btn-default bg-purple">
                                    <i class="fa fa-search"></i> Liệt kê
                                </button>
                                @if (count($danhSachLopHoc) != 0)
                                    <a href="{{ route('tra-chu-xuat-ket-qua') }}?nien_khoa={{ request()->nien_khoa }}&lhId={{ $lhId }}&hoc_ky={{ request()->hoc_ky }}"
                                        class="btn btn-primary" download>
                                        <i class="fa fa-file-word-o"></i> Xuất Word
                                    </a>
                                @endif
                            </div>
                        </div>
                    </form>
                </div>
                @if (count($danhSachLopHoc) == 0)
                    <div class="box-footer text-center">
                        Không tìm thấy dữ liệu
                    </div>
                @endif
            </div>
            @foreach ($danhSachLopHoc as $lopHoc)
                @php
                    $quyChe2022 = $lopHoc['lh_nienche'] == 1;
                @endphp
                <div class="box box-widget">
                    <div class="box-header with-border">
                        {{-- <div class="box-title">Học kỳ {{ $lopHoc['semester'] }} năm @for ($i = 1; $i <= round($lopHoc['year']); $i++)
                                <span>I</span>
                            @endfor
                        </div> --}}
                        <div class="box-title">Học kỳ {{ $lopHoc['semester'] }} năm
                            @php
                                $yearRoman = str_repeat('I', round($lopHoc['year']));
                                echo $yearRoman;
                            @endphp
                        </div>

                    </div>
                    <div class="box-body table-responsive">
                        <table class="table table-striped table-hover table-bordered no-margin">
                            <thead>
                                <tr>
                                    <th rowspan="2" class="w-3 text-center">#</th>
                                    <th rowspan="2" class="w-10 text-center">Mã môn học</th>
                                    <th rowspan="2" class="text-center">Tên môn học</th>
                                    <th rowspan="2" class="w-5 text-center">Số tín chỉ</th>
                                    {{-- <th rowspan="2" class="w-10 text-center">Dự lớp (%)</th> --}}
                                    <th colspan="{{ $quyChe2022 ? 3 : 2 }}" class="text-center">Điểm tổng kết</th>
                                    <th rowspan="2" class="text-center">Ghi chú</th>
                                </tr>
                                <tr>
                                    <th class="text-center">Lần 1</th>
                                    <th class="text-center">Lần 2</th>
                                    @if ($quyChe2022)
                                        <th class="text-center">Lần 3</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($lopHoc['monHoc'] as $monHoc)
                                    <tr>
                                        <td class="text-center">{{ $loop->index + 1 }}</td>
                                        <td>{{ $monHoc->mh_ma }}</td>
                                        <td>{{ $monHoc->mh_ten }}</td>
                                        <td class="text-center">{{ $monHoc->mh_sodonvihoctrinh }}</td>
                                        {{-- <td class="text-center">{{ $monHoc->svd_dulop }}</td> --}}
                                        <td class="text-center">
                                            @if (isset($monHoc->svd_first) && !empty($monHoc->svd_first))
                                                {{ number_format($monHoc->svd_first, 1) }}
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if (isset($monHoc->svd_second) && !empty($monHoc->svd_second))
                                                {{ number_format($monHoc->svd_second, 1) }}
                                            @endif
                                        </td>
                                        @if ($quyChe2022)
                                            <td class="text-center">
                                                @if (isset($monHoc->svd_third) && !empty($monHoc->svd_third))
                                                    {{ number_format($monHoc->svd_third, 1) }}
                                                @endif
                                            </td>
                                        @endif
                                        <td>
                                            @if (isset($monHoc->svd_first) &&
                                                    !empty($monHoc->svd_first) &&
                                                    $monHoc->svd_first < 5 &&
                                                    (isset($monHoc->svd_second) && !empty($monHoc->svd_second) && $monHoc->svd_second > 5))
                                            @elseif (isset($monHoc->svd_first) &&
                                                    !empty($monHoc->svd_first) &&
                                                    $monHoc->svd_first < 5 &&
                                                    (isset($monHoc->svd_second) && !empty($monHoc->svd_second) && $monHoc->svd_second < 5) &&
                                                    (isset($monHoc->svd_third) && !empty($monHoc->svd_third) && $monHoc->svd_third > 5))
                                            @else
                                                {{ $monHoc->svd_ghichu }}
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                                @if (count($lopHoc['monHoc']) == 0)
                                    <tr>
                                        <td colspan="100" class="text-center">Không tìm thấy dữ liệu</td>
                                    </tr>
                                @endempty
                        </tbody>
                    </table>
                    @if (
                        (count($lopHoc['monHoc']) > 0 && isset($lopHoc['avg'])) ||
                            (isset($lopHoc['diemRenLuyen']) && $lopHoc['diemRenLuyen']))
                        <div class="row" style="margin:0px;margin-top:12px;">
                            <div class="col-md-6">
                                @if (isset($lopHoc['avg']))
                                    <div style="padding: 0px 12px;">
                                        <b class="text-center" style="text-align: right">Điểm trung bình học kỳ:</b>
                                        <b>{{ number_format($lopHoc['avg'], 1) }}</b>
                                    </div>
                                @endif
                                @if (isset($lopHoc['tichLuy']))
                                    <div style="padding: 0px 12px;">
                                        <b class="text-center" style="text-align: right">Điểm trung bình tích lũy:</b>
                                        <b colspan="3">{{ number_format($lopHoc['tichLuy'], 1) }}</b>
                                    </div>
                                @endif
                                @if ($lopHoc['diemRenLuyen'])
                                    <div style="padding: 0px 12px;">
                                        <b class="text-center" style="text-align: right">Điểm rèn luyện:</b>
                                        <b style="text-left">{{ $lopHoc['diemRenLuyen']->svd_final }}</b>
                                    </div>
                                @endif
                            </div>
                            <div class="col-md-6">
                                @if (isset($lopHoc['tinhchi_total']))
                                    <div style="padding: 0px 12px;">
                                        <b class="text-center" style="text-align: right">Số tín chỉ học kỳ:</b>
                                        <b>{{ $lopHoc['tinhchi_total'] }}</b>
                                    </div>
                                @endif
                                @if (isset($lopHoc['tinChiTichLuy']))
                                    <div style="padding: 0px 12px;">
                                        <b class="text-center" style="text-align: right">Số tín chỉ tích lũy:</b>
                                        <b>{{ $lopHoc['tinChiTichLuy'] }}</b>
                                    </div>
                                @endif
                                @if (isset($lopHoc['avg']) && $lopHoc['diemRenLuyen'] && $lopHoc['diemRenLuyen']->svd_final != 0)
                                    <div style="padding: 0px 12px;">
                                        <b class="text-center" style="text-align: right">Xếp loại:</b>
                                        <b>{{ $lopHoc['hocLuc'] }}</b>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection

@push('scripts')
<script type="text/javascript"></script>
@endpush
