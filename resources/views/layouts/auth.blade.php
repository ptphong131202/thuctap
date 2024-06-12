<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    @include('layouts.admin.favicon')
    {!! SEO::generate() !!}
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{ assetv('css/admin_phong.css') }}">
    @stack('style')
</head>

<body class="hold-transition login-page">
    <div class="container cms-info-box" style="padding-top:50px">
        <div class="col-lg-12">
            <div class="col-lg-7 col-md-6 col-sm-12 col-xs-12">
                <a href="/" class="text-black"><h1>CEA<b> - Tra cứu điểm sinh viên</b></h1></a>
                <div class="text-content">
                    @lang('auth.login_to_continue')
                    {{-- <img src="{{ assetv('/images/stories/security-pana-v2.png') }}" alt="Secure login pana" style="width: 100%"> --}}
                    <div class="clearfix"></div>
                    <div class="text-center">
                        <img src="{{ assetv('/images/favicon/android-chrome-512x512.png') }}" alt="Secure login pana" style="width: 60%">
                    </div>
                </div>
            </div>
            <div class="col-lg-5 col-md-6 col-sm-12 col-xs-12 login-form">
                @yield('content')
            </div>
        </div>
    </div>
    <footer class="bs-docs-footer login-footer">
        <div class="container" style="padding-top: 32px">
            <div class="row">
                <div class="col-md-12">
                    <h4><strong>THÔNG TIN LIÊN HỆ</strong></h4>
                    <ul style="padding:0px; list-style:none">
                        <li><strong>Địa chỉ</strong> Số 184 - Tỉnh lộ 923 (Lộ Vòng Cung), P. Phước Thới, Q. Ô Môn, Tp. Cần Thơ</li>
                        <li><strong>Điện thoại</strong> (0292) 3 862 067</li>
                        <li><strong>Fax</strong> (0292) 3 862 791</li>
                        <li><strong>Email</strong> cdcodien08@yahoo.com - cen@moet.edu.vn</li>
                    </ul>
                </div>
                <div class="col-md-12 text-center">
                    © Truong Cao Dang Co Dien Va Nong Nghiep Nam Bo
                </div>
            </div>
        </div>
    </footer>
    <script src="{{ assetv('js/admin.js') }}"></script>
    @stack('scripts')
</body>

</html>