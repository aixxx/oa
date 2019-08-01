@extends('layouts.admin')

@section('content')
    <header class="page-header">
        <div class="d-flex align-items-center">
            <div class="mr-auto">
                <h1 class="separator">角色列表</h1>
                <nav class="breadcrumb-wrapper" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{url('/')}}"><i class="icon dripicons-home"></i></a></li>
                        <li class="breadcrumb-item"><a href="javascript:void(0)"></a>角色管理</li>
                        <li class="breadcrumb-item active" aria-current="page">角色列表</li>
                    </ol>
                </nav>
            </div>
        </div>
    </header>
    <section class="page-content container-fluid">
        <div class="card">
            <h3 class="card-header">
                <a href="{{ route('admin.apiroles.create') }}" class="btn btn-primary">添加角色</a>
            </h3>
            <div class="card-body table-responsive">
                <table class="table table-bordered table-striped {{ count($roles) > 0 ? 'datatable' : '' }} dt-select">
                    <thead>
                    <tr>
                        <th style="width:48px;text-align:center;">ID</th>
                        <th>名称</th>
                        <th>权限</th>
                        <th>操作</th>
                    </tr>
                    </thead>

                    <tbody>
                    @if (count($roles) > 0)
                        @foreach ($roles as $role)
                            <tr data-entry-id="{{ $role->id }}">
                                <td>{{ $role->id }}</td>
                                <td>{{ $role->title }}</td>
                                <td>
                                    @foreach ($role->belongsToManyVueAction as $action)
                                        <span style="margin-top: 10px;" class="badge badge-info badge-many" data-toggle="tooltip" data-placement="top"
                                              title="{{ $action->vue_path }}">{{ $action->title }}</span>
                                    @endforeach
                                </td>
                                <td>
                                    <a href="{{ route('admin.apiroles.edit',[$role->id]) }}" class="btn btn-sm btn-primary">编辑</a>
                                    <a style="" href="javascript:;" data-href="{{ route('admin.apiroles.destroy',[$role->id]) }}"
                                       class="btn btn-sm badge badge-danger delete">删除</a>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="6">@lang('global.app_no_entries_in_table')</td>
                        </tr>
                    @endif
                    </tbody>
                </table>
            </div>
            <div class="card-footer">
                @if(isset($searchData) and count($searchData))
                    {!! $roles->appends($searchData)->links() !!}
                @else
                    {!! $roles->links() !!}
                @endif
            </div>
        </div>

        <script>
            $(function () {
                $(".delete").on('click', function () {

                    if (!confirm('确定要删除这个角色吗？')) {
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