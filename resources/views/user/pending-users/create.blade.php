@extends('layouts.main',['title' => '添加待入职员工'])

@section('content')
    <header class="page-header">
        <div class="d-flex align-items-center">
            <div class="mr-auto">
                <h1 class="separator">待入职添加</h1>
                <nav class="breadcrumb-wrapper" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{url('/')}}"><i class="icon dripicons-home"></i></a></li>
                        <li class="breadcrumb-item"><a href="javascript:void(0)"></a>待入职管理</li>
                        <li class="breadcrumb-item active" aria-current="page">待入职添加</li>
                    </ol>
                </nav>
            </div>
        </div>
    </header>
    <section class="page-content container-fluid">
        <div class="card">
            {{--<div class="card-header">--}}
            {{--<h3 class="card-title">添加待入职员工</h3>--}}
            {{--</div>--}}
            <div class="card-body">
                <form action="{{  route('pendingusers.store')  }}" method="POST">
                    @csrf
                    <div class="form-group">
                        @if($errors->get('email'))
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach($errors->get('email') as $error)
                                        <li>{{  $error  }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        <div class="form-inline">
                            <label class="form-label">邮箱<span style="color:red">*</span>：</label>
                            <input type="text" name="email" value="" class="form-control col-md-3">(唯一的,不可更改)
                        </div>
                    </div>

                    <div class="form-group">
                        @if($errors->get('chinese_name'))
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach($errors->get('chinese_name') as $error)
                                        <li>{{  $error  }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        <div class="form-inline">
                            <label class="form-label">姓名<span style="color:red">*</span>：</label>
                            <input type="text" name="chinese_name" class="form-control col-md-3"
                                   value="{{  old('chinese_name')  }}">
                        </div>
                    </div>

                    <div class="form-group">
                        @if($errors->get('password'))
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach($errors->get('password') as $error)
                                        <li>{{  $error  }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        <div class="form-inline">
                            <label class="form-label">密码<span style="color:red">*</span>：</label>
                            <input type="text" name="password" class="form-control col-md-3" value="">
                        </div>
                    </div>
                    <div class="form-group">
                        @if($errors->get('company_id'))
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach($errors->get('company_id') as $error)
                                        <li>{{  $error  }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        <div class="form-inline">
                            <label class="col-form-label">所属公司</label><label class="text-danger col-form-label">*</label>
                            <select name="company_id" class="form-control col-md-3">
                                <option value="">请选择</option>
                                @if($companies)
                                    @foreach($companies as $company)
                                        <option value="{{  $company->id  }}" {{ (Input::old("company_id") ==$company->id ? "selected" : "") }}>{{  $company->name  }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>
                    <div class="text-left">
                        <button type="submit" class="btn btn-primary btn-sm line-height-fix">保存</button>
                    </div>
                </form>
            </div>
        </div>
    </section>

@endsection


@section("javascript")
    <!-- ================== DATEPICKER SCRIPTS ==================-->
    <script src="/static/vendor/moment/min/moment.min.js"></script>
    <script src="/static/vendor/bootstrap-datepicker/bootstrap-datepicker.min.js"></script>
    <script src="/static/vendor/bootstrap-daterangepicker/daterangepicker.js"></script>
    <script src="/static/vendor/sweetalert2/dist/sweetalert2.min.js"></script>
    <script src="/static/js/components/bootstrap-datepicker-init.js"></script>
    <script src="/static/js/components/bootstrap-date-range-picker-init.js"></script>

    <!-- ================== 树形 ==================-->
    <script src="{{  asset('js/bootstrap-treeview.js')  }}"></script>
    <script>
        store_error = "{{  session('storeError')  }}";
        if (store_error) {
            alert(store_error);
        }
        name_unique = $("input[name='name']");

        name_unique.blur(function () {
            $("input[name='prefixemail']").val($(this).val().toLowerCase());
            $("input[name='email']").val($(this).val().toLowerCase() + '@' + $("select[name='suffixemail'] option:selected").val() + ".com")
            console.log($("input[name='email']").val());
        });

        $("select[name='suffixemail']").change(function () {
            $("input[name='email']").val($("input[name='prefixemail']").val() + '@' + $(this).val() + ".com");
            console.log($("input[name='email']").val());
        });


        function alertNoticeMessage(sync_value) {
            if (sync_value == 1) {
                alert('选择是，将会更新该员工企业微信信息，请谨慎选择！');
            } else if (sync_value == 0) {
                alert('选择否，将会删除该员工企业微信信息，请谨慎选择！');
            }
        }
    </script>
    <script>
        $('.datepicker').parent().datepicker({
            "autoclose": true,
            "format": "yyyy-mm-dd",
            "language": "zh-CN"
            // "startDate": "-3d"
        });
        $("#add_name").blur(function () {
            name = $.trim($("#add_name").val());
            usercheck(name);
        })

        //name校验
        function usercheck(name) {
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: "POST",
                dataType: "json",
                url: "{{  route('users.check')  }}",
                data: {'name': name},
                success: function (response) {
                    if (response.status == 'failed') {
                        alert(response.messages);
                    }
                }
            })
        }

        // })
    </script>
    <script>
        var select_dept = null;
        var $treeview = null;

        function getTree() {
            // Some logic to retrieve, or generate tree structure
            $.get('/departments/all', function (data, status) {
                treeData = formatTree(data);
                $treeview = $('#tree').treeview({
                    //color: "#967ADC",
                    expandIcon: 'glyphicon glyphicon-triangle-right',
                    collapseIcon: 'glyphicon glyphicon-triangle-bottom',
                    nodeIcon: 'glyphicon glyphicon-folder-close',
                    data: treeData,
                    onNodeSelected: function (event, node) {
                        console.log(node);
                        select_dept = node;
                    }
                });


                //绑定弹出窗口
                depts_model = $("#set_dept");
                depts_model.click(function () {
                    $("#DeptModal").modal('show');

                });

                $("#dept_selected").click(function () {
                    $("#DeptModal").modal('hide');
                    if (select_dept != null) {
                        $("#dept_container").empty();
                        $("#dept_container").append('<div class="btn btn-primary btn-sm line-height-fix">' + '<span data-toggle="tooltip" title="' + select_dept.path + '" data-placement="bottom">' +
                                select_dept.text + '</span>' +
                                '<input type="hidden" name="department_id" value="' +
                                select_dept.departId +
                                '"><a role="button" class="tag-i">×</a></div>&nbsp;');

                        $(".tag-i").off("click").click(function () {
                            $(this).parent().remove();
                        });
                    }
                    $treeview.treeview('collapseAll', {levels: 2});
                    $treeview.treeview('unselectNode', [select_dept]);
                });

            });
        }

        function formatTree(data) {
            let result = [];
            $(data).each(function () {
                if (this.childList) {
                    childNodes = formatTree(this.childList);
                }
                node = {text: this.name, departId: this.id, path: this.path, nodes: childNodes};
                result.push(node);
            });

            return result;

        }

        if ($("#tree").length > 0) {
            //元素存在时执行的代码
            getTree();
        }
    </script>
@endsection
