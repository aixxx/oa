@extends('layouts.main',['title' => '我的申请'])

@section('content')
    <header class="page-header">
        <div class="d-flex align-items-center">
            <div class="mr-auto">
                <h1 class="separator">我的申请</h1>
                <nav class="breadcrumb-wrapper" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{url('/')}}"><i class="icon dripicons-home"></i></a></li>
                        <li class="breadcrumb-item active" aria-current="page">我的申请</li>
                    </ol>
                </nav>
            </div>
        </div>
    </header>
    <section class="page-content container-fluid">
        <div class="card">
            <div class="card-header">
                <form action="{{route('workflow.entry.my_apply')}}" method="get">
                    <div class="form-inline form-search-group">
                        <div class="form-group">
                            <input type="text" class="form-control form-search datepicker z-index-fix" name="create_begin" placeholder="创建时间开始" value="{{Request::input('create_begin')}}" autocomplete="off">~
                            <input type="text" class="form-control form-search datepicker z-index-fix" name="create_end" placeholder="创建时间截止" value="{{Request::input('create_end')}}" autocomplete="off">
                            <select class="form-control form-search" name="entry_status">
                                <option value="">请选择申请状态</option>
                                @foreach($entryStatusMap as $status => $des)
                                    <option value="{{$status}}" @if(strcmp(Request::input('entry_status'), $status) == 0) selected @endif>{{$des}}</option>
                                @endforeach
                            </select>
                            <select class="form-control form-search" name="flow">
                                <option value="">请选择流程</option>
                                @foreach($flows as $flowNo => $flowName)
                                    <option value="{{$flowNo}}" @if(Request::input('flow') == $flowNo) selected @endif>{{$flowName}}</option>
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
                                <td>{{$entry->title}}</td>
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
                @if(isset($searchData) and count($searchData))
                    {!! $entries->appends($searchData)->links() !!}
                @else
                    {!! $entries->links() !!}
                @endif
            </div>
        </div>
    </section>
@endsection
@section('javascript')
    <!-- ================== DATEPICKER SCRIPTS ==================-->
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