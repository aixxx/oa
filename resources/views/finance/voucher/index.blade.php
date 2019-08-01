@extends("layouts.main",['title' => '凭证模板'])
@section('content')
    <header class="page-header">
        <div class="d-flex align-items-center">
            <div class="mr-auto">
                <h1 class="separator">凭证模板</h1>
                <nav class="breadcrumb-wrapper" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{url('/')}}"><i class="icon dripicons-home"></i></a></li>
                        <li class="breadcrumb-item"><a href="javascript:void(0)"></a>凭证模板</li>
                    </ol>
                </nav>
            </div>
        </div>
    </header>
    <section class="page-content container-fluid">
        <div class="card">
            <h3 class="card-header">
                <form action="{{route('finance.financialManage.voucher')}}" method="get">
                    <div class="form-inline form-search-group">
                        <div class="form-group">
                            <select class="form-control form-search" name="sof_id">
                                <option value="">请选择账套名称</option>
                                @foreach($sofinfo as $_info)
                                    <option value="{{$_info['sof_id']}}"
                                            @if(Request::input('sof_id') == $_info['sof_id'])
                                            selected
                                            @endif>{{$_info['sof_name']}}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <button class="btn btn-primary form-search">查询</button>
                            <button class="btn btn-primary form-search add" style="float:right;">添加</button>
                        </div>
                    </div>
                </form>
            </h3>
            <div class="table-responsive">
                <table class="table table-hover table-outline table-vcenter text-nowrap card-table">
                    <thead>
                    <tr>
                        <th>账套名称</th>
                        <th>模板名称</th>
                        <th>模板类型</th>
                        <th class="text-center">操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    @if(count($voucher_list))
                        @foreach($voucher_list as $_list)
                            <tr id="user_{{  $_list['id']  }}">
                                <td>
                                    <a> {{$_list['sof_name']}} </a>
                                </td>
                                <td>
                                    <a> {{$_list['title']}} </a>
                                </td>
                                <td>
                                    <a> {{$_list['sofz_name']}} </a>
{{--                                    <div class="clearfix">--}}
{{--                                        <div class="float-left">--}}
{{--                                            <div>@if($_list['type'] == 1)--}}
{{--                                                    记--}}
{{--                                                @elseif($_list['type'] == 2)--}}
{{--                                                    收--}}
{{--                                                @elseif($_list['type'] == 3)--}}
{{--                                                    付--}}
{{--                                                @elseif($_list['type'] == 4)--}}
{{--                                                    转--}}
{{--                                                @endif</div>--}}
{{--                                        </div>--}}
{{--                                    </div>--}}
                                </td>

                                <td class="text-center">
                                    <a href="{{route('finance.financialManage.voucherSetting',['id'=>$_list['id']])}}" class="btn btn-primary">设置模板</a>
                                    <a href="{{ route('finance.financialManage.voucherEdit', ['id' => $_list['id'] ])  }}" class="btn btn-primary" data-editId="{{ $_list['id']  }}" data-target="#editModal">编辑</a>
                                    <a href="javascript:void(0)" class="btn btn-danger delete simple_del"  data-deleteId="{{ $_list['id'] }}" data-target="#delModal">删除</a>
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
{{--                                            <a href="{{  route('finance.financialManage.voucherEdit', ['id' => $_list['id'] ])  }}" class="dropdown-item edit" data-editId="{{ $_list['id']  }}" data-target="#editModal"><i class="icon dripicons-pencil"></i> 编辑</a>--}}
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
                                未找到相关凭证模板
                            </td>
                        </tr>

                    @endif
                    </tbody>
                </table>
            </div>
            <div class="card-footer">
                @if(isset($searchData) and count($searchData))
                    {!! $voucher_list->appends($searchData)->links() !!}
                @else
                    {!! $voucher_list->links() !!}
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

        $('.add').click(function () {
            window.location.href='/financialManage/voucherAdd';
            return false;
        });

        $('.simple_del').on('click', function () {
            if (confirm('确定要删除吗？')) {
                $.ajax({
                    url: '/financialManage/voucherDel',
                    type: 'GET',
                    data: {'id' : $(this).attr('data-deleteId')},
                    datatype: 'json',
                    success: function(response) {
                        alert(response.message);
                        if(response.code = 200) {
                            window.location.href = "{{route("finance.financialManage.voucher")}}";
                        }
                    }
                });
            }
        })
    </script>
@endsection




