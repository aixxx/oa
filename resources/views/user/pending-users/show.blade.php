@extends('layouts.main',['title' => '待入职员工信息'])

@section('content')
    <header class="page-header">
        <div class="d-flex align-items-center">
            <div class="mr-auto">
                <h1 class="separator">待入职员工信息</h1>
                <nav class="breadcrumb-wrapper" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{url('/')}}"><i class="icon dripicons-home"></i></a></li>
                        <li class="breadcrumb-item"><a href="javascript:void(0)"></a>待入职管理</li>
                        <li class="breadcrumb-item active" aria-current="page">待入职员工信息</li>
                    </ol>
                </nav>
            </div>
        </div>
    </header>
    <section class="page-content container-fluid">
        <div class="card">
            <h3 class="card-header font-size-20">
                <a href="{{  route('pendingusers.edit',['id' => $user->id])  }}"
                   class="btn btn-primary btn-sm line-height-fix float-right">编辑</a>
            </h3>

            <div class="card-body font-size-fix">
                <div class="row">
                    @if($user->name)
                        <div class="col-md-6 text-left">企业微信账号：{{  $user->name  }}</div>
                    @else
                        <div class="col-md-6 text-left">企业微信账号：</div>
                    @endif
                    <div class="col-md-6">员工状态：待入职</div>
                </div>
                <br>
                <div class="row">
                    <div class="col-md-6 text-left">姓名： {{  $user->family_name.$user->given_name  }}</div>
                    <div class="col-md-6">英文名： {{  $user->english_name  }} </div>
                </div>
                <br>
                <div class="row">
                    @if($user->join_at)
                        <div class="col-md-6">预计入职时间：{{  date('Y-m-d',strtotime($user->join_at))  }}</div>
                    @else
                        <div class="col-md-6">预计入职时间：</div>
                    @endif
                    @if($user->work_address)
                        @if($user->work_address == 'shanghai')
                            <div class="col-md-6">工作地点：上海</div>
                        @elseif($user->work_address == 'beijing')
                            <div class="col-md-6">工作地点：北京</div>
                        @elseif($user->work_address == 'chengdu')
                            <div class="col-md-6">工作地点：成都</div>
                        @elseif($user->work_address == 'shenzhen')
                            <div class="col-md-6">工作地点：深圳</div>
                        @endif
                    @else
                        <div class="col-md-6">工作地点：</div>
                    @endif
                </div>

                <br>
                <div class="row">
                    <div class="col-md-6">所属公司： {{  $user->company_name  }}</div>
                    <div class="col-md-6">主部门：{{  $user->department_name  }}</div>
                </div>

                <br>
                <div class="row">
                    <div class="col-md-6 text-left">企业邮箱：{{  $user->email  }}</div>
                    @if($user->mobile)
                        <div class="col-md-6">手机号：{{  decrypt($user->mobile)  }}</div>
                    @else
                        <div class="col-md-6">手机号：</div>
                    @endif
                </div>
                <br>
                <div class="row">
                    <div class="col-md-3 text-left">职位： {{  $user->position  }}</div>
                    <div class="col-md-3"></div>
                    <div class="col-md-3">性别： <?php  echo ($user->gender == \App\Models\User::GENDER_UNKNOWN) ? '未知' :
                            ($user->gender == \App\Models\User::GENDER_MALE ? '男' : '女'); ?></div>
                </div>
                <br>
                <div class="row">
                    @if($user->is_leader == 1)
                        <div class="col-md-6">高管：是</div>
                    @else
                        <div class="col-md-6">高管：否</div>
                    @endif
                    @if($user->is_sync_wechat == 1)
                        <div class="col-md-6 text-left">是否同步：是</div>
                    @else
                        <div class="col-md-6 text-left">是否同步：否</div>
                    @endif
                </div>
            </div>
        </div>
    </section>
@endsection
@section('javascript')
    <script>
        edit_error = "{{  session('editError')  }}";

        if(edit_error)
        {
            alert(edit_error);
        }
    </script>
@endsection