@extends('layouts.main',['title' => '编辑待入职员工'])

@section('content')
    <header class="page-header">
        <div class="d-flex align-items-center">
            <div class="mr-auto">
                <h1 class="separator">待入职编辑</h1>
                <nav class="breadcrumb-wrapper" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{url('/')}}"><i class="icon dripicons-home"></i></a></li>
                        <li class="breadcrumb-item"><a href="javascript:void(0)"></a>待入职管理</li>
                        <li class="breadcrumb-item active" aria-current="page">待入职编辑</li>
                    </ol>
                </nav>
            </div>
        </div>
    </header>
    <section class="page-content container-fluid">
        <div class="card">
            {{--<div class="card-header">--}}
            {{--<h3 class="card-title">编辑待入职员工</h3>--}}
            {{--</div>--}}
            <div class="card-body">
                <form action="{{ route('pendingusers.update',['id' => $user->id]) }}" method="POST">
                    @csrf
                    <div class="form-group">
                        @if($errors->get('email'))
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach($errors->get('email') as $error)
                                        <li>{{  $error  }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        <div class="form-inline">
                            <label class="form-label">邮箱<span style="color:red">*</span>：</label>
                            <input type="text" name="email" value="{{ $user->email }}" class="form-control col-md-3"
                                   readonly>(唯一的,不可更改)
                        </div>
                    </div>

                    <div class="form-group">
                        @if($errors->get('chinese_name'))
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach($errors->get('chinese_name') as $error)
                                        <li>{{  $error  }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        <div class="form-inline">
                            <label class="form-label">姓名<span style="color:red">*</span>：</label>
                            <input type="text" name="chinese_name" class="form-control col-md-3"
                                   value="{{  $user->chinese_name  }}">
                        </div>
                    </div>
                    <div class="form-group">
                        @if($errors->get('company_id'))
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach($errors->get('company_id') as $error)
                                        <li>{{  $error  }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        <div class="form-inline">
                            <label class="form-label">所属公司&nbsp;<span style="color:red">*</span></label>
                            <select name="company_id" class="form-control col-md-3">
                                <option value="">请选择</option>
                                @if($companies)
                                    @foreach($companies as $company)
                                        <option value="{{ $company->id }}" <?php if ($user->company_id == $company->id) {
                                            echo 'selected';
                                        }?>>{{  $company->name  }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>

                    <div class="text-left">
                        <button type="submit" class="btn btn-primary btn-sm line-height-fix">保存</button>
                    </div>
                </form>
            </div>
        </div>
    </section>

@endsection
@section('javascript')
    <!-- ================== DATEPICKER SCRIPTS ==================-->
    <script src="/static/vendor/moment/min/moment.min.js"></script>
    <script src="/static/vendor/bootstrap-datepicker/bootstrap-datepicker.min.js"></script>
    <script src="/static/vendor/bootstrap-daterangepicker/daterangepicker.js"></script>
    <script src="/static/vendor/sweetalert2/dist/sweetalert2.min.js"></script>
    <script src="/static/js/components/bootstrap-datepicker-init.js"></script>
    <script src="/static/js/components/bootstrap-date-range-picker-init.js"></script>
    <script src="{{  asset('js/bootstrap-treeview.js')  }}"></script>
    <script>
        $('.datepicker').parent().datepicker({
            "autoclose": true,
            "format": "yyyy-mm-dd",
            "language": "zh-CN"
            // "startDate": "-3d"
        });

        store_error = "{{  session('storeError')  }}";
        if (store_error) {
            alert(store_error);
        }
    </script>
    <script>
        $("#add_name").blur(function () {
            name = $.trim($("#add_name").val());
            usercheck(name);
        })

        //name校验
        function usercheck(name) {
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: "POST",
                dataType: "json",
                url: "{{  route('users.check')  }}",
                data: {'name': name},
                success: function (response) {
                    if (response.status == 'failed') {
                        alert(response.messages);
                    }
                }
            })
        }
    </script>
@endsection
