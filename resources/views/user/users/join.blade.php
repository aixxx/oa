@extends("layouts.main",['title' => '部门员工'])
@section('content')
    <header class="page-header">
        <div class="d-flex align-items-center">
            <div class="mr-auto">
                <h1 class="separator">员工入职</h1>
                <nav class="breadcrumb-wrapper" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{url('/')}}"><i class="icon dripicons-home"></i></a></li>
                        <li class="breadcrumb-item"><a href="javascript:void(0)"></a>员工管理</li>
                        <li class="breadcrumb-item active" aria-current="page">员工入职</li>
                    </ol>
                </nav>
            </div>
        </div>
    </header>
    <section class="page-content container-fluid">
        <div class="card">
            {{--<div class="card-header">--}}
            {{--<h3 class="card-title">创建员工</h3>--}}
            {{--</div>--}}
            <div class="card-body">
                <form action="{{  route('users.store')  }}" method="POST">
                    @csrf
                    <input type="hidden" name="join" value="{{  $user->id  }}">
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
                                       value="{{  $user->family_name  }}">
                                <label class="form-label text-red">*</label>
                            </div>
                            &nbsp;&nbsp;&nbsp;
                            <div class="form-group">
                                <label class="form-label">名：</label>
                                <input type="text" name="last_chinese_name" class="form-control"
                                       value="{{  $user->given_name  }}">
                                <label class="form-label text-red">*</label>
                            </div>
                        </div>

                    </div>

                    <div class="form-group">
                        <label class="col-form-label">英文名</label>
                        <label class="col-form-label text-red">*</label>
                        @if($errors->get('english_name'))
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach($errors->get('english_name') as $error)
                                        <li>{{  $error  }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        <input type="text" name="english_name" class="form-control" value="{{  $user->english_name  }}">
                    </div>
                    <div class="form-group">
                        <label class="col-form-label">系统唯一账号 (英文名+姓氏拼音)</label>
                        <label class="col-form-label text-red">*</label>
                        @if($errors->get('name'))
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach($errors->get('name') as $error)
                                        <li>{{  $error  }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        <input type="text" name="name" class="form-control" value="{{ $user->name }}">
                    </div>
                    <div class="form-group">
                        <label class="col-form-label">员工编号</label>
                        <label class="col-form-label text-red">*</label>
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
                        <label class="col-form-label">所属公司</label>
                        <label class="col-form-label text-red">*</label>
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
                                    <option value="{{  $company->id  }}" <?php if ($user->company_id ==
                                        $company->id) {
                                        echo 'selected';
                                    }?> >{{  $company->name  }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">所属部门<span style="color:red">*</span></label>
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
                            @foreach($departments as $department)
                                @if( $department->id == $user->department_id)
                                    <div class="btn btn-primary btn-sm line-height-fix"><span title="{{$user->department_path}}">{{$department->name}}
                                            <span><input type="hidden" name="departments[]" value="{{  $department->id   }}"><a role="button" class="tag-i">×</a></div>
                                @endif
                            @endforeach

                        </div>
                        <a href="javascript:void(0)" class="btn btn-twitter btn-sm" id="set_dept">设置部门</a>
                    </div>
                    <div class="form-group">
                        <label class="col-form-label">主部门</label>
                        <label class="col-form-label text-red">*</label>
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

                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">设置部门领导</label>
                        <div class="custom-controls-stacked" id="leader">
                            {{--@foreach($deptLeader as $key => $value)--}}
                            {{--@if($value->is_leader == 1)--}}
                            {{--<label class="custom-control custom-checkbox custom-control-inline">--}}
                            {{--<input type="checkbox" class="custom-control-input" name="deptleader[]" value="{{  $value->department_id  }}" checked>--}}
                            {{--<span class="custom-control-label">{{  $value->department->name }}</span>--}}
                            {{--</label>--}}
                            {{--@else--}}
                            {{--<label class="custom-control custom-checkbox custom-control-inline">--}}
                            {{--<input type="checkbox" class="custom-control-input" name="deptleader[]" value="{{  $value->department_id  }}">--}}
                            {{--<span class="custom-control-label">{{  $value->department->name  }}</span>--}}
                            {{--</label>--}}
                            {{--@endif--}}
                            {{--@endforeach--}}
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-form-label">email &nbsp; (@前为企业微信账号)</label>
                        <label class="col-form-label text-red">*</label>
                        @if($errors->get('email'))
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach($errors->get('email') as $error)
                                        <li>{{  $error  }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        <input type="text" name="email" class="form-control" value="{{  $user->email  }}">
                    </div>
                    <div class="form-group">
                        <label class="col-form-label">手机号</label>
                        <label class="col-form-label text-red">*</label>
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
                               value="{{ (!empty($user->mobile) && isset($user->mobile)) ? decrypt($user->mobile) : ''  }}">
                    </div>
                    <div class="form-group">
                        <label class="col-form-label">职位</label>
                        <label class="col-form-label text-red">*</label>
                        @if($errors->get('position'))
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach($errors->get('position') as $error)
                                        <li>{{  $error  }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        <input type="text" name="position" class="form-control" value="{{  $user->position  }}">
                    </div>
                    <div class="form-group">
                        <label class="col-form-label">是否同步</label>
                        <label class="col-form-label text-red">*</label>
                        @if($errors->get('is_sync_wechat'))
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach($errors->get('is_sync_wechat') as $error)
                                        <li>{{  $error  }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        <select class="form-control" name="is_sync_wechat" onchange="alert('选择否，会删除企业微信信息，请谨慎选择！')">
                            <option value="">请选择</option>
                            @if($user->is_sync_wechat == 1)
                                <option value="1" selected>是</option>
                                <option value="0">否</option>
                            @else
                                <option value="1">是</option>
                                <option value="0" selected>否</option>
                            @endif
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="col-form-label">性别</label>
                        <label class="col-form-label text-red">*</label>
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
                            <option value="0">请选择</option>
                            <option value="1" <?php if ($user->gender == \App\Models\User::GENDER_MALE) {
                                echo 'selected';
                            }?>>男
                            </option>
                            <option value="2" <?php if ($user->gender == \App\Models\User::GENDER_FEMALE) {
                                echo 'selected';
                            }?>>女
                            </option>
                            <option value="0" <?php if ($user->gender == \App\Models\User::GENDER_UNKNOWN) {
                                echo 'selected';
                            }?>>未知
                            </option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">固定电话</label>
                        <input type="text" name="telephone" class="form-control" value="{{  old('telephone')  }}">
                    </div>
                    <div class="form-group">
                        <label class="col-form-label">入职时间</label>
                        <label class="col-form-label text-red">*</label>
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
                                @if($user->join_at)
                                    <input type="text" class="form-control datepicker z-index-fix"
                                           placeholder="Select Date" name="join_at"
                                           value="{{  date('Y-m-d',strtotime($user->join_at))  }}">
                                @else
                                    <input type="text" class="form-control datepicker z-index-fix"
                                           placeholder="Select Date" name="join_at">
                                @endif
                                <span class="input-group-addon action">
														<i class="icon dripicons-calendar"></i>
                                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-form-label">工作地点</label>
                        <label class="col-form-label text-red">*</label>
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
                            <option value="shanghai" <?php if ($user->work_address ==
                                \App\Models\PendingUser::WORK_ADDRESS_SHANGHAI) {
                                echo 'selected';
                            }?>>上海
                            </option>
                            <option value="beijing" <?php if ($user->work_address ==
                                \App\Models\PendingUser::WORK_ADDRESS_BEIJING) {
                                echo 'selected';
                            }?>>北京
                            </option>
                            <option value="chengdu" <?php if ($user->work_address ==
                                \App\Models\PendingUser::WORK_ADDRESS_CHENGDU) {
                                echo 'selected';
                            }?>>成都
                            </option>
                            <option value="shenzhen" <?php if ($user->work_address ==
                                \App\Models\PendingUser::WORK_ADDRESS_SHENZHEN) {
                                echo 'selected';
                            }?>>深圳
                            </option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="col-form-label">是否高管</label>
                        <label class="col-form-label text-red">*</label>
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
                            @if($user->is_leader == 1)
                                <option value="1" selected>是</option>
                                <option value="0">否</option>
                            @else
                                <option value="1">是</option>
                                <option value="0" selected>否</option>
                            @endif
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
                                <option value="{{ $class->class_title }}">{{  $class->class_title .'' . $class->class_name }}</option>
                            @endforeach
                        </select>
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
                        <button type="button" class="close" style="color: #0b0603;" data-dismiss="modal" aria-label="Close">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div id="tree"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default btn-sm line-height-fix" data-dismiss="modal">取消</button>
                        <button id="dept_selected" type="submit" class="btn btn-primary btn-sm line-height-fix">确定</button>
                    </div>
                </div>
            </div>
        </div>

@endsection
@section('javascript')
    <!-- ================== DATEPICKER SCRIPTS ==================-->
    <script src="/static/vendor/moment/min/moment.min.js"></script>
    <script src="/static/vendor/bootstrap-datepicker/bootstrap-datepicker.min.js"></script>
    <script src="/static/vendor/bootstrap-daterangepicker/daterangepicker.js"></script>
    <script src="/static/vendor/sweetalert2/dist/sweetalert2.min.js"></script>
    <script src="/static/js/components/bootstrap-datepicker-init.js"></script>
    <script src="/static/js/components/bootstrap-date-range-picker-init.js"></script>

    <script src="{{  asset('js/bootstrap-treeview.js')  }}"></script>
    <script>
        store_error = "{{  session('storeError')  }}";
        if(store_error)
        {
            alert(store_error);
            delete store_error;
        }

        //时间控件
        $('.datepicker').parent().datepicker({
            "autoclose": true,
            "format": "yyyy-mm-dd",
            "language": "zh-CN"
            // "startDate": "-3d"
        });
    </script>
    <script src="/js/select2/select2.min.js"></script>
    <script src="/js/select2/zh-CN.js"></script>
    <script>
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
            escapeMarkup: function (markup) { return markup; }, // let our custom formatter work
            minimumInputLength: 1,
            templateResult: formatRepoName, // omitted for brevity, see the source of this page
            templateSelection: formatRepoSelection // omitted for brevity, see the source of this page
        });

        function formatRepoName(repo) {
            console.log(repo);
            var markup = "<div>"+repo.text+"</div>";
            return markup;
        }
        function formatRepoSelection (repo) {
            return repo.full_name || repo.text;
        }
    </script>
    <script>
        var select_dept = null;
        var $treeview = null;

        function getTree() {
            // Some logic to retrieve, or generate tree structure
            $.get('/departments/all',function(data,status){
                treeData = formatTree(data);
                $treeview = $('#tree').treeview({
                    //color: "#967ADC",
                    expandIcon: 'glyphicon glyphicon-triangle-right',
                    collapseIcon: 'glyphicon glyphicon-triangle-bottom',
                    nodeIcon: 'glyphicon glyphicon-folder-close',
                    data: treeData,
                    onNodeSelected: function(event, node) {
                        console.log(node);
                        select_dept =  node;
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
                    if(select_dept!=null){
                        $("#dept_container").append('<div class="btn btn-primary btn-sm line-height-fix">'+'<span data-toggle="tooltip" title="'+select_dept.path+'" data-placement="bottom">'+select_dept.text+'</span>'+
                            '<input type="hidden" name="departments[]" value="'+select_dept.departId+'"><a role="button" class="tag-i">×</a></div>&nbsp;');

                        let node = '<label class="custom-control custom-radio custom-control-inline">\n' +
                            '<input type="radio" class="custom-control-input" name="pri_dept_id" value="' + select_dept.departId + '">\n' +
                            '<span class="custom-control-label" data-toggle="tooltip" title="'+ select_dept.path +'" data-placement="bottom">'+ select_dept.text +'</span>\n' +
                            '</label>';

                        let dept_leader_node = '<label class="custom-control custom-checkbox custom-control-inline">\n' +
                            '<input type="checkbox" class="custom-control-input" name="deptleader[]" value="' + select_dept.departId + '">\n' +
                            '<span class="custom-control-label" data-toggle="tooltip" title="'+ select_dept.path +'" data-placement="bottom">'+ select_dept.text +'</span>\n' +
                            '</label>';

                        main.append(node);
                        dept_leader.append(dept_leader_node);

                        dept_container_childrens = $("#dept_container").children();
                        dept_container_childrens.each(function(){
                            $(this).find("a").click(function(){
                                $(this).parent().remove();
                                main.find("input[value="+$(this).prev().attr('value')+"]").parent().remove()
                                dept_leader.find("input[value=" + $(this).prev().attr('value') + "]").parent().remove();
                            });
                        });
                        // $(".tag-i").off("click").click(function(){
                        //     $(this).parent().remove();
                        //     main.find("input[value="+$(this).prev().attr('value')+"]").parent().remove()
                        //     dept_leader.find("input[value=" + $(this).prev().attr('value') + "]").parent().remove();
                        // });
                    }
                    $treeview.treeview('collapseAll',{levels:2});
                    $treeview.treeview('unselectNode', [ select_dept]);
                });

            });
        }

        function formatTree(data){
            let result = [];
            $(data).each(function(){
                if(this.childList){
                    childNodes = formatTree(this.childList);
                }
                node = {text:this.name,departId:this.id,path:this.path,nodes:childNodes};
                result.push(node);
            });

            return result;

        }

        if($("#tree").length > 0) {
            //元素存在时执行的代码
            getTree();
        }


    </script>
    <script>
        main = $("#main");
        dept_leader = $("#leader");
        dept_container_children = $("#dept_container").children();
        dept_container_children.each(function(){
            $(this).find("a").click(function(){
                $(this).parent().parent().parent().remove();
                main.find("input[value="+$(this).prev().attr('value')+"]").parent().remove()
                dept_leader.find("input[value=" + $(this).prev().attr('value') + "]").parent().remove();
            });
        });

        if(dept_container_children.length)
        {
            if(!$("input[name='pri_dept_id']").length)
            {
                dept_container_children.each(function () {
                    var main_node = '<label class="custom-control custom-radio custom-control-inline">\n' +
                        '<input type="radio" class="custom-control-input" name="pri_dept_id" value="' + $(this).find("input").val() + '" checked>\n' +
                        '<span class="custom-control-label"  data-toggle="tooltip" title="'+ $(this).find('span').first().attr('title') +'" data-placement="bottom">'+ $(this).text().substring(0,$(this).text().length-1) +'</span>\n' +
                        '</label>';

                    var dept_leader_node = '<label class="custom-control custom-checkbox custom-control-inline">\n' +
                        '<input type="checkbox" class="custom-control-input" name="deptleader[]" value="' + $(this).find("input").val() + '">\n' +
                        '<span class="custom-control-label" data-toggle="tooltip" title="'+ $(this).find('span').first().attr('title') +'" data-placement="bottom">'+ $(this).text().substring(0,$(this).text().length-1) +'</span>\n' +
                        '</label>';

                    main.append(main_node);
                    dept_leader.append(dept_leader_node);
                });
            }
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
@endsection