@extends("layouts.main",['title' => '花名册'])

@section('content')
    <header class="page-header">
        <div class="d-flex align-items-center">
            <div class="mr-auto">
                <h1 class="separator">花名册</h1>
                <nav class="breadcrumb-wrapper" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{url('/')}}"><i class="icon dripicons-home"></i></a></li>
                        <li class="breadcrumb-item"><a href="javascript:void(0)"></a>员工管理</li>
                        <li class="breadcrumb-item active" aria-current="page">花名册</li>
                    </ol>
                </nav>
            </div>
        </div>
    </header>
    <section class="page-content container-fluid">
        <div class="card">
            <h3 class="card-header">
                {{--<div class="float-left">--}}
                    {{--<form id="importForm" enctype="multipart/form-data" method="post">--}}
                        {{--<input class="btn btn-outline-danger btn-sm" name="inputImport" id="inputImport" type="file" multiple>--}}
                        {{--<a href="javascript:void(0)" class="btn btn-danger btn-sm" id="input-import">导入</a>--}}
                        {{--{{ csrf_field() }}--}}
                    {{--</form>--}}
                {{--</div>--}}
                <div class="float-right">
                    @if(isset($searchData) and count($searchData))
                        <a href="{{  route('users.index')  }}" class="btn btn-secondary btn-sm line-height-fix">返回</a>
                    @endif
                    <a href="javascript:void(0)" class="btn btn-accent btn-sm" id="search">搜索</a>
                    <a href="{{  route('users.create')  }}" class="btn btn-primary  btn-sm line-height-fix">添加</a>
                    {{--<a href="javascript:void(0)" class="btn btn-danger btn-sm line-height-fix" id="delete">离职</a>--}}
                </div>
            </h3>
            <div class="table-responsive">
                <table class="table table-hover table-outline table-vcenter text-nowrap card-table">
                    <thead>
                    <tr>
                        <th>姓名</th>
                        <th>职务</th>
                        <th>状态</th>
                    </tr>
                    </thead>
                    <tbody>
                    @if(count($users))
                    @foreach($users as $user)
                        <tr id="user_{{  $user->id  }}">
                            <td>
                                <a href="{{route('users.show',['id' => $user->id])}}"> {{$user->chinese_name}} </a>
                            </td>
                            <td>
                                <div class="clearfix">
                                    <div class="float-left">
                                        <div>{{  $user->position  }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div>
                                    @if($user->status == 1)
                                        在职
                                    @else($user->status == 9)
                                        离职
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    @else
                        <tr>
                            <td colspan="7" class="text-center">
                                未找到相关用户
                            </td>
                        </tr>

                    @endif
                    </tbody>
                </table>
            </div>
            <div class="card-footer">
                @if(isset($searchData) and count($searchData))
                    {!! $users->appends($searchData)->links() !!}
                @else
                    {!! $users->links() !!}
                @endif
            </div>
        </div>
    </section>

        <div class="modal fade" id="delUserModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">提示</h4>
                        <button type="button" class="close" style="color: #0b0603;" data-dismiss="modal" aria-label="Close">&times;</button>
                    </div>
                    <div class="modal-body">
                        <p>是否离职所选员工</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default btn-sm line-height-fix" data-dismiss="modal">取消</button>
                        <button type="button" class="btn btn-danger btn-sm line-height-fix" id="del_user">确定</button>
                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div>

        <!-- 搜索弹窗 -->
        <div class="modal fade" id="searchModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel">
            <form action="{{  route('users.search')  }}" method="post">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="exampleModalLabel">员工搜索</h4>
                        <button type="button" class="close" style="color: #0b0603;" data-dismiss="modal" aria-label="Close">&times;</button>
                    </div>
                    <div class="modal-body">
                        <form action="" method="POST">
                            @csrf
                            {{--<div class="form-group">--}}
                                {{--<label for="recipient-name" class="control-label">企业微信名</label>--}}
                                {{--<input type="text" class="form-control" name="name" value="">--}}
                            {{--</div>--}}
                            <div class="form-group">
                                <label for="recipient-name" class="control-label">中文名</label>
                                <input type="text" class="form-control" name="chinese_name" value="">
                            </div>
                            <div class="form-group">
                                <label for="recipient-name" class="control-label">英文名</label>
                                <input type="text" class="form-control" name="english_name" value="">
                            </div>
                            <div class="form-group">
                                <label for="recipient-name" class="control-label">手机号</label>
                                <input type="text" class="form-control" name="mobile" value="">
                            </div>
                            {{--<div class="form-group">--}}
                                {{--<label for="recipient-name" class="control-label">员工编号</label>--}}
                                {{--<input type="text" class="form-control" name="employee_num" value="">--}}
                            {{--</div>--}}

                            {{--<div class="form-group">--}}
                                {{--<label for="recipient-name" class="control-label">邮箱</label>--}}
                                {{--<input type="text" class="form-control" name="email" value="">--}}
                            {{--</div>--}}
                            {{--<div class="form-group">--}}
                                {{--<label for="recipient-name" class="control-label">职位</label>--}}
                                {{--<input type="text" class="form-control" name="position" value="">--}}
                            {{--</div>--}}
                            {{--<div class="form-group">--}}
                                {{--<label for="recipient-name" class="control-label">是否要同步到企业微信</label>--}}
                                {{--<select  class="form-control" name="is_sync_wechat" value="">--}}
                                    {{--<option value="" selected>请选择</option>--}}
                                    {{--<option value="1">是</option>--}}
                                    {{--<option value="0">否</option>--}}
                                {{--</select>--}}
                            {{--</div>--}}
                            {{--<div class="form-group">--}}
                                {{--<label for="recipient-name" class="control-label">离职</label>--}}
                                {{--<select  class="form-control" name="status" value="">--}}
                                    {{--<option value="" selected>请选择</option>--}}
                                    {{--<option value="9">是</option>--}}
                                    {{--<option value="1">否</option>--}}
                                {{--</select>--}}
                            {{--</div>--}}
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default btn-sm line-height-fix" data-dismiss="modal">取消</button>
                        <button type="submit" class="btn btn-primary btn-sm line-height-fix">提交</button>
                    </div>
                </div>
            </div>
            </form>
        </div>
@endsection
@section('javascript')
    <script>
        edit_error = "{{  session('editError')  }}";

        if(edit_error)
        {
            alert(edit_error);
        }

        store_success = "{{  session('storeSuccess')  }}";
        if(store_success){
            alert(store_success);
            delete store_success;
        }
    </script>
    <script>
        //删除单个员工
        simple_del = $(".simple_del");

        simple_del.one("click",function () {
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
                                alert(response.messages);

                                window.location.href = "/users/"+simple_del_id +"/dimission_create";
                                // window.location.reload();
                            } else if(response.status == 'failed') {
                                alert(response.messages);
                            }
                        }
                    })
                })
            }
        });

        //员工筛选
        user_search = $("#search");
        user_search.click(function () {
            $("#searchModal").modal('show');
        });
    </script>
    <script>
        $('#input-import').click(function () {
            var formData = new FormData();
            var name = $("#inputImport").val();
            formData.append("inputImport",$("#inputImport")[0].files[0]);
            formData.append("name",name);

            layer.confirm("确定导入文件中的数据?",function (index) {
                //layer.close(index);
                //layer.load(3);
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: "POST",
                    dataType: "json",
                    //data: $('#input-import').serialize(),
                    data: formData,
                    url: "",//route('users.batch_import')
                    processData : false,
                    contentType : false,
                    success: function (response) {
                        if (response.status == 'success') {
                            window.location.href = "{{ route('users.index') }}";
                        }else {
                            layer.close(layer.load(3));
                            layer.msg(response.message);
                        }
                    },
                    error: function (e) {
                        var json = (e.responseJSON);
                        for (var n in json['errors']) {
                            layer.close(layer.load(3));
                            layer.msg(json['errors'][n][0]);
                            //layer.msg(json['errors'][n][0])
                            return;
                        }
                    }
                })
            });
        });
    </script>
@endsection
