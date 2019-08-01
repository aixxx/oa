<?php

namespace App\Models\AttendanceApi;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AttendanceApiCycleContent extends Model
{
    use SoftDeletes;

    protected $table = 'attendance_api_cycle_content';

    public $timestamps  = true;

    /**
     * 可以被批量赋值的属性.
     *
     * @var array
     */
    protected $fillable = [
        'sort','cycle_id','classes_id',
        'created_at','updated_at',
        'admin_id',
    ];
    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [

    ];

    protected $appends = [];

    public function classes(){
        return $this->hasOne(AttendanceApiClasses::class, 'id', 'classes_id')->select(['id','title']);
    }
}
