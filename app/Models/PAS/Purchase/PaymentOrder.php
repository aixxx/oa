<?php

namespace App\Models\PAS\Purchase;
   
use Illuminate\Database\Eloquent\Model;

class PaymentOrder extends Model {

    const STATUS_DRAFT =0;	//0草稿
    const STATUS_REVIEW =1;// 1待审核
    const STATUS_WITHDRAWAL =2;    // 2已撤回
    const STATUS_RETURNED =3;    // 3已退回
    const STATUS_SUCCESS =4;    // 4已完成
    protected $table = 'pas_payment_order';


    protected $fillable = ['id',"user_id","code","business_date","supplier_id","payable_money","apply_name",'apply_id','money','status'];

}