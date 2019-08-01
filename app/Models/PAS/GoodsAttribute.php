<?php

namespace App\Models\PAS;

use Illuminate\Database\Eloquent\Model;

class GoodsAttribute extends Model
{
    protected $table = 'pas_goods_attributes';
    protected $primaryKey = 'goods_attribute_id';
    protected $guarded = [];
}
