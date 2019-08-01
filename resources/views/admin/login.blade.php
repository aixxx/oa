@extends('layouts.auth')
@section('content')
    <style>
        .tab-content {
            min-height: 300px;
        }
        .row-margin {
            display: flex;
            flex-wrap: wrap;
        }
    </style>
    <h5 class="sign-in-heading text-center">欢迎回来，请登录！222</h5>
    <div class="tab-content">
        <div role="tabpanel" class="tab-pane fade" aria-labelledby="code-tab">
            <div id="" class="text-center">
                <h2>&nbsp;</h2>
                <a href="">使用OA一键登录</a>
            </div>
        </div>
        <div role="tabpanel">
            <form id="loginForm" method="POST" action="/admin/login">
                @csrf
                <div class="form-group">
                    <label for="name" class="form-label">账号</label>
                    <div class="row-margin">
                        <input id="name"
                               class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}" name="name"
                               value="{{ old('name') }}" required autofocus>
                        @if ($errors->has('name'))
                            <span class="invalid-feedback"><strong>{{ $errors->first('name') }}</strong></span>
                        @endif
                    </div>
                </div>
                <div class="form-group">
                    <label for="password" class="form-label">密码</label>
                    <div class="row-margin">
                        <input id="password" type="password"
                               class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password"
                               required>
                        @if ($errors->has('password'))
                            <span class="invalid-feedback"><strong>{{ $errors->first('password') }}</strong></span>
                        @endif
                    </div>
                </div>
                <!--<div class="form-group">
                    <label for="name" class="form-label">企业微信验证码</label>
                    <div class="row-margin">
                        <input id="check_code"
                               class="form-control{{ $errors->has('check_code') ? ' is-invalid' : '' }} col-md-7"
                               name="check_code"
                               value="{{ old('check_code') }}" required autofocus>
                        @if ($errors->has('check_code'))
                            <span class="invalid-feedback"><strong>{{ $errors->first('check_code') }}</strong></span>
                        @endif
                        <p class="col-md-1"></p>
                        <button type="button" class="btn btn-info btn-block send col-md-4">发送</button>
                    </div>
                </div>-->
                <div class="form-footer row-margin">
                    <button type="submit" class="btn btn-primary btn-block">登录</button>
                </div>
            </form>
        </div>
        <div role="tabpanel" class="tab-pane fade show active" id="code" aria-labelledby="code-tab">
            <div id="qrcode" class="text-center"></div>
        </div>
    </div>
@endsection
@section('javascript')
    <script type="text/javascript">
        $(".send").click(function () {
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: "POST",
                dataType: "json",
                data: {'name': $('#name').val(), 'password': $('#password').val()},
                url: "{{ route('admin.check') }}",
                success: function (response) {
                    message_show_info(response.message);
                }
            })
        });
    </script>
@endsection