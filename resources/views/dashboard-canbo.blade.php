<!-- T.Phong chcinhr sửa file này -->

@extends('layouts.admin')

@section('header')
@endsection

@section('content')
<div class="row">

    <!-- lớp học -->
    <div class="col-lg-6 col-xs-6">
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
                                <a style="color: white;" href="http://localhost/cea_3.0/public/khoa-dao-tao/{{ $caodang->kdt_id }}/hoc-ky">
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
                                <a style="color: white;" href="http://localhost/cea_3.0/public/khoa-dao-tao/{{ $caodang->kdt_id }}/hoc-ky">
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


    <!-- Ngành nghề (Phong)-->
    <div class="col-lg-6 col-xs-6">
        <div class="small-box bg-purple">
            <div class="inner">
                <h3  class="d-block d-sm-none" style="">{{ $nganhnghe['slNganhNghe'] }}</h3>
            
                <h4 style="margin: 0 0 5px 5px; font-size: 18px; justify-content: space-between;">Ngành, nghề đào tạo</h4>
                <div class="hidden-xs" style="display: flex;    width: 100%;">
                    <p style="width: 50%;    text-align: center; font-size: 20px;">Trung cấp <span
                    style="display: block; font-size: 41px;">{{ $nganhnghe['slNganhNghe_TrungCap'] }}</span> Ngành, nghề</p>
                    <p style="width: 50%;  text-align: center;  font-size: 20px;">Cao đẳng <span
                    style="display: block; font-size: 41px;">{{ $nganhnghe['slNganhNghe_CaoDang'] }}</span> Ngành, nghề</p>
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

    <!-- Đợt xét tốt nghiệp (Phong)-->
    <div class="col-lg-6 col-xs-6">
        <div class="small-box bg-olive">
            <div class="inner">
                <!-- <h3 class="d-none d-sm-block">{{ $thongKe['slLopHoc'] }}</h3> -->
                <p class="d-none d-sm-block">Đợt thi, xét tốt nghiệp</p>
                <div class="hidden-xs"
                    style="border: 1px solid white;
                    height: 158px; width: 70%; margin-left: 28px;  border-right: none; border-top: none; margin-top: -5px; display: flex; margin-bottom: 19px;">
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
                            <div style="    display: flex; justify-content: left; margin-bottom: 5px;"> <div style="    width: 20px;    height: 20px;    background: red;"></div> - Trung cấp </div>
                            <div style="   display: flex; justify-content: left; margin-bottom: 5px;"> <div style="    width: 20px;    height: 20px;    background: #00ff29;"></div> - Cao đẳng</div>
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
    

   
   <!-- Số lượng sinh viên -->
    <div class="col-lg-6 col-xs-6">
        <div class="small-box bg-olive text-black">
            @foreach ($kqnienkhoa as $nienKhoaInfo)
                <p style="padding: 10px 0 0 10px;     margin: 0;    font-size: 16px;">Khóa {{ $nienKhoaInfo['nk_ten'] }}</p>
                <div style="    font-size: 16px;    display: flex;    width: 100%;    height: 12px;    justify-content: center;    align-items: center;"><div style="width: 25px;
                    height: 15px; margin-right: 5px;    background: blue;"></div> - Tốt nghiệp  <div style="width: 25px;    height: 15px; margin-right: 5px;  margin-left: 10px;  background: green;"></div> - Nghỉ  <div style="width: 25px; margin-left: 10px;   height: 15px; margin-right: 5px;
                    background: red;"></div> - Đang học </div>
                <p style="padding: 0 0 0 10px;     margin: 0;    font-size: 16px;">Sinh viên</p>
    
                <div class="inner" style="padding: 0px 30px; height: 250px; ">
                       <!--  <p>&emsp;Số lượng lớp: {{ $nienKhoaInfo['số lượng lớp'] }}</p>
                        @foreach ($nienKhoaInfo['lophocs'] as $lopInfo)
                            <p>&emsp;&emsp;Lớp {{ $lopInfo['lop_ma'] }}:</p>
                            <p>&emsp;&emsp;Lớp {{ $lopInfo['lop_ten'] }}:</p>
                            <p>&emsp;&emsp;&emsp;Số lượng sinh viên tốt nghiệp: {{ $lopInfo['số lượng sinh viên tốt nghiệp'] }}</p>
                            <p>&emsp;&emsp;&emsp;Số lượng sinh viên xóa tên: {{ $lopInfo['số lượng sinh viên xóa tên'] }}</p>
                            <p>&emsp;&emsp;&emsp;Số lượng còn lại: {{ $lopInfo['số lượng còn lại'] }}</p>
                        @endforeach -->
                        <div style="width: 100%;  display: flex; height: 150px;  border: 1px solid black;   border-top: none;    border-right: none; position: relative;">
                        <div style="position: absolute;    top: 0;    left: -22px; height: 100%;">
                            <div style="padding: 0; margin: 0; width: 100%; height: calc(100% / 3);text-align: center;">{{ $nienKhoaInfo['max_số lượng sinh viên'] }}</div>
                            <div style="padding: 0; margin: 0; width: 100%; height: calc(100% / 3);text-align: center; display: flex; align-items: center;">{{ round($nienKhoaInfo['max_số lượng sinh viên']  / 2)}}</div>
                            <div style="padding: 0; margin: 0; width: 100%; height: calc(100% / 3);text-align: center; display: flex;
    align-items: end;">0</div>
                        </div>
                        @foreach ($nienKhoaInfo['lophocs'] as $lopInfo)
                            @php
                                $widthColumn = (100 / $nienKhoaInfo['số lượng lớp']);
                                $sltong = $lopInfo['số lượng sinh viên tốt nghiệp'] + $lopInfo['số lượng sinh viên xóa tên'] + $lopInfo['số lượng còn lại'];
                                $phantramtonghiep = $lopInfo['số lượng sinh viên tốt nghiệp'] / $nienKhoaInfo['max_số lượng sinh viên'] * 100;
                                $phantramtonghiep = $phantramtonghiep > 0 ? $phantramtonghiep : 1;
                                $phantramxoa = $lopInfo['số lượng sinh viên xóa tên']  / $nienKhoaInfo['max_số lượng sinh viên'] * 100;
                                $phantramxoa = $phantramxoa > 0 ? $phantramxoa : 1;
                                $phantramconlai =  $lopInfo['số lượng còn lại']  / $nienKhoaInfo['max_số lượng sinh viên'] * 100;
                                $phantramconlai = $phantramconlai > 0 ? $phantramconlai : 1;
                            @endphp
                            <div style="width: {{$widthColumn}}%; position: relative;">
                                <a href="http://localhost/cea_3.0/public/lop-hoc/{{$lopInfo['lop_id']}}" style="color: black;">
                                    <div style="width: 80%; height: 100%; margin: 0 auto; display: flex; align-items: flex-end;" title="{{ $lopInfo['lop_ten'] }} tổng cộng có {{$sltong}} sinh viên trong đó: &#10; - {{$lopInfo['số lượng sinh viên tốt nghiệp']}} sinh viêntốt nghiệp  &#10; - {{$lopInfo['số lượng sinh viên xóa tên']}} sinh viên tạm nghỉ &#10; - {{$lopInfo['số lượng còn lại']}} sinh viên đang học
                                    ">
                                        <p style="margin: 0; padding: 0; background: blue; width: calc(100% / 3); height: {{$phantramtonghiep}}%;"><span style="writing-mode: vertical-rl;
                                        font-size: 10px;
                                        transform: translateX(-5px) translateY(-18px); ">{{$lopInfo['số lượng sinh viên tốt nghiệp']}}</span></p>
                                                                        <p style="margin: 0; padding: 0; background: green; width: calc(100% / 3); height: {{$phantramxoa}}%;"><span style="writing-mode: vertical-rl;
                                        font-size: 10px;
                                        transform: translateX(-5px) translateY(-18px); ">{{$lopInfo['số lượng sinh viên xóa tên']}}</span></p>
                                                                        <p style="margin: 0; padding: 0; background: red; width: calc(100% / 3); height: {{$phantramconlai}}%;"><span style="writing-mode: vertical-rl;
                                        font-size: 10px;
                                        transform: translateX(-5px) translateY(-18px); ">{{$lopInfo['số lượng còn lại'] }}</span></p>
                                    </div>
                                </a>
                                <span title="{{ $lopInfo['lop_ten'] }}" style=" writing-mode: vertical-rl; position: absolute; bottom: -85px; height: 80px;">
                                    <a style="color: black;" href="http://localhost/cea_3.0/public/lop-hoc/{{$lopInfo['lop_id']}}">{{ $lopInfo['lop_ma'] }}</a>
                                </span>
                            </div>
                            @endforeach
                            <p style="padding: 0 0 0 10px;     margin: 0;    font-size: 14px; position: absolute;    top: 100%;    right: -20px;">Lớp</p>
                        </div>
                    </div>
            @endforeach

            <div class="icon">
                <i class="fa fa-child"></i>
            </div>
            <a href="http://localhost/cea_3.0/public/lop-hoc/" class="small-box-footer">Chi tiết <i class="fa fa-arrow-circle-right"></i></a>
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
@endpush