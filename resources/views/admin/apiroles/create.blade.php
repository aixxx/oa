@extends('layouts.admin')
@section('content')
@section('javascript')
    <script>
        function checkAll(num,flag) {
            var vname = 'name_item_' + num;
            var v = flag?true:false;
            v?$("#un_name_all_").attr("checked",false ):$("#name_all_").attr("checked",false );;

            $(".name_item_" + num + ":checkbox").attr("checked",v );
        }
    </script>
@endsection
<header class="page-header">
    <div class="d-flex align-items-center">
        <div class="mr-auto">
            <h1 class="separator">角色添加</h1>
            <nav class="breadcrumb-wrapper" aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{url('/')}}"><i class="icon dripicons-home"></i></a></li>
                    <li class="breadcrumb-item"><a href="javascript:void(0)"></a>角色</li>
                    <li class="breadcrumb-item active" aria-current="page">角色添加</li>
                </ol>
            </nav>
        </div>
    </div>
</header>
<section class="page-content container-fluid">
    <div class="card">
        <form id="roleForm">
            {{csrf_field()}}
            <div class="form-body">
                <div class="card-body">
                    <div class="form-group row">
                        <label class="control-label text-right col-md-3">名称</label>
                        <div class="col-md-5">
                            <input class="form-control" id="title" name="title"
                                   value="{{ old('title') }}" placeholder="角色名称">
                            @if($errors->has('title'))
                                <p class="help-block">
                                    {{ $errors->first('title') }}
                                </p>
                            @endif
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="control-label text-right col-md-3">权限</label>
                        <div class="col-md-5">
                            @php
                                if (!empty($roles)) {
                                    foreach ($roles as $role) {
                                        echo "<fieldset>";
                                        echo "<input name='name_all_" . $role['id'] . "' onclick='checkAll(" . $role['id'] . ",1)'  type='radio' id='name_all_" . $role['id'] . "' >&nbsp;";
                                        echo "<label for='name_all_" . $role['id'] . "'> 全选</label>&nbsp;&nbsp;";

                                        echo "<input name='name_all_" . $role['id'] . "' onclick='checkAll(" . $role['id'] . ",0)'  type='radio' id='un_name_all_" . $role['id'] . "' >&nbsp;";
                                        echo "<label for='un_name_all_" . $role['id'] . "'> 全不选</label>&nbsp;&nbsp;";

                                        echo "<legend><input value='" . $role['id'] . "' class='name_item_" . $role['id'] . "' type='checkbox' name='name_item[]' id='item_" .
                                                    $role['id'] . "'>&nbsp;<label for='item_" . $role['id'] . "'>" . $role['title'] .
                                                    "</label></legend>";
                                        $count = 0;
                                        foreach ($role->hasManyChildren as $val) {
                                                $count++;
                                                echo "<input value='" . $val['id'] . "' class='name_item_" . $role['id'] . "' type='checkbox' name='name_item[]' id='item_" .
                                                    $val['id'] . "'>&nbsp;";
                                                echo "<label for='item_" . $val['id'] . "'>" . $val['title'] .
                                                    "</label>&nbsp;&nbsp;";
                                                echo ($count % 4) == 0 ? '<br>' : '';
                                        }
                                        echo "</fieldset>";
                                    }
                                }
                            @endphp
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <div class="card-footer bg-light">
            <div class="row">
                <div class="offset-sm-3 col-md-5">
                    <button id="save" class="btn btn-primary">确定</button>
                </div>
            </div>
        </div>
    </div>
</section>

<script type="text/javascript">
    $("#save").click(function () {
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type: "POST",
            dataType: "json",
            data: $("#roleForm").serialize(),
            url: "{{ route('admin.apiroles.store') }}",
            success: function (response) {
                alert(response.message);
                if (response.status == 'success') {
                    window.location.href = "{{ route('admin.apiroles.index') }}";
                }
            }
        })
    });
</script>

@endsection
