<?php

namespace App\Models\Attendance;

use App\Http\Helpers\Dh;
use Illuminate\Database\Eloquent\Model;



/**
 * App\Models\Attendance\AttendanceLeave
 *
 * @property int $id
 * @property int|null $entry_id         流程id
 * @property int|null $user_id          发起人id
 * @property string $title              标题
 * @property string $holiday_type       休假类型
 * @property string $date_begin         开始时间
 * @property string $date_end           结束时间
 * @property int $date_time_sub         请假时长
 * @property \Carbon\Carbon $created_at 申请时间
 * @property \Carbon\Carbon $updated_at 修改时间
 * @property string|null $finished_at   审批完成时间
 * @property string|null $remark        备注
 * @property int|null $file_upload      上传附件
 * @property int|null $is_resumed       休假1是否销假:(0:未销;1:已销)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Attendance\AttendanceLeave whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Attendance\AttendanceLeave whereDateBegin($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Attendance\AttendanceLeave whereDateEnd($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Attendance\AttendanceLeave whereDateTimeSub($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Attendance\AttendanceLeave whereEntryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Attendance\AttendanceLeave whereFileUpload($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Attendance\AttendanceLeave whereFinishedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Attendance\AttendanceLeave whereHolidayType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Attendance\AttendanceLeave whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Attendance\AttendanceLeave whereRemark($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Attendance\AttendanceLeave whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Attendance\AttendanceLeave whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Attendance\AttendanceLeave whereUserId($value)
 * @mixin \Eloquent
 * @property string|null $ehr_deal_status ehr数据处理状态
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Attendance\AttendanceLeave whereEhrDealStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Attendance\AttendanceLeave whereIsResumed($value)
 */
class AttendanceLeave extends Model
{
    const RESUMPTION_TYPE_NO = 0; //休假1是否销假:(0:未销;1:已销)
    const RESUMPTION_TYPE_YES = 1; //休假1是否销假:(0:未销;1:已销)

    protected $table = 'attendance_workflow_leaves';

    protected $fillable = [
        'id',
        'entry_id',
        'user_id',
        'title',
        'holiday_type',
        'date_begin',
        'date_end',
        'date_time_sub',
        'created_at',
        'updated_at',
        'finished_at',
        'remark',
        'file_upload',
        'is_resumed',
    ];

    /**
     * 请假流程数据记录入库
     *
     * @param $params array
     */
    public static function workflowImport($params)
    {
        if (is_array($params)) {
            $workflow = reset($params);

            //无效数据不入库
            if (!isset($workflow['form_data']['holiday_type']) ||
                !isset($workflow['form_data']['date_begin']) ||
                !isset($workflow['form_data']['date_end']) ||
                !isset($workflow['entry']['user_id']) ||
                !isset($workflow['entry']['title'])
            ) {
                return;
            }

            $entryId = $workflow['entry']['id'];

            $leaveEntry = self::where('entry_id', $entryId)->first();
            if (!$leaveEntry) {
                $leaveEntry = new self();
                $columnData = [
                    'entry_id'      => $entryId,
                    'user_id'       => $workflow['entry']['user_id'],
                    'title'         => $workflow['entry']['title'],
                    'holiday_type'  => $workflow['form_data']['holiday_type']['value'],
                    'date_begin'    => $workflow['form_data']['date_begin']['value'],
                    'date_end'      => $workflow['form_data']['date_end']['value'],
                    'date_time_sub' => isset($workflow['form_data']['date_time_sub']) ? $workflow['form_data']['date_time_sub']['value'] : 0,
                    'remark'        => isset($workflow['form_data']['cause']) ? $workflow['form_data']['cause']['value'] : '',
                    'file_upload'   => isset($workflow['form_data']['file_upload']) ? $workflow['form_data']['file_upload']['value'] : 0,
                    'created_at'    => Dh::getcurrentDateTime(),
                    'finished_at'   => $workflow['entry']['finish_at'],
                ];

                $leaveEntry->fill($columnData);
                $leaveEntry->save();
            }
        }
    }

    /**
     *  被销假的请假流程标志为已销假
     *
     * @param $entry_id
     */
    public static function leaveResumption($entryId)
    {
        $leave = self::where('entry_id', $entryId)->first();

        if ($leave) {
            $leave->update(['is_resumed' => self::RESUMPTION_TYPE_YES]);
        }
    }
}