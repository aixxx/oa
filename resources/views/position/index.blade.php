@extends('layouts.main',['title' => '职位管理'])
@section('content')
    <header class="page-header">
        <div class="d-flex align-items-center">
            <div class="mr-auto">
                <h1 class="separator">部门管理</h1>
                <nav class="breadcrumb-wrapper" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{url('/')}}"><i class="icon dripicons-home"></i></a></li>
                        <li class="breadcrumb-item"><a href="{{ url('/deptuser/depart') }}">部门管理</a></li>
                        <li class="breadcrumb-item active" aria-current="page">职位管理</li>
                    </ol>
                </nav>
            </div>
        </div>
    </header>
    <section class="page-content container-fluid">
        <div class="card">
            <h3 class="card-header">
                <a href="{{ route('position.create', ['deptId'=> $dept->id]) }}" class="btn btn-primary">添加职位</a>
            </h3>
            <div class="card-body table-responsive">
                <table class="table table-bordered table-striped dt-select">
                    <thead>
                    <tr>
                        <th style="text-align:center;">职位编号</th>
                        <th>部门</th>
                        <th>职位名称</th>
                        <th>角色</th>
                        <th>操作</th>
                    </tr>
                    </thead>

                    <tbody>
                    @if ($position)
                        @foreach ($position as $action)
                            <tr data-entry-id="{{Q($action,'id')}}">
                                <td style="text-align:center;">{{Q($action,'id')}}</td>
                                <td>{{$dept->name}}</td>
                                <td>{{Q($action,'name')}}</td>
                                <td>
                                    @foreach(Q($action,'belongsToManyRoles') as $routes)
                                        <span style="margin-top: 10px;" class="badge badge-info badge-many"
                                              data-toggle="tooltip" data-placement="top">{{ $routes->title }}</span>
                                    @endforeach
                                </td>
                                <td>
                                    <a href="{{ route('position.edit',[$action->id]) }}" class="btn btn-sm btn-primary">编辑</a>
                                    <a href="javascript:poisionDel({{$action->id}});" class="btn btn-sm btn-danger" data-actionId="{{$action->id}}">删除</a>
                                    {{--{!! Form::open(array(
                                        'style' => 'display: inline-block;',
                                        'method' => 'DELETE',
                                        'onsubmit' => "return confirm('确定要删除？');",
                                        'route' => ['position.destroy', '?deptId='.$deptId."&id=". $action->id])) !!}
                                    {!! Form::submit('删除', array('class' => 'btn btn-sm btn-danger')) !!}
                                    {!! Form::close() !!}--}}
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
        </div>
    </section>

    <script>
        function poisionDel(id) {
            layer.confirm("确定删除该职位?",function (index) {
                layer.close(index);
                layer.load(3);
                $.ajax({
                    type: "GET",
                    dataType: "json",
                    data: {
                        'id': id,
                        'deptId': "{{$dept->id}}"
                    },
                    url: "{{  route('position.delete')  }}",
                    success: function (response) {
                        if (response.status == 'success') {
                            window.location.href = "{{ route('position.index') }}?deptId={{$dept->id}}";
                        }else {
                            layer.close(layer.load(3));
                            layer.msg(response.message);
                        }
                    },
                    error: function (e) {
                        var json = (e.responseJSON);
                        for (var n in json['errors']) {
                            layer.close(layer.load(3));
                            layer.msg(json['errors'][n][0]);
                            //layer.msg(json['errors'][n][0])
                            return;
                        }
                    }
                })
            });
        }
    </script>
@endsection