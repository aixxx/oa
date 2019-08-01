<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}</title>
    <link href="{{ asset('static/css/min/screen.css') }}" rel="stylesheet">
    <script src="/static/vendor/modernizr/modernizr.custom.js"></script>
    <script src="/static/vendor/jquery/dist/jquery.min.js"></script>
</head>
<body class="sign-in-page">
<div class="container">
    <div class="sign-in-form">
        <div class="card">
            @if(session('message'))
                <div class="alert alert-success alert-dismissible fade show text-center" role="alert">
                    <strong>{{  session('message')  }}</strong>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true" class="la la-close"></span>
                    </button>
                </div>
            @endif
            <div class="card-body">
                <a href="/" class="brand text-center d-block m-b-20">
                     <img src="/static/img/logo.png" width="70px" height="70px">
					<p id="logo" style="font-size:16px;color:#333;" >艾克智能ERP V1.0</p>
                        
                </a>
                @yield('content')
            </div>
        </div>
    </div>
</div>
<div class="footer text-right">
    <span>&copy;{{ date('Y',time()) }} 艾克科技有限公司</span>
</div>
@include('layouts.partials.javascript')
</body>
</html>
