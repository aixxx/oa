<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserAccountRecord extends Model
{
	protected $fillable = ['sub','title', 'user_id', 'account_type_id', 'balance', 'is_correlation_model', 'model_id', 'model_name','type','account_type'];

    const ACCOUNT_INVESTMENT                = 0;// 投资收益
    const ACCOUNT_INVESTMENT_NAME           = '投资收益';
    const ACCOUNT_WAGE                      = 1;// 工资收益
    const ACCOUNT_WAGE_NAME                 = '工资收益';
    const ACCOUNT_SHARE                     = 2;// 分红收益
    const ACCOUNT_SHARE_NAME                = '分红收益';
    const ACCOUNT_EXPENSE_ACCOUNT           = 3;// 报销
    const ACCOUNT_EXPENSE_ACCOUNT_NAME      = '报销';
    const ACCOUNT_PAY                       = 4;// 支付
    const ACCOUNT_PAY_NAME                  = '支付';
    const ACCOUNT_BORROWING                 = 5;// 借款
    const ACCOUNT_BORROWING_NAME            = '借款';
    const ACCOUNT_REIMBURSEMENT             = 6;// 还款
    const ACCOUNT_REIMBURSEMENT_NAME        = '还款';
    const ACCOUNT_COLLECTION                = 7;// 收款
    const ACCOUNT_COLLECTION_NAME           = '收款';

    //记录类型
    const RECORD_EARNINGS           = 0;// 收益
    const RECORD_SPENDING           = 1;// 支出


	public function getBalanceAttribute($value)
	{
		return sprintf("%.2f",$value/100);
	}
	
	public function getRecordTitleAttribute()
	{
		if ($this->is_correlation_model == 0) {
			return $this->title;
		}

		$project = new $this->model_name; 
		return  $project->where('id', '=', $this->model_id)->first()->title;
	}
}
