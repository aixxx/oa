@extends("layouts.main",['title' => '维度库管理'])
@section('content')
    <header class="page-header">
        <div class="d-flex align-items-center">
            <div class="mr-auto">
                <h1 class="separator">费控维度库管理</h1>
                <nav class="breadcrumb-wrapper" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{url('/')}}"><i class="icon dripicons-home"></i></a></li>
                        <li class="breadcrumb-item"><a href="javascript:void(0)"></a>费控维度库管理</li>
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
                        <th class="text-center">维度名称</th>
                        <th class="text-center">操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    @if(count($dimension))
                        @foreach($dimension as $_dimension)
                            <tr id="user_{{  $_dimension['id']  }}">
                                <td class="text-center">
                                    <a> {{$_dimension['title']}} </a>
                                </td>

                                <td class="text-center">
                                    <a href="{{route('finance.financialManage.budgetDimensionEdit',['id'=>$_dimension['id']])}}" class="btn btn-primary">编辑</a>
                                    <a href="javascript:void(0);" class="btn btn-danger delete simple_del" data-deleteId="{{ $_dimension['id'] }}">删除</a>
                                </td>

                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="7" class="text-center">
                                未找到相关费控维度
                            </td>
                        </tr>

                    @endif
                    </tbody>
                </table>
            </div>
{{--                        <div class="card-footer">--}}
{{--                            @if(isset($searchData) and count($searchData))--}}
{{--                                {!! $cost_budget->appends($searchData)->links() !!}--}}
{{--                            @else--}}
{{--                                {!! $cost_budget->links() !!}--}}
{{--                            @endif--}}
{{--                        </div>--}}
        </div>
    </section>
@endsection
@section('javascript')
    <script src="/static/vendor/bootstrap-datepicker/bootstrap-datepicker.min.js"></script>
    <script src="/static/vendor/bootstrap-daterangepicker/daterangepicker.js"></script>
    <script src="/static/js/components/bootstrap-datepicker-init.js"></script>
    <script>

        $('.add').click(function () {
            window.location.href='/financialManage/budgetDimensionAdd';
            return false;
        });

    </script>
@endsection

