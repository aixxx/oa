@extends('layouts.main',['title' => '基础设置'])
@section('content')
    <header class="page-header">
        <div class="d-flex align-items-center">
            <div class="mr-auto">
                <h1 class="separator">基础设置</h1>
                <nav class="breadcrumb-wrapper " aria-label="breadcrumb" id="positionForm">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{url('/')}}"><i class="icon dripicons-home"></i></a></li>
                        <li class="breadcrumb-item"><a href="{{ url('/Basic/index') }}">系统设置</a></li>
                        <li class="breadcrumb-item active" aria-current="page">基础设置</li>
                    </ol>
                </nav>
            </div>
        </div>
    </header>
    <section class="page-content container-fluid">
        <div class="card">
            <form  id="positionForm" accept-charset="UTF-8" class="form-horizontal">
                {{csrf_field()}}
                <div class="card-body table-responsive">
                    <div class="form-group">
                        <label>系统名称</label>
                        <input type="text" class="form-control" name="name" id="name" placeholder="后台系统名称" value="{{$result->website_name}}">
                    </div>

                    <div class="form-group">
                        <label>欢迎词</label>
                        <input type="text" class="form-control" name="greetings"  id='greetings' placeholder="登录页欢迎词"  value="{{$result->login_greetings}}">
                    </div>

                    <div class="text-left">
                        <input type="hidden" class="form-control" name="id"  id='id' value="{{$result->id}}">
                        <button type="button" class="btn btn-primary " id="save">保存</button>
                    </div>
                </div>
            </form>
        </div>
    </section>
    <script type="text/javascript">
        $("#save").click(function () {
            layer.load(3);
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: "POST",
                dataType: "json",
                data: {name:$("#name").val(),greetings:$("#greetings").val(),id:$("#id").val()},
                url: "{{ route('basic.update') }}",
                success: function (response) {
                    console.log($("#positionForm").serialize());
                    console.log(response);
                    if (response.success == 1) {
                        window.location.href = "{{ route('basic.index') }}";
                    }else {
                        layer.close(layer.load(3));
                        layer.msg(response.message);
                    }
                },
                error: function (e) {
                    layer.close(layer.load(3));
                    var json = (e.responseJSON);
                    for (var n in json['errors']) {
                        layer.msg(json['errors'][n][0]);
                        return;
                    }
                }
            })
        });
    </script>

@endsection