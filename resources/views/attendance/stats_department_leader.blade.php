@extends("layouts.main",['title' => '部门统计'])
@section('content')
    <style>
        .table .thead-light th {
            border: 3px solid #f8f9fa !important;
        }
    </style>
<header class="page-header">
        <div class="d-flex align-items-center">
            <div class="mr-auto">
                <h1 class="separator">部门统计</h1>
                <nav class="breadcrumb-wrapper" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{url('/')}}"><i class="icon dripicons-home"></i></a></li>
                        <li class="breadcrumb-item"><a href="javascript:void(0)"></a>考勤管理</li>
                        <li class="breadcrumb-item active" aria-current="page">考勤统计</li>
                    </ol>
                </nav>
            </div>
            {{--<div class="actions top-right">--}}
                {{--<a href="{{  route('attendance.department.export',['month' =>$request['month']])  }}" class="btn btn-primary  btn-sm line-height-fix">部门统计下载</a>--}}
            {{--</div>--}}
        </div>
    </header>
    <section class="page-content container-fluid">
        <div class="card" id="card">
            <h3 class="card-header">
                <form action="{{  route('attendance.attendance.department_leader')  }}" method="get">
                    <div class="form-inline">
                        <div class="input-group date dp-years col-md-2">
                            <span class="input-group-addon action"><i class="icon dripicons-calendar"></i></span>
                            <input type="text" class="form-control datepicker z-index-fix" placeholder="月份" name="month"
                                   value="{{ $request['month'] }}" readonly>
                        </div>
                        <br>
                        <br>
                        <div class="input-group col-md-3">
                            <label>选择部门</label>
                            <select name="depart" class="form-control">
                                <option value="0">全部</option>
                                @foreach($departments as $key=>$comment)
                                    <option value="{{ $key }}" <?php if (isset($request['depart']) && $request['depart'] == $key) {
                                        echo 'selected';
                                    }?>>{{$comment}}</option>
                                @endforeach
                            </select>
                        </div>
                        <br>
                        <br>
                        <div class="form-group col-md-4">
                            <button type="submit" class="btn btn-primary btn-sm line-height-fix">搜索</button>
                        </div>
                    </div>
                </form>
            </h3>
            <div class="table-responsive">
                <table class="table table-hover table-bordered table-outline table-vcenter text-nowrap card-table">
                    <thead class="thead-light">
                    <tr>
                        <th style="border-color: #0b7ec4"></th>
                        <th colspan="3" class="text-center" style="border-color: #0b7ec4">平均工作时长统计</th>
                        <th colspan="5" class="text-center" style="border-color: #0b7ec4">打卡记录统计</th>
                        <th colspan="2" class="text-center" style="border-color: #0b7ec4">日工作时长统计</th>
                        <th colspan="2" class="text-center" style="border-color: #0b7ec4">加班请假时长统计</th>
                        <th style="border-color: #0b7ec4"></th>
                    </tr>
                    <tr  class="text-center">
                        <th>姓名</th>
                        <th>月总</th>
                        <th>周均</th>
                        <th>日均</th>
                        <th>10:00 前</th>
                        <th>10:30 后</th>
                        <th>19:00 前</th>
                        <th>19:30 后</th>
                        <th>21:00 后</th>
                        <th>> 5H</th>
                        <th>>12 H</th>
                        <th>请假时长</th>
                        <th>加班时长</th>
                        <th>操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    @if(count($statistics))
                        @foreach($statistics as $user => $stats)
                            <tr>
                                <td class="text-center">{{ $user }}</td>
                                <td class="text-center">{{ number_format($stats['length_total'], 1)  }}</td>
                                <td class="text-center">{{ number_format($stats['week_avg'], 1)  }}</td>
                                <td class="text-center">{{ number_format($stats['day_avg'], 1)  }}</td>
                                <td class="text-center">{{ $stats['before_1000']  }}</td>
                                <td class="text-center">{{ $stats['after_1030']  }}</td>
                                <td class="text-center">{{ $stats['before_1900']  }}</td>
                                <td class="text-center">{{ $stats['after_1930']  }}</td>
                                <td class="text-center">{{ $stats['after_2100']  }}</td>
                                <td class="text-center">{{ $stats['longer_than_5h']  }}</td>
                                <td class="text-center">{{ $stats['longer_than_12h']  }}</td>
                                <td class="text-center">{{ $stats['leave']  }}</td>
                                <td class="text-center">{{ $stats['overtime']  }}</td>
                                <td class="text-center"><a target="_blank" href="{{ route('attendance.attendance.leader_show', ['month' => $request['month'], 'user_id' => $stats['user_id']]) }}">详情明细</a></td>
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
        });
    </script>
@endsection
