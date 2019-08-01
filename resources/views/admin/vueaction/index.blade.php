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
            <a href="{{ route('admin.vueaction.create') }}" class="btn btn-primary">添加基础选项</a>
           </h3>
            <div class="card-body table-responsive">
                <table class="table table-bordered table-striped {{ count($vueAction) > 0 ? 'datatable' : '' }} dt-select">
                    <thead>
                    <tr>
                        <th style="text-align:center;">路由编号</th>
                        <th>路由名称</th>
                        <th>接口地址</th>
						<th>时间</th>
                        <th>操作</th>
                    </tr>
                    </thead>

                    <tbody>
                    @if ($vueAction)
                        @foreach ($vueAction as $action)
                            <tr data-entry-id="{{ $action->id }}">
                                <td>{{Q($action,'vue_path')}}</td>
                                <td>{{Q($action,'title')}}</td>
                                <td>
                                    @foreach(Q($action,'belongsToManyRotes') as $routes)
                                        <span style="margin-top: 10px;" class="badge badge-info badge-many" data-toggle="tooltip" data-placement="top"
                                              title="{{ $routes->path }}">{{ $routes->title }}</span>
                                    @endforeach
                                </td>
								<td>{{Q($vueAction,'created_at')}}</td>
                                <td>
                                    @if(!$action->parent_id)
                                    <a href="{{ route('admin.vueaction.create',['parentId'=>$action->id]) }}" class="btn btn-sm btn-primary">添加子路由</a>
                                    @endif
                                    <a href="{{ route('admin.vueaction.edit',[$action->id]) }}" class="btn btn-sm btn-primary">编辑</a>
                                        <a style="" href="javascript:;" data-href="{{ route('admin.vueaction.destroy',[$action->id]) }}"
                                           class="btn btn-sm badge badge-danger delete">删除</a>
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
        <script>
            $(function () {
                $(".delete").on('click', function () {

                    if (!confirm('确定要删除？')) {
                        return;
                    }

                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        type: "DELETE",
                        dataType: "json",
                        url: $(this).attr('data-href'),
                        data: {},
                        success: function (response) {
                            alert(response.message);
                            if (response.status == 'success') {
                                location.reload();
                            }
                        }
                    })
                })
            })
        </script>
@endsection