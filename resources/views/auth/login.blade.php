@extends('layouts.auth')

@section('content')
<form action="{{ route('login') }}" method="post">
    @csrf
    <div class="form-group has-feedback">
        <label>@lang('auth.username')</label>
        <input type="text" name="username" class="form-control" placeholder="@lang('auth.username')" value="{{ old('username') }}">
        <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
        @if ($errors->has('username'))
            <span class="messages form-bar">
                <p class="text-danger error">{{ $errors->first('username') }}</p>
            </span>
        @endif
    </div>
    <div class="form-group has-feedback">
        <label>@lang('auth.password')</label>
        <input type="password" name="password" class="form-control" placeholder="@lang('auth.password')">
        <span class="glyphicon glyphicon-lock form-control-feedback"></span>
        @if ($errors->has('password'))
            <span class="messages form-bar">
                <p class="text-danger error">{{ $errors->first('password') }}</p>
            </span>
        @endif
    </div>
    <div class="row">
        <div class="col-xs-12">
            <button type="submit" class="btn btn-primary bg-purple btn-block btn-flat">@lang('auth.sign_in')</button>
        </div>
    </div>
    <div class="row hidden" style="margin-top: 15px;">
        <div class="col-lg-12">
            <a href="{{ route('password.request') }}" class="pull-right">@lang('auth.forgot_password')</a>
        </div>
    </div>
</form>
@endsection

@push('scripts')
@if (env('APP_ENV') === 'local')
<script>
    $(document).ready(function () {
        $('input[name=username]').val('admin');
        $('input[name=password]').val('qlsv@123456');
    });
</script>
@endif
@endpush