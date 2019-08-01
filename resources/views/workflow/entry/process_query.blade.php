@extends('layouts.main',['title' => '流程查询'])
@section('content')
    <header class="page-header">
        <div class="d-flex align-items-center">
            <div class="mr-auto">
                <h1 class="separator">流程查询</h1>
                <nav class="breadcrumb-wrapper" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{url('/')}}"><i class="icon dripicons-home"></i></a></li>
                        <li class="breadcrumb-item active" aria-current="page">我的工作流</li>
                        <li class="breadcrumb-item active" aria-current="page">流程查询</li>
                    </ol>
                </nav>
            </div>
        </div>
    </header>
    <section class="page-content container-fluid">
        <div class="card">
            <div class="row">
                <div class="col-12">
                    <div class="card-title" style="padding-top: 30px;">
                        <ul id="myTab" class="col-12 nav nav-tabs" role="tablist"
                            style="padding-top: 0px; padding-right: 10px;position: relative;">
                            <li class="nav-item"><a href="#my_apply" class="nav-link active" data-toggle="tab"><h6>我提交过的 </h6>
                                </a></li>
                            <li class="nav-item"><a href="#my_audited" class="nav-link" data-toggle="tab"><h6>
                                        我审批过的</h6></a></li>
                            <li class="nav-item"><a href="#my_procs" class="nav-link" data-toggle="tab"><h6>待我审批 </h6>
                                </a></li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="tab-content">
                        <div class="card tab-pane acitve" id="my_apply">
                            <div class="card-header">
                                <form action="{{route('workflow.entry.process_query')}}" method="get">
                                    <input type="hidden" name="tab" value="my_apply">
                                    <div class="form-inline form-search-group">
                                        <div class="">
                                            <input type="text" class="form-control form-search datepicker z-index-fix" name="create_begin" placeholder="创建时间开始" value="{{Request::input('create_begin')}}" autocomplete="off">~
                                            <input type="text" class="form-control form-search datepicker z-index-fix" name="create_end" placeholder="创建时间截止" value="{{Request::input('create_end')}}" autocomplete="off">
                                            <select class="form-control form-search" name="entry_status">
                                                <option value="">请选择申请状态</option>
                                                @foreach($entryEntriesStatusMap as $status => $des)
                                                    <option value="{{$status}}"
                                                            @if(Request::input('tab') == 'my_apply')
                                                            @if(strcmp(Request::input('entry_status'), $status) == 0)
                                                            selected
                                                            @endif
                                                            @endif
                                                    >{{$des}}</option>
                                                @endforeach
                                            </select>
                                            <select class="form-control form-search" name="flow">
                                                <option value="">请选择流程</option>
                                                @foreach($flows as $flowNo => $flowName)
                                                    <option value="{{$flowNo}}"
                                                            @if(Request::input('tab') == 'my_apply')
                                                            @if(Request::input('flow') == $flowNo)
                                                            selected
                                                            @endif
                                                            @endif
                                                    >{{$flowName}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <button type="submit" class="btn btn-primary form-search">查询</button>
                                            <button type="button" class="btn btn-info form-search clear-search">重置</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover table-outline table-vcenter text-nowrap card-table">
                                        <thead>
                                        <tr>
                                            <th>流程单号</th>
                                            <th>标题</th>
                                            <th>流程类别</th>
                                            <th>流程名称</th>
                                            <th>申请时间</th>
                                            <th>更新时间</th>
                                            <th>申请状态</th>
                                            <th>当前审批人</th>
                                            <th>操作</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($entries as $entry)
                                            <tr>
                                                <td>{{$entry->id}}</td>
                                                <td style="width: 15em;white-space:pre-wrap;">{{$entry->title}}</td>
                                                <td>{{$entry->flow->type->type_name}}</td>
                                                <td>{{$entry->flow->flow_name}}</td>
                                                <td>{{$entry->created_at}}</td>
                                                <td>{{$entry->updated_at}}</td>
                                                <td>{{$entry->getStatusDesc()}}</td>
                                                <td>
                                                    @foreach($entry->getCurrentStepProcs() as $proc)
                                                        @if(empty($proc->authorizer_ids))
                                                            <span class="audit-user-name">{{$proc->user_name}}</span>
                                                        @endif
                                                    @endforeach
                                                </td>
                                                <td>
                                                    @if($entry->isDraft())
                                                        <a href="{{route('workflow.entry.edit',['id' => $entry->id])}}"
                                                           class="badge badge-info">编辑</a>
                                                    @else
                                                        <a href="{{route('workflow.entry.show', $entry->id)}}"
                                                           class="badge badge-info">查看</a>
                                                    @endif
                                                    @if($entry->isInHand())
                                                        <a href='javascript:void(0);' data-href="{{route('workflow.entry.destroy',['id'=>$entry->id])}}"
                                                           class="badge badge-danger cancel-my-workflow">撤销</a>
                                                    @elseif($entry->isDraft())
                                                        <a href='javascript:void(0);' data-href="{{route('workflow.entry.destroy',['id'=>$entry->id])}}"
                                                           class="badge badge-warning cancel-my-workflow">删除</a>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="card-footer">
                                @if(isset($myApplySearchData) and count($myApplySearchData))
                                    {!! $entries->appends($myApplySearchData)->links() !!}
                                @else
                                    {!! $entries->links() !!}
                                @endif
                            </div>
                        </div>
                        <div class="card tab-pane" id='my_audited'>
                            <div class="card-header">
                                <form action="{{route('workflow.entry.process_query')}}" method="get">
                                    <input type="hidden" name="tab" value="my_audited">
                                    <div class="form-inline form-search-group">
                                        <div class="">
                                            <input type="text" class="form-control form-search datepicker"
                                                   name="create_begin" placeholder="创建时间开始"
                                                   value="{{Request::input('create_begin')}}" autocomplete="off">~
                                            <input type="text" class="form-control form-search datepicker"
                                                   name="create_end" placeholder="创建时间截止"
                                                   value="{{Request::input('create_end')}}" autocomplete="off">
                                            <input type="text" class="form-control form-search" name="chinese_name"
                                                   placeholder="申请人" value="{{Request::input('chinese_name')}}">
                                            <select class="form-control form-search" name="entry_status">
                                                <option value="">请选择申请状态</option>
                                                @foreach($entryAuditorStatusMap as $status => $des)
                                                    <option value="{{$status}}"
                                                            @if(Request::input('tab') == 'my_audited')
                                                                @if(strcmp(Request::input('entry_status'), $status) == 0)
                                                                    selected
                                                                @endif
                                                            @endif
                                                            >{{$des}}</option>
                                                @endforeach
                                            </select>
                                            <select class="form-control form-search" name="flow">
                                                <option value="">请选择流程</option>
                                                @foreach($flows as $flowNo => $flowName)
                                                    <option value="{{$flowNo}}"
                                                            @if(Request::input('tab') == 'my_audited')
                                                                @if(Request::input('flow') == $flowNo)
                                                                    selected
                                                                @endif
                                                            @endif
                                                            >{{$flowName}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <button type="submit" class="btn btn-primary form-search">查询</button>
                                            <button type="button" class="btn btn-info form-search clear-search">重置</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover table-outline table-vcenter text-nowrap card-table">
                                        <thead>
                                        <tr>
                                            <th>流程单号</th>
                                            <th>申请人</th>
                                            <th>流程名称</th>
                                            <th>流程节点</th>
                                            <th>标题</th>
                                            <th>申请单状态</th>
                                            <th>申请时间</th>
                                            <th>操作</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($procsAudited as $proc)
                                            <tr>
                                                <td>{{$proc->entry->id}}</td>
                                                <td>{{Q($proc,'entry','user','chinese_name')}}</td>
                                                <td>{{$proc->entry->flow->flow_name}}</td>
                                                <td>{{$proc->process_name}}</td>
                                                <td style="width: 15em;white-space:pre-wrap;">{{$proc->entry->title}}</td>
                                                <td>
                                                    {{\App\Models\Workflow\Entry::STATUS_MAP[$proc->entry->status]}}
                                                </td>
                                                <td>{{$proc->entry->created_at}}</td>
                                                <td>
                                                    <a href='/workflow/proc/{{$proc->id}}'
                                                       class="badge badge-info">查看</a>
                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="card-footer">
                                @if(isset($myAuditedSearchData) and count($myAuditedSearchData))
                                    {!! $procsAudited->appends($myAuditedSearchData)->links() !!}
                                @else
                                    {!! $procsAudited->links() !!}
                                @endif
                            </div>
                        </div>
                        <div class="card tab-pane" id="my_procs">
                            <div class="card-header">
                                <form action="{{route('workflow.entry.process_query')}}" method="get">
                                    <input type="hidden" name="tab" value="my_procs">
                                    <div class="form-inline form-search-group">
                                        <div class="">
                                            <input type="text" class="form-control form-search datepicker" name="create_begin" placeholder="创建时间开始" value="{{Request::input('create_begin')}}" autocomplete="off">~
                                            <input type="text" class="form-control form-search datepicker" name="create_end" placeholder="创建时间截止" value="{{Request::input('create_end')}}" autocomplete="off">
                                            <input type="text" class="form-control form-search" name="chinese_name" placeholder="申请人" value="{{Request::input('chinese_name')}}">

                                            <select class="form-control form-search" name="flow">
                                                <option value="">请选择流程</option>
                                                @foreach($flows as $flowNo => $flowName)
                                                    <option value="{{$flowNo}}"
                                                            @if(Request::input('tab') == 'my_procs')
                                                                @if(Request::input('flow') == $flowNo)
                                                                    selected
                                                                @endif
                                                            @endif
                                                            >{{$flowName}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <button type="submit" class="btn btn-primary form-search">查询</button>
                                            <button type="button" class="btn btn-info form-search clear-search">重置</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover table-outline table-vcenter text-nowrap card-table">
                                        <thead>
                                        <tr>
                                            <th>流程单号</th>
                                            <th>申请人</th>
                                            <th>流程名称</th>
                                            <th>标题</th>
                                            <th>申请时间</th>
                                            <th>当前位置</th>
                                            <th>操作</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($procsProc as $proc)
                                            <tr>
                                                <td>{{$proc->entry->id}}</td>
                                                <td>{{$proc->entry->user->chinese_name}}</td>
                                                <td>{{$proc->entry->flow->flow_name}}</td>
                                                <td style="width: 15em;white-space:pre-wrap;">{{$proc->entry->title}}</td>
                                                <td>{{$proc->entry->created_at}}</td>
                                                <td>{{$proc->process_name}}</td>
                                                <td>
                                                    @if($proc->entry->isInHand())
                                                        <a href='/workflow/proc/{{$proc->id}}' class="badge badge-info">批复</a>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="card-footer">
                                @if(isset($myProcsSearchData) and count($myProcsSearchData))
                                    {!! $procsProc->appends($myProcsSearchData)->links() !!}
                                @else
                                    {!! $procsProc->links() !!}
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
@section('javascript')
    <script src="/static/vendor/bootstrap-datepicker/bootstrap-datepicker.min.js"></script>
    <script src="/static/vendor/bootstrap-daterangepicker/daterangepicker.js"></script>
    <script src="/static/js/components/bootstrap-datepicker-init.js"></script>
    <script>
        $('.datepicker').datepicker({
            "autoclose": true,
            "format": "yyyy-mm-dd",
            "language": "zh-CN"
            // "startDate": "-3d"
        });


        //ab保持当前选项卡
        $("#myTab li").click(function(){
            var processTabNum = $(this).index();
            $(this).siblings().children().removeClass("active show");
            sessionStorage.setItem("processTabNum",processTabNum);
        });
        $(function () {
            $("#myTab li a").eq(0).removeClass("active show");
            var getProcessTabNum = sessionStorage.getItem("processTabNum");

            $("#myTab li a").eq(getProcessTabNum).addClass("active show").parent().siblings().removeClass("active show");
            $(".tab-content>div").eq(getProcessTabNum).addClass("active show").siblings().removeClass("active show");
        });

        $('.clear-search').click(function () {
            var formObj = $(this).closest('form');
            formObj.find('input,select').each(function () {
                $(this).val('');
            });
        });
        $('.cancel-my-workflow').on('click', function () {
            callDeleteAjax($(this));
        })
    </script>
@endsection