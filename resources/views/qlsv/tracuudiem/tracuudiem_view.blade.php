@extends('layouts.admin')

@section('header')
<h1>
    Điểm học kỳ {{ $hocKy }}, lớp {{ $lopHoc->lh_ma }}
</h1>
<ol class="breadcrumb">
    <li><a href="/"><i class="fa fa-dashboard"></i> Trang chủ</a></li>
    <li class="active">Tra cứu điểm</li>
</ol>
@endsection

@section('content')
<div class="row">
    <div class="col-md-12 col-sm-12">
        <div class="box box-widget">
            <div class="box-body">
                <table class="table table-striped table-hover table-bordered no-margin">
                    <thead>
                        <tr>
                            <th rowspan="2" class="w-3 text-center">#</th>
                            <th rowspan="2" class="w-10 text-center">Mã môn học</th>
                            <th rowspan="2" class="text-center">Tên môn học</th>
                            <th rowspan="2" class="w-10 text-center">Dự lớp (%)</th>
                            <th colspan="2" class="text-center">Điểm tổng kết</th>
                        </tr>
                        <tr>
                            <th class="text-center">Lần 1</th>
                            <th class="text-center">Lần 2</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($bangDiem as $monHoc)
                        <tr>
                            <td class="text-center">{{ $loop->index + 1 }}</td>
                            <td>{{ $monHoc->mh_ma }}</td>
                            <td>{{ $monHoc->mh_ten }}</td>
                            <td class="text-center">{{ $monHoc->svd_dulop }}</td>
                            <td class="text-center">{{ $monHoc->svd_first }}</td>
                            <td class="text-center">{{ $monHoc->svd_second }}</td>
                        </tr>
                        @endforeach
                        @if ($bangDiem->isEmpty())
                        <tr>
                            <td colspan="100" class="text-center">Không tìm thấy dữ liệu</td>
                        </tr>
                        @endempty
                    </tbody>
                </table>
            </div>
            <div class="box-footer">
                <a href="{{ route('tra-cuu-diem.index') }}" class="btn btn-default"><i class="fa fa-share"></i> Trở về</a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script type="text/javascript">
   
</script>
@endpush