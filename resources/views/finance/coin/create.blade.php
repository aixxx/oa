@extends("layouts.main",['title' => '币种'])

@section('content')
    <header class="page-header">
        <div class="d-flex align-items-center">
            <div class="mr-auto">
                <h1 class="separator">@if(isset($one_coin['id']))币种编辑@else币种添加@endif</h1>
                <nav class="breadcrumb-wrapper" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{url('/')}}"><i class="icon dripicons-home"></i></a></li>
                        <li class="breadcrumb-item"><a href="javascript:void(0)"></a>币种管理</li>
                        <li class="breadcrumb-item active" aria-current="page">@if(isset($one_coin['id']))币种编辑@else币种添加@endif</li>
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
                        <label class="col-form-label">币种名称</label><label
                                class="col-form-label text-danger">*</label>
                        <input type="text" name="name" class="form-control" value="@if(isset($one_coin['name'])){{ $one_coin['name'] }}@endif">
                    </div>
                    <div class="form-group">
                        <label class="col-form-label">币种代码</label><label
                                class="col-form-label text-danger">*</label>
                        <input type="text" name="name_code" class="form-control" value="@if(isset($one_coin['name_code'])){{ $one_coin['name_code'] }}@endif">
                    </div>
                    <div class="form-group">
                        <label class="col-form-label">币种数字代码</label><label
                                class="col-form-label text-danger">*</label>
                        <input type="text" name="num_code" class="form-control" value="@if(isset($one_coin['num_code'])){{ $one_coin['num_code'] }}@endif">
                    </div>
                    <div class="form-group">
                        <label class="col-form-label">币种小数位数</label><label
                                class="col-form-label text-danger">*</label>
                        <input type="text" name="format" class="form-control" value="@if(isset($one_coin['format'])){{ $one_coin['format'] }}@endif">
                    </div>

                    <div class="form-group">
                        <label class="col-form-label">使用国家</label><label
                                class="col-form-label text-danger">*</label>
                        <input type="text" name="area" class="form-control" value="@if(isset($one_coin['area'])){{ $one_coin['area'] }}@endif">
                    </div>

                    <div class="text-left">
{{--                        <button type="submit" class="btn btn-primary btn-sm line-height-fix">保存</button>--}}
                        <button type="button" class="btn btn-primary btn-sm line-height-fix" data-editId="@if(isset($one_coin['id'])){{ $one_coin['id'] }}@endif">保存</button>
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
            var name = $("[name='name']").val();
            var name_code = $("[name='name_code']").val();
            var num_code = $("[name='num_code']").val();
            var format = $("[name='format']").val();
            var area = $("[name='area']").val();
            var coin_id = $(this).attr('data-editId');
            var data = {
                'name': name,
                'name_code': name_code,
                'num_code': num_code,
                'format': format,
                'area': area,
                '_token':'{{csrf_token()}}',
                'coin_id' : coin_id
            };
            if (!name || !name_code || !num_code || !format || !area) {
                alert('全是必填项哦');
                return;
            }
            $.ajax({
                url: '/financialManage/coinStore',
                type: 'POST',
                data: data,
                datatype: 'json',
                success: function(response) {
                    alert(response.message);
                    if(response.code = 200) {
                        window.location.href = "{{route("finance.financialManage.coin")}}";
                    }
                }
            });
        })

    </script>
@endsection

