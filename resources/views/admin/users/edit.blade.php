@extends('layouts.admin')

@section('content')

    <header class="page-header">
        <div class="d-flex align-items-center">
            <div class="mr-auto">
                <h1 class="separator">用户角色设置</h1>
                <nav class="breadcrumb-wrapper" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{url('/')}}"><i class="icon dripicons-home"></i></a></li>
                        <li class="breadcrumb-item"><a href="javascript:void(0)"></a>管理员管理</li>
                        <li class="breadcrumb-item active" aria-current="page">用户角色设置</li>
                    </ol>
                </nav>
            </div>
        </div>
    </header>
    <section class="page-content container-fluid">
        <div class="card">
            {{--<h5 class="card-header">用户角色设置</h5>--}}
            <form id="roleForm">
                {{csrf_field()}}
                <div class="card-body">
                    <div class="form-body">
                        <div class="form-group row">
                            <label class="control-label text-right col-md-3">唯一账号名</label>
                            <div class="col-md-5">
                                {{ $user->name }}
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="roles" class="control-label text-right col-md-3">角色</label>
                            <div class="col-md-5">
                                {!! Form::select('roles[]', $roles, old('roles') ? old('role') : $user->roles()->pluck('name', 'name'), ['class' => 'form-control select2', 'multiple' => 'multiple']) !!}
                            </div>
                        </div>
                    </div>
                </div>
            </form>
            <div class="card-footer">
                <div class="form-actions">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="row">
                                <div class="offset-sm-3 col-md-5">
                                    <button id="save" class="btn btn-primary">提交</button>
                                    <button class="btn btn-secondary btn-outline cancel">取消</button>
                                </div>
                            </div>
                        </div>
                    </div>
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
        $(function () {
            $(".cancel").on('click', function () {
                window.location.href = "javascript:history.go(-1)";
            })
        });

        $("#save").click(function () {
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: "PUT",
                dataType: "json",
                data: $("#roleForm").serialize(),
                url: "{{ route('admin.users.update',['id'=>$user->id]) }}",
                success: function (response) {
                    alert(response.message);
                    if (response.status == 'success') {
                        window.location.href = "{{ route('admin.users.index') }}";
                    }
                }
            })
        });

    </script>
@endsection

