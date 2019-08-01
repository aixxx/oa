<?php

namespace App\Models\Vacations;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Vacations\VacationExtra
 *
 * @mixin \Eloquent
 * @property int $id
 * @property int $uid 用户id
 * @property int $company_id 公司id
 * @property int $entry_id 工作流申请id
 * @property string $begin_time 开始时间
 * @property string $end_time 结束时间
 * @property int $time_sub_by_hour 时长统计
 * @property string $reson 加班原因
 * @property string $file_upload 图片
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 */
class VacationExtraRecord extends Model
{
    //
    protected $table = 'vacation_extra';

    protected $fillable = [
        'uid',
        'company_id',
        'entry_id',
        'begin_time',
        'end_time',
        'time_sub_by_hour',
        'reson',
        'file_upload',
    ];
}
