<?php

namespace App\Models\Attendance;

use Illuminate\Database\Eloquent\Model;


/**
 * App\Models\Attendance\AnnualRule
 *
 * @property int $id
 * @property int $min 区间启始值
 * @property int $max 区间结束值
 * @property int $value 区间值
 * @property int|null $type 年假方案
 * @property string $description 描述
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Attendance\AnnualRule newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Attendance\AnnualRule newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Attendance\AnnualRule query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Attendance\AnnualRule whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Attendance\AnnualRule whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Attendance\AnnualRule whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Attendance\AnnualRule whereMax($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Attendance\AnnualRule whereMin($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Attendance\AnnualRule whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Attendance\AnnualRule whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Attendance\AnnualRule whereValue($value)
 * @mixin \Eloquent
 */
class AnnualRule extends Model
{
    protected $table = 'attendance_annual_rule';

    //
    protected $fillable = ['min', 'max', 'value', 'type', 'description'];


}
