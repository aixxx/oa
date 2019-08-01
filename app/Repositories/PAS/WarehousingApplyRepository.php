<?php

namespace App\Repositories\PAS;
use App\Models\PAS\Purchase\Purchase;
use App\Models\PAS\Purchase\PurchaseCommodityContent;
use App\Models\PAS\Purchase\PurchasePayableMoney;
use App\Models\PAS\Purchase\ReturnOrder;
use App\Models\PAS\Purchase\WarehousingApply;
use App\Models\PAS\Purchase\WarehousingApplyContent;
use App\Models\PAS\Warehouse\WarehouseDeliveryType;
use App\Repositories\ParentRepository;
use App\Constant\ConstFile;
use App\Models\User;
use App\Repositories\RpcRepository;
use Exception;
use Auth;
use DB;


/**
 * Class UsersRepositoryEloquent.
 *入库申请
 * @package namespace App\Repositories;
 */
class WarehousingApplyRepository extends ParentRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return WarehousingApply::class;
    }

    /*
     * 2019-05-08
     * 添加入库申请单（保存草稿）
     */
    public function setAdd($user,$arr){
        $user_id =$user->id;
        $this->getErr($user,$arr);
        $user_name=$user->chinese_name;
        if(!empty($arr['type']) && isset($arr['type'])){
            $type=intval($arr['type']);
        }else{
            $type=1;
        }

        if(empty($arr['code'])){
            return returnJson($message='入库单号不能为空！',$code=ConstFile::API_RESPONSE_FAIL);
        }
        if(empty($arr['p_code'])){
            return returnJson($message='采购单号不能为空！',$code=ConstFile::API_RESPONSE_FAIL);
        }

        $Purchase =Purchase::where('code',trim($arr['p_code']))->first(['id','discount','w_status']);
        if(!$Purchase){
            return returnJson($message='采购单号错误！',$code=ConstFile::API_RESPONSE_FAIL);
        }
        if($Purchase->w_status==1){
            return returnJson($message='采购单已经全部入库！',$code=ConstFile::API_RESPONSE_FAIL);
        }
        if(empty($arr['supplier_id'])){
            return returnJson($message='供应商不能为空！',$code=ConstFile::API_RESPONSE_FAIL);
        }
        if(empty($arr['apply_id'])){
            return returnJson($message='经手人不能为空！',$code=ConstFile::API_RESPONSE_FAIL);
        }
        $data['apply_id']=intval($arr['apply_id']);
        $chinese_name=User::where('id',$data['apply_id'])->value('chinese_name');
        if(!$chinese_name){
            return returnJson($message='经手人数据错误！',$code=ConstFile::API_RESPONSE_FAIL);
        }
        $data['apply_name']=$chinese_name;

        if(empty($arr['day'])){
            return returnJson($message='业务日期不能为空！',$code=ConstFile::API_RESPONSE_FAIL);
        }
        if(intval($arr['payable_money'])==0){//此前应付钱
            $data['payable_money']=0;
        }else{
            if(empty($arr['payable_money'])){
                return returnJson($message='此前应付不能为空！',$code=ConstFile::API_RESPONSE_FAIL);
            }
            $data['payable_money']=intval($arr['payable_money']);
        }
        $infoArr=app()->make(RpcRepository::class)->getCustomerById(intval($arr['supplier_id']));
        $supplierTitle = $infoArr['cusname'];
        if(!$supplierTitle){
            return returnJson($message='供应商数据错误！',$code=ConstFile::API_RESPONSE_FAIL);
        }

        if(!empty($arr['remark']) && isset($arr['remark'])){
            $data['remarks']=trim($arr['remark']);
        }
        if(!isset($arr['goods']) && !is_array($arr['goods']) ) {
            return returnJson($message='入库商品不能为空！',$code=ConstFile::API_RESPONSE_FAIL);
        }

        $wherecode['code']=trim($arr['code']);
        $count =  WarehousingApply::where($wherecode)->count('id');
        if($count){
            return returnJson($message='入库单号已存在！',$code=ConstFile::API_RESPONSE_FAIL);
        }
        $data['code']=trim($arr['code']);
        $data['p_id']=$Purchase->id;
        $data['p_code']=trim($arr['p_code']);
        $data['business_date']=trim($arr['day']);
        $data['supplier_id']=trim($arr['supplier_id']);
        $data['supplier_name']=$supplierTitle;
        $data['apply_id']=intval($arr['apply_id']);
        $data['user_id']=$user_id;
        $chinese_name=User::where('id',$data['apply_id'])->value('chinese_name');
        if(!$chinese_name){
            return returnJson($message='经手人数据错误！',$code=ConstFile::API_RESPONSE_FAIL);
        }
        $data['apply_name']=$chinese_name;
        $data['created_at']=date('Y-m-d H:i:s' , time());
        $data['updated_at']=date('Y-m-d H:i:s' , time());
        //dd($data);
        $goods=[];
        if(!isset($arr['goods']) && !is_array($arr['goods']) ) {
            return returnJson($message='入库商品不能为空！',$code=ConstFile::API_RESPONSE_FAIL);
        }else{
            $goods= $arr['goods'];
            $towsum=0;
            foreach($arr['goods'] as $k=>$value){
                $where['id']=intval($value['id']);
                $info = PurchaseCommodityContent::where($where)->first(['war_number','r_number','wa_number','goods_name','sku','price']);
                if($k==0){
                    $data['goods_name']=$info->goods_name.$info->sku;
                }
                $datas['id']=intval($value['id']);
                if($info){
                    $info=$info->toArray();
                    if(($info['war_number']-$info['r_number']) < intval($value['number'])){
                        return returnJson($message='入库数量超过可入库数量',$code=ConstFile::API_RESPONSE_FAIL);
                    }
                    $towsum =$towsum + (intval($value['number']) * $info['price']);//可入库数量
                    $datas['war_number']=$info['war_number']-intval($value['number']);//可入库数量
                    $datas['wa_number']=$info['wa_number']+intval($value['number']);//已入库数量
                }else{
                    return returnJson($message='商品sku数据不存在！',$code=ConstFile::API_RESPONSE_FAIL);
                }
                $datasArr[]=$datas;
            }
            $data['money']=$towsum;
        }
        try{
            DB::transaction(function() use($data,$user,$datasArr,$type,$goods,$Purchase) {
                if($type!=1){
                    $data['status']=0;
                }
                $n = WarehousingApply::insertGetId($data);
                foreach($goods as $key =>$value){
                    $dataR['p_id'] =$n;
                    $dataR['pcc_id'] =intval($value['id']);
                    $dataR['sku_id'] =intval($value['sku_id']);
                    $dataR['code'] =$data['code'];
                    $dataR['number'] =intval($value['number']);
                    $dataR['type']  =WarehousingApplyContent::TYPE_WAREHOUSING;
                    $dataR['created_at']=date('Y-m-d H:i:s' , time());
                    $dataR['updated_at']=date('Y-m-d H:i:s' , time());
                    $dataArr[]=$dataR;
                }
                WarehousingApplyContent::insert($dataArr);
                if($type==1){//提交申请
                    app()->make(GoodsRepository::class)->updateBatch('pas_purchase_commodity_content',$datasArr);
                    $whereOnes['id']=$Purchase->id;
                    $whereOnesS['p_id']=$Purchase->id;
                    $war_numbers = PurchaseCommodityContent::where($whereOnesS)->sum('war_number');
                    $r_number = PurchaseCommodityContent::where($whereOnesS)->sum('r_number');
                    if(($war_numbers-$r_number)==0){
                        $datatsArr['w_status']=1;
                        Purchase::where($whereOnes)->update($datatsArr);//修改采购单的入库状态
                    }
                }
            });
            if($type==1){
                return returnJson($message = '入库单添加成功', $code = ConstFile::API_RESPONSE_SUCCESS);
            }else{
                return returnJson($message = '草稿保存成功', $code = ConstFile::API_RESPONSE_SUCCESS);
            }

        }catch (Exception $e){
            return returnJson($e->getMessage(),$code=ConstFile::API_RESPONSE_FAIL);
        }
    }

    /*
     * 2019-05-08
     *入库申请单申请（修改草稿）
     */
    public function setUpdate($user,$arr){
        $user_id =$user->id;
        $user_name=$user->chinese_name;

        $this->getErr($user,$arr);
        if(!empty($arr['type']) && isset($arr['type'])){
            $type=intval($arr['type']);
        }else{
            $type=1;
        }
        if(empty($arr['id'])){
            return returnJson($message='入库单号id不能为空！',$code=ConstFile::API_RESPONSE_FAIL);
        }
        $id=intval($arr['id']);
        if(empty($arr['code'])){
            return returnJson($message='入库单号不能为空！',$code=ConstFile::API_RESPONSE_FAIL);
        }
        if(empty($arr['p_code'])){
            return returnJson($message='采购单号不能为空！',$code=ConstFile::API_RESPONSE_FAIL);
        }
        $Purchase =Purchase::where('code',trim($arr['p_code']))->first(['id','discount','w_status']);
        if(!$Purchase){
            return returnJson($message='采购单号错误！',$code=ConstFile::API_RESPONSE_FAIL);
        }
        if($Purchase->w_status==1){
            return returnJson($message='采购单已经全部入库！',$code=ConstFile::API_RESPONSE_FAIL);
        }
        if(empty($arr['supplier_id'])){
            return returnJson($message='供应商不能为空！',$code=ConstFile::API_RESPONSE_FAIL);
        }
        if(empty($arr['apply_id'])){
            return returnJson($message='经手人不能为空！',$code=ConstFile::API_RESPONSE_FAIL);
        }
        $data['apply_id']=intval($arr['apply_id']);
        $chinese_name=User::where('id',$data['apply_id'])->value('chinese_name');
        if(!$chinese_name){
            return returnJson($message='经手人数据错误！',$code=ConstFile::API_RESPONSE_FAIL);
        }
        $data['apply_name']=$chinese_name;

        if(empty($arr['day'])){
            return returnJson($message='业务日期不能为空！',$code=ConstFile::API_RESPONSE_FAIL);
        }
        if(intval($arr['payable_money']==0)){//此前应付钱
            $data['payable_money']=0;
        }else{
            if(empty($arr['payable_money'])){
                return returnJson($message='此前应付不能为空！',$code=ConstFile::API_RESPONSE_FAIL);
            }
            $data['payable_money']=intval($arr['payable_money']);
        }
        $infoArr=app()->make(RpcRepository::class)->getCustomerById(intval($arr['supplier_id']));
        $supplierTitle = $infoArr['cusname'];
        if(!$supplierTitle){
            return returnJson($message='供应商数据错误！',$code=ConstFile::API_RESPONSE_FAIL);
        }

        if(!empty($arr['remark']) && isset($arr['remark'])){
            $data['remarks']=trim($arr['remark']);
        }
        if(!isset($arr['goods']) && !is_array($arr['goods']) ) {
            return returnJson($message='入库商品不能为空！',$code=ConstFile::API_RESPONSE_FAIL);
        }

        $wherecode['code']=trim($arr['code']);
        $wherecode[]=['id','!=',intval($id)];
        $count =  WarehousingApply::where($wherecode)->count('id');
        if($count){
            return returnJson($message='入库单号已存在！',$code=ConstFile::API_RESPONSE_FAIL);
        }
        $data['code']=trim($arr['code']);
        $data['p_id']=$Purchase->id;
        $data['p_code']=trim($arr['p_code']);
        $data['business_date']=trim($arr['day']);
        $data['supplier_id']=trim($arr['supplier_id']);
        $data['supplier_name']=$supplierTitle;
        $data['apply_id']=intval($arr['apply_id']);
        $data['user_id']=$user_id;

        $chinese_name=User::where('id',$data['apply_id'])->value('chinese_name');
        if(!$chinese_name){
            return returnJson($message='经手人数据错误！',$code=ConstFile::API_RESPONSE_FAIL);
        }
        $data['apply_name']=$chinese_name;
        $data['created_at']=date('Y-m-d H:i:s' , time());
        //dd($data);
        $goods=[];
        if(!isset($arr['goods']) && !is_array($arr['goods']) ) {
            return returnJson($message='入库商品不能为空！',$code=ConstFile::API_RESPONSE_FAIL);
        }else{
            $goods= $arr['goods'];
            $towsum=0;
            foreach($arr['goods'] as $k=>$value){
                $where['id']=intval($value['sku_id']);
                $where[]=['war_number','>=',intval($value['number'])];
                $info = PurchaseCommodityContent::where($where)->first(['war_number','r_number','wa_number','goods_name','sku','price']);
                if($k){
                    $data['goods_name']=$info->goods_name.$info->sku;
                }
                $datas['id']=intval($value['id']);
                if($info){
                    $info=$info->toArray();
                    if(($info['war_number']-$info['r_number']) < intval($value['number'])){
                        return returnJson($message='入库数量超过可入库数量',$code=ConstFile::API_RESPONSE_FAIL);
                    }
                    $towsum =$towsum + (intval($value['number']) * $info['price']);//可入库数量
                    $datas['war_number']=$info['war_number']-intval($value['number']);//可入库数量
                    $datas['wa_number']=$info['wa_number']+intval($value['number']);//已入库数量
                }else{
                    return returnJson($message='入库商品的数量不能大于总数量！',$code=ConstFile::API_RESPONSE_FAIL);
                }
                $datasArr[]=$datas;
            }
            $data['money']=$towsum;
        }
        try{
            DB::transaction(function() use($data,$user,$datasArr,$type,$goods,$id,$Purchase) {
                if($type==1){
                    $data['status']=1;
                }
                $whereid['id']=$id;
                WarehousingApply::where($whereid)->update($data);
                foreach($goods as $key =>$value){
                    $dataR['id'] =$value['id'];
                    $dataR['code'] =$data['code'];
                    $dataR['number'] =intval($value['number']);
                    $dataR['updated_at']=date('Y-m-d H:i:s' , time());
                    $dataArr[]=$dataR;
                }
                app()->make(GoodsRepository::class)->updateBatch('pas_warehousing_apply_content',$dataArr);
                //WarehousingApplyContent::insert($dataArr);
                if($type==1){//提交申请
                    app()->make(GoodsRepository::class)->updateBatch('pas_purchase_commodity_content',$datasArr);
                    $whereOnes['id']=$Purchase->id;
                    $whereOnesS['p_id']=$Purchase->id;
                    $war_numbers = PurchaseCommodityContent::where($whereOnesS)->sum('war_number');
                    $r_number = PurchaseCommodityContent::where($whereOnesS)->sum('r_number');
                    if(($war_numbers-$r_number)==0){
                        $datatsArr['w_status']=1;
                        Purchase::where($whereOnes)->update($datatsArr);//修改采购单的入库状态
                    }
                }
            });
            if($type==1){
                return returnJson($message = '入库单添加成功', $code = ConstFile::API_RESPONSE_SUCCESS);
            }else{
                return returnJson($message = '草稿修改成功', $code = ConstFile::API_RESPONSE_SUCCESS);
            }
        }catch (Exception $e){
            return returnJson($e->getMessage(),$code=ConstFile::API_RESPONSE_FAIL);
        }
    }
    /*2019-05-08
     * 采购单编号
     */
    public function getCode() {
        $codes =  $this->getCodes('RK');
        return returnJson($message='OK',$code=ConstFile::API_RESPONSE_SUCCESS,$codes);
    }

    /*2019-05-08
    * 获取供应商详情
    */
    public function getInfo($user,$arr)
    {
        $where['id']= intval($arr['id']);
        $first = ['id','code','p_code','business_date','supplier_id','supplier_name','apply_id','apply_name','payable_money','invoice_id','remarks','status','created_at'];
        $info =  WarehousingApply::where($where)->first($first);

        if($info){
            $info=$info->toArray();
//            if($info['status']>1){
//                $wheres['in_id']=$info['id'];
//               $warehouse_id= WarehouseInGoods::where($wheres)->groupBy('warehouse_id')->get(['warehouse_id']);
//               dd($warehouse_id->toArray());
//
//            }else{
                $wheres['a.type']=1;//申请入库商品数据
                $wheres['a.p_id']=$info['id'];
                $list = DB::table('pas_warehousing_apply_content as a')
                    ->leftJoin('pas_purchase_commodity_content as b' ,'a.pcc_id','b.id')
                    ->where($wheres)->get(['a.id as pccid','a.number','b.id as sku_ids','b.goods_id','b.goods_name','b.goods_url','b.number as sum_number','b.war_number','b.r_number','b.sku','b.sku_id','b.price']);
                $lists=[];
                if($list){
                    $lists=$list->toArray();
                }
                $info['sku_list']=$lists;
//            }


            //获取已经入库信息  仓库名称   仓库地址   仓库货位（提供接口）
            $info['s_name']='';
            $info['s_address']='';
            $info['s_cargo']='';
        }else{
            $info=[];
        }
        return returnJson($message='OK',$code=ConstFile::API_RESPONSE_SUCCESS,$info);
    }

    /*2019-05-08
    * 入库申请单列表   type =1  申请人查看列表    type =2   供给仓库查看
    */
    public function getList($user,$arr)
    {
        $user_id=$user->id;
        if(!isset($arr['type']) && !is_array($arr['type']) ) {
            $type=1;
        }else{
            $type=1;
            if(intval($arr['type'])!=1){
                $type=2;
            }
        }
        if($arr['status']==0){
            $where['status'] = 0;
        }
        if(!empty($arr['status']) && isset($arr['status'])) {
            if(intval($arr['status'])!=-1){
                $where[]=['status','=',intval($arr['status'])];
            }
        }

        if($type==1){
            $where['user_id']=$user_id;
        }else{
            $where[]=['status','>=',0];
        }

        if(!empty($arr['title']) && isset($arr['title'])){
            $list = WarehousingApply::where($where)->where(function ($query) use($arr){
                $query->orWhere('code','like','%'.trim($arr['title']).'%')
                    ->orWhere('supplier_name','like','%'.trim($arr['title']).'%')
                    ->orWhere('goods_name','like','%'.trim($arr['title']).'%');
            })->orderBy('created_at','desc')->select(['id','code','goods_name','business_date','supplier_name','status','money'])->paginate(10);
        }else{
            $list = WarehousingApply::where($where)->select(['id','code','goods_name','business_date','supplier_name','status','money'])->orderBy('created_at','desc')->paginate(10);
        }
        if($list){
            $list=$list->toArray();
            unset($list['first_page_url']);
            unset($list['from']);
            unset($list['last_page']);
            unset($list['last_page_url']);
            unset($list['next_page_url']);
            unset($list['path']);
            unset($list['prev_page_url']);
        }else{
            $list=[];
        }
        return returnJson($message='OK',$code=ConstFile::API_RESPONSE_SUCCESS,$list);
    }


    /**
     * 查询入库单跟采购单关联页面的详情数据
     */
    public function  getRelationInfo($user,$arr){
        if(empty($arr['id'])){
            return returnJson($message='参数错误！',$code=ConstFile::API_RESPONSE_FAIL);
        }
        $where['id']=intval($arr['id']);
        $info=Purchase::with('skuList')->where($where)
            ->first(['id','code','supplier_id','supplier_name','apply_id','apply_name', 'earnest_money', 'turnover_amount','w_status', 'business_date']);

        if($info){
            $info->rk_code =  $this->getCodes('RK');
            $money = PurchasePayableMoney::where('supplier_id',$info->supplier_id)->value('money');
            $info->payable_money =$money;
            return returnJson($message = 'ok', $code = ConstFile::API_RESPONSE_SUCCESS,$info->toArray());
        }

        return returnJson($message = 'ok', $code = ConstFile::API_RESPONSE_SUCCESS,[]);
    }
    /**
     * 查询入库单跟采购单关联页面的详情数据
     */
    public function  getErr($user,$arr){
        if(empty($arr['id'])){
            return returnJson($message='参数错误！',$code=ConstFile::API_RESPONSE_FAIL);
        }
        $where['id']=intval($arr['id']);
        $code=Purchase::where($where)->value('code');
        if($code){
            $id=ReturnOrder::where('p_code',$code)->where('status',1)->count('id');
            if($id){
                return returnJson($message = '有商品在退货，请在退货操作完成过后在进行该操作!',$code = ConstFile::API_RESPONSE_FAIL);
            }
        }
        return returnJson($message = 'ok', $code = ConstFile::API_RESPONSE_SUCCESS);
    }

    /**
     * 关联发货方式
     */
    public function setInvoiceAdd($user,$arr){
        if(empty($arr['id'])){
            return returnJson($message='参数错误！',$code=ConstFile::API_RESPONSE_FAIL);
        }
        $count = ReturnOrder::where('id',intval($arr['id']))->count('id');
        if(!$count){
            return returnJson($message='采购单不存在！',$code=ConstFile::API_RESPONSE_FAIL);
        }
        if(empty($arr['invoice_id'])){
            return returnJson($message='参数错误！',$code=ConstFile::API_RESPONSE_FAIL);
        }
        $counts= WarehouseDeliveryType::where('id',intval($arr['invoice_id']))->count('id');
        if(!$counts){
            return returnJson($message='发货方式不存在！',$code=ConstFile::API_RESPONSE_FAIL);
        }
        $data['invoice_id']=$arr['invoice_id'];
        $data['updated_at']=date('Y-m-d H:i:s');
        $n = ReturnOrder::where('id',intval($arr['id']))->update($data);

        if($n){
            return returnJson($message = '发货方式添加成功', $code = ConstFile::API_RESPONSE_SUCCESS,intval($arr['id']));
        }
        return returnJson($message = '发货方式添加失败',$code = ConstFile::API_RESPONSE_FAIL);
    }
}
