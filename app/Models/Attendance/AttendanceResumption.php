<?php

namespace App\Models\Attendance;

use App\Http\Helpers\Dh;
use Illuminate\Database\Eloquent\Model;




/**
 * App\Models\Attendance\AttendanceResumption
 *
 * @property int $id
 * @property int|null $entry_id 流程id
 * @property int|null $user_id 发起人id
 * @property string $title 标题
 * @property int|null $resumption_leave_list 对应已审批请假流程id
 * @property float $resumption_leave_length 销假时长
 * @property \Illuminate\Support\Carbon $created_at 申请时间
 * @property \Illuminate\Support\Carbon $updated_at 修改时间
 * @property string|null $finished_at 审批完成时间
 * @property string|null $resumption_leave_cause 销假原因
 * @property int $file_upload 上传附件
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Attendance\AttendanceResumption whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Attendance\AttendanceResumption whereEntryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Attendance\AttendanceResumption whereFileUpload($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Attendance\AttendanceResumption whereFinishedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Attendance\AttendanceResumption whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Attendance\AttendanceResumption whereResumptionLeaveCause($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Attendance\AttendanceResumption whereResumptionLeaveLength($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Attendance\AttendanceResumption whereResumptionLeaveList($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Attendance\AttendanceResumption whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Attendance\AttendanceResumption whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Attendance\AttendanceResumption whereUserId($value)
 * @mixin \Eloquent
 */
class AttendanceResumption extends Model
{
    protected $table = 'attendance_workflow_resumptions';

    protected $fillable = [
        'entry_id',
        'user_id',
        'title',
        'resumption_leave_list',
        'resumption_leave_length',
        'created_at',
        'updated_at',
        'finished_at',
        'resumption_leave_cause',
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
            $entryId = $workflow['entry']['id'];

            //无效数据不入库
            if (!isset($workflow['form_data']['resumption_leave_cause']) ||
                !isset($workflow['form_data']['resumption_leave_length']) ||
                !isset($workflow['form_data']['resumption_leave_list']) ||
                !isset($workflow['entry']['user_id']) ||
                !isset($workflow['entry']['title'])
            ) {
                return;
            }

            $resumptionEntry = self::where('entry_id', $entryId)->first();

            if (!$resumptionEntry) {
                //被销假的请假流程有唯一键过滤
                $leaveEntry = self::where('resumption_leave_list', $workflow['form_data']['resumption_leave_list']['value'])->first();
                if (!$leaveEntry) {
                    $resumptionEntry = new self();
                    $columnData = [
                        'entry_id' => $entryId,
                        'user_id' => $workflow['entry']['user_id'],
                        'title' => $workflow['entry']['title'],
                        'resumption_leave_list' => $workflow['form_data']['resumption_leave_list']['value'],
                        'resumption_leave_length' => $workflow['form_data']['resumption_leave_length']['value'],
                        'resumption_leave_cause' => isset($workflow['form_data']['resumption_leave_cause']) ? $workflow['form_data']['resumption_leave_cause']['value'] : '',
                        'file_upload' => isset($workflow['form_data']['file_upload']) ? $workflow['form_data']['file_upload']['value'] : 0,
                        'created_at' => Dh::getcurrentDateTime(),
                        'finished_at' => $workflow['entry']['finish_at'],
                    ];

                    $resumptionEntry->fill($columnData);
                    $resumptionEntry->save();
                }
            }
        }
    }
}