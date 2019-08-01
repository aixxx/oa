<?php
namespace App\Models\AttendanceApi;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AttendanceApiOvertimeRule extends Model
{
    use SoftDeletes;

    protected $table = 'attendance_api_overtime_rule';

    public $timestamps  = true;

    /**
     * 可以被批量赋值的属性.
     *
     * @var array
     */
    protected $fillable = [
        'is_working_overtime','working_overtime_type','working_begin_time',
        'working_min_overtime','is_rest_overtime','rest_overtime_type',
        'rest_min_overtime','title',
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

}
