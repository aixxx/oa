<?php

namespace App\Models\PAS;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\PAS\Goods
 *
 * @property int $goods_id 商品id
 * @property int $category_id 分类id
 * @property string|null $category_parent_id 商品类目组
 * @property string|null $goods_sn 商品编号
 * @property string $goods_name 商品名称
 * @property int|null $suppliers_id 供应商ID
 * @property int $store_count 库存数量
 * @property float $wholesale_price 批发价
 * @property float $price 售价
 * @property float|null $cost_price 商品成本价
 * @property string|null $description 商品描述
 * @property string|null $thumb_img 商品缩略图
 * @property string|null $img 商品图片
 * @property int|null $goods_type 商品类型， 1商品 2服务
 * @property int|null $goods_from 商品来源 1内部商品 2外部商品
 * @property int|null $brand_id 品牌id
 * @property string|null $mnemonic 助记符
 * @property int|null $status 商品状态 0草稿 1上架 2下架
 * @property string $on_time 商品上架时间
 * @property int $sort 商品排序
 * @property int|null $sales_num 商品销量
 * @property string|null $barcode_scheme 条码方案
 * @property string|null $remark 备注
 * @property string|null $department 归属部门
 * @property string|null $organization 归属组织
 * @property int|null $relate_work 关联工作 1关联客户 2关联项目 3关联生产
 * @property int|null $from_system 添加数据的系统 1erp 2客户
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\PAS\GoodsAttribute[] $attribute
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\PAS\GoodsSpecificPrice[] $specific_price
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Goods newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Goods newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Goods query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Goods whereBarcodeScheme($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Goods whereBrandId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Goods whereCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Goods whereCategoryParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Goods whereCostPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Goods whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Goods whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Goods whereDepartment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Goods whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Goods whereFromSystem($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Goods whereGoodsFrom($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Goods whereGoodsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Goods whereGoodsName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Goods whereGoodsSn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Goods whereGoodsType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Goods whereImg($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Goods whereMnemonic($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Goods whereOnTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Goods whereOrganization($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Goods wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Goods whereRelateWork($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Goods whereRemark($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Goods whereSalesNum($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Goods whereSort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Goods whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Goods whereStoreCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Goods whereSuppliersId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Goods whereThumbImg($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Goods whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Goods whereWholesalePrice($value)
 * @mixin \Eloquent
 */
class Goods extends Model
{
    protected $table = 'pas_goods';
    protected $primaryKey = 'goods_id';
    protected $guarded = [];


    /*
     * 商品规格
     * */
    public function specific(){
        return $this->hasMany('App\Models\PAS\GoodsSpecific', 'goods_id', 'goods_id')->where(function($query){
            $query->where('deleted_at', 0)->orWhereNull('deleted_at');
        })->select('goods_specific_id', 'goods_id', 'spec_id');
    }


    /*
     * 商品属性
     * */
    public function attribute(){
        return $this->hasMany('App\Models\PAS\GoodsAttribute', 'goods_id', 'goods_id')->whereNull('deleted_at')->select('goods_attribute_id', 'goods_id', 'attr_id');
    }


    /*
     * 规格组合价格
     * */
    public function specific_price(){
        return $this->hasMany('App\Models\PAS\GoodsSpecificPrice', 'goods_id', 'goods_id')->whereNull('deleted_at')->orWhere('deleted_at', 0);
    }


    /*
     * 获取商品信息
     * */
    public function getGoodsInfo($id, $select=[]){
        //'goods_id','goods_name','goods_sn','price','img','store_count','wholesale_price','cost_price','description','goods_type','goods_from'
        $arr=['goods_id','goods_name','goods_sn','price','img','store_count','wholesale_price','cost_price','description','goods_type','goods_from'];
        $arr = array_merge($select, $arr);

        return Goods::where('status', 1)->find($id)->select($arr);
    }


    /*
     * 根据商品id获取商品及规格详细信息
     * */
    public function getGoodsSpecificDetails($id, $select=[]){
        //$data = Goods::where('status', 1)->find($id)->with('specific')->with('specific_price')->select('goods_id','goods_name','goods_sn','price','img','store_count','wholesale_price','cost_price','description','goods_type','goods_from')->first()->toArray();
        $data = $this->getGoodsInfo($id, $select)->with('specific')->with('specific_price')->first()->toArray();

        if(!empty($data) && !empty($data['specific'])){
            $spec_ids = array_unique(array_column($data['specific'], 'spec_id'));
            $specific = Specific::whereIn('id', $spec_ids)->with(['children'=>function($query){
                $query->select('id', 'spec_id', 'name');
            }])->orderBy('id', 'ASC')->select('id', 'name')->get()->toArray();

            $data['specific'] = $specific;
        }

        return $data;
    }


    /*
     * 根据商品id获取商品及属性详细信息
     * */
    public function getGoodsAttributeDetails($id, $select=[]){
        $data = $this->getGoodsInfo($id, $select)->with('attribute')->first()->toArray();

        if(!empty($data) && !empty($data['attribute'])){
            $attr_ids = array_unique(array_column($data['attribute'], 'attr_id'));
            $attribute = Attribute::whereIn('id', $attr_ids)->orderBy('id', 'ASC')->select('id', 'name', 'values')->get()->toArray();
            $data['attribute'] = $attribute;
        }

        return $data;
    }
}
