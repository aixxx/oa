@extends("layouts.main",['title' => '员工假期管理'])

@section('content')
    <header class="page-header">
        <div class="d-flex align-items-center">
            <div class="mr-auto">
                <h1 class="separator">员工假期管理</h1>
                <nav class="breadcrumb-wrapper" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{url('/')}}"><i class="icon dripicons-home"></i></a></li>
                        <li class="breadcrumb-item"><a href="javascript:void(0)"></a>考勤管理</li>
                        <li class="breadcrumb-item active" aria-current="page">员工假期管理</li>
                    </ol>
                </nav>
            </div>
        </div>
    </header>
    <section class="page-content container-fluid">
        <header class="panel-heading font-bold">提示：重新导入员工数据(格式同下载，导哪个员工修改哪个员工)</header>
        <div class="card">
            <div class="card-body">
                <form class="form-horizontal" data-validate="parsley" action="{{  route('vacationManage.addLastYearVacations')  }}" method="post"
                      enctype="multipart/form-data">
                    @csrf
                    <div class="panel-body">
                        <div class="clearfix pull-left">
                            <div class="">
                                <label for="uid_file" class="col-xs-1 control-label">
                                    <input type="radio" id="upload_type" name="upload_type" value="uid" checked>&nbsp;上传文件</label>
                                <input type="file" id="order_process_file2" name="order_process_file2" class="filestyle"
                                       data-classbutton="btn btn-default"
                                       data-classinput="form-control inline v-middle input-s">
                                <button id="submit" class="btn btn-success btn-s-xs upload2">上传</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <h3 class="card-header">
                    <form action="{{  route('vacationManage.search')  }}" method="get">
                        <div class="form-inline">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-label control-label">员工姓名：</label>
                                    <input class="form-control" name="user_name" value="{{$userName?:''}}" style="width: 50%"/>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-label control-label" style="justify-content: left;">员工编号：</label>
                                    <input class="form-control" name="user_num" value="{{$userNum?:''}}" style="width: 50%"/>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <button type="submit" class="btn btn-success">查询</button>
                            </div>
                        </div>
                    </form>
                </h3>
            </div>
        </div>
        <div class="card">
            <div class="card-header">
                <div class="pull-left">
                    <h3 class="card-title" style="margin-top: 8px;">员工可用假期表(单位／小时)</h3>
                </div>
                <div class="pull-right">
                    <a href="{{  route('vacationManage.downloadVacationDate')  }}" class="btn btn-primary  btn-sm line-height-fix">员工假期数据下载</a>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover table-bordered" style="min-width:100%">
                        <thead>
                        <tr>
                            <th class="text-center">员工姓名</th>
                            <th class="text-center">员工编号</th>
                            <th class="text-center">法定年假</th>
                            <th class="text-center">应发法定年假</th>
                            <th class="text-center">实发法定年假</th>
                            <th class="text-center">公司福利年假</th>
                            <th class="text-center">应发福利年假</th>
                            <th class="text-center">实发福利年假</th>
                            <th class="text-center">全薪病假</th>
                            <th class="text-center">实发全薪病假</th>
                            <th class="text-center">调休</th>
                        </tr>
                        @if(count($vacations))
                            @foreach($vacations as $vacation)
                                <tr data-key="0">
                                    @php
                                        $user = $vacation->user;
                                        $restVacation  = App\Services\VacationManageService::getRestVacation($vacation);
                                    @endphp
                                    <th class="text-center">{{ $user?$user->chinese_name:'' }}</th>
                                    <th class="text-center">{{ $user?$user->getPrefixEmployeeNum():'' }}</th>
                                    <th class="text-center">{{ floor($vacation->annual) }}
                                        @if($restVacation['restAnnual']>0)
                                            <span class="font-size-12">{{'含去年'.$restVacation['restAnnual']}}</span>
                                        @endif
                                    </th>
                                    <th class="text-center">{{ $restVacation['totalAnnualHour'] }}</th>
                                    <th class="text-center">{{ $vacation->actual_annual}}</th>
                                    <th class="text-center">{{ floor($vacation->company_benefits) }}
                                        @if($restVacation['restBenefit'])
                                            <span class="font-size-12">{{'含去年'.$restVacation['restBenefit']}}</span>
                                        @endif
                                    </th>
                                    <th class="text-center">{{ $restVacation['totalBenefitHour'] }}</th>
                                    <th class="text-center">{{  $vacation->actual_company_benefits }}</th>
                                    <th class="text-center">{{ floor($vacation->full_pay_sick) }}</th>
                                    <th class="text-center">{{ floor($vacation->actual_full_pay_sick) }}</th>
                                    <th class="text-center">{{ $vacation->extra_day_off }}</th>
                                </tr>
                            @endforeach
                        @endif
                        </thead>
                    </table>
                </div>
                <div class="card-footer">
                    @if(isset($userName) && isset($userNum))
                        {!! $vacations->appends(['user_name' => $userName,'user_num'=>$userNum?:''])->links() !!}
                    @else
                        {!! $vacations->links() !!}
                    @endif
                </div>
            </div>
        </div>
    </section>

@endsection
@section('javascript')
    <script>
        $(function () {
            var err = '<?php echo isset($err) ? $err : '' ?>';
            if (err) {
                alert(err);
            }

            // 表单提交拦截
            $(".upload1").click(function () {
                if ($('#order_process_file').val() === '') {
                    alert("请选择上传文件");
                    return false;
                }
            });
            $(".upload2").click(function () {
                if ($('#order_process_file2').val() === '') {
                    alert("请选择上传文件");
                    return false;
                }
            });

        });
    </script>
    <script>
        addVacationSuccess = "{{  session('addVacationSuccess')  }}";
        if (addVacationSuccess) {
            alert(addVacationSuccess);
            delete addVacationSuccess;
        }
    </script>
@endsection

