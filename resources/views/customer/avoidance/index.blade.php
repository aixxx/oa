@extends("layouts.main",['title' => '防撞单设置'])
@section('content')
    <header class="page-header">
        <div class="d-flex align-items-center">
            <div class="mr-auto">
                <h1 class="separator">防撞单</h1>
                <nav class="breadcrumb-wrapper" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{url('/')}}"><i class="icon dripicons-home"></i></a></li>
                        <li class="breadcrumb-item"><a href="javascript:void(0)"></a>防撞单</li>
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
                    <label class="col-form-label">防撞单&nbsp;&nbsp;&nbsp;&nbsp;
                        <input type="radio" value="1" name="avoidance"@if(isset($settings['avoidance']) && $settings['avoidance'] == 1) checked @else checked @endif>&nbsp;&nbsp;开启
                        &nbsp;&nbsp;&nbsp;&nbsp;
                        <input type="radio" value="0" name="avoidance"@if(isset($settings['avoidance']) && $settings['avoidance'] == 0) checked @endif>&nbsp;&nbsp;关闭
                    </label>
                </div>
                <div class="form-group">
                    <label class="col-form-label">关闭后，允许不同客户下有电话号码相同的联系人</label>
                </div>
                <div class="form-group">
                    <label class="col-form-label">新建客户联系人必填&nbsp;&nbsp;&nbsp;&nbsp;
                        <input type="radio" value="1" name="filled"@if(isset($settings['filled']) && $settings['filled'] == 1) checked @else checked @endif>&nbsp;&nbsp;是
                        &nbsp;&nbsp;&nbsp;&nbsp;
                        <input type="radio" value="0" name="filled"@if(isset($settings['filled']) && $settings['filled'] == 0) checked @endif>&nbsp;&nbsp;否
                    </label>
                </div>
                <div class="form-group">
                    <label class="col-form-label">打开时，新建客户时，至少要填写一个客户联系人，并且禁止删除最后一个联系人。</label>
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
            var avoidance = $("[name='avoidance']:checked").val();
            var filled = $("[name='filled']:checked").val();
            var data = {
                'avoidance': avoidance,
                'filled': filled,
                '_token':'{{csrf_token()}}',
                'id': $(this).attr('data-editId')
            };
            // if (!avoidance || !filled) {
            //     alert('全是必填项哦');
            //     return;
            // }
            $.ajax({
                url: '/customerManage/avoidanceStore',
                type: 'POST',
                data: data,
                datatype: 'json',
                success: function(response) {
                    alert(response.message);
                    if(response.code = 200) {
                        window.location.href = "{{route("customer.customerManage.avoidance")}}";
                    }
                }
            });
        })

    </script>
@endsection

