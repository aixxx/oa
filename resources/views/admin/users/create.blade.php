@extends('layouts.admin')

@section('content')
    <section class="page-content container-fluid">
        <div class="card">
            <h5 class="card-header">管理员角色设置</h5>
            <div class="card-body">
                <form id="roleForm" class="form-horizontal">
                    {{csrf_field()}}
                    <div class="form-body">
                        <div class="form-group row">
                            <label class="control-label text-right col-md-3">唯一账号名</label>
                            <div class="col-md-5">
                                {!! Form::select('user_id', $users, old('user_id'), ['class' => 'form-control select2', 'required' => '']) !!}
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="control-label text-right col-md-3">角色</label>
                            <div class="col-md-5">
                                {!! Form::select('roles[]', $roles, old('roles'), ['class' => 'form-control select2', 'multiple' => 'multiple', 'required' => '']) !!}
                            </div>
                        </div>
                    </div>
                </form>
                <div class="card-footer bg-light">
                    <div class="row">
                        <div class="offset-sm-3 col-md-5">
                            <button id="save" class="btn btn-primary">确定</button>
                            <button class="btn btn-secondary clear-form btn-outline cancel">取消</button>
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
                type: "POST",
                dataType: "json",
                data: $("#roleForm").serialize(),
                url: "{{ route('admin.users.store') }}",
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