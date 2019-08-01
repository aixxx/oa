@extends('layouts.admin')

@section('content')
    <br/>
    <section class="page-content container-fluid">
        <h5 class="sign-in-heading text-center">设置新密码</h5>
        @if(session('message'))
            <div class="alert alert-success alert-dismissible fade show text-center" role="alert">
                <strong>{{  session('message')  }}</strong>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true" class="la la-close"></span>
                </button>
            </div>
        @endif
        <div class="card">
            <form method="POST" action="{{ route('admin.resetpasswordstore') }}">
                @csrf
                <div class="form-body">
                    <div class="card-body">
                        <div class="form-group row">
                            <label for="oldpassword" class="form-label">旧密码</label>
                            <input id="oldpassword" type="password"
                                   class="form-control{{ $errors->has('oldpassword') ? ' is-invalid' : '' }}"
                                   name="oldpassword" value="{{ $oldpassword ?? old('oldpassword') }}" required autofocus>
                            @if ($errors->has('oldpassword'))
                                <span class="invalid-feedback">
            <strong>{{ $errors->first('oldpassword') }}</strong>
        </span>
                            @endif
                        </div>

                        <div class="form-group row">
                            <label for="password" class="form-label">新密码(长度为8-16，由数字、字母和特殊字符组成)</label>
                            <input id="password" type="password"
                                   class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password"
                                   required>
                            @if ($errors->has('password'))
                                <span class="invalid-feedback">
            <strong>{{ $errors->first('password') }}</strong>
        </span>
                            @endif
                        </div>

                        <div class="form-group row">
                            <label for="password-confirm" class="form-label">确认新密码</label>
                            <input id="password-confirm" type="password" class="form-control" name="password_confirmation"
                                   required>
                        </div>
                        <button class="btn btn-primary btn-block">保存</button>
                    </div>
                </div>
            </form>
        </div>
    </section>
@endsection
