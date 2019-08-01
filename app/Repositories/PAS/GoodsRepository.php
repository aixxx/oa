<?php

namespace App\Repositories\PAS;

use App\Models\PAS\Brand;
use App\Models\PAS\Goods;
use App\Models\PAS\GoodsAttribute;
use App\Models\PAS\GoodsSpecific;
use App\Models\PAS\GoodsSpecificPrice;
use App\Models\PAS\SaleOrderGoods;
use App\Models\PAS\Warehouse\GoodsAllocationGoods;
use App\Repositories\ParentRepository;
use App\Constant\ConstFile;
use App\Models\PAS\Category;
use App\Models\PAS\Specific;
use App\Models\PAS\SpecificItem;
use App\Models\PAS\Attribute;
use DB;
use Exception;

class GoodsRepository extends ParentRepository
{
    private $field = [
        'goods_sn' => '请填写商品编号',
        'goods_name' => '请填写商品名称',
        'category_id' => '请选择商品类目',
        'category_parent_id' => '请选择商品类目组',
        'goods_type' => '请选择商品类型',
        'goods_from' => '请选择商品来源',
        'img' => '请上传商品图片',
        'mnemonic' => '助记符',
        'on_time' => '请填写上市时间',
        'department' => '请选择归属部门',
        'organization' => '请选择归属组织'
    ];


    public function __construct(){

    }

    /*
     * 添加修改类目
     * */
    public function editCategory($param, $user){
        //权限判断
        try{
            $error = $this->checkCategoryData($param);//验证数据
            if(!empty($error)){
                return returnJson('参数错误：'.$error, ConstFile::API_RESPONSE_FAIL);
            }

            $data = [
                'name' => $param['name'],
                'deepth' => $param['deepth'],
                'parent_id' => $param['deepth'] > 1 ? $param['pid'] : 0
            ];

            $id = 0;
            DB::transaction(function () use ($param, $data, &$id) {
                $time = date('Y-m-d H:i:s', time());
                if(!empty($param['id'])){
                    //修改
                    $data['updated_at'] = $time;
                    $res = Category::where('id', $param['id'])->update($data);
                    if($res){
                        $id = $param['id'];
                    }
                }else{
                    //添加
                    $data['id'] = Category::generateCategoryId();
                    $data['created_at'] = $time;
                    $data['updated_at'] = $time;
                    $res = Category::create($data);
                    $id = $res->id;
                }
            });

            if($id){
                return returnJson(ConstFile::API_RESPONSE_SUCCESS_MESSAGE,ConstFile::API_RESPONSE_SUCCESS,['id'=>$id]);
            }else{
                return returnJson(ConstFile::API_RESPONSE_FAIL_MESSAGE, ConstFile::API_RESPONSE_FAIL);
            }
        } catch (Exception $e){
            return returnJson($e->getMessage(), $e->getCode());
        }
    }


    /*
     * 下级类目列表
     * */
    public function categoryList($param){
        try{
            if(!isset($param['pid'])){
                return returnJson('参数错误：请填写父级类目', ConstFile::API_RESPONSE_FAIL);
            }

            $data = Category::where('parent_id', $param['pid'])->whereNull('deleted_at')->select('id', 'name', 'parent_id', 'deepth')->get()->toArray();

            return returnJson(ConstFile::API_RESPONSE_SUCCESS_MESSAGE,ConstFile::API_RESPONSE_SUCCESS, $data);
        }catch (Exception $e){
            return returnJson($e->getMessage(), $e->getCode());
        }
    }


    /*
     * 商品多级类目展示
     * */
    public function getCategoryList($param){
        try{
            $pid = empty($param['pid']) ? 0 : $param['pid'];
            $category = new Category();
            $data = [];
            $data = $category->getChildCategory($data, $pid);

            return returnJson(ConstFile::API_RESPONSE_SUCCESS_MESSAGE,ConstFile::API_RESPONSE_SUCCESS, $data);
        }catch (Exception $e){
            return returnJson($e->getMessage(), $e->getCode());
        }
    }


    /*
     * 添加规格及选项
     * */
    public function createSpecific($param){
        //权限判断
        try{
            $error = $this->checkSpecificData($param, 1);
            if(!empty($error)){
                return returnJson('参数错误：'.$error, ConstFile::API_RESPONSE_FAIL);
            }
            $data = [
                'name' => $param['name']
            ];

            $id = 0;
            DB::transaction(function () use ($param, $data, &$id) {
                //1.规格数据
                $res = 1;
                $add = Specific::create($data);

                //2.规格选项值
                $item = explode(',', $param['item']);
                $time = date('Y-m-d H:i:s', time());
                $da = [];
                foreach ($item as $v){
                    $da[] = [
                        'spec_id' => $add->id,
                        'name' => $v,
                        'created_at' => $time,
                        'updated_at' => $time
                    ];
                }
                $res = SpecificItem::insert($da);
                if(!empty($param['spec_price_ids'])){
                    $res = GoodsSpecificPrice::whereIn('id', $param['spec_price_ids'])->update(['deleted_at'=>$time]);
                }
                if($res){
                    $id = $add->id;
                }
            });

            if($id){
                $child = SpecificItem::where('spec_id', $id)->get(['id', 'name'])->toArray();
                $result = [
                    'id' => $id,
                    'name' => $param['name'],
                    'child' => $child
                ];
                return returnJson(ConstFile::API_RESPONSE_SUCCESS_MESSAGE,ConstFile::API_RESPONSE_SUCCESS, $result);
            }else{
                return returnJson(ConstFile::API_RESPONSE_FAIL_MESSAGE, ConstFile::API_RESPONSE_FAIL);
            }
        } catch (Exception $e){
            return returnJson($e->getMessage(), $e->getCode());
        }
    }


    /*
     * 修改规格及选项
     * */
    public function editSpecific($param){
        try{
            $error = $this->checkSpecificData($param, 2);
            if(!empty($error)){
                return returnJson('参数错误：'.$error, ConstFile::API_RESPONSE_FAIL);
            }

            $res = 0;
            DB::transaction(function () use ($param, &$res) {
                //1.规格数据更新
                $res = Specific::where('id', $param['id'])->update(['name'=>$param['name']]);

                //2.规格选项值数据更新
                $item = json_decode(htmlspecialchars_decode($param['item']), true);
                $time = date('Y-m-d H:i:s', time());
                $del_ids = $param['del_ids'];

                $add_data = $up_data = [];
                foreach($item as $v){
                    if(!empty($v['id'])){
                        $up_data[] = ['id'=>$v['id'], 'name'=>$v['name'], 'updated_at'=>$time];
                    }else{
                        $add_data[] = ['name'=>$v['name'], 'spec_id'=>$param['id'], 'created_at'=>$time, 'updated_at'=>$time];
                    }
                }
                if(!empty($add_data)){
                    $res = SpecificItem::insert($add_data);
                }
                if(!empty($up_data)){
                    $spec_item = new SpecificItem();
                    $res = $this->updateBatch($spec_item->table, $up_data);
                }
                if(!empty($del_ids)){
                    $res = SpecificItem::where('spec_id', $param['id'])->whereIn('id', $del_ids)->update(['deleted_at'=> $time]);

                    if(count($del_ids) > 1){
                        $exp_str = "[".implode('|', $del_ids)."]";
                    }else{
                        $exp_str = $del_ids[0];
                    }
                    $sql = 'update pas_goods_specific_prices set deleted_at = "'.$time.'" where id in ('.implode(',', $param['spec_price_ids']).') and sku regexp "^'.$exp_str.'$|^'.$exp_str.'_|_'.$exp_str.'$|_'.$exp_str.'_"';

                    $res = DB::update(DB::raw($sql));
                }
            });

            $child = SpecificItem::where('spec_id', $param['id'])->whereNull('deleted_at')->get(['id', 'name'])->toArray();
            $spec_price = [];
            if(!empty($param['spec_price_ids'])){
                $spec_price = GoodsSpecificPrice::whereIn('id', $param['spec_price_ids'])->whereNull('deleted_at')->get()->toArray();
            }
            $result = [
                'id' => $param['id'],
                'name' => $param['name'],
                'child' => $child,
                'spec_price' => $spec_price
            ];

            return returnJson(ConstFile::API_RESPONSE_SUCCESS_MESSAGE,ConstFile::API_RESPONSE_SUCCESS, $result);
        } catch (Exception $e){
            return returnJson($e->getMessage(), $e->getCode());
        }
    }


    /*
     * 删除规格选项
     * */
    public function delSpecificItem($param){
        try{
            if(empty($param['id'])){
                return returnJson('参数错误：请选择规格', ConstFile::API_RESPONSE_FAIL);
            }
            if(empty($param['del_ids'])){
                return returnJson('参数错误：请选择删除的规格选项', ConstFile::API_RESPONSE_FAIL);
            }
            $del = explode(',', $param['del_ids']);
            $time = date('Y-m-d H:i:s', time());
            $res = SpecificItem::where('spec_id', $param['id'])->whereIn('id', $del)->update(['deleted_at'=> $time]);
            if($res){
                return returnJson(ConstFile::API_RESPONSE_SUCCESS_MESSAGE);
            }else{
                return returnJson(ConstFile::API_RESPONSE_FAIL_MESSAGE, ConstFile::API_RESPONSE_FAIL);
            }
        }catch (Exception $e){
            return returnJson($e->getMessage(), $e->getCode());
        }
    }


    /*
     * 获取规格及选项
     * */
    public function specificChildren($param){
        try{
            if(empty($param['id'])){
                return returnJson('参数错误：请选择规格', ConstFile::API_RESPONSE_FAIL);
            }

            $data = Specific::where('id', $param['id'])->with(['children'=>function($query){
                $query->select('id', 'spec_id', 'name');
            }])->get(['id', 'name'])->toArray();

            return returnJson(ConstFile::API_RESPONSE_SUCCESS_MESSAGE,ConstFile::API_RESPONSE_SUCCESS, $data);
        }catch (Exception $e){
            return returnJson($e->getMessage(), $e->getCode());
        }
    }


    /*
     * 删除规格
     * */
    public function delSpecific($param){
        try{
            throw_if(empty($param['id']), new Exception('参数错误：请选择规格'));
            //throw_if(empty($param['price_ids']), new Exception('参数错误：请选择定价'));

            $res = 0;
            DB::transaction(function () use ($param, &$res) {
                $time = date('Y-m-d H:i:s', time());
                $res = Specific::where('id', $param['id'])->update(['deleted_at'=>$time]);
                if($res){
                    $res = SpecificItem::where('spec_id', $param['id'])->update(['deleted_at'=>$time]);
                }
                if(!empty($param['price_ids'])){
                    $s = GoodsSpecificPrice::whereIn('id', $param['price_ids']);

                    if(!empty($param['goods_id'])){
                        $s->where('goods_id', $param['goods_id']);
                    }else{
                        $s->where('goods_id', 0);
                    }
                    $res = $s->update(['deleted_at'=> $time]);
                }
            });

            if($res){
                return returnJson(ConstFile::API_RESPONSE_SUCCESS_MESSAGE);
            }else{
                return returnJson(ConstFile::API_RESPONSE_FAIL_MESSAGE, ConstFile::API_RESPONSE_FAIL);
            }
        }catch (Exception $e){
            return returnJson($e->getMessage(), $e->getCode());
        }
    }


    /*
     * 添加修改属性及值
     * */
    public function editAttribute($param){
        try{
            $error = $this->checkAttributeData($param);
            if(!empty($error)){
                return returnJson('参数错误：'.$error, ConstFile::API_RESPONSE_FAIL);
            }

            $data = [
                'name' => $param['name'],
                'values' => $param['item']
            ];
            //$time = date('Y-m-d H:i:s', time());
            $id = 0;
            if(!empty($param['id'])){
                //修改
                //$data['updated_at'] = $time;
                $res = Attribute::where('id', $param['id'])->update($data);
                $id = $param['id'];
            }else{
                //添加
                $res = Attribute::create($data);
                $id = $res->id;
            }
            if(empty($res)){
                return returnJson(ConstFile::API_RESPONSE_FAIL_MESSAGE, ConstFile::API_RESPONSE_FAIL);
            }else{
                return returnJson(ConstFile::API_RESPONSE_SUCCESS_MESSAGE, ConstFile::API_RESPONSE_SUCCESS, ['id'=>$id]);
            }
        } catch (Exception $e){
            return returnJson($e->getMessage(), $e->getCode());
        }
    }


    /*
     * 删除属性
     * */
    public function delAttribute($param){
        try{
            if(empty($param['id'])){
                return returnJson('参数错误：请选择删除的属性', ConstFile::API_RESPONSE_FAIL);
            }

            $time = date('Y-m-d H:i:s', time());
            $res = Attribute::where('id', $param['id'])->update(['deleted_at'=> $time]);
            if($res){
                return returnJson(ConstFile::API_RESPONSE_SUCCESS_MESSAGE);
            }else{
                return returnJson(ConstFile::API_RESPONSE_FAIL_MESSAGE, ConstFile::API_RESPONSE_FAIL);
            }
        }catch (Exception $e){
            return returnJson($e->getMessage(), $e->getCode());
        }
    }


    /*
     * 添加修改品牌
     * */
    public function editBrand($param){
        try{
            if(empty($param['name'])){
                return returnJson('参数错误：请填写品牌名称', ConstFile::API_RESPONSE_FAIL);
            }
            $data = [
                'name' => $param['name'],
                'first' => getFirstCharter($param['name'])
            ];
            if(!empty($param['id'])){
                //修改
                $res = Brand::where('id', $param['id'])->update($data);
                $id = $param['id'];
            }else{
                //添加
                $res = Brand::create($data);
                $id = $res->id;
            }
            if(empty($res)){
                return returnJson(ConstFile::API_RESPONSE_FAIL_MESSAGE, ConstFile::API_RESPONSE_FAIL);
            }else{
                return returnJson(ConstFile::API_RESPONSE_SUCCESS_MESSAGE, ConstFile::API_RESPONSE_SUCCESS, ['id'=>$id]);
            }
        } catch (Exception $e){
            return returnJson($e->getMessage(), $e->getCode());
        }
    }


    /*
     * 删除品牌
     * */
    public function delBrand($param){
        try{
            if(empty($param['id'])){
                return returnJson('参数错误：请选择删除的品牌', ConstFile::API_RESPONSE_FAIL);
            }

            $time = date('Y-m-d H:i:s', time());
            $res = Brand::where('id', $param['id'])->update(['deleted_at'=> $time]);
            if($res){
                return returnJson(ConstFile::API_RESPONSE_SUCCESS_MESSAGE);
            }else{
                return returnJson(ConstFile::API_RESPONSE_FAIL_MESSAGE, ConstFile::API_RESPONSE_FAIL);
            }
        }catch (Exception $e){
            return returnJson($e->getMessage(), $e->getCode());
        }
    }


    /*
     * 品牌列表
     * */
    public function brandList($param){
        try{
            $data = Brand::whereNull('deleted_at')->select('id', 'name', 'first')->orderBy('first', 'ASC')->orderBy('id', 'DESC')->paginate($param['limit'])->toArray();;

            $result['total'] = $data['total'];
            $result['total_page'] = $data['last_page'];
            $result['data'] = $data['data'];

            return returnJson(ConstFile::API_RESPONSE_SUCCESS_MESSAGE_DATA,ConstFile::API_RESPONSE_SUCCESS,$result);
        }catch (Exception $e){
            return returnJson($e->getMessage(), $e->getCode());
        }
    }


    /*
     * 添加sku定价
     * */
    public function addSkuPrice($param){
        try{
            $res = $this->checkSkuData($param);
            if($res['status'] == 0){
                return returnJson('参数错误：'.$res['msg'], ConstFile::API_RESPONSE_FAIL);
            }

            $result = GoodsSpecificPrice::create($res['data']);
            if($result){
                return returnJson(ConstFile::API_RESPONSE_SUCCESS_MESSAGE,ConstFile::API_RESPONSE_SUCCESS, $result);
            }else{
                return returnJson(ConstFile::API_RESPONSE_FAIL_MESSAGE, ConstFile::API_RESPONSE_FAIL);
            }
        }catch (Exception $e){
            return returnJson($e->getMessage(), $e->getCode());
        }
    }


    /*
     * 删除sku定价
     * */
    public function delSkuPrice($param){
        try{
            if(empty($param['id'])){
                return returnJson('参数错误：请选择删除的定价', ConstFile::API_RESPONSE_FAIL);
            }

            $time = date('Y-m-d H:i:s', time());
            $res = GoodsSpecificPrice::where('id', $param['id'])->update(['deleted_at'=> $time]);

            if($res){
                return returnJson(ConstFile::API_RESPONSE_SUCCESS_MESSAGE);
            }else{
                return returnJson(ConstFile::API_RESPONSE_FAIL_MESSAGE, ConstFile::API_RESPONSE_FAIL);
            }
        }catch (Exception $e){
            return returnJson($e->getMessage(), $e->getCode());
        }
    }


    /*
     * 添加规格库存预警
     * */
    public function addSkuStoreEarlyWarning($param){
        try{
            if(empty($param['spec_id'])){
                return returnJson('参数错误：请选择预警选项', ConstFile::API_RESPONSE_FAIL);
            }
            if(empty($param['store_upper_limit'])){
                return returnJson('参数错误：请填写库存上限', ConstFile::API_RESPONSE_FAIL);
            }
            if(empty($param['store_lower_limit'])){
                return returnJson('参数错误：请填写库存下限', ConstFile::API_RESPONSE_FAIL);
            }

            $data = [
                'store_upper_limit' => $param['store_upper_limit'],
                'store_lower_limit' => $param['store_lower_limit'],
            ];
            $result = GoodsSpecificPrice::where('id', $param['spec_id'])->update($data);

            if($result){
                return returnJson(ConstFile::API_RESPONSE_SUCCESS_MESSAGE,ConstFile::API_RESPONSE_SUCCESS);
            }else{
                return returnJson(ConstFile::API_RESPONSE_FAIL_MESSAGE, ConstFile::API_RESPONSE_FAIL);
            }
        }catch (Exception $e){
            return returnJson($e->getMessage(), $e->getCode());
        }
    }


    /*
     * 删除规格库存预警
     * */
    public function delSkuStoreEarlyWarning($param){
        try{
            if(empty($param['id'])){
                return returnJson('参数错误：请选择删除的规格', ConstFile::API_RESPONSE_FAIL);
            }
            $data = [
                'store_upper_limit' => '',
                'store_lower_limit' => '',
            ];

            $res = GoodsSpecificPrice::where('id', $param['id'])->update($data);
            if($res){
                return returnJson(ConstFile::API_RESPONSE_SUCCESS_MESSAGE);
            }else{
                return returnJson(ConstFile::API_RESPONSE_FAIL_MESSAGE, ConstFile::API_RESPONSE_FAIL);
            }
        }catch (Exception $e){
            return returnJson($e->getMessage(), $e->getCode());
        }
    }


    /*
     * 添加修改商品
     * */
    public function editGoods($param){
        try{
            $error = $this->checkGoodsData($param);
            if($error){
                return returnJson('参数错误：'.$error, ConstFile::API_RESPONSE_FAIL);
            }

            $id = 0;
            DB::transaction(function () use ($param, &$id) {
                //1.提交商品信息
                $data = $param['need'];
                if(!empty($param['specific_price_ids'])){
                    $specific_price_ids = explode(',', $param['specific_price_ids']);
                    $specific_price = GoodsSpecificPrice::where('id', $specific_price_ids[0])->select('cost_price', 'price', 'wholesale_price')->first()->toArray();
                    if(!empty($specific_price)){
                        $data['cost_price'] = $specific_price['cost_price'];
                        $data['price'] = $specific_price['price'];
                        $data['wholesale_price'] = $specific_price['wholesale_price'];
                    }
                }
                $data['thumb_img'] = '';
                if(!empty($data['img'])){
                    $img = explode(',', $data['img']);
                    $data['thumb_img'] = !empty($img[0]) ? $img[0] : '';
                }

                if(!empty($param['id'])){
                    //修改
                    Goods::where('goods_id', $param['id'])->update($data);

                    $info = Goods::find($param['id'])->with('specific')->with('attribute')->with(['specific_price'=>function($query){
                        $query->select('id', 'goods_id');
                    }])->get(['goods_id', 'goods_name'])->toArray();

                    $res = $this->updateGoods($info, $param, $param['id']);
                    if(!empty($res)){
                        $id = $param['id'];
                    }
                }else{
                    //添加
                    $goods = Goods::create($data);
                    $res = $this->addGoods($param, $goods->goods_id);
                    if(!empty($res)){
                        $id = $goods->goods_id;
                    }
                }
            });

            if($id){
                return returnJson(ConstFile::API_RESPONSE_SUCCESS_MESSAGE,ConstFile::API_RESPONSE_SUCCESS, ['id'=>$id]);
            }else{
                return returnJson(ConstFile::API_RESPONSE_FAIL_MESSAGE, ConstFile::API_RESPONSE_FAIL);
            }
        }catch (Exception $e){
            return returnJson($e->getMessage(), $e->getCode());
        }
    }


    /*
     * 添加商品
     * */
    public function addGoods($param, $id){
        //1.更新规格信息
        $res = 0;
        if(!empty($param['specific_ids'])){
            $specific_ids = explode(',', $param['specific_ids']);
            $spec_da = [];
            foreach ($specific_ids as $v){
                $spec_da[] = [
                    'goods_id' => $id,
                    'spec_id' => $v,
                    'created_at' => date('Y-m-d H:i:s', time()),
                    'updated_at' => date('Y-m-d H:i:s', time())
                ];
            }
            $res = GoodsSpecific::insert($spec_da);
        }
        //2.更新规格定价信息
        if(!empty($param['specific_price_ids'])){
            $specific_price_ids = explode(',', $param['specific_price_ids']);
            $res = GoodsSpecificPrice::whereIn('id', $specific_price_ids)->update(['goods_id'=>$id]);
        }
        //3.更新属性信息
        if(!empty($param['attribute_ids'])){
            $attribute_ids = explode(',', $param['attribute_ids']);
            $attr_da = [];
            foreach ($attribute_ids as $v){
                $attr_da[] = [
                    'goods_id' => $id,
                    'attr_id' => $v,
                    'created_at' => date('Y-m-d H:i:s', time()),
                    'updated_at' => date('Y-m-d H:i:s', time())
                ];
            }
            $res = GoodsAttribute::insert($attr_da);
        }
        return $res;
    }


    /*
     * 修改商品
     * */
    public function updateGoods($info, $param, $id){
        $time = date('Y-m-d H:i:s', time());
        $res = 0;
        //1.更新规格信息
        if(!empty($param['specific_ids'])){
            $select_specific_ids = explode(',', $param['specific_ids']);
            $has_specific_ids = !empty($info['specific']) ? array_column($info['specific'], 'spec_id') : [];
            $del_specific_ids = array_diff($has_specific_ids, $select_specific_ids);
            $add_specific_ids = array_diff($select_specific_ids, $has_specific_ids);

            if(!empty($add_specific_ids)){
                $add_spec_data = [];
                foreach ($add_specific_ids as $v){
                    $add_spec_data[] = [
                        'goods_id' => $id,
                        'spec_id' => $v,
                        'created_at' => date('Y-m-d H:i:s', time()),
                        'updated_at' => date('Y-m-d H:i:s', time())
                    ];
                }
                $res = GoodsSpecific::insert($add_spec_data);
            }
            if(!empty($del_specific_ids)){
                $res = GoodsSpecific::whereIn('spec_id', $del_specific_ids)->update(['deleted_at'=> $time]);
            }
        }
        //2.更新规格定价信息
        if(!empty($param['specific_price_ids'])){
            $specific_price_ids = explode(',', $param['specific_price_ids']);
            $has_specific_price_ids = !empty($info['specific_price']) ? array_column($info['specific_price'], 'id') : [];
            $del_specific_price_ids = array_diff($has_specific_price_ids, $specific_price_ids);
            $up_specific_price_ids = array_diff($specific_price_ids, $has_specific_price_ids);

            if(!empty($del_specific_ids)){
                $res = GoodsSpecificPrice::whereIn('spec_id', $del_specific_price_ids)->update(['deleted_at'=> $time]);
            }
            if(!empty($up_specific_price_ids)){
                $res = GoodsSpecificPrice::whereIn('id', $up_specific_price_ids)->update(['goods_id'=>$id]);
            }
        }
        //3.更新属性信息
        if(!empty($param['attribute_ids'])){
            $select_attribute_ids = explode(',', $param['attribute_ids']);
            $has_attribute_ids = !empty($info['attribute']) ? array_column($info['attribute'], 'attr_id') : [];
            $del_attribute_ids = array_diff($has_attribute_ids, $select_attribute_ids);
            $add_attribute_ids = array_diff($select_attribute_ids, $has_attribute_ids);

            if(!empty($add_attribute_ids)){
                $add_attribute_data = [];
                foreach ($add_attribute_ids as $v){
                    $add_attribute_data[] = [
                        'goods_id' => $id,
                        'attr_id' => $v,
                        'created_at' => date('Y-m-d H:i:s', time()),
                        'updated_at' => date('Y-m-d H:i:s', time())
                    ];
                }
                $res = GoodsAttribute::insert($add_attribute_data);
            }
            if(!empty($del_attribute_ids)){
                $res = GoodsAttribute::whereIn('spec_id', $del_attribute_ids)->update(['deleted_at'=> $time]);
            }
        }
        return $res;
    }


    /*
     * 进入修改页面，商品信息数据
     * */
    public function editGoodsInfo($id){
        try{
            if(empty($id)){
                return returnJson('参数错误：请选择商品', ConstFile::API_RESPONSE_FAIL);
            }

            $goods = new Goods();
            $info = $goods->getGoodsSpecificDetails($id, ['*']);

            $info['category_parent_id'] = explode('_', $info['category_parent_id']);
            $info['img'] = explode(',', $info['img']);

            $attr = $goods->getGoodsAttributeDetails($id);
            if(!empty($attr) && !empty($attr['attribute'])){
                foreach ($attr['attribute'] as &$v){
                    $v['values'] = explode(',', $v['values']);
                }
                $info['attribute'] = $attr['attribute'];
            }

            return $info;
        }catch (Exception $e){
            return returnJson($e->getMessage(), $e->getCode());
        }
    }


    /*
     * 商品列表
     * */
    public function goodsList($param){
        try{
            //$condition = $this->combineGoodsList($param);
            $data = Goods::leftJoin('pas_brands', 'pas_brands.id', '=', 'pas_goods.brand_id');//, 'category_id', 'goods_sn', 'goods_name', 'price', 'description', 'thumb_img',

            if(!empty($param['key'])){
                $key = $param['key'];
                $data->where(function($query) use ($key){
                    $query->where('pas_goods.goods_sn', 'like', '"%'.$key.'%"')->orWhere('pas_goods.goods_name', 'like', '"%'.$key.'%"')->orWhere('pas_brand.name', 'like', '"%'.$key.'%"');
                });
            }
            if(!empty($param['category'])){
                $ids = [$param['category']];
                $child_ids = Category::getChildCategoryID($ids, $param['category'], 1);

                $data->whereIn('pas_goods.category_id', $child_ids);
            }
            if(!empty($param['department'])){
                $data->where('pas_goods.department', $param['department']);
            }
            if(!empty($param['status'])){
                $data->where('pas_goods.status', $param['status']);
            }
            if(!empty($param['order'])){
                if($param['order'] == 'up'){
                    $data->orderBy('pas_goods.sales_num', 'ASC');
                }else{
                    $data->orderBy('pas_goods.sales_num', 'DESC');
                }
            }
            if(!empty($param['goods_from'])){
                $data->where('pas_goods.goods_from', $param['goods_from']);
            }

            if(!empty($param['supplier'])){
                $data->where('suppliers_id', $param['supplier'])
                    ->leftJoin('pas_supplier', 'pas_goods.suppliers_id', '=', 'pas_supplier.id')
                    ->with('specific_price');

                $data->select('pas_goods.category_id', 'pas_goods.goods_id', 'pas_goods.goods_sn', 'pas_goods.goods_name', 'pas_goods.price', 'pas_goods.description', 'pas_goods.thumb_img', 'pas_goods.sales_num', 'pas_goods.status', 'pas_supplier.title');
            }else{
                $data->select('pas_goods.category_id', 'pas_goods.goods_id', 'pas_goods.goods_sn', 'pas_goods.goods_name', 'pas_goods.price', 'pas_goods.description', 'pas_goods.thumb_img', 'pas_goods.sales_num', 'pas_goods.status');
            }

            if(!empty($param['ck'])){
                //仓库判断
            }
            $data = $data->paginate($param['limit'])->toArray();

            $result['total'] = $data['total'];
            $result['total_page'] = $data['last_page'];
            $result['data'] = $data['data'];

            return returnJson(ConstFile::API_RESPONSE_SUCCESS_MESSAGE_DATA,ConstFile::API_RESPONSE_SUCCESS,$result);
        }catch (Exception $e){
            return returnJson($e->getMessage(), $e->getCode());
        }
    }


    /*
     * 商品购买详情
     * */
    public function goodsBuyDetail($param){
        try{
            if(empty($param['goods_id'])){
                return returnJson('参数错误：请选择商品', ConstFile::API_RESPONSE_FAIL);
            }

            $goods = new Goods();
            $data = $goods->getGoodsSpecificDetails($param['goods_id']);

            if(!empty($data) && !empty($data['specific'])){
                if(count($data['specific']) <= 1){
                    $specific = [];
                    $last = $data['specific'];
                }else{
                    $tem = array_chunk($data['specific'], count($data['specific'])-1);
                    $specific = $tem[0];
                    $last = $tem[1][0];
                }

                if(!empty($specific)){
                    $spec_ids = array_column($specific, 'id');
                    sort($spec_ids);
                    $specific_tem = array_field_as_key($specific, 'id');
                    foreach($spec_ids as $v){
                        $sku[] = $specific_tem[$v]['children'][0]['id'];
                    }
                }
                $price = array_field_as_key($data['specific_price'], 'sku');
                foreach($last['children'] as &$v){
                    $sku_tem = $sku;
                    $sku_tem[] = $v['id'];
                    $sku_tem = implode('_', $sku_tem);
                    $v['spec_sku_id'] = !empty($price[$sku_tem]) ? $price[$sku_tem]['id'] : 0;
                    $v['sku'] = $sku_tem;
                    $v['store_count'] = !empty($price[$sku_tem]) ? $price[$sku_tem]['store_count'] : 0;
                    $v['price'] = !empty($price[$sku_tem]) ? $price[$sku_tem]['price'] : $data['price'];
                }
            }

            unset($data['specific'], $data['specific_price']);
            $data['specific'] = $specific;
            $data['last'] = $last;

            return returnJson(ConstFile::API_RESPONSE_SUCCESS_MESSAGE_DATA,ConstFile::API_RESPONSE_SUCCESS, $data);
        }catch (Exception $e){
            return returnJson($e->getMessage(), $e->getCode());
        }
    }


    /*
     * 切换规格选项获取规格组合数据
     * */
    public function changeSpecificItem($param){
        try{
            if(empty($param['goods_id'])){
                return returnJson('参数错误：请选择商品', ConstFile::API_RESPONSE_FAIL);
            }
            if(empty($param['specific'])){
                return returnJson('参数错误：请选择商品规格选项', ConstFile::API_RESPONSE_FAIL);
            }
            $specific = htmlspecialchars_decode($param['specific']);
            $specific = json_decode($specific,true);

            $goods = new Goods();
            $data = $goods->getGoodsSpecificDetails($param['goods_id']);

            if(!empty($data) && !empty($specific)){
                if(count($data['specific']) <= 1){
                    $specific = [];
                    $last = $data['specific'];
                }else{
                    $tem = array_chunk($data['specific'], count($data['specific'])-1);
                    $last = $tem[1][0];
                }

                $spec_ids = array_column($specific, 'spec_id');
                sort($spec_ids);
                $specific_tem = array_field_as_key($specific, 'spec_id');
                foreach($spec_ids as $v){
                    $sku[] = $specific_tem[$v]['item_id'];
                }

                $price = array_field_as_key($data['specific_price'], 'sku');
                foreach($last['children'] as &$v){
                    $sku_tem = $sku;
                    $sku_tem[] = $v['id'];
                    $sku_tem = implode('_', $sku_tem);
                    $v['sku'] = $sku_tem;
                    $v['store_count'] = !empty($price[$sku_tem]) ? $price[$sku_tem]['store_count'] : 0;
                    $v['price'] = !empty($price[$sku_tem]) ? $price[$sku_tem]['price'] : $data['price'];
                }
            }

            return returnJson(ConstFile::API_RESPONSE_SUCCESS_MESSAGE_DATA,ConstFile::API_RESPONSE_SUCCESS, $last);
        }catch (Exception $e){
            return returnJson($e->getMessage(), $e->getCode());
        }
    }


    /*
     * 获取用户购买的商品列表
     * */
    public function userBuyGoodsList($param){
        try{
            if(empty($param['uid'])){
                return returnJson('参数错误：请选择客户', ConstFile::API_RESPONSE_FAIL);
            }

            $count = SaleOrderGoods::where('user_id', $param['uid'])->groupBy('goods_id')->count();

            $sql = 'select goods_id from (select id, goods_id from pas_sale_order_goods where user_id = '.$param['uid'].' order by updated_at desc, id desc) as a group by goods_id order by goods_id desc limit '.(($param['page']-1)*$param['limit']).', '.$param['limit'];
            $goods_id = DB::select(DB::raw($sql));
            $ids = [];
            foreach ($goods_id as $v){
                $ids[] = $v->goods_id;
            }
            $goods = Goods::where('goods_id', $ids)->get()->toArray();

            $result = [
                'total' => $count,
                'page' => $param['page'],
                'limit' => $param['limit'],
                'goods' => $goods
            ];

            return returnJson(ConstFile::API_RESPONSE_SUCCESS_MESSAGE_DATA,ConstFile::API_RESPONSE_SUCCESS, $result);
        }catch (Exception $e){
            return returnJson($e->getMessage(), $e->getCode());
        }
    }


    /*
     * 获取关联规格
     * */
    public function selectRelateSpecific($param){
        try{
            if(empty($param['goods_id'])){
                return returnJson('参数错误：请选择商品', ConstFile::API_RESPONSE_FAIL);
            }
            if(empty($param['item'])){
                return returnJson('参数错误：请选择商品规格选项', ConstFile::API_RESPONSE_FAIL);
            }

            $item = htmlspecialchars_decode($param['item']);
            $item = json_decode($item,true);
            $res = $this->getSpecificCombine($item, 0);

            $key = $res['key'];
            $sku = $res['sku'];

            /*$where = '`key` regexp "^'.$key.'$|^'.$key.'_|_'.$key.'$|_'.$key.'_" and sku regexp "^'.$sku.'$|^'.$sku.'_|_'.$sku.'$|_'.$sku.'_"';
            $specific = GoodsSpecificPrice::where(DB::raw($where))->get(['sku', 'id'])->toArray();*/
            $sql = 'select id, sku from pas_goods_specific_prices where goods_id = '.$param['goods_id'].' and `key` regexp "^'.$key.'$|^'.$key.'_|_'.$key.'$|_'.$key.'_" and sku regexp "^'.$sku.'$|^'.$sku.'_|_'.$sku.'$|_'.$sku.'_"';
            $specific = DB::select($sql);

            $relate_sku = [];
            foreach ($specific as $v){
                $arr = explode('_', $v->sku);
                foreach($arr as $vv){
                    array_push($relate_sku, $vv);
                }
            }
            $relate_sku = array_values(array_unique($relate_sku));

            return returnJson(ConstFile::API_RESPONSE_SUCCESS_MESSAGE_DATA,ConstFile::API_RESPONSE_SUCCESS, $relate_sku);
        }catch (Exception $e){
            return returnJson($e->getMessage(), $e->getCode());
        }
    }


    /*
     * 商品统计
     * */
    public function goodsStatistics(){
        try{
            $stime = date('Y-m-d H:i:s', mktime(0,0,0, date('m'), 1, date('y')));
            $etime = date('Y-m-d H:i:s', mktime(23,59,59,date('m')+1, 0, date('y')));

            $goods_num = GoodsAllocationGoods::whereNull('deleted_at')->sum('number');//商品总库存量
            $month_goods_num = GoodsAllocationGoods::whereNull('deleted_at')->where('created_at', '>=', $stime)->where('updated_at', '<=', $etime)->sum('number');//本月商品库存数量
            $goods_profit = SaleOrderGoods::where('status', 1)->where('created_at', '>=', $stime)->where('updated_at', '<=', $etime)->sum(DB::raw('(price-cost_price)*(num-back_num)'));//本月商品收益
            $sale_num = SaleOrderGoods::where('status', 1)->where('created_at', '>=', $stime)->where('updated_at', '<=', $etime)->sum(DB::raw('num-back_num'));//本月商品销售量

            $result = [
                'goods_num' =>$goods_num,
                'month_goods_num' =>$month_goods_num,
                'goods_profit' =>$goods_profit,
                'sale_num' =>$sale_num,
            ];
            return returnJson(ConstFile::API_RESPONSE_SUCCESS_MESSAGE_DATA,ConstFile::API_RESPONSE_SUCCESS, $result);
        }catch (Exception $e){
            return returnJson($e->getMessage(), $e->getCode());
        }
    }


    /*
     * 检测类目的数据
     * */
    private function checkCategoryData($param){
        $msg = '';

        if(empty(intval($param['deepth'])) && !in_array(intval($param['deepth']), array(1,2,3))){
            $msg = '请填写类目等级';
        }
        if(empty($param['name'])){
            $msg = '请填写类目名称';
        }
        if($param['deepth'] > 1 && empty($param['pid'])){
            $msg = '请选择父级类目';
        }

        return $msg;
    }


    /*
     * 检测规格的数据
     * */
    private function checkSpecificData($param, $type=1){
        $msg = '';

        if($type == 2){
            if(empty($param['id'])){
                $msg = '请选择规格';
            }
            $param['item'] = htmlspecialchars_decode($param['item']);
            $param['item'] = json_decode($param['item'],true);
        }
        if(empty($param['name'])){
            $msg = '请填写规格名称';
        }
        if(empty($param['item'])){
            $msg = '请填写规格选项';
        }

        return $msg;
    }


    /*
     * 检测属性提交数据
     * */
    private function checkAttributeData($param){
        $msg = '';

        if(empty($param['name'])){
            $msg = '请填写规格名称';
        }
        if(empty($param['item'])){
            $msg = '请填写规格选项';
        }

        return $msg;
    }


    /*
     * 检测规格sku定价数据
     * */
    private function checkSkuData($param){
        $msg = '';

        $skuInfo = htmlspecialchars_decode($param['sku']);
        $skuInfo = json_decode($skuInfo,true);

        if(empty($skuInfo)){
            $msg = '请选择规格选项';
        }
        if(empty($param['need']['cost_price'])){
            $msg = '请填写进价';
        }
        if(empty($param['need']['price'])){
            $msg = '请填写零售价';
        }
        if(empty($param['need']['wholesale_price'])){
            $msg = '请填写批发价';
        }

        if($msg){
            return ['status'=>0, 'msg'=>$msg];
        }else{
            $data = $param['need'];
            $res = $this->getSpecificCombine($skuInfo);

            $data['key'] = $res['key'];
            $data['key_name'] = $res['key_name'];
            $data['sku'] = $res['sku'];
            $data['sku_name'] = $res['sku_name'];

            /*$sku = $sku_name = $key = $key_name = [];
            $skuInfo = array_field_as_key($skuInfo, 'spec_id');//print_r($skuInfo);die;

            $key = array_keys($skuInfo);
            sort($key);//print_r($key);die;

            foreach($key as $v){
                $sku[] = $skuInfo[$v]['item_id'];
                $sku_name[] = $skuInfo[$v]['item_name'];
                $key_name[] = $skuInfo[$v]['spec_name'];
            }

            $data['key'] = implode('_', $key);
            $data['key_name'] = implode('+', $key_name);
            $data['sku'] = implode('_', $sku);
            $data['sku_name'] = implode('+', $sku_name);*/

            return ['status'=>1, 'data'=>$data];
        }
    }


    /*
     * 组合规格结果
     * */
    //[{
    //	"spec_id": "1",
    //	"spec_name": "颜色",
    //	"item_id": "13",
    //	"item_name": "蓝色"
    //},{
    //	"spec_id": "2",
    //	"spec_name": "尺寸",
    //	"item_id": "3",
    //	"item_name": "S"
    //}]
    public function getSpecificCombine($skuInfo, $type = 1){
        $sku = $sku_name = $key = $key_name = [];
        $skuInfo = array_field_as_key($skuInfo, 'spec_id');//print_r($skuInfo);die;

        $key = array_keys($skuInfo);
        sort($key);//print_r($key);die;

        foreach($key as $v){
            $sku[] = $skuInfo[$v]['item_id'];
            if($type){
                $sku_name[] = $skuInfo[$v]['item_name'];
                $key_name[] = $skuInfo[$v]['spec_name'];
            }
        }

        $data['key'] = implode('_', $key);
        $data['sku'] = implode('_', $sku);
        if($type){
            $data['key_name'] = implode('+', $key_name);
            $data['sku_name'] = implode('+', $sku_name);
        }

        return $data;
    }


    /*
     * 检测商品提交的数据
     * */
    private function checkGoodsData($param){
        $msg = '';

        foreach($param['need'] as $k => $v){
            if(in_array($k, array_keys($this->field)) && empty($v)){
                $msg = $this->field[$k];
            }
        }
        if(empty($param['specific_price_ids'])){
            if(empty($param['need']['wholesale_price'])){
                $msg = '请填写商品批发价';
            }
            if(empty($param['need']['price'])){
                $msg = '请填写商品零售价';
            }
            if(empty($param['need']['cost_price'])){
                $msg = '请填写商品成本价';
            }
        }
        return $msg;
    }


    /*
     * 批量更新
     * $type 1字符串 0数字
     * */
    public function updateBatch($tableName = "", $multipleData = array(), $type = 1){
        if( $tableName && !empty($multipleData) ) {
            $updateColumn = array_keys($multipleData[0]);
            $referenceColumn = $updateColumn[0]; //e.g id
           // var_dump($updateColumn);

            unset($updateColumn[0]);
            //var_dump($multipleData);die;
            $whereIn = "";

            $q = "UPDATE ".$tableName." SET ";
            foreach ( $updateColumn as $uColumn ) {
                $q .=  $uColumn." = CASE ";

                foreach( $multipleData as $data ) {
                    if($type){
                        $q .= "WHEN ".$referenceColumn." = ".$data[$referenceColumn]." THEN '".$data[$uColumn]."' ";
                    }else{
                        $q .= "WHEN ".$referenceColumn." = ".$data[$referenceColumn]." THEN ".$data[$uColumn]." ";
                    }
                }
                $q .= "ELSE ".$uColumn." END, ";
            }
            foreach( $multipleData as $data ) {
                $whereIn .= "'".$data[$referenceColumn]."', ";
            }
            $q = rtrim($q, ", ")." WHERE ".$referenceColumn." IN (".  rtrim($whereIn, ', ').")";
            //var_dump($q);die;
            return DB::update(DB::raw($q));
        } else {
            return false;
        }
    }

}
