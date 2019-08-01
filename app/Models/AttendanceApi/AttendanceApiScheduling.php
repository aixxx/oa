<?php

namespace App\Models\AttendanceApi;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AttendanceApiScheduling extends Model
{
    use SoftDeletes;

    protected $table = 'attendance_api_scheduling';

    public $timestamps  = true;

    /**
     * 可以被批量赋值的属性.
     *
     * @var array
     */
    protected $fillable = [
        'attendance_id','user_id','dates',
        'take_effect_dates','classes_id',
        'created_at','updated_at','deleted_at',
    ];
    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [

    ];

    protected $appends = [];

    /*
     * 获取排班信息
     * $user_id  用户ID
     * $dates   日期
     * */
    public static function getSchedulingInfo($user_id, $dates = ''){
        $dt = Carbon::now()->toDateString();
        $dates = $dates ?: $dt;
        return self::query()->where([
            ['user_id', '=', $user_id],
            ['dates', '=', $dates],
            ['take_effect_dates', '<=', $dates],
        ])->orderBy('take_effect_dates', 'desc')
            ->orderBy('id', 'desc')
            ->first();
    }

    public static function getSchedulingList($user_id, $begin, $end){
        return self::query()->where([
            ['user_id', '=', $user_id],
            ['dates', '>=', $begin],
            ['dates', '<=', $end],
            ['take_effect_dates', '<=', $end],
        ])->orderBy('take_effect_dates', 'desc')
            ->orderBy('id', 'desc')
            ->get();
    }


    public static function getSchedulingUserId($begin, $end){
        return self::query()->where([
            ['dates', '>=', $begin],
            ['dates', '<=', $end],
            ['take_effect_dates', '<=', $end],
        ])->groupBy('user_id')
            ->orderBy('take_effect_dates', 'desc')
            ->orderBy('id', 'desc')->pluck('user_id');
    }


    public function classes(){
        return $this->hasOne(AttendanceApiClasses::class,'id', 'classes_id');
    }
}
