@extends('layouts.admin')

@section('header')
<h1>
    Kết quả học tập lớp {{ $lopHoc->lh_ma }} học kỳ {{ $hocKy }}
</h1>
<ol class="breadcrumb">
    <li><a href="/"><i class="fa fa-dashboard"></i> Trang chủ</a></li>
    <li class="active">Kết quả học kỳ</li>
</ol>
@endsection

@section('content')
<div class="row">
    <div class="col-md-12 col-sm-12">
        <a href="/nhap-diem/{{ $lopHoc->lh_id }}"
                title="Trở về danh sách" style="margin-bottom:6px;"
                class="btn btn-default">
            <i class="fa fa-share"></i> Trở về danh sách
        </a>
        <div class="box box-widget">
            <div class="box-body table-responsive">
                <table class="table table-striped table-hover table-bordered no-margin">
                    <thead>
                        <tr>
                            <th rowspan="2" class="w-3 text-center">STT</th>
                            <th rowspan="2" class="w-10 text-center">MSHS</th>
                            <th rowspan="2" colspan="2" class="text-center">Họ và tên</th>
                            @foreach ($danhSachMonHoc as $monHoc)
                            <th rowspan="1" class="text-center">
                                <span title="{{ $monHoc->mh_ten }}">{{ $monHoc->mh_ma }}</span>
                            </th>
                            @endforeach
                            <th rowspan="2" class="text-center">
                                <span title="Điểm trung bình cộng">ĐTBHK{{ $hocKy }}</span>
                            </th>
                            <th rowspan="2" class="text-center">
                                <span title="Điểm rèn luyện">RLHK{{ $hocKy }}</span>
                            </th>
                            <th rowspan="2" class="text-center">
                                <span title="Xếp loại">XLHK{{ $hocKy }}</span>
                            </th>
                            <th rowspan="2" class="text-center">
                                <span title="Điểm tích lũy">TCTL</span>
                            </th>
                            <th rowspan="2" class="text-center">
                                <span title="Điểm tích lũy">ĐTBLC</span>
                            </th>
                        </tr>
                        <tr>
                            @foreach ($danhSachMonHoc as $monHoc)
                            <td class="text-center">
                                {{ $monHoc->mh_sodonvihoctrinh }}
                            </td>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($danhSachSinhVien as $sinhVien)
                        <tr>
                            <td class="text-center">{{ $loop->index + 1 }}</td>
                            <td class="text-center">{{ $sinhVien->sv_ma }}</td>
                            <td>{{ $sinhVien->sv_ho }}</td>
                            <td>{{ $sinhVien->sv_ten }}</td>
                            @foreach ($sinhVien->monHoc as $monHoc)
                            <td class="text-center">
                                @if ($monHoc->ketQua)
                                {{ $monHoc->ketQua->svd_first }}
                                @endif
                            </td>
                            @endforeach
                            <td class="text-center">
                                @if ($sinhVien->avg)
                                <b>{{ $sinhVien->avg }}</b>
                                @endif
                            </td>
                            <td class="text-center">
                                @if ($sinhVien->diemRenLuyen)
                                {{ $sinhVien->diemRenLuyen->svd_final }}
                                @endif
                            </td>
                            <td class="text-center">
                                @if ($sinhVien->hocLuc)
                                <b>{{ $sinhVien->hocLuc }}</b>
                                @endif
                            </td>
                            <td class="text-center">
                                @if ($sinhVien->tinChiTichLuy)
                                <b>{{ $sinhVien->tinChiTichLuy }}</b>
                                @endif
                            </td>
                            <td class="text-center">
                                @if ($sinhVien->tichLuy)
                                <b>{{ $sinhVien->tichLuy }}</b>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
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
        <a href="/nhap-diem/{{ $lopHoc->lh_id }}"
                title="Trở về danh sách"
                class="btn btn-default">
            <i class="fa fa-share"></i> Trở về danh sách
        </a>
    </div>
</div>
@endsection

@push('scripts')
<script type="text/javascript">
   
</script>
@endpush