@extends('layouts.main')

@section('content')
    <header class="page-header">
        <div class="d-flex align-items-center">
            <div class="mr-auto">
                <h1 class="separator">{{ $flow->flow_name }}</h1>
                <nav class="breadcrumb-wrapper" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{url('/')}}"><i class="icon dripicons-home"></i></a></li>
                        <li class="breadcrumb-item"><a href="javascript:void(0)"></a>审核管理</li>
                        <li class="breadcrumb-item active" aria-current="page">{{ $flow->flow_name }}</li>
                    </ol>
                </nav>
            </div>
        </div>
    </header>
    <section class="page-content container-fluid">
        <div class="card">
            <h3 class="card-header collapse" id="collapseExample">
                <form action="{{  route('workflow.approval.flow', [$flow->id])  }}" method="GET" id="search_form">
                    @csrf
                    {{--@if(isset($request))--}}
                    {{--@endif--}}
                    <br>
                    <div class="row  font-size-fix">
                        <input type="hidden" name="id" value="{{ $flow->id }}">
                        <div class="form-inline col-md-12">
                            <div class="input-group date dp-years">
                                <input type="text" class="form-control datepicker z-index-fix" readonly placeholder="提交开始日期"
                                       name="start_at" value="@php echo  isset($request['start_at']) ? $request['start_at'] : ''; @endphp">
                                <span class="input-group-addon action">
                                    <i class="icon dripicons-calendar"></i>
                                </span>
                            </div> &nbsp;~&nbsp;
                            <div class="input-group date dp-years">
                                <input type="text" class="form-control datepicker z-index-fix" readonly placeholder="提交结束日期"
                                       name="end_at" value="@php echo  isset($request['end_at']) ? $request['end_at'] : ''; @endphp">
                                <span class="input-group-addon action">
                                    <i class="icon dripicons-calendar"></i>
                                </span>
                            </div>

                            <div class="form-inline col-md-2">
                                <input type="text" class="form-control" name="chinese_name" placeholder="申请人" value="@php echo isset($request['chinese_name']) ? $request['chinese_name'] : ''; @endphp">
                            </div>

                            <div class="form-inline col-md-2">
                                <input type="text" class="form-control" name="entry_id" placeholder="审批编号" value="@php echo  isset($request['entry_id']) ? $request['entry_id'] : ''; @endphp">
                            </div>

                            <div class="form-inline col-md-2">
                                &nbsp;
                                <label class="form-label">审批状态</label>
                                <select name="status" class="form-control">
                                    <option value="">请选择</option>
                                    @foreach(\App\Models\Workflow\Entry::STATUS_MAP as $key=>$comment)
                                        <option value="{{ $key }}" <?php if (isset($request['status']) && $request['status'] == $key) {
                                            echo 'selected';
                                        }?>>{{$comment}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                    </div>

                    <br>
                    <div class="row" style="padding-left: 40%;">
                        <input type="submit" class="btn btn-sm btn-primary search-btn" value="查询">&nbsp;&nbsp;
                        <input type="reset" class="btn btn-sm btn-danger reset-btn" value="清空">
                        <div style="margin-left: 0.7em">
                            <a class="btn btn-sm btn-primary" href="{{  route('workflow.approval.index')  }}">返回</a>
                        </div>
                        <div style="margin-left: 0.7em">
                            <a class="btn btn-sm btn-warning download-btn" href="javascript:void(0)">导出</a>
                        </div>
                    </div>
                </form>
            </h3>
            <div class="table-responsive">
                <table class="table card-table table-vcenter text-nowrap">
                    <thead>
                    <tr class="text-center">
                        <th>审批编号</th>
                        <th>类型</th>
                        <th>提交时间</th>
                        <th>申请人</th>
                        <th>状态</th>
                        <th>操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    @if($data)
                        @foreach($data as $key=>$entry)
                        <tr class="text-center">
                            <td>{{  $key  }}</td>
                            <td>{{  $flow->flow_name  }}</td>
                            <td>{{  $entry['entry']['created_at']  }}</td>
                            <td>{{  \App\Models\User::findById($entry['entry']['user_id'])->chinese_name }}</td>
                            <td>{{  \App\Models\Workflow\Entry::STATUS_MAP[$entry['entry']['status']] }}</td>
                            <td>
                                <a target="_blank" href="/workflow/hrshow/{{$key}}" class="btn btn-info btn-sm">查看</a>
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
@section('javascript')
    <script src="/static/vendor/moment/min/moment.min.js"></script>
    <script src="/static/vendor/bootstrap-datepicker/bootstrap-datepicker.min.js"></script>
    <script src="/static/vendor/bootstrap-daterangepicker/daterangepicker.js"></script>
    <script src="/static/js/components/bootstrap-datepicker-init.js"></script>
    <script src="/static/js/components/bootstrap-date-range-picker-init.js"></script>
    <script>
        @if(isset($request) && $request)
        $("#collapseExample").collapse('show');
        @endif
        $('.datepicker').parent().datepicker({
            "autoclose": true,
            "format": "yyyy-mm-dd",
            "language": "zh-CN"
            // "startDate": "-3d"
        });

        //重置
        $('.reset-btn').click(function(){
            $('.row input[type="text"]').attr("value",'');
            $(".row select").each(function(i, j){
                $(j).find("option:selected").attr("selected", false);
                $(j).find("option").first().attr("selected", true);
            });
        });

        //导出
        $('.download-btn').click(function () {
            $("#search_form").attr("action", "{{  route('workflow.approval.flow_export', [$flow->id])  }}" ).submit();
        });
        $('.search-btn').click(function () {
            $("#search_form").attr("action", "{{  route('workflow.approval.flow', [$flow->id])  }}" ).submit();
        });
    </script>
@endsection