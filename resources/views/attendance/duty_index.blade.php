@extends('layouts.main',['title' => '班值管理'])
<!-- ======================= PAGE LEVEL VENDOR STYLES ========================-->
<link rel="stylesheet" href="/static/vendor/bootstrap-datepicker/bootstrap-datepicker.min.css">
<link rel="stylesheet" href="/static/vendor/bootstrap-daterangepicker/daterangepicker.css">
@section('content')

    <header class="page-header">
        <div class="d-flex align-items-center">
            <div class="mr-auto">
                <h1 class="separator">班值管理</h1>
                <nav class="breadcrumb-wrapper" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{url('/')}}"><i class="icon dripicons-home"></i></a></li>
                        <li class="breadcrumb-item"><a href="javascript:void(0)"></a>班值管理</li>
                        {{--<li class="breadcrumb-item active" aria-current="page"></li>--}}
                    </ol>
                </nav>
            </div>
        </div>
    </header>
    <section class="page-content container-fluid">
        <div class="card">
            <h3 class="card-header">当前拥有班值({{  $totalnum     }}个)
                <a href="{{  route('attendance.duty.create')  }}" class="btn btn-primary btn-sm line-height-fix float-right">添加</a>
            </h3>
            <div class="table-responsive">
                <table class="table table-hover table-outline table-vcenter text-nowrap card-table">
                    <thead>
                    <tr>
                        <th>班值代码</th>
                        <th>班值名称</th>
                        <th>所属类型</th>
                        <th>上班时间</th>
                        <th>下班时间</th>
                        <th>休息开始时间</th>
                        <th>休息结束时间</th>
                        <th>班次/日</th>
                        <th>创建人</th>
                        <th class="text-center"><i class="icon-settings"></i>操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($duties as $duty)
                            <tr id="duty_{{  $duty->class_id  }}">
                                <td>
                                    <div>{{  $duty->class_title  }}</div>
                                </td>
                                <td>
                                    <div>{{  $duty->class_name  }}</div>
                                </td>
                                <td>
                                    <div class="float-left">
                                        <div>{{  isset(\App\Models\Attendance\AttendanceWorkClass::CLASS_TYPE[$duty->type]) ? \App\Models\Attendance\AttendanceWorkClass::CLASS_TYPE[$duty->type] : ''  }}</div>
                                    </div>
                                </td>
                                <td>
                                    <div class="float-left">
                                        <div>{{  $duty->class_begin_at  }}</div>
                                    </div>
                                </td>
                                <td>
                                    <div class="float-left">
                                        <div>{{  $duty->class_end_at  }}</div>
                                    </div>
                                </td>
                                <td>
                                    <div class="float-left">
                                        <div>{{  $duty->class_rest_begin_at  }}</div>
                                    </div>
                                </td>
                                <td>
                                    <div class="float-left">
                                        <div>{{  $duty->class_rest_end_at  }}</div>
                                    </div>
                                </td>
                                <td>
                                    <div class="float-left">
                                        <div>{{  $duty->class_times  }}</div>
                                    </div>
                                </td>
                                <td>
                                    <div class="float-left">
                                        {{  \App\Models\User::findById($duty->class_create_user_id)->chinese_name }}
                                    </div>
                                </td>

                                <td class="text-center">
                                    <div class="item-action dropdown">
                                        <a href="javascript:void(0)" class="btn btn-fab " data-toggle="dropdown" aria-expanded="false" style="padding-top: 0px;">
                                            <i class="icon dripicons-dots-3 rotate-90 font-size-24"></i>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-right">
                                            {{--<a href="{{  route('duty.show',['id'=> $duty->class_id])  }}" class="dropdown-item"><i class="icon dripicons-view-list"></i> 详情</a>--}}
                                            {{--<div class="dropdown-divider"></div>--}}
                                            <a href="{{  route('attendance.duty.edit',['id'=> $duty->class_id])  }}" class="dropdown-item"><i class="icon dripicons-pencil"></i> 编辑</a>
                                            <div class="dropdown-divider"></div>
                                            <a href="javascript:void(0)" class="dropdown-item simple_del" data-deleteid="{{ $duty->class_id }}" data-target="#delModal"><i class="icon dripicons-trash"></i> 删除</a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            <div class="card-footer">
                {!! $duties->links() !!}
            </div>
        </div>
    </section>

    <div class="modal fade" id="delModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">提示</h4>
                    <button type="button" class="close" style="color: #0b0603;" data-dismiss="modal" aria-label="Close">&times;</button>
                </div>
                <div class="modal-body">
                    <p>是否删除所选班值</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                    <button type="button" class="btn btn-danger" id="del_dept">确定</button>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div>
@endsection

@section('javascript')
    <script>
        single_del = $(".simple_del");
        single_del.click(function () {
            $("#delModal").modal('show');
            let del_single_id = $(this).data('deleteid');
            let del_dept_single = $("#del_dept");
            del_dept_single.off("click").on('click', function () {
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: "{{  route('attendance.duty.delete')  }}",
                    data: {'id': del_single_id},
                    method: 'post',
                    dataType: "json",
                    success: function (response) {
                        if (response.status == 'success') {
                            $("#duty_" + del_single_id).remove();
                            $("#delModal").modal('hide');
                            alert(response.messages);
                        } else if (response.status == 'failed') {
                            $("#delModal").modal('hide');
                            alert(response.messages);
                        }
                    }
                });
            });
        });
    </script>
@endsection