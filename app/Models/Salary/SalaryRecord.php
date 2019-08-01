<?php

namespace App\Models\Salary;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SalaryRecord extends Model
{

    const SALARY_RECORD_STATUS_PROCESSING = 0;//未计算
    const SALARY_RECORD_STATUS_FINISH = 1;//已计算完成

    const STATUS_PASS = 1;
    const STATUS_IN_HAND = 0;

    use SoftDeletes;

    protected $table = 'salary_record';

    public $timestamps = true;

    /**
     * 可以被批量赋值的属性.
     *
     * @var array
     */
    protected $fillable = [
        'year',
        'month',
        'count',
        'total_amount',
        'should_amount',
        'actual_amount',
        'performance_amount',
        'bonus_amount',
        'fines_amount',
        'overtime_salary_amount',
        'single_salary_amount',
        'social_company_amount',
        'fund_company_amount',
        'float_salary_amount',
        'status',
        'remark',
        'entry_id',
        'title',
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
