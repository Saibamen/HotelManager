@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">@lang('auth.change_password')</div>

                <div class="card-body">
                    @include('layouts.messages')

                    <form class="form-horizontal" method="POST" action="{{ route('user.post_change_password') }}">
                        @csrf

                        <div class="form-group row">
                            <label for="current-password" class="col-md-4 col-form-label text-md-right">@lang('auth.current_password')</label>

                            <div class="col-md-6">
                                <input id="current-password" type="password" class="form-control {{ $errors->has('current_password') ? ' is-invalid' : '' }}" name="current_password" required autofocus>

                                @if ($errors->has('current_password'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('current_password') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="new-password" class="col-md-4 col-form-label text-md-right">@lang('auth.new_password')</label>

                            <div class="col-md-6">
                                <input id="new-password" type="password" class="form-control{{ $errors->has('new_password') ? ' is-invalid' : '' }}" name="new_password" required>

                                @if ($errors->has('new_password'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('new_password') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="new-password-confirm" class="col-md-4 col-form-label text-md-right">@lang('auth.password_confirmation')</label>

                            <div class="col-md-6">
                                <input id="new-password-confirm" type="password" class="form-control" name="new_password_confirmation" required>
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    @lang('auth.change_password')
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
