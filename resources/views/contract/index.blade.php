@extends('layouts.main')

@section('content')
    <header class="page-header">
        <div class="d-flex align-items-center">
            <div class="mr-auto">
                <h1 class="separator">合同管理</h1>
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
                            </select>
                            <select class="form-control form-search" name="flow">
                                <option value="">请选择流程</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary form-search">查询</button>
                            <button type="button" class="btn btn-info form-search clear-search">重置</button>
                        </div>
                    </div>
                </form>
                <a href="{{route('contract.create')}}" type="button" class="btn btn-info btn-blue">创建合同</a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-outline table-vcenter text-nowrap card-table">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>标题</th>
                            <th>作者</th>
                            <th>标签</th>
                            <th>内容</th>
                            <th>修改时间</th>
                            <th>开始时间</th>
                            <th>操作</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($contracts as $contract)
                            <tr>
                                <td>{{$contract->id}}</td>
                                <td>{{$contract->title}}</td>
                                <td>{{$contract->authorObj->chinese_name}}</td>
                                <td>{{$contract->tags}}</td>
                                <td>{{$contract->content}}</td>
                                <td>{{$contract->updated_at}}</td>
                                <td>{{$contract->created_at}}</td>
                                <td>
                                        <a href="{{route('contract.edit',['id' => $contract->id])}}"
                                           class="badge badge-info">编辑</a>
                                        <a href="{{route('contract.show', $contract->id)}}"
                                           class="badge badge-info">查看</a>
                                        <a href='javascript:void(0);' data-href="{{route('contract.destroy',['id'=>$contract->id])}}"
                                           class="badge badge-danger cancel-my-workflow">撤销</a>
                                        <a href='javascript:void(0);' data-href="{{route('contract.destroy',['id'=>$contract->id])}}"
                                           class="badge badge-warning cancel-my-workflow">删除</a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer">
            </div>
        </div>
    </section>
@endsection