<?php

namespace App\Models\Vacations;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Vacations\VacationBusinessTripRecord
 *
 * @property int $id
 * @property int $uid 用户id
 * @property int $company_id 公司id
 * @property int $entry_id 工作流申请id
 * @property string $reson 出差事由
 * @property int $trip_days 出差天数
 * @property string|null $remark 出差备注
 * @property string|null $other_people 同行人
 * @property string|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @mixin \Eloquent
 */
class VacationBusinessTripRecord extends Model
{
    //
    protected $table = 'vacation_business_trip_record';

    protected $fillable = [
        'uid',
        'company_id',
        'entry_id',
        'reson',
        'trip_days',
        'remark',
        'other_people',
    ];


    public function trip(){
        return $this->hasMany(VacationTripRecord::class, 'business_trip_id','id');
    }
}
