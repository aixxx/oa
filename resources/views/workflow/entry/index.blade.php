@extends('layouts.main')
@section('head')
    <style>
        a.more {
            font-size: 12px;
        }
    </style>
@endsection
@section('content')
    <header class="page-header">
        <div class="d-flex align-items-center">
            <div class="mr-auto"><h1>我的工作流</h1></div>
            <div class="top-right">
                <a href="{{ route('workflow.entry.create') }}" class="btn btn-primary">发起申请</a>
            </div>
        </div>
    </header>
    <section class="page-content container-fluid">
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <h5 class="card-header">
                        待办事项
                        <a href="{{route('workflow.entry.my_procs')}}" class="pull-right more">更多>></a>
                    </h5>
                    <div class="card-body">
                        <table class="table v-align-middle">
                            <thead class="bg-light">
                            <tr>
                                <th>单号</th>
                                <th>流程</th>
                                <th>发起人</th>
                                <th>当前位置</th>
                                <th>操作</th>
                            </tr>
                            </thead>
                            <tbody>
                            @if($procs->count()>0)
                                @foreach($procs as $v)
                                    <tr>
                                        <td>
                                            {{$v->entry->id}}
                                        </td>
                                        <td>
                                            <a href="/workflow/proc/{{$v->id}}">{{$v->flow?$v->flow->flow_name:('流程'.$v->flow_id)}}</a>
                                        </td>
                                        <td>{{$v->entry->user->chinese_name}}</td>
                                        <td>{{$v->process_name}}</td>
                                        <td>
                                        @if($v->status==0)
                                            <!-- 进行中. <a href="/pass/{{$v->id}}">通过</a> <a href="/unpass/{{$v->id}}">不通过</a> -->
                                                <a href='/workflow/proc/{{$v->id}}' class="badge badge-info">批复</a>
                                            @elseif($v->status==9)
                                                已通过
                                            @elseif($v->status==-1)
                                                已驳回
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <td colspan="5">暂无待办事项</td>
                            @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <h5 class="card-header">
                        我提交的
                        <a href="{{route('workflow.entry.my_apply')}}" class="pull-right more">更多>></a>
                    </h5>
                    <div class="card-body">
                        <table class="table v-align-middle">
                            <thead class="bg-light">
                            <tr>
                                <th>标题</th>
                                <th>当前位置</th>
                                <th>当前状态</th>
                                <th>操作</th>
                            </tr>
                            </thead>
                            <tbody>
                            @if($entries->count()>0)
                                @foreach($entries as $v)
                                    <tr>
                                        <td>
                                            <a href="{{ $v->isDraft()?route('workflow.entry.edit',['id'=>$v->id]):route('workflow.entry.show', $v->id) }}">
                                                {{$v->title }}
                                            </a>
                                        </td>
                                        <td>
                                            @if($v->status != -9)
                                                {{--非草稿--}}
                                                @if($v->child > 0)
                                                    <span class="text text-danger">子流程({{$v->child_process->flow->flow_name}}
                                                        ):</span>{{$v->child_process->process_name}}
                                                @else
                                                    {{$v->process?$v->process->process_name:''}}
                                                @endif
                                            @endif
                                        </td>
                                        <td>
                                            @if($v->isInHand())
                                                <span class="text text-info">{{$v->getStatusDesc()}}</span>
                                            @elseif($v->isFinish())
                                                <span class="text text-success">{{$v->getStatusDesc()}}</span>
                                            @else
                                                <span class="text text-danger">{{$v->getStatusDesc()}}</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if( $v->isDraft() )
                                                <a href="{{route('workflow.entry.edit',['id'=>$v->id])}}"
                                                   class="badge badge-info">编辑</a>
                                            @endif
                                            @if($v->isInHand())
                                                <a href='javascript:void(0);' data-href="{{route('workflow.entry.destroy',['id'=>$v->id])}}"
                                                   class="badge badge-danger cancel-my-workflow">撤销</a>
                                            @elseif($v->isDraft())
                                                <a href='javascript:void(0);' data-href="{{route('workflow.entry.destroy',['id'=>$v->id])}}"
                                                   class="badge badge-warning cancel-my-workflow">删除</a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <td colspan="5">暂无申请</td>
                            @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
@section('javascript')
    <script>
        $('.cancel-my-workflow').on('click', function () {
            callDeleteAjax($(this));
        })
    </script>
@endsection
