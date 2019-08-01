@extends('layouts.main', ['title' => '消息模板'])

@section('content')
    <header class="page-header">
        <div class="d-flex align-items-center">
            <div class="mr-auto">
                <h1 class="separator">系统管理</h1>
                <nav class="breadcrumb-wrapper" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{url('/')}}"><i class="icon dripicons-home"></i></a></li>
                        <li class="breadcrumb-item"><a href="javascript:void(0)">系统管理</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('message.template.index') }}">消息模板</a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{ $model->template_name }}</li>
                    </ol>
                </nav>
            </div>
        </div>
    </header>
    <section class="page-content container-fluid">
        <style>
            .no-padding {
                padding: 0;
            }
            .table-striped > tbody > tr:nth-of-type(odd) {
                background-color: #f9f9f9;
            }
            .table th {
                word-break: keep-all;
            }
            .btn-flat {
                 -webkit-box-shadow: none;
                 -moz-box-shadow: none;
                 box-shadow: none;
                 border-radius: 0;
             }
        </style>
        <div class="card card-pills">
            <div class="card-header">
                <div class="card-title">消息模板 - {{ $model->template_name }}</div>
                <a href="javascript:void(0);" onclick="postDelete($(this));" data-href="{{ route('message.template.delete', ['id' => $model->template_id]) }}" data-confirm="确认删除?" data-method="post">
                    {{ Form::button('删除', [
                        'class' => 'btn btn-sm btn-accent btn-flat m-r-10 btn-outline float-right'
                    ]) }}
                </a>
                <a href="{{ route('message.template.update', ['id' => $model->template_id]) }}">{{ Form::button('编辑', [
                    'class' => 'btn btn-sm btn-info btn-flat m-r-10 btn-outline float-right'
                ]) }}</a>
                <a href="{{ route('message.template.create') }}">{{ Form::button('新增', [
                    'class' => 'btn btn-sm btn-success btn-flat m-r-10 btn-outline float-right'
                ]) }}</a>
                <a href="{{ route('message.template.index') }}">{{ Form::button('返回', [
                    'class' => 'btn btn-sm btn-secondary btn-flat m-r-10 btn-outline float-right'
                ]) }}</a>
            </div>
            <div class="card-body no-padding">
                <table class="table table-striped table-bordered detail-view">
                    <tr><th>模板键值</th><td>{{ $model->template_key }}</td></tr>
                    <tr><th>名称</th><td>{{ $model->template_name }}</td></tr>
                    <tr><th>签名</th><td>【{{ $model->template_sign }}】</td></tr>
                    <tr><th>类型</th><td>{{ App\Constant\CommonConstant::MESSAGE_TYPE_MAPPING[$model->template_type] }}</td></tr>
                    <tr><th>推送方式</th><td>{{ App\Constant\CommonConstant::MESSAGE_PUSH_TYPE_MAPPING[$model->template_push_type] }}</td></tr>
                    <tr><th>模板标题</th><td>{{ $model->template_title }}</td></tr>
                    <tr><th>模板内容</th><td>{{ $model->template_content }}</td></tr>
                    <tr><th>备注</th><td>{{ $model->template_memo }}</td></tr>
                    <tr><th>状态</th><td>{{ App\Constant\CommonConstant::STATUS_MAPPING[$model->template_status] }}</td></tr>
                    <tr><th>创建用户</th><td>{{ $model->createdUser->chinese_name }}</td></tr>
                    <tr><th>更新用户</th><td>{{ $model->updatedUser->chinese_name }}</td></tr>
                    <tr><th>创建时间</th><td>{{ $model->template_created_at }}</td></tr>
                    <tr><th>更新时间</th><td>{{ $model->template_updated_at }}</td></tr>
                </table>
            </div>
        </div>
    </section>

@endsection

@section('javascript')
    @include('message._js')
@endsection