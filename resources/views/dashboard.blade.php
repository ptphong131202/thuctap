@extends('layouts.admin')

@section('header')
@endsection

@section('content')
    <div class="box box-default" style="max-width: 650px;border: 2px solid #605ca8;border-radius: 6px;">
        <div class="box-body">
            <form method="POST" action="http://localhost/cea-2.0/public/user/update-Info-Sv">
                @csrf
                <div class="row">
                    <div class="col-md-5 ">
                        <h3 class="box-title" style="font-size:26px;margin:0px">
                            Xin chào {{ $infoUser->sinhVien['sv_ten'] }}
                        </h3>
                        @if ($infoUser->sinhVien['sv_gioitinh'] == 1)
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
                                <th style="width: 120px;border:none">Họ tên: </th>
                                <td style="border:none">
                                    {{ $infoUser->sinhVien['sv_ho'] . ' ' . $infoUser->sinhVien['sv_ten'] }}
                                </td>
                            </tr>
                            <tr>
                                <th>MSSV: </th>
                                <td>{{ $infoUser->sinhVien['sv_ma'] }}</td>
                                <input type="hidden" name="sv_ma" value="{{ $infoUser->sinhVien['sv_ma'] }}">
                            </tr>
                            <tr>
                                <th>Lớp: </th>
                                <td>
                                    @foreach ($infoUser->sinhVien->lopHoc as $lop)
                                        <div>{{ $lop->lh_ma }} - {{ $lop->lh_ten }}</div>
                                    @endforeach
                                </td>
                            </tr>
                            <tr>
                                <th>Ngày sinh: </th>
                                <td>
                                    @if ($configSv == 1)
                                        <input type="text" class="form-control" id="ngaysinh" name="sv_ngaysinh"
                                            placeholder="ngày/tháng/năm" value="{{ $infoUser->sinhVien['sv_ngaysinh'] }}">
                                    @else
                                        {{ $infoUser->sinhVien['sv_ngaysinh'] }}
                                    @endif

                                    @error('sv_ngaysinh')
                                        <p class="text-danger">{{ $message }}</p>
                                    @enderror
                                </td>
                            </tr>
                            <tr>
                                <th>Email: </th>
                                <td>
                                    @if ($configSv == 1)
                                        <input type="email" class="form-control" id="exampleInputEmail1" name="email"
                                            placeholder="vd: nguyenvana@gmail.com" value="{{ $infoUser->email }}">
                                    @else
                                        {{ $infoUser->email }}
                                    @endif

                                    @error('email')
                                        <p class="text-danger">{{ $message }}</p>
                                    @enderror
                                </td>
                            </tr>
                            <tr>
                                <th>Địa chỉ: </th>
                                <td>
                                    {{-- @if ($configSv == 1)
                                        <textarea class="form-control" id="exampleInputEmail1" placeholder="Địa chỉ...">{{ $infoUser->sinhVien['sv_diachi'] }}</textarea>
                                    @else --}}
                                    {{ $infoUser->sinhVien['sv_diachi'] }}
                                    {{-- @endif --}}
                                </td>
                            </tr>
                        </table>

                        @if (session('success_message'))
                            <p class="text-success">
                                {{ session('success_message') }}
                            </p>
                        @endif

                        {{-- @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif --}}


                        @if ($configSv == 1)
                            <button class="btn btn-success pull-right"><i class="fa fa-save"></i> Cập nhật</button>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script type="text/javascript"></script>
@endpush
