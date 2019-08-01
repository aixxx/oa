@extends('layouts.main',['title' => '人力地图'])

@section('content')

    <header class="page-header">
        <div class="d-flex align-items-center">
            <div class="mr-auto">
                <h1 class="separator">人事地图</h1>
                <nav class="breadcrumb-wrapper" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.html"><i class="icon dripicons-home"></i></a></li>
                        <li class="breadcrumb-item"><a href="javascript:void(0)">员工管理</a></li>
                        <li class="breadcrumb-item active" aria-current="page">人事地图</li>
                    </ol>
                </nav>
            </div>
            <div class="actions top-right">
                <a href="{{  route('users.download')  }}" class="btn btn-primary  btn-sm line-height-fix">下载</a>
            </div>
        </div>
    </header>
    <section class="page-content container-fluid">
        <h3 class="card">
            <div class="card-header">
                <h5>查看历史数据</h5>
            </div>
            <div class="card-body font-size-fix">
                <form action="{{  route('departments.index')  }}" method="get">
                    <div class="form-inline">
                        <div class="form-group">
                            <div class="input-group col-md-7">
                                <div class="input-group date dp-years">
                                    <span class="input-group-addon action">
                                        <i class="icon dripicons-calendar"></i>
                                    </span>
                                    <input type="text" class="form-control datepicker z-index-fix" placeholder="选择日期"
                                           name="month"
                                           value={{ $month }}>
                                </div>
                            </div>
                        </div>
                        <div>
                            <button type="submit" class="btn btn-primary btn-sm line-height-fix">搜索</button>
                        </div>
                    </div>
                </form>
            </div>
        </h3>
        @foreach($departments as $department)
            @php
                $_SESSION['subtotal'] = [];
                $_SESSION['subcost'] = [];
            @endphp
            @if(in_array($department->auto_id,$allDepartAutoIds))
                <div class="card">
                    <div class="card-header">
                        <a class="has-arrow" href="javascript:;">
                            <i class="icon dripicons-blog"></i> {{ $department->name }}
                        </a>
                        @if(in_array($department->id,$childrenDeparts)){{--此部门需要在员工的子部门中，才展示部门所属员工--}}
                        @foreach(\App\Models\User::getByDepartIdAndDatetime($department->id, $chooseTime) as $user)
                            @if($isLeader[$user->id.'_'.$department->id])
                                @if($isPrimary[$user->id.'_'.$department->id] && $isCanLookUserInfo)
                                    {{ $user->chinese_name }}
                                @endif
                                @if($isPrimary[$user->id.'_'.$department->id] && !$isCanLookUserInfo)
                                    {{ $user->chinese_name }}
                                @endif
                                @if(!$isPrimary[$user->id.'_'.$department->id]){{--非主部门则按钮则显示白色按钮--}}
                                {{ $user->chinese_name }}
                                @endif
                            @endif
                        @endforeach
                        @endif
                    </div>
                    <div class="card-body font-size-fix">
                        <p>
                            @if(in_array($department->id,$childrenDeparts)){{--此部门需要在员工的子部门中，才展示部门所属员工--}}
                            @php
                                $report['cost']=$_SESSION['cost']??[];
                                $report['subcost']=$_SESSION['subcost']??[];
                                $report['subtotal']=$_SESSION['subtotal']??[];
                            @endphp
                            @foreach(\App\Models\User::getByDepartIdAndDatetime($department->id, $chooseTime) as $user)
                                @php
                                    if($isPrimary[$user->id.'_'.$department->id]){
                                        $report['cost'][$user->id]=1;
                                        $report['subcost'][$user->id]=1;
                                    }
                                    $report['subtotal'][$user->id]=1;
                                @endphp
                                @if($isPrimary[$user->id.'_'.$department->id] && $isCanLookUserInfo)
                                    <a href="/users/{{ $user->id }}"
                                       class="btn badge badge-pill badge-secondary btn-sm line-height-fix">{{ $user->chinese_name }}</a>
                                @endif
                                @if($isPrimary[$user->id.'_'.$department->id] && !$isCanLookUserInfo)
                                    <a href="javascript:void(0)"
                                       class="btn badge badge-pill badge-secondary btn-sm line-height-fix">{{ $user->chinese_name }}</a>
                                @endif
                                @if(!$isPrimary[$user->id.'_'.$department->id]){{--非主部门则按钮则显示白色按钮--}}
                                <a href="#{{ $user->id }}" class="btn btn-secondary btn-sm line-height-fix"
                                   class="tag">{{ $user->chinese_name }}</a>
                                @endif
                            @endforeach
                            @php
                                $_SESSION['cost']=$report['cost'];
                                $_SESSION['subcost']=$report['subcost'];
                                $_SESSION['subtotal']=$report['subtotal'];
                            @endphp
                            @endif
                        </p>
                        @if(count($isHaveChildren[$department->id]))
                            @include('department.child',
                            [
                            'children' => $isHaveChildren[$department->id],
                            'allDepartAutoIds'=>$allDepartAutoIds,
                            'childrenDeparts'=>$childrenDeparts,
                            'isCanLookUserInfo'=>$isCanLookUserInfo,
                            'chooseTime'=>$chooseTime,
                            'isLeader'=>$isLeader,
                            'isPrimary'=>$isPrimary
                            ])
                        @endif
                    </div>
                    <div class="card-footer">
                        <small>合计 {{ count($_SESSION['subtotal']) }} 人，主部门 {{ count($_SESSION['subcost']) }} 人</small>
                    </div>
                </div>
            @endif
        @endforeach

        <div class="alert alert-success alert-outline">
            总计在职人数 {{ count($_SESSION['cost']) }} 人
        </div>
        <p>以上不含社保挂靠、顾问、兼职7人</p>
    </section>
@endsection
@section('javascript')
    <!-- ================== DATEPICKER SCRIPTS ==================-->
    <script src="/static/vendor/moment/min/moment.min.js"></script>
    <script src="/static/vendor/bootstrap-datepicker/bootstrap-datepicker.min.js"></script>
    <script src="/static/vendor/bootstrap-daterangepicker/daterangepicker.js"></script>
    <script src="/static/vendor/sweetalert2/dist/sweetalert2.min.js"></script>
    <script src="/static/js/components/bootstrap-datepicker-init.js"></script>
    <script src="/static/js/components/bootstrap-date-range-picker-init.js"></script>
    <script>
        $('.datepicker').parent().datepicker({
            "weekStart": 1,
            "autoclose": true,
            "maxViewMode": 'years', //最大视图层，为年视图层
            "minViewMode": 'months', //最小视图层，为月视图层
            "format": "yyyy-mm",
            "language": "zh-CN"
            // "startDate": "-3d"
        });
    </script>
@endsection
