<?php

namespace App\Models\Attendance;

use App\Http\Helpers\Dh;
use Illuminate\Database\Eloquent\Model;



/**
 * App\Models\Attendance\AttendanceRetroactive
 *
 * @property int            $id
 * @property int|null       $entry_id             流程id
 * @property int|null       $user_id              发起人id
 * @property string         $title                标题
 * @property string         $retroactive_datatime 补签时间
 * @property string         $retroactive_type     上下班类型
 * @property \Carbon\Carbon $created_at           申请时间
 * @property \Carbon\Carbon $updated_at           修改时间
 * @property string|null    $finished_at          审批完成时间
 * @property string|null    $retroactive_reason   补签原因
 * @property string|null    $note                 备注
 * @property int|null       $file_upload          上传附件
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Attendance\AttendanceRetroactive whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Attendance\AttendanceRetroactive whereEntryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Attendance\AttendanceRetroactive whereFileUpload($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Attendance\AttendanceRetroactive whereFinishedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Attendance\AttendanceRetroactive whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Attendance\AttendanceRetroactive whereNote($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Attendance\AttendanceRetroactive whereRetroactiveDatatime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Attendance\AttendanceRetroactive whereRetroactiveReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Attendance\AttendanceRetroactive whereRetroactiveType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Attendance\AttendanceRetroactive whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Attendance\AttendanceRetroactive whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Attendance\AttendanceRetroactive whereUserId($value)
 * @mixin \Eloquent
 */
class AttendanceRetroactive extends Model
{
    protected $table = 'attendance_workflow_retroactives';

    protected $fillable = [
        'id',
        'entry_id',
        'user_id',
        'title',
        'retroactive_datatime',
        'retroactive_type',
        'created_at',
        'updated_at',
        'finished_at',
        'retroactive_reason',
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
            $entryId = $workflow['entry']['id'];

            //无效数据不入库
            if (!isset($workflow['form_data']['retroactive_reason']) ||
                !isset($workflow['form_data']['retroactive_datatime']) ||
                !isset($workflow['form_data']['retroactive_type']) ||
                !isset($workflow['entry']['user_id']) ||
                !isset($workflow['entry']['title'])
            ) {
                return;
            }

            $travelEntry = self::where('entry_id', $entryId)->first();

            if (!$travelEntry) {
                $retroactiveType = $workflow['form_data']['retroactive_type']['value'];
                $subStr = '签卡';
                if (!strstr($retroactiveType, $subStr)) {
                    $retroactiveType .= $subStr;
                }

                $travelEntry = new self();
                $columnData = [
                    'entry_id'             => $entryId,
                    'user_id'              => $workflow['entry']['user_id'],
                    'title'                => $workflow['entry']['title'],
                    'retroactive_datatime' => $workflow['form_data']['retroactive_datatime']['value'],
                    'retroactive_type'     => $retroactiveType,
                    'retroactive_reason'   => $workflow['form_data']['retroactive_reason']['value'],
                    'note'                 => $workflow['form_data']['note']['value'],
                    'created_at'           => Dh::getcurrentDateTime(),
                    'finished_at'          => $workflow['entry']['finish_at'],
                ];

                $travelEntry->fill($columnData);
                $travelEntry->save();
            }
        }
    }
}