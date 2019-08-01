<?php

namespace App\Models\PAS;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    //
    protected $table = 'pas_categorys';
    protected $primaryKey = 'auto_id';
    public $fillable = [
        'id',
        'name',
        'parent_id',
        'sort',
        'deepth'
    ];

    use SoftDeletes;

    /**
     * 生成categoryId
     * 生成规则：查询所有记录最大的id(categoryId,包含deleted_at的记录)加1，为新建类目的Id
     */
    public static function generateCategoryId()
    {
        $res = Category::withTrashed()->orderBy('id', 'desc')->value('id');

        if(!empty($res)){
            $res++;
        }else{
            $res = 1;
        }

        return $res;
    }

    /*
     * 下级类目
     * */
    public function children()
    {
        return $this->hasMany('App\Models\PAS\Category', 'parent_id', 'id');
    }

    /*
     * 所有子集类目id
     * */
    public static function getChildCategoryID($tree, $id)
    {
        $category = Category::where('parent_id', '=', $id)->get();

        collect($category)->map(function ($entry) use (&$tree, &$node) {
            $tree[] = $entry->id;

            $tree = self::getChildCategoryID($tree, $entry->id);
        });

        return $tree;
    }


    /*
     * 所有类目数据
     * */
    public function getChildCategory($tree, $pid=0)
    {
        Category::whereNull('deleted_at')->orWhere('deleted_at', 0)->select('id', 'name', 'parent_id', 'deepth')->chunk(100, function($res) use(&$tree) {
            foreach($res as $v){
                $tree[] = $v;
                /*$tree[] = [
                    'id' => $v['id'],
                    'name' => $v['name'],
                    'parent_id' => $v['parent_id'],
                    'deepth' => $v['deepth'],
                ];*/
            }
        });

        $data = $this->getTree($tree, $pid);
        return $data;
    }

    public function getTree($menus,$pid=0)
    {
        if (empty($menus)) {
            return '';
        }
        $arr = [];
        foreach ($menus as $key => $value) {
            if ($value['parent_id'] == $pid) {
                //$arr[$value['id']] = $value;
                //$arr[$value['id']]['child'] = self::getTree($menus,$value['id']);
                $value['child'] = self::getTree($menus,$value['id']);
                $arr[] = $value;
            }
        }
        return $arr;
    }


    /*
     * 指定类目下的商品
     * */
    public static function getCategoryGoods($category_id, $type = 0){
        $ids = $category = [];
        if($type == 1){
            //包含子集的商品
            $ids = self::getChildCategory($category, $category_id);
        }else{
            $ids[] = $category_id;
        }

        return Goods::where([['on_sale', 1], ['deleted_at', 0]])->whereIn('category_id', $ids)->select('goods_id', 'goods_name', 'goods_sn', 'thumb_img', 'price')->get();
    }

}
