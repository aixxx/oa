@extends('layouts.main')
@section('content')

    <header class="page-header">
        <div class="d-flex align-items-center">
            <div class="mr-auto">
                <h1 class="separator">新建角色</h1>
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
                    <form class="form-horizontal" action="{{route('workflow.role.store')}}" method="POST">
                        {{csrf_field()}}
                        <h5 class="card-header">新角色</h5>
                        <div class="card-body">
                            <div class="form-group row">
                                <label class="control-label text-right col-md-3">角色名</label>
                                <div class="col-md-3">
                                    <input type="text" class="form-control z-index-fix" placeholder="" id="role_name" name="role_name"
                                           value="{{  old('role_name')  }}">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="control-label text-right col-md-3">公司</label>
                                <div class="col-md-3">
                                    {!! Form::select('company_id[]', $companyList, [], ['class' => 'form-control select2','multiple' => '']) !!}
                                </div>
                            </div>
                        </div>
                        <div class="card-footer bg-light">
                            <div class="row">
                                <div class="offset-sm-3 col-md-1">
                                    <button type="button" class="btn btn-secondary btn-rounded cancel">取消</button>
                                </div>
                                <div class="offset-sm-1 col-md-1">
                                    <button type="submit" class="btn btn-primary btn-rounded">确定</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
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
            </script>
@endsection