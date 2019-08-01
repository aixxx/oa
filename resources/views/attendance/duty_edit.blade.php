@extends('layouts.main',['title' => '编辑班值'])

@section('content')
    <header class="page-header">
        <div class="d-flex align-items-center">
            <div class="mr-auto">
                <h1 class="separator">编辑班值</h1>
                <nav class="breadcrumb-wrapper" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{url('/')}}"><i class="icon dripicons-home"></i></a></li>
                        <li class="breadcrumb-item"><a href="javascript:void(0)"></a>班值管理</li>
                        <li class="breadcrumb-item active" aria-current="page">编辑班值</li>
                    </ol>
                </nav>
            </div>
        </div>
    </header>
    <section class="page-content container-fluid">
        <div class="card">
            <div class="card-body">
                <form action="{{  route('attendance.duty.update',['class_id' => $duty->class_id])  }}" method="POST">
                    @csrf
                    <input type="hidden" name="_method" value="PUT">
                    <div class="form-group">
                        @if($errors->get('class_title'))
                            <div class="alert alert-danger">
                                @foreach($errors->get('class_title') as $error)
                                    {{  $error  }}<br>
                                @endforeach
                            </div>
                        @endif
                        <div class="form-group col-6">
                            <label class="form-label">班值代码 &nbsp;</label>
                            <input type="text" name="class_title" class="form-control" value="{{  $duty->class_title  }}">
                        </div>
                    </div>
                    <div class="form-group">
                        @if($errors->get('class_name'))
                            <div class="alert alert-danger">
                                @foreach($errors->get('class_name') as $error)
                                    {{  $error  }}<br>
                                @endforeach
                            </div>
                        @endif
                        <div class="form-group col-6">
                            <label class="form-label">班值名称 &nbsp;</label>
                            <input type="text" name="class_name" class="form-control" value="{{  $duty->class_name  }}">
                        </div>
                    </div>
                    <div class="form-group">
                        @if($errors->get('class_begin_at'))
                            <div class="alert alert-danger">
                                @foreach($errors->get('class_begin_at') as $error)
                                    {{  $error  }}<br>
                                @endforeach
                            </div>
                        @endif
                        <div class="form-group col-10">
                            <label class="form-label">上班时间 &nbsp;【如09:00】</label>
                            <div class="input-group col-md-7">
                                <div class="input-group date">
                                                    <span class="input-group-addon action">
                                                            <i class="icon"></i>
                                                    </span>
                                    <input type="text" class="form-control  z-index-fix" placeholder=""
                                           name="class_begin_at" value="{{  $duty->class_begin_at  }}">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        @if($errors->get('class_end_at'))
                            <div class="alert alert-danger">
                                @foreach($errors->get('class_end_at') as $error)
                                    {{  $error  }}<br>
                                @endforeach
                            </div>
                        @endif
                        <div class="form-group col-10">
                            <label class="form-label">下班时间 &nbsp;【如18:00】</label>
                            <div class="input-group col-md-7">
                                <div class="input-group date">
                                    <span class="input-group-addon action">
                                        <i class="icon "></i>
                                    </span>
                                    <input type="text" class="form-control z-index-fix" placeholder=""
                                           name="class_end_at" value="{{  $duty->class_end_at  }}">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        @if($errors->get('class_rest_begin_at'))
                            <div class="alert alert-danger">
                                @foreach($errors->get('class_rest_begin_at') as $error)
                                    {{  $error  }}<br>
                                @endforeach
                            </div>
                        @endif
                        <div class="form-group col-10">
                            <label class="form-label">休息开始时间 &nbsp;【如12:00】</label>
                            <div class="input-group col-md-7">
                                <div class="input-group date">
                                                    <span class="input-group-addon action">
                                                            <i class="icon"></i>
                                                    </span>
                                    <input type="text" class="form-control  z-index-fix" placeholder=""
                                           name="class_rest_begin_at" value="{{  $duty->class_rest_begin_at  }}">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        @if($errors->get('class_rest_end_at'))
                            <div class="alert alert-danger">
                                @foreach($errors->get('class_rest_end_at') as $error)
                                    {{  $error  }}<br>
                                @endforeach
                            </div>
                        @endif
                        <div class="form-group col-10">
                            <label class="form-label">休息结束时间 &nbsp;【如13:00】</label>
                            <div class="input-group col-md-7">
                                <div class="input-group date">
                                    <span class="input-group-addon action">
                                        <i class="icon "></i>
                                    </span>
                                    <input type="text" class="form-control z-index-fix" placeholder=""
                                           name="class_rest_end_at" value="{{  $duty->class_rest_end_at  }}">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group col-6">
                        <label class="form-label">班次／日&nbsp;</label>
                        @if($errors->get('class_times'))
                            <div class="alert alert-danger">
                                @foreach($errors->get('class_times') as $error)
                                    {{  $error  }}<br>
                                @endforeach
                            </div>
                        @endif
                        <select name="class_times" class="form-control">
                            <option value="">请选择</option>
                            <option value="1" <?php if ($duty->class_times == 1) {echo 'selected';}?>>1次／日</option>
                            <option value="2" <?php if ($duty->class_times == 2) {echo 'selected';}?>>2次／日</option>
                            <option value="3" <?php if ($duty->class_times == 3) {echo 'selected';}?>>3次／日</option>
                        </select>
                    </div>
                    <div class="form-group col-6">
                        <label class="form-label">所属类型&nbsp;</label>
                        @if($errors->get('type'))
                            <div class="alert alert-danger">
                                @foreach($errors->get('type') as $error)
                                    {{  $error  }}<br>
                                @endforeach
                            </div>
                        @endif
                        <select name="type" class="form-control">
                            @foreach(\App\Models\Attendance\AttendanceWorkClass::CLASS_TYPE as $key=>$type)
                                <option value="{{ $key }}" <?php if ($duty->type == $key) {echo 'selected';}?>>{{ $type }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="text-left">
                        <button type="submit" class="btn btn-primary btn-sm line-height-fix">保存</button>
                    </div>
                </form>
            </div>
        </div>
    </section>

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
        save_error = "{{  session('saveError')  }}";
        if (save_error) {
            alert(save_error);
        }

        $('.datepicker').parent().datepicker({
            "autoclose": true,
            "maxViewMode":'hours', //最大视图层，为年视图层
            "minViewMode":'hours', //最小视图层，为月视图层
            // "format": "yyyy-mm-dd",
            // "language": "zh-CN",
            "minDate": new Date()
            // "startDate": "-3d"
        });

        $("input[name='capital']").blur(function () {
            capital = $.trim($("input[name='capital']").val());
            if (capital.length > 10 || capital.length < 1 || parseFloat(capital) >= 2147483647) {
                alert('注册资本输入有误');
                return false;
            }
        })

    </script>
@endsection