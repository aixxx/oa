<?php

namespace App\Services\Workflow;

use App\Http\Helpers\Dh;
use App\Models\User;
use App\Models\Workflow\Entry;
use App\Services\Attendance\CalcTimeService;
use App\Services\VacationManageService;
use App\Services\WorkflowLeaveService;
use UserFixException;
use Illuminate\Support\Facades\Auth;

class FlowHoliday implements FlowInterface
{
    //休假类型必须上传附件
    public static $typeWithFile = [
        '婚假',
        '陪产假',
        '产假',
        '产检假',
        '病假',
        '全薪病假',
        '丧假',
    ];

    //转正后享有假期类型
    public static $typeAfterRegulared = [
        '全薪病假',
        '春节假',
    ];

    /**
     * 校验表单数据合法性
     *
     * @param \App\Models\Workflow\Entry $entry
     *
     * @throws \Exception
     */
    public function checkValidate(Entry $entry)
    {
        $entry_data    = $entry->entry_data;
        $holiday_type  = $entry_data->where('field_name', 'holiday_type')->first()->field_value;
        $holiday_value = $entry_data->where('field_name', 'date_time_sub')->first()->field_value;
        $date_begin    = $entry_data->where('field_name', 'date_begin')->first()->field_value;
        $date_end      = $entry_data->where('field_name', 'date_end')->first()->field_value;
        $file_upload   = $entry_data->where('field_name', 'file_upload')->first();

        if ($date_begin > $date_end) {
            $hour = CalcTimeService::getLeaveLength($date_end, $date_begin, $holiday_type, Auth::id());
        } else {
            $hour = CalcTimeService::getLeaveLength($date_begin, $date_end, $holiday_type, Auth::id());
        }
        $data = VacationManageService::formatVacation($entry->user_id);
        foreach ($data as $d) {
            if ($d['name'] == $holiday_type && isset($d['value'])) {
                $holiday_value = str_replace($d['unit'], "", $holiday_value);
                if ($holiday_value < 4) {
                    throw new UserFixException("请假最小单位为4小时");
                }
                //因为余额已经把当前流程扣除，所以大于等于0就好
                if ($d['value'] < 0) {
                    throw new UserFixException("$holiday_type 剩余假时不足");
                }
                if ($hour != $holiday_value) {
                    throw new UserFixException("请假时长不正确");
                }
            }
        }

        if (!WorkflowLeaveService::checkHolidayRecord(Auth::id(), $date_begin, $date_end)) {
            throw new UserFixException("请假时间不能重复");
        }

        if ('丧假' == trim($holiday_type) && !in_array(trim($holiday_value), ['8小时', '24小时'])) {
            throw new UserFixException("丧假时长只能为1天或3天");
        }

        //陪产假限定性别男，产假，产检限定为性别女
        if ('陪产假' == $holiday_type && Auth::user()->gender != User::GENDER_MALE) {
            throw new UserFixException("陪产假限定员工性别男");
        }

        if (in_array($holiday_type, ['产假', '产检假']) && Auth::user()->gender != User::GENDER_FEMALE) {
            throw new UserFixException("产假、产检假限定员工性别女");
        }

        //产检假单次最多8小时
        if ('产检假' == $holiday_type) {
            preg_match("/[0-9]+/", $holiday_value, $matches);
            if ($matches[0] > 8) {
                throw new UserFixException("产检假单次最多8小时");
            }
        }

        //春节假最多三天，日历年最多一次，试用期之后
        //转正已处理 EntryController->updateOrCreateEntry
        if ('春节假' == $holiday_type) {
            preg_match("/[0-9]+/", $holiday_value, $matches);
            if ($matches[0] > 3 * 8) {
                throw new UserFixException("春节假最多三天");
            }

            $year_start = date('Y-01-01', strtotime($date_begin));
            $year_end = date('Y-01-01', strtotime($year_start."+12 month"));
            if(!WorkflowLeaveService::checkChineseSpringFestival(Auth::id(), $year_start, $year_end)) {
                throw new UserFixException("春节假日历年最多申请一次");
            }

        }

        //同一天请假时长不要超过8小时
        if (!WorkflowLeaveService::checkSameDateHolidayRecord(Auth::id(), $date_begin, $date_end, $holiday_type)) {
            throw new UserFixException("同一天请假总时长不要超过8小时");
        }

        //婚假等休假必须上传附件
        if (isset($holiday_type) && in_array($holiday_type, FlowHoliday::$typeWithFile)) {
            if (!$file_upload || !$file_upload->field_value) {
                throw new UserFixException(sprintf("%s必须上传附件", $holiday_type));
            }
        }

        //春节假等转正后可申请
        if (isset($holiday_type) && in_array($holiday_type, FlowHoliday::$typeAfterRegulared)) {
            $regular_at = Auth::user()->regular_at;
            if (!$regular_at || Dh::formatDate($regular_at) > Dh::todayDate()) {
                throw new UserFixException(sprintf("%s转正后可申请", $holiday_type));
            }
        }

    }
}