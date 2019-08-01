@extends("layouts.main",['title' => '结算类型'])
@section('content')
    <header class="page-header">
        <div class="d-flex align-items-center">
            <div class="mr-auto">
                <h1 class="separator">结算类型</h1>
                <nav class="breadcrumb-wrapper" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{url('/')}}"><i class="icon dripicons-home"></i></a></li>
                        <li class="breadcrumb-item"><a href="javascript:void(0)"></a>结算类型</li>
                    </ol>
                </nav>
            </div>
        </div>
    </header>
    <section class="page-content container-fluid">
        <div class="card">
            <h3 class="card-header">
                <button class="btn btn-primary form-search add" style="float:right;">添加</button>
            </h3>
            <div class="table-responsive">
                <table class="table table-hover table-outline table-vcenter text-nowrap card-table">
                    <thead>
                    <tr>
                        <th class="text-center">结算类型名字</th>
                        <th class="text-center">操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    @if(count($balance_list))
                        @foreach($balance_list as $_list)
                            <tr id="user_{{  $_list['id']  }}">
                                <td class="text-center">
                                    <a> {{$_list['balance_name']}} </a>
                                </td>
                                <td class="text-center">
                                    <a href="{{route('finance.financialManage.balanceEdit',['id'=>$_list['id']])}}" class="btn btn-primary">编辑</a>
                                    <a href="javascript:void(0);" class="btn btn-danger delete simple_del" data-deleteId="{{ $_list['id'] }}">删除</a>
                                </td>


                                {{--                                <td class="text-center">--}}
{{--                                    <div class="item-action dropdown">--}}
{{--                                        <a href="javascript:void(0)" class="btn btn-fab " data-toggle="dropdown" aria-expanded="false" style="padding-top: 0px;">--}}
{{--                                            <i class="icon dripicons-dots-3 rotate-90 font-size-24"></i>--}}
{{--                                        </a>--}}
{{--                                        <div class="dropdown-menu dropdown-menu-right">--}}
{{--                                            --}}{{--<a href="{{  route('pendingusers.join',['id' => $user->id])  }}" class="dropdown-item" data-joinId="{{ $user->id }}"><i class="icon la la-user-plus font-size-5 v-align-text-bottom"></i> 入职</a>--}}
{{--                                            --}}{{--<div class="dropdown-divider"></div>--}}
{{--                                            --}}{{--<a href="{{  route('pendingusers.show',['id' => $user->id])  }}" class="dropdown-item"><i class="icon dripicons-view-list"></i> 详情</a>--}}
{{--                                            <div class="dropdown-divider"></div>--}}
{{--                                            <a href="{{  route('finance.financialManage.balanceEdit', ['id' => $_list['id'] ])  }}" class="dropdown-item edit" data-editId="{{ $_list['id']  }}" data-target="#editModal"><i class="icon dripicons-pencil"></i> 编辑</a>--}}
{{--                                            <div class="dropdown-divider"></div>--}}
{{--                                            <a href="javascript:void(0)" class="dropdown-item simple_del" data-deleteId="{{ $_list['id'] }}" data-target="#delModal"><i class="icon dripicons-trash"></i> 删除</a>--}}
{{--                                        </div>--}}
{{--                                    </div>--}}
{{--                                </td>--}}

                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="7" class="text-center">
                                未找到相关结算类型
                            </td>
                        </tr>

                    @endif
                    </tbody>
                </table>
            </div>
        </div>
    </section>
@endsection
@section('javascript')
    <script src="/static/vendor/bootstrap-datepicker/bootstrap-datepicker.min.js"></script>
    <script src="/static/vendor/bootstrap-daterangepicker/daterangepicker.js"></script>
    <script src="/static/js/components/bootstrap-datepicker-init.js"></script>
    <script>

        $('.add').click(function () {
            window.location.href='/financialManage/balanceAdd';
            return false;
        });

        $('.simple_del').on('click', function () {
            if (confirm('确定要删除吗？')) {
                $.ajax({
                    url: '/financialManage/balanceDel',
                    type: 'GET',
                    data: {'id' : $(this).attr('data-deleteId')},
                    datatype: 'json',
                    success: function(response) {
                        alert(response.message);
                        if(response.code = 200) {
                            window.location.href = "{{route("finance.financialManage.balance")}}";
                        }
                    }
                });
            }
        })
    </script>
@endsection




