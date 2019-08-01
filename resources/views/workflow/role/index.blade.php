@extends('layouts.main',['title' => '审批角色'])

@section('content')
    <header class="page-header">
        <div class="d-flex align-items-center">
            <div class="mr-auto">
                <h1>审批角色</h1>
                <nav class="breadcrumb-wrapper" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{url('/')}}"><i class="icon dripicons-home"></i></a></li>
                        <li class="breadcrumb-item active" aria-current="page">流程管理</li>
                        <li class="breadcrumb-item active" aria-current="page">审批角色</li>
                    </ol>
                </nav>
            </div>
            <div class="actions top-right">
                <a href="{{ route('workflow.role.create') }}" class="btn btn-primary">
                    新增角色
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
                        <th>负责公司</th>
                        <th>创建时间</th>
                        <th>更新时间</th>
                        <th>操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($roles as $v)
                        <tr>
                            <td scope="row">
                                <a target="_blank" href="{{route('workflow.role_user.index', ['role_id' => $v->id])}}">{{$v->role_name}}</a>
                            </td>
                            <td>
                                @if($v->company_id != '0')
                                    @php
                                        $companyIds = explode(',',$v->company_id);
                                        foreach ($companyIds as $c){
                                            echo $companyList[$c]??'';
                                            echo '<br />';
                                        }
                                    @endphp
                                @else
                                    全部
                                @endif
                            </td>
                            <td>
                                {{ $v->created_at }}
                            </td>
                            <td>
                                {{ $v->updated_at }}
                            </td>
                            <td>
                                <a href="javascript:;" data-href="{{route('workflow.role.destroy',['id'=>$v->id])}}"
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
                if (confirm('确定要删除这个角色吗？')) {
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
                }
            })
        })
    </script>
@endsection