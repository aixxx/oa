<?php

namespace App\Http\Controllers\User;

use App\Models\UsersTurnoverStats;
use App\Http\Controllers\Controller;
use Validator;

class StatisticsController extends Controller
{
    /**
     * 列表页
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function turnover()
    {
        $lastdate = UsersTurnoverStats::getLastPeriodBeginDate();
        $turnvoers = UsersTurnoverStats::where('begin_date', '=', $lastdate)->orderBy('department_structure', SORT_DESC)->get();

        $sum = [
            'begin_total'      => 0,
            'end_total'        => 0,
            'sh_join'          => 0,
            'sh_leave'         => 0,
            'cd_join'          => 0,
            'cd_leave'         => 0,
            'sz_join'          => 0,
            'sz_leave'         => 0,
            'bj_join'          => 0,
            'bj_leave'         => 0,
            'px_join'          => 0,
            'px_leave'         => 0,
            'part_time_worker' => 0,
            'adviser'          => 0,
            'passive_leave'    => 0,
            'voluntary_leave'  => 0,
            'leader'           => 0,
            'temporary'        => 0,
            'week'             => 0,
            'end_date'         => 0,
            'ignore_total'     => 0,
        ];
        $week = '';
        $turnvoers->each(function ($item, $key) use (& $sum, & $week){
            $week = $item;

            $sum['begin_total']      += $item->begin_total;
            $sum['end_total']        += $item->end_total;
            $sum['sh_join']          += $item->sh_join;
            $sum['sh_leave']         += $item->sh_leave;
            $sum['cd_join']          += $item->cd_join;
            $sum['cd_leave']         += $item->cd_leave;
            $sum['sz_join']          += $item->sz_join;
            $sum['sz_leave']         += $item->sz_leave;
            $sum['bj_join']          += $item->bj_join;
            $sum['bj_leave']         += $item->bj_leave;
            $sum['px_join']          += $item->px_join;
            $sum['px_leave']         += $item->px_leave;
            $sum['passive_leave']    += $item->passive_leave;
            $sum['voluntary_leave']  += $item->voluntary_leave;
            $sum['part_time_worker'] = $item->part_time_worker;
            $sum['adviser']          = $item->adviser;
            $sum['leader']           = $item->leader;
            $sum['temporary']        = $item->temporary;
            $sum['week']             = $item->week;
            $sum['end_date']         = $item->end_date;
            $sum['ignore_total']     = $item->ignore_total;

        });

        return view('user.statistics.turnover',compact('turnvoers', 'week','sum'));
    }
}
