<?php

namespace App\Models\Attendance;

use App\Http\Helpers\Dh;
use Illuminate\Database\Eloquent\Model;



/**
 * App\Models\Attendance\AttendanceTravel
 *
 * @property int            $id
 * @property int|null       $entry_id      流程id
 * @property int|null       $user_id       发起人id
 * @property string         $chinese_name  中文姓名
 * @property string         $primary_dept  主部门
 * @property string         $title         标题
 * @property string         $date_begin    开始时间
 * @property string         $date_end      结束时间
 * @property int            $date_interval 出差天数（单位：天）
 * @property \Carbon\Carbon $created_at    申请时间
 * @property \Carbon\Carbon $updated_at    修改时间
 * @property string|null    $finished_at   审批完成时间
 * @property string         $address       出差地点
 * @property string|null    $cause         出差事由
 * @property string|null    $note          备注
 * @property int            $file_upload   上传附件
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Attendance\AttendanceTravel whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Attendance\AttendanceTravel whereCause($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Attendance\AttendanceTravel whereChineseName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Attendance\AttendanceTravel whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Attendance\AttendanceTravel whereDateBegin($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Attendance\AttendanceTravel whereDateEnd($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Attendance\AttendanceTravel whereDateInterval($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Attendance\AttendanceTravel whereEntryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Attendance\AttendanceTravel whereFileUpload($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Attendance\AttendanceTravel whereFinishedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Attendance\AttendanceTravel whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Attendance\AttendanceTravel whereNote($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Attendance\AttendanceTravel wherePrimaryDept($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Attendance\AttendanceTravel whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Attendance\AttendanceTravel whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Attendance\AttendanceTravel whereUserId($value)
 * @mixin \Eloquent
 */
class AttendanceTravel extends Model
{
    const DAY_TO_HOURS = 8;//出差一天这算时长

    //出差单位：天
    protected $table = 'attendance_workflow_travels';

    protected $fillable = [
        'id',
        'entry_id',
        'user_id',
        'chinese_name',
        'primary_dept',
        'title',
        'date_begin',
        'date_end',
        'date_interval',
        'created_at',
        'updated_at',
        'finished_at',
        'address',
        'cause',
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
            if (!isset($workflow['form_data']['date_begin']) ||
                !isset($workflow['form_data']['date_end']) ||
                !isset($workflow['entry']['user_id']) ||
                !isset($workflow['entry']['title'])
            ) {
                return;
            }

            $entryId = $workflow['entry']['id'];

            $travelEntry = self::where('entry_id', $entryId)->first();
            if (!$travelEntry) {
                $travelEntry = new self();
                $columnData = [
                    'entry_id'      => $entryId,
                    'user_id'       => $workflow['entry']['user_id'],
                    'title'         => $workflow['entry']['title'],
                    'chinese_name'  => $workflow['form_data']['applicant_chinese_name']['value'],
                    'primary_dept'  => $workflow['form_data']['primary_dept']['value'],
                    'date_begin'    => $workflow['form_data']['date_begin']['value'],
                    'date_end'      => $workflow['form_data']['date_end']['value'],
                    'date_interval' => isset($workflow['form_data']['date_interval']) ? $workflow['form_data']['date_interval']['value'] : 0,
                    'address'       => isset($workflow['form_data']['address']) ? $workflow['form_data']['address']['value'] : '',
                    'cause'         => isset($workflow['form_data']['cause']) ? $workflow['form_data']['cause']['value'] : '',
                    'created_at'    => Dh::getcurrentDateTime(),
                    'finished_at'   => $workflow['entry']['finish_at'],
                ];

                $travelEntry->fill($columnData);
                $travelEntry->save();
            }
        }
    }
}