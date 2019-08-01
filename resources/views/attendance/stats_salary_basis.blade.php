@extends("layouts.main",['title' => '考勤统计'])
@section('content')
<header class="page-header">
        <div class="d-flex align-items-center">
            <div class="mr-auto">
                <h1 class="separator">考勤统计</h1>
                <nav class="breadcrumb-wrapper" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{url('/')}}"><i class="icon dripicons-home"></i></a></li>
                        <li class="breadcrumb-item"><a href="javascript:void(0)"></a>考勤管理</li>
                        <li class="breadcrumb-item active" aria-current="page">考勤统计</li>
                    </ol>
                </nav>
            </div>
            <div class="actions top-right">
                <a href="{{  route('attendance.salary.export',['month' =>$request['month']])  }}" class="btn btn-primary  btn-sm line-height-fix">导出</a>
            </div>
        </div>
    </header>
    <section class="page-content container-fluid">
        <div class="card" id="card">
            <h3 class="card-header">
                <form action="{{  route('attendance.attendance.salary_basis')  }}" method="get">
                    <div class="form-inline">
                        <div class="input-group col-md-4">
                            <div class="input-group date dp-years">
                                    <span class="input-group-addon action">
                                        <i class="icon dripicons-calendar"></i>
                                    </span>
                                    <input type="text" class="form-control datepicker z-index-fix" placeholder="月份"
                                           name="month" value="{{ $request['month'] }}" readonly>
                            </div>
                        </div>
                        <br>
                        <br>
                        <div class="input-group col-md-2">
                            <input type="text" class="form-control z-index-fix" placeholder="姓名" name="name" value="{{  $request['name'] }}">
                        </div>
                        <br>
                        <br>
                        <br>
                        <div class="input-group col-md-2">
                            <button type="submit" class="btn btn-primary btn-sm line-height-fix search-btn">查询</button>
                        </div>
                    </div>

                </form>
            </h3>
            <div class="table-responsive">
                <table class="table table-hover table-outline table-vcenter text-nowrap card-table">
                    <thead>
                    <tr>
                        <th>工号</th>
                        <th>姓名</th>
                        <th>一级部门</th>
                        <th>二级部门</th>
                        <th>当月实际出勤</th>
                        <th>节假日加班</th>
                        <th>事假(小时)</th>
                        <th>病假(小时)</th>
                        <th>旷工(小时)</th>
                        <th>加班转调休(小时)</th>
                        <th>其他带薪假合计(小时)</th>
                    </tr>
                    </thead>
                    <tbody>
                    @if(count($statistics))
                        @foreach($statistics as $user => $stats)
                            <tr>
                                <td>
                                    {{ $stats['employee_num'] }}
                                </td>
                                <td>
                                    {{ $stats['chinese_name'] }}
                                </td>
                                <td>
                                    {{ $stats['first_depart'] }}
                                </td>
                                <td>
                                    {{ $stats['second_depart'] }}
                                </td>
                                <td>
                                    {{ $stats['stats']['working_days'] }}
                                </td>

                                <td>
                                    {{ $stats['stats']['overtime_statutory'] }}
                                </td>
                                <td>
                                    {{ $stats['stats']['casual'] }}
                                </td>
                                <td>
                                    {{ $stats['stats']['sick'] }}
                                </td>
                                <td>
                                    {{ $stats['stats']['absent'] }}
                                </td>
                                <td>
                                    {{ $stats['stats']['overtime_working'] +  $stats['stats']['overtime_workend']}}
                                </td>
                                <td>
                                    {{ $stats['stats']['all_other'] }}
                                </td>
                            </tr>
                        @endforeach
                    @endif
                    </tbody>
                </table>
            </div>
            <div class="card-footer">
                {!! $users->appends($request)->links() !!}
            </div>
        </div>
    </section>
@endsection
@section("javascript")
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
            "maxViewMode":'years', //最大视图层，为年视图层
            "minViewMode":'months', //最小视图层，为月视图层
            "format": "yyyy-mm",
            "language": "cn"
            // "startDate": "-3d"
        });
    </script>
@endsection
