@extends("layouts.main",['title' => '关账考勤列表'])

@section('content')
    <header class="page-header">
        <div class="d-flex align-items-center">
            <div class="mr-auto">
                <h1 class="separator">关账考勤列表</h1>
                <nav class="breadcrumb-wrapper" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{url('/')}}"><i class="icon dripicons-home"></i></a></li>
                        <li class="breadcrumb-item"><a href="javascript:void(0)"></a>考勤管理</li>
                        <li class="breadcrumb-item active" aria-current="page">关账考勤列表</li>
                    </ol>
                </nav>
            </div>
        </div>
    </header>
    <section class="page-content container-fluid">
        <div class="card">
            <div class="table-responsive">
                <table class="table table-hover table-outline table-bordered table-vcenter text-nowrap card-table font-size-fix">
                    <thead>
                    <tr>
                        <th>日期</th>
                        <th>姓名</th>
                        <th>上班</th>
                        <th>下班</th>
                        <th>打卡记录</th>
                        <th>备注</th>
                    </tr>
                    </thead>
                    <tbody>
                    @if(count($sheet))
                        @foreach($sheet as $time)
                            <tr>
                                <td>
                                    <div class="clearfix">
                                        <div <?php if (in_array(date('w', strtotime($time->attendance_date)), [0,6])) {echo 'style="color:red;"';}?>><?php  echo date('Y-m-d', strtotime($time->attendance_date)); ?></div>
                                    </div>
                                </td>
                                <td>
                                    <div class="clearfix">
                                        {{ $chineseName  }}
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        <?php echo App\Models\Attendance\AttendanceSheet::getHour($time->attendance_begin_at);  ?>
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        <?php echo App\Models\Attendance\AttendanceSheet::getHour($time->attendance_end_at);  ?>
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        <span data-toggle="tooltip" title="{{ App\Models\Attendance\AttendanceSheet::getHour($time->attendance_time, false)  }}" data-placement="bottom">{{ App\Models\Attendance\AttendanceSheet::getHour($time->attendance_time)  }}</span>
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
