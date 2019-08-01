@extends("layouts.main",['title' => '币种管理'])
@section('content')
    <header class="page-header">
        <div class="d-flex align-items-center">
            <div class="mr-auto">
                <h1 class="separator">币种</h1>
                <nav class="breadcrumb-wrapper" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{url('/')}}"><i class="icon dripicons-home"></i></a></li>
                        <li class="breadcrumb-item"><a href="javascript:void(0)"></a>币种</li>
                    </ol>
                </nav>
            </div>
        </div>
    </header>
    <section class="page-content container-fluid">
        <div class="card">
            <h3 class="card-header">
                <form action="{{route('finance.financialManage.coinSearch')}}" method="get">
                    <div class="form-inline form-search-group">
                        <div class="form-group">
                            <input name="coinName" value="{{Request::input('coinName')}}" class="form-control form-search"
                                   placeholder="币种名称或使用国家">
                        </div>
                        <div class="form-group">
                            <button class="btn btn-primary form-search">查询</button>
                            <button type="button" class="btn btn-info form-search clear-search">重置</button>
                            <button class="btn btn-primary form-search add">添加</button>
                        </div>
                    </div>
                </form>

            </h3>
            <div class="table-responsive">
                <table class="table table-hover table-outline table-vcenter text-nowrap card-table">
                    <thead>
                    <tr>
                        <th>币种名称</th>
                        <th>币种代码</th>
                        <th>币种数字代码</th>
                        <th>币种小数位数</th>
                        <th>使用国家</th>
                    </tr>
                    </thead>
                    <tbody>
                    @if(count($coinList))
                        @foreach($coinList as $_coin)
                            <tr id="user_{{  $_coin['id']  }}">
                                <td>
                                    <a> {{$_coin['name']}} </a>
                                </td>
                                <td>
                                    <div class="clearfix">
                                        <div class="float-left">
                                            <div>{{  $_coin['name_code']  }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="clearfix">
                                        <div class="float-left">
                                            <div>{{  $_coin['num_code']  }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="clearfix">
                                        <div class="float-left">
                                            <div>{{  $_coin['format']  }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="clearfix">
                                        <div class="float-left">
                                            <div style="width: 200px; overflow: hidden; text-overflow:ellipsis; white-space: nowrap;">
                                                {{  $_coin['area']  }}
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <td class="text-center">
                                    <div class="item-action dropdown">
                                        <a href="javascript:void(0)" class="btn btn-fab " data-toggle="dropdown" aria-expanded="false" style="padding-top: 0px;">
                                            <i class="icon dripicons-dots-3 rotate-90 font-size-24"></i>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-right">
                                            {{--<a href="{{  route('pendingusers.join',['id' => $user->id])  }}" class="dropdown-item" data-joinId="{{ $user->id }}"><i class="icon la la-user-plus font-size-5 v-align-text-bottom"></i> 入职</a>--}}
                                            {{--<div class="dropdown-divider"></div>--}}
                                            {{--<a href="{{  route('pendingusers.show',['id' => $user->id])  }}" class="dropdown-item"><i class="icon dripicons-view-list"></i> 详情</a>--}}
                                            <div class="dropdown-divider"></div>
                                            <a href="{{  route('finance.financialManage.coinEdit', ['coin_id' => $_coin['id']])  }}" class="dropdown-item edit" data-editId="{{ $_coin['id'] }}" data-target="#editModal"><i class="icon dripicons-pencil"></i> 编辑</a>
                                            <div class="dropdown-divider"></div>
                                            <a href="javascript:void(0)" class="dropdown-item simple_del" data-deleteId="{{ $_coin['id'] }}" data-target="#delModal"><i class="icon dripicons-trash"></i> 删除</a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="7" class="text-center">
                                未找到相关币种
                            </td>
                        </tr>

                    @endif
                    </tbody>
                </table>
            </div>
            <div class="card-footer">
                @if(isset($searchData) and count($searchData))
                    {!! $coinList->appends($searchData)->links() !!}
                @else
                    {!! $coinList->links() !!}
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
        $('.clear-search').click(function () {
            var formObj = $(this).closest('form');
            formObj.find('input,select').each(function () {
                $(this).val('');
                window.location.href='/financialManage/coin';
            });
        });

        $('.add').click(function () {
            window.location.href='/financialManage/coinAdd';
            return false;
        });

        $('.simple_del').on('click', function () {
            if (confirm('确定要删除吗？')) {
                $.ajax({
                    url: '/financialManage/coinDel',
                    type: 'GET',
                    data: {'coin_id' : $(this).attr('data-deleteId')},
                    datatype: 'json',
                    success: function(response) {
                        alert(response.message);
                        if(response.code = 200) {
                            window.location.href = "{{route("finance.financialManage.coin")}}";
                        }
                    }
                });
            }
        })
    </script>
@endsection

