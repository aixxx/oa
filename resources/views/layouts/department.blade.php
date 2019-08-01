<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}</title>
    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/dhtmlxtreeview.css') }}" rel="stylesheet">
    <script src="{{ asset('js/dhtmlxtreeview.js') }}"></script>
</head>
<body>
    <div class="page-main">
        <div class="my-3 my-md-5">
            @yield('content')
        </div>
    </div>
</body>
</html>