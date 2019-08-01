@extends('layouts.main')
@section('content')
    <header class="page-header">
        <div class="d-flex align-items-center">
            <div class="mr-auto">
                <h1 class="separator">审核首页</h1>
                <nav class="breadcrumb-wrapper" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{url('/')}}"><i class="icon dripicons-home"></i></a></li>
                        <li class="breadcrumb-item"><a href="javascript:void(0)"></a>审核管理</li>
                        <li class="breadcrumb-item active" aria-current="page">首页</li>
                    </ol>
                </nav>
            </div>
        </div>
    </header>
    <section class="page-content container-fluid">
        <div class="card">
            <div class="card-body demo-buttons--previe">
                @if (count($flows) > 0)
                    @foreach ($flows as $flow)
                        <a href="{{ route('workflow.approval.flow',[$flow->id]) }}"
                           class="btn btn-sm btn-primary">{{ $flow->flow_name }}</a>

                    @endforeach
                @else
                    尚无已发布的流程
                @endif
            </div>
    </div>
@endsection