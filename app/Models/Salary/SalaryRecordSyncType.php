<?php

namespace App\Models\Salary;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SalaryRecordSyncType extends Model
{
    use SoftDeletes;

    protected $table = 'salary_record_sync_type';

    public $timestamps = true;

    /**
     * 计算薪资之前所需要的步骤
     */
    const SALARY_GENERATE_STEP_REFRESH = '1';
    const SALARY_GENERATE_STEP_SYNC_ATTENDANCE = '2';
    const SALARY_GENERATE_STEP_SYNC_PROFIT_SHARE = '3';
    const SALARY_GENERATE_STEP_SYNC_PERFORMANCE = '4';
    const SALARY_GENERATE_STEP_SYNC_REWORDS_AND_PUNISHMENT = '5';
    const SALARY_GENERATE_STEP_SYNC_FLOAT_SALARY = '6';
    const SALARY_GENERATE_STEP_SYNC_TAX = '7';
    const SALARY_GENERATE_STEP_SYNC_SOCIAL_SECURITY = '8';

    public static $typeList = [
        self:: SALARY_GENERATE_STEP_REFRESH => '刷新',
        self:: SALARY_GENERATE_STEP_SYNC_ATTENDANCE => '同步考勤',
        self:: SALARY_GENERATE_STEP_SYNC_PROFIT_SHARE => '同步分红',
        self:: SALARY_GENERATE_STEP_SYNC_PERFORMANCE => '同步绩效',
        self:: SALARY_GENERATE_STEP_SYNC_REWORDS_AND_PUNISHMENT => '同步奖惩',
        self:: SALARY_GENERATE_STEP_SYNC_FLOAT_SALARY => '同步浮动薪资',
        self:: SALARY_GENERATE_STEP_SYNC_TAX => '同步个税',
        self:: SALARY_GENERATE_STEP_SYNC_SOCIAL_SECURITY => '同步社保',
    ];

    /**
     * 可以被批量赋值的属性.
     *
     * @var array
     */
    protected $fillable = [
        'year',
        'month',
        'type',
        'count',
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
