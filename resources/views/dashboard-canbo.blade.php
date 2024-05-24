@extends('layouts.admin')

@section('header')
@endsection

@section('content')
<div class="row">
    <div class="col-lg-6 col-xs-6">
        <div class="small-box bg-aqua">
            <div class="inner">
                <h3>{{ $thongKe['slLopHoc'] }}</h3>
                <p>Lớp học 1</p>
            </div>
            <div class="icon">
                <i class="fa fa-users"></i>
            </div>
            <a href="{{ route('lop-hoc.index') }}" class="small-box-footer">Chi tiết <i class="fa fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <div class="col-lg-6 col-xs-6">
        <div class="small-box bg-aqua">
            <div class="inner">
                <h3>{{ $thongKe['slMonHoc'] }}</h3>
                <p>Môn học</p>
            </div>
            <div class="icon">
                <i class="fa fa-users"></i>
            </div>
            <a href="{{ route('mon-hoc.index') }}" class="small-box-footer">Chi tiết <i class="fa fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <div class="col-lg-6 col-xs-6">
        <div class="small-box bg-aqua">
            <div class="inner">
            <h3>{{ $dsNganhNghe->filter(function($item) {
                    return $item->hdt_id === 4;
                })->count() }} Ngành, nghề - trung cấp
            </h3>
            <h3>{{ $dsNganhNghe->filter(function($item) {
                    return $item->hdt_id === 5;
                })->count() }} Ngành, nghề - Cao đẵng
            </h3>
            <h3>{{$dsNganhNghe->count() }} Tổng cộng
            </div>
            <div class="icon">
                <i class="fa fa-users"></i>
            </div>
            <a href="{{ route('nganh-nghe.index') }}" class="small-box-footer">Chi tiết <i class="fa fa-arrow-circle-right"></i></a>
        </div>
    </div>

    <div class="col-lg-6 col-xs-6">
        <div class="small-box bg-aqua">
            <div class="inner">
            <h3>{{ $dsDotXetTotNghiepSinhVien->filter(function($item) {
                    return $item->svxtn_dattn === 1;
                })->count() }} Đã tốt nghiệp
            </h3>
            <h3>{{ $dsDotXetTotNghiepSinhVien->filter(function($item) {
                    return $item->svxtn_dattn === 0;
                })->count() }} Chuẩn bị tốt nghiệp
            </h3>
            <h3>{{$thongKe['slMonHoc'] - $dsDotXetTotNghiepSinhVien->count() }} Chưa xét
            </h3>
            </div>
            <div class="icon">
                <i class="fa fa-users"></i>
            </div>
            <a href="{{ route('lop-hoc.index') }}" class="small-box-footer">Chi tiết <i class="fa fa-arrow-circle-right"></i></a>
        </div>
    </div>

    {{-- <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-green">
            <div class="inner">
                <h3>{{ $thongKe['slMonHoc'] }}</h3>
                <p>Môn học</p>
            </div>
            <div class="icon">
                <i class="fa fa-book"></i>
            </div>
            <a href="{{ route('mon-hoc.index') }}" class="small-box-footer">Chi tiết <i class="fa fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-yellow">
            <div class="inner">
                <h3>{{ $thongKe['slKhoaDaoTao'] }}</h3>
                <p>Chương trình đào tạo</p>
            </div>
            <div class="icon">
                <i class="fa  fa-list"></i>
            </div>
            <a href="{{ route('khoa-dao-tao.index') }}" class="small-box-footer">Chi tiết <i class="fa fa-arrow-circle-right"></i></a>
        </div>
    </div> --}}
    <div class="col-lg-6 col-xs-6">
        <div class="small-box bg-red">
            <div class="inner">
                <h3>{{ $thongKe['slSinhVien'] }}</h3>
                <p>Sinh Viên</p>
            </div>
            <div class="icon">
                <i class="fa fa-child"></i>
            </div>
            <a href="{{ route('sinh-vien.index') }}" class="small-box-footer">Chi tiết <i class="fa fa-arrow-circle-right"></i></a>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-6">
        <div class="box box-default" style="max-width: 650px;border: 2px solid #605ca8;border-radius: 6px;">
            <div class="box-body">
                <div class="row">
                    <div class="col-md-5 ">
                        @if ($infoUser->canBo['cb_gioitinh'] == 1)
                        <img src="{{ assetv('/images/default/sinhvien-nam.svg') }}" style="margin-top: -24px;margin-bottom: -19px;"/>
                        @else
                        <img src="{{ assetv('/images/default/sinhvien.svg') }}" style="margin-top: -24px;margin-bottom: -19px;"/>
                        @endif
                    </div>
                    <div class="col-md-7 ">
                        <table class="table">
                            <tr>
                                <th style="width: 120px;border:none">MSCB: </th>
                                <td style="border:none">{{ $infoUser->canBo['cb_ma'] }}</td>
                            </tr>
                            <tr>
                                <th >Họ tên: </th>
                                <td >{{ $infoUser->canBo['cb_ho'].' '.$infoUser->canBo['cb_ten'] }}</td>
                            </tr>
                            <tr>
                                <th>Chức vụ: </th>
                                <td>{{ $infoUser->canBo['cb_chucvu'] }}</td>
                            </tr>
                            <tr>
                                <th>Email: </th>
                                <td>{{ $infoUser->email }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <a class="btn btn-block btn-social btn-twitter btn-lg" href="{{ assetv('/TaiLieuHDSD_QuanLyDiemSinhVien_v2.doc') }}" target="_blank" download>
            <i class="fa fa-download"></i> Tài liệu hướng dẫn
        </a>
    </div>
</div>

<!-- @foreach ($dsSinhVien as $sinhvien)
@endforeach -->

@endsection

@push('scripts')
<script type="text/javascript">

</script>
@endpush
