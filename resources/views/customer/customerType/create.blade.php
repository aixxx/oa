@extends("layouts.main",['title' => '客户标签'])

@section('content')
    <header class="page-header">
        <div class="d-flex align-items-center">
            <div class="mr-auto">
                <h1 class="separator">@if(isset($types['id']))客户标签编辑@else客户标签添加@endif</h1>
                <nav class="breadcrumb-wrapper" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{url('/')}}"><i class="icon dripicons-home"></i></a></li>
                        <li class="breadcrumb-item"><a href="javascript:void(0)"></a>客户标签设置</li>
                        <li class="breadcrumb-item active" aria-current="page">@if(isset($types['id']))客户标签编辑@else客户标签添加@endif</li>
                    </ol>
                </nav>
            </div>
        </div>
    </header>
    <section class="page-content container-fluid">
        <div class="card">
            <div class="card-body">
                @csrf
                <div class="form-group">
                    <label class="col-form-label">标签名称</label><label
                            class="col-form-label text-danger">*</label>
                    <input type="text" name="type_name" style="width: 100px;" class="form-control" value="@if(isset($types['type_name'])){{ $types['type_name'] }}@endif">
                </div>
                <div class="form-group">
                    <label class="col-form-label">折扣率(%)</label><label
                            class="col-form-label text-danger">*</label>
                    <input type="text" name="discount" style="width: 100px;" class="form-control" value="@if(isset($types['discount'])){{ $types['discount'] }}@endif">
                    <label class="col-form-label">折扣率：定义此类客户在商品市场价额基础上享有的折扣。如果某个商品订货价不使用此处的默认折扣率，可在单独编辑该商品对各类客户的售价。</label>
                </div>
                <div class="form-group">
                    <label class="col-form-label">付款期限(天数)</label><label
                            class="col-form-label text-danger">*</label>
                    <input type="text" name="payment_day" style="width: 100px;" class="form-control" value="@if(isset($types['payment_day'])){{ $types['payment_day'] }}@endif">
                    <label class="col-form-label">付款天数：定义此类客户的默认付款期限（天数）。如果需要给某个客户单独设定其付款期限，可以单独编辑该客户。
                        付款期限从订单商品的销售单生成之日开始计算，超出此期限没有付款时，系统会将未付款金额视为逾期。修改付款期限只对未来生成的销售单起作用，原来的销售单仍然按照原来的付款期限计算。</label>
                </div>
                <div class="form-group">
                    <label class="col-form-label">排序(数字)</label><label
                            class="col-form-label text-danger">*</label>
                    <input type="text" name="orderby" style="width: 100px;" class="form-control" value="@if(isset($types['orderby'])){{ $types['orderby'] }}@endif">
                </div>
                <div class="form-group">
                    <label class="col-form-label">
                    保存设置：如果您修改了某类客户的订单折扣率，那么保存后，会按照新的折扣率重新计算所有商品对该类客户的订货价，这也将覆盖您为此类客户手工设置的订货价。
                    </label>
                </div>

                <div class="text-left">
                    <button type="button" class="btn btn-primary btn-sm line-height-fix" data-editId="@if(isset($types['id'])){{ $types['id'] }}@endif">保存</button>
                </div>
            </div>
        </div>
    </section>

@endsection

@section("javascript")
    <!-- ================== DATEPICKER SCRIPTS ==================-->
    <script>
        $("button.line-height-fix").on('click', function () {
            var type_name = $("[name='type_name']").val();
            var discount = $("[name='discount']").val();
            var payment_day = $("[name='payment_day']").val();
            var orderby = $("[name='orderby']").val();
            var data = {
                'type_name': type_name,
                'discount': discount,
                'payment_day': payment_day,
                'orderby': orderby,
                '_token':'{{csrf_token()}}',
                'id': $(this).attr('data-editId')
            };
            if (!type_name || !discount || !payment_day || !orderby) {
                alert('全是必填项哦');
                return;
            }
            $.ajax({
                url: '/customerManage/customerTypeStore',
                type: 'POST',
                data: data,
                datatype: 'json',
                success: function(response) {
                    alert(response.message);
                    if(response.code = 200) {
                        window.location.href = "{{route("customer.customerManage.customerType")}}";
                    }
                }
            });
        })

    </script>
@endsection

