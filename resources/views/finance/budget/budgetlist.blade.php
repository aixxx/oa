@extends("layouts.main",['title' => $one_budget['title'].'编辑'])

@section('content')
    <header class="page-header">
        <div class="d-flex align-items-center">
            <div class="mr-auto">
                <h1 class="separator">{{ $one_budget['title'] }}编辑</h1>
                <nav class="breadcrumb-wrapper" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{url('/')}}"><i class="icon dripicons-home"></i></a></li>
                        <li class="breadcrumb-item"><a href="javascript:void(0)"></a>费控预算管理</li>
                        <li class="breadcrumb-item active" aria-current="page">{{ $one_budget['title'] }}编辑</li>
                    </ol>
                </nav>
            </div>
        </div>
    </header>
    <section class="page-content container-fluid">
        <div class="card">
            <div class="card-body" data-id="{{ $one_budget['id'] }}">
                <div class="form-group">
                    <label class="col-form-label">所属:{{ $one_budget['dep_name'] }}-{{ $one_budget['project_name'] }}</label>
{{--                    <label class="col-form-label">所属:@if($one_budget['organ_name']){{ $one_budget['organ_name'] }}-@endif{{ $one_budget['dep_name'] }}-{{ $one_budget['project_name'] }}</label>--}}
                </div>
                <div class="form-group">
                    <label class="col-form-label">收入预算:&nbsp;&nbsp;{{ $one_budget['inc'] }}元</label>
                </div>
                <div class="form-group">
                    <label class="col-form-label">支出预算:&nbsp;&nbsp;{{ $one_budget['exp'] }}元</label>
                </div>
                <div class="form-group">
                    <label class="col-form-label">是否锁死</label><label
                            class="col-form-label text-danger">*</label>
                    <select name="is_lock" class="form-control is_lock" style="width: 70px;">
                        <option value="1"@if(isset($one_budget['is_lock']) && $one_budget['is_lock'] == 1) selected @endif>是</option>
                        <option value="0"@if(isset($one_budget['is_lock']) && $one_budget['is_lock'] == 0) selected @endif>否</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="col-form-label">是否超支</label><label
                            class="col-form-label text-danger">*</label>
                    <select name="is_over" class="form-control is_over" style="width: 70px;">
                        <option value="1"@if(isset($one_budget['is_over']) && $one_budget['is_over'] == 1) selected @endif>是</option>
                        <option value="0"@if(isset($one_budget['is_over']) && $one_budget['is_over'] == 0) selected @endif>否</option>
                    </select>
                </div>

                <div class="form-inline" id="form-condition">

                </div>

        </div>
        <div class="card">

            <div class="table-responsive">
                <table class="table table-hover table-outline table-vcenter text-nowrap card-table">
                    <thead>
                    <tr>
                        <th>费控类目</th>
                        <th>说明</th>
                        <th>是否锁死</th>
                        <th>是否超支</th>
                        <th>更新时间</th>
                    </tr>
                    </thead>
                    <tbody>
                    @if(count($category))
                        @foreach($category as $_budget)
                            <tr>
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
                                            <div data-id="{{ $_budget['id'] }}">
                                                <select name="is_lock" class="form-control category_is_lock" style="width: 70px;">
                                                    <option value="1"@if(isset($_budget['is_lock']) && $_budget['is_lock'] == 1) selected @endif>是</option>
                                                    <option value="0"@if(isset($_budget['is_lock']) && $_budget['is_lock'] == 0) selected @endif>否</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="clearfix">
                                        <div class="float-left">
                                            <div data-id="{{ $_budget['id'] }}">
                                                <select name="is_over" class="form-control category_is_over" style="width: 70px;">
                                                    <option value="1"@if(isset($_budget['is_over']) && $_budget['is_over'] == 1) selected @endif>是</option>
                                                    <option value="0"@if(isset($_budget['is_over']) && $_budget['is_over'] == 0) selected @endif>否</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="clearfix">
                                        <div class="float-left">
                                            <div>{{ date('Y-m-d H:i:s', $_budget['created_time']) }} </div>
                                        </div>
                                    </div>
                                </td>

                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="7" class="text-center">
                                未找到相关费控类目，请去添加类目。
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

        $('.simple_del').on('click', function () {
            if (confirm('确定要删除吗？')) {
                $.ajax({
                    url: '/financialManage/budgetDel',
                    type: 'GET',
                    data: {'id' : $(this).attr('data-deleteId')},
                    datatype: 'json',
                    success: function(response) {
                        alert(response.message);
                        if(response.code = 200) {
                            window.location.href = "{{route("finance.financialManage.budgetSetting",['id'=>$cost_budget_id])}}";
                        }
                    }
                });
            }
        });

        $('.is_lock').on('change', function () {
            var id = $(this).parent().parent().attr('data-id');
            $.ajax({
                url: '/financialManage/budgetChange',
                type: 'GET',
                data: {'id' : id, 'is_lock' : $(this).val()},
                datatype: 'json',
                success: function(response) {
                    alert(response.message);
                    if(response.code = 200) {
                        location.reload();
                        // window.location.href = "/financialManage/budgetSetting?id="+id;
                    }
                }
            });
        })

        $('.is_over').on('change', function () {
            var id = $(this).parent().parent().attr('data-id');
            $.ajax({
                url: '/financialManage/budgetChange',
                type: 'GET',
                data: {'id' : id, 'is_over' : $(this).val()},
                datatype: 'json',
                success: function(response) {
                    alert(response.message);
                    if(response.code = 200) {
                        location.reload();
                    }
                }
            });
        })


        $('.category_is_lock').on('change', function () {
            var id = $(this).parent().attr('data-id');
            $.ajax({
                url: '/financialManage/budgetItemChange',
                type: 'GET',
                data: {'id' : id, 'is_lock' : $(this).val()},
                datatype: 'json',
                success: function(response) {
                    alert(response.message);
                    if(response.code = 200) {
                        location.reload();
                    }
                }
            });
        })

        $('.category_is_over').on('change', function () {
            var id = $(this).parent().attr('data-id');
            $.ajax({
                url: '/financialManage/budgetItemChange',
                type: 'GET',
                data: {'id' : id, 'is_over' : $(this).val()},
                datatype: 'json',
                success: function(response) {
                    alert(response.message);
                    if(response.code = 200) {
                        location.reload();
                    }
                }
            });
        })

    </script>
@endsection






