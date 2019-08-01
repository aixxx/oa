@extends("layouts.main",['title' => '考勤列表'])

@section('content')
    <header class="page-header">
        <div class="d-flex align-items-center">
            <div class="mr-auto">
                <h1 class="separator">考勤列表</h1>
                <nav class="breadcrumb-wrapper" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{url('/')}}"><i class="icon dripicons-home"></i></a></li>
                        <li class="breadcrumb-item"><a href="javascript:void(0)"></a>考勤管理</li>
                        <li class="breadcrumb-item active" aria-current="page">考勤列表</li>
                    </ol>
                </nav>
            </div>
        </div>
    </header>
    <section class="page-content container-fluid">
        <div class="card">
            <h3 class="card-header">
                <form action="{{ route('attendance.attendance.allusers') }}" method="get" id="search_form">
                    <div class="form-inline">
                        <div class="input-group col-md-3">
                            <div class="input-group date dp-years">
                                    <span class="input-group-addon action">
                                        <i class="icon dripicons-calendar"></i>
                                    </span>
                                <input type="text" class="form-control datepicker z-index-fix" id="month" placeholder=""
                                       name="month" readonly value="{{  $request['month'] }}">
                            </div>
                        </div>
                        <br>
                        <br>
                        <div class="input-group col-md-2">
                            <input type="text" class="form-control z-index-fix" placeholder="姓名" name="name"
                                   value="{{  $request['name'] }}">
                        </div>
                        <br>
                        <br>
                        <br>
                        <div class="input-group col-md-1">
                            <button type="submit" class="btn btn-primary btn-sm line-height-fix search-btn">搜索</button>
                        </div>
                        <br>
                        <br>
                        <br>
                        <div class="input-group col-md-2">
                            <button type="submit" class="btn btn-success btn-sm line-height-fix download-btn">导出</button>
                        </div>

                        <div class="input-group col-md-1">
                            <button class="btn btn-sm btn-danger refresh_attendance">刷新考勤</button>
                        </div>
                        <br>
                        <br>
                        <br>
                        <div class="input-group col-md-1">
                            <button class="btn btn-sm btn-danger refresh_workflow">刷新流程</button>
                        </div>
                    </div>
                </form>
            </h3>
            <div class="table-responsive">
                <table class="table table-hover table-outline table-bordered table-vcenter text-nowrap card-table font-size-fix">
                    <thead>
                    <tr>
                        <th>日期</th>
                        <th>姓名</th>
                        <th style="text-align: center">操作</th>
                        <th>上班</th>
                        <th>下班</th>
                        <th>打卡记录</th>
                        <th>备注</th>
                    </tr>
                    </thead>
                    <tbody>
                    @if(count($checkinout))
                        @foreach($checkinout as $time)
                            <tr>
                                <td>
                                    <div class="clearfix">
                                        <div <?php if (in_array(date('w', strtotime($time->attendance_date)), [0,6])) {echo 'style="color:red;"';}?>><?php  echo date('Y-m-d', strtotime($time->attendance_date)); ?></div>
                                    </div>
                                </td>
                                <td>
                                    <div class="clearfix">
                                        {{ $time->chinese_name  }}
                                    </div>
                                </td>
                                <td style="text-align: center">
                                    <div>
                                        <a href="javascript:void(0)" class="dropdown-item attendance_edit" data-attendance="{{ json_encode($time) }}" data-target="#editModal">补签</a>
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
            <div class="card-footer">
                {!! $checkinout->appends($request)->links() !!}
            </div>
        </div>
    </section>
    <link rel="stylesheet" href="/static/vendor/bootstrap-datepicker/bootstrap-datepicker.min.css">
    <link rel="stylesheet" href="/static/vendor/bootstrap-daterangepicker/daterangepicker.css">
    <link rel="stylesheet" href="/static/vendor/bootstrap-timepicker/bootstrap-datetimepicker.min.css">
    <!--编辑窗口-->
    <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="exampleModalLabel">考勤补签</h4>
                    <button type="button" class="close" style="color: #0b0603;" data-dismiss="modal" aria-label="Close">
                        &times;
                    </button>
                </div>
                <div class="modal-body">
                    <form action="" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="employee_num" class="control-label">员工编号</label>
                            <input type="text" class="form-control" id="employee_num" value="" readonly>
                        </div>
                        <div class="form-group">
                            <label for="chinese_namee" class="control-label">姓名</label>
                            <input type="text" class="form-control" id="chinese_name" value="" readonly>
                        </div>
                        <div class="form-group">
                            <label for="begin_at" class="control-label">上班时间<span style="color:red">*</span></label>
                            <div class="input-group">
                                <div class="input-group">
                                    <span class="input-group-addon action">
                                        <i class="icon dripicons-calendar"></i>
                                    </span>
                                    <input type="text" class="form-control datetimepicker z-index-fix" readonly placeholder="" id="begin_at">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="end_at" class="control-label">下班时间<span style="color:red">*</span></label>
                            <div class="input-group">
                                <div class="input-group">
                                    <span class="input-group-addon action">
                                        <i class="icon dripicons-calendar"></i>
                                    </span>
                                    <input type="text" class="form-control datetimepicker z-index-fix" readonly placeholder="" id="end_at">
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default btn-sm line-height-fix" data-dismiss="modal">取消</button>
                    <button type="button" class="btn btn-primary btn-sm line-height-fix" id="edit_save">保存</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="attendanceModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">提示</h4>
                    <button type="button" class="close" style="color: #0b0603;" data-dismiss="modal" aria-label="Close">&times;</button>
                </div>
                <div class="modal-body">
                    <p>需要执行几分钟，是否要刷新考勤么？</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                    <button type="button" class="btn btn-danger" id="refresh_attendance">确定</button>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div>

    <div class="modal fade" id="workflowModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">提示</h4>
                    <button type="button" class="close" style="color: #0b0603;" data-dismiss="modal" aria-label="Close">&times;</button>
                </div>
                <div class="modal-body">
                    <p>需要执行几分钟，是否要刷新流程么？</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                    <button type="button" class="btn btn-danger" id="refresh_workflow">确定</button>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div>
@endsection
@section("javascript")
    <!-- ================== DATEPICKER SCRIPTS ==================-->
    {{--<script src="/static/vendor/modernizr/modernizr.custom.js"></script>--}}
    {{--<script src="/static/vendor/jquery/dist/jquery.min.js"></script>--}}
    <script src="/static/vendor/moment/min/moment.min.js"></script>
    <script src="/static/vendor/bootstrap-datepicker/bootstrap-datepicker.min.js"></script>
    <script src="/static/vendor/bootstrap-daterangepicker/daterangepicker.js"></script>
    <script src="/static/vendor/bootstrap-timepicker/bootstrap-datetimepicker.min.js"></script>
    <script src="/static/vendor/sweetalert2/dist/sweetalert2.min.js"></script>
    <script src="/static/js/components/bootstrap-datepicker-init.js"></script>
    <script src="/static/js/components/bootstrap-date-range-picker-init.js"></script>
    <script>
        $(".refresh_attendance").click(function (e) {
            e.preventDefault();
            $("#attendanceModal").modal('show');
            $("#refresh_attendance").attr('data-href',"{{  route('attendance.attendance.refresh_attendance')  }}");

            $("#refresh_attendance").off('click').click(function (e) {
                e.preventDefault();
                callGetAjax($("#refresh_attendance"), {month: $("#month").val()}, function (response) {
                    if (response.code == 0) {
                        alert('刷新中请稍后');
                    }
                });
                $("#attendanceModal").modal('hide');
            });
        });

        $(".refresh_workflow").click(function (e) {
            e.preventDefault();
            $("#workflowModal").modal('show');
            $("#refresh_workflow").attr('data-href', "{{  route('attendance.attendance.refresh_workflow')  }}");

            $("#refresh_workflow").off('click').click(function (e) {
                e.preventDefault();
                callGetAjax($("#refresh_workflow"), {month: $("#month").val()}, function (response) {
                    if (response.code == 0) {
                        alert('刷新中请稍后');
                    }
                });
                $("#workflowModal").modal('hide');
            });
        });
    </script>
    <script>
        //导出
        $('.download-btn').click(function () {
            $("#search_form").attr("action", "{{ route('attendance.attendance.allusers_export') }}" ).submit();
        });
        $('.search-btn').click(function () {
            $("#search_form").attr("action", "{{ route('attendance.attendance.allusers') }}" ).submit();
        });

        store_error = "{{  session('storeError')  }}";
        if (store_error) {
            alert(store_error);
        }

        $('.datepicker').parent().datepicker({
            "weekStart": 1,
            "autoclose": true,
            "maxViewMode":'years', //最大视图层，为年视图层
            "minViewMode":'months', //最小视图层，为月视图层
            "format": "yyyy-mm",
            "language": "cn"
            // "startDate": "-3d"
        });

        $('.datetimepicker').datetimepicker({
            "format": "yyyy-mm-dd hh:ii:00",
            "language": "zh-CN",
            "autoclose": true,
            "sideBySide": true,
            "minView": "0"
        }).on('changeDate', function () {
            $(this).trigger('change');
        });

        //人工补签
        edit = $(".attendance_edit");
        edit.click(function () {
            attendance = $(this).attr('data-attendance');
            attendance = eval('(' + attendance + ')');

            if (attendance) {
                $("#employee_num").val(attendance.employee_num);
                $("#chinese_name").val(attendance.chinese_name);
                $("#begin_at").val(attendance.attendance_begin_at);
                $("#end_at").val(attendance.attendance_end_at);
                $("#editModal").modal('show');
                save = $("#edit_save");

                // return false;
                save.off('click').click(function () {
                    var f = arguments.callee, self = this;  //注：callee 属性是 arguments 对象的一个成员，他表示对函数对象本身的引用
                    $(self).unbind('click', f);
                    id = attendance.attendance_id;
                    begin = $("#begin_at").val();
                    end = $("#end_at").val();

                    if (!begin) {
                        alert("上班时间不能为空");
                        return;
                    }
                    if (!end) {
                        alert("下班时间不能为空");
                        return;
                    }


                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        type: "POST",
                        dataType: "json",
                        url: "{{  route('attendance.attendance.update')  }}",
                        data: {
                            'id'    : id,
                            'begin' : begin,
                            'end'   : end,
                            'operater' : '{{auth()->id()}}'
                        },
                        success: function (response) {
                            if (response.status == 'success') {
                                alert('补签成功');
                            } else {
                                alert(response.messages);
                                $(self).click(f);
                            }

                            $('#editModal').modal('hide');
                            location.reload();
                        }
                    })
                });
            }
        });
    </script>
@endsection
