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
    <link href="{{ asset('css/all.css') }}" rel="stylesheet">
    <!-- Scripts -->
    <script src="{{ asset('assets/js/require.min.js') }}" ></script>
    <script>
        requirejs.config({
            baseUrl: '/'
        });
    </script>
    {{--<script src="{{  asset('js/all.js')  }}"></script>--}}
    <link href="{{ asset('assets/css/dashboard.css') }}" rel="stylesheet">
    <script src="{{ asset('assets/js/dashboard.js') }}" ></script>
    <script src="{{ asset('js/dhtmlxtreeview.js') }}"></script>
    {{--<!-- c3.js Charts Plugin -->--}}
    {{--<link href="/assets/plugins/charts-c3/plugin.css" rel="stylesheet" />--}}
    {{--<script src="/assets/plugins/charts-c3/plugin.js"></script>--}}
    {{--<!-- Google Maps Plugin -->--}}
    {{--<link href="/assets/plugins/maps-google/plugin.css" rel="stylesheet" />--}}
    {{--<script src="/assets/plugins/maps-google/plugin.js"></script>--}}
    <!-- Input Mask Plugin -->
    {{--<script src="/assets/plugins/input-mask/plugin.js"></script>--}}
    {{--<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">--}}
    {{--<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,300i,400,400i,500,500i,600,600i,700,700i&amp;subset=latin-ext">--}}
</head>
<body>
    <div id="app" class="page">
        <div class="page-main">
            <div class="header py-4">
                <div class="container">
                    <div class="d-flex">
                        <a class="header-brand" href="{{ url('/') }}">
                            <img src="/images/logo.svg" class="header-brand-img" alt="{{ config('app.name', 'Laravel') }}">
                        </a>
                        <div class="d-flex order-lg-2 ml-auto">
                            @auth
                            <div class="dropdown">
                                <a href="#" class="nav-link pr-0 leading-none" data-toggle="dropdown">
                                    <span class="avatar" style="background-image: url({{ Auth::user()->avatar }})"></span>
                                    <span class="ml-2 d-none d-lg-block">
                                        <span class="text-default">{{ Auth::user()->chinese_name }}</span>
                                        <small class="text-muted d-block mt-1">{{ Auth::user()->english_name }}</small>
                                    </span>
                                </a>
                                <div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow">
                                    <a class="dropdown-item" href="#">
                                        <i class="dropdown-icon fe fe-user"></i> 账号
                                    </a>
                                    <a class="dropdown-item" href="#">
                                        <i class="dropdown-icon fe fe-settings"></i> 设置
                                    </a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault();document.getElementById('logout-form').submit();">
                                        <i class="dropdown-icon fe fe-log-out"></i> 退出
                                    </a>
                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                        @csrf
                                    </form>
                                </div>
                            </div>
                            @endauth
                        </div>
                        <a href="#" class="header-toggler d-lg-none ml-3 ml-lg-0" data-toggle="collapse" data-target="#headerMenuCollapse">
                            <span class="header-toggler-icon"></span>
                        </a>
                    </div>
                </div>
            </div>
            @if ((Auth::user()->isAn('administrator')) || (Auth::user()->isAn('HR_manager')))
            <div class="header collapse d-lg-flex p-0" id="headerMenuCollapse">
                <div class="container">
                    <div class="row align-items-center">
                        <div class="col-lg-3 ml-auto">
                            <form class="input-icon my-3 my-lg-0" action="/search">
                                <input type="search" name="q" class="form-control header-search" placeholder="搜索&hellip;" tabindex="1">
                                <div class="input-icon-addon">
                                    <i class="fe fe-search"></i>
                                </div>
                            </form>
                        </div>
                        <div class="col-lg order-lg-first">
                            <ul class="nav nav-tabs border-0 flex-column flex-lg-row">
                                <li class="nav-item">
                                    <a href="/" class="nav-link active"><i class="fe fe-home"></i> 首页</a>
                                </li>
                                @if ((Auth::user()->isAn('administrator')) || (Auth::user()->isAn('HR_manager')))
                                <li class="nav-item">
                                    <a href="javascript:void(0)" class="nav-link" data-toggle="dropdown"><i class="fe fe-users"></i> 人事</a>
                                    <div class="dropdown-menu dropdown-menu-arrow">
                                        <a href="{{ url('/deptuser') }}" class="dropdown-item ">花名册</a>
                                        <a href="{{ url('/users') }}" class="dropdown-item ">员工管理</a>
                                        <a href="{{ url('/deptuser/depart') }}" class="dropdown-item ">部门管理</a>
                                        <a href="{{ url('/companies') }}" class="dropdown-item ">公司管理</a>
                                        <a href="{{ url('/pendingusers/index') }}" class="dropdown-item ">待入职管理</a>
                                        <a href="{{  url('/department')  }}" class="dropdown-item ">人力地图</a>
                                        <a href="{{  url('/stats/turnover')  }}" class="dropdown-item ">入离职统计</a>
                                    </div>
                                </li>
                                @endif
                                @if ((Auth::user()->isAn('administrator')))
                                <li class="nav-item">
                                    <a href="/developer/" class="nav-link"><i class="fe fe-code"></i> 研发</a>
                                </li>
                                @endif
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            @endif
            <div class="my-3 my-md-5">
                @yield('content')
            </div>
        </div>
    </div>
    <footer class="footer">
        <div class="container">
            <div class="row align-items-center flex-row-reverse">
                <div class="col-auto ml-lg-auto">
                    <ul class="list-inline list-inline-dots mb-0">
                        <li class="list-inline-item"><span class="btn btn-outline-light btn-sm">Env: {{ config('app.env', 'missing') }}</span></li>
                        <li class="list-inline-item"><a href="/developer">Documentation</a></li>
                    </ul>
                </div>
                <div class="col-12 col-lg-auto mt-3 mt-lg-0 text-center">
                    Copyright &copy; {{ date('Y') }} <a href="/">KuaiNiu Group</a>. All rights reserved.
                </div>
            </div>
        </div>
    </footer>
</body>
</html>