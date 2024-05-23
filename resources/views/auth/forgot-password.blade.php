@extends('layouts.auth')

@section('content')
@if (session('status'))
    <div class="alert alert-success" role="alert">
        {{ session('status') }}
    </div>
@endif

<form method="POST" action="{{ route('password.email') }}">
    @csrf

    <div class="form-group has-feedback">
        <label>@lang('auth.email')</label>
        <input type="text" name="email" class="form-control" placeholder="@lang('auth.email')" value="{{ old('email') }}">
        <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
        @if ($errors->has('email'))
            <span class="messages form-bar">
                <p class="text-danger error">{{ $errors->first('email') }}</p>
            </span>
        @endif
    </div>

    <div class="row">
        <div class="col-xs-12">
            <button type="submit" class="btn btn-primary bg-purple btn-block btn-flat">{{ __('auth.reset_password') }}</button>
        </div>
    </div>
</form>
@endsection
