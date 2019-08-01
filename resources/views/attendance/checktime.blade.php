@extends("layouts.main",['title' => '考勤'])
@section('content')
    <style>
        table th, td{
            text-align: center;
        }
    </style>
    <header class="page-header">
        <div class="d-flex align-items-center">
            <div class="mr-auto">
                <h1 class="separator">{{ isset($user) ? $user->chinese_name : '我' }}的考勤</h1>
                <nav class="breadcrumb-wrapper" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{url('/')}}"><i class="icon dripicons-home"></i></a></li>
                        <li class="breadcrumb-item"><a href="javascript:void(0)"></a>考勤管理</li>
                        <li class="breadcrumb-item active" aria-current="page">{{ isset($user) ? $user->chinese_name : '我' }}的考勤</li>
                    </ol>
                </nav>
            </div>
        </div>
    </header>
    <section class="page-content container-fluid">
        <div class="card">
            <h3 class="card-header">
                <form action="{{  route('attendance.attendance.checktime')  }}" method="get">
                    <div class="form-inline">
                        <div class="input-group date dp-years col-md-2">
                            <span class="input-group-addon action"><i class="icon dripicons-calendar"></i></span>
                            <input type="text" class="form-control datepicker z-index-fix" placeholder="" name="month" value="{{  $request['month'] }}"  readonly  @php if(isset($user)) { echo 'disabled';} @endphp>
                        </div>
                        <br>
                        <br>
                        <br>
                        <div class="input-group col-md-4">
                            @if(!isset($user))
                                <button type="submit" class="btn btn-primary btn-sm line-height-fix">搜索</button>
                            @endif
                        </div>
                    </div>
                </form>
            </h3>
            <div class="table-responsive">
                <table class="table table-hover table-outline table-bordered table-vcenter text-nowrap card-table font-size-fix">
                    <thead>
                    <tr style="border-radius: 1px">
                        <th>日期</th>
                        @if(!isset($user))
                            <th>操作</th>
                        @endif
                        <th>上班</th>
                        <th>下班</th>
                        <th>打卡记录</th>
                        <th>是否异常</th>
                        <th>异常说明</th>
                    </tr>
                    </thead>
                    <tbody>
                    @if(count($checkinout))
                        @foreach($checkinout as $time)
                            <tr style="border-radius: 20%">
                                <td>
                                    <div class="clearfix">
                                        <div <?php if (in_array(date('w', strtotime($time->attendance_date)), [0,6])) {echo 'style="color:red;"';}?>><?php  echo date('Y-m-d', strtotime($time->attendance_date)); ?></div>
                                    </div>
                                </td>
                                @if(!isset($user))
                                    <td>
                                        @if ($flow)
                                            <a href="{{  route('workflow.entry.create',['flow_id'=> ($flow->id), 'date' => date('md', strtotime($time->attendance_date))])  }}" target="_blank" class="dropdown-item">补签</a>
                                        @endif
                                    </td>
                                @endif
                                <td>
                                    <div>
                                        <?php echo ($time->attendance_begin_at && strtotime($time->attendance_begin_at) > 0) ? App\Models\Attendance\AttendanceSheet::getHour($time->attendance_begin_at) : '';  ?>
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        <?php echo ($time->attendance_end_at && strtotime($time->attendance_end_at) > 0) ? App\Models\Attendance\AttendanceSheet::getHour($time->attendance_end_at) : '';  ?>
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        <span data-toggle="tooltip" title="{{ App\Models\Attendance\AttendanceSheet::getHour($time->attendance_time, false)  }}" data-placement="bottom">{{ App\Models\Attendance\AttendanceSheet::getHour($time->attendance_time)  }}</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="clearfix">
                                        @if(strtotime($time->attendance_date) >= strtotime(\Illuminate\Support\Facades\Auth::user()->join_at)
                                        && $time->attendance_is_abnormal == \App\Models\Attendance\AttendanceSheet::STATUS_ABNORMAL
                                        && !in_array($time->attendance_user_id, $whiteUserId)
                                        && !$time->attendance_holiday_type_sub
                                        && !$time->attendance_holiday_type_sub_second
                                        && !$time->attendance_travel_interval)
                                            @if(strtotime($time->attendance_begin_at) <= 0 && strtotime($time->attendance_end_at) <= 0)
                                                <div style="color:red;">无打卡记录</div>
                                            @elseif(strtotime($time->attendance_begin_at) <= 0)
                                                <div style="color:red;">无上班时间</div>
                                             @else
                                                <div style="color:red;">无下班时间</div>
                                            @endif
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div class="clearfix">
                                        {{ $time->attendance_abnormal_note }}
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    @endif
                    </tbody>
                </table>
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
