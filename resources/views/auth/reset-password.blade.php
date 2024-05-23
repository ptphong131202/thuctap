@extends('layouts.auth')

@section('content')
<form method="POST" action="{{ route('password.update') }}">
    @csrf

    <input type="hidden" name="token" value="{{ $request->route('token') }}">

    <div class="form-group has-feedback">
        <label for="username">{{ __('auth.username') }}</label>
        <input type="text" name="username" class="form-control" placeholder="@lang('auth.username')" value="{{ old('username') }}">
        <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
        @if ($errors->has('username'))
            <span class="messages form-bar">
                <p class="text-danger error">{{ $errors->first('username') }}</p>
            </span>
        @endif
    </div>

    <div class="form-group has-feedback">
        <label for="password">{{ __('auth.password') }}</label>
        <input type="password" name="password" class="form-control" placeholder="@lang('auth.password')" value="{{ old('password') }}">
        <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
        @if ($errors->has('password'))
            <span class="messages form-bar">
                <p class="text-danger error">{{ $errors->first('password') }}</p>
            </span>
        @endif
    </div>

    <div class="form-group has-feedback">
        <label for="password_confirmation">{{ __('auth.password') }}</label>
        <input type="password" name="password_confirmation" class="form-control" placeholder="@lang('auth.password_confirmation')" value="{{ old('password_confirmation') }}">
        <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
        @if ($errors->has('password_confirmation'))
            <span class="messages form-bar">
                <p class="text-danger error">{{ $errors->first('password_confirmation') }}</p>
            </span>
        @endif
    </div>

    <div class="row">
        <div class="col-xs-12">
            <button type="submit" class="btn btn-primary bg-purple btn-block btn-flat">
                {{ __('auth.reset_password') }}
            </button>
        </div>
    </div>
</form>
@endsection
