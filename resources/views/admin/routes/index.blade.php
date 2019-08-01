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
            <a href="{{ route('admin.routes.create') }}" class="btn btn-primary">添加基础选项</a>
           </h3>
            <div class="card-body table-responsive">
                <table class="table table-bordered table-striped {{ count($routes) > 0 ? 'datatable' : '' }} dt-select">
                    <thead>
                    <tr>
                        <th style="text-align:center;">路由编号</th>
                        <th>路由名称</th>
						<th>时间</th>
                        <th>操作</th>
                    </tr>
                    </thead>

                    <tbody>
                    @if ($routes)
                        @foreach ($routes as $route)
                            <tr data-entry-id="{{ $route->id }}">
                                <td>{{Q($route,'path')}}</td>
                                <td>{{Q($route,'title')}}</td>
								<td>{{Q($route,'created_at')}}</td>
                                <td>
                                    <a href="{{ route('admin.routes.edit',[$route->id]) }}" class="btn btn-sm btn-primary">编辑</a>
                                        <a style="" href="javascript:;" data-href="{{ route('admin.routes.destroy',[$route->id]) }}"
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

            <div class="card-footer">
                @if(isset($searchData) and count($searchData))
                    {!! $routes->appends($searchData)->links() !!}
                @else
                    {!! $routes->links() !!}
                @endif
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