@extends("layouts.main",['title' => '考勤关账'])
@section('content')
    <header class="page-header">
        <div class="d-flex align-items-center">
            <div class="mr-auto">
                <h1 class="separator">考勤关账</h1>
                <nav class="breadcrumb-wrapper" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{url('/')}}"><i class="icon dripicons-home"></i></a></li>
                        <li class="breadcrumb-item"><a href="javascript:void(0)"></a>考勤管理</li>
                        <li class="breadcrumb-item active" aria-current="page">考勤关账</li>
                    </ol>
                </nav>
            </div>
        </div>
    </header>
    <section class="page-content container-fluid">
        <div class="card" id="card">
            <div class="card-header col-12">
                <form action="" method="get" id="search_form">
                    <div class="form-group row text-right">
                        <div class="col-4">
                            <div class="input-group date dp-years">
                                    <span class="input-group-addon action">
                                        <i class="icon dripicons-calendar"></i>
                                    </span>
                                <input type="text" class="form-control datepicker z-index-fix" placeholder="选择月份"
                                       name="month" value="{{ $request['month'] }}">
                            </div>
                        </div>

                        <div class="col-4 text-left z-index-fix" style="margin-top: 0.8em;">
                            @if($dueDays)
                                当月应出勤 {{ $dueDays }} 天
                            @endif
                        </div>
                        <div class="col-4 text-right">
                            <button type="button" class="btn btn-success btn-sm line-height-fix button_overflow reload">重新载入考勤</button>
                            @if (date('Y-m', strtotime($request['month']))  >= date("Y-m", strtotime("-1month")) )
                            <button type="button" class="btn btn-warning btn-sm line-height-fix closing">考勤关账</button>
                            @endif
                        </div>
                    </div>
                    <div class="form-group row text-right">
                        <div class="col-4">
                            <select name="level_first" class="form-control">
                                <option value="">请选择一级部门</option>
                                @foreach($firstDepartments as $key=>$comment)
                                    <option value="{{ $key }}" <?php if (isset($request['level_first']) && $request['level_first'] == $key) {
                                        echo 'selected';
                                    }?>>{{$comment}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-4 z-index-fix">
                            <input type="text" class="form-control z-index-fix" placeholder="姓名、员工工号" name="name"
                                   value="{{  $request['name'] }}">
                        </div>

                        <div class="col-4 text-right">
                            <button type="submit" class="btn btn-primary btn-sm line-height-fix search-btn">查询</button>
                            <button type="button" class="btn btn-primary btn-sm line-height-fix download-btn">导出</button>
                            <button type="button" class="btn btn-primary btn-sm line-height-fix import">导入修改</button>
                        </div>

                    </div>
                </form>
            </div>

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
                    @if(count($monthly))
                        @foreach($monthly as $user => $stats)
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
                                    <a href="{{  route('attendance.attendance.allusers',['name' => $stats->chinese_name, 'month'=> date('Y-m', strtotime($stats->date))]) }}" target="_blank" class="dropdown-item">查看明细</a>
                                </td>
                            </tr>
                        @endforeach
                    @endif
                    </tbody>
                </table>
            </div>
            <div class="card-footer">
                {{ $monthly->appends($request)->links() }}
            </div>
        </div>
    </section>

    <!--导入窗口-->
    <div class="modal fade" id="importModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="exampleModalLabel">导入考勤</h4>
                    <button type="button" class="close" style="color: #0b0603;" data-dismiss="modal" aria-label="Close">
                        &times;
                    </button>
                </div>
                <div class="modal-body">
                    <form class="form-horizontal form" data-validate="parsley" action="{{  route('attendance.closing.import')  }}"
                          method="post" enctype="multipart/form-data" id="import_form">
                        @csrf
                        <div class="panel-body">
                            <div class="form-group clearfix">
                                <div class="col-sm-12" style="padding-top: 20px;">
                                    <label for="uid_file" class="col-xs-1 control-label">
                                        <input type="hidden" name="import_month">&nbsp;</label>
                                        <input type="radio" id="upload_type" name="upload_type" value="uid" checked>&nbsp;上传考勤(重复导入则覆盖)</label>
                                    <input type="file" id="order_process_file" name="order_process_file" class="filestyle"
                                           data-classbutton="btn btn-default"
                                           data-classinput="form-control inline v-middle input-s">
                                    <button id="submit" class="btn btn-success btn-s-xs">上传</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!--编辑窗口-->
    <div class="modal fade" id="closeModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="exampleModalLabel">考勤关账</h4>
                    <button type="button" class="close" style="color: #0b0603;" data-dismiss="modal" aria-label="Close">
                        &times;
                    </button>
                </div>
                <div class="modal-body">
                    <form action="" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="month" class="control-label">关账月份</label>
                            <input type="text" class="form-control" id="closing_month"  value="" readonly>
                        </div>
                        <div class="form-group">
                            <label for="title" class="control-label">关账名称*</label>
                            <input type="text" class="form-control"  name = "title" value="" placeholder="最多20字">
                        </div>
                        {{--<h6 style="margin-top: 2.2em;">当月第一次考勤关账后,将会推送考勤数据至员工微信</h6>--}}

                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default btn-sm line-height-fix" data-dismiss="modal">取消</button>
                    <a href="javascript:void(0);" class="btn btn-primary btn-sm line-height-fix" id="closing">保存</a>
                </div>
            </div>
        </div>
    </div>
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
        var success = "{{  session('success')  }}";
        if(success)
        {
            alert(success);
            delete success;
        }

        var error = "{{  session('error')  }}";
        if(error)
        {
            alert(error);
            delete error;
        }

        //导出
        $('.download-btn').click(function () {
            $("#search_form").attr("action", "{{ route('attendance.closing.export') }}" ).submit();
        });
        $('.search-btn').click(function () {
            $("#search_form").attr("action", "{{ route('attendance.closing.index') }}" ).submit();
        });

        //重新载入
        $(".reload").click(function (e) {
            e.preventDefault();
            $(".reload").attr('data-href', "{{  route('attendance.closing.reload')  }}");

            callGetAjax($(".reload"), {month: $("input[name='month']").val()}, function (response) {
                alert(response.message);
                location.reload();
            });
        });

        //修改后导入
        $(".import").click(function (e) {
            e.preventDefault();
            $(".import").attr('data-href', "{{  route('attendance.closing.import')  }}");

            $("input[name='import_month']").val($("input[name='month']").val());
            $("#importModal").modal('show');
        });


        $("#submit").one('click', function (e) {
            var err = '<?php echo isset($err) ? $err:'' ?>';
            if(err){
                alert(err);
            }
            var form = $("#import_form");
            // 表单提交拦截
            form.submit(function(e){
                if ($('#order_process_file').val() === '') {
                    alert("请选择要上传文件");
                    return false;
                }

            });
        });


        Date.prototype.Format = function (fmt) {
            var o = {
                "M+": this.getMonth() + 1, //月份
                "d+": this.getDate(), //日
                "H+": this.getHours(), //小时
                "m+": this.getMinutes(), //分
                "s+": this.getSeconds(), //秒
                "q+": Math.floor((this.getMonth() + 3) / 3), //季度
                "S": this.getMilliseconds() //毫秒
            };
            if (/(y+)/.test(fmt)) fmt = fmt.replace(RegExp.$1, (this.getFullYear() + "").substr(4 - RegExp.$1.length));
            for (var k in o)
                if (new RegExp("(" + k + ")").test(fmt)) fmt = fmt.replace(RegExp.$1, (RegExp.$1.length == 1) ? (o[k]) : (("00" + o[k]).substr(("" + o[k]).length)));
            return fmt;
        };

        //关账
        $(".closing").click(function (e) {
            e.preventDefault();
            $(".closing").attr('data-href', "{{  route('attendance.closing.close')  }}");

            var selected_month = $("input[name='month']").val();
            var format_month = new Date(selected_month).Format("yyyy年MM月");
            $("#closing_month").val(format_month);
            $("#closeModal").modal('show');

            $("#closing").one('click',function (e) {
                e.preventDefault();
                var title = $("input[name='title']").val();

                if (title.length < 1) {
                    alert('请正确填写关账名称');
                    return false;
                }

                callPostAjax($(".closing"), {month: selected_month, title:title}, function (response) {
                    alert(response.message);
                    $("#closeModal").modal('hide');
                });
            });
        });
    </script>
@endsection