@extends('layouts.admin')

@section('content')
    <header class="page-header">
        <div class="d-flex align-items-center">
            <div class="mr-auto">
                <h1 class="separator">管理员列表</h1>

                <nav class="breadcrumb-wrapper" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{url('/')}}"><i class="icon dripicons-home"></i></a></li>
                        <li class="breadcrumb-item"><a href="javascript:void(0)"></a>管理员管理</li>
                        <li class="breadcrumb-item active" aria-current="page">管理员列表</li>
                    </ol>
                </nav>
            </div>
        </div>
    </header>
    <section class="page-content container-fluid">
        <div class="card">
            <h3 class="card-header">
                <a href="{{ route('admin.users.create') }}" class="btn btn-primary">添加管理员</a>
            </h3>
            <div class="card-body p-0">
                <table class="table table-bordered table-striped {{ count($users) > 0 ? 'datatable' : '' }} dt-select">
                    <thead>
                    <tr>
                        <th class="p-l-20">姓名</th>
                        <th>英文名</th>
                        <th>唯一账号名</th>
                        <th>职位</th>
                        <th>角色</th>
                        <th>操作</th>
                    </tr>
                    </thead>
                    <tbody>

                    @if (count($users) > 0)
                        @foreach ($users as $user)
                            <tr data-entry-id="{{ $user->id }}">
                                <td><img class="align-self-center mr-3 ml-2 w-30 rounded-circle"
                                         src=" {{ $user->avatar }} " alt="">
                                    <a href="{{ route('admin.users.edit',[$user->id]) }}" class="bold">
                                        {{ $user->chinese_name }}
                                    </a>
                                </td>
                                <td>{{ $user->english_name }}</td>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->position }}</td>
                                <td>
                                    @foreach ($user->roles->pluck('name') as $role)
                                        <span class="badge badge-info badge-many">{{ $role }}</span>
                                    @endforeach
                                </td>
                                <td>
                                    <a href="{{ route('admin.users.edit',[$user->id]) }}"
                                       class="btn btn-sm btn-primary">编辑</a>
                                </td>

                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="9">暂无</td>
                        </tr>
                    @endif
                    </tbody>
                </table>
            </div>
            <div class="card-footer">
                    {!! $links !!}
            </div>
        </div>
    </section>
@endsection