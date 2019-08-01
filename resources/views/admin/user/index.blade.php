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
            <div class="card-body table-responsive">
                <table class="table table-bordered table-striped {{ count($result) > 0 ? 'datatable' : '' }} dt-select">
                    <thead>
                    <tr>
                        <th style="text-align:center;">ID</th>
                        <th>姓名</th>
                        <th>操作</th>
                    </tr>
                    </thead>

                    <tbody>
                    @if ($result)
                        @foreach ($result as $user)
                            <tr data-entry-id="{{ $user->id }}">
                                <td>{{Q($user,'id')}}</td>
								<td>{{Q($user,'chinese_name')}}</td>
                                <td>
                                    <a href="{{ route('admin.user.edit',[$user->id]) }}" class="btn btn-sm btn-primary">角色编辑</a>
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
                    {!! $result->appends($searchData)->links() !!}
                @else
                    {!! $result->links() !!}
                @endif
            </div>
        </div>
@endsection