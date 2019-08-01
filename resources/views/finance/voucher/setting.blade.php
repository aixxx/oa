@extends("layouts.main",['title' => '凭证模板设置'])
@section('content')
    <header class="page-header">
        <div class="d-flex align-items-center">
            <div class="mr-auto">
                <h1 class="separator">{{ $voucher['title']}}模板设置</h1>
                <nav class="breadcrumb-wrapper" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{url('/')}}"><i class="icon dripicons-home"></i></a></li>
                        <li class="breadcrumb-item"><a href="javascript:void(0)"></a>{{ $voucher['title']}}模板设置</li>
                    </ol>
                </nav>
            </div>
        </div>
    </header>
    <section class="page-content container-fluid">
        <div class="card">
            <h3 class="card-header">
                <button class="btn btn-primary form-search add" style="float:right;" onclick="voucherAdd({{$voucher['id']}});">添加</button>
            </h3>
            <div class="table-responsive">
                <table class="table table-hover table-outline table-vcenter text-nowrap card-table">
                    <thead>
                    <tr>
                        <th>凭证摘要</th>
                        <th>科目名称</th>
                        <th>余额方向</th>
                        <th>操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    @if(count($voucher_item))
                        @foreach($voucher_item as $_item)
                            <tr id="user_{{  $_item['id']  }}">
                                <td>
                                    <a> {{$_item['title']}} </a>
                                </td>
                                <td>
                                    <div class="clearfix">
                                        <div class="float-left">
                                            {{$_item['name']}}
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="clearfix">
                                        <div class="float-left">
                                            <div>@if($_item['round_way'] == 1)
                                                    借
                                                @else
                                                    贷
                                                @endif</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <a href="{{route('finance.financialManage.voucherItemEdit',['id'=>$_item['id']])}}" class="btn btn-primary">编辑</a>
                                    <a href="javascript:void(0);" class="btn btn-danger delete simple_del" data-deleteId="{{ $_item['id'] }}">删除</a>
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
{{--                                            <a href="{{  route('finance.financialManage.voucherItemEdit', ['id' => $_item['id'] ])  }}" class="dropdown-item edit" data-editId="{{ $_item['id']  }}" data-target="#editModal"><i class="icon dripicons-pencil"></i> 编辑</a>--}}
{{--                                            <div class="dropdown-divider"></div>--}}
{{--                                            <a href="javascript:void(0)" class="dropdown-item simple_del" data-deleteId="{{ $_item['id'] }}" data-target="#delModal"><i class="icon dripicons-trash"></i> 删除</a>--}}
{{--                                        </div>--}}
{{--                                    </div>--}}
{{--                                </td>--}}
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="7" class="text-center">
                                未找到相关凭证项目列表
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
        function voucherAdd(id) {
            window.location.href='/financialManage/voucherItemAdd?v_t_id='+id;
            return false;
        };

        $('.simple_del').on('click', function () {
            if (confirm('确定要删除吗？')) {
                $.ajax({
                    url: '/financialManage/voucherItemDel',
                    type: 'GET',
                    data: {'id' : $(this).attr('data-deleteId')},
                    datatype: 'json',
                    success: function(response) {
                        alert(response.message);
                        if(response.code = 200) {
                            window.location.href = "{{route("finance.financialManage.voucherSetting",['id'=>$voucher['id']])}}";
                        }
                    }
                });
            }
        });

    </script>
@endsection

