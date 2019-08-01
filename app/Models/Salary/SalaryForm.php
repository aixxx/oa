<?php

namespace App\Models\Salary;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SalaryForm extends Model
{
    use SoftDeletes;

    //是否可以发送
    const STATUS_IS_PASS_NO = 0;
    const STATUS_IS_PASS_YES = 1;
    //是否已经发送
    const STATUS_IS_SEND_NO = 0;
    const STATUS_IS_SEND_YES = 1;
    //是否已经查看
    const STATUS_IS_VIEW_NO = 0;
    const STATUS_IS_VIEW_YES = 1;
    //是否已经撤销(未查看才可以撤销)
    const STATUS_IS_WITHDRAW_NO = 0;
    const STATUS_IS_WITHDRAW_YES = 1;
    //是否已经确认
    const STATUS_IS_CONFIRM_NO = 0;
    const STATUS_IS_CONFIRM_YES = 1;

    protected $table = 'salary_form';

    public $timestamps = true;

    /**
     * 可以被批量赋值的属性.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'employee_num',
        'year',
        'month',
        'base',
        'base_json',
        'subsidy',
        'subsidy_json',
        'bonus',
        'fines',
        'dividend',
        'should_salary',
        'actual_salary',
        'float_salary',
        'remark',
        'is_pass',
        'is_send',
        'is_view',
        'is_confirm',
        'is_withdraw',
        'auditor_note',
        'entry_id',
        'performance',
        'greetings',
    ];
    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [

    ];

    protected $appends = [];

    public function user()
    {
        return $this->hasOne('App\Models\User', 'id', 'user_id');
    }
}
