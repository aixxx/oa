@extends("layouts.main",['title' => '客户标签设置'])
@section('content')
    <header class="page-header">
        <div class="d-flex align-items-center">
            <div class="mr-auto">
                <h1 class="separator">客户标签</h1>
                <nav class="breadcrumb-wrapper" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{url('/')}}"><i class="icon dripicons-home"></i></a></li>
                        <li class="breadcrumb-item"><a href="javascript:void(0)"></a>客户标签</li>
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
                        <th>标签名称</th>
                        <th>折扣率</th>
                        <th>付款期限</th>
                        <th>排序</th>
                        <th class="text-center">操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    @if(count($types))
                        @foreach($types as $_type)
                            <tr id="user_{{  $_type['id']  }}">
                                <td>
                                    <a> {{$_type['type_name']}} </a>
                                </td>
                                <td>
                                    <div class="clearfix">
                                        <div class="float-left">
                                            <div>{{  $_type['discount']  }}%</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="clearfix">
                                        <div class="float-left">
                                            <div>{{  $_type['payment_day']  }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="clearfix">
                                        <div class="float-left">
                                            <div>{{  $_type['orderby']  }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <a href="{{route('customer.customerManage.customerTypeEdit',['id'=>$_type['id']])}}" class="btn btn-primary">编辑</a>
                                    <a href="javascript:void(0);" class="btn btn-danger delete simple_del" data-deleteId="{{ $_type['id'] }}">删除</a>
                                </td>

                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="7" class="text-center">
                                未找到相关客户标签
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
        $('.add').click(function () {
            window.location.href='/customerManage/customerTypeAdd';
            return false;
        });

        $('.simple_del').on('click', function () {
            if (confirm('确定要删除吗？')) {
                $.ajax({
                    url: '/customerManage/customerTypeDel',
                    type: 'GET',
                    data: {'id' : $(this).attr('data-deleteId')},
                    datatype: 'json',
                    success: function(response) {
                        alert(response.message);
                        if(response.code = 200) {
                            window.location.href = "{{route("customer.customerManage.customerType")}}";
                        }
                    }
                });
            }
        })
    </script>
@endsection

