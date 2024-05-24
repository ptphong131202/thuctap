@extends('layouts.admin')

@section('header')
@endsection

@section('content')
<div class="row">

    <!-- Ngành nghề (Phong)-->
        <div class="col-lg-6 col-xs-6">
        <div class="small-box bg-aqua">
        <div class="inner" style="display: flex; with: 50%; justify-content: left; align-items: center; flex-wrap: wrap">
            <?php
                $trungcap = ($dsNganhNghe->filter(function($item) {
                    return $item->hdt_id === 4;
                })->count() / $dsNganhNghe->count()) * 100;

                $caodang = ($dsNganhNghe->filter(function($item) {
                    return $item->hdt_id === 5;
                })->count() / $dsNganhNghe->count()) * 100;
                // Định dạng phần trăm
                $trungcapFormatted = number_format($trungcap, 2);
                $caodangFormatted = number_format($caodang, 2);
                $backgroundStyle = "conic-gradient(rgb(60 245 68) 0% {$trungcapFormatted}%, #f44336 {$trungcapFormatted}% 100%)";

            ?>
            <!-- <div class="col-lg-3 col-sm-12"><div style="width: 120px; height: 120px; border-radius: 50%; background: {{ $backgroundStyle }};"></div></div> -->
            <div class="col-lg-3 col-sm-12">
                <div style="position: relative; width: 125px; height: 125px; border-radius: 50%; background: {{ $backgroundStyle }};">
                </div>
            </div>
            <div style="margin-top: 10px; margin: 10px auto; " class="col-lg-6 col-sm-12">
                <div style="display: flex; align-items: center; margin-bottom: 5px;">
                    <span style="display: inline-block; width: 20px; height: 20px; margin-right: 10px; background: rgb(60 245 68);"></span>Trung cấp - {{$trungcapFormatted}}%
                </div>
                <div style="display: flex; align-items: center; margin-bottom: 5px;">
                    <span style="display: inline-block; width: 20px; height: 20px; margin-right: 10px; background: #f44336;"></span>Cao đẳng - {{$caodangFormatted}}%
                </div>
            </div>
        </div>
        <h4 style="margin: 0 0 5px 10px; font-size: 20px">Ngành, nghề đào tạo</h4>
        <!-- icon (Phong)-->
            <div class="icon">
            <i class="fas fa-screwdriver-wrench"></i>
            </div>
            <a href="{{ route('nganh-nghe.index') }}" class="small-box-footer">Chi tiết <i class="fa fa-arrow-circle-right"></i></a>
        </div>
    </div>              

    <!-- Đợt xét tốt nghiệp (Phong)-->         
    <div class="col-lg-6 col-xs-6">
        <div class="small-box bg-yellow">
        <div class="inner" style="display: flex; with: 50%; justify-content: left;   align-items: center; flex-wrap: wrap">
            <?php
                $datotnhiep = ($dsDotXetTotNghiepSinhVien->filter(function($item) {
                    return $item->svxtn_dattn === 1;
                })->count() / $dsDotXetTotNghiepSinhVien->count()) * 100;

                $dangxet = ($dsDotXetTotNghiepSinhVien->filter(function($item) {
                    return $item->svxtn_dattn === 0;
                })->count() / $dsDotXetTotNghiepSinhVien->count()) * 100;
                // Định dạng phần trăm
                $datotnhiepFormatted = number_format($datotnhiep, 2);
                $dangxetFormatted = number_format($dangxet, 2);
                $backgroundStyle2 = "conic-gradient(blue 0% {$datotnhiepFormatted}%, red {$datotnhiepFormatted}% 100%)";

            ?>
            <div class="col-lg-3 col-sm-12"><div style="width: 125px; height: 125px; border-radius: 50%; background: {{ $backgroundStyle2 }};">
        </div></div>

            <div style="margin-top: 10px;  margin: 10px auto;" class="col-lg-6 col-sm-12">
                <div style="display: flex; align-items: center; margin-bottom: 5px; ">
                    <span style="display: inline-block; width: 20px; height: 20px; margin-right: 10px; background: blue;"></span>Đã xét - {{$datotnhiepFormatted}}%
                </div>
                <div style="display: flex; align-items: center; margin-bottom: 5px;">
                    <span style="display: inline-block; width: 20px; height: 20px; margin-right: 10px; background: red;"></span>Đang xét - {{$dangxetFormatted}}%
                </div>
            </div>
            </div>
            <h4 style="margin: 0 15px 5px; font-size: 20px">Đợt xét tốt nghiệp</h4>
            <div class="icon">
            <i class="fa-solid fa-user-graduate"></i>
            </div>
            <a href="{{ route('lop-hoc.index') }}" class="small-box-footer">Chi tiết <i class="fa fa-arrow-circle-right"></i></a>
        </div>
    </div>

    <!-- Đợt xét tốt nghiệp -->           
    <div class="col-lg-6 col-xs-6">
        <div class="small-box bg-aqua">
            <div class="inner">
                <h3>{{ $thongKe['slLopHoc'] }}</h3>
                <p>Lớp học</p>
            </div>
            <div class="icon">
                <i class="fa fa-users"></i>
            </div>
            <a href="{{ route('lop-hoc.index') }}" class="small-box-footer">Chi tiết <i class="fa fa-arrow-circle-right"></i></a>
        </div>
    </div>

    <!-- Số lượng môn học -->
    <div class="col-lg-6 col-xs-6">
        <div class="small-box bg-aqua">
            <div class="inner">
                <h3>{{ $thongKe['slMonHoc'] }}</h3>
                <p>Môn học</p>
            </div>
            <div class="icon">
            <i class="fa-solid fa-book-open-reader"></i>
            </div>
            <a href="{{ route('mon-hoc.index') }}" class="small-box-footer">Chi tiết <i class="fa fa-arrow-circle-right"></i></a>
        </div>
    </div>
   
    <!-- Số lượng khóa đào tạo -->
    <div class="col-lg-6     col-xs-6">
        <div class="small-box bg-yellow">
            <div class="inner">
                <h3>{{ $thongKe['slKhoaDaoTao'] }}</h3>
                <p>Chương trình đào tạo</p>
            </div>
            <div class="icon">
            <i class="fa-solid fa-gears"></i>
            </div>
            <a href="{{ route('khoa-dao-tao.index') }}" class="small-box-footer">Chi tiết <i class="fa fa-arrow-circle-right"></i></a>
        </div>
    </div>
    
    <!-- Số lượng sinh viên -->
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

<!-- Thông tin cán bộ -->
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
