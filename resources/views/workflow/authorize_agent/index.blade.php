@extends('layouts.main')

@section('content')
    <header class="page-header">
        <div class="d-flex align-items-center">
            <div class="mr-auto">
                <h1>授权管理</h1>
            </div>
            <div class="actions top-right">
                <a href="{{ route('workflow.authorize_agent.create') }}" class="btn btn-primary">
                    新授权
                </a>
            </div>
        </div>
    </header>
    <section class="page-content container-fluid">
        <div class="card">
            <div class="card-body">
                <table class="table">
                    <thead>
                    <tr>
                        <th>授权人姓名</th>
                        <th>授权生效时间</th>
                        <th>授权流程</th>
                        <th>代理人</th>
                        <th>状态</th>
                        <th>授权创建时间</th>
                        <th>操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($agents as $v)
                        <tr>
                            <td scope="row">{{$v->authorizer_user_name}}</td>
                            <td>
                                {{ $v->authorize_valid_begin }}&nbsp;~&nbsp;{{ $v->authorize_valid_end }}
                            </td>
                            <td>
                                {{ $v->flow? $v->flow->flow_name:'全流程有效' }}
                            </td>
                            <td>
                                {{ $v->agent_user_name }}
                            </td>
                            <td>
                                {!! $v->authorize_valid_begin < now() ?
                                 "<span class='badge badge-pill badge-success'>&nbsp;生效中&nbsp;</span>":
                                 "<span class='badge badge-pill badge-warning'>&nbsp;未生效&nbsp;</span>"  !!}
                            </td>
                            <td>{{$v->created_at}}</td>
                            <td>
                                <a href="javascript:;" data-href="{{route('workflow.authorize_agent.destroy',['id'=>$v->id])}}"
                                   class="badge badge-danger delete">删除</a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </section>
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
        $('.datepicker').datepicker({
            "autoclose": true,
            "format": "yyyy-mm-dd",
            "language": "zh-CN"
            // "startDate": "-3d"
        });
        $(function () {
            $(".delete").on('click', function () {
                callDeleteAjax($(this));
            })
        })
    </script>
@endsection