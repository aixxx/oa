@extends("layouts.main",['title' => '结算类型'])

@section('content')
    <header class="page-header">
        <div class="d-flex align-items-center">
            <div class="mr-auto">
                <h1 class="separator">@if(isset($balance['id']))结算类型编辑@else结算类型添加@endif</h1>
                <nav class="breadcrumb-wrapper" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{url('/')}}"><i class="icon dripicons-home"></i></a></li>
                        <li class="breadcrumb-item"><a href="javascript:void(0)"></a>结算类型管理</li>
                        <li class="breadcrumb-item active" aria-current="page">@if(isset($balance['id']))结算类型编辑@else结算类型添加@endif</li>
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
                        <label class="col-form-label">结算类型名字</label><label
                                class="col-form-label text-danger">*</label>
                        <input type="text" name="balance_name" class="form-control" value="@if(isset($balance['balance_name'])){{ $balance['balance_name'] }}@endif">
                    </div>

                    <div class="text-left">
{{--                        <button type="submit" class="btn btn-primary btn-sm line-height-fix">保存</button>--}}
                        <button type="button" class="btn btn-primary btn-sm line-height-fix" data-editId="@if(isset($balance['id'])){{ $balance['id'] }}@endif">保存</button>
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
            var balance_name = $("[name='balance_name']").val();
            var data = {
                'balance_name': balance_name,
                '_token':'{{csrf_token()}}',
                'id': $(this).attr('data-editId')
            };
            if (!balance_name) {
                alert('是必填项哦');
                return;
            }
            $.ajax({
                url: '/financialManage/balanceStore',
                type: 'POST',
                data: data,
                datatype: 'json',
                success: function(response) {
                    alert(response.message);
                    if(response.code = 200) {
                        window.location.href = "{{route("finance.financialManage.balance")}}";
                    }
                }
            });
        })

    </script>
@endsection

