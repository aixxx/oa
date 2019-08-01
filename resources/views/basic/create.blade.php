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
                        <li class="breadcrumb-item"><a href="{{route('position.index')}}?deptId={{$dept->id}}">职位管理</a></li>
                        <li class="breadcrumb-item active" aria-current="page">添加职位</li>
                    </ol>
                </nav>
            </div>
        </div>
    </header>
    <section class="page-content container-fluid">
        <div class="card">
            <form id="positionForm">
                {{csrf_field()}}
                <input type="hidden" id="deptId" name="deptId" value="{{$dept->id}}">
                <div class="form-body">
                    <div class="card-body">
                        <div class="form-group row">
                            <label class="control-label text-right col-md-3">部门名称</label>
                            <div class="col-md-5">
                                <input class="form-control" value="{{$dept->name}}" readonly />
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="control-label text-right col-md-3">名称</label>
                            <div class="col-md-5">
                                <input class="form-control" name="name" value="" placeholder="职位名称">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="control-label text-right col-md-3">是否为主管</label>
                            <div class="col-md-5">
                                <input name="is_leader" type="radio" value="0" checked> 普通职员
                                <input name="is_leader" type="radio" value="1"> 主管
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="control-label text-right col-md-3">角色</label>
                            <div class="col-md-8">
                                @foreach($roles as $m)
                                    <span style="display: block; padding-bottom: 6px;">
                                        <input class="checkbox_mo" name="roles[]" value="{{$m->id}}" type="checkbox" /> {{$m->title}}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </form>

            <div class="card-footer bg-light">
                <div class="row">
                    <div class="offset-sm-3 col-md-5">
                        <button type="submit" id="save" class="btn btn-primary">确定</button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script type="text/javascript">
        $("#save").click(function () {
            layer.load(3)
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: "POST",
                dataType: "json",
                data: $("#positionForm").serialize(),
                url: "{{ route('position.store') }}",
                success: function (response) {
                    if (response.status == 'success') {
                        window.location.href = "{{ route('position.index') }}?deptId={{$dept->id}}";
                    }else{
                        layer.close(layer.load(3));
                        layer.msg(response.message);
                    }
                },
                error: function (e) {
                    var json = (e.responseJSON);
                    for (var n in json['errors']) {
                        layer.close(layer.load(3));
                        layer.msg(json['errors'][n][0], {icon: 2});
                        //alert(json['errors'][n][0]);
                        //layer.msg(json['errors'][n][0])
                        return;
                    }
                }
            })
        });
    </script>
	 
@endsection

