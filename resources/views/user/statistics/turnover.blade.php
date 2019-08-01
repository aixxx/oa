@extends('layouts.main', ['title' =>'入离职员工统计'])

@section('content')
    <header class="page-header">
        <div class="d-flex align-items-center">
            <div class="mr-auto">
                <h1 class="separator">入离职统计</h1>
                <nav class="breadcrumb-wrapper" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{url('/')}}"><i class="icon dripicons-home"></i></a></li>
                        <li class="breadcrumb-item"><a href="javascript:void(0)"></a>员工管理</li>
                        <li class="breadcrumb-item active" aria-current="page">入离职统计</li>
                    </ol>
                </nav>
            </div>
        </div>
    </header>
    <section class="page-content container-fluid">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><?php echo date('Y年m月第w周', strtotime($sum['end_date'])); ?>集团入离职统计</h3>
            </div>
            <div class="card-body">
                <div class="table-responsive" id="sum" data-data='<?php echo json_encode($sum) ?>'>
                    <table class="table table-striped table-hover table-bordered" style="min-width:100%">
                        <thead>
                        <tr>
                            <th class="text-center" rowspan="2">部门体系</th>
                            <th class="text-center" rowspan="2">一级部门</th>
                            <th class="text-center" rowspan="2">期初人数</th>
                            <th class="text-center" colspan="2">上海</th>
                            <th class="text-center" colspan="2">成都</th>
                            <th class="text-center" colspan="2">深圳</th>
                            <th class="text-center" colspan="2">北京</th>
                            <th class="text-center" colspan="2">萍乡</th>
                            <th class="text-center" rowspan = "2">期末人数</th>
                        </tr>
                        <tr>
                            <th class="text-center">本周入职</th>
                            <th class="text-center">本周离职</th>
                            <th class="text-center">本周入职</th>
                            <th class="text-center">本周离职</th>
                            <th class="text-center">本周入职</th>
                            <th class="text-center">本周离职</th>
                            <th class="text-center">本周入职</th>
                            <th class="text-center">本周离职</th>
                            <th class="text-center">本周入职</th>
                            <th class="text-center">本周离职</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($turnvoers as $turnvoer)
                            <tr data-key="0">
                                <td class="text-right">{{ $turnvoer->department_structure }}</td>
                                <td class="text-right">{{ $turnvoer->first_level }}</td>
                                <td class="text-right">{{ $turnvoer->begin_total ? $turnvoer->begin_total : '' }}</td>
                                <td class="text-right">{{ $turnvoer->sh_join ? $turnvoer->sh_join : '' }}</td>
                                <td class="text-right">{{ $turnvoer->sh_leave ? $turnvoer->sh_leave : '' }}</td>
                                <td class="text-right">{{ $turnvoer->cd_join ? $turnvoer->cd_join : '' }}</td>
                                <td class="text-right">{{ $turnvoer->cd_leave ? $turnvoer->cd_leave : '' }}</td>
                                <td class="text-right">{{ $turnvoer->sz_join ? $turnvoer->sz_join : '' }}</td>
                                <td class="text-right">{{ $turnvoer->sz_leave ? $turnvoer->sz_leave : '' }}</td>
                                <td class="text-right">{{ $turnvoer->bj_join ? $turnvoer->bj_join : '' }}</td>
                                <td class="text-right">{{ $turnvoer->bj_leave ? $turnvoer->bj_leave : '' }}</td>
                                <td class="text-right">{{ $turnvoer->px_join ? $turnvoer->px_join : '' }}</td>
                                <td class="text-right">{{ $turnvoer->px_leave ? $turnvoer->px_leave : '' }}</td>
                                <td class="text-right">{{ $turnvoer->end_total ? $turnvoer->end_total : '' }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="text-left">
                    <span>备注：1.表中未含以下人员：兼职{{ $sum['part_time_worker'] }}人；顾问：{{ $sum['adviser'] }}人；高管{{ $sum['leader'] }}人；因企业架构调整暂未放入管理层体系的临时人员：{{ $sum['temporary'] }}人；共计{{ $sum['ignore_total'] }}人</span>
                    <br>
                    <span>2.期初实际总人数：{{ $sum['begin_total'] +  $sum['ignore_total']}}人；期末实际总人数：{{ $sum['end_total'] + $sum['ignore_total'] }}人</span>
                </div>
                <br>
                <div class="text-left">
                    <span>截止至{{date('Y月m日第w周', strtotime($sum['end_date']))}},人员情况概述</span>
                    <br>
                    <span>在职人数：{{ $sum['end_total'] + $sum['ignore_total'] }}</span>
                    <br>
                    <span>本周新入职人数：{{ $sum['sh_join'] +  $sum['cd_join'] + $sum['sz_join']+ $sum['px_join']+ $sum['bj_join'] }}</span>
                    <br>
                    <span>本周离职总人数：{{ $sum['sh_leave'] +  $sum['cd_leave'] + $sum['sz_leave']+ $sum['bj_leave']+ $sum['px_leave'] }}；本周离职率{{ ($sum['end_total'] + $sum['ignore_total']) > 0 ? number_format(($sum['sh_leave'] + $sum['cd_leave'] + $sum['sz_leave'] + $sum['bj_leave']+ $sum['px_leave']) * 100 /($sum['end_total'] + $sum['ignore_total']), 2) : 0  }}%</span>
                    <br>
                    <span>主动离职：{{ $sum['sh_leave'] +  $sum['cd_leave'] + $sum['sz_leave']+ $sum['bj_leave']+ $sum['px_leave'] }}人；被动离职：0人；</span>
                </div>
            </div>
        </div>
    </section>
@endsection
@section('javascript')
    <script>
        var sum = eval('(' + $("#sum").attr('data-data') + ')');
        var tr_node = ' <tr data-key="0">';

        tr_node += '<td class="text-center" colspan="2">总计</td>';
        tr_node += '<td class="text-right">' + sum.begin_total + '</td>';
        tr_node += '<td class="text-right">' + sum.sh_join + '</td>';
        tr_node += '<td class="text-right">' + sum.sh_leave + '</td>';
        tr_node += '<td class="text-right">' + sum.cd_join + '</td>';
        tr_node += '<td class="text-right">' + sum.cd_leave + '</td>';
        tr_node += '<td class="text-right">' + sum.sz_join + '</td>';
        tr_node += '<td class="text-right">' + sum.sz_leave + '</td>';
        tr_node += '<td class="text-right">' + sum.bj_join + '</td>';
        tr_node += '<td class="text-right">' + sum.bj_leave + '</td>';
        tr_node += '<td class="text-right">' + sum.px_join + '</td>';
        tr_node += '<td class="text-right">' + sum.px_leave + '</td>';
        tr_node += '<td class="text-right">' + sum.end_total + '</td>';

        tr_node += '</tr>';

        $("table tbody").append(tr_node);
    </script>
@endsection
