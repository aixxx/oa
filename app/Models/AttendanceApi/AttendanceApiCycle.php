<?php

namespace App\Models\AttendanceApi;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AttendanceApiCycle extends Model
{
    use SoftDeletes;

    protected $table = 'attendance_api_cycle';

    public $timestamps  = true;

    /**
     * 可以被批量赋值的属性.
     *
     * @var array
     */
    protected $fillable = [
        'type','title','cycle_days',
        'created_at','updated_at','deleted_at',
        'admin_id'
    ];
    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [

    ];

    protected $appends = [];

    public function content(){
        return $this->hasMany(AttendanceApiCycleContent::class, 'cycle_id', 'id')->select(['sort','cycle_id','classes_id']);
    }
}
