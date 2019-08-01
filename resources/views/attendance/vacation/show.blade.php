@extends("layouts.main",['title' => '我的假期'])

@section('content')
    <style>
        .card-body table span {
            display: block;
            width: 100%;
        }
    </style>
    <header class="page-header">
        <div class="d-flex align-items-center">
            <div class="mr-auto">
                <h1 class="separator">我的假期</h1>
                <nav class="breadcrumb-wrapper" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{url('/')}}"><i class="icon dripicons-home"></i></a></li>
                        <li class="breadcrumb-item"><a href="javascript:void(0)"></a>考勤管理</li>
                        <li class="breadcrumb-item active" aria-current="page">我的假期</li>
                    </ol>
                </nav>
            </div>
        </div>
    </header>
    <section class="page-content container-fluid">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">个人可用假期(单位／小时)</h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-bordered">
                        <thead>
                        <tr>
                            <th class="text-center" >法定年假</th>
                            <th class="text-center" >福利年假</th>
                            <th class="text-center" >全薪病假</th>
                            <th class="text-center" >调休</th>
                        </tr>
                        </thead>
                        <tr data-key="0">
                            <td class="text-center">
                                <span>{{ floor($vacation->annual) }}</span>
                                @if($restVacation['restAnnual'])
                                    <span class="font-size-12">{{'含去年'.floor($restVacation['restAnnual'])}}</span>
                                @else
                                    <span class="font-size-12">&nbsp;</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <span>{{ floor($vacation->company_benefits) }}</span>
                                @if($restVacation['restBenefit'])
                                    <span class="font-size-12">{{'含去年'.floor($restVacation['restBenefit'])}}</span>
                                @else
                                    <span class="font-size-12">&nbsp;</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <span>{{ floor($vacation->full_pay_sick) }}</span>
                                <span class="font-size-12">&nbsp;</span>
                            </td>
                            <td class="text-center">
                                <span>{{ $vacation->extra_day_off }}</span>
                                <span class="font-size-12">&nbsp;</span>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </section>
@endsection

