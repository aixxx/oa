@extends("layouts.main",['title' => '客户公海设置'])
@section('content')
    <header class="page-header">
        <div class="d-flex align-items-center">
            <div class="mr-auto">
                <h1 class="separator">客户公海</h1>
                <nav class="breadcrumb-wrapper" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{url('/')}}"><i class="icon dripicons-home"></i></a></li>
                        <li class="breadcrumb-item"><a href="javascript:void(0)"></a>客户公海</li>
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
                    <label class="col-form-label">签约前超过
                        <input type="text" name="over_contact" style="width: 100px;"  value="@if(isset($settings['over_contact']) && $settings['over_contact'] != 0){{ $settings['over_contact'] }}@endif">
                    天未联系客户将进入客户公海。</label>
                </div>
                <div class="text-left">
                    <button type="button" class="btn btn-primary btn-sm line-height-fix" data-editId="@if(isset($settings['id'])){{ $settings['id'] }}@endif">保存</button>
                </div>
            </div>
        </div>
    </section>

@endsection

@section("javascript")
    <!-- ================== DATEPICKER SCRIPTS ==================-->
    <script>
        $("button.line-height-fix").on('click', function () {
            var over_contact = $("[name='over_contact']").val();
            var data = {
                'over_contact': over_contact,
                '_token':'{{csrf_token()}}',
                'id': $(this).attr('data-editId')
            };
            if (!over_contact) {
                alert('是必填项哦');
                return;
            }
            $.ajax({
                url: '/customerManage/seasPublicStore',
                type: 'POST',
                data: data,
                datatype: 'json',
                success: function(response) {
                    alert(response.message);
                    if(response.code = 200) {
                        window.location.href = "{{route("customer.customerManage.seasPublic")}}";
                    }
                }
            });
        })

    </script>
@endsection

