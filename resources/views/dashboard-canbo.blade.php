<!-- T.Phong chcinhr sửa file này -->

@extends('layouts.admin')

@section('header')
@endsection

@section('content')
<div class="row">
<!-- Ngành nghề (Phong)-->
        <div class="col-lg-6 col-xs-6 hidden-xs" >
                <div class="small-box bg-purple">
                    <div class="inner">
                        <h4 style="margin: 0 0 5px 5px; font-size: 25px; justify-content: space-between;">Ngành, nghề đào tạo</h4>
                        <div class="hidden-xs" style="display: flex;    width: 100%;">
                            <p style="width: 50%;    text-align: center; font-size: 20px;"><span
                            style="display: block; font-size: 110px;">{{ $nganhnghe['slNganhNghe_TrungCap'] }}</span> Trung cấp </p>
                            <p style="width: 50%;  text-align: center;  font-size: 20px;"> <span
                            style="display: block; font-size: 110px;">{{ $nganhnghe['slNganhNghe_CaoDang'] }}</span> Cao đẳng</p>
                        </div>
                    </div>
                    <!-- icon (Phong)-->
                    <div class="icon">
                        <i class="fas fa-screwdriver-wrench"></i>
                    </div>
                    <a href="{{ route('nganh-nghe.index') }}" class="small-box-footer">Chi tiết <i
                        class="fa fa-arrow-circle-right"></i></a>
            </div>
        </div>
        

        <!-- Số lượng khóa đào tạo -->
        <div class="col-lg-6     col-xs-6">
            <div class="small-box bg-aqua">
                <div class="inner">
                    <h3>{{ $thongKe['slKhoaDaoTao'] }}</h3>
                    <p>Chương trình đào tạo</p>
                    <div class="hidden-xs" style="width: 96%; height: 135px; display: flex; position: relative;  justify-content: space-between;  margin-top: 20px; ">
                        <div style="width: 48%; height: 100%; border: 1px solid rgb(0 102 255); position: relative;">
                                    <div style="position: absolute; width: 100%;
                                    padding: 2px 5px;
                                    background: rgb(0 102 255);
                                    font-size: 13px;
                                    top: -22px;">Hệ Cao Đẳng</div>
                                <div class="">
                                    @foreach ($khoadaotaocaodangQuery as $caodang)
                                    <p style="    padding: 3px;    font-size: 14px;    margin: 0px;    display: -webkit-box;  
                                    line-height: 20px;    -webkit-box-orient: vertical;    -webkit-line-clamp: 1; 
                                    overflow: hidden;">
                                    <a style="color: white;" href="http://localhost/cea-2.1/public/khoa-dao-tao/{{ $caodang->kdt_id }}/hoc-ky">
                                        {{ $caodang->kdt_ten }}
                                    </a>
                                        </p>
                                        @endforeach
                                </div>
                        </div>
                        <div style="width: 48%; height: 100%; border: 1px solid rgb(0 102 255);position: relative;">
                            <div style="position: absolute; width: 100%;
                                padding: 2px 5px;
                                background: rgb(0 102 255);
                                font-size: 13px;
                                top: -22px;">Hệ Trung Cấp</div>
                                <div class="">
                                    @foreach ($khoadaotaotrungcapQuery as $caodang)
                                    <p style="    padding: 3px;    font-size: 14px;    margin: 0px;    display: -webkit-box;
                                    line-height: 20px;    -webkit-box-orient: vertical;    -webkit-line-clamp: 1; 
                                    overflow: hidden;">
                                    <a style="color: white;" href="http://localhost/cea-2.1/public/khoa-dao-tao/{{ $caodang->kdt_id }}/hoc-ky">
                                        {{ $caodang->kdt_ten }}
                                    </a>
                                        </p>
                                        @endforeach
                                </div>
                        </div>
                </div>
                </div>
                <div class="icon">
                    <i class="fa-solid fa-gears"></i>
                </div>
                <a href="{{ route('khoa-dao-tao.index') }}" class="small-box-footer">Chi tiết <i
                        class="fa fa-arrow-circle-right"></i></a>
            </div>
        </div>

    
    <div class="col-lg-6 col-xs-6">
            <!-- lớp học -->
        <div class="col-lg-12 col-xs-12" style="padding: 0;">
                <div class="small-box bg-orange">
                    <div class="inner">
                        <h3 class="d-none d-sm-block">{{ $thongKe['slLopHoc'] }}</h3>
                        <p class="d-none d-sm-block">Lớp học</p>
                        <div class="hidden-xs"
                            style="border: 1px solid white;
                            height: 130px; width: 75%; margin-left: 28px;  border-right: none; border-top: none; margin-top: -5px; display: flex; margin-bottom: 19px;">
                            <span style="width: 30px; height: 130px; position: absolute;  left: 8px;">
                                <div style="position: absolute; top: -5%; left: 37%">{{$maxLopHoc}}</div>
                                <div style="position: absolute; top: 45%; left: 37%">{{ round($maxLopHoc / 2) }}</div>
                                <div style="position: absolute; bottom: -5px; left: 37%">0</div>
                            </span>
                            <span style="width: calc(100% / 5); flex: 1;    position: relative;">
                                @php
                                $heightPercentage = ($lophocs[0]['sllop'] / $maxLopHoc) * 100;
                                @endphp
                                <div
                                    style=" width: 50%; height: {{$heightPercentage}}% ; background: #00ff29;  position: absolute; bottom: 0; left: 23%;">
                                    <span style="position: absolute; top:-17px; left: 31%;">{{$lophocs[0]['sllop']}}
                                    </span>
                                </div>
                                <span style="                        position: absolute;                        bottom: -22px;                        right: 12px;                        font-size: 13px;
                                ">{{$lophocs[0]['nk_ten']}}</span>
                            </span>
                            <span style="width: calc(100% / 5); flex: 1; position: relative;">
                                @php
                                $heightPercentage = ($lophocs[1]['sllop'] / $maxLopHoc) * 100;
                                @endphp
                                <div
                                    style=" width: 50%; height: {{$heightPercentage}}% ; background: #00ff29;  position: absolute; bottom: 0; left: 23%;">
                                    <span style="position: absolute; top:-17px; left: 31%;">{{$lophocs[1]['sllop']}}
                                    </span>
                                </div>

                                <span style="                        position: absolute;                        bottom: -22px;                        right: 12px;                        font-size: 13px;
                                ">{{$lophocs[1]['nk_ten']}}</span>
                            </span>
                            <span style="width: calc(100% / 5); flex: 1; position: relative;">
                                @php
                                $heightPercentage2 = ($lophocs[2]['sllop'] / $maxLopHoc) * 100;
                                @endphp
                                <div
                                    style=" width: 50%; height: {{$heightPercentage2}}% ; background: #00ff29;  position: absolute; bottom: 0; left: 23%;">
                                    <span style="position: absolute; top:-17px; left: 31%;">{{$lophocs[2]['sllop']}}
                                    </span>

                                </div>

                                <span style="
                                position: absolute;                        bottom: -22px;                        right: 12px;                        font-size: 13px;
                                ">{{$lophocs[2]['nk_ten']}}</span>
                            </span>
                            <span style="width: calc(100% / 5); flex: 1; position: relative;">
                                @php
                                $heightPercentage1 = ($lophocs[3]['sllop'] / $maxLopHoc) * 100;
                                @endphp
                                <div
                                    style=" width: 50%; height: {{$heightPercentage1}}% ; background: #00ff29;  position: absolute; bottom: 0; left: 23%;">
                                    <span style="position: absolute; top:-17px; left: 31%;">{{$lophocs[3]['sllop']}}
                                    </span>
                                </div>

                                <span style="                        position: absolute;                        bottom: -22px;                        right: 12px;                        font-size: 13px;
                                ">{{$lophocs[3]['nk_ten']}}</span>
                            </span>
                            <span style="width: calc(100% / 5); flex: 1; position: relative;">
                                @php
                                $heightPercentage0 = ($lophocs[4]['sllop'] / $maxLopHoc) * 100;
                                @endphp
                                <div
                                    style=" width: 50%; height: {{$heightPercentage0}}% ; background: #00ff29;  position: absolute; bottom: 0; left: 23%;">
                                    <span style="position: absolute; top:-17px; left: 31%;">{{$lophocs[4]['sllop']}}
                                    </span>
                                </div>

                                <span style="                        position: absolute;                        bottom: -22px;                        right: 12px;                        font-size: 13px;
                                ">{{$lophocs[4]['nk_ten']}}</span>
                            </span>
                            <span style="position: absolute;    right: 8%;    bottom: 16%;">Niên khóa</span>
                        </div>
                    </div>
                    <div class="icon">
                        <i class="fa fa-users" style="color: white;"></i>
                    </div>
                    <a href="{{ route('lop-hoc.index') }}" class="small-box-footer">Chi tiết <i
                            class="fa fa-arrow-circle-right"></i></a>
                </div>
            </div>
        

        <!-- Đợt xét tốt nghiệp (Phong)-->
        <div class="col-lg-12 col-xs-12 hidden-xs" style="padding: 0;">
            <div class="small-box bg-olive">
                <div class="inner">
                    <!-- <h3 class="d-none d-sm-block">{{ $thongKe['slLopHoc'] }}</h3> -->
                    <p class="d-none d-sm-block">Đợt thi, xét tốt nghiệp</p>
                    <div class="hidden-xs"
                        style="border: 1px solid white;
                        height: 130px; width: 70%; margin-left: 28px;  border-right: none; border-top: none; margin-top: -5px; display: flex; margin-bottom: 19px;">
                        <span style="width: 30px; height: 158px; position: absolute;  left: 8px;">
                            <div style="position: absolute; top: -5%; left: 37%">{{$dsdotxettotnghiep['maxtotnhghiep']}}</div>
                            <div style="position: absolute; top: 45%; left: 37%">{{ round($dsdotxettotnghiep['maxtotnhghiep'] / 2) }}</div>
                            <div style="position: absolute; bottom: -5px; left: 37%">0</div>
                        </span>
                        <span style="width: calc(100% / 5); flex: 1;    position: relative;">
                            @php
                            $heightPercentage = ($dsDotThi['value5nam'][$dsdotxettotnghiep['maxnamxettotnghiep'] - 4] / $dsdotxettotnghiep['maxtotnhghiep']) * 100 + 1;
                            $heightPercentage =  ($dsdotxettotnghiep['value5namtotnghiep'][$dsdotxettotnghiep['maxnamxettotnghiep'] - 4] / $dsdotxettotnghiep['maxtotnhghiep']) * 100 + 1;
                            @endphp
                            <div
                                style=" width: 40%; height: {{$heightPercentage}}% ; background: red;  position: absolute; bottom: 0; left: 10%;">
                                <span style="position: absolute; top:-17px; left: 31%;">{{$dsDotThi['value5nam'][$dsdotxettotnghiep['maxnamxettotnghiep'] - 4]}}
                                </span>
                            </div>
                            <div
                                style=" width: 40%; height: {{$heightPercentage}}% ; background: #00ff29;  position: absolute; bottom: 0; right: 10%;">
                                <span style="position: absolute; top:-17px; left: 31%;">{{$dsdotxettotnghiep['value5namtotnghiep'][$dsdotxettotnghiep['maxnamxettotnghiep'] - 4]}}
                                </span>
                            </div>
                            <span style="                        position: absolute;                        bottom: -22px;                        right: 27px;                        font-size: 13px;
                            ">{{$dsdotxettotnghiep['maxnamxettotnghiep'] - 4}}</span>
                        </span>
                        <span style="width: calc(100% / 5); flex: 1; position: relative;">
                            @php
                            $heightPercentage3 = ($dsDotThi['value5nam'][$dsdotxettotnghiep['maxnamxettotnghiep'] - 3] / $dsdotxettotnghiep['maxtotnhghiep']) * 100 + 1;
                            $heightPercentage31 = ($dsdotxettotnghiep['value5namtotnghiep'][$dsdotxettotnghiep['maxnamxettotnghiep'] - 3] / $dsdotxettotnghiep['maxtotnhghiep']) * 100 + 1;
                            @endphp
                            <div
                                style=" width: 40%; height: {{$heightPercentage3}}% ; background: red;  position: absolute; bottom: 0; left: 10%;">
                                <span style="position: absolute; top:-17px; left: 31%;">{{$dsDotThi['value5nam'][$dsdotxettotnghiep['maxnamxettotnghiep'] - 3]}}
                                </span>
                            </div>
                            <div
                                style=" width: 40%; height: {{$heightPercentage31}}% ; background: #00ff29;  position: absolute; bottom: 0; right: 10%;">
                                <span style="position: absolute; top:-17px; left: 31%;">{{$dsdotxettotnghiep['value5namtotnghiep'][$dsdotxettotnghiep['maxnamxettotnghiep'] - 3]}}
                                </span>
                            </div>

                            <span style="                        position: absolute;                        bottom: -22px;                        right: 27px;                        font-size: 13px;
                            ">{{$dsdotxettotnghiep['maxnamxettotnghiep'] - 3}}</span>
                        </span>
                        <span style="width: calc(100% / 5); flex: 1; position: relative;">
                            @php
                            $heightPercentage2 = ($dsDotThi['value5nam'][$dsdotxettotnghiep['maxnamxettotnghiep'] - 2] / $dsdotxettotnghiep['maxtotnhghiep']) * 100 + 1;
                            $heightPercentage21 = ($dsdotxettotnghiep['value5namtotnghiep'][$dsdotxettotnghiep['maxnamxettotnghiep'] - 2] / $dsdotxettotnghiep['maxtotnhghiep']) * 100 + 1;
                            @endphp
                            <div
                                style=" width: 40%; height: {{$heightPercentage2}}% ; background: red;  position: absolute; bottom: 0; left: 10%;">
                                <span style="position: absolute; top:-17px; left: 31%;">{{$dsDotThi['value5nam'][$dsdotxettotnghiep['maxnamxettotnghiep'] - 2]}}
                                </span>

                            </div>
                            <div
                                style=" width: 40%; height: {{$heightPercentage21}}% ; background: #00ff29;  position: absolute; bottom: 0; right: 10%;">
                                <span style="position: absolute; top:-17px; left: 31%;">{{$dsdotxettotnghiep['value5namtotnghiep'][$dsdotxettotnghiep['maxnamxettotnghiep'] - 2]}}
                                </span>

                            </div>

                            <span style="
                            position: absolute;                        bottom: -22px;                        right: 27px;                        font-size: 13px;
                            ">{{$dsdotxettotnghiep['maxnamxettotnghiep'] - 2}}</span>
                        </span>
                        <span style="width: calc(100% / 5); flex: 1; position: relative;">
                            @php
                            $heightPercentage1 = ($dsDotThi['value5nam'][$dsdotxettotnghiep['maxnamxettotnghiep'] - 1] / $dsdotxettotnghiep['maxtotnhghiep']) * 100 + 1;
                            $heightPercentage12 = ($dsdotxettotnghiep['value5namtotnghiep'][$dsdotxettotnghiep['maxnamxettotnghiep'] - 1] / $dsdotxettotnghiep['maxtotnhghiep']) * 100 + 1;
                            @endphp
                            <div
                                style=" width: 40%; height: {{$heightPercentage1}}% ; background: red;  position: absolute; bottom: 0; left: 10%;">
                                <span style="position: absolute; top:-17px; left: 31%;">{{$dsDotThi['value5nam'][$dsdotxettotnghiep['maxnamxettotnghiep'] - 1]}}
                                </span>
                            </div>
                            <div
                                style=" width: 40%; height: {{$heightPercentage12}}% ; background: #00ff29;  position: absolute; bottom: 0; right: 10%;">
                                <span style="position: absolute; top:-17px; left: 31%;">{{$dsdotxettotnghiep['value5namtotnghiep'][$dsdotxettotnghiep['maxnamxettotnghiep'] - 1]}}
                                </span>
                            </div>

                            <span style="                        position: absolute;                        bottom: -22px;                        right: 27px;                        font-size: 13px;
                            ">{{$dsdotxettotnghiep['maxnamxettotnghiep'] - 1}}</span>
                        </span>
                        <span style="width: calc(100% / 5); flex: 1; position: relative;">
                            @php
                            $heightPercentage0 = ($dsDotThi['value5nam'][$dsdotxettotnghiep['maxnamxettotnghiep']] / $dsdotxettotnghiep['maxtotnhghiep']) * 100 + 1 ;
                            $heightPercentage01 = ($dsdotxettotnghiep['value5namtotnghiep'][$dsdotxettotnghiep['maxnamxettotnghiep']] / $dsdotxettotnghiep['maxtotnhghiep']) * 100 + 1;
                            
                            @endphp
                            <div
                                style=" width: 40%; height: {{$heightPercentage0}}% ; background: red;  position: absolute; bottom: 0; left: 10%;">
                                <span style="position: absolute; top:-17px; left: 31%;">{{$dsDotThi['value5nam'][$dsdotxettotnghiep['maxnamxettotnghiep']]}}
                                </span>
                            </div>
                            <div
                                style=" width: 40%; height: {{$heightPercentage01}}% ; background: #00ff29;  position: absolute; bottom: 0; right: 10%;">
                                <span style="position: absolute; top:-17px; left: 31%;">{{$dsdotxettotnghiep['value5namtotnghiep'][$dsdotxettotnghiep['maxnamxettotnghiep']]}}
                                </span>
                            </div>

                            <span style="                        position: absolute;                        bottom: -22px;                        right: 27px;                        font-size: 13px;
                            ">{{$dsdotxettotnghiep['maxnamxettotnghiep']}}</span>
                        </span>
                        <span style="position: absolute;    right: 19%;    bottom: 16%;">Năm</span>

                        <div style="position: absolute;    right: 2%;    bottom: 34%;  width: 125px;">
                                <div style="    display: flex; justify-content: left; margin-bottom: 5px; "> <div style="  margin-right: 5px;  width: 20px;    height: 20px;    background: red;"></div> - đợt thi </div>
                                <div style="   display: flex; justify-content: left; margin-bottom: 5px; "> <div style="  margin-right: 5px;  width: 20px;    height: 20px;    background: #00ff29;"></div> - xét tốt nghiệp</div>
                        </div>


                    </div>
                </div>
                <div class="icon">
                    <i class="fa fa-graduation-cap" style="color: white;"></i>
                </div>
                <a href="{{ route('lop-hoc.index') }}" class="small-box-footer">Chi tiết <i
                        class="fa fa-arrow-circle-right"></i></a>
            </div>
        </div>
    </div>

     <!-- Số lượng sinh viên -->
     <div class="col-lg-6 col-xs-6 hidden-xs">
            <div class="small-box bg-olive text-black">
               

            @php
                // Lấy niên khóa cuối cùng từ mảng danhsachlophocsinhvien
                $nienKhoaKeys = array_keys($danhsachlophocsinhvien);
                $lastNienKhoa = end($nienKhoaKeys);
            @endphp
            <p style="    margin: 0;
                            padding: 10px;
                            font-size: 20px;
                        ">Niên Khóa
                <select id="nienKhoaSelect">
                    <option value="{{ $lastNienKhoa }}">{{$lastNienKhoa}}</option>
                    @foreach(array_keys($danhsachlophocsinhvien) as $nk_ten)
                        <option value="{{ $nk_ten }}">{{ $nk_ten }}</option>
                    @endforeach
                </select></p>
                <div id="lopHocsContainer">
                    <!-- Lớp học và sinh viên sẽ được hiển thị ở đây -->
                </div>


                <div class="icon">
                    <i class="fa fa-child"></i>
                </div>
                <a href="http://localhost/cea-2.1/public/lop-hoc/" class="small-box-footer">Chi tiết <i class="fa fa-arrow-circle-right"></i></a>
            </div>
        </div>


    </div>

<div class="row">
    <!-- Thông tin cán bộ -->
    <div class="col-md-6">
        <div class="box box-default" style="max-width: 650px;border: 2px solid #605ca8;border-radius: 6px;">
            <div class="box-body">
                <div class="row">
                    <div class="col-md-5 ">
                        @if ($infoUser->canBo['cb_gioitinh'] == 1)
                        <img src="{{ assetv('/images/default/sinhvien-nam.svg') }}"
                            style="margin-top: -24px;margin-bottom: -19px;" />
                        @else
                        <img src="{{ assetv('/images/default/sinhvien.svg') }}"
                            style="margin-top: -24px;margin-bottom: -19px;" />
                        @endif
                    </div>
                    <div class="col-md-7 ">
                        <table class="table">
                            <tr>
                                <th style="width: 120px;border:none">MSCB: </th>
                                <td style="border:none">{{ $infoUser->canBo['cb_ma'] }}</td>
                            </tr>
                            <tr>
                                <th>Họ tên: </th>
                                <td>{{ $infoUser->canBo['cb_ho'].' '.$infoUser->canBo['cb_ten'] }}</td>
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
</div>

<div class="row">
    <div class="col-md-6">
            <a class="btn btn-block btn-social btn-twitter btn-lg"
                href="{{ assetv('/TaiLieuHDSD_QuanLyDiemSinhVien_v2.doc') }}" target="_blank" download>
                <i class="fa fa-download"></i> Tài liệu hướng dẫn
            </a>
        </div>
</div>





@endsection

@push('scripts')
<script type="text/javascript">
        document.addEventListener('DOMContentLoaded', function() {
        const nienKhoaSelect = document.getElementById('nienKhoaSelect');
        const lopHocsContainer = document.getElementById('lopHocsContainer');
        const danhsachlophocsinhvien = @json($danhsachlophocsinhvien);
        function round(value) {
            return Math.round(value);
        }
        function updateLopHocs() {
            const selectedNienKhoa = nienKhoaSelect.value;
            lopHocsContainer.innerHTML = '';

            if (selectedNienKhoa && danhsachlophocsinhvien[selectedNienKhoa]) {
                const nienKhoaData = danhsachlophocsinhvien[selectedNienKhoa];
                const soLuongMaxSinhVienLop = nienKhoaData.max_sinh_vien_lop;
                const soLuongLop = nienKhoaData.so_luong_lop;
                const lopHocs = nienKhoaData.lops;
                let content = `
                    <div style="display: flex;    justify-content: center; padding: 10px 0 20px 0; margin-bottom: 37px;">
                        <div style="background: blue;
                                width: 30px;
                                height: 20px;
                                margin-right: 5px;
                                "></div> - Tốt nghỉ
                        <div style="background: green;
                                width: 30px;
                                height: 20px;
                                margin-right: 5px;
                                margin-left: 15px;"></div> - Nghỉ
                        <div style="background: red;
                                width: 30px;
                                height: 20px;
                                margin-right: 5px;
                                margin-left: 15px;"></div> - Đang học
                    </div>
                `;

                content += `
                            <div style="width: 100%; padding: 0 40px;    margin-bottom: 130px;">
                                <div style="width: 98%; height: 222px; border: 1px solid black; border-top: none; border-right: none; position: relative;">
                                <div style="position: absolute; top: -30px; font-size: 20px; left: -30px;">Sinh viên</div>
                                <div style="position: absolute; font-size: 20px; bottom: -9px; right: -37px;">Lớp</div>
                                    <div style="position: absolute;
                                        top: 0;   left: -20px;  width: 20px;
                                        height: 100%; display: flex; flex-direction: column;
                                        text-align: center;">
                                            <div style="height: calc(100% / 3); width: 100%;text-align: center;    width: 100%; ">${soLuongMaxSinhVienLop}</div>
                                            <div style="height: calc(100% / 3); width: 100%; text-align: center;    width: 100%;  display: flex; align-items: center; justify-content: center;">${round(soLuongMaxSinhVienLop / 2)}</div>
                                            <div style="height: calc(100% / 3); width: 100%; text-align: center;    width: 100%;  display: flex; align-items: center;align-items: flex-end; justify-content: center;">0</div>
                                        </div>
                                    <div style="width: 100%; height: 100%; display: flex; justify-content: space-between;">
                                        ${Object.values(lopHocs).map(lop => `
                                            <div title="Lớp ${lop.lop_ten} có ${lop.slsinhvienlop} sinh viên trong đó &#10; - ${lop.slsvtotnghiep} sinh viên tốt nghiệp &#10; - ${lop.slsvxoaten} sinh viên tạm nghỉ &#10; - ${lop.slsvconlai} sinh viên đang học" style="width: calc(100 / ${soLuongLop})%;  max-width: 50px; min-width: 25px; height: 100%; display: flex;position: relative; justify-content: space-between; align-items: flex-end;">
                                            <div style="    position: absolute;
                                                            writing-mode: vertical-rl;
                                                            bottom: -130px;
                                                            left: 5px;
                                                            width: 100%;
                                                            height: 120px;
                                                            display: flex;     align-items: center;     cursor: default;" title="${lop.lop_ten}" >
                                                            ${lop.lop_ma}</div>
                                                <div style="width: calc(100% / 3); height: ${((lop.slsvtotnghiep / soLuongMaxSinhVienLop) * 100) + 1}%; position: relative; background: blue;">
                                                     <span style="position: absolute; font-size: 8px; top: -13px; left: 0;">${lop.slsvtotnghiep}</span></div>
                                                <div style="width: calc(100% / 3); height: ${((lop.slsvxoaten / soLuongMaxSinhVienLop) * 100) + 1}%; position: relative; background: green;">
                                                     <span style="position: absolute; font-size: 8px; top: -13px; left: 0;">${lop.slsvxoaten}</span></div>
                                                <div style="width: calc(100% / 3); height: ${((lop.slsvconlai / soLuongMaxSinhVienLop) * 100) + 1}%; position: relative; background: red;">
                                                     <span style="position: absolute; font-size: 8px; top: -13px; left: 0;">${lop.slsvconlai}</span></div>
                                                
                                            </div>
                                        `).join('')}
                                    </div>
                                </div>
                            </div>
                        `;

                 lopHocsContainer.innerHTML = content;
            }
        }

        // Gọi hàm updateLopHocs khi trang được tải xong
        updateLopHocs();

        // Gọi hàm updateLopHocs khi lựa chọn niên khóa thay đổi
        nienKhoaSelect.addEventListener('change', updateLopHocs);
    });
</script>
@endpush