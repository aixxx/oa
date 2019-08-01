@extends('layouts.admin')
@section('content')
@section('javascript')
    <script>
        function checkAll(num,flag) {
            var vname = 'name_item_' + num;
            var v = flag?true:false;
            v?$("#un_name_all_").attr("checked",false ):$("#name_all_").attr("checked",false );;
            $("input[name='" + vname + "[]']:checkbox").attr("checked",v );
        }
    </script>
@endsection
<header class="page-header">
    <div class="d-flex align-items-center">
        <div class="mr-auto">
            <h1 class="separator">角色权限设置</h1>
            <nav class="breadcrumb-wrapper" aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{url('/')}}"><i class="icon dripicons-home"></i></a></li>
                    <li class="breadcrumb-item"><a href="javascript:void(0)"></a>角色管理</li>
                    <li class="breadcrumb-item active" aria-current="page">角色权限设置</li>
                </ol>
            </nav>
        </div>
    </div>
</header>
<section class="page-content container-fluid">
    <div class="card">
        {{--<h3 class="card-header">角色的权限设置</h3>--}}
        <div class="card-body">
            <form id="roleForm">
                {{csrf_field()}}
                <div class="form-body">
                    <div class="form-group row">
                        <label class="control-label text-right col-md-3">名称</label>
                        <div class="col-md-5">
                            <input type="text" name="title" class="form-control" value="{{ $role->title }}">
                            <p class="help-block"></p>
                            @if($errors->has('title'))
                                <p class="help-block">
                                    {{ $errors->first('title') }}
                                </p>
                            @endif
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="control-label text-right col-md-3">代码</label>
                        <div class="col-md-5">
                            <input type="text" name="name" placeholder="请遵循规则： HR_USER_MGT" class="form-control"
                                   value="{{ $role->name }}">
                            <p class="help-block"></p>
                            @if($errors->has('name'))
                                <p class="help-block">
                                    {{ $errors->first('name') }}
                                </p>
                            @endif
                        </div>
                    </div>
                    <hr class="dashed">

                    <div class="form-group row">
                        <label class="control-label text-right col-md-3">权限</label>
                        <div class="col-md-5">
                            <?php
                            if (!empty($abilities)) {
                                foreach ($menu as $m) {
                                    echo "<fieldset>";
                                    echo "<input onclick='checkAll(" . $m['no'] . ",1)'  type='checkbox' id='name_all_" . $m['no'] . "' >&nbsp;";
                                    echo "<label for='name_all_" . $m['no'] . "'> 全选</label>&nbsp;&nbsp;";

                                    echo "<input onclick='checkAll(" . $m['no'] . ",0)'  type='checkbox' id='un_name_all_" . $m['no'] . "' >&nbsp;";
                                    echo "<label for='un_name_all_" . $m['no'] . "'> 全不选</label>&nbsp;&nbsp;";

                                    echo "<legend>" . $m['title'] . "</legend>";

                                    $count = 0;
                                    foreach ($abilities as $a) {

                                        $checked = in_array($a['id'],$ids)?'checked':'';

                                        if ($a['level1_no'] == $m['no']) {
                                            $count++;
                                            echo "<input $checked value='" . $a['id'] . "' type='checkbox' name='name_item_" . $m['no'] . "[]' id='item_" .
                                                $a['id'] . "'>&nbsp;";
                                            echo "<label for='item_" . $a['id'] . "'>" . $a['title'] .
                                                "</label>&nbsp;&nbsp;";
                                            echo ($count % 3) == 0 ? '<br>' : '';
                                        }
                                    }
                                    echo "</fieldset>";
                                }
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </form>
            <div class="card-footer bg-light">
                <div class="form-actions">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="row">
                                <div class="offset-sm-3 col-md-5">
                                    <button id="save" class="btn btn-primary">确定</button>
                                </div>
                            </div>
                        </div>
                    </div>
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
            type: "PUT",
            dataType: "json",
            data: $("#roleForm").serialize(),
            url: "{{ route('admin.roles.update',['id'=>$role->id]) }}",
            success: function (response) {
                alert(response.message);
                if (response.status == 'success') {
                    window.location.href = "{{ route('admin.roles.index') }}";
                }
            }
        })
    });
</script>
@endsection

