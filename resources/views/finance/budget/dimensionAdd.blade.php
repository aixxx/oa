@extends("layouts.main",['title' => '维度库管理'])
@section('content')
    <header class="page-header">
        <div class="d-flex align-items-center">
            <div class="mr-auto">
                <h1 class="separator">@if(isset($dimension))编辑@else添加@endif维度库</h1>
                <nav class="breadcrumb-wrapper" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{url('/')}}"><i class="icon dripicons-home"></i></a></li>
                        <li class="breadcrumb-item"><a href="javascript:void(0)"></a>@if(isset($dimension))编辑@else添加@endif维度库</li>
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
                    <label class="col-form-label">标题</label><label
                            class="col-form-label text-danger">*</label>
                    <input type="text" name="title" class="form-control" value="@if(isset($dimension['title'])){{ $dimension['title'] }}@endif">
                </div>

                <div class="form-group">
                    <button type="button" class="btn btn-primary btn-sm line-height-fix">添加选项</button>
                </div>

                <div class="form-inline" id="form-condition" style="margin-bottom: 10px;display: block;">
                    @if(isset($condition))
                        @foreach($condition as $_k => $_condition)
                            <div class="form-group condition" style="margin-bottom: 10px;" id="condition_{{ intval($_k+1) }}">
                                <label class="col-form-label">选项</label>&nbsp;&nbsp;&nbsp;&nbsp;
                                <input type="text" name="item_name[]" class="form-control" attr-id="{{ $_condition['id'] }}" value="{{ $_condition['title'] }}">
                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                <div class="text-left">
                                    <button type="button" style="height: 34px;" class="btn btn-danger delete line-del">删除</button>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="form-group condition" style="margin-bottom: 10px;" id="condition_1">
                            <label class="col-form-label">选项</label>&nbsp;&nbsp;&nbsp;&nbsp;
                            <input type="text" name="item_name[]" class="form-control" attr-id="" value="">
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <div class="text-left">
                                <button type="button" style="height: 34px;" class="btn btn-danger delete line-del">删除</button>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="text-left">
                    <button type="button" class="btn btn-primary btn-sm line-save" data-editId="@if(isset($dimension['id'])){{ $dimension['id'] }}@endif">保存</button>
                </div>
                {{--                </form>--}}
            </div>
        </div>
    </section>
@endsection
@section('javascript')
    <script src="/static/vendor/bootstrap-datepicker/bootstrap-datepicker.min.js"></script>
    <script src="/static/vendor/bootstrap-daterangepicker/daterangepicker.js"></script>
    <script src="/static/js/components/bootstrap-datepicker-init.js"></script>
    <script>
        $("button.line-height-fix").on('click', function () {
            var strVar = $("#form-condition").append($(".condition:last").clone(true));
            var num = $(".condition").length;
            strVar.find(".condition:last").attr("id","condition_"+num);
            strVar.find(".condition:last .form-control").attr("attr-id","");
        });

        $("button.line-del").on('click', function () {
            var num = $(".condition").length;
            if(num == 1) {
                alert('条件至少有1个');
            } else {
                // $(this).parent().parent().remove();
                var parent = $(this).parent().parent();
                $.ajax({
                    url: '/financialManage/isBudgetDimensionCondition',
                    type: 'GET',
                    data: {'id':$(this).parent().prev().attr('attr-id')},
                    datatype: 'json',
                    success: function(response) {
                        if(response.code == 200) {
                            parent.remove();
                        } else {
                            alert(response.message);
                            return ;
                        }
                    }
                });
            }
        });

        $("button.line-save").on('click',function () {
            var title = $("[name='title']").val();
            var condition_title = [];
            $("[name='item_name[]']").each(function(){
                condition_title.push({'id': $(this).attr('attr-id'), 'title' : $(this).val()});
            });

            var data = {
                'title' : title,
                'condition_title' : condition_title,
                '_token':'{{csrf_token()}}',
                'id': $(this).attr('data-editId')
            };

            $.ajax({
                url: '/financialManage/budgetDimensionStore',
                type: 'POST',
                data: data,
                datatype: 'json',
                success: function(response) {
                    alert(response.message);
                    if(response.code == 200) {
                        window.location.href = "{{  route('finance.financialManage.budgetDimension')}}";
                    }
                }
            });
        });

    </script>
@endsection

