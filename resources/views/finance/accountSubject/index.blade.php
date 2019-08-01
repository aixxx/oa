@extends("layouts.main",['title' => '会计科目管理'])
@section('content')
    <header class="page-header">
        <div class="d-flex align-items-center">
            <div class="mr-auto">
                <h1 class="separator">会计科目</h1>
                <nav class="breadcrumb-wrapper" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{url('/')}}"><i class="icon dripicons-home"></i></a></li>
                        <li class="breadcrumb-item"><a href="javascript:void(0)"></a>会计科目</li>
                    </ol>
                </nav>
            </div>
        </div>
    </header>
    <section class="page-content container-fluid">
        <div class="card">
            <h3 class="card-header">
                <form action="{{route('finance.financialManage.accountSubjectSearch')}}" method="get">
                    <div class="form-inline form-search-group">
                        <div class="form-group">
                            <select class="form-control form-search" name="sofzz">
                                <option value="">请选择准则名称</option>
                                @foreach($sofzz as $sofzzName)
                                    <option value="{{$sofzzName['id']}}"
                                            @if(Request::input('sofzz') == $sofzzName['id'])
                                                selected
                                            @endif>{{$sofzzName['name']}}</option>
                                @endforeach
                            </select>
                            <select class="form-control form-search" name="class_type">
                                <option value="">请选择科目类型</option>
                                <option value="1" @if(Request::input('class_type') == 1) selected @endif>成本</option>
                                <option value="2" @if(Request::input('class_type') == 2) selected @endif>负债</option>
                                <option value="3" @if(Request::input('class_type') == 3) selected @endif>共同</option>
                                <option value="4" @if(Request::input('class_type') == 4) selected @endif>损益</option>
                                <option value="5" @if(Request::input('class_type') == 5) selected @endif>所有者权益</option>
                                <option value="6" @if(Request::input('class_type') == 6) selected @endif>资产</option>
                            </select>
                            {{--                        <select class="form-control form-search" name="round_way">--}}
                            {{--                            <option value="">请选择余额方向</option>--}}
                            {{--                            <option value="1">借</option>--}}
                            {{--                            <option value="2">贷</option>--}}
                            {{--                        </select>--}}
                        </div>
                    </div>
                    <div class="form-inline form-search-group">
                        <div class="form-group">
                            <input name="class_name" value="{{Request::input('class_name')}}" class="form-control form-search"
                                   placeholder="科目名称或科目编号">
                        </div>
                        <div class="form-group">
                            <button class="btn btn-primary form-search">查询</button>
                            <button type="button" class="btn btn-info form-search clear-search">重置</button>
                            <button class="btn btn-primary form-search add">添加</button>
                        </div>
                    </div>
                </form>

{{--                <div class="float-right">--}}
{{--                    @if(isset($searchData) and count($searchData))--}}
{{--                        <a href="{{  route('financialManage.accountSubject')  }}" class="btn btn-secondary btn-sm line-height-fix">返回</a>--}}
{{--                    @endif--}}
{{--                    <a href="javascript:void(0)" class="btn btn-accent btn-sm" id="search">搜索</a>--}}
{{--                    <a href="{{  route('finance.financialManage.accountsubjectAdd')  }}" class="btn btn-primary  btn-sm line-height-fix">添加</a>--}}
{{--                </div>--}}
            </h3>
            <div class="table-responsive">
                <table class="table table-hover table-outline table-vcenter text-nowrap card-table">
                    <thead>
                    <tr>
                        <th>准则名称</th>
                        <th>科目名称</th>
                        <th>科目编号</th>
                        <th>科目类型</th>
                        <th>余额方向</th>
                        <th>备注</th>
                    </tr>
                    </thead>
                    <tbody>
                    @if(count($sofclass))
                        @foreach($sofclass as $_sof)
                            <tr id="user_{{  $_sof['id']  }}">
                                <td>
                                    <a> {{$_sof['name']}} </a>
                                </td>
                                <td>
                                    <div class="clearfix">
                                        <div class="float-left">
                                            <div>{{  $_sof['class_name']  }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="clearfix">
                                        <div class="float-left">
                                            <div>{{  $_sof['code']  }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="clearfix">
                                        <div class="float-left">
                                            <div>
                                                @if($_sof['class_type'] == 1)
                                                    成本
                                                @elseif($_sof['class_type'] == 2)
                                                    负债
                                                @elseif($_sof['class_type'] == 3)
                                                    共同
                                                @elseif($_sof['class_type'] == 4)
                                                    损益
                                                @elseif($_sof['class_type'] == 5)
                                                    所有者权益
                                                @elseif($_sof['class_type'] == 6)
                                                    资产
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        @if($_sof['round_way'] == 1)
                                            借
                                        @else
                                            贷
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div class="clearfix">
                                        <div class="float-left">
                                            <div>
                                                {{  $_sof['remark']  }}
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
                                            <a href="{{  route('finance.financialManage.accountSubjectEdit', ['sc_id' => $_sof['id']])  }}" class="dropdown-item edit" data-editId="{{ $_sof['id'] }}" data-target="#editModal"><i class="icon dripicons-pencil"></i> 编辑</a>
                                            <div class="dropdown-divider"></div>
                                            <a href="javascript:void(0)" class="dropdown-item simple_del" data-deleteId="{{ $_sof['id'] }}" data-target="#delModal"><i class="icon dripicons-trash"></i> 删除</a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="7" class="text-center">
                                未找到相关科目
                            </td>
                        </tr>

                    @endif
                    </tbody>
                </table>
            </div>
            <div class="card-footer">
                @if(isset($searchData) and count($searchData))
                    {!! $sofclass->appends($searchData)->links() !!}
                @else
                    {!! $sofclass->links() !!}
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
                window.location.href='/financialManage/accountSubject';
            });
        });

        $('.add').click(function () {
            window.location.href='/financialManage/accountSubjectAdd';
            return false;
        });

        $('.simple_del').on('click', function () {
            if (confirm('确定要删除吗？')) {
                $.ajax({
                    url: '/financialManage/accountSubjectDel',
                    type: 'GET',
                    data: {'sc_id' : $(this).attr('data-deleteId')},
                    datatype: 'json',
                    success: function(response) {
                        alert(response.message);
                        if(response.code = 200) {
                            window.location.href = "{{route("finance.financialManage.accountSubject")}}";
                        }
                    }
                });
            }
        })
    </script>
@endsection

