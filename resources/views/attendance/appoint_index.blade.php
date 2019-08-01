@extends('layouts.main',['title' => '班值管理'])
<!-- ======================= PAGE LEVEL VENDOR STYLES ========================-->
<link rel="stylesheet" href="/static/vendor/bootstrap-datepicker/bootstrap-datepicker.min.css">
<link rel="stylesheet" href="/static/vendor/bootstrap-daterangepicker/daterangepicker.css">
@section('content')

    <header class="page-header">
        <div class="d-flex align-items-center">
            <div class="mr-auto">
                <h1 class="separator">排班管理</h1>
                <nav class="breadcrumb-wrapper" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{url('/')}}"><i class="icon dripicons-home"></i></a></li>
                        <li class="breadcrumb-item"><a href="javascript:void(0)"></a>考勤管理</li>
                        <li class="breadcrumb-item active" aria-current="page">排班管理</li>
                    </ol>
                </nav>
            </div>
        </div>
    </header>
    <section class="page-content container-fluid">
        <div class="card">
            <div style="margin: auto;">
                <form class="form-horizontal form" data-validate="parsley" action="{{  route('attendance.attendance.import')  }}"
                      method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="panel-body">
                        <div class="form-group clearfix">
                            <div class="col-sm-12" style="padding-top: 20px;">
                                <label for="uid_file" class="col-xs-1 control-label">
                                    <input type="radio" id="upload_type" name="upload_type" value="uid" checked>&nbsp;上传客服排班(重复导入则覆盖原数据)</label>
                                <input type="file" id="order_process_file" name="order_process_file" class="filestyle"
                                       data-classbutton="btn btn-default"
                                       data-classinput="form-control inline v-middle input-s">
                                <button id="submit" class="btn btn-success btn-s-xs">上传</button>
                                {{--支持csv、xlsx、xls等多种表格文件--}}
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="card">
            <h3 class="card-header">
                <form action="{{  route('attendance.attendance.appoint')  }}" method="get">
                    <div class="form-inline">
                        <div class="input-group col-md-2">
                            <div class="input-group date dp-years">
                                    <span class="input-group-addon action">
                                        <i class="icon dripicons-calendar"></i>
                                    </span>
                                <input type="text" class="form-control datepicker z-index-fix" id="month" placeholder="选择月份"
                                       name="month" readonly value="{{  $request['month'] }}">
                            </div>
                        </div>

                        <br>
                        <div class="input-group col-md-2">
                            <button type="submit" class="btn btn-primary btn-sm line-height-fix">搜索</button>
                        </div>
                    </div>
                </form>
            </h3>

            <div class="table-responsive">
                <div class="card-body">
                    <h5 class="z-index-fix"><span style="color:red;">说明</span>：tips显示员工的排班工作时间</h5>
                </div>
                <table class="table table-hover table-outline table-vcenter text-nowrap card-table">
                    <thead>
                    <tr>
                        @if($users->count())
                            <th style="width: 50px;">排班表</th>
                            @foreach($users as $user => $appoint)
                                <th style="font-weight: bold;">{{ $user }}</th>
                            @endforeach
                        @else
                            <th>暂无排班</th>
                        @endif
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($appointments as $date => $appointment)
                            <tr>
                                <td style="width: 50px;">
                                    <div <?php if (in_array(date('w', strtotime($date)), [0,6])) {echo 'style="color:red;"';}?>><?php echo date('Y-m-d', strtotime($date)) ?></div>
                                </td>
                                @foreach($appointment as $appoint)
                                    <td>
                                        <div><span data-toggle="tooltip" title="<?php echo $appoint->workClass ? $appoint->workClass->class_begin_at .'--'. $appoint->workClass->class_end_at : ''; ?>" data-placement="bottom">{{  $appoint->class_title  }}</span></div>
                                    </td>
                                @endforeach
                            </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            <div class="card-footer">
                {!! $appointments->appends($request)->links() !!}
            </div>
        </div>
        {{--<div class="card">--}}
            {{--<div style="margin: auto;">--}}
                {{--<form class="form-horizontal form" data-validate="parsley" action="{{  route('attendance.attendance.import_crew')  }}"--}}
                      {{--method="post" enctype="multipart/form-data">--}}
                    {{--@csrf--}}
                    {{--<div class="panel-body">--}}
                        {{--<div class="form-group clearfix">--}}
                            {{--<div class="col-sm-12" style="padding-top: 20px;">--}}
                                {{--<label for="uid_file" class="col-xs-1 control-label">--}}
                                    {{--<input type="radio" id="upload_type" name="upload_type" value="uid" checked>&nbsp;上传全体员工排班(重复导入则覆盖原数据)</label>--}}
                                {{--<input type="file" id="work_class_user_file" name="work_class_user_file" class="filestyle"--}}
                                       {{--data-classbutton="btn btn-default"--}}
                                       {{--data-classinput="form-control inline v-middle input-s">--}}
                                {{--<button id="submit" class="btn btn-success btn-s-xs">上传</button>--}}
                                {{--支持csv、xlsx、xls等多种表格文件--}}
                            {{--</div>--}}
                        {{--</div>--}}
                    {{--</div>--}}
                {{--</form>--}}
            {{--</div>--}}
        {{--</div>--}}
    </section>

    <div class="modal fade" id="delModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">提示</h4>
                    <button type="button" class="close" style="color: #0b0603;" data-dismiss="modal" aria-label="Close">&times;</button>
                </div>
                <div class="modal-body">
                    <p>是否删除所选班值</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                    <button type="button" class="btn btn-danger" id="del_dept">确定</button>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div>
@endsection

@section('javascript')
    <script src="/static/vendor/moment/min/moment.min.js"></script>
    <script src="/static/vendor/bootstrap-datepicker/bootstrap-datepicker.min.js"></script>
    <script src="/static/vendor/bootstrap-daterangepicker/daterangepicker.js"></script>
    <script src="/static/vendor/bootstrap-timepicker/bootstrap-datetimepicker.min.js"></script>
    <script src="/static/vendor/sweetalert2/dist/sweetalert2.min.js"></script>
    <script src="/static/js/components/bootstrap-datepicker-init.js"></script>
    <script src="/static/js/components/bootstrap-date-range-picker-init.js"></script>
    <script>
        single_del = $(".simple_del");
        single_del.click(function () {
            $("#delModal").modal('show');
            let del_single_id = $(this).data('deleteid');
            let del_dept_single = $("#del_dept");
            del_dept_single.off("click").on('click', function () {
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: "{{  route('attendance.duty.delete')  }}",
                    data: {'id': del_single_id},
                    method: 'post',
                    dataType: "json",
                    success: function (response) {
                        if (response.status == 'success') {
                            $("#duty_" + del_single_id).remove();
                            $("#delModal").modal('hide');
                            alert(response.messages);
                        } else if (response.status == 'failed') {
                            $("#delModal").modal('hide');
                            alert(response.messages);
                        }
                    }
                });
            });
        });
    </script>

    <script>
        $(function() {
            var err = '<?php echo isset($err) ?$err:'' ?>';
            if(err){
                alert(err);
            }
            var form = $(".form");
            // 表单提交拦截
            form.submit(function(e){
                if ($('#order_process_file').val() === '' && $('#work_class_user_file').val() === '') {
                    alert("请选择要上传文件");
                    return false;
                }

            });
        });

        success = "{{  session('success')  }}";
        if(success)
        {
            alert(success);
            delete success;
        }
    </script>
    <script>
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
    </script>
@endsection