@extends('layouts.main',['title' => '首页'])

@section('content')
    <header class="page-header">
        <div class="d-flex align-items-center">
            <div class="mr-auto">
                <h1>首页</h1>
            </div>
            <ul class="actions top-right">
                <li class="dropdown">
                    <a href="javascript:void(0)" class="btn btn-fab" data-toggle="dropdown" aria-expanded="false">
                        <i class="la la-ellipsis-h"></i>
                    </a>
                    <div class="dropdown-menu dropdown-icon-menu dropdown-menu-right">
                        <div class="dropdown-header">
                            首页设置
                        </div>
                        <a href="#" class="dropdown-item">
                            <i class="icon dripicons-plus"></i> 添加报表
                        </a>
                        <a href="#" class="dropdown-item">
                            <i class="icon dripicons-cloud-download"></i> 显示设置
                        </a>
                        {{--<a href="{{ url('/attendance/checktime') }}" class="dropdown-item">--}}
                            {{--<i class="icon dripicons-plus"></i> 我的考勤--}}
                        {{--</a>--}}
                        {{--<a href="{{ url('/attendance/alltime') }}" class="dropdown-item">--}}
                            {{--<i class="icon dripicons-plus"></i> 考勤管理--}}
                        {{--</a>--}}
                    </div>
                </li>
            </ul>
        </div>
    </header>
    <!--END PAGE HEADER -->
    <!--START PAGE CONTENT -->
    <section class="page-content container-fluid">
        <div class="row">
            <div class="col-xl-5 col-lg-4">
                <div class="card">
                    <div class="card-body">
                        <div class="profile-card text-center">
                            <div class="thumb-xl member-thumb m-b-10 center-block">
                                <img src="{{ Auth::user()->avatar }}" width="200" class="rounded-circle img-thumbnail" alt="profile-image">
                            </div>
                            <div class="">
                                <h5 class="m-b-5">{{ Auth::user()->chinese_name }}</h5>
                                <p class="text-muted"><span>@</span>{{ Auth::user()->english_name }}</p>
                            </div>
                            <ul class="social-links list-inline m-t-30">
                                <li class="list-inline-item">
                                    <a title="" data-placement="top" data-toggle="tooltip" class="tooltips" href="" data-original-title="Facebook"><i class="zmdi zmdi-facebook"></i></a>
                                </li>
                                <li class="list-inline-item">
                                    <a title="" data-placement="top" data-toggle="tooltip" class="tooltips" href="" data-original-title="Twitter"><i class="zmdi zmdi-twitter"></i></a>
                                </li>
                                <li class="list-inline-item">
                                    <a title="" data-placement="top" data-toggle="tooltip" class="tooltips" href="" data-original-title="GitHub"><i class="zmdi zmdi-github-alt"></i></a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <hr />
                    <ul class="nav sub-nav v-sub-nav flex-column p-l-30 p-b-30">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('cert.download') }}"><i class="icon dripicons-download font-size-22 v-align-middle p-r-15 p-t-5"></i>下载证书</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </section>
@endsection
