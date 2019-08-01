<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FinancialLoanBill extends Model
{

	protected $table = 'financial_loan_bill';//借款单

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'financial_id',
        'loan_bill_id'
	];
	/**
     * 不能被批量赋值的属性
     *
     * @var array
     */
    protected $guarded = [
        "id"
    ];
    public function getFinanceInfo(){
        return $this->hasOne(Financial::class,'id','loan_bill_id')->where('expense_amount','>',0)->select('id','flow_id','user_id','expense_amount','title','created_at','budget_id');
    }
}
