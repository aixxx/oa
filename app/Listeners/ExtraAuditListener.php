<?php

namespace App\Listeners;


use App\Constant\ConstFile;
use App\Events\ExtraAuditEvent;
use App\Models\AttendanceApi\AttendanceApiAnomaly;
use App\Models\Vacations\UserVacation;
use App\Models\Vacations\VacationExtraWorkflowPass;
use App\Models\Workflow\Proc;

/**
 * 加班监听事件
 * Class ExtraAuditListener
 * @package App\Listeners
 */
class ExtraAuditListener
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
     * @param  ExtraAuditEvent  $event
     * @return mixed
     */
    public function handle($event)
    {
        $procsId = $event->procsId;
        $process = Proc::where('id', $procsId)->first();
        $entry_data = $process->entry->entry_data;
        $userId = $process->entry->user_id;
        $time_sub_by_hour = 0;
        $end_time = $begin_time = '';
        foreach ($entry_data as $entry_datum)
        {
            if($entry_datum->field_name == 'begin_time'){
                $begin_time = $entry_datum->field_value;
            }elseif ($entry_datum->field_name == 'end_time'){
                $end_time = $entry_datum->field_value;
            }elseif ($entry_datum->field_name == 'time_sub_by_hour'){
                $time_sub_by_hour = floatval($entry_datum->field_value) * 60;
            }
            else{
                continue;
            }
        }
        $value = 0;
        //加班  按考勤为准后 剩余按审批单的时间为准
        $anomalyRes = AttendanceApiAnomaly::overtimeCheck($userId, $begin_time, $end_time);
        if($anomalyRes['code'] == ConstFile::API_RESPONSE_FAIL){
            return false;
        }else{
            $rest_time = $anomalyRes['rest_time'];
            $cannot = $anomalyRes['cannot'];
            if(count($cannot) > 0){
                //需要按照审批单为准
                $value = $time_sub_by_hour-$rest_time;  //减去打卡已算的时间
                //将需要审批的数据 写入单独的数据表 待处理
                VacationExtraWorkflowPass::query()->create([
                    'begin_end_dates' => implode(',', $cannot),
                    'times' => $value,
                    'user_id' => $userId,
                    'entry_id' => $process->entry_id,
                ]);
            }
        }

        //增加调休
        $vacationObj = UserVacation::query()->where('user_id', '=', $userId)->first();
        if($vacationObj){
            $vacationObj->increment('rest_time', $value); //分钟
            $vacationObj->save();
        }else{
            $vacationObj = UserVacation::query()->create(['user_id' => $userId,
                'rest_time' => $value
            ]);
        }
    }
}
