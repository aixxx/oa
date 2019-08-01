<?php

namespace App\Services\Workflow;

use App\Http\Helpers\Dh;
use App\Http\Helpers\StringHelper;
use App\Models\Workflow\Entry;
use App\Models\Workflow\EntryData;
use App\Models\Workflow\Flow;
use App\Models\Workflow\Proc;
use App\Models\Workflow\Workflow;
use App\Services\WorkflowMessageService;
use App\Services\WorkflowTaskService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use UserFixException;
class FlowAttendanceOvertime implements FlowInterface
{
    /**
     * 校验表单数据合法性
     *
     * @param \App\Models\Workflow\Entry $entry
     *
     * @throws \Exception
     */
    public function checkValidate(Entry $entry)
    {
        $entry_data  = $entry->entry_data;
        $field_value = $entry_data->where('field_name', 'time_sub_by_hour')->first()->field_value;
        if ($field_value <= 0) {
            throw new UserFixException("加班时长不能为0小时");
        }

        //加班，对时间段进行判断，只要有在该时间段有已经审核通过或者正在走的流程，不允许提交申请
        $begin_at = $entry_data->where('field_name', 'begin_time')->first()->field_value;
        $end_at   = $entry_data->where('field_name', 'end_time')->first()->field_value;

        $workflows = EntryData::leftJoin('workflow_entries', 'workflow_entry_data.entry_id', 'workflow_entries.id')
            ->where('workflow_entries.flow_id', $entry->flow_id)
            ->where('user_id', Auth::id())
            ->whereIn('status', [Entry::STATUS_IN_HAND, Entry::STATUS_FINISHED])    //进行中，结束
            ->where('entry_id','<>', $entry->id)    //过滤当前流程
            ->where(function ($query) use ($begin_at, $end_at) {
                $query->where(function ($query) use ($begin_at) {
                    $query->where('field_name', '=', 'begin_time')
                        ->where('field_value', '>=', Dh::formatDate($begin_at));
                })->orWhere(function ($query) use ($end_at) {
                    $query->where('field_name', '=', 'end_time')->where('field_value', '<=', Dh::addDays($end_at, 1));
                });
            })->groupBy('entry_id')->having(DB::raw('COUNT(1)'), '>', 1)->get();

        if ($workflows) {
            foreach ($workflows as $workflow) {
                $workflow_entry_data = EntryData::where('entry_id', $workflow->entry_id)->get();

                $workflow_begin_time = $workflow_entry_data->where('field_name','begin_time')->first()->field_value;
                $workflow_end_time = $workflow_entry_data->where('field_name','end_time')->first()->field_value;

                if (($begin_at >= $workflow_begin_time && $begin_at < $workflow_end_time) ||
                    ($end_at > $workflow_begin_time) && $end_at <= $workflow_end_time ||
                    ($workflow_begin_time >= $begin_at && $workflow_begin_time < $end_at) ||
                    ($workflow_end_time > $begin_at) && $workflow_end_time <= $end_at) {
                    throw new UserFixException("重复申请，该时间段已经有流程提交！");
                }
            }
        }
    }
}