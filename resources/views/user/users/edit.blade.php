@extends("layouts.main",['title' => '员工'])

@section('content')
    <header class="page-header">
        <div class="d-flex align-items-center">
            <div class="mr-auto">
                <h1 class="separator">员工编辑</h1>
                <nav class="breadcrumb-wrapper" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{url('/')}}"><i class="icon dripicons-home"></i></a></li>
                        <li class="breadcrumb-item"><a href="javascript:void(0)"></a>员工管理</li>
                        <li class="breadcrumb-item active" aria-current="page">员工编辑</li>
                    </ol>
                </nav>
            </div>
        </div>
    </header>
    <section class="page-content container-fluid">
        @if($type == 'basic')
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title" id="basic">基础信息</h3>
                </div>
                <div class="card-body">
                    <form action="{{  route('users.update',['id' => $user->id])  }}" method="POST">
                        @csrf
                        <input type="hidden" name="_method" value="PUT">
                        <input type="hidden" name="infotype" value="basic">
                        <div class="form-group">
                            <label class="col-form-label">中文名</label>
                            <label class="col-form-label text-danger">*</label>
                            @if($errors->get('chinese_name'))
                                <div class="alert alert-danger">
                                    <ul>
                                        @foreach($errors->get('chinese_name') as $error)
                                            <li>{{  $error  }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            <input type="text" name="chinese_name" class="form-control"
                                   value="{{  $user->chinese_name  }}">
                        </div>
                        <div class="form-group">
                            <label class="col-form-label">英文名 (英文名+姓氏拼音)</label>
                            <label class="col-form-label text-danger">*</label>
                            @if($errors->get('english_name'))
                                <div class="alert alert-danger">
                                    <ul>
                                        @foreach($errors->get('english_name') as $error)
                                            <li>{{  $error  }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            <input type="text" name="english_name" class="form-control"
                                   value="{{  $user->english_name  }}">
                        </div>
                        <div class="form-group">
                            <label class="col-form-label">系统唯一账号 (英文名+姓氏拼音)</label>
                            <label class="col-form-label text-danger">*</label>
                            @if($errors->get('name'))
                                <div class="alert alert-danger">
                                    <ul>
                                        @foreach($errors->get('name') as $error)
                                            <li>{{  $error  }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            <input type="text" name="name" class="form-control" value="{{  $user->name  }}" readonly>
                        </div>
                        <div class="form-group">
                            <label class="col-form-label">员工编号</label>
                            <label class="col-form-label text-danger">*</label>
                            @if($errors->get('employee_num'))
                                <div class="alert alert-danger">
                                    <ul>
                                        @foreach($errors->get('employee_num') as $error)
                                            <li>{{  $error  }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            <input type="text" name="employee_num" class="form-control"
                                   value="{{  $user->employee_num  }}">
                        </div>
                        <div class="form-group">
                            <label class="col-form-label">所属公司</label>
                            <label class="col-form-label text-danger">*</label>
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
                                        @if(in_array($company->id,$userCompanies))
                                            <option value="{{  $company->id  }}"
                                                    selected>{{  $company->name  }}</option>
                                        @else
                                            <option value="{{  $company->id  }}">{{  $company->name  }}</option>
                                        @endif

                                    @endforeach
                                @endif
                            </select>
                        </div>

                        @if($user->status == 1)
                            <div class="form-group">
                                <label class="col-form-label">所属部门</label>
                                <label class="col-form-label text-danger">*</label>
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
                                        @if(in_array($department->id,$userDepartmentsInfo))
                                            <div class="btn btn-primary btn-sm line-height-fix">
                                                <span title="{{$department->path}}">{{$department->name}}</span>
                                                <input type="hidden" name="departments[]" value="{{  $department->id   }}">
                                                <a role="button" class="tag-i">×</a>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                                <a href="javascript:void(0)" class="btn btn-twitter btn-sm line-height-fix"
                                   id="set_dept" style="margin-top: 0.5em;line-height:normal;">设置部门</a>
                            </div>

                            <div class="form-group">
                                <label class="col-form-label">主部门</label>
                                <label class="col-form-label text-danger">*</label>
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
                                    @foreach($deptLeader as $key => $value)
                                        @if($value->is_leader == 1)
                                            <label class="custom-control custom-checkbox custom-control-inline">
                                                <input type="checkbox" class="custom-control-input" name="deptleader[]"
                                                       value="{{  $value->department_id  }}" checked>
                                                <span class="custom-control-label" data-toggle="tooltip"
                                                      title="{{  $value->path  }}"
                                                      data-placement="bottom">{{  $value->department->name }}</span>
                                            </label>
                                        @else
                                            <label class="custom-control custom-checkbox custom-control-inline">
                                                <input type="checkbox" class="custom-control-input" name="deptleader[]"
                                                       value="{{  $value->department_id  }}">
                                                <span class="custom-control-label" data-toggle="tooltip"
                                                      title="{{  $value->path  }}"
                                                      data-placement="bottom">{{  $value->department->name  }}</span>
                                            </label>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        @endif
                        {{--<div class="form-group">--}}
                        {{--<label class="col-form-label">行政汇报领导</label>--}}
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
                        {{--<option value="">请选择</option>--}}
                        {{--@if($allUsers)--}}
                        {{--@foreach($allUsers as $key => $value)--}}
                        {{--@if($user->superior_leaders == $value->id)--}}
                        {{--<option value="{{ $value->id }}" selected>{{  $value->chinese_name  }}</option>--}}
                        {{--@else--}}
                        {{--<option value="{{ $value->id }}">{{  $value->chinese_name  }}</option>--}}
                        {{--@endif--}}
                        {{--@endforeach--}}
                        {{--@endif--}}
                        {{--</select>--}}
                        {{--</div>--}}
                        <div class="form-group">
                            <label class="col-form-label">企业邮箱 &nbsp; (@前为系统唯一账号)</label>
                            <label class="col-form-label text-danger">*</label>
                            @if($errors->get('email'))
                                <div class="alert alert-danger">
                                    <ul>
                                        @foreach($errors->get('email') as $error)
                                            <li>{{  $error  }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            <input type="text" name="email" class="form-control" value="{{  $user->email  }}" readonly>
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
                                   value="{{ (!empty($user->mobile) && isset($user->mobile)) ? ($user->mobile) : ''  }}">
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
                            <input type="text" name="position" class="form-control" value="{{  $user->position }}">
                        </div>
                        @if($errors->get('password'))
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach($errors->get('password') as $error)
                                        <li>{{  $error  }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        {{--<div class="form-group">--}}
                        {{--<label class="form-label">密码</label>--}}
                        {{--<input type="password" name="password" class="form-control" value="{{  old('password')  }}">--}}
                        {{--</div>--}}
                        {{--<div class="form-group">--}}
                        {{--<label class="form-label">确认密码</label>--}}
                        {{--<input type="password" name="password_confirmation" class="form-control" value="{{  old('password_confirmation')  }}">--}}
                        {{--</div>--}}
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
                            <input type="text" name="telephone" class="form-control" value="{{  $user->telephone  }}">
                        </div>
                        <div class="form-group">
                            <label class="col-form-label">入职时间</label>
                            <label class="col-form-label text-danger">*</label>
                            <div class="input-group">
                                @if($user->join_at)
                                    <div class="input-group">
                                        <div class="input-group date dp-years">
                                            <input type="text" class="form-control datepicker z-index-fix"
                                                   placeholder="Select Date" name="join_at"
                                                   value="{{  date('Y-m-d',strtotime($user->join_at))  }}">
                                            <span class="input-group-addon action">
														    <i class="icon dripicons-calendar"></i>
                                                        </span>
                                        </div>
                                    </div>
                                @else
                                    <div class="input-group">
                                        <div class="input-group date dp-years">
                                            <input type="text" class="form-control datepicker z-index-fix"
                                                   placeholder="Select Date" name="join_at" value="">
                                            <span class="input-group-addon action">
														    <i class="icon dripicons-calendar"></i>
                                                        </span>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-form-label">工作地点</label>
                            <label class="col-form-label text-danger">*</label>
                            <select name="work_address" class="form-control">
                                <option value="">请选择</option>
                                @if($user->work_address)
                                    @if($user->work_address == 'shanghai')
                                        <option value="shanghai" selected>上海</option>
                                        <option value="beijing">北京</option>
                                        <option value="chengdu">成都</option>
                                        <option value="shenzhen">深圳</option>
                                        <option value="pingxiang">萍乡</option>
                                    @elseif($user->work_address == 'beijing')
                                        <option value="shanghai">上海</option>
                                        <option value="beijing" selected>北京</option>
                                        <option value="chengdu">成都</option>
                                        <option value="shenzhen">深圳</option>
                                        <option value="pingxiang">萍乡</option>
                                    @elseif($user->work_address == 'chengdu')
                                        <option value="shanghai">上海</option>
                                        <option value="beijing">北京</option>
                                        <option value="chengdu" selected>成都</option>
                                        <option value="shenzhen">深圳</option>
                                        <option value="pingxiang">萍乡</option>
                                    @elseif($user->work_address == 'shenzhen')
                                        <option value="shanghai">上海</option>
                                        <option value="beijing">北京</option>
                                        <option value="chengdu">成都</option>
                                        <option value="shenzhen" selected>深圳</option>
                                        <option value="pingxiang">萍乡</option>
                                    @elseif($user->work_address == 'pingxiang')
                                        <option value="shanghai">上海</option>
                                        <option value="beijing">北京</option>
                                        <option value="chengdu">成都</option>
                                        <option value="shenzhen">深圳</option>
                                        <option value="pingxiang" selected>萍乡</option>
                                    @endif
                                @else
                                    <option value="shanghai">上海</option>
                                    <option value="beijing">北京</option>
                                    <option value="chengdu">成都</option>
                                    <option value="shenzhen">深圳</option>
                                    <option value="pingxiang">萍乡</option>
                                @endif

                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label">离职时间</label>
                            <div class="input-group">
                                @if($user->leave_at)
                                    <div class="input-group">
                                        <div class="input-group date dp-years">
                                            <input type="text" class="form-control datepicker z-index-fix"
                                                   placeholder="Select Date" name="leave_at"
                                                   value="{{  date('Y-m-d',strtotime($user->leave_at))  }}">
                                            <span class="input-group-addon action">
														    <i class="icon dripicons-calendar"></i>
                                                        </span>
                                        </div>
                                    </div>
                                @else
                                    <div class="input-group">
                                        <div class="input-group date dp-years">
                                            <input type="text" class="form-control datepicker z-index-fix"
                                                   placeholder="Select Date" name="leave_at" value="">
                                            <span class="input-group-addon action">
														    <i class="icon dripicons-calendar"></i>
                                                        </span>
                                        </div>
                                    </div>
                                @endif

                            </div>
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
                                @if($user->isleader == 1)
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
                                @foreach(\App\Models\Attendance\AttendanceWorkClass::CLASS_TYPE as $key=>$type)
                                    <option value="{{ $key }}" <?php if ($user->work_type == $key) {
                                        echo 'selected';
                                    }?>>{{ $type }}</option>
                                @endforeach
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
                                    <option value="{{ $class->class_title }}" <?php if ($user->work_title == $class->class_title) {
                                        echo 'selected';
                                    }?>>{{  $class->class_title .'' . $class->class_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="text-left">
                            <button type="submit" class="btn btn-primary btn-sm line-height-fix">保存</button>
                        </div>
                    </form>
                </div>
            </div>
        @elseif($type == 'job')
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title" id="job">工作信息</h3>
                </div>
                <div class="card-body">
                    <form action="{{  route('users.update',['id' => $user->id])  }}" method="POST">
                        @csrf
                        <input type="hidden" name="_method" value="PUT">
                        <input type="hidden" name="infotype" value="job">
                        <div class="form-group">
                            <label class="form-label">员工类型</label>
                            <select class="form-control" name="user_type">
                                <option value="">请选择</option>
                                @if($user->detail && $user->detail->user_type)
                                    @if(($user->detail->user_type) == 'full-time')
                                        <option value="full-time" selected>全职</option>
                                        <option value="part-time">兼职</option>
                                        <option value="internship">实习</option>
                                        <option value="labor-dispatch">劳务派遣</option>
                                        <option value="hire-retired">退休返聘</option>
                                        <option value="labor-outsourcing">劳务外包</option>
                                        <option value="counselor">顾问</option>
                                    @elseif(($user->detail->user_type) == 'part-time')
                                        <option value="full-time">全职</option>
                                        <option value="part-time" selected>兼职</option>
                                        <option value="internship">实习</option>
                                        <option value="labor-dispatch">劳务派遣</option>
                                        <option value="hire-retired">退休返聘</option>
                                        <option value="labor-outsourcing">劳务外包</option>
                                        <option value="counselor">顾问</option>
                                    @elseif(($user->detail->user_type) == 'internship')
                                        <option value="full-time">全职</option>
                                        <option value="part-time">兼职</option>
                                        <option value="internship" selected>实习</option>
                                        <option value="labor-dispatch">劳务派遣</option>
                                        <option value="hire-retired">退休返聘</option>
                                        <option value="labor-outsourcing">劳务外包</option>
                                        <option value="counselor">顾问</option>
                                    @elseif(($user->detail->user_type) == 'labor-dispatch')
                                        <option value="full-time">全职</option>
                                        <option value="part-time">兼职</option>
                                        <option value="internship">实习</option>
                                        <option value="labor-dispatch" selected>劳务派遣</option>
                                        <option value="hire-retired">退休返聘</option>
                                        <option value="labor-outsourcing">劳务外包</option>
                                        <option value="counselor">顾问</option>
                                    @elseif(($user->detail->user_type) == 'hire-retired')
                                        <option value="full-time">全职</option>
                                        <option value="part-time">兼职</option>
                                        <option value="internship">实习</option>
                                        <option value="labor-dispatch">劳务派遣</option>
                                        <option value="hire-retired" selected>退休返聘</option>
                                        <option value="labor-outsourcing">劳务外包</option>
                                        <option value="counselor">顾问</option>
                                    @elseif(($user->detail->user_type) == 'labor-outsourcing')
                                        <option value="full-time">全职</option>
                                        <option value="part-time">兼职</option>
                                        <option value="internship">实习</option>
                                        <option value="labor-dispatch">劳务派遣</option>
                                        <option value="hire-retired">退休返聘</option>
                                        <option value="labor-outsourcing" selected>劳务外包</option>
                                        <option value="counselor">顾问</option>
                                    @elseif(($user->detail->user_type) == 'counselor')
                                        <option value="full-time">全职</option>
                                        <option value="part-time">兼职</option>
                                        <option value="internship">实习</option>
                                        <option value="labor-dispatch">劳务派遣</option>
                                        <option value="hire-retired">退休返聘</option>
                                        <option value="labor-outsourcing" selected>劳务外包</option>
                                        <option value="counselor" selected>顾问</option>
                                    @endif
                                @else
                                    <option value="full-time">全职</option>
                                    <option value="part-time">兼职</option>
                                    <option value="internship">实习</option>
                                    <option value="labor-dispatch">劳务派遣</option>
                                    <option value="hire-retired">退休返聘</option>
                                    <option value="labor-outsourcing">劳务外包</option>
                                    <option value="counselor">顾问</option>
                                @endif
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">员工状态</label>
                            <select class="form-control" name="user_status">
                                <option value="">请选择</option>
                                @if($user->detail && $user->detail->user_status)
                                    @if(($user->detail->user_status) == 'regular')
                                        <option value="regular" selected>正式</option>
                                        <option value="non-regular">非正式</option>
                                    @elseif(($user->detail->user_status) == 'non-regular')
                                        <option value="regular">正式</option>
                                        <option value="non-regular" selected>非正式</option>
                                    @endif
                                @else
                                    <option value="regular">正式</option>
                                    <option value="non-regular">非正式</option>
                                @endif

                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">试用期</label>
                            @if($user->detail && $user->detail->probation)
                                <input type="text" name="probation" class="form-control"
                                       value="{{  ($user->detail->probation)  }}">
                            @else
                                <input type="text" name="probation" class="form-control" value="">
                            @endif
                        </div>

                        <div class="form-group">
                            <label class="form-label">转正日期</label>
                            <div class="input-group">
                                @if($user && $user->regular_at)
                                    <div class="input-group">
                                        <div class="input-group date dp-years">
                                            <input type="text" class="form-control datepicker z-index-fix"
                                                   placeholder="Select Date" name="regular_at"
                                                   value="{{  $user->regular_at  }}">
                                            <span class="input-group-addon action">
														    <i class="icon dripicons-calendar"></i>
                                                        </span>
                                        </div>
                                    </div>
                                @else
                                    <div class="input-group">
                                        <div class="input-group date dp-years">
                                            <input type="text" class="form-control datepicker z-index-fix"
                                                   placeholder="Select Date" name="regular_at" value="">
                                            <span class="input-group-addon action">
														    <i class="icon dripicons-calendar"></i>
                                                        </span>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">岗位职级</label>
                            @if($user->detail && $user->detail->grade)
                                <input type="text" name="grade" class="form-control"
                                       value="{{  ($user->detail->grade)  }}">
                            @else
                                <input type="text" name="grade" class="form-control" value="">
                            @endif
                        </div>
                        <div class="text-left">
                            <button type="submit" class="btn btn-primary btn-sm line-height-fix">保存</button>
                        </div>
                    </form>
                </div>
            </div>
        @elseif($type == 'person')
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title" id="person">身份信息</h3>
                </div>
                <div class="card-body">
                    <form action="{{  route('users.update',['id' => $user->id])  }}" method="POST">
                        @csrf
                        <input type="hidden" name="_method" value="PUT">
                        <input type="hidden" name="infotype" value="person">
                        <div class="form-group">
                            <label class="form-label">身份证姓名</label>
                            @if($user->detail && $user->detail->id_name)
                                <input type="text" name="id_name" class="form-control"
                                       value="{{  ($user->detail->id_name)  }}">
                            @else
                                <input type="text" name="id_name" class="form-control" value="">
                            @endif
                        </div>
                        <div class="form-group">
                            <label class="form-label">证件号码</label>
                            @if($user->detail && $user->detail->id_number)
                                <input type="text" name="id_number" class="form-control"
                                       value="{{  ($user->detail->id_number)  }}">
                            @else
                                <input type="text" name="id_number" class="form-control" value="">
                            @endif
                        </div>
                        <div class="form-group">
                            <label class="form-label">出生日期</label>
                            @if($user->detail && $user->detail->born_time)
                                <div class="input-group">
                                    <div class="input-group date dp-years">
                                        <input type="text" class="form-control datepicker z-index-fix"
                                               placeholder="Select Date" name="born_time"
                                               value="{{  ($user->detail->born_time)  }}">
                                        <span class="input-group-addon action">
														    <i class="icon dripicons-calendar"></i>
                                                        </span>
                                    </div>
                                </div>
                            @else
                                <div class="input-group">
                                    <div class="input-group date dp-years">
                                        <input type="text" class="form-control datepicker z-index-fix"
                                               placeholder="Select Date" name="born_time" value="">
                                        <span class="input-group-addon action">
														    <i class="icon dripicons-calendar"></i>
                                                        </span>
                                    </div>
                                </div>
                            @endif
                        </div>
                        <div class="form-group">
                            <label class="form-label">民族</label>
                            @if($user->detail && $user->detail->ethnic)
                                <input type="text" name="ethnic" class="form-control"
                                       value="{{  ($user->detail->ethnic)  }}">
                            @else
                                <input type="text" name="ethnic" class="form-control" value="">
                            @endif
                        </div>
                        <div class="form-group">
                            <label class="form-label">身份证地址</label>
                            @if($user->detail && $user->detail->id_address)
                                <input type="text" name="id_address" class="form-control"
                                       value="{{  ($user->detail->id_address)  }}">
                            @else
                                <input type="text" name="id_address" class="form-control" value="">
                            @endif
                        </div>
                        <div class="form-group">
                            <label class="form-label">证件有效期</label>
                            @if($user->detail && $user->detail->validity_certificate)
                                <div class="input-group">
                                    <div class="input-group date dp-years">
                                        <input type="text" class="form-control datepicker z-index-fix"
                                               placeholder="Select Date" name="validity_certificate"
                                               value="{{  ($user->detail->validity_certificate)  }}">
                                        <span class="input-group-addon action">
														    <i class="icon dripicons-calendar"></i>
                                                        </span>
                                    </div>
                                </div>
                            @else
                                <div class="input-group">
                                    <div class="input-group date dp-years">
                                        <input type="text" class="form-control datepicker z-index-fix"
                                               placeholder="Select Date" name="validity_certificate" value="">
                                        <span class="input-group-addon action">
														    <i class="icon dripicons-calendar"></i>
                                                        </span>
                                    </div>
                                </div>
                            @endif
                        </div>
                        <div class="form-group">
                            <label class="form-label">户籍类型</label>
                            <select name="census_type" class="form-control">
                                <option value="">请选择</option>
                                @if($user->detail && $user->detail->census_type)
                                    @if(($user->detail->census_type) == 'agriculture')
                                        <option value="agriculture" selected>农业</option>
                                        <option value="non-agriculture">非农业</option>
                                    @elseif(($user->detail->census_type) == 'non-agriculture')
                                        <option value="agriculture">农业</option>
                                        <option value="non-agriculture" selected>非农业</option>
                                    @endif
                                @else
                                    <option value="agriculture">农业</option>
                                    <option value="non-agriculture">非农业</option>
                                @endif
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">住址</label>
                            @if($user->detail && $user->detail->address)
                                <input type="text" name="address" class="form-control"
                                       value="{{  ($user->detail->address)  }}">
                            @else
                                <input type="text" name="address" class="form-control" value="">
                            @endif
                        </div>
                        <div class="form-group">
                            <label class="form-label">政治面貌</label>
                            <select name="politics_status" class="form-control">
                                <option value="">请选择</option>
                                @if($user->detail && $user->detail->politics_status)
                                    @if(($user->detail->politics_status) == 'party_member')
                                        <option value="party_member" selected>党员</option>
                                        <option value="masses">群众</option>
                                    @elseif(($user->detail->politics_status) == 'masses')
                                        <option value="party_member">党员</option>
                                        <option value="masses" selected>群众</option>
                                    @endif
                                @else
                                    <option value="party_member">党员</option>
                                    <option value="masses">群众</option>
                                @endif
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">婚姻状况</label>
                            <select name="marital_status" class="form-control">
                                <option value="">请选择</option>
                                @if($user->detail && $user->detail->marital_status)
                                    @if(($user->detail->marital_status) == 'unmarried')
                                        <option value="unmarried" selected>未婚</option>
                                        <option value="married">已婚</option>
                                        <option value="divorced">离异</option>
                                        <option value="widowed">丧偶</option>
                                    @elseif(($user->detail->marital_status) == 'married')
                                        <option value="unmarried">未婚</option>
                                        <option value="married" selected>已婚</option>
                                        <option value="divorced">离异</option>
                                        <option value="widowed">丧偶</option>
                                    @elseif(($user->detail->marital_status) == 'divorced')
                                        <option value="unmarried">未婚</option>
                                        <option value="married">已婚</option>
                                        <option value="divorced" selected>离异</option>
                                        <option value="widowed">丧偶</option>
                                    @elseif(($user->detail->marital_status) == 'widowed')
                                        <option value="unmarried">未婚</option>
                                        <option value="married">已婚</option>
                                        <option value="divorced">离异</option>
                                        <option value="widowed" selected>丧偶</option>
                                    @endif
                                @else
                                    <option value="unmarried">未婚</option>
                                    <option value="married">已婚</option>
                                    <option value="divorced">离异</option>
                                    <option value="widowed">丧偶</option>
                                @endif
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">首次参加工作时间</label>
                            <div class="input-group">
                                <div class="input-group date dp-years">
                                    @if($user->detail && $user->detail->first_work_time)
                                        <input type="text" class="form-control datepicker z-index-fix"
                                               placeholder="Select Date" name="first_work_time"
                                               value="{{  $user->detail->first_work_time  }}">
                                    @else
                                        <input type="text" class="form-control datepicker z-index-fix"
                                               placeholder="Select Date" name="first_work_time" value="">
                                    @endif
                                    <span class="input-group-addon action">
                                                        <i class="icon dripicons-calendar"></i>
                                                </span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">个人社保账号</label>
                            @if($user->detail && $user->detail->per_social_account)
                                <input type="text" name="per_social_account" class="form-control"
                                       value="{{  ($user->detail->per_social_account)  }}">
                            @else
                                <input type="text" name="per_social_account" class="form-control" value="">
                            @endif
                        </div>
                        <div class="form-group">
                            <label class="form-label">个人公积金账号</label>
                            @if($user->detail && $user->detail->per_fund_account)
                                <input type="text" name="per_fund_account" class="form-control"
                                       value="{{  ($user->detail->per_fund_account)  }}">
                            @else
                                <input type="text" name="per_fund_account" class="form-control" value="">
                            @endif
                        </div>
                        <div class="text-left">
                            <button type="submit" class="btn btn-primary btn-sm line-height-fix">保存</button>
                        </div>
                    </form>
                </div>
            </div>
        @elseif($type == 'edu')
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title" id="edu">学历信息</h3>
                </div>
                <div class="card-body">
                    <form action="{{  route('users.update',['id' => $user->id])  }}" method="POST">
                        @csrf
                        <input type="hidden" name="_method" value="PUT">
                        <input type="hidden" name="infotype" value="edu">
                        <div class="form-group">
                            <label class="form-label">最高学历</label>
                            @if($user->detail && $user->detail->highest_education)
                                <input type="text" name="highest_education" class="form-control"
                                       value="{{  ($user->detail->highest_education)  }}">
                            @else
                                <input type="text" name="highest_education" class="form-control" value="">
                            @endif
                        </div>

                        <div class="form-group">
                            <label class="form-label">毕业院校</label>
                            @if($user->detail && $user->detail->graduate_institutions)
                                <input type="text" name="graduate_institutions" class="form-control"
                                       value="{{  ($user->detail->graduate_institutions)  }}">
                            @else
                                <input type="text" name="graduate_institutions" class="form-control" value="">
                            @endif
                        </div>

                        <div class="form-group">
                            <label class="form-label">毕业时间</label>
                            <div class="input-group">
                                <div class="input-group date dp-years">
                                    @if($user->detail && $user->detail->graduate_time)
                                        <input type="text" class="form-control datepicker z-index-fix"
                                               placeholder="Select Date" name="graduate_time"
                                               value="{{  ($user->detail->graduate_time)  }}">
                                    @else
                                        <input type="text" class="form-control datepicker z-index-fix"
                                               placeholder="Select Date" name="graduate_time" value="">
                                    @endif
                                    <span class="input-group-addon action">
                                                    <i class="icon dripicons-calendar"></i>
                                            </span>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">所学专业</label>
                            @if($user->detail && $user->detail->major)
                                <input type="text" name="major" class="form-control"
                                       value="{{  ($user->detail->major)  }}">
                            @else
                                <input type="text" name="major" class="form-control" value="">
                            @endif
                        </div>
                        <div class="text-left">
                            <button type="submit" class="btn btn-primary btn-sm line-height-fix">保存</button>
                        </div>
                    </form>
                </div>
            </div>
        @elseif($type == 'card')
            <div class="card">
                <h3 class="card-header font-size-20">银行卡信息
                    @if(count($user->bankCard)<2)
                        <a class="btn btn-primary btn-outline btn-sm float-right" data-target="#addBankCard" data-toggle="modal">添加</a>
                    @endif
                </h3>

                <h4>&nbsp;</h4>
                <div class="font-size-fix card-deck">
                    @if(count($user->bankCard))
                        @foreach($user->bankCard as $key=> $bankcard)
                            <div id="card1" class="card card-body card-overlay">
                                <div>
                                    <h3 class="font-size-20">卡{{ $key+1 }}{{ $bankcard->bank_type==1?'(主卡)':'(副卡)' }}
                                        <button data-href="{{ route('users.delete_bank_card',['id' => $bankcard->id]) }}"
                                                class="btn btn-danger btn-outline float-right btn-sm delete_bank_card">删除
                                        </button>
                                    </h3>
                                </div>
                                <div class="row">
                                    @if($bankcard->card_num)
                                        <div class="col-12 text-left">银行卡号：
                                            {{ \App\Models\UserBankCard::formatBankCardShowType(($bankcard->card_num),4) }}
                                        </div>
                                    @else
                                        <div class="col-12 text-left">银行卡号：</div>
                                    @endif

                                    @if( $bankcard->bank)
                                        <div class="col-12">开户行： {{  ($bankcard->bank )  }}</div>
                                    @else
                                        <div class="col-12">开户行：</div>
                                    @endif

                                </div>
                                <div class="row">
                                    @if( $bankcard->branch_bank)
                                        <div class="col-12 text-left">支行名称： {{   ($bankcard->branch_bank)  }}</div>
                                    @else
                                        <div class="col-12 text-left">支行名称：</div>
                                    @endif

                                    @if($bankcard->bank_provinc||$bankcard->bank_city)
                                        <div class="col-12">银行卡属地：
                                            @php
                                                if ($bankcard->bank_province && $bankcard->bank_city) {
                                                    echo ($bankcard->bank_province).($bankcard->bank_city);
                                                } elseif($bankcard->bank_province && !$bankcard->bank_city) {
                                                    echo ($bankcard->bank_province);
                                                } elseif(!$bankcard->bank_province && $bankcard->bank_city){
                                                    echo ($bankcard->bank_city);
                                                }
                                            @endphp
                                        </div>
                                    @else
                                        <div class="col-md-6">银行卡属地：</div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        @elseif($type == 'contract')
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title" id="contract">合同信息</h3>
                </div>

                <div class="card-body">
                    <form action="{{  route('users.update',['id' => $user->id])  }}" method="POST">
                        @csrf
                        <input type="hidden" name="_method" value="PUT">
                        <input type="hidden" name="infotype" value="contract">
                        <div class="form-group">
                            <label class="form-label">合同公司</label>
                            @if($user->detail && $user->detail->contract_company)
                                <input type="text" name="contract_company" class="form-control"
                                       value="{{  ($user->detail->contract_company)  }}">
                            @else
                                <input type="text" name="contract_company" class="form-control" value="">
                            @endif
                        </div>

                        <div class="form-group">
                            <label class="form-label">合同类型</label>
                            <select name="contract_type" class="form-control">
                                <option value="">请选择</option>
                                @if($user->detail && $user->detail->contract_type)
                                    @if(($user->detail->contract_type) == 'fixed')
                                        <option value="fixed" selected>全职</option>
                                        <option value="non-fixed">兼职</option>
                                    @elseif(($user->detail->contract_type) == 'non-fixed')
                                        <option value="fixed">全职</option>
                                        <option value="non-fixed" selected>兼职</option>
                                    @endif
                                @else
                                    <option value="fixed">全职</option>
                                    <option value="non-fixed">兼职</option>
                                @endif
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label">首次合同起始日</label>
                            <div class="input-group">
                                <div class="input-group date dp-years">
                                    @if($user->detail && $user->detail->first_contract_start_time)
                                        <input type="text" class="form-control datepicker z-index-fix"
                                               placeholder="Select Date" name="first_contract_start_time"
                                               value="{{  ($user->detail->first_contract_start_time)  }}">
                                    @else
                                        <input type="text" class="form-control datepicker z-index-fix"
                                               placeholder="Select Date" name="first_contract_start_time" value="">
                                    @endif
                                    <span class="input-group-addon action">
                                                    <i class="icon dripicons-calendar"></i>
                                            </span>
                                </div>
                            </div>

                        </div>

                        <div class="form-group">
                            <label class="form-label">首次合同到期日</label>
                            <div class="input-group">
                                <div class="input-group date dp-years">
                                    @if($user->detail && $user->detail->first_contract_end_time)
                                        <input type="text" class="form-control datepicker z-index-fix"
                                               placeholder="Select Date" name="first_contract_end_time"
                                               value="{{  ($user->detail->first_contract_end_time)  }}">
                                    @else
                                        <input type="text" class="form-control datepicker z-index-fix"
                                               placeholder="Select Date" name="first_contract_end_time" value="">
                                    @endif
                                    <span class="input-group-addon action">
                                                    <i class="icon dripicons-calendar"></i>
                                            </span>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">现合同起始日</label>
                            <div class="input-group">
                                <div class="input-group date dp-years">
                                    @if($user->detail && $user->detail->cur_contract_start_time)
                                        <input type="text" class="form-control datepicker z-index-fix"
                                               placeholder="Select Date" name="cur_contract_start_time"
                                               value="{{  ($user->detail->cur_contract_start_time)  }}">
                                    @else
                                        <input type="text" class="form-control datepicker z-index-fix"
                                               placeholder="Select Date" name="cur_contract_start_time" value="">
                                    @endif
                                    <span class="input-group-addon action">
                                                    <i class="icon dripicons-calendar"></i>
                                            </span>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">现合同到期日</label>
                            <div class="input-group">
                                <div class="input-group date dp-years">
                                    @if($user->detail && $user->detail->cur_contract_end_time)
                                        <input type="text" class="form-control datepicker z-index-fix"
                                               placeholder="Select Date" name="cur_contract_end_time"
                                               value="{{  ($user->detail->cur_contract_end_time)  }}">
                                    @else
                                        <input type="text" class="form-control datepicker z-index-fix"
                                               placeholder="Select Date" name="cur_contract_end_time" value="">
                                    @endif
                                    <span class="input-group-addon action">
                                                    <i class="icon dripicons-calendar"></i>
                                            </span>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">合同期限（年）</label>
                            @if($user->detail && $user->detail->contract_term)
                                <input type="text" name="contract_term" class="form-control"
                                       value="{{  ($user->detail->contract_term)  }}">
                            @else
                                <input type="text" name="contract_term" class="form-control" value="">
                            @endif

                        </div>

                        <div class="form-group">
                            <label class="form-label">续签次数</label>
                            @if($user->detail && $user->detail->renew_times)
                                <input type="text" name="renew_times" class="form-control"
                                       value="{{  ($user->detail->renew_times)  }}">
                            @else
                                <input type="text" name="renew_times" class="form-control" value="">
                            @endif

                        </div>

                        <div class="text-left">
                            <button type="submit" class="btn btn-primary btn-sm line-height-fix">保存</button>
                        </div>
                    </form>
                </div>
            </div>
        @elseif($type == 'emerge')
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title" id="emerge">紧急联系人</h3>
                </div>
                <div class="card-body">
                    <form action="{{  route('users.update',['id' => $user->id])  }}" method="POST">
                        @csrf
                        <input type="hidden" name="_method" value="PUT">
                        <input type="hidden" name="infotype" value="emerge">
                        <div class="form-group">
                            <label class="form-label">紧急联系人姓名</label>
                            @if($user->detail && $user->detail->emergency_contact)
                                <input type="text" name="emergency_contact" class="form-control"
                                       value="{{  ($user->detail->emergency_contact)  }}">
                            @else
                                <input type="text" name="emergency_contact" class="form-control" value="">
                            @endif
                        </div>

                        <div class="form-group">
                            <label class="form-label">联系人关系</label>
                            @if($user->detail && $user->detail->contact_relationship)
                                <input type="text" name="contact_relationship" class="form-control"
                                       value="{{  ($user->detail->contact_relationship)  }}">
                            @else
                                <input type="text" name="contact_relationship" class="form-control" value="">
                            @endif

                        </div>

                        <div class="form-group">
                            <label class="form-label">联系人电话</label>
                            @if($user->detail && $user->detail->contact_mobile)
                                <input type="text" name="contact_mobile" class="form-control"
                                       value="{{  ($user->detail->contact_mobile)  }}">
                            @else
                                <input type="text" name="contact_mobile" class="form-control" value="">
                            @endif
                        </div>

                        <div class="text-left">
                            <button type="submit" class="btn btn-primary btn-sm line-height-fix">保存</button>
                        </div>
                    </form>
                </div>
            </div>
        @elseif($type == 'family')
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title" id="family">家庭信息</h3>
                </div>
                <div class="card-body">
                    <form action="{{  route('users.update',['id' => $user->id])  }}" method="POST">
                        @csrf
                        <input type="hidden" name="_method" value="PUT">
                        <input type="hidden" name="infotype" value="family">
                        <div class="form-group">
                            <label class="form-label">有无子女</label>
                            <select name="has_children" class="form-control">
                                <option value="">请选择</option>
                                @if($user->detail && $user->detail->has_children)
                                    @if(($user->detail->has_children) == 1)
                                        <option value="1" selected>有</option>
                                        <option value="0">无</option>
                                    @elseif(($user->detail->has_children) == 0)
                                        <option value="1">有</option>
                                        <option value="0" selected>无</option>
                                    @endif
                                @else
                                    <option value="1">有</option>
                                    <option value="0">无</option>
                                @endif
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label">子女姓名</label>
                            @if($user->detail && $user->detail->child_name)
                                <input type="text" name="child_name" class="form-control"
                                       value="{{  ($user->detail->child_name)  }}">
                            @else
                                <input type="text" name="child_name" class="form-control" value="">
                            @endif

                        </div>

                        <div class="form-group">
                            <label class="form-label">子女性别</label>
                            <select name="child_gender" class="form-control">
                                <option value="0">请选择</option>
                                @if($user->detail && $user->detail->child_gender)
                                    <option value="1" <?php if (($user->detail->child_gender) == \App\Models\User::GENDER_MALE) {
                                        echo 'selected';
                                    }?>>男
                                    </option>
                                    <option value="2" <?php if (($user->detail->child_gender) == \App\Models\User::GENDER_FEMALE) {
                                        echo 'selected';
                                    }?>>女
                                    </option>
                                    <option value="0" <?php if (($user->detail->child_gender) == \App\Models\User::GENDER_UNKNOWN) {
                                        echo 'selected';
                                    }?>>未知
                                    </option>
                                @else
                                    <option value="1">男</option>
                                    <option value="2">女</option>
                                    <option value="0" selected>未知</option>
                                @endif
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label">子女出生日期</label>
                            @if($user->detail && $user->detail->child_born_time)
                                <input type="text" name="child_born_time" class="form-control"
                                       value="{{  ($user->detail->child_born_time)  }}">
                            @else
                                <input type="text" name="child_born_time" class="form-control" value="">
                            @endif
                        </div>
                        <div class="text-left">
                            <button type="submit" class="btn btn-primary btn-sm line-height-fix">保存</button>
                        </div>
                    </form>
                </div>
            </div>
        @elseif($type == 'stuff')
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title" id="stuff">个人材料</h3>
                </div>

                <div class="card-body">
                    <form action="" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="infotype" value="pic">
                        <div class="form-group">
                            <label class="form-label">身份证（人像面）</label>
                            @if($user->detail && $user->detail->pic_id_pos)
                                <img src="{{ $user->detail->pic_id_pos }}" alt="">
                            @else
                                <img src="" alt="">
                            @endif
                            <input type="file" class="file input_id" name="pic_id_pos">
                        </div>

                        <div class="form-group">
                            <label class="form-label">身份证（国徽面）</label>
                            @if($user->detail && $user->detail->pic_id_neg)
                                <img src="{{ $user->detail->pic_id_neg }}" alt="">
                            @else
                                <img src="" alt="">
                            @endif

                            <input type="file" class="file input_id" name="pic_id_neg">
                        </div>

                        <div class="form-group">
                            <label class="form-label">学历证书</label>
                            @if($user->detail && $user->detail->pic_edu_background)
                                <img src="{{ $user->detail->pic_edu_background }}" alt="">
                            @else
                                <img src="" alt="">
                            @endif

                            <input type="file" class="file input_id" name="pic_edu_background">
                        </div>

                        <div class="form-group">
                            <label class="form-label">学位证书</label>
                            @if($user->detail && $user->detail->pic_degree)
                                <img src="{{ $user->detail->pic_degree }}" alt="">
                            @else
                                <img src="" alt="">
                            @endif

                            <input type="file" class="file input_id" name="pic_degree">
                        </div>

                        <div class="form-group">
                            <label class="form-label">前公司离职证明</label>
                            @if($user->detail && $user->detail->pic_pre_company)
                                <img src="{{ $user->detail->pic_pre_company }}" alt="">
                            @else
                                <img src="" alt="">
                            @endif
                            <input type="file" class="file input_id" name="pic_pre_company">
                        </div>

                        <div class="form-group">
                            <label class="form-label">员工照片</label>
                            @if($user->detail && $user->detail->pic_user)
                                <img src="{{ $user->detail->pic_user }}" alt="">
                            @else
                                <img src="" alt="">
                            @endif
                            <input type="file" class="file input_id" name="pic_user">
                        </div>
                    </form>
                </div>
            </div>
        @endif
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


    <!--添加银行卡model-->
    <div class="modal fade" id="addBankCard" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">

            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="exampleModalLabel">添加银行卡</h4>
                </div>
                <div class="modal-body">
                    <form class='addBankCard' method="post">
                        @csrf
                        <input type="hidden" name="user_id" value="{{  $user->id  }}">
                        <div class="form-group">
                            <label for="recipient-name" class="control-label" id="model_main_body">银行卡号<label class="text-danger">*</label></label>
                            <input class="form-control" name="bank_card_num" id="bank_card_num" oninput="myFunction()"/>
                        </div>
                        <div class="form-group">
                            <label for="recipient-name" class="control-label" id="model_seal_type">开户行<label class="text-danger">*</label></label>
                            <select class="form-control" name="bank" id="user_family_sex">
                                <option value="0">-请选择-</option>
                                @foreach($banks as $key => $bank)
                                    <option value="{{$key}}">{{$bank}}</option>
                                @endforeach
                            </select>
                            {{--  <input class="form-control" name="bank" id="bank"/>--}}
                        </div>
                        <div class="form-group">
                            <label for="recipient-name" class="control-label" id="model_seal_type">支行名称</label>
                            <input class="form-control" name="bank_branch" id="bank_branch"/>
                        </div>
                        <div class="form-group">
                            <label for="recipient-name" class="control-label" id="model_seal_type">银行卡属地(省)</label>
                            <input class="form-control" name="bank_province" id="bank_province"/>
                        </div>
                        <div class="form-group">
                            <label for="recipient-name" class="control-label" id="model_seal_type">银行卡属地(市)<label class="text-danger">*</label></label>
                            <input class="form-control" name="bank_city" id="bank_city"/>
                        </div>
                        <div class="form-group">
                            <label for="recipient-name" class="control-label" id="model_seal_type">银行卡类型（只能添加一张主卡）<label
                                        class="text-danger">*</label></label>
                            <select class="form-control" name="bank_type" id="bank_type">
                                <option value="2">副卡</option>
                                <option value="1">主卡</option>
                            </select>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default btn-sm line-height-fix" data-dismiss="modal">
                                取消
                            </button>
                            <button data-href="{{  route('users.admin_add_bank_card')  }}"
                                    type="submit" class="btn btn-primary btn-sm line-height-fix save-bankcard">保存
                            </button>
                        </div>
                    </form>
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
        @if(isset($errors) && $errors->count()>0)
            error_message = "";
        @foreach ($errors->all() as $error)
            error_message += "{{$error}}" + "\n";
        @endforeach
        alert(error_message);
        delete error_message;
        @endif




            update_success = "{{  session('updateSuccess')  }}";
        update_error = "{{  session('updateError')  }}";
        if (update_success) {
            alert(update_success);
        }

        if (update_error) {
            alert(update_error);
        }

        message = "{{  session('message')  }}";
        if (message) {
            alert(message);
            delete message;
        }

        function alertNoticeMessage(sync_value) {
            if (sync_value == 1) {
                alert('选择是，将会更新该员工企业微信信息，请谨慎选择！');
            } else if (sync_value == 0) {
                alert('选择否，将会删除该员工企业微信信息，请谨慎选择！');
            }
        }

    </script>
    <script src="{{  asset('js/bootstrap-treeview.js')  }}"></script>
    <script>
        //时间控件
        $('.datepicker').parent().datepicker({
            "autoclose": true,
            "format": "yyyy-mm-dd",
            "language": "zh-CN"
            // "startDate": "-3d"
        });

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

                        dept_container_childrens = $("#dept_container").children();
                        dept_container_childrens.each(function () {
                            $(this).find("a").click(function () {
                                console.log($(this));
                                $(this).parent().remove();
                                main.find("input[value=" + $(this).prev().attr('value') + "]").parent().remove();
                                dept_leader.find("input[value=" + $(this).prev().attr('value') + "]").parent().remove();
                            });
                        });
                        // $(".tag-i").off("click").click(function(){
                        //     $(this).parent().remove();
                        //     main.find("input[value="+$(this).prev().attr('value')+"]").parent().remove()
                        //     dept_leader.find("input[value=" + $(this).prev().attr('value') + "]").parent().remove();
                        // });
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

    <script src="{{  asset('js/fileinput/fileinput.js')  }}"></script>
    <script>
        $(".input_id").each(function () {
            $(this).fileinput({
                uploadUrl: '{{  route('users.uploadimg',['id' => $user->id])  }}',
                enctype: 'multipart/form-data',
                allowedFileExtensions: ['jpg', 'png', 'bmp', 'jpeg'],
                uploadExtraData: {"_token": $('meta[name="csrf-token"]').attr('content')},
            }).on("fileuploaded", function (event, data, previewId, index) {
                if (data.response.status == "success") {
                    alert(data.response.messages);
                } else {
                    alert(data.response.messages);
                }
            })
        });
    </script>
    <script>
        main = $("#main");
        dept_leader = $("#leader");
        dept_container_children = $("#dept_container").children();
        dept_container_children.each(function () {
            $(this).find("a").click(function () {
                $(this).parent().remove();
                main.find("input[value=" + $(this).prev().attr('value') + "]").parent().remove();
                dept_leader.find("input[value=" + $(this).prev().attr('value') + "]").parent().remove();
            });
        });

        if (dept_container_children.length) {
            if (!$("input[name='pri_dept_id']").length) {
                pri_dept_id = "{{  $user->pri_dept_id  }}";
                dept_container_children.each(function () {
                    if ($(this).find("input").val() == pri_dept_id) {
                        var node = '<label class="custom-control custom-radio custom-control-inline">\n' +
                            '<input type="radio" class="custom-control-input" name="pri_dept_id" value="' + $(this).find("input").val() + '" checked>\n' +
                            '<span class="custom-control-label"  data-toggle="tooltip" title="' + $(this).find('span').first().attr('title') + '" data-placement="bottom">' + $(this).text().trim().substring(0, $(this).text().trim().length - 1) + '</span>\n' +
                            '</label>';
                    } else {
                        var node = '<label class="custom-control custom-radio custom-control-inline">\n' +
                            '<input type="radio" class="custom-control-input" name="pri_dept_id" value="' + $(this).find("input").val() + '">\n' +
                            '<span class="custom-control-label" data-toggle="tooltip" title="' + $(this).find('span').first().attr('title') + '" data-placement="bottom">' + $(this).text().substring(0, $(this).text().length - 1) + '</span>\n' +
                            '</label>';
                    }

                    main.append(node);
                });
            }
        }
    </script>
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
    {{--添加银行卡--}}
    <script>
        function myFunction() {
            var x = document.getElementById("bank_card_num").value;
            var x = x.replace(/\s/g, '').replace(/(\d{4})(?=\d)/g, "$1 ");
            document.getElementById("bank_card_num").value = x;
        }

        $(function () {
            $('.save-bankcard').on('click', function (e) {
                e.preventDefault();
                var data = $('.addBankCard').serialize();
                callPostAjax($(this), data, function (response) {
                    if (response.status == 'success') {
                        alert(response.message);
                        window.location.reload();
                    } else if (response.status == 'error') {
                        alert(response.message);
                    }
                }, function (response) {
                    console.log(response);
                    var valid_message="";
                    if (response.responseJSON.errors) {
                        for (var i in response.responseJSON.errors) {
                            valid_message += response.responseJSON.errors[i][0] + "\n";
                        }
                    } else {
                        valid_message += response.responseJSON.message;
                    }
                    if (valid_message) {
                        alert(valid_message);
                    }
                });
            })
        })
    </script>

    {{--删除银行卡--}}
    <script>
        $(function () {
            $('.delete_bank_card').on('click', function () {
                if (confirm("确定删除银行卡？")) {
                } else {
                    return false;
                }
                callPostAjax($(this), {});
            })
        });
    </script>


    <script>
        //排班
        if ($("#work_type").val() == 1 || $("#work_type").val() == '') {
            $(".work_title").hide();
        }
        $("#work_type").change(function (event) {
            //不等于客服制，可选择班值代码
            if ($("option:selected", this).val() == 1) {
                $(".work_title").hide();
            } else {
                $(".work_title").show();
            }
        })
    </script>
@endsection
