@extends('layouts.main',['title' => '审批管理'])
@section('content')
    <header class="page-header">
        <div class="d-flex align-items-center">
            <div class="mr-auto">
                <h1 class="separator">审批管理</h1>
                <nav class="breadcrumb-wrapper" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{url('/')}}"><i class="icon dripicons-home"></i></a></li>
                        <li class="breadcrumb-item active" aria-current="page">流程管理</li>
                        <li class="breadcrumb-item active" aria-current="page">审批管理</li>
                    </ol>
                </nav>
            </div>
        </div>
    </header>
    <section class="page-content container-fluid">
        <div class="card container">
            <div class="row">
                <div class="col-12">
                    <div class="tab-content">
                        <div class="card tab-pane container active show" id="my_apply">
                            <div class="card-header">
                                <form action="{{route('workflow.manager.index')}}" method="get">
                                    <div class="form-inline form-search-group">
                                        <div class="form-group">
                                            <select class="form-control form-search" name="flow">
                                                <option value="">请选择流程</option>
                                                @foreach($flows as $flowNo => $flowName)
                                                    <option value="{{$flowNo}}"
                                                            @if(Request::input('flow') == $flowNo)
                                                            selected
                                                            @endif
                                                    >{{$flowName}}</option>
                                                @endforeach
                                            </select>
                                            <select class="form-control form-search" name="entry_status">
                                                <option value="">请选择流程状态</option>
                                                @foreach($entryEntriesStatusMap as $status => $des)
                                                    <option value="{{$status}}"
                                                            @if(strcmp(Request::input('entry_status'), $status) == 0)
                                                            selected
                                                            @endif
                                                    >{{$des}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-inline form-search-group">
                                        <div class="form-group">
                                            <input class="form-control form-search datepicker z-index-fix" name="create_begin"
                                                   placeholder="申请时间开始"
                                                   value="{{Request::input('create_begin')}}" autocomplete="off">
                                            <input class="form-control form-search datepicker z-index-fix" name="create_end" placeholder="申请时间截止"
                                                   value="{{Request::input('create_end')}}" autocomplete="off">
                                            <input name="userNameOrNo" value="{{Request::input('userNameOrNo')}}" class="form-control form-search"
                                                   placeholder="申请人姓名或者工号">
                                        </div>
                                        <div class="form-group">
                                            <button class="btn btn-primary form-search">查询</button>
                                            <button type="button" class="btn btn-info form-search clear-search">重置</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered table-outline table-vcenter text-nowrap card-table">
                                        <thead>
                                        <tr>
                                            <th>流程单号</th>
                                            <th>员工工号</th>
                                            <th>员工姓名</th>
                                            {{--<th>所属部门</th>--}}
                                            <th>流程类别</th>
                                            <th>流程标题</th>
                                            <th>申请时间</th>
                                            <th>申请状态</th>
                                            <th>当前审批人</th>
                                            <th>操作</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($entries as $entry)
                                            <tr>
                                                <td><a href="{{route('workflow.entry.manager.show',['id' => $entry->id,'userId' => $entry->user_id])
                                                }}">{{$entry->id}}</a></td>
                                                <td>{{$entry->user->employee_num}}</td>
                                                <td>{{$entry->user->chinese_name}}</td>
                                                {{--<td></td>--}}
                                                <td>{{$entry->flow->type->type_name}}</td>
                                                <td>{{$entry->title}}</td>
                                                <td>{{$entry->created_at}}</td>
                                                <td>{{$entry->getStatusDesc()}}</td>
                                                <td>
                                                    @foreach($entry->getCurrentStepProcs() as $proc)
                                                        @if(empty($proc->authorizer_ids))
                                                            <span class="audit-user-name">{{$proc->user_name}}</span>
                                                        @endif
                                                    @endforeach
                                                </td>
                                                <td>
                                                    @if($entry->isInHand())
                                                        <a href='javascript:void(0);' data-href="{{route('workflow.manager.destroy',['id'=>$entry->id])}}"
                                                           class="badge badge-danger cancel-my-workflow">撤销</a>
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
        });

        $('.clear-search').click(function () {
            var formObj = $(this).closest('form');
            formObj.find('input,select').each(function () {
                $(this).val('');
            });
        });

        $('.cancel-my-workflow').on('click', function () {
            if (confirm('确定要撤销流程申请吗？')) {
                callDeleteAjax($(this));
            }
        })
    </script>
@endsection