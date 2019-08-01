@extends("layouts.main",['title' => '部门员工'])

@section('content')
    <header class="page-header">
        <div class="d-flex align-items-center">
            <div class="mr-auto">
                <h1 class="separator">通讯录</h1>
                <nav class="breadcrumb-wrapper" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{url('/')}}"><i class="icon dripicons-home"></i></a></li>
                        <li class="breadcrumb-item"><a href="javascript:void(0)"></a>部门管理</li>
                        <li class="breadcrumb-item active" aria-current="page">通讯录</li>
                    </ol>
                </nav>
            </div>
        </div>
    </header>
    <section class="page-content container-fluid">
        <div class="row">
            {{--<div class="center-content" style="width: 25%;">--}}
                {{--@component('components.tree', ['foo' => 'bar'])--}}
                    {{--@slot('title')--}}
                        {{--公司部门--}}
                    {{--@endslot--}}

                {{--@endcomponent--}}
            {{--</div>--}}

            <aside class="aside aside-left flex-fill">
                <div class="row row-cards row-deck" style="height: 100%;">
                    <div class="col-12">
                        <div class="card" style="height: 100%;">
                            <h3 class="card-header font-size-20">{{  $curDept->name  }}({{  $totalnum     }}人/{{ $primarynum }}人)
                                <a href="{{  route('users.create')  }}" class="btn btn-primary btn-sm line-height-fix float-right">添加</a>
                            </h3>
                            <div class="table-responsive" style="min-height: 500px;">
                                <table class="table table-hover table-outline table-vcenter text-nowrap card-table">
                                    <thead>
                                    <tr>
                                        {{--<th class="text-center">--}}
                                            {{--<label class="custom-control custom-checkbox">--}}
                                                {{--<input type="checkbox" class="custom-control-input" name="example-checkbox1" value="option1">--}}
                                                {{--<span class="custom-control-label"></span>--}}
                                            {{--</label>--}}
                                        {{--</th>--}}
                                        <th>姓名</th>
                                        <th>职务</th>
                                        <th>主部门</th>
                                        <th>部门</th>
                                        <th>操作</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @if ($users->items())
                                        @foreach($users as $user)
                                            @if($user->user)
                                                <tr id="user_{{  $user->user->id  }}">
                                                    {{--<td>--}}
                                                        {{--<label class="custom-control custom-checkbox">--}}
                                                            {{--<input type="checkbox" class="custom-control-input" name="users[]" value="{{  $user->user->id  }}">--}}
                                                            {{--<span class="custom-control-label"></span>--}}
                                                        {{--</label>--}}
                                                    {{--</td>--}}
                                                    <td>
                                                        <a href="{{  route('users.show',['id' => $user->user->id])  }}">{{  $user->user->chinese_name  }}</a>
                                                        <div class="small text-muted">
                                                            {{--Registered: Apr 11, 2018--}}
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="clearfix">
                                                            <div class="float-left">
                                                                <div>{{  $user->user->position  }}</div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <span data-toggle="tooltip" title="{{  $user->pri_path  }} " data-placement="bottom">
                                                            {{  $user->pri_name  }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <ul style="list-style: none;padding-left: 0;">
                                                            @php
                                                                if ($userInfo[$user->user->id]['name']) {
                                                                    foreach ($userInfo[$user->user->id]['name'] as $key => $value)
                                                                    {
                                                                        if ($userInfo[$user->user->id]['is_leader'][$key])
                                                                        {
                                                                            $dept_str = '<li><i class="fa fa-user" data-toggle="tooltip" title="" data-original-title="fa fa-user"></i>'.'<span data-toggle="tooltip" title="'.$userInfo[$user->user->id]['path'][$key].'" data-placement="bottom">'.$value.'</span></li>';
                                                                        } else {
                                                                            $dept_str = '<li>'.'<span data-toggle="tooltip" title="'.$userInfo[$user->user->id]['path'][$key].'" data-placement="bottom">'.$value.'</span></li>';
                                                                        }
                                                                        echo $dept_str;
                                                                    }
                                                                }
                                                            @endphp
                                                        </ul>
                                                    </td>

                                                    <td class="dropdown ">
                                                        <a href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                            <i class="icon dripicons-dots-3 rotate-90 font-size-20"></i>
                                                        </a>
                                                        <div class="dropdown-menu dropdown-menu-right">
                                                            <a class="dropdown-item" href="{{  route('users.edit',['id' => $user->user->id])  }}"><i class="la la-edit font-size-20"></i> 编辑</a>
                                                            <div class="dropdown-divider"></div>
                                                            <a class="dropdown-item" href="{{  route('users.show',['id' => $user->user->id])  }}"><i class="la la-info font-size-20"></i>详情</a>
                                                            <div class="dropdown-divider"></div>
                                                            <a href="javascript:void(0)" class="dropdown-item simple_del" data-deleteId="{{ $user->user->id }}" data-target="#delUserModal"><i class="zmdi zmdi-delete zmdi-hc-fw font-size-20"></i>离职</a>
                                                        </div>
                                                    </td>
                                                    {{--<td class="text-center">--}}
                                                    {{--<div class="item-action dropdown">--}}
                                                    {{--<a href="javascript:void(0)" data-toggle="dropdown" class="icon"><i class="fe fe-more-vertical"></i></a>--}}
                                                    {{--<div class="dropdown-menu dropdown-menu-right">--}}
                                                    {{--<a href="{{  route('users.edit',['id' => $user->user->id])  }}" class="dropdown-item"><i class="dropdown-icon fe fe-tag"></i>编辑</a>--}}
                                                    {{--<div class="dropdown-divider"></div>--}}
                                                    {{--<a href="{{  route('users.show',['id' => $user->user->id])  }}" class="dropdown-item"><i class="dropdown-icon fe fe-tag"></i>详情</a>--}}
                                                    {{--<div class="dropdown-divider"></div>--}}
                                                    {{--<a href="javascript:void(0)" class="dropdown-item simple_del" data-deleteId="{{ $user->user->id }}" data-target="#delUserModal"><i class="dropdown-icon fe fe-edit-2"></i>离职</a>--}}
                                                    {{--</div>--}}
                                                    {{--</div>--}}
                                                    {{--</td>--}}
                                                </tr>


                                            @endif
                                        @endforeach
                                    @else
                                        <tr><td colspan="8" class="text-center">部门没有员工</td></tr>
                                    @endif
                                    </tbody>
                                </table>
                            </div>
                            <div class="card-footer">
                                @if(isset($params) and count($params))
                                    {!! $users->appends($params)->links() !!}
                                @else
                                    {!! $users->links() !!}
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </aside>
        </div>
    </section>
        <!--添加部门-->
        <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="exampleModalLabel">新建部门</h4>
                        <button type="button" class="close" style="color: #0b0603;" data-dismiss="modal" aria-label="Close">&times;</button>
                    </div>
                    <div class="modal-body">
                        <form action="" method="POST">
                            @csrf
                            <div class="form-group">
                                <label for="recipient-name" class="control-label">部门名称</label>
                                <input type="text" class="form-control" name="department_name" value="" id="department_name">
                            </div>
                            <div class="form-group">
                                <label for="recipient-name" class="control-label">排序</label>
                                <input type="text" class="form-control" name="department_order" value="" id="department_order">
                            </div>
                            <div class="form-group">
                                <label for="recipient-name" class="control-label">是否要同步到企业微信</label>
                                <select  class="form-control" name="is_sync_wechat" value="" id="add_is_sync_wechat">
                                    <option value="1" selected>是</option>
                                    <option value="0">否</option>
                                </select>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default btn-sm line-height-fix" data-dismiss="modal">取消</button>
                        <button type="button" class="btn btn-primary btn-sm line-height-fix" id="save">保存</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="delModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">提示</h4>
                        <button type="button" class="close" style="color: #0b0603;" data-dismiss="modal" aria-label="Close">&times;</button>
                    </div>
                    <div class="modal-body">
                        <p>是否删除所选部门</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default btn-sm line-height-fix" data-dismiss="modal">取消</button>
                        <button type="button" class="btn btn-danger btn-sm line-height-fix" id="del_dept">确定</button>
                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div>

        <div class="modal fade" id="delUserModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">提示</h4>
                        <button type="button" class="close" style="color: #0b0603;" data-dismiss="modal" aria-label="Close">&times;</button>
                    </div>
                    <div class="modal-body">
                        <p>是否让所选员工离职</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default btn-sm line-height-fix" data-dismiss="modal">取消</button>
                        <button type="button" class="btn btn-danger btn-sm line-height-fix" id="del_user">确定</button>
                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div>
        <script src="/static/vendor/jquery/dist/jquery.min.js"></script>
        <script>
            //离职单个员工
            simple_del = $(".simple_del");
            simple_del.off('click').click(function () {
                simple_del_id = $(this).attr('data-deleteId');
                if(simple_del_id)
                {
                    $("#delUserModal").modal('show');
                    let del_user = $("#del_user");
                    del_user.click(function () {
                        $.ajax({
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            type: "POST",
                            dataType:"json",
                            url: "{{  route('users.delete')  }}",
                            data: { 'ids' : simple_del_id },
                            success: function (response) {
                                if(response.status == 'success')
                                {
                                    $("#delUserModal").modal('hide');
                                    let tr_node = $("#user_"+simple_del_id);
                                    tr_node.remove();
                                    alert(response.messages);
                                    let children_node = $("tbody").children();
                                    if(children_node.length == 0)
                                    {
                                        let none_employee = '<tr><td colspan="8" class="text-center">部门没有员工</td></tr>';
                                        $("tbody").append(none_employee);
                                    }
                                } else if(response.status == 'failed') {
                                    alert(response.messages);
                                }
                            }
                        })
                    })
                }

            });


            //删除部门
            $('#delModal').on('show.bs.modal', function (event) {
                var button = $(event.relatedTarget);
                deptId = button.data('department');
            })

            del_dept = $("#del_dept");

            del_dept.click(function(){
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: "POST",
                    dataType:"json",
                    url: "{{  route('departments.delete')  }}",
                    data: {  'deptId' : deptId },
                    success:function (response) {
                        if(response.status == 'success')
                        {
                            $('#delModal').modal('hide');
                            window.location.href= "{{  route("dept.user")  }}";
                        }

                        if(response.status == 'error')
                        {
                            alert(response.messages);
                        }

                        if(response.status == 'failed')
                        {
                            alert(response.messages);
                        }

                    }
                })
            });

        </script>
@endsection