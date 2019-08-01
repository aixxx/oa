@extends('layouts.main')
@section('content')

    <header class="page-header">
        <div class="d-flex align-items-center">
            <div class="mr-auto">
                <h1 class="separator">新建角色用户</h1>
                <nav class="breadcrumb-wrapper" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.html"><i class="icon dripicons-home"></i></a></li>
                    </ol>
                </nav>
            </div>
        </div>
    </header>
    <section class="page-content container-fluid">
        <div class="row">
            <div class="col">
                <div class="card">
                    <form class="form-horizontal" action="{{route('workflow.role_user.store')}}" method="POST">
                        {{csrf_field()}}
                        <h5 class="card-header">新建角色用户</h5>
                        <div class="card-body">

                            <div class="form-group row">
                                <label class="control-label text-right col-md-3">角色名</label>
                                <div class="col-md-3">
                                    <select name="role_id" class="form-control">
                                        @foreach($rolesMap as $roleId => $roleName)
                                            <option value="{{$roleId}}" @if($roleId == $roleIdSelected) selected @endif>{{$roleName}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="control-label text-right col-md-3">用户</label>
                                <div class="col-md-3">
                                    <select name="user_id" class="js-data-example-ajax form-control select_user_name">
                                        <option value="">请选择</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer bg-light">
                            <div class="row">
                                <div class="offset-sm-3 col-md-1">
                                    <button class="btn btn-secondary btn-rounded cancel">取消</button>
                                </div>
                                <div class="offset-sm-1 col-md-1">
                                    <button type="submit" class="btn btn-primary btn-rounded">确定</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
@endsection

@section('javascript')
    <!-- ================== DATEPICKER SCRIPTS ==================-->
    <script src="/static/vendor/moment/min/moment.min.js"></script>
    <script src="/static/vendor/bootstrap-datepicker/bootstrap-datepicker.min.js"></script>
    <script src="/static/vendor/bootstrap-daterangepicker/daterangepicker.js"></script>
    <script src="/static/vendor/sweetalert2/dist/sweetalert2.min.js"></script>
    <script src="/static/js/components/bootstrap-datepicker-init.js"></script>
    <script src="/static/js/components/bootstrap-date-range-picker-init.js"></script>
    <script>
        $(".cancel").click(function (e) {
            e.preventDefault();
            window.history.go(-1);
        })
    </script>
@endsection