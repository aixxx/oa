@extends("layouts.main",['title' => '会计准则'])

@section('content')
    <header class="page-header">
        <div class="d-flex align-items-center">
            <div class="mr-auto">
                <h1 class="separator">@if(isset($sofzz['id']))会计准则编辑@else会计准则添加@endif</h1>
                <nav class="breadcrumb-wrapper" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{url('/')}}"><i class="icon dripicons-home"></i></a></li>
                        <li class="breadcrumb-item"><a href="javascript:void(0)"></a>会计准则管理</li>
                        <li class="breadcrumb-item active" aria-current="page">@if(isset($sofzz['id']))会计准则编辑@else会计准则添加@endif</li>
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
                        <label class="col-form-label">会计准则名字</label><label
                                class="col-form-label text-danger">*</label>
                        <input type="text" name="name" class="form-control" value="@if(isset($sofzz['name'])){{ $sofzz['name'] }}@endif">
                    </div>

                    <div class="text-left">
{{--                        <button type="submit" class="btn btn-primary btn-sm line-height-fix">保存</button>--}}
                        <button type="button" class="btn btn-primary btn-sm line-height-fix" data-editId="@if(isset($sofzz['id'])){{ $sofzz['id'] }}@endif">保存</button>
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
            var data = {
                'name': name,
                '_token':'{{csrf_token()}}',
                'id': $(this).attr('data-editId')
            };
            if (!name) {
                alert('是必填项哦');
                return;
            }
            $.ajax({
                url: '/financialManage/sofzzStore',
                type: 'POST',
                data: data,
                datatype: 'json',
                success: function(response) {
                    alert(response.message);
                    if(response.code = 200) {
                        window.location.href = "{{route("finance.financialManage.sofzz")}}";
                    }
                }
            });
        })

    </script>
@endsection

