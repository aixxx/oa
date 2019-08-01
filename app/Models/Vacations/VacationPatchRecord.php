<?php

namespace App\Models\Vacations;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Vacations\VacationPatchRecord
 *
 * @property int $id
 * @property int $uid 用户id
 * @property int $company_id 公司id
 * @property int $entry_id 工作流申请id
 * @property string $patch_time 补卡时间点
 * @property string $reson 缺卡原因
 * @property string $file_upload 图片
 * @property int $patch_type 补卡类型
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @mixin \Eloquent
 */
class VacationPatchRecord extends Model
{
    //
    protected $table = 'vacation_patch_record';

    protected $fillable = [
        'uid',
        'company_id',
        'entry_id',
        'patch_time',
        'reson',
        'file_upload',
        'patch_type',
    ];
}
