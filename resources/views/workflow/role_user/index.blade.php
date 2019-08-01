@extends('layouts.main')

@section('content')
    <header class="page-header">
        <div class="d-flex align-items-center">
            <div class="mr-auto">
                <h1>工作流角色用户</h1>
            </div>
            <div class="actions top-right">
                <a href="{{ route('workflow.role_user.create', ['role_id' => $roleId]) }}" class="btn btn-primary">
                    新增用户
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
                        <th>角色名</th>
                        <th>用户名</th>
                        <th>添加人</th>
                        <th>操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($roleUsers as $v)
                        <tr>
                            <td scope="row">{{$v->role->role_name}}</td>
                            <td>
                                {{ $v->user_chinese_name }}
                            </td>
                            <td>
                                {{ $v->creater_user_chinese_name }}
                            </td>
                            <td>
                                <a href="javascript:;" data-href="{{route('workflow.role_user.destroy',['id'=>$v->id])}}"
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
        $(function () {
            $(".delete").on('click', function () {
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: "DELETE",
                    dataType: "json",
                    url: $(this).attr('data-href'),
                    data: {},
                    success: function (response) {
                        if (response.status == 'success') {
                            location.reload();
                        } else {
                            alert(response.message);
                        }
                        window.location.reload();
                    },
                    error: function (response) {
                        alert(response.responseJSON.message);
                    }
                })
            })
        })
    </script>
@endsection