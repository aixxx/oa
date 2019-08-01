<?php

namespace App\Models\PAS;

use Illuminate\Database\Eloquent\Model;

class Specific extends Model
{
    protected $table = 'pas_specifics';
    public $fillable = [
        'id',
        'name',
        'sort'
    ];
    //protected $guarded = [];

    /*
     * 规格选项
     * */
    public function children()
    {
        return $this->hasMany('App\Models\PAS\SpecificItem', 'spec_id', 'id')->whereNull('deleted_at');
    }

    /*
     * 商品属性关联模型
     * */
    public function belongToGoods(){
        return $this->belongsToMany('App\Models\PAS\Goods', 'pas_goods_specifics', 'spec_id', 'goods_id');
    }
}
