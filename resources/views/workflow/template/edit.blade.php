@extends('layouts.main',['title' => '编辑模板'])

@section('content')
    <header class="page-header">
        <div class="d-flex align-items-center">
            <div class="mr-auto">
                <h1>编辑模板</h1>
            </div>
        </div>
    </header>
    <section class="page-content container-fluid">
        <div class="card">
            <div class="card-body">

                <div class="row">
                    <div class="col-md-12 col-lg-8">
                            <div class="card-header">模板设置
                                <ul class="actions top-right">
                                    <li><a href="{{route('workflow.template_form.create',['template_id'=>$template->id])}}" class="badge badge-info">
                                            + 添加控件
                                        </a>
                                    </li>
                                </ul>
                            </div>
                            <div class="card-body">
                                <table class="table">
                                    <thead>
                                    <tr>
                                        <th>名称</th>
                                        <th>placeholder</th>
                                        <th>字段名</th>
                                        <th>字段类型</th>
                                        <th>排序</th>
                                        <th>是否必填</th>
                                        <th>操作</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($forms as $v)
                                        <tr>
                                            <td>{{$v->field_name}}</td>
                                            <td>{{$v->placeholder}}</td>
                                            <td>{{$v->field}}</td>
                                            <td>{{$v->field_type}}</td>
                                            <td>{{$v->sort}}</td>
                                            <td>@php echo $v->required?'是':'否'; @endphp</td>
                                            <td>
                                                <a href="{{route('workflow.template_form.edit',['id'=>$v->id])}}" class="badge badge-info">编辑</a>
                                                <a href="javascript:;" data-href="{{route('workflow.template_form.destroy',['id'=>$v->id])}}" class="badge badge-danger delete">删除</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>

                    </div>
                    <div class="col-md-12 col-lg-4">
                            <form action="{{route('workflow.template.update',['id'=>$template->id])}}" method="POST">
                                <div class="card-body">
                                    {{csrf_field()}}
                                    {{method_field('PUT')}}
                                    <div class="form-group">
                                        <label>模板名称</label>
                                        <input type="text" class="form-control"  name="template_name" placeholder="模板名称" value="{{$template->template_name}}">
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <button type="submit" class="btn btn-primary">保存</button>
                                </div>
                            </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection

@section('javascript')
    <script>
        $(function () {
            $(".delete").on('click', function () {
                callDeleteAjax($(this));
            })
        })
    </script>
@endsection