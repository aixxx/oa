@extends("layouts.main",['title' => '会计准则列表'])
@section('content')
    <header class="page-header">
        <div class="d-flex align-items-center">
            <div class="mr-auto">
                <h1 class="separator">会计准则</h1>
                <nav class="breadcrumb-wrapper" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{url('/')}}"><i class="icon dripicons-home"></i></a></li>
                        <li class="breadcrumb-item"><a href="javascript:void(0)"></a>会计准则</li>
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
                        <th class="text-center">会计准则名字</th>
                        <th class="text-center">创建时间</th>
                        <th class="text-center">操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    @if(count($sofzz_list))
                        @foreach($sofzz_list as $_list)
                            <tr id="user_{{  $_list['id']  }}">
                                <td class="text-center">
                                    <a> {{$_list['name']}} </a>
                                </td>
                                <td class="text-center">
                                    <a> {{ date('Y-m-d', $_list['start_time']) }} </a>
                                </td>
                                <td class="text-center">
                                    <a href="{{route('finance.financialManage.sofzzEdit',['id'=>$_list['id']])}}" class="btn btn-primary">编辑</a>
                                    <a href="javascript:void(0);" class="btn btn-danger delete simple_del" data-deleteId="{{ $_list['id'] }}">删除</a>
                                </td>

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
            window.location.href='/financialManage/sofzzAdd';
            return false;
        });

        $('.simple_del').on('click', function () {
            if (confirm('确定要删除吗？')) {
                $.ajax({
                    url: '/financialManage/sofzzDel',
                    type: 'GET',
                    data: {'id' : $(this).attr('data-deleteId')},
                    datatype: 'json',
                    success: function(response) {
                        alert(response.message);
                        if(response.code = 200) {
                            window.location.href = "{{route("finance.financialManage.sofzz")}}";
                        }
                    }
                });
            }
        })
    </script>
@endsection




