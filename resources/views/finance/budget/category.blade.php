@extends("layouts.main",['title' => '费控模板管理'])

@section('content')
    <header class="page-header">
        <div class="d-flex align-items-center">
            <div class="mr-auto">
                <h1 class="separator">费控模板管理</h1>
                <nav class="breadcrumb-wrapper" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{url('/')}}"><i class="icon dripicons-home"></i></a></li>
                        <li class="breadcrumb-item"><a href="javascript:void(0)"></a>费控预算</li>
                        <li class="breadcrumb-item active" aria-current="page">费控模板管理</li>
                    </ol>
                </nav>
            </div>
        </div>
    </header>
    <section class="page-content container-fluid">
        <div class="card">
               <h3 class="card-header">
			   <button class="btn btn-info expenses_all">全部</button>
                <button class="btn btn-primary  income">收入</button>
                <button type="button" class="btn btn-primary expenses">支出</button>
            </h3>
            <div class="table-responsive">
                <table class="table table-hover table-outline table-vcenter text-nowrap card-table">
                    <thead>
                    <tr>
                        <th>费控类目</th>
                        <th>说明</th>
                        <th>是否锁死</th>
                        <th>是否超支</th>
                        <th>收支类型</th>
                        <th>更新时间</th>
                        <th>状态</th>
                        <th>操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    @if(count($category))
                        @foreach($category as $_budget)
                            <tr id="user_{{  $_budget['id']  }}">
                                <td>
                                    <a> {{$_budget['title']}} </a>
                                </td>
                                <td>
                                    <div class="clearfix">
                                        <div class="float-left">
                                            <div>{{  $_budget['explain']  }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="clearfix">
                                        <div class="float-left">
                                            <div>@if($_budget['is_lock'] ==1)是@else否@endif</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="clearfix">
                                        <div class="float-left">
                                            <div>@if($_budget['is_over'] ==1)是@else否@endif</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="clearfix">
                                        <div class="float-left">
                                            <div>@if($_budget['type'] ==1)收入@else支出@endif</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="clearfix">
                                        <div class="float-left">
                                            <div>{{ $_budget['created_time'] != 0?date('Y-m-d H:i:s', $_budget['created_time']):'' }} </div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="clearfix">
                                        <div class="float-left">
                                            <div>{{$_budget['is_seted_title']}}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if($_budget['created_time'] != 0)
                                    <a href="{{route('finance.financialManage.budgetPoint',['id'=>$_budget['cb_id']])}}" class="btn btn-primary">设置费控点</a>
                                    @endif
                                    <a href="{{route('finance.financialManage.budgetCategoryEdit',['id'=>$_budget['id']])}}" class="btn btn-primary">编辑</a>
{{--                                    <a href="javascript:void(0);" class="btn btn-danger delete simple_del" data-deleteId="{{ $_budget['id'] }}">删除</a>--}}
                                </td>

                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="7" class="text-center">
                                未找到相关费控类目
                            </td>
                        </tr>

                    @endif
                    </tbody>
                </table>
            </div>
            {{--            <div class="card-footer">--}}
            {{--                @if(isset($searchData) and count($searchData))--}}
            {{--                    {!! $sofclass->appends($searchData)->links() !!}--}}
            {{--                @else--}}
            {{--                    {!! $sofclass->links() !!}--}}
            {{--                @endif--}}
            {{--            </div>--}}
        </div>
    </section>
@endsection
@section('javascript')
    <script src="/static/vendor/bootstrap-datepicker/bootstrap-datepicker.min.js"></script>
    <script src="/static/vendor/bootstrap-daterangepicker/daterangepicker.js"></script>
    <script src="/static/js/components/bootstrap-datepicker-init.js"></script>
    <script>
	
	$('.expenses_all').click(function () {
            window.location.href= "{{route('finance.financialManage.budgetCategory')}}";
            return false;
        });
	  $('.income').click(function () {
            window.location.href= "{{route('finance.financialManage.budgetCategory')}}?type=1";
            return false;
        });

        $('.expenses').click(function () {
            window.location.href="{{route('finance.financialManage.budgetCategory')}}?type=2";
            return false;
        });

        $('.simple_del').on('click', function () {
            if (confirm('确定要删除吗？')) {
                $.ajax({
                    url: '/financialManage/budgetCategoryDel',
                    type: 'GET',
                    data: {'id' : $(this).attr('data-deleteId')},
                    datatype: 'json',
                    success: function(response) {
                        alert(response.message);
                        if(response.code == 200) {
                            window.location.href = "{{route("finance.financialManage.budgetCategory")}}";
                        }
                    }
                });
            }
        })
    </script>
@endsection






