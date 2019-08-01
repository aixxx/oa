@extends("layouts.main",['title' => '费控模板管理'])

@section('content')
    <header class="page-header">
        <div class="d-flex align-items-center">
            <div class="mr-auto">
                <h1 class="separator">费控模板类目@if(isset($category))编辑@else添加@endif</h1>
                <nav class="breadcrumb-wrapper" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{url('/')}}"><i class="icon dripicons-home"></i></a></li>
                        <li class="breadcrumb-item"><a href="javascript:void(0)"></a>费控预算</li>
                        <li class="breadcrumb-item active" aria-current="page">费控模板类目@if(isset($category))编辑@else添加@endif</li>
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
                    <label class="col-form-label">类目名称</label><label
                            class="col-form-label text-danger">*</label>
                    <input type="text" name="title" disabled class="form-control" value="@if(isset($category['title'])){{ $category['title'] }}@endif">
                </div>
                <div class="form-group">
                    <label class="col-form-label">类目说明</label>
{{--                    <label class="col-form-label text-danger">*</label>--}}
                    <input type="text" name="explain" class="form-control" value="@if(isset($category['explain'])){{ $category['explain'] }}@endif">
                </div>
                <div class="form-group">
                    <label class="col-form-label">是否锁死</label><label
                            class="col-form-label text-danger">*</label>
                    <select name="is_lock" class="form-control">
                        <option value="0"@if(isset($category['is_lock']) && $category['is_lock'] == 0) selected @endif>否</option>
                        <option value="1"@if(isset($category['is_lock']) && $category['is_lock'] == 1) selected @endif>是</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="col-form-label">是否超支</label><label
                            class="col-form-label text-danger">*</label>
                    <select name="is_over" class="form-control">
                        <option value="0"@if(isset($category['is_over']) && $category['is_over'] == 0) selected @endif>否</option>
                        <option value="1"@if(isset($category['is_over']) && $category['is_over'] == 1) selected @endif>是</option>
                    </select>
                </div>

                <div class="text-left">
                    <button type="button" class="btn btn-primary btn-sm line-height-fix"
                            data-editId="@if(isset($category['id'])){{ $category['id'] }}@endif">保存</button>
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
            var is_lock = $("[name='is_lock']").val();
            var is_over = $("[name='is_over']").val();
            var explain = $("[name='explain']").val();
            var data = {
                'title': title,
                'is_lock': is_lock,
                'is_over': is_over,
                'explain': explain,
                '_token':'{{csrf_token()}}',
                'id': $(this).attr('data-editId')
            };
            if (!title) {
                alert('类目名称是必填项哦');
                return;
            }
            $.ajax({
                url: '/financialManage/budgetCategoryStore',
                type: 'POST',
                data: data,
                datatype: 'json',
                success: function(response) {
                    alert(response.message);
                    if(response.code == 200) {
                        window.location.href = "{{  route('finance.financialManage.budgetCategory')}}";
                    }
                }
            });
        })

    </script>
@endsection

