@extends("layouts.main",['title' => '费控预算管理'])
@section('content')
    <header class="page-header">
        <div class="d-flex align-items-center">
            <div class="mr-auto">
                <h1 class="separator">费控预算</h1>
                <nav class="breadcrumb-wrapper" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{url('/')}}"><i class="icon dripicons-home"></i></a></li>
                        <li class="breadcrumb-item"><a href="javascript:void(0)"></a>费控预算</li>
                    </ol>
                </nav>
            </div>
        </div>
    </header>
    <section class="page-content container-fluid">
        <div class="card">
            <h3 class="card-header">
                <button class="btn btn-primary category">费控模板管理</button>
                <button type="button" class="btn btn-info dimension">维度库管理</button>
            </h3>
            <div class="table-responsive">
                <table class="table table-hover table-outline table-vcenter text-nowrap card-table">
                    <thead>
                    <tr>
                        <th>费控预算单名称</th>
                        <th>费控项目</th>
{{--                        <th>费控组织</th>--}}
                        <th>费控部门</th>
                        <th>是否锁死</th>
                        <th>是否可超支</th>
                        <th>收入预算</th>
                        <th>支出预算</th>
                        <th>创建时间</th>
                        <th>操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    @if(count($cost_budget))
                        @foreach($cost_budget as $_budget)
                            <tr id="user_{{  $_budget['id']  }}">
                                <td>
                                    <a> {{$_budget['title']}} </a>
                                </td>
                                <td>
                                    <div class="clearfix">
                                        <div class="float-left">
                                            <div>{{  $_budget['project_name']  }}</div>
                                        </div>
                                    </div>
                                </td>
{{--                                <td>--}}
{{--                                    <div class="clearfix">--}}
{{--                                        <div class="float-left">--}}
{{--                                            <div>{{  $_budget['organ_name']  }}</div>--}}
{{--                                        </div>--}}
{{--                                    </div>--}}
{{--                                </td>--}}
                                <td>
                                    <div class="clearfix">
                                        <div class="float-left">
                                            <div>{{  $_budget['dep_name']  }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="clearfix">
                                        <div class="float-left">
                                            <div class="float-left">
                                                @if($_budget['is_lock'] == 1) 是 @else 否@endif
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <td>
                                    <div class="clearfix">
                                        <div class="float-left">
                                            <div class="float-left">
                                                @if($_budget['is_over'] == 1) 是 @else 否@endif
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <td>
                                    <div class="clearfix">
                                        <div class="float-left">
                                            <div class="float-left">
                                                {{  $_budget['inc']  }}
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <td>
                                    <div class="clearfix">
                                        <div class="float-left">
                                            <div class="float-left">
                                                {{  $_budget['exp']  }}
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <td>
                                    <div class="clearfix">
                                        <div class="float-left">
                                            <div class="float-left">
                                                {{ date('Y-m-d', $_budget['created_time'])  }}
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <td>
                                    <a href="{{route('finance.financialManage.budgetSetting',['id'=>$_budget['id']])}}" class="btn btn-primary">编辑</a>
                                </td>

{{--                                <td class="text-center">--}}
{{--                                    <div class="item-action dropdown">--}}
{{--                                        <a href="javascript:void(0)" class="btn btn-fab " data-toggle="dropdown" aria-expanded="false" style="padding-top: 0px;">--}}
{{--                                            <i class="icon dripicons-dots-3 rotate-90 font-size-24"></i>--}}
{{--                                        </a>--}}
{{--                                        <div class="dropdown-menu dropdown-menu-right">--}}
{{--                                            <div class="dropdown-divider"></div>--}}
{{--                                            <a href="{{  route('finance.financialManage.coinEdit', ['coin_id' => $_coin['id']])  }}" class="dropdown-item edit" data-editId="{{ $_coin['id'] }}" data-target="#editModal"><i class="icon dripicons-pencil"></i> 编辑</a>--}}
{{--                                            <div class="dropdown-divider"></div>--}}
{{--                                            <a href="javascript:void(0)" class="dropdown-item simple_del" data-deleteId="{{ $_coin['id'] }}" data-target="#delModal"><i class="icon dripicons-trash"></i> 删除</a>--}}
{{--                                        </div>--}}
{{--                                    </div>--}}
{{--                                </td>--}}
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="7" class="text-center">
                                未找到相关费控预算
                            </td>
                        </tr>

                    @endif
                    </tbody>
                </table>
            </div>
                        <div class="card-footer">
                            @if(isset($searchData) and count($searchData))
                                {!! $cost_budget->appends($searchData)->links() !!}
                            @else
                                {!! $cost_budget->links() !!}
                            @endif
                        </div>
        </div>
    </section>
@endsection
@section('javascript')
    <script src="/static/vendor/bootstrap-datepicker/bootstrap-datepicker.min.js"></script>
    <script src="/static/vendor/bootstrap-daterangepicker/daterangepicker.js"></script>
    <script src="/static/js/components/bootstrap-datepicker-init.js"></script>
    <script>

        $('.dimension').click(function () {
            window.location.href='/financialManage/budgetDimension';
            return false;
        });

        $('.category').click(function () {
            window.location.href='/financialManage/budgetCategory';
            return false;
        });


    </script>
@endsection

