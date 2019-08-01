@extends('layouts.main')

@section('content')

    <header class="page-header">
        <div class="d-flex align-items-center">
            <div class="mr-auto">
                <h1 class="separator">编辑流程</h1>
                <nav class="breadcrumb-wrapper" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.html"><i class="icon dripicons-home"></i></a></li>
                        <li class="breadcrumb-item"><a href="javascript:void(0)">审批</a></li>
                        <li class="breadcrumb-item active" aria-current="page">流程设置</li>
                    </ol>
                </nav>
            </div>
        </div>
    </header>
    <section class="page-content container-fluid">
        <div class="row">
            <div class="col">
                <div class="card">
                    <form class="form-horizontal" action="{{ route('workflow.flow.update',['id'=>$flow->id]) }}"
                          method="POST">
                        {{csrf_field()}}
                        {{method_field('PUT')}}
                        <h5 class="card-header">编辑流程</h5>
                        <div class="card-body">
                            <div class="form-group row">
                                <label class="control-label text-right col-md-3">流程名</label>
                                <div class="col-md-5">
                                    <input type="text" class="form-control" name="flow_name"
                                           value="{{$flow->flow_name}}" placeholder="流程名称">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="control-label text-right col-md-3">流程编号</label>
                                <div class="col-md-5">
                                    <input type="text" class="form-control" name="flow_no" value="{{$flow->flow_no}}"
                                           placeholder="流程编号">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="control-label text-right col-md-3" for="template_id">当前模板</label>
                                <div class="col-md-5">
                                    <select class="form-control" name="template_id" id="template_id">
                                        <option value="0">无</option>
                                        @foreach($templates as $v)
                                            <option value="{{$v->id}}"
                                                    @if($v->id==$flow->template_id) selected="selected" @endif>{{$v->template_name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="control-label text-right col-md-3">流程分类</label>
                                <div class="col-md-5">
                                    <select class="form-control" name="type_id">
                                        <option value="0">无</option>
                                        @foreach($flow_types as $v)
                                            <option value="{{$v->id}}"
                                                    @if($v->id==$flow->type_id) selected="selected" @endif>{{$v->type_name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="control-label text-right col-md-3">领导审批条线</label>
                                <div class="col-md-5">
                                    <select class="form-control" name="leader_link_type">
                                        @foreach($flow_leader_links as $type => $des)
                                            <option value="{{$type}}"
                                                    @if($type == $flow->leader_link_type) selected="selected" @endif>{{$des}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="control-label text-right col-md-3">流程说明</label>
                                <div class="col-md-5"><textarea class="form-control" name="introduction"
                                                                placeholder="流程说明">{{$flow->introduction}}</textarea>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="control-label text-right col-md-3">是否显示</label>
                                <div class="col-md-5">
                                    <div class="form-check form-check-inline">
                                        <input type="radio" id="is_show_show" name="is_show" value="1"
                                               @if($flow->is_show==1) checked @endif  class="form-check-input">
                                        <label class="form-check-label" for="is_show_show">显示</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input type="radio" id="is_show_hide" name="is_show" value="0"
                                               @if($flow->is_show==0) checked @endif  class="form-check-input">
                                        <label class="form-check-label" for="is_show_hide">隐藏</label>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="control-label text-right col-md-3">icon</label>
                                <div class="col-md-5">
                                    <input type="text" class="form-control" name="icon_url" value="{{$flow->icon_url}}"
                                           placeholder="icon">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="control-label text-right col-md-3">前端路由</label>
                                <div class="col-md-5">
                                    <input type="text" class="form-control" name="route_url" value="{{$flow->route_url}}"
                                           placeholder="前端路由">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="control-label text-right col-md-3">前端展示路由</label>
                                <div class="col-md-5">
                                    <input type="text" class="form-control" name="show_route_url" value="{{$flow->show_route_url}}" placeholder="前端展示路由">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="control-label text-right col-md-3">部门白名单</label>
                                <div class="col-md-5">
                                    {!! Form::select('departments_ids[]', $departments, $can_view_departments, ['class' => 'form-control select2','multiple' => 'multiple']) !!}
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="control-label text-right col-md-3">人员白名单</label>
                                <div class="col-md-5">
                                    {!! Form::select('users_ids[]', $users, $can_view_users, ['class' => 'form-control select2','multiple' => 'multiple']) !!}
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="control-label text-right col-md-3">核心提示</label>
                                <div class="col-md-5" style="color:red;">
                                    白名单不选的话本流程对所有人和部门均可见
                                </div>
                            </div>
                        </div>
                        <div class="card-footer bg-light">
                            <div class="row">
                                <div class="offset-sm-3 col-md-5">
                                    <button class="btn btn-primary btn-rounded">确定</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('head')
    <link rel="stylesheet" href="/static/vendor/select2/select2.min.css">
@endsection

@section('javascript')
    <script src="/static/vendor/select2/select2.min.js"></script>
    <script>
        (function (window, document, $, undefined) {
            "use strict";
            $(function () {
                $(".select2").select2();
            });

        })(window, document, window.jQuery);
    </script>
@endsection
