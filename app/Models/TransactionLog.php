<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransactionLog extends Model
{
    public $fillable = ['user_id', 'department_id', 'source', 'outer_id', 'in_out', 'title', 'status', 'model_name', 'amount', 'category', 'type', 'status_end_time', 'is_bill', 'is_jysr', 'is_more_department','status_start_time','company_id','qishu'];

    const FINANCIALS_DEPARTMENT_DNJYS_TYPE = 1;// 对内交易（收)
    const FINANCIALS_DEPARTMENT_DNJYZ_TYPE = 2;// 对内交易（支)
    const FINANCIALS_DEPARTMENT_DWJYS_TYPE = 3;// 对外交易（收)
    const FINANCIALS_DEPARTMENT_DWJYZ_TYPE = 4;// 对外交易（支）
    const FINANCIALS_DEPARTMENT_FHZC_TYPE = 5;// 分红支出
    const FINANCIALS_DEPARTMENT_ZCJZ_TYPE = 6;// 资产价值
    const FINANCIALS_DEPARTMENT_DJZT_TYPE = 7;// 单据状态
    const FINANCIALS_DEPARTMENT_FEE_BOOTH_TYPE = 8;// 平摊


    const FINANCIALS_DEPARTMENT_INOUTS = 1;// 应收
    const FINANCIALS_DEPARTMENT_INOUTF = 2;// 应付

    const FINANCIALS_DEPARTMENT_CATEGORY_BX = 1;// 报销
    const FINANCIALS_DEPARTMENT_CATEGORY_JK = 2;// 借款
    const FINANCIALS_DEPARTMENT_CATEGORY_HK = 3;// 还款
    const FINANCIALS_DEPARTMENT_CATEGORY_SK = 4;// 收款
    const FINANCIALS_DEPARTMENT_CATEGORY_ZF = 5;// 支付
}
