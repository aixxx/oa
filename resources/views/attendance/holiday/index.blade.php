@extends("layouts.main",['title' => '节假日管理'])
@section('content')
    <header class="page-header">
        <div class="d-flex align-items-center">
            <div class="mr-auto">
                <h1 class="separator">节假日管理</h1>
                <nav class="breadcrumb-wrapper" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{url('/')}}"><i class="icon dripicons-home"></i></a></li>
                        <li class="breadcrumb-item"><a href="javascript:void(0)"></a>考勤管理</li>
                        <li class="breadcrumb-item active" aria-current="page">节假日管理</li>
                    </ol>
                </nav>
            </div>
        </div>
    </header>

    <section class="page-content container-fluid">
        <header class="panel-heading font-bold">提示：批量导入节假日管理数据(请填写'1000-01-01'格式的日期)</header>
        <div class="card" style="margin-top: 1%;height: 7%;">
            <form class="form-horizontal upload_file" data-validate="parsley" action="{{  route('holiday.addHolidays')  }}" method="post" enctype="multipart/form-data">
                @csrf
                <div class="panel-body">
                    <div class="form-group clearfix" style="margin-top: 1%">
                        <div class="col-sm-12">
                            <label for="uid_file" class="col-xs-1 control-label"><input type="radio" id="upload_type"
                                                                                        name="upload_type" value="uid"
                                                                                        checked>&nbsp;上传文件</label>
                            <input type="file" id="order_process_file" name="order_process_file" class="filestyle"
                                   data-classbutton="btn btn-default"
                                   data-classinput="form-control inline v-middle input-s">
                            <button id="submit" class="btn btn-success btn-s-xs">上传</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </section>
    <div class="actions top-right">
        <a href="{{  route('holiday.download')  }}" class="btn btn-primary  btn-sm line-height-fix">节假日数据导入模版下载</a>
    </div>
    <section class="page-content container-fluid">
        <div class="card">
            <h3 class="card-header">
                <form action="{{  route('holiday.chooseTime')  }}" method="get">
                    <div class="form-inline">
                        <div class="form-group">
                            <label class="form-label control-label" style="justify-content: left;">选择期间</label>
                            <div class="input-group col-md-7">
                                <div class="input-group date dp-years">
                                    <span class="input-group-addon action">
                                        <i class="icon dripicons-calendar"></i>
                                    </span>
                                    <input type="text" class="form-control datepicker z-index-fix" placeholder=""
                                           name="month"
                                           value='{{  $beginTime }}'>
                                </div>
                            </div>
                        </div>
                        <div>
                            <button type="submit" class="btn btn-primary btn-sm line-height-fix">搜索</button>
                        </div>
                    </div>

                </form>
            </h3>
            <div class="table-responsive">
                <table class="table table-hover table-outline table-vcenter text-nowrap card-table">
                    <thead>
                    <tr>
                        <th>日期</th>
                        <th>工作状态</th>
                        <th>是否法定节假日</th>
                        <th>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    @if(count($holidays))
                        @foreach($holidays as $holiday)
                            <tr>
                                <td>
                                    <div <?php if ($holiday->holiday_status == 1) {echo 'style="color:red;"';}?>>
                                        <?php echo date('Y-m-d',strtotime($holiday->holiday_date)) ; ?>
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        <?php echo $holiday->holiday_status == 0 ? '工作' : '休息';  ?>
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        <?php echo $holiday->holiday_type == 0 ? '否' : '是';  ?>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <div class="item-action dropdown" style="margin-right:80%">
                                        <a href="javascript:void(0)" class="btn btn-fab " data-toggle="dropdown"
                                           aria-expanded="false" style="padding-top: 0px;">
                                            <i class="icon dripicons-dots-3 rotate-90 font-size-24"></i>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-right">
                                            <a href="javascript:void(0)" class="dropdown-item edit-type"
                                               data-date="{{  date('Y-m-d',strtotime($holiday->holiday_date))  }}"
                                               data-id="{{ $holiday->holiday_id  }}"
                                               data-target="#editTypeModal"><i
                                                        class="icon dripicons-pencil"></i>编辑</a>
                                        </div>
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
    <!--编辑类型-->
    <div class="modal fade" id="editTypeModal" role="dialog" aria-labelledby="exampleModalLabel">
        <div class="modal-dialog" role="document">

            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="exampleModalLabel">编辑节假日</h4>
                    <button type="button" class="close" style="color: #0b0603;" data-dismiss="modal" aria-label="Close">&times;</button>
                </div>
                <div class="modal-body">
                    <form action="{{  route('holiday.updateHoliday')  }}" method="post">
                        @csrf
                        <input type="hidden" name="_method" value="put">
                        <div class="form-group">
                            <label for="recipient-name" class="control-label">日期<label class="text-danger"></label></label>
                            <input type="text" class="form-control" name="holiday_date" value="" readonly>
                        </div>
                        <div class="form-group">
                            <label for="recipient-name" class="control-label holiday_status">状态</label>
                            <input type="hidden" name="holiday_id">
                            <label class="col-form-label text-danger">*</label>
                            <select class="form-control" name="holiday_status">
                                <option value="0">工作</option>
                                <option value="1">休息</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="recipient-name" class="control-label holiday_status">是否法定节假日</label>
                            <label class="col-form-label text-danger">*</label>
                            <select class="form-control" name="holiday_type">
                                <option value="0">否</option>
                                <option value="1">是</option>
                            </select>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default btn-sm line-height-fix" data-dismiss="modal">取消</button>
                            <button type="submit" class="btn btn-primary btn-sm line-height-fix" id="child_save">保存</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
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
            "maxViewMode":'years', //最大视图层，为年视图层
            "minViewMode":'months', //最小视图层，为月视图层
            "format": "yyyy-mm",
            "language": "zh-CN"
            // "startDate": "-3d"
        });
    </script>
    <script>
        $(function(){
            var err = '<?php echo isset($err) ?$err:'' ?>';
            if(err){
                alert(err);
            }
            var form = $(".upload_file");
            // 表单提交拦截
            form.submit(function(e){
                if ($('#order_process_file').val() === '') {
                    alert("文件都不上传，你想嘎哈？");
                    return false;
                }
            });
        });
    </script>
    <script>
        addHolidaysSuccess = "{{  session('addHolidaysSuccess')  }}";
        if(addHolidaysSuccess)
        {
            alert(addHolidaysSuccess);
            delete addHolidaysSuccess;
        }
        $(".edit-type").click(function(){
            date = $(this).data('date');
            id = $(this).data('id');
            $("#editTypeModal").modal('show');
            $("#editTypeModal").off('shown.bs.modal').on('shown.bs.modal',function () {
                $("input[name='holiday_date']").val(date);
                $("input[name='holiday_id']").val(id);
            });
        });
    </script>
    <script>
        updateHolidaySuccess = "{{  session('updateHolidaySuccess')  }}";
        if(updateHolidaySuccess)
        {
            alert(updateHolidaySuccess);
            delete updateHolidaySuccess;
        }
    </script>
@endsection