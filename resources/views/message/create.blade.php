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
                        <li class="breadcrumb-item active" aria-current="page">新建</li>
                    </ol>
                </nav>
            </div>
        </div>
    </header>
    <section class="page-content container-fluid">
        @include('message._form', ['model' => $model, 'title' => '新建消息模板'])
    </section>
@endsection