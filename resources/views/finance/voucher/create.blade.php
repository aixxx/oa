@extends("layouts.main",['title' => '凭证模板'])

@section('content')
    <header class="page-header">
        <div class="d-flex align-items-center">
            <div class="mr-auto">
                <h1 class="separator">@if(isset($voucher['id']))凭证模板编辑@else凭证模板添加@endif</h1>
                <nav class="breadcrumb-wrapper" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{url('/')}}"><i class="icon dripicons-home"></i></a></li>
                        <li class="breadcrumb-item"><a href="javascript:void(0)"></a>凭证模板</li>
                        <li class="breadcrumb-item active" aria-current="page">@if(isset($voucher['id']))凭证模板编辑@else凭证模板添加@endif</li>
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
                        <label class="col-form-label">账套名称</label><label
                                class="col-form-label text-danger">*</label>
{{--                        <input type="text" name="sof_id" class="form-control" value="@if(isset($voucher['title'])){{ $voucher['title'] }}@endif">--}}
                        <select name="sof_id" class="form-control">
                            <option value="">请选择账套名称</option>
                            @if($sofinfo)
                                @foreach($sofinfo as $_info)
                                    <option value="{{  $_info['sof_id']  }}"
                                            @if(isset($voucher['sof_id']) && ($_info['sof_id'] == $voucher['sof_id']))
                                            selected
                                            @endif>{{  $_info['sof_name']  }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="col-form-label">模板名称</label><label
                                class="col-form-label text-danger">*</label>
                        <input type="text" name="title" class="form-control" value="@if(isset($voucher['title'])){{ $voucher['title'] }}@endif">
                    </div>
                    <div class="form-group">
                        <label class="col-form-label">模板类型</label><label
                                class="col-form-label text-danger">*</label>
                        <select name="type" class="form-control">
{{--                            <option value="">请选择</option>--}}
{{--                            <option value="1" @if(isset($voucher['type']) && $voucher['type'] == 1) selected @endif>记</option>--}}
{{--                            <option value="2" @if(isset($voucher['type']) && $voucher['type'] == 2) selected @endif>收</option>--}}
{{--                            <option value="3" @if(isset($voucher['type']) && $voucher['type'] == 3) selected @endif>付</option>--}}
{{--                            <option value="4" @if(isset($voucher['type']) && $voucher['type'] == 4) selected @endif>转</option>--}}
                        </select>
                    </div>

                    <div class="text-left">
{{--                        <button type="submit" class="btn btn-primary btn-sm line-height-fix">保存</button>--}}
                        <button type="button" class="btn btn-primary btn-sm line-height-fix" data-editId="@if(isset($voucher['id'])){{ $voucher['id'] }}@endif">保存</button>
                    </div>
{{--                </form>--}}
            </div>
        </div>
    </section>

@endsection

@section("javascript")
    <!-- ================== DATEPICKER SCRIPTS ==================-->
    <script>
        $(function() {// 初始化内容
            var sofpzz_id = '@if(isset($voucher['sofpzz_id'])){{ $voucher['sofpzz_id'] }}@endif';
            $.ajax({
                url: '/financialManage/voucherSofpzz',
                type: 'GET',
                data: {'sof_id': $("[name='sof_id']").val(),'sofpzz_id': sofpzz_id},
                datatype: 'json',
                success: function(data) {
                    $("[name='type']").html(data);
                }
            });
        });

        $("button.line-height-fix").on('click', function () {
            var sof_id = $("[name='sof_id']").val();
            var title = $("[name='title']").val();
            var type = $("[name='type']").val();
            var id = $(this).attr('data-editId');
            var data = {
                'sof_id':sof_id,
                'title': title,
                'type': type,
                '_token':'{{csrf_token()}}',
                'id': id
            };
            if (!title || !type) {
                alert('全是必填项哦');
                return;
            }
            $.ajax({
                url: '/financialManage/voucherStore',
                type: 'POST',
                data: data,
                datatype: 'json',
                success: function(response) {
                    alert(response.message);
                    if(response.code = 200) {
                        window.location.href = "{{route("finance.financialManage.voucher")}}";
                    }
                }
            });
        });


        $("[name='sof_id']").on('change',function(){
            $.ajax({
                url: '/financialManage/voucherSofpzz',
                type: 'GET',
                data: {'sof_id': $(this).val()},
                datatype: 'json',
                success: function(data) {
                    $("[name='type']").html(data);
                }
            });

        });

    </script>
@endsection

