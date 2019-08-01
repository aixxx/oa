@extends("layouts.main",['title' => '会计科目'])

@section('content')
    <header class="page-header">
        <div class="d-flex align-items-center">
            <div class="mr-auto">
                <h1 class="separator">@if(isset($one_coin['id']))会计科目编辑@else会计科目添加@endif</h1>
                <nav class="breadcrumb-wrapper" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{url('/')}}"><i class="icon dripicons-home"></i></a></li>
                        <li class="breadcrumb-item"><a href="javascript:void(0)"></a>会计科目管理</li>
                        <li class="breadcrumb-item active" aria-current="page">@if(isset($one_coin['id']))会计科目编辑@else会计科目添加@endif</li>
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
                        <label class="col-form-label">准则名称</label><label class="text-danger col-form-label">*</label>
                        <select name="zid" class="form-control">
                            {{--                            <option value="">请选择</option>--}}
                            @if($sofzz)
                                @foreach($sofzz as $_sofzz)
                                    <option value="{{  $_sofzz['id']  }}"
                                        @if(isset($one_sof['zid']) && ($_sofzz['id'] == $one_sof['zid']))
                                        selected
                                        @endif>{{  $_sofzz['name']  }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="col-form-label">科目名称</label><label
                                class="col-form-label text-danger">*</label>
                        <input type="text" name="class_name" class="form-control" value="@if(isset($one_sof['class_name'])){{ $one_sof['class_name'] }}@endif">
                    </div>
                    <div class="form-group">
                        <label class="col-form-label">科目编号</label><label
                                class="col-form-label text-danger">*</label>
                        <input type="text" name="code" class="form-control" value="@if(isset($one_sof['code'])){{ $one_sof['code'] }}@endif">
                    </div>

                    <div class="form-group">
                        <label class="col-form-label">科目类型</label><label class="text-danger col-form-label">*</label>
                        <select name="class_type" class="form-control">
                            <option value="">请选择</option>
                            <option value="1" @if(isset($one_sof['class_type']) && $one_sof['class_type'] == '1') selected @endif>成本</option>
                            <option value="2" @if(isset($one_sof['class_type']) && $one_sof['class_type'] == '2') selected @endif>负债</option>
                            <option value="3" @if(isset($one_sof['class_type']) && $one_sof['class_type'] == '3') selected @endif>共同</option>
                            <option value="4" @if(isset($one_sof['class_type']) && $one_sof['class_type'] == '4') selected @endif>损益</option>
                            <option value="5" @if(isset($one_sof['class_type']) && $one_sof['class_type'] == '5') selected @endif>所有者权益</option>
                            <option value="6" @if(isset($one_sof['class_type']) && $one_sof['class_type'] == '6') selected @endif>资产</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="col-form-label">余额方向</label><label class="text-danger col-form-label">*</label>

                        <select name="round_way" class="form-control">
{{--                            <option value="">请选择</option>--}}
                            <option value="1" @if(isset($one_sof['round_way']) && $one_sof['round_way'] == 1) selected @endif>借</option>
                            <option value="2" @if(isset($one_sof['round_way']) && $one_sof['round_way'] == 2) selected @endif>贷</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="col-form-label">科目备注</label>
                        <input type="text" name="remark" class="form-control" value="@if(isset($one_sof['remark'])){{ $one_sof['remark'] }}@endif">
                    </div>

                    <div class="text-left">
{{--                        <button type="submit" class="btn btn-primary btn-sm line-height-fix">保存</button>--}}
                        <button type="button" class="btn btn-primary btn-sm line-height-fix" data-editId="@if(isset($one_sof['id'])){{ $one_sof['id'] }}@endif">保存</button>
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
            var zid = $("[name='zid']").val();
            var code = $("[name='code']").val();
            var class_name = $("[name='class_name']").val();
            var class_type = $("[name='class_type']").val();
            var round_way = $("[name='round_way']").val();
            var remark = $("[name='remark']").val();
            var sc_id = $(this).attr('data-editId');
            var data = {
                'zid': zid,
                'code': code,
                'class_name': class_name,
                'class_type': class_type,
                'round_way': round_way,
                'remark': remark,
                '_token':'{{csrf_token()}}',
                'sc_id' : sc_id
            };
            if (!zid || !code || !class_name || !class_type || !round_way) {
                alert('除了科目备注全是必填项哦');
                return;
            }
            $.ajax({
                url: '/financialManage/accountSubjectStore',
                type: 'POST',
                data: data,
                datatype: 'json',
                success: function(response) {
                    alert(response.message);
                    if(response.code = 200) {
                        window.location.href = "{{route("finance.financialManage.accountSubject")}}";
                    }
                }
            });
        })

    </script>
@endsection

