<?php

namespace App\Models\Vacations;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Vacations\VacationLeaveRecord
 *
 * @property int $id
 * @property int $uid 用户id
 * @property int $company_id 公司id
 * @property int $entry_id 工作流申请id
 * @property string $begin_time 开始时间
 * @property string $end_time 结束时间
 * @property string $reson 请假事由
 * @property int $time_sub_by_hour 时长统计
 * @property int $vacation_type 请假类型
 * @property int $balance 假期剩余
 * @property string $file_upload 图片
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vacations\VacationLeaveRecord newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vacations\VacationLeaveRecord newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vacations\VacationLeaveRecord query()
 * @mixin \Eloquent
 */
class VacationLeaveRecord extends Model
{
    //
    protected $table = 'vacation_leave_record';

    protected $fillable = [
        'uid',
        'company_id',
        'entry_id',
        'begin_time',
        'end_time',
        'reson',
        'time_sub_by_hour',
        'vacation_type',
        'balance',
        'file_upload',
    ];
}
