<?php

namespace App\Models\PAS;

use App\Models\PAS\Warehouse\Warehouse;
use Illuminate\Database\Eloquent\Model;

class SaleReturnInWarehouseGoods extends Model
{
    protected $table = 'pas_sale_return_in_warehouse_goods';
    protected $guarded = [];

    public function warehouse(){
        return $this->hasOne(Warehouse::class, 'id', 'warehouse_id');
    }

    /*
     * 关联商品
     * */
    public function goods(){
        return $this->hasOne(Goods::class, 'goods_id', 'goods_id');
    }
}
