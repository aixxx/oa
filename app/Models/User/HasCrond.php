<?php

namespace App\Models\User;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\User\HasCrond
 *
 * @property int $id
 * @property int $uid 用户id
 * @property int $type 类型1、年假 2、其他福利
 * @property int $cron_date 上一次脚本更新的时间
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @mixin \Eloquent
 */
class HasCrond extends Model
{
    const TYPE_ANNUAL = 1;

    //
    protected $table= "attendance_vacation_has_croned";

    protected $fillable = ['uid', 'type', 'cron_date'];

}
