<?php

namespace App\Models\Vacations;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Vacations\VacationTripRecord
 *
 * @property int $id
 * @property int $business_trip_id 出差记录id
 * @property string $fd_traffic 交通工具
 * @property string $fd_go_and_back 单程往返
 * @property int $fd_start_of 出发城市
 * @property int $fd_purpose 目的城市
 * @property string $fd_begin_time 开始时间
 * @property string $fd_end_time 结束时间
 * @property int $fd_time_sub_by_day 时长(天)
 * @property string|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @mixin \Eloquent
 */
class VacationTripRecord extends Model
{
    //
    protected $table = 'vacation_trip_record';

    protected $fillable = [
        'business_trip_id',
        'fd_traffic',
        'fd_go_and_back',
        'fd_start_of',
        'fd_purpose',
        'fd_begin_time',
        'fd_end_time',
        'fd_time_sub_by_day',
    ];
}
