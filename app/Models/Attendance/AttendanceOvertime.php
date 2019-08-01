<?php

namespace App\Models\Attendance;

use App\Http\Helpers\Dh;
use Illuminate\Database\Eloquent\Model;



/**
 * App\Models\Attendance\AttendanceOvertime
 *
 * @property int            $id
 * @property int|null       $entry_id         流程id
 * @property int|null       $user_id          发起人id
 * @property string         $title            标题
 * @property string         $begin_time       开始时间
 * @property string         $end_time         结束时间
 * @property int            $time_sub_by_hour 请假时长
 * @property \Carbon\Carbon $created_at       申请时间
 * @property \Carbon\Carbon $updated_at       修改时间
 * @property string|null    $finished_at      审批完成时间
 * @property string|null    $note             备注
 * @property int            $file_upload      上传附件
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Attendance\AttendanceOvertime whereBeginTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Attendance\AttendanceOvertime whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Attendance\AttendanceOvertime whereEndTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Attendance\AttendanceOvertime whereEntryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Attendance\AttendanceOvertime whereFileUpload($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Attendance\AttendanceOvertime whereFinishedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Attendance\AttendanceOvertime whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Attendance\AttendanceOvertime whereNote($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Attendance\AttendanceOvertime whereTimeSubByHour($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Attendance\AttendanceOvertime whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Attendance\AttendanceOvertime whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Attendance\AttendanceOvertime whereUserId($value)
 * @mixin \Eloquent
 * @property string|null $ehr_deal_status ehr数据处理状态
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Attendance\AttendanceOvertime whereEhrDealStatus($value)
 */
class AttendanceOvertime extends Model
{
    protected $table = 'attendance_workflow_overtimes';

    protected $fillable = [
        'id',
        'entry_id',
        'user_id',
        'title',
        'begin_time',
        'end_time',
        'time_sub_by_hour',
        'created_at',
        'updated_at',
        'finished_at',
        'note',
        'file_upload',
    ];

    /**
     * 出差流程数据记录入库
     *
     * @param $params array
     */
    public static function workflowImport($params)
    {
        if (is_array($params)) {
            $workflow = reset($params);

            //无效数据不入库
            if (!isset($workflow['form_data']['begin_time']) ||
                !isset($workflow['form_data']['end_time']) ||
                !isset($workflow['entry']['user_id']) ||
                !isset($workflow['entry']['title'])
            ) {
                return;
            }

            $entryId = $workflow['entry']['id'];

            $entry = self::where('entry_id', $entryId)->first();
            if (!$entry) {
                $entry = new self();
                $columnData = [
                    'entry_id'         => $entryId,
                    'user_id'          => $workflow['entry']['user_id'],
                    'title'            => $workflow['entry']['title'],
                    'begin_time'       => $workflow['form_data']['begin_time']['value'],
                    'end_time'         => $workflow['form_data']['end_time']['value'],
                    'time_sub_by_hour' => isset($workflow['form_data']['time_sub_by_hour']) ? $workflow['form_data']['time_sub_by_hour']['value'] : 0,
                    'note'             => isset($workflow['form_data']['note']) ? $workflow['form_data']['note']['value'] : 0,
                    'created_at'       => Dh::getcurrentDateTime(),
                    'finished_at'      => $workflow['entry']['finish_at'],
                ];

                $entry->fill($columnData);
                $entry->save();
            }
        }
    }
}