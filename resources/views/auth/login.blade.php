@extends('layouts.auth')
@section('content')
    <style>
        .tab-content {
            min-height: 300px;
        }
    </style>
	
    <h5 class="sign-in-heading text-center">登录账户</h5>
    <ul class="nav nav-tabs">
        <li class="nav-item" role="presentation">

        </li>
        @if(config('app.use_oauth'))
            <li class="nav-item" role="presentation">
                <a href="#aike-io" class="nav-link" data-toggle="tab" aria-expanded="true" id="aike-io" onclick="setTitleItem(this)">统一登录</a>
            </li>
        @endif
    </ul>
    <div class="tab-content">
        <div role="tabpanel" class="tab-pane fade" id="aike-io" aria-labelledby="code-tab">
        </div>
        <div role="tabpanel" class="tab-pane fade show active" id="account" aria-labelledby="account-tab">
            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="form-group">
                    <label for="email" class="form-label">邮箱</label>
                    <input id="email" type="email"
                           class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email"
                           value="{{ old('email') }}" required autofocus>
                    @if ($errors->has('email'))
                        <span class="invalid-feedback"><strong>{{ $errors->first('email') }}</strong></span>
                    @endif
                </div>
                <div class="form-group">
                    <label for="password" class="form-label">密码</label>
                    <input id="password" type="password"
                           class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password"
                           required>
                    @if ($errors->has('password'))
                        <span class="invalid-feedback"><strong>{{ $errors->first('password') }}</strong></span>
                    @endif
                    @if($errors->has('password_tips'))
                        <span class="invalid-feedback" style="display: block;"><strong>密码提示：{{ $errors->first('password_tips') }}</strong></span>
                    @endif
                </div>
                <div class="form-group">
                    <label class="custom-checkbox">
                        <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}> 记住我
                    </label>
                </div>
                <div class="form-footer">
                    <button type="submit" class="btn btn-primary btn-block">登录</button>
                </div>
            </form>
        </div>
        <div role="tabpanel" class="tab-pane fade " id="code" aria-labelledby="code-tab">
            <div id="qrcode" class="text-center"></div>
        </div>
    </div>

    <script src="https://rescdn.qqmail.com/node/ww/wwopenmng/js/sso/wwLogin-1.0.0.js"></script>
@endsection
@section('javascript')
    <script>
        $(function () {
            var current_title_item = sessionStorage.getItem('login-title');
            if (current_title_item) {
                deleteShowActive();
                if (current_title_item == 'code') {
                    $("a[href='#code']").addClass('show active');
                    $("#code").addClass('show active');
                } else if (current_title_item == 'account') {
                    $("a[href='#account']").addClass('show active');
                    $("#account").addClass('show active');
                } else if (current_title_item == 'aike-io') {
                    $("a[href='#aike-io']").addClass('show active');
                    $("#aike-io").addClass('show active');
                }
            }
        });

        function deleteShowActive() {
            $("ul[class='nav nav-tabs']").children().each(function () {
                $(this).find("a").removeClass('show active');
            });
            $("div[class='tab-content']").children().each(function () {
                $(this).removeClass('show active');
            });
        }

        function setTitleItem(obj) {
            sessionStorage.setItem('login-title', $(obj).data('id'));
        }
    </script>
@endsection