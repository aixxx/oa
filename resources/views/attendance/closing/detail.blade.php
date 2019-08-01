@extends("layouts.main",['title' => '考勤关账明细'])
@section('content')
    <header class="page-header">
        <div class="d-flex align-items-center">
            <div class="mr-auto">
                <h1 class="separator">考勤关账明细</h1>
                <nav class="breadcrumb-wrapper" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{url('/')}}"><i class="icon dripicons-home"></i></a></li>
                        <li class="breadcrumb-item"><a href="javascript:void(0)"></a>考勤管理</li>
                        <li class="breadcrumb-item active" aria-current="page">{{ date('Y年m月', strtotime($record->date)) . '考勤关账' }}</li>
                    </ol>
                </nav>
            </div>

        </div>
    </header>
    <section class="page-content container-fluid">
        <div class="card" id="card">
            <divz class="card-header">
                <form action="" method="get" id="search_form">
                    @csrf
                    <div class="form-group row text-right">
                        <div class="col-4">
                            <div class="input-group date dp-years">
                                    <span class="input-group-addon action">
                                        <i class="icon dripicons-calendar"></i>
                                    </span>
                                <input type="text" class="form-control datepicker z-index-fix" placeholder="选择月份"
                                       name="month" value="{{ date('Y-m', strtotime($record->date)) }}" disabled>
                            </div>
                        </div>



                        <div class="col-8 text-left z-index-fix"  style="margin-top: 0.8em;">
                            <div class="input-group">
                                <input type="hidden" class="form-control z-index-fix" name="id" value="{{ $id }}">
                            </div>
                            考勤关账时间：{{ $record->created_at}}
                            @if ($record->id == $markRecord->id)
                                （{{ date('Y年m月份', strtotime($record->date)) }}考勤最终结果）
                            @endif
                        </div>
                    </div>
                    <div class="form-group row text-right">
                        <div class="col-3">
                            <select name="level_first" class="form-control">
                                <option value="">请选择一级部门</option>
                                @foreach($firstDepartments as $key=>$comment)
                                    <option value="{{ $key }}" <?php if (isset($request['level_first']) && $request['level_first'] == $key) {
                                        echo 'selected';
                                    }?>>{{$comment}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-3 z-index-fix">
                            <input type="text" class="form-control z-index-fix" placeholder="姓名、员工工号" name="name"
                                   value="{{  $request['name'] }}">
                        </div>

                        <div class="col-4 text-right">
                            <button type="submit" class="btn btn-primary btn-sm line-height-fix search-btn">查询</button>
                            <button type="button" class="btn btn-primary btn-sm line-height-fix download-btn">导出</button>
                            <a class="btn btn-sm btn-primary" href="{{  route('attendance.closing.record')  }}">返回</a>
                        </div>

                    </div>
                </form>
            </divz>
            <div class="table-responsive">
                <table class="table table-hover table-outline table-vcenter text-nowrap card-table">
                    <thead>
                    <tr>
                        <th>工号</th>
                        <th>姓名</th>
                        <th>一级部门</th>
                        <th>二级部门</th>
                        <th>当月实际出勤(天)</th>
                        <th>节假日加班(小时)</th>
                        <th>事假(小时)</th>
                        <th>病假(小时)</th>
                        <th>旷工(小时)</th>
                        <th>加班转调休(小时)</th>
                        <th>其他带薪假合计(小时)</th>
                        <th style="text-align: center">操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    @if(count($closings))
                        @foreach($closings as $stats)
                            <tr>
                                <td>
                                    {{ \App\Models\User::getEmployeeNum($stats->employee_num) }}
                                </td>
                                <td>
                                    {{ $stats->chinese_name }}
                                </td>
                                <td>
                                    {{ $stats->department_level_first }}
                                </td>
                                <td>
                                    {{ $stats->department_level_second }}
                                </td>
                                <td>
                                    {{ $stats->actual_days }}
                                </td>
                                <td>
                                    {{ $stats->three_times }}
                                </td>
                                <td>
                                    {{ $stats->casual }}
                                </td>
                                <td>
                                    {{ $stats->sick }}
                                </td>
                                <td>
                                    {{ $stats->absent }}
                                </td>
                                <td>
                                    {{ $stats->one_and_half_times + $stats->two_times }}
                                </td>
                                <td>
                                    {{ $stats->annual + $stats->extra_day_off + $stats->extra + $stats->full_pay_sick + $stats->spring_festival + $stats->marriage + $stats->funeral + $stats->paternity +$stats->company + $stats->check_up + $stats->maternity}}
                                </td>
                                <td>
                                    <div>
                                        <span data-toggle="tooltip" title="截止到关账时刻的考勤记录" data-placement="bottom">
                                            <a href="{{  route('attendance.closing.daily',['user_id'=> $stats->user_id, 'record_id' => $stats->record_id]) }}" target="_blank" class="dropdown-item">查看明细</a>
                                        </span>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    @endif
                    </tbody>
                </table>
            </div>
            <div class="card-footer">
                {{ $closings->links() }}
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
        $('.download-btn').click(function () {
            $("#search_form").attr("action", "{{ route('attendance.closing.detail_export', ['id' => $id ]) }}" ).submit();
        });
        $('.search-btn').click(function () {
            $("#search_form").attr("action", "{{ route('attendance.closing.detail', ['id' => $id ]) }}" ).submit();
        });

        store_error = "{{  session('storeError')  }}";
        if (store_error) {
            alert(store_error);
        }
    </script>
@endsection