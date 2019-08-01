@extends('layouts.main')
@section('content')

    <header class="page-header">
        <div class="d-flex align-items-center">
            <div class="mr-auto">
                <h1 class="separator">新建授权</h1>
                <nav class="breadcrumb-wrapper" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.html"><i class="icon dripicons-home"></i></a></li>
                        <li class="breadcrumb-item"><a href="javascript:void(0)">审批</a></li>
                        <li class="breadcrumb-item active" aria-current="page">流程设置</li>
                    </ol>
                </nav>
            </div>
        </div>
    </header>
    <section class="page-content container-fluid">
        <div class="row">
            <div class="col">
                <div class="card">
                    <form class="form-horizontal" action="{{route('workflow.authorize_agent.store')}}" method="POST">
                        {{csrf_field()}}
                        <h5 class="card-header">新授权</h5>
                        <div class="card-body">

                            <div class="form-group row">
                                <label class="control-label text-right col-md-3">流程编号</label>
                                <div class="col-md-3">
                                    {{Form::select('flow_no', $select_form, null, ['class'=>'form-control'])}}
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="control-label text-right col-md-3">代理人</label>
                                <div class="col-md-3">
                                    <select name="agent_user_id" class="js-data-example-ajax form-control select_user_name">
                                        <option value="">请选择</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="control-label text-right col-md-3">授权开始</label>
                                <div class="col-md-3">
                                    <input type="text" class="form-control datepicker z-index-fix" placeholder="" id="authorize_valid_begin"
                                           name="authorize_valid_begin" value="{{  old('authorize_valid_begin')  }}">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="control-label text-right col-md-3">授权结束</label>
                                <div class="col-md-3">
                                    <input type="text" class="form-control datepicker z-index-fix" placeholder="" id="authorize_valid_end"
                                           name="authorize_valid_end" value="{{  old('authorize_valid_end')  }}">
                                </div>
                            </div>
                        </div>
                        <div class="card-footer bg-light">
                            <div class="row">
                                <div class="offset-sm-3 col-md-1">
                                    <a href="{{route("workflow.authorize_agent.index")}}" class="btn btn-secondary btn-rounded cancel">取消</a>
                                </div>
                                <div class="offset-sm-1 col-md-1">
                                    <button type="button" class="btn btn-primary btn-rounded" data-href="{{route('workflow.authorize_agent.store')}}">确定
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
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
        $('.datepicker').datepicker({
            "autoclose": true,
            "format": "yyyy-mm-dd",
            "language": "zh-CN"
            // "startDate": "-3d"
        });
        $(function () {
            $("button.btn-rounded").on('click', function () {
                var flow_no = $("[name='flow_no']").val();
                var agent_user_id = $("[name='agent_user_id']").val();
                var authorize_valid_begin = $("[name='authorize_valid_begin']").val();
                var authorize_valid_end = $("[name='authorize_valid_end']").val();
                var data = {
                    'flow_no': flow_no,
                    'agent_user_id': agent_user_id,
                    'authorize_valid_begin': authorize_valid_begin,
                    'authorize_valid_end': authorize_valid_end
                };
                if (!agent_user_id || !authorize_valid_begin || !authorize_valid_end) {
                    alert('全是必填项哦');
                    return;
                }
                callPostAjax($(this), data, function (response) {
                    if (response.status == 'success' && response.message != '') {
                        alert(response.message);
                    }
                    window.location.href = "{{route("workflow.authorize_agent.index")}}";
                });
            })
        })
    </script>
@endsection