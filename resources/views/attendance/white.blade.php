@extends("layouts.main",['title' => '白名单'])

@section('content')
    <header class="page-header">
        <div class="d-flex align-items-center">
            <div class="mr-auto">
                <h1 class="separator">白名单</h1>
                <nav class="breadcrumb-wrapper" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{url('/')}}"><i class="icon dripicons-home"></i></a></li>
                        <li class="breadcrumb-item"><a href="javascript:void(0)"></a>考勤管理</li>
                        <li class="breadcrumb-item active" aria-current="page">白名单</li>
                    </ol>
                </nav>
            </div>
        </div>
    </header>
    <section class="page-content container-fluid">
        <div class="card">
            <div class="card-body">
                <form action="{{  route('attendance.attendance.white_add')  }}" method="POST">
                    @csrf

                    <div class="form-inline">
                        <div class="input-group col-md-4">
                            {!! Form::select('chinese_name', $allUsers->pluck('chinese_name', 'chinese_name'), old('users'), ['class' => 'form-control select2', 'required' => '']) !!}
                        </div>
                        <br>
                        <br>
                        <div class="input-group col-md-4">
                            <button type="submit" class="btn btn-primary btn-sm line-height-fix">添加</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="card">
            <div class="table-responsive">
                <table class="table table-hover table-outline table-vcenter text-nowrap card-table">
                    <thead>
                    <tr>
                        <th>编号</th>
                        <th>姓名</th>
                        <th>职务</th>
                        <th>操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    @if(count($whitelist))
                        @foreach($whitelist as $white)
                            <tr id="user_{{  $white->user->id  }}">
                                <td>
                                    <div>{{  $white->user->getPrefixEmployeeNum()  }}</div>
                                </td>
                                <td>
                                    <a href="{{route('users.show',['id' => $white->user->id])}}"> {{$white->chinese_name}} </a>
                                </td>
                                <td>
                                    <div>{{  $white->user->position  }}</div>
                                </td>
                                <td>
                                    <a href="javascript:void(0)" class="del" data-deleteId="{{ $white->id }}" data-target="#delModal">删除</a>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="7" class="text-center">
                                未找到相关用户
                            </td>
                        </tr>

                    @endif
                    </tbody>
                </table>
            </div>
        </div>

    </section>
@endsection

@section('head')
    <link rel="stylesheet" href="/static/vendor/select2/select2.min.css">
@endsection
@section('javascript')
    <script src="/static/vendor/select2/select2.min.js"></script>
    <script>
        (function(window, document, $, undefined) {
            "use strict";
            $(function() {
                $(".select2").select2();
            });

        })(window, document, window.jQuery);
    </script>
    <script>
        //删除
        $(".del").attr('data-href',"{{  route('attendance.attendance.white_delete') }}");

        $(".del").click(function (e) {
            var id = $(this).attr('data-deleteId');
            e.preventDefault();
            callPostAjax($(this), {id: id}, function (response) {
                console.log(response);
                if (response.code == 1) {

                    alert(response.message);
                    return;
                }

                location.href = "{{  route('attendance.attendance.white') }}";
            });
        });


    </script>
@endsection