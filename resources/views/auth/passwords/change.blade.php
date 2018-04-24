@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">@lang('auth.change_password')</div>

                <div class="panel-body">
                    @include('layouts.messages')

                    <form class="form-horizontal" method="POST" action="{{ route('user.post_change_password') }}">
                        {{ csrf_field() }}

                        <div class="form-group{{ $errors->has('current_password') ? ' has-error' : '' }}">
                            <label for="current-password" class="col-md-4 control-label">@lang('auth.current_password')</label>

                            <div class="col-md-6">
                                <input id="current-password" type="password" class="form-control" name="current_password" required autofocus>

                                @if ($errors->has('current_password'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('current_password') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('new_password') ? ' has-error' : '' }}">
                            <label for="new-password" class="col-md-4 control-label">@lang('auth.new_password')</label>

                            <div class="col-md-6">
                                <input id="new-password" type="password" class="form-control" name="new_password" required>

                                @if ($errors->has('new_password'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('new_password') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="new-password-confirm" class="col-md-4 control-label">@lang('auth.password_confirmation')</label>

                            <div class="col-md-6">
                                <input id="new-password-confirm" type="password" class="form-control" name="new_password_confirmation" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
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
