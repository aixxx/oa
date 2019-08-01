@extends("layouts.main",['title' => '凭证模板设置'])
@section('content')
    <header class="page-header">
        <div class="d-flex align-items-center">
            <div class="mr-auto">
                <h1 class="separator">{{ $voucher['title']}}模板项目设置</h1>
                <nav class="breadcrumb-wrapper" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{url('/')}}"><i class="icon dripicons-home"></i></a></li>
                        <li class="breadcrumb-item"><a href="javascript:void(0)"></a>{{ $voucher['title']}}模板项目设置</li>
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
                    <label class="col-form-label">凭证摘要</label><label
                            class="col-form-label text-danger">*</label>
                    <input type="text" name="title" class="form-control" value="@if(isset($voucher_item['title'])){{ $voucher_item['title'] }}@endif">
                </div>
                <div class="form-group">
                    <label class="col-form-label">科目名称</label><label
                            class="col-form-label text-danger">*</label>
                    <select name="sofusclass_id" class="form-control">
                        @if($sofusClass)
                            @foreach($sofusClass as $_class)
                                <option value="{{  $_class['id']  }}"
                                        @if(isset($voucher_item['sofusclass_id']) && ($_class['id'] == $voucher_item['sofusclass_id']))
                                        selected
                                        @endif>{{  $_class['name']  }}</option>
                            @endforeach
                        @endif
                    </select>

                </div>
                <div class="form-group">
                    <label class="col-form-label">余额方向</label><label
                            class="col-form-label text-danger">*</label>
                    <select name="round_way" class="form-control">
                            <option value="">请选择</option>
                            <option value="1" @if(isset($voucher_item['round_way']) && $voucher_item['round_way'] == 1) selected @endif>借</option>
                            <option value="2" @if(isset($voucher_item['round_way']) && $voucher_item['round_way'] == 2) selected @endif>贷</option>
                    </select>
                </div>
                <input type="hidden" name="voucher_template_id" value="{{$voucher['id']}}">
                <div class="text-left">
                    {{--                        <button type="submit" class="btn btn-primary btn-sm line-height-fix">保存</button>--}}
                    <button type="button" class="btn btn-primary btn-sm line-height-fix" data-editId="@if(isset($voucher_item['id'])){{ $voucher_item['id'] }}@endif">保存</button>
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
            var title = $("[name='title']").val();
            var sofusclass_id = $("[name='sofusclass_id']").val();
            var round_way = $("[name='round_way']").val();
            var voucher_template_id = $("[name='voucher_template_id']").val();
            var id = $(this).attr('data-editId');
            var data = {
                'title': title,
                'sofusclass_id':sofusclass_id,
                'round_way': round_way,
                'voucher_template_id': voucher_template_id,
                '_token':'{{csrf_token()}}',
                'id': id
            };
            if (!title || !sofusclass_id || !round_way) {
                alert('全是必填项哦');
                return;
            }
            $.ajax({
                url: '/financialManage/voucherItemStore',
                type: 'POST',
                data: data,
                datatype: 'json',
                success: function(response) {
                    alert(response.message);
                    if(response.code = 200) {
                        window.location.href = "{{route("finance.financialManage.voucherSetting",['id'=>$voucher['id']])}}";
                    }
                }
            });
        });


    </script>
@endsection

