<?php

namespace App\Models\PAS\Purchase;
   
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model {
    const API_STATUS_DRAFT       = 0;//草稿
    const API_STATUS_REVIEW      = 1;//审核中
    const API_STATUS_WITHDRAW    = 2;//撤回
    const API_STATUS_FAIL        = 3;//审核不通过
    const API_STATUS_SUCCESS     = 5;//完成
    protected $table = 'pas_purchase';


    protected $fillable = ['id',"user_id","code","business_date","supplier_id","payable_money","apply_name",'apply_id','earnest_money','status'];
    //费用信息数据查询
    public function costList()
    {
        return $this->hasMany('App\Models\PAS\Purchase\CostInformation', 'code_id', 'id')
            ->where('status',1)
            ->where('type',1);

    }
    //商品信息
    public function goodsList()
    {
        return $this->hasMany('App\Models\PAS\Purchase\PurchaseCommodity', 'p_id', 'id')
            ->select(['p_id','c_id','c_name as goods_name','c_url as goods_url', 'c_url as goods_url','id']);
    }

    //商品信息
    public function skuList()
    {
        return $this->hasMany('App\Models\PAS\Purchase\PurchaseCommodityContent', 'p_id', 'id')
            ->select(['p_id','goods_name','goods_url', 'goods_id','id','sku','number','price','money','war_number','r_number','rw_number','wa_number','sku_id']);
    }
}