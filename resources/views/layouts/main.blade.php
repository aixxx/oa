<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <link href="{{ asset('favicon.ico') }}" rel="shortcut icon">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title> @isset($title) {{ $title }} | @endisset {{ config('app.name') }}</title>
    <link href="{{ mix('/static/css/min/screen.css') }}" rel="stylesheet">
@yield('head')
    <script src="/static/vendor/modernizr/modernizr.custom.js"></script>
    <script src="/static/vendor/jquery/dist/jquery.min.js"></script>
    <script src="{{ asset('/vendor/layer/src/layer.js') }}"></script>
</head>
<body>
<!-- CONTENT WRAPPER -->
<div id="app">
    <!-- MENU SIDEBAR WRAPPER -->
    @include('layouts.partials.sidebar')
    <!-- END MENU SIDEBAR WRAPPER -->
    <section class="content-wrapper">
        <!-- TOP TOOLBAR WRAPPER -->
    @include('layouts.partials.toolbar')
        <!-- END TOP TOOLBAR WRAPPER -->
        <section class="content">
            @yield('content')
        </section>
    </section>
</div>
@include('layouts.partials.javascript')
</body>
</html>
