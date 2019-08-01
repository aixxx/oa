<?php

namespace App\Http\Controllers\Api\V1\PAS;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\PAS\GoodsRepository;
use App\Constant\ConstFile;
use Auth;
use App\Models\PAS\Purchase\Supplier;
use App\Models\PAS\Brand;


class GoodsController extends Controller
{
    public $goods;
    public function __construct(GoodsRepository $goods)
    {
        //$user = Auth::user();
        $this->goods = $goods;
    }

    /*
     * 添加修改类目
     * */
    public function editCategory(Request $request){
        $user = Auth::user();
        return $this->goods->editCategory($request->all(), $user);
    }

    /*
     * 下级类目列表
     * */
    public function categoryList(Request $request){
        return $this->goods->categoryList($request->all());
    }

    /*
     * 多级类目展示
     * */
    public function getCategoryList(Request $request){
        return $this->goods->getCategoryList($request->all());
    }


    /*
     * 添加规格及选项
     * */
    public function createSpecific(Request $request){
        return $this->goods->createSpecific($request->all());//添加规格
    }

    /*
     * 修改规格及选项
     * */
    public function editSpecific(Request $request){
        return $this->goods->editSpecific($request->all());
    }

    /*
     * 删除规格选项
     * */
    public function delSpecificItem(Request $request){
        return $this->goods->delSpecificItem($request->all());
    }

    /*
     * 获取规格及其选项
     * */
    public function specificChildren(Request $request){
        return $this->goods->specificChildren($request->all());
    }

    /*
     * 删除规格
     * */
    public function delSpecific(Request $request){
        return $this->goods->delSpecific($request->all());
    }

    /*
     * 添加修改属性
     * */
    public function editAttribute(Request $request){
        return $this->goods->editAttribute($request->all());
    }

    /*
     * 删除属性
     * */
    public function delAttribute(Request $request){
        return $this->goods->delAttribute($request->all());
    }

    /*
     * 添加sku定价
     * */
    public function addSkuPrice(Request $request){
        return $this->goods->addSkuPrice($request->all());
    }

    /*
     * 删除规格定价
     * */
    public function delSkuPrice(Request $request){
        return $this->goods->delSkuPrice($request->all());
    }

    /*
     * 添加规格库存预警
     * */
    public function addSkuStoreEarlyWarning(Request $request){
        return $this->goods->addSkuStoreEarlyWarning($request->all());
    }

    /*
     * 删除规格库存预警
     * */
    public function delSkuStoreEarlyWarning(Request $request){
        return $this->goods->delSkuStoreEarlyWarning($request->all());
    }

    /*
     * 添加修改品牌
     * */
    public function editBrand(Request $request){
        return $this->goods->editBrand($request->all());
    }

    /*
     * 删除品牌
     * */
    public function delBrand(Request $request){
        return $this->goods->delBrand($request->all());
    }

    /*
     * 品牌列表
     * */
    public function brandList(Request $request){
        return $this->goods->brandList($request->all());
    }

    /*
     * 添加修改商品
     * */
    public function editGoods(Request $request){
        return $this->goods->editGoods($request->all());
    }

    /*
     * 修改商品详情
     * */
    public function editGoodsInfo(Request $request){
        $param = $request->all();

        $info = [];
        if($param['id']){
            $info = $this->goods->editGoodsInfo($param['id']);
        }

        $supplier = Supplier::where('status', 1)->select('id', 'title')->limit(300)->get()->toArray();
        $brand = Brand::where('status', 0)->where(function($query){
            $query->whereNull('deleted_at')->orWhere('deleted_at', 0);
        })->select('id', 'name')->limit(300)->get()->toArray();

        $data = [
            'goods_type' => ConstFile::$goods_type,
            'goods_from' => ConstFile::$goods_from,
            'goods_status' => ConstFile::$goods_status,
            'related_work_type' => ConstFile::$related_work_type,
            'info' => $info,
            'supplier' => $supplier,
            'brand' => $brand
        ];
        return returnJson(ConstFile::API_RESPONSE_SUCCESS_MESSAGE,ConstFile::API_RESPONSE_SUCCESS, $data);
    }

    /*
     * 商品列表
     * */
    public function goodsList(Request $request){
        return $this->goods->goodsList($request->all());
    }

    /*
     * 商品购买详情
     * */
    public function goodsBuyDetail(Request $request){
        return $this->goods->goodsBuyDetail($request->all());
    }

    /*
     * 规格切换获取规格组合信息
     * */
    public function changeSpecificItem(Request $request){
        return $this->goods->changeSpecificItem($request->all());
    }

    /*
     * 指定用户购买的商品列表
     * */
    public function userBuyGoodsList(Request $request){
        return $this->goods->userBuyGoodsList($request->all());
    }

    /*
     * 获取关联规格
     * */
    public function selectRelateSpecific(Request $request){
        return $this->goods->selectRelateSpecific($request->all());
    }

    /*
     * 商品统计
     * */
    public function goodsStatistics(){
        return $this->goods->goodsStatistics();
    }

}
