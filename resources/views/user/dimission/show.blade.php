@extends("layouts.main",['title' => '离职信息'])

@section('content')
    <header class="page-header">
        <div class="d-flex align-items-center">
            <div class="mr-auto">
                <h1 class="separator">离职信息编辑</h1>
                <nav class="breadcrumb-wrapper" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{url('/')}}"><i class="icon dripicons-home"></i></a></li>
                        <li class="breadcrumb-item"><a href="javascript:void(0)"></a>员工管理</li>
                        <li class="breadcrumb-item active" aria-current="page">离职信息编辑</li>
                    </ol>
                </nav>
            </div>
        </div>
    </header>
    <section class="page-content container-fluid">
        <div class="card">
            <h3 class="card-header" style="padding-bottom: 34px;">{{$user->chinese_name}}离职信息
                <a href="{{  route('dimission.edit',['id' => $dimission->id]) }}"
                   class="btn btn-primary btn-sm line-height-fix float-right" style="margin-left: auto">编辑</a>
            </h3>
            <div class="card-body font-size-fix">
                <div class="row">
                    @if($dimission->is_voluntary == \App\Models\UsersDimission::STATUS_YES)
                        <div class="col-md-6 text-left">是否主动离职：是</div>
                    @else
                        <div class="col-md-6 text-left">是否主动离职：否</div>
                    @endif

                    @if($dimission->is_sign == \App\Models\UsersDimission::STATUS_YES)
                        <div class="col-md-6">直属领导是否已签字：是</div>
                    @else
                        <div class="col-md-6">直属领导是否已签字：否</div>
                    @endif
                </div>
                <br>
                <div class="row">
                    @if($dimission->is_complete == \App\Models\UsersDimission::STATUS_YES)
                        <div class="col-md-6 text-left">流程手续是否已走完：是</div>
                    @else
                        <div class="col-md-6 text-left">流程手续是否已走完：否</div>
                    @endif
                </div>
                <br>
                <div class="row">
                    <div class="col-md-12">离职原因： {{  $dimission->reason  }}</div>
                </div>
                <br>
                <div class="row">
                    <div class="col-md-12">面谈结论： {{  $dimission->interview_result  }}</div>
                </div>
                <div class="row">
                    <div class="col-md-12">备注： {{  $dimission->note  }}</div>
                </div>
                <br>
            </div>
        </div>
    </section>

@endsection
@section('javascript')
    <script>
        edit_error = "{{  session('editError')  }}";

        if(edit_error)
        {
            alert(edit_error);
        }
    </script>
@endsection