@extends('layouts.main',['title' => '模板管理'])

@section('content')
    <header class="page-header">
        <div class="d-flex align-items-center">
            <div class="mr-auto">
                <h1>模板管理</h1>
                <nav class="breadcrumb-wrapper" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{url('/')}}"><i class="icon dripicons-home"></i></a></li>
                        <li class="breadcrumb-item active" aria-current="page">流程管理</li>
                        <li class="breadcrumb-item active" aria-current="page">模版管理</li>
                    </ol>
                </nav>
            </div>
            <div class="actions top-right">
                    <a href="{{ route('workflow.template.create') }}" class="btn btn-primary">
                        添加模板
                    </a>
            </div>
        </div>
    </header>
    <section class="page-content container-fluid">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                        <tr>
                            <th>模版名称</th>
                            <th>创建时间</th>
                            <th>更新时间</th>
                            <th>操作</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($templates as $v)
                            <tr>
                                <td><a href="{{route('workflow.template.edit',['id'=>$v->id])}}">{{$v->template_name}}</a></td>
                                <td>{{$v->created_at}}</td>
                                <td>{{$v->updated_at}}</td>
                                <td>
                                    <a href="{{route('workflow.template.edit',['id'=>$v->id])}}" class="btn btn-primary">编辑</a>
                                    <a href="javascript:;" data-href="{{route('workflow.template.destroy',['id'=>$v->id])}}" class="btn btn-danger delete">删除</a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
@endsection