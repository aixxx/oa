@extends('layouts.main',['title' => '添加离职信息'])

@section('content')
    <header class="page-header">
        <div class="d-flex align-items-center">
            <div class="mr-auto">
                <h1 class="separator">离职信息添加</h1>
                <nav class="breadcrumb-wrapper" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{url('/')}}"><i class="icon dripicons-home"></i></a></li>
                        <li class="breadcrumb-item"><a href="javascript:void(0)"></a>员工管理</li>
                        <li class="breadcrumb-item active" aria-current="page">离职信息添加</li>
                    </ol>
                </nav>
            </div>
        </div>
    </header>
    <section class="page-content container-fluid">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">添加{{  $user->chinese_name  }}离职信息</h3>
            </div>
            <div class="card-body">
                <form action="{{  route('dimission.store')  }}" method="POST">
                    @csrf
                    <input type="hidden" name="user_id" value="{{  $user->id  }}">
                    <div class="form-group">
                        @if($errors->get('is_voluntary'))
                            <div class="alert alert-danger">
                                @foreach($errors->get('is_voluntary') as $error)
                                    {{  $error  }}<br>
                                @endforeach
                            </div>
                        @endif
                        <label class="form-label control-label col-md-5" style="justify-content: left;">是否主动离职 <span
                                    style="color:red">*</span>&nbsp;</label>
                        <div class="col-md-7">
                            <select class="form-control" name="is_voluntary">
                                <option value="1">是</option>
                                <option value="2">否</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        @if($errors->get('is_sign'))
                            <div class="alert alert-danger">
                                @foreach($errors->get('is_sign') as $error)
                                    {{  $error  }}<br>
                                @endforeach
                            </div>
                        @endif
                        <label class="form-label control-label col-md-5" style="justify-content: left;">直属领导是否已签字 <span
                                    style="color:red">*</span>&nbsp;</label>
                        <div class="col-md-7">
                            <select class="form-control" name="is_sign">
                                <option value="1">是</option>
                                <option value="2">否</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        @if($errors->get('is_complete'))
                            <div class="alert alert-danger">
                                @foreach($errors->get('is_complete') as $error)
                                    {{  $error  }}<br>
                                @endforeach
                            </div>
                        @endif

                        <label class="form-label col-md-5" style="justify-content:left;">手续是否已办理完成 <span
                                    style="color:red">*</span>&nbsp;</label>
                        <div class="col-md-7">
                            <select class="form-control" name="is_complete">
                                <option value="1">是</option>
                                <option value="2">否</option>
                            </select>

                        </div>

                    </div>
                    <div class="form-group">
                        @if($errors->get('reason'))
                            <div class="alert alert-danger">
                                @foreach($errors->get('reason') as $error)
                                    {{  $error  }}<br>
                                @endforeach
                            </div>
                        @endif
                        <div class="form-group col-10">
                            <label class="form-label">离职原因 &nbsp;</label>
                            <textarea type="text" rows="3" cols="20" name="reason" class="form-control"
                                      style="resize:none">{{  old('reason') }}</textarea>
                        </div>
                    </div>

                    <div class="form-group">
                        @if($errors->get('interview_result'))
                            <div class="alert alert-danger">
                                @foreach($errors->get('interview_result') as $error)
                                    {{  $error  }}<br>
                                @endforeach
                            </div>
                        @endif
                        <div class="form-group col-10">
                            <label class="form-label">面谈结论 &nbsp;</label>
                            <textarea type="text" rows="3" cols="20" name="interview_result" class="form-control"
                                      style="resize:none">{{  old('interview_result') }}</textarea>
                        </div>
                    </div>

                    <div class="form-group">
                        @if($errors->get('note'))
                            <div class="alert alert-danger">
                                @foreach($errors->get('note') as $error)
                                    {{  $error  }}<br>
                                @endforeach
                            </div>
                        @endif
                        <div class="form-group col-10">
                            <label class="form-label">备注 &nbsp;</label>
                            <input type="text" name="note" class="form-control" value="{{  old('note')  }}">
                        </div>
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
            "format": "yyyy-mm-dd",
            "language": "zh-CN"
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