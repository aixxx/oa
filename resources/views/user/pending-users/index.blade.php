@extends('layouts.main',['title' => '待入职员工'])

@section('content')
    <header class="page-header">
        <div class="d-flex align-items-center">
            <div class="mr-auto">
                <h1 class="separator">待入职列表</h1>
                <nav class="breadcrumb-wrapper" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{url('/')}}"><i class="icon dripicons-home"></i></a></li>
                        <li class="breadcrumb-item"><a href="javascript:void(0)"></a>待入职管理</li>
                        <li class="breadcrumb-item active" aria-current="page">待入职列表</li>
                    </ol>
                </nav>
            </div>
        </div>
    </header>
    <section class="page-content container-fluid">
        <div class="card">
            <h3 class="card-header">待入职员工数【{{  $totalnum  }}】
                <a href="{{  route('pendingusers.create')  }}" class="btn btn-primary btn-sm line-height-fix float-right" style="margin-left: auto" id="add">添加</a>
            </h3>
            <div class="table-responsive" style="min-height: 500px;">
                <table class="table table-hover table-outline table-vcenter text-nowrap card-table">
                    <thead>
                    <tr>
                        <th>姓名</th>
                        <th>邮箱</th>
                        <th>公司</th>
                        <th>添加时间</th>
                        {{--<th class="text-center"><i class="icon-settings"></i>操作</th>--}}
                    </tr>
                    </thead>
                    <tbody>
                    @if(count($pendingUsers))
                        @foreach($pendingUsers as $user)
                            <tr id="user_{{  $user->id  }}">
                                <td>
                                    <div>{{  $user->chinese_name  }}</div>
                                    <div class="small text-muted">
                                    </div>
                                </td>

                                <td>
                                    <div>{{  $user->email  }}</div>
                                    <div class="small text-muted">
                                    </div>
                                </td>
                                <td>
                                    <div>{{  $user->company->name  }}</div>
                                    <div class="small text-muted">
                                    </div>
                                </td>
                                <td>
                                    <div>{{  $user->created_at }}</div>
                                    <div class="small text-muted">
                                    </div>
                                </td>
                                <td class="text-center">
                                    <div class="item-action dropdown">
                                        <a href="javascript:void(0)" class="btn btn-fab " data-toggle="dropdown" aria-expanded="false" style="padding-top: 0px;">
                                            <i class="icon dripicons-dots-3 rotate-90 font-size-24"></i>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-right">
                                            {{--<a href="{{  route('pendingusers.join',['id' => $user->id])  }}" class="dropdown-item" data-joinId="{{ $user->id }}"><i class="icon la la-user-plus font-size-5 v-align-text-bottom"></i> 入职</a>--}}
                                            {{--<div class="dropdown-divider"></div>--}}
                                            {{--<a href="{{  route('pendingusers.show',['id' => $user->id])  }}" class="dropdown-item"><i class="icon dripicons-view-list"></i> 详情</a>--}}
                                            <div class="dropdown-divider"></div>
                                            <a href="{{  route('pendingusers.edit', ['id' => $user->id])  }}" class="dropdown-item edit" data-editId="{{ $user->id }}" data-target="#editModal"><i class="icon dripicons-pencil"></i> 编辑</a>
                                            <div class="dropdown-divider"></div>
                                            <a href="javascript:void(0)" class="dropdown-item simple_del" data-deleteId="{{ $user->id }}" data-target="#delModal"><i class="icon dripicons-trash"></i> 删除</a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="11" class="text-center">
                                未找到相关记录
                            </td>
                        </tr>

                    @endif
                    </tbody>
                </table>
            </div>
            <div class="card-footer">
                {!! $pendingUsers->links() !!}
            </div>
        </div>
    </section>

    <!-- 删除弹窗 -->
        <div class="modal fade" id="delModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">提示</h4>
                        <button type="button" class="close" style="color: #0b0603;" data-dismiss="modal" aria-label="Close">&times;</button>
                    </div>
                    <div class="modal-body">
                        <p>是否删除所选待入职员工</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default btn-sm line-height-fix" data-dismiss="modal">取消</button>
                        <button type="button" class="btn btn-danger btn-sm line-height-fix" id="del_dept">确定</button>
                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div>
@endsection

@section("javascript")
    <script>
        //删除单个人员
        simple_del = $(".simple_del");

        simple_del.click(function () {
            simple_del_id = $(this).attr('data-deleteId');
            if (simple_del_id) {
                $("#delModal").modal('show');
                let del_depart = $("#del_dept");
                del_depart.click(function () {
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        type: "POST",
                        dataType: "json",
                        url: "{{  route('pendingusers.delete')  }}",
                        data: {'id': simple_del_id},
                        success: function (response) {
                            if (response.status == 'success') {
                                $("#delModal").modal('hide');
                                let tr_node = $("#user_" + simple_del_id);
                                tr_node.remove();
                                alert(response.messages);
                            } else {
                                alert(response.messages);
                            }
                        }
                    })
                })
            }
        });
    </script>
@endsection