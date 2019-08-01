@extends('layouts.admin')
@section('content')
    <header class="page-header">
        <div class="d-flex align-items-center">
            <div class="mr-auto">
                <h1 class="separator">OA基础分类选项</h1>
                <nav class="breadcrumb-wrapper" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{url('/')}}"><i class="icon dripicons-home"></i></a></li>
                        <li class="breadcrumb-item"><a href="javascript:void(0)"></a>OA基础分类选项</li>
                        <li class="breadcrumb-item active" aria-current="page">OA基础分类选项-列表</li>
                    </ol>
                </nav>
            </div>
        </div>
    </header>
    <section class="page-content container-fluid">
        <div class="card">
            <h3 class="card-header">
            <a href="{{ route('admin.abilities.create') }}" class="btn btn-primary">添加基础选项</a>
           </h3>
            <div class="card-body table-responsive">
                <table class="table table-bordered table-striped {{ count($abilities) > 0 ? 'datatable' : '' }} dt-select">
                    <thead>
                    <tr>
                        <th style="text-align:center;">类型编号</th>
                        <th>类型名称</th>
                        <th>状态</th>
						<th>时间</th>
                        <th>操作</th>
                    </tr>
                    </thead>

                    <tbody>
                    @if ($abilities)
                        @foreach ($abilities as $ability)
                            <tr data-entry-id="{{ $ability->id }}">
                                <td>{{Q($ability,'code')}}</td>
                                <td>{{ $ability->title }}</td>
                                <td>@if($ability->status==1)启用 @else 停用 @endif</td>
								<td>{{Q($ability,'created_at')}}</td>
                                <td>
                                    <a href="{{ route('admin.abilities.edit',[$ability->id]) }}" class="btn btn-sm btn-primary">编辑</a>
                                    {!! Form::open(array(
                                        'style' => 'display: inline-block;',
                                        'method' => 'DELETE',
                                        'onsubmit' => "return confirm('确定要删除？');",
                                        'route' => ['admin.abilities.destroy', $ability->id])) !!}
                                    {!! Form::submit('删除', array('class' => 'btn btn-sm btn-danger')) !!}
                                    {!! Form::close() !!}
                                </td>

                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="5">暂无数据</td>
                        </tr>
                    @endif
                    </tbody>
                </table>
            </div>
        </div>
@endsection