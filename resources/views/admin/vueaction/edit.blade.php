@extends('layouts.admin')

@section('content')
    <header class="page-header">
        <div class="d-flex align-items-center">
            <div class="mr-auto">
                <h1 class="separator">权限编辑</h1>
                <nav class="breadcrumb-wrapper" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{url('/')}}"><i class="icon dripicons-home"></i></a></li>
                        <li class="breadcrumb-item"><a href="javascript:void(0)"></a>权限管理</li>
                        <li class="breadcrumb-item active" aria-current="page">权限编辑</li>
                    </ol>
                </nav>
            </div>
        </div>
    </header>
    <section class="page-content container-fluid">
        <div class="card">
            {{--<h5 class="card-header">编辑权限</h5>--}}
            <form action="{{ route('admin.vueaction.update', $ability->id) }}" method="POST" accept-charset="UTF-8" class="form-horizontal">
                {{csrf_field()}}
                {{method_field('PUT')}}
                <div class="form-body">
                    <div class="card-body">
                        <div class="form-group row">
                            <label class="control-label text-right col-md-3">名称</label>
                            <div class="col-md-5">
                                <input type="text" class="form-control" id="title" name="title" value="{{ $ability->title }}" placeholder="权限名称">
                                @if($errors->has('name'))
                                    <p class="help-block">
                                        {{ $errors->first('name') }}
                                    </p>
                                @endif
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="control-label text-right col-md-3">代码</label>
                            <div class="col-md-5">
                                <input type="text" class="form-control" id="name" name="name" value="{{ $ability->name }}" placeholder="权限代码">
                                @if($errors->has('name'))
                                    <p class="help-block">
                                        {{ $errors->first('name') }}
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="card-footer bg-light">
                        <div class="form-actions">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="offset-sm-3 col-md-5">
                                            <button class="btn btn-primary">确定</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </section>
@endsection
