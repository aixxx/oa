<?php

namespace App\Models\PAS\Purchase;
   
use App\Models\PAS\GoodsSpecificPrice;
use Illuminate\Database\Eloquent\Model;

class PurchaseCommodityContent extends Model {

    protected $table = 'pas_purchase_commodity_content';


    protected $fillable = ['id',"p_code","p_id","sku","number","price","money",'r_number','status', 'sku_id', 'goods_id'];

    public function skuInfo(){
        return $this->hasOne(GoodsSpecificPrice::class, 'id', 'sku_id');
    }
}