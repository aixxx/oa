@extends("layouts.main",['title' => '凭证模板设置'])
@section('content')
    <header class="page-header">
        <div class="d-flex align-items-center">
            <div class="mr-auto">
                <h1 class="separator">{{ $voucher['title']}}模板设置</h1>
                <nav class="breadcrumb-wrapper" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{url('/')}}"><i class="icon dripicons-home"></i></a></li>
                        <li class="breadcrumb-item"><a href="javascript:void(0)"></a>{{ $voucher['title']}}模板设置</li>
                    </ol>
                </nav>
            </div>
        </div>
    </header>
    <section class="page-content container-fluid">
        <div class="card">
            <div class="table-responsive">
                <table class="table table-hover table-outline table-vcenter text-nowrap card-table">
                    <thead>
                    <tr>
                        <th>凭证项目</th>
                        <th>科目名称</th>
                        <th>余额方向</th>
                        <th>金额</th>
                    </tr>
                    </thead>
                    <tbody>
                    @if(count($voucher_item))
                        @foreach($voucher_item as $_item)
                            <tr id="user_{{  $_item['id']  }}">
                                <td>
                                    <a> {{$_item['title']}} </a>
                                </td>
                                <td>
                                    <div class="clearfix">
                                        <div class="float-left">
                                            <div>{{ $_item['class_name'] }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="clearfix">
                                        <div class="float-left">
                                            <div>@if($_item['round_way'] == 1)
                                                    借
                                                @else
                                                    贷
                                                @endif</div>
                                        </div>
                                    </div>
                                </td>
{{--                                <td>--}}
{{--                                    <a href="{{route('finance.financialManage.voucherSetting',['id'=>$_list['id']])}}" class="btn btn-primary">设置模板</a>--}}
{{--                                </td>--}}

                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="7" class="text-center">
                                未找到相关列表
                            </td>
                        </tr>

                    @endif
                    </tbody>
                </table>
            </div>
        </div>
    </section>
@endsection

