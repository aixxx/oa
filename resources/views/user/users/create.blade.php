@extends("layouts.main",['title' => '员工'])

@section('content')
    <header class="page-header">
        <div class="d-flex align-items-center">
            <div class="mr-auto">
                <h1 class="separator">员工添加</h1>
                <nav class="breadcrumb-wrapper" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{url('/')}}"><i class="icon dripicons-home"></i></a></li>
                        <li class="breadcrumb-item"><a href="javascript:void(0)"></a>员工管理</li>
                        <li class="breadcrumb-item active" aria-current="page">员工添加</li>
                    </ol>
                </nav>
            </div>
        </div>
    </header>
    <section class="page-content container-fluid">
        <div class="card">
            {{--<div class="card-header">--}}
            {{--<h3 class="card-title">添加员工</h3>--}}
            {{--</div>--}}
            <div class="card-body">
                <form action="{{  route('users.store')  }}" method="POST">
                    @csrf
                    <div class="form-group">
                        @if($errors->get('first_chinese_name'))
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach($errors->get('first_chinese_name') as $error)
                                        <li>{{  $error  }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        @if($errors->get('last_chinese_name'))
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach($errors->get('last_chinese_name') as $error)
                                        <li>{{  $error  }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        <div class="form-inline">
                            <div class="form-group">
                                <label class="form-label">姓：</label>
                                <input type="text" name="first_chinese_name" class="form-control"
                                       value="{{  old('first_chinese_name')  }}">
                                <label class="form-label text-danger">*</label>
                            </div>
                            &nbsp;&nbsp;&nbsp;
                            <div class="form-group">
                                <label class="form-label">名：</label>
                                <input type="text" name="last_chinese_name" class="form-control"
                                       value="{{  old('last_chinese_name')  }}">
                                <label class="form-label text-red text-danger">*</label>
                            </div>
                        </div>

                    </div>

                    <div class="form-group">
                        <label class="col-form-label">英文名 (英文名+姓氏拼音)</label><label
                                class="col-form-label text-danger">*</label>
                        @if($errors->get('english_name'))
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach($errors->get('english_name') as $error)
                                        <li>{{  $error  }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        <input type="text" name="english_name" class="form-control" value="{{  old('english_name')  }}">
                    </div>
                    <div class="form-group">
                        <label class="col-form-label">系统唯一账号 (英文名+姓氏拼音)</label><label
                                class="col-form-label text-danger">*</label>
                        @if($errors->get('name'))
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach($errors->get('name') as $error)
                                        <li>{{  $error  }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        <input type="text" name="name" class="form-control" value="{{  old('name')  }}">
                    </div>
                    <div class="form-group">
                        <label class="col-form-label">员工编号</label><label class="col-form-label text-danger">*</label>
                        @if($errors->get('employee_num'))
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach($errors->get('employee_num') as $error)
                                        <li>{{  $error  }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        <input type="text" name="employee_num" class="form-control" value="{{  $newEmployeeNum  }}">
                    </div>
                    <div class="form-group">
                        <label class="col-form-label">所属公司</label><label class="text-danger col-form-label">*</label>
                        @if($errors->get('company_id'))
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach($errors->get('company_id') as $error)
                                        <li>{{  $error  }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        <select name="company_id" class="form-control">
                            <option value="">请选择</option>
                            @if($companies)
                                @foreach($companies as $company)
                                    <option value="{{  $company->id  }}" {{ (Input::old("company_id") ==$company->id ? "selected" : "") }}>{{  $company->name  }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="col-form-label">所属部门</label><label class="col-form-label text-danger">*</label>
                        @if($errors->get('departments'))
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach($errors->get('departments') as $error)
                                        <li>{{  $error  }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        <div class="case-sensitive form-control" data-tags-input-name="case-sensitive"
                             id="dept_container" style="min-height: 40px;">
                            @if(Input::old("departments"))
                                @foreach(Input::old("departments") as $departmentId)
                                    <div class="btn btn-primary btn-sm line-height-fix">
                                        <span title="{{  \App\Models\Department::getDeptPath($departmentId)  }}">{{\App\Models\Department::getDepartmentName($departmentId)}}</span>
                                        <input type="hidden" name="departments[]" value="{{  $departmentId  }}">
                                        <a role="button" class="tag-i">×</a>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                        <a href="javascript:void(0)" class="btn btn-twitter btn-sm line-height-fix" id="set_dept"
                           style="margin-top: 0.5em;line-height:normal;">设置部门</a>
                    </div>
                    <div class="form-group">
                        <label class="col-form-label">主部门</label><label class="col-form-label text-danger">*</label>
                        @if($errors->get('pri_dept_id'))
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach($errors->get('pri_dept_id') as $error)
                                        <li>{{  $error  }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        <div class="custom-controls-stacked" id="main">
                            @if(Input::old("departments"))
                                @foreach(Input::old("departments") as $departmentId)
                                    <label class="custom-control custom-radio custom-control-inline">
                                        <input type="radio" class="custom-control-input" name="pri_dept_id" value="{{  $departmentId  }}"
                                            @php
                                                if (Input::old('pri_dept_id') == $departmentId) {
                                                    echo "checked";
                                                }
                                            @endphp
                                        >
                                        <span class="custom-control-label" data-toggle="tooltip" title="{{   \App\Models\Department::getDeptPath
                                        ($departmentId)  }}" data-placement="bottom" >{{\App\Models\Department::getDepartmentName($departmentId)}}</span>
                                    </label>
                                @endforeach
                            @endif

                            {{--<label class="custom-control custom-radio custom-control-inline">\n' +--}}
                                {{--'<input type="radio" class="custom-control-input" name="pri_dept_id" value="' + select_dept.departId + '">\n' +--}}
                                {{--'<span class="custom-control-label" data-toggle="tooltip" title="' + select_dept.path + '" data-placement="bottom">' + select_dept.text + '</span>\n' +--}}
                                {{--'</label>--}}
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">设置部门领导</label>
                        <div class="custom-controls-stacked" id="leader">
                            @if(Input::old("departments"))
                                @foreach(Input::old("departments") as $departmentId)
                                    <label class="custom-control custom-checkbox custom-control-inline">
                                        <input type="checkbox" class="custom-control-input" name="deptleader[]" value="{{  $departmentId }}"
                                            @php
                                                if (Input::old('deptleader')) {
                                                    if (in_array($departmentId,Input::old('deptleader'))){
                                                        echo "checked";
                                                    }
                                                }
                                            @endphp
                                        >
                                        <span class="custom-control-label" data-toggle="tooltip" title="{{\App\Models\Department::getDeptPath
                                        ($departmentId)}}"  data-placement="bottom">{{\App\Models\Department::getDepartmentName($departmentId)}}</span>
                                    </label>
                                @endforeach
                            @endif

                            {{--'<label class="custom-control custom-checkbox custom-control-inline">\n' +--}}
                                {{--'<input type="checkbox" class="custom-control-input" name="deptleader[]" value="' + select_dept.departId + '">\n' +--}}
                                {{--'<span class="custom-control-label" data-toggle="tooltip" title="' + select_dept.path + '" data-placement="bottom">' + select_dept.text + '</span>\n' +--}}
                                {{--'</label>'--}}
                        </div>
                    </div>
                    {{--<div class="form-group">--}}
                    {{--<label class="col-form-label">上级领导</label>--}}
                    {{--<label class="col-form-label text-danger">*</label>--}}
                    {{--@if($errors->get('superior_leaders'))--}}
                    {{--<div class="alert alert-danger">--}}
                    {{--<ul>--}}
                    {{--@foreach($errors->get('superior_leaders') as $error)--}}
                    {{--<li>{{  $error  }}</li>--}}
                    {{--@endforeach--}}
                    {{--</ul>--}}
                    {{--</div>--}}
                    {{--@endif--}}
                    {{--<select name="superior_leaders" id="sel_menu3" class="js-data-example-ajax form-control">--}}
                    {{--</select>--}}

                    {{--</div>--}}
                    <div class="form-group">
                        <label class="col-form-label">企业邮箱 &nbsp; (@前为系统唯一账号)</label><label
                                class="col-form-label text-danger">*</label>
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
                            <input type="hidden" name="email" value="{{old('email')}}">
                            <input type="text" name="prefixemail" class="form-control col-md-3"
                                   value="{{  old('name')  }}" readonly>@
                            <select name="suffixemail" class="form-control col-md-3">
                                <option value="aike" selected>aike</option>
                                <option value="hehan">hehan</option>
                            </select>
                            .com
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-form-label">手机号</label>
                        <label class="col-form-label text-danger">*</label>
                        @if($errors->get('mobile'))
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach($errors->get('mobile') as $error)
                                        <li>{{  $error  }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        <input type="text" name="mobile" class="form-control" maxlength="11"
                               value="{{  old('mobile')  }}">
                    </div>
                    <div class="form-group">
                        <label class="col-form-label">职位</label>
                        <label class="col-form-label text-danger">*</label>
                        @if($errors->get('position'))
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach($errors->get('position') as $error)
                                        <li>{{  $error  }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        <input type="text" name="position" class="form-control" value="{{  old('position')  }}">
                    </div>
                    <div class="form-group">
                        <label class="col-form-label">是否同步</label>
                        <label class="col-form-label text-danger">*</label>
                        @if($errors->get('is_sync_wechat'))
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach($errors->get('is_sync_wechat') as $error)
                                        <li>{{  $error  }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        <select class="form-control" name="is_sync_wechat" onchange="alertNoticeMessage(this.value)">
                            <option value="">请选择</option>
                            <option value="1" {{ (Input::old("is_sync_wechat") === '1' ? "selected" : "") }}>是</option>
                            <option value="0" {{ (Input::old("is_sync_wechat") === '0' ? "selected" : "") }}>否</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="col-form-label">性别</label>
                        <label class="col-form-label text-danger">*</label>
                        @if($errors->get('gender'))
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach($errors->get('gender') as $error)
                                        <li>{{  $error  }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        <select class="form-control" name="gender">
                            <option value="">请选择</option>
                            <option value="1" {{ (Input::old("gender") === '1' ? "selected" : "") }}>男</option>
                            <option value="2" {{ (Input::old("gender") === '2' ? "selected" : "") }}>女</option>
                            <option value="0" {{ (Input::old("gender") === '0' ? "selected" : "") }}>未知</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">固定电话</label>
                        <input type="text" name="telephone" class="form-control" value="{{  old('telephone')  }}">
                    </div>
                    <div class="form-group">
                        <label class="col-form-label">入职时间</label>
                        <label class="col-form-label text-danger">*</label>
                        @if($errors->get('join_at'))
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach($errors->get('join_at') as $error)
                                        <li>{{  $error  }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        <div class="input-group">
                            <div class="input-group date dp-years">
                                <input type="text" class="form-control datepicker z-index-fix" placeholder="Select Date"
                                       name="join_at" value="{{  old('join_at')  }}">
                                <span class="input-group-addon action">
														<i class="icon dripicons-calendar"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-form-label">工作地点</label>
                        <label class="col-form-label text-danger">*</label>
                        @if($errors->get('work_address'))
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach($errors->get('work_address') as $error)
                                        <li>{{  $error  }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        <select name="work_address" class="form-control">
                            <option value="">请选择</option>
                            <option value="shanghai" {{ (Input::old("work_address") === 'shanghai' ? "selected" : "") }}>上海</option>
                            <option value="beijing" {{ (Input::old("work_address") === 'beijing' ? "selected" : "") }}>北京</option>
                            <option value="chengdu" {{ (Input::old("work_address") === 'chengdu' ? "selected" : "") }}>成都</option>
                            <option value="shenzhen" {{ (Input::old("work_address") === 'shenzhen' ? "selected" : "") }}>深圳</option>
                            <option value="pingxiang" {{ (Input::old("work_address") === 'pingxiang' ? "selected" : "") }}>萍乡</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="col-form-label">是否高管</label>
                        <label class="col-form-label text-danger">*</label>
                        @if($errors->get('isleader'))
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach($errors->get('isleader') as $error)
                                        <li>{{  $error  }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        <select name="isleader" class="form-control">
                            <option value="">请选择</option>
                            <option value="1" {{ (Input::old("isleader") === '1' ? "selected" : "") }}>是</option>
                            <option value="0" {{ (Input::old("isleader") === '0' ? "selected" : "") }}>否</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="col-form-label">班值类型</label>
                        <label class="col-form-label text-danger">*</label>
                        @if($errors->get('work_type'))
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach($errors->get('work_type') as $error)
                                        <li>{{  $error  }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        <select name="work_type" id="work_type" class="form-control">
                            <option value="">请选择</option>
                            <option value="1" {{ (Input::old("work_type") === '1' ? "selected" : "") }}>客服制</option>
                            <option value="2" {{ (Input::old("work_type") === '2' ? "selected" : "") }}>职能制</option>
                            <option value="3" {{ (Input::old("work_type") === '3' ? "selected" : "") }}>弹性制</option>
                        </select>
                    </div>
                    <div class="form-group work_title">
                        <label class="col-form-label">班值代码</label>
                        <label class="col-form-label text-danger">*</label>
                        @if($errors->get('work_title'))
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach($errors->get('work_title') as $error)
                                        <li>{{  $error  }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        <select name="work_title" class="form-control">
                            <option value="">请选择</option>
                           @foreach($workClass as $class)
                                <option value="{{ $class->class_title }}" {{ (Input::old("class_title") == $class->class_title ? "selected" : "") }}>{{  $class->class_title .'' . $class->class_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="text-left">
                        <button type="submit" class="btn btn-primary btn-sm line-height-fix">保存</button>
                    </div>
                </form>
            </div>
        </div>
    </section>
    <!-- 部门弹窗 -->
    <div class="modal fade" id="DeptModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="exampleModalLabel">选择所在部门</h4>
                    <button type="button" class="close" style="color: #0b0603;" data-dismiss="modal" aria-label="Close">
                        &times;
                    </button>
                </div>
                <div class="modal-body">
                    <div id="tree"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default btn-sm line-height-fix" data-dismiss="modal">取消
                    </button>
                    <button id="dept_selected" type="submit" class="btn btn-primary btn-sm line-height-fix">确定</button>
                </div>
            </div>
        </div>
    </div>

@endsection

@section("javascript")
    <!-- ================== DATEPICKER SCRIPTS ==================-->
    <script src="/static/vendor/moment/min/moment.min.js"></script>
    <script src="/static/vendor/bootstrap-datepicker/bootstrap-datepicker.min.js"></script>
    <script src="/static/vendor/bootstrap-daterangepicker/daterangepicker.js"></script>
    <script src="/static/vendor/sweetalert2/dist/sweetalert2.min.js"></script>
    <script src="/static/js/components/bootstrap-datepicker-init.js"></script>
    <script src="/static/js/components/bootstrap-date-range-picker-init.js"></script>

    <script>
        //时间控件
        $('.datepicker').parent().datepicker({
            "autoclose": true,
            "format": "yyyy-mm-dd",
            "language": "zh-CN"
            // "startDate": "-3d"
        });

        store_error = "{{  session('storeError')  }}";
        if (store_error) {
            alert(store_error);
        }
    </script>
    <script>
        //排班
        if ($("#work_type").val() == 1 || $("#work_type").val() == '') {
            $(".work_title").hide();
        }
       $("#work_type").change(function(event) {
           //不等于客服制，可选择班值代码
           if ($("option:selected",this).val() == 1) {
               $(".work_title").hide();
           } else {
               $(".work_title").show();
           }
       })
    </script>

    <script src="/js/select2/select2.min.js"></script>
    <script src="/js/select2/zh-CN.js"></script>
    <script>

        function alertNoticeMessage(sync_value) {
            if (sync_value == 1) {
                alert('选择是，将会更新该员工企业微信信息，请谨慎选择！');
            } else if(sync_value == 0) {
                alert('选择否，将会删除该员工企业微信信息，请谨慎选择！');
            }
        }


        //远程筛选
        $("#sel_menu3").select2({
            ajax: {
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: "POST",
                url: "{{ route('users.ajax_search') }}",
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        q: params.term, // search term
                        page: params.page
                    };
                },

                cache: true
            },
            placeholder: '请输入',
            escapeMarkup: function (markup) {
                return markup;
            }, // let our custom formatter work
            minimumInputLength: 1,
            templateResult: formatRepoName, // omitted for brevity, see the source of this page
            templateSelection: formatRepoSelection // omitted for brevity, see the source of this page
        });

        function formatRepoName(repo) {
            console.log(repo);
            var markup = "<div>" + repo.text + "</div>";
            return markup;
        }

        function formatRepoSelection(repo) {
            return repo.full_name || repo.text;
        }
    </script>
    <script src="{{  asset('js/bootstrap-treeview.js')  }}"></script>
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
                    main = $("#main");
                    dept_leader = $("#leader");
                    if (select_dept != null) {
                        $("#dept_container").append('<div class="btn btn-primary btn-sm line-height-fix">' + '<span data-toggle="tooltip" title="' + select_dept.path + '" data-placement="bottom">' + select_dept.text + '</span>' +
                            '<input type="hidden" name="departments[]" value="' + select_dept.departId + '"><a role="button" class="tag-i">×</a></div>&nbsp;');

                        let node = '<label class="custom-control custom-radio custom-control-inline">\n' +
                            '<input type="radio" class="custom-control-input" name="pri_dept_id" value="' + select_dept.departId + '">\n' +
                            '<span class="custom-control-label" data-toggle="tooltip" title="' + select_dept.path + '" data-placement="bottom">' + select_dept.text + '</span>\n' +
                            '</label>';

                        let dept_leader_node = '<label class="custom-control custom-checkbox custom-control-inline">\n' +
                            '<input type="checkbox" class="custom-control-input" name="deptleader[]" value="' + select_dept.departId + '">\n' +
                            '<span class="custom-control-label" data-toggle="tooltip" title="' + select_dept.path + '" data-placement="bottom">' + select_dept.text + '</span>\n' +
                            '</label>';

                        main.append(node);
                        dept_leader.append(dept_leader_node);

                        $(".tag-i").off("click").click(function () {
                            $(this).parent().remove();
                            main.find("input[value=" + $(this).prev().attr('value') + "]").parent().remove()
                            dept_leader.find("input[value=" + $(this).prev().attr('value') + "]").parent().remove();
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

    </script>
@endsection

