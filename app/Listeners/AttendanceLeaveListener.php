<?php

namespace App\Listeners;

use App\Events\AttendanceLeaveEvent;
use App\Models\AttendanceApi\AttendanceApiAnomaly;
use App\Models\Vacations\UserVacation;
use App\Models\Workflow\Proc;

/**
 * 请假事件监听
 * Class AttendanceLeaveListener
 * @package App\Listeners
 */
class AttendanceLeaveListener
{
    /**
     * Create the event listener.
     *
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  AttendanceLeaveEvent  $event
     * @return mixed
     */
    public function handle($event)
    {
        $procsId = $event->procsId;
        $process = Proc::where('id', $procsId)->first();
        $entry_data = $process->entry->entry_data;
//        $userId = $process->user_id;
        $time_sub_by_hour = 0;
        $end_time = $begin_time = '';
        $vacation_type = 0;
        foreach ($entry_data as $entry_datum)
        {
            if($entry_datum->field_name == 'begin_time'){
                $begin_time = $entry_datum->field_value;
            }elseif ($entry_datum->field_name == 'end_time'){
                $end_time = $entry_datum->field_value;
            }elseif ($entry_datum->field_name == 'time_sub_by_hour'){
                $time_sub_by_hour = intval($entry_datum->field_value) * 60;
            }elseif ($entry_datum->field_name == 'vacation_type'){
                $vacation_type = $entry_datum->field_value;
            }
            else{
                continue;
            }
        }

        $userId = $process->entry->user_id;
        $anomalyRes = AttendanceApiAnomaly::workflowCheck($userId, $begin_time, $end_time,
            AttendanceApiAnomaly::ADDRESS_TYPE_LEAVE);
        $userVacation = UserVacation::uid($userId)->first();
        if(empty($userVacation)){
            return false;
        }
        \Log::info('userId:'.$userId);
        \Log::info('time_sub_by_hour:'.$time_sub_by_hour);
        \Log::info('vacation_type:'.$vacation_type);
        //请假类型   减主表值
            if($vacation_type == '年假'){
                $userVacation->decrement('annual_time', floatval($time_sub_by_hour/60));

            }else if($vacation_type == '调休'){
                $userVacation->decrement('rest_time', floatval($time_sub_by_hour));

            }else if($vacation_type == '事假'){

            }else if($vacation_type == '病假'){
                //$userVacation->decrement('    -----', $time_sub_by_hour);

            }else if($vacation_type == '产假'){
                $userVacation->increment('maternity_cnt');
            }else if($vacation_type == '陪产'){
                $userVacation->increment('paternity_cnt');
            }else if($vacation_type == '婚假'){
                $userVacation->increment('marital_cnt');
            }else if($vacation_type == '例假'){
                $userVacation->decrement('menstrual_time', $time_sub_by_hour);
            }else if($vacation_type == '丧假'){

            }else if($vacation_type == '哺乳假'){
                $userVacation->increment('breastfeeding_cnt');
            }
        $userVacation->save();

    }
}
