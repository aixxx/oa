<?php

namespace App\Models\PAS\Purchase;
   
use Illuminate\Database\Eloquent\Model;

class WarehousingApplyContent extends Model {
    const TYPE_WAREHOUSING = 1;//入库单申请（采购执行单）
    const TYPE_RETURN_ORDER = 2;//采购退货单
    protected $table = 'pas_warehousing_apply_content';

    protected $fillable = ['id',"code"];

}