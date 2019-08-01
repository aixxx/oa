<?php

namespace App\Models\Salary;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SalaryAttendance extends Model
{
    use SoftDeletes;

    protected $table = 'salary_attendance';

    public $timestamps = true;

    /**
     * 可以被批量赋值的属性.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'year',
        'month',
        'should_attendance_days',
        'actual_attendance_days',
        'casual_leave_days',
        'casual_leave_minus',
        'sick_leave_days',
        'sick_leave_minus',
        'overtime_days',
        'overtime_salary',
        'remark',
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
