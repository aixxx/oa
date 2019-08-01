@extends('layouts.main',['title' => '部门管理'])
@section('content')
    <header class="page-header">
        <div class="d-flex align-items-center">
            <div class="mr-auto">
                <h1 class="separator">部门管理</h1>
                <nav class="breadcrumb-wrapper" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{url('/')}}"><i class="icon dripicons-home"></i></a></li>
                        <li class="breadcrumb-item"><a href="javascript:void(0)"></a>部门管理</li>
                        {{--<li class="breadcrumb-item active" aria-current="page"></li>--}}
                    </ol>
                </nav>
            </div>
        </div>
    </header>
    <section class="page-content container-fluid">
        <h3 class="card-header">
            <div class="float-left">
                <form id="importForm" enctype="multipart/form-data" method="post">
                    <input class="btn btn-outline-danger btn-sm" name="inputImport" id="inputImport" type="file" multiple>
                    <a href="javascript:void(0)" class="btn btn-danger btn-sm" id="input-import">导入部门</a>
                    {{ csrf_field() }}
                </form>
            </div>
        </h3>
        <div class="center-content" style="width: 50%;">
            @component('components.tree', ['foo' => 'bar'])
                @slot('title')
                    公司部门
                @endslot
            @endcomponent
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
    <!--删除部门-->
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
    <!--编辑部门-->
    <div class="modal fade" id="editDepartModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="exampleModalLabel">编辑部门</h4>
                    <button type="button" class="close" style="color: #0b0603;" data-dismiss="modal" aria-label="Close">
                        &times;
                    </button>
                </div>
                <div class="modal-body">
                    <form action="" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="recipient-name" class="control-label">部门名称</label>
                            <input type="text" class="form-control" name="department_name" value=""
                                   id="edit_department_name">
                        </div>
                        <div class="form-group">
                            <label for="recipient-name" class="control-label">上级部门</label>
                            <input type="text" name="department_parent" class="form-control" id="edit_department_parent" disabled>
                        </div>
                        <div class="form-group">
                            <label for="recipient-name" class="control-label">排序</label>
                            <input type="text" class="form-control" name="department_order" value=""
                                   id="edit_department_order">
                        </div>
                        <div class="form-group">
                            <label for="recipient-name" class="control-label">是否要同步到企业微信&nbsp;<span
                                        style="color:red">*</span></label>
                            <select class="form-control" name="is_sync_wechat" value="" id="edit_is_sync_wechat" disabled>
                                <option value="">请选择</option>
                                <option value="1">是</option>
                                <option value="0">否</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default btn-sm line-height-fix" data-dismiss="modal">取消
                    </button>
                    <button type="button" class="btn btn-primary btn-sm line-height-fix" id="edit_save">保存</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('javascript')
    <script>
        store_success = "{{  session('storeSuccess')  }}";
        if(store_success)
        {
            alert(store_success);
            delete store_success;
        }
    </script>
@endsection