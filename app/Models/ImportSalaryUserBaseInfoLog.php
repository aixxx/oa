<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class importSalaryUserBaseInfoLog extends Model
{
    use SoftDeletes;
    protected $table    = "import_salary_user_base_info_log";
    public    $fillable =
        [
            'salary_user_base_info_id',
            'title',
            'month',
            'employee_number',
            'chinese_name',
            'contract_department',
            'belong_to_business',
            'contract_company',
            'new_business_wechat_first_level',
            'new_business_wechat_second_level',
            'contract_nature',
            'position',
            'employee_type',
            'join_at',
            'leave_at',
            'id_number',
            'salary_card_number',
            'mobile',
            'work_address',
            'specific_support_fee_contract_company',
            'specific_support_fee_wechat_department_first_level',
            'specific_support_fee_wechat_department_second_level',
            'specific_support_fee_transaction_account',
            'specific_support_fee_transaction_id_number',
            'specific_support_fee_card_number',
            'specific_support_fee_salary_note',
            'specific_support_fee_taxation_note',
        ];
}
