<?php
namespace App\Models\AttendanceApi;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AttendanceApiClasses extends Model
{
    use SoftDeletes;

    protected $table = 'attendance_api_classes';

    public $timestamps  = true;

    /**
     * 可以被批量赋值的属性.
     *
     * @var array
     */
    protected $fillable = [
        'title','code','type',
        'work_time_begin1','work_time_begin2','work_time_begin3',
        'work_time_end1','work_time_end2','work_time_end3',
        'is_siesta','begin_siesta_time','end_siesta_time',
        'clock_time_begin1','clock_time_begin2','clock_time_begin3',
        'clock_time_end1','clock_time_end2','clock_time_end3',
        'elastic_min','serious_late_min','absenteeism_min',
        'created_at','updated_at','deleted_at',
        'admin_id','status',
    ];
    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [

    ];

    protected $appends = [];

}
