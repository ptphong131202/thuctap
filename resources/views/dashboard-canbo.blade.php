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
                        <h4 class="css_h4">Ngành, nghề đào tạo</h4>
                        <div class="hidden-xs div_nn">
                            <p class="div_nn_p"><span
                            class="div_nn_p_span">{{ $nganhnghe['slNganhNghe_TrungCap'] }}</span> Trung cấp </p>
                            <p class="div_nn_p"> <span
                            class="div_nn_p_span">{{ $nganhnghe['slNganhNghe_CaoDang'] }}</span> Cao đẳng</p>
                        </div>
                    </div>
                    <!-- icon (Phong)-->
                    <div class="icon">
                        <i class="fas fa-screwdriver-wrench" style="color: white;"></i>
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
                    <div class="hidden-xs div_kdt" >
                        <div class="div_kdt_div1">
                                    <div class="div_kdt_div1_1">Hệ Cao Đẳng</div>
                                <div class="">
                                    @foreach ($khoadaotaocaodangQuery as $caodang)
                                    <p class="div_kdt_div1_1_p">
                                    <a href="http://localhost/cea-2.0/public/khoa-dao-tao/{{ $caodang->kdt_id }}/hoc-ky">
                                        {{ $caodang->kdt_ten }}
                                    </a>
                                        </p>
                                        @endforeach
                                </div>
                        </div>
                        <div  class="div_kdt_div1">
                            <div class="div_kdt_div1_1">Hệ Trung Cấp</div>
                                <div class="">
                                    @foreach ($khoadaotaotrungcapQuery as $caodang)
                                    <p  class="div_kdt_div1_1_p">
                                    <a href="http://localhost/cea-2.0/public/khoa-dao-tao/{{ $caodang->kdt_id }}/hoc-ky">
                                        {{ $caodang->kdt_ten }}
                                    </a>
                                        </p>
                                        @endforeach
                                </div>
                        </div>
                </div>
                </div>
                <div class="icon">
                    <i class="fa-solid fa-gears" style="color: white;"></i>
                </div>
                <a href="{{ route('khoa-dao-tao.index') }}" class="small-box-footer">Chi tiết <i
                        class="fa fa-arrow-circle-right"></i></a>
            </div>
        </div>

    
        <div class="col-lg-6 col-xs-6 hidden-xs p-0"  >
                <div class="small-box bg-orange">
                    <div class="inner">
                        <h3 class="d-none d-sm-block">{{ $thongKe['slLopHoc'] }}</h3>
                        <p class="d-none d-sm-block">Lớp học</p>
                        <div class="hidden-xs div_lh">
                            <span class="div_lh_span">
                                <div class="div_lh_span_div1">{{$maxLopHoc}}</div>
                                <div class="div_lh_span_div2">{{ round($maxLopHoc / 2) }}</div>
                                <div class="div_lh_span_div3">0</div>
                            </span>
                            <span class="div_lh_span2">
                                @php
                                $heightPercentage = ($lophocs[0]['sllop'] / $maxLopHoc) * 100;
                                @endphp
                                <div
                                    class="div_lh_span_div_chart" style=" height: {{$heightPercentage}}% ;">
                                    <span class="div_lh_span_div_chart_span">{{$lophocs[0]['sllop']}}
                                    </span>
                                </div>
                                <span class="div_lh_span_div_chart_span2">{{$lophocs[0]['nk_ten']}}</span>
                            </span>
                            <span style="width: calc(100% / 5); flex: 1; position: relative;">
                                @php
                                $heightPercentage = ($lophocs[1]['sllop'] / $maxLopHoc) * 100;
                                @endphp
                                <div
                                    class="div_lh_span_div_chart" style=" height: {{$heightPercentage}}% ;">
                                    <span class="div_lh_span_div_chart_span">{{$lophocs[1]['sllop']}}
                                    </span>
                                </div>

                                <span class="div_lh_span_div_chart_span2">{{$lophocs[1]['nk_ten']}}</span>
                            </span>
                            <span style="width: calc(100% / 5); flex: 1; position: relative;">
                                @php
                                $heightPercentage = ($lophocs[2]['sllop'] / $maxLopHoc) * 100;
                                @endphp
                                <div
                                    class="div_lh_span_div_chart" style=" height: {{$heightPercentage}}% ;">
                                    <span class="div_lh_span_div_chart_span">{{$lophocs[2]['sllop']}}
                                    </span>

                                </div>

                                <span class="div_lh_span_div_chart_span2">{{$lophocs[2]['nk_ten']}}</span>
                            </span>
                            <span style="width: calc(100% / 5); flex: 1; position: relative;">
                                @php
                                $heightPercentage = ($lophocs[3]['sllop'] / $maxLopHoc) * 100;
                                @endphp
                                <div
                                    class="div_lh_span_div_chart" style=" height: {{$heightPercentage}}% ;">
                                    <span class="div_lh_span_div_chart_span">{{$lophocs[3]['sllop']}}
                                    </span>
                                </div>

                                <span class="div_lh_span_div_chart_span2">{{$lophocs[3]['nk_ten']}}</span>
                            </span>
                            <span style="width: calc(100% / 5); flex: 1; position: relative;">
                                @php
                                $heightPercentage = ($lophocs[4]['sllop'] / $maxLopHoc) * 100;
                                @endphp
                                <div
                                    class="div_lh_span_div_chart" style=" height: {{$heightPercentage}}% ;">
                                    <span class="div_lh_span_div_chart_span">{{$lophocs[4]['sllop']}}
                                    </span>
                                </div>

                                <span class="div_lh_span_div_chart_span2">{{$lophocs[4]['nk_ten']}}</span>
                            </span>
                            <span class="div_lh_span_div_chart_span3">Niên khóa</span>
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
        <div class="col-lg-6 col-xs-6 hidden-xs" >
            <div class="small-box bg-olive">
                <div class="inner">
                    <!-- <h3 class="d-none d-sm-block">{{ $thongKe['slLopHoc'] }}</h3> -->
                    <p class="d-none d-sm-block">Đợt thi, xét tốt nghiệp</p>
                    <div style="width: 100%;display: flex; justify-content: center; margin-bottom: 26px;">
                                <div style="    display: flex; justify-content: left; margin-bottom: 5px; margin-right: 10px; "> <div style="  margin-right: 5px;  width: 20px;    height: 20px;    background: red;"></div> - đợt thi </div>
                                <div style="   display: flex; justify-content: left; margin-bottom: 5px; "> <div style="  margin-right: 5px;  width: 20px;    height: 20px;    background: #00ff29;"></div> - xét tốt nghiệp</div>
                        </div>
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
                                <span class="div_lh_span_div_chart_span">{{$dsDotThi['value5nam'][$dsdotxettotnghiep['maxnamxettotnghiep'] - 4]}}
                                </span>
                            </div>
                            <div
                                style=" width: 40%; height: {{$heightPercentage}}% ; background: #00ff29;  position: absolute; bottom: 0; right: 10%;">
                                <span class="div_lh_span_div_chart_span">{{$dsdotxettotnghiep['value5namtotnghiep'][$dsdotxettotnghiep['maxnamxettotnghiep'] - 4]}}
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
                                <span class="div_lh_span_div_chart_span">{{$dsDotThi['value5nam'][$dsdotxettotnghiep['maxnamxettotnghiep'] - 3]}}
                                </span>
                            </div>
                            <div
                                style=" width: 40%; height: {{$heightPercentage31}}% ; background: #00ff29;  position: absolute; bottom: 0; right: 10%;">
                                <span class="div_lh_span_div_chart_span">{{$dsdotxettotnghiep['value5namtotnghiep'][$dsdotxettotnghiep['maxnamxettotnghiep'] - 3]}}
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
                                <span class="div_lh_span_div_chart_span">{{$dsDotThi['value5nam'][$dsdotxettotnghiep['maxnamxettotnghiep'] - 2]}}
                                </span>

                            </div>
                            <div
                                style=" width: 40%; height: {{$heightPercentage21}}% ; background: #00ff29;  position: absolute; bottom: 0; right: 10%;">
                                <span class="div_lh_span_div_chart_span">{{$dsdotxettotnghiep['value5namtotnghiep'][$dsdotxettotnghiep['maxnamxettotnghiep'] - 2]}}
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
                                <span class="div_lh_span_div_chart_span">{{$dsDotThi['value5nam'][$dsdotxettotnghiep['maxnamxettotnghiep'] - 1]}}
                                </span>
                            </div>
                            <div
                                style=" width: 40%; height: {{$heightPercentage12}}% ; background: #00ff29;  position: absolute; bottom: 0; right: 10%;">
                                <span class="div_lh_span_div_chart_span">{{$dsdotxettotnghiep['value5namtotnghiep'][$dsdotxettotnghiep['maxnamxettotnghiep'] - 1]}}
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
                                <span class="div_lh_span_div_chart_span">{{$dsDotThi['value5nam'][$dsdotxettotnghiep['maxnamxettotnghiep']]}}
                                </span>
                            </div>
                            <div
                                style=" width: 40%; height: {{$heightPercentage01}}% ; background: #00ff29;  position: absolute; bottom: 0; right: 10%;">
                                <span class="div_lh_span_div_chart_span">{{$dsdotxettotnghiep['value5namtotnghiep'][$dsdotxettotnghiep['maxnamxettotnghiep']]}}
                                </span>
                            </div>

                            <span style="                        position: absolute;                        bottom: -22px;                        right: 27px;                        font-size: 13px;
                            ">{{$dsdotxettotnghiep['maxnamxettotnghiep']}}</span>
                        </span>
                        <span style="position: absolute;    right: 19%;    bottom: 16%;">Năm</span>

                        


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
     <div class="col-lg-12 col-xs-12 hidden-xs" >
            <div class="small-box  text-black" style="background: #f8d7d7;">
               

            @php
                // Lấy niên khóa cuối cùng từ mảng danhsachlophocsinhvien
                $nienKhoaKeys = array_keys($danhsachlophocsinhvien);
                $lastNienKhoa = end($nienKhoaKeys);
            @endphp
            <p class="div_sv_p">Niên Khóa
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
                    <i class="fa fa-child" style="color: white;"></i>
                </div>
                <a href="http://localhost/cea-2.0/public/lop-hoc/" class="small-box-footer">Chi tiết <i class="fa fa-arrow-circle-right"></i></a>
            </div>
        </div>


    </div>

<div class="row">
    <!-- Thông tin cán bộ -->
    <div class="col-md-6">
        <div class="box box-default div_canbo">
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
                    <div class="div_sv_div1">
                        <div class="div_sv_div1_chart1"></div> - Tốt nghiệp
                        <div class="div_sv_div1_chart2"></div> - Nghỉ
                        <div class="div_sv_div1_chart3"></div> - Đang học
                    </div>
                `;

                content += `
                            <div class="div_sv_div2">
                                <div class="div_sv_div2_1">
                                <div class="div_sv_div2_2">Sinh viên</div>
                                <div class="div_sv_div2_3">Lớp</div>
                                    <div class="div_sv_div2_3_1">
                                            <div class="div_sv_div2_3_div1">${soLuongMaxSinhVienLop}</div>
                                            <div class="div_sv_div2_3_div2">${round(soLuongMaxSinhVienLop / 2)}</div>
                                            <div class="div_sv_div2_3_div3">0</div>
                                        </div>
                                    <div class="div_sv_div2_3_div4">
                                        ${Object.values(lopHocs).map(lop => `
                                            <div title="Lớp ${lop.lop_ten} có ${lop.slsinhvienlop} sinh viên trong đó &#10; -
                                             ${lop.slsvtotnghiep} sinh viên tốt nghiệp &#10; - ${lop.slsvxoaten} sinh viên tạm nghỉ &#10; 
                                             - ${lop.slsvconlai} sinh viên đang học" style="width: calc(100% / ${soLuongLop}); max-width: 100px;
                                                height: 100%; display: flex;
                                               position: relative; justify-content: space-between; align-items: flex-end;">
                                            <div class="div_sv_div2_3_div5" title="${lop.lop_ten}" >
                                                            ${lop.lop_ma}</div>
                                                <div class="div_sv_div2_3_div6" style="height: ${((lop.slsvtotnghiep / soLuongMaxSinhVienLop) * 100) + 1}%; ">
                                                     <p class="div_sv_div2_3_div8_p">${lop.slsvtotnghiep}</p></div>
                                                <div class="div_sv_div2_3_div7" style="height: ${((lop.slsvxoaten / soLuongMaxSinhVienLop) * 100) + 1}%; ">
                                                     <p class="div_sv_div2_3_div8_p">${lop.slsvxoaten}</p></div>
                                                <div class="div_sv_div2_3_div8" style="height: ${((lop.slsvconlai / soLuongMaxSinhVienLop) * 100) + 1}%; ">
                                                     <p class="div_sv_div2_3_div8_p">${lop.slsvconlai}</p></div>
                                                
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