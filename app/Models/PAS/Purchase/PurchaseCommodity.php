<?php

namespace App\Models\PAS\Purchase;
   
use Illuminate\Database\Eloquent\Model;

class PurchaseCommodity extends Model {

    protected $table = 'pas_purchase_commodity';


    protected $fillable = ['id',"p_code","p_id","c_url","c_name","c_id","number",'money','status'];
    //商品信息
    public function skuList()
    {
        return $this->hasMany('App\Models\PAS\Purchase\PurchaseCommodityContent', 'goods_id', 'c_id')
            ->where('p_id',p_id)
            ->select(['goods_id','sku','number','price','money','status','id']);
    }
}