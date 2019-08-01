<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>@yield('title')</title>
        <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    </head>
    <body>
        <div class="page">
            <div class="page-content">
                <div class="container text-center">
                    <div class="display-2 text-muted mb-5"><i class="fe fe-cloud-drizzle"></i> @yield('title')</div>
                    <h1 class="h2 mb-3">@yield('message')</h1>
                    <h1 class="h3 mb-3">@yield('tips')</h1>
                    <p class="h4 text-muted font-weight-normal mb-7">We are sorry but our service is currently not available&hellip;</p>
                    <a class="btn btn-primary" href="javascript:history.back()">
                        <i class="fe fe-arrow-left mr-2"></i>返回
                    </a>
                </div>
            </div>
        </div>
    </body>
</html>