<?php

namespace App\Models\PAS\Purchase;
   
use Illuminate\Database\Eloquent\Model;

class PaymentOrderContent extends Model {
    const TYPE_PURCHASE=1;//采购单
    const TYPE_RETURN_ORDER=2;//采购单
    protected $table = 'pas_payment_order_content';


    protected $fillable = ['id',"p_id","po_id"];

    //费用信息数据查询
    public function purchaseList()
    {
        return $this->hasMany('App\Models\PAS\Purchase\Purchase', 'id', 'p_id')
            ->where('status',5)
            ->select(['id','code','business_date','earnest_money','turnover_amount']);

    }
    //费用信息数据查询
    public function returnorderList()
    {
        return $this->hasMany('App\Models\PAS\Purchase\ReturnOrder', 'id', 'p_id')
            ->where('status',4)
            ->select(['id','code','business_date','money']);

    }

}