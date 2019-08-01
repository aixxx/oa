@extends("layouts.main",['title' => $one_budget['title'].'类目'])

@section('content')
    <header class="page-header">
        <div class="d-flex align-items-center">
            <div class="mr-auto">
                <h1 class="separator">{{ $one_budget['title'] }}类目@if(isset($budget_item))编辑@else添加@endif</h1>
                <nav class="breadcrumb-wrapper" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{url('/')}}"><i class="icon dripicons-home"></i></a></li>
                        <li class="breadcrumb-item"><a href="javascript:void(0)"></a>费控预算管理</li>
                        <li class="breadcrumb-item active" aria-current="page">{{ $one_budget['title'] }}类目@if(isset($budget_item))编辑@else添加@endif</li>
                    </ol>
                </nav>
            </div>
        </div>
    </header>
    <section class="page-content container-fluid">
        <div class="card">
            <div class="card-body">
                {{--                <form action="{{  route('finance.financialManage.accountSubjectStore')  }}" method="POST">--}}
                @csrf
                <div class="form-group">
                    <label class="col-form-label">费控类目</label><label
                            class="col-form-label text-danger">*</label>
                    <input type="text" name="title" class="form-control" value="@if(isset($budget_item['title'])){{ $budget_item['title'] }}@endif">
                </div>
                <div class="form-group">
                    <label class="col-form-label">是否锁死</label><label
                            class="col-form-label text-danger">*</label>
                    <select name="is_lock" class="form-control">
                        <option value="1"@if(isset($budget_item['is_lock']) && $budget_item['is_lock'] == 1) selected @endif>是</option>
                        <option value="0"@if(isset($budget_item['is_lock']) && $budget_item['is_lock'] == 0) selected @endif>否</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="col-form-label">是否超支</label><label
                            class="col-form-label text-danger">*</label>
                    <select name="is_over" class="form-control">
                        <option value="1"@if(isset($budget_item['is_over']) && $budget_item['is_over'] == 1) selected @endif>是</option>
                        <option value="0"@if(isset($budget_item['is_over']) && $budget_item['is_over'] == 0) selected @endif>否</option>
                    </select>
                </div>
                <div class="form-group" style="width: 200px;">
                    <label class="col-form-label">费控条件:金额/额度(元)/人</label><label
                            class="col-form-label text-danger">*</label>
                    <input type="text" name="amount" class="form-control" value="@if(isset($budget_item['amount'])){{ $budget_item['amount'] }}@endif">
                </div>

{{--                <div class="form-group" style="width: 200px;">--}}
{{--                    <label class="col-form-label">费控条件:人员数量(人)</label><label--}}
{{--                            class="col-form-label text-danger">*</label>--}}
{{--                    <input type="text" name="personnel_count" class="form-control"--}}
{{--                           value="@if(isset($budget_item['personnel_count'])){{ $budget_item['personnel_count'] }}@endif">--}}
{{--                </div>--}}

{{--                <div class="form-group">--}}
{{--                    <label class="col-form-label">费控条件设定</label>--}}
{{--                    <input type="text" name="num_code" class="form-control" value="">--}}
{{--                </div>--}}

                <div class="text-left">
                    <button type="button" class="btn btn-primary btn-sm line-height-fix"
                            data-editId="@if(isset($budget_item['id'])){{ $budget_item['id'] }}@endif">保存</button>
                </div>
                {{--                </form>--}}
            </div>
        </div>
    </section>

@endsection

@section("javascript")
    <!-- ================== DATEPICKER SCRIPTS ==================-->
    <script>
        $("button.line-height-fix").on('click', function () {
            var cost_budget_id = '{{ $cost_budget_id }}';
            var title = $("[name='title']").val();
            var is_lock = $("[name='is_lock']").val();
            var is_over = $("[name='is_over']").val();
            var amount = $("[name='amount']").val();
            // var personnel_count = $("[name='personnel_count']").val();
            var data = {
                'cost_budget_id' : cost_budget_id,
                'title': title,
                'is_lock': is_lock,
                'is_over': is_over,
                'amount': amount,
                // 'personnel_count': personnel_count,
                '_token':'{{csrf_token()}}',
                'id': $(this).attr('data-editId')
            };
            if (!title || !amount) {
                alert('全是必填项哦');
                return;
            }
            $.ajax({
                url: '/financialManage/budgetStore',
                type: 'POST',
                data: data,
                datatype: 'json',
                success: function(response) {
                    alert(response.message);
                    if(response.code == 200) {
                        window.location.href = "{{  route('finance.financialManage.budgetSetting',['id'=>$cost_budget_id])}}";
                    }
                }
            });
        })

    </script>
@endsection

