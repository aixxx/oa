@extends("layouts.main",['title' => '考勤关账记录'])
@section('content')
    <header class="page-header">
        <div class="d-flex align-items-center">
            <div class="mr-auto">
                <h1 class="separator">考勤关账记录</h1>
                <nav class="breadcrumb-wrapper" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{url('/')}}"><i class="icon dripicons-home"></i></a></li>
                        <li class="breadcrumb-item"><a href="javascript:void(0)"></a>考勤管理</li>
                        <li class="breadcrumb-item active" aria-current="page">考勤关账记录</li>
                    </ol>
                </nav>
            </div>
            {{--<div class="actions top-right">--}}
            {{--<a href="{{  route('salary.export',['month' =>$request['month']])  }}" class="btn btn-primary  btn-sm line-height-fix">报表下载</a>--}}
            {{--</div>--}}
        </div>
    </header>
    <section class="page-content container-fluid">
        <div class="card" id="card">
            <h3 class="card-header">
                <form action="" method="get" id="search_form">
                    <div class="form-inline">
                        <div class="input-group col-md-4">
                            <div class="input-group date dp-years">
                                    <span class="input-group-addon action">
                                        <i class="icon dripicons-calendar"></i>
                                    </span>
                                <input type="text" class="form-control datepicker z-index-fix" placeholder="月份"
                                       name="month" value="{{ $request['month'] }}">
                            </div>
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
                        <th>考勤关账名称</th>
                        <th>关账月份</th>
                        <th>关账时间</th>
                        <th>关账人</th>
                        <th>是否最终关账</th>
                        <th style="text-align: center">操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    @if(count($records))
                        @foreach($records as $record)
                            <tr>
                                <td>
                                    {{ $record->title }}
                                </td>
                                <td>
                                    {{ date('Y-m', strtotime($record->date)) }}
                                </td>
                                <td>
                                    {{ $record->created_at }}
                                </td>
                                <td>
                                    {{ $record->origin_auth_name }}
                                </td>
                                <td>
                                    {{ \App\Models\Attendance\PayrollMonthlyRecord::$closingStatusMap[$record->status] }}
                                </td>
                                <td style="text-align: center">
                                    <a href="{{  route('attendance.closing.detail',['id'=> $record->id]) }}" target="_blank" class="dropdown-item">查看明细</a>
                                </td>
                            </tr>
                        @endforeach
                    @endif
                    </tbody>
                </table>
            </div>
            <div class="card-footer">
                {{ $records->appends($request)->links() }}
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
        });
    </script>

    <script type="text/javascript">
        //导出
        $('.search-btn').click(function () {
            $("#search_form").attr("action", "{{ route('attendance.closing.record') }}" ).submit();
        });

        store_error = "{{  session('storeError')  }}";
        if (store_error) {
            alert(store_error);
        }
    </script>
@endsection