<?php

namespace App\Repositories\PAS;
use App\Constant\ConstFile;
use App\Models\PAS\Goods;
use App\Models\PAS\Purchase\CostInformation;
use App\Models\PAS\Purchase\PurchaseCommodity;
use App\Models\PAS\Purchase\PurchaseCommodityContent;
use App\Models\PAS\Purchase\PurchasePayableMoney;
use App\Models\PAS\Purchase\Supplier;
use App\Models\Workflow\Entry;
use App\Repositories\ParentRepository;
use App\Repositories\RpcRepository;
use App\Services\Workflow\FlowCustomize;
use Illuminate\Http\Request;
use App\Models\PAS\Purchase\Purchase;
use App\Models\Workflow\Proc;
use App\Models\Workflow\Workflow;
use App\Models\OperateLog;
use Carbon\Carbon;
use App\Models\User;
use Exception;
use Auth;
use DB;


/**
 * Class UsersRepositoryEloquent.
 *采购
 * @package namespace App\Repositories;
 */
class PurchaseRepository extends ParentRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Purchase::class;

    }

    /*2019-05-08
     * 采购单编号
     */
    public function getCode() {
        $codes =  $this->getCodes('CG');
        return returnJson($message='OK',$code=ConstFile::API_RESPONSE_SUCCESS,$codes);
    }

    /*2019-05-08
     * 采购单编号
     */
    public function getUniversalCode($user,$arr) {
        if(empty($arr['code'])){
            return returnJson($message='编号前缀不能为空',$code=ConstFile::API_RESPONSE_FAIL);
        }
        $code=trim($arr['code']);
        //var_dump($code);
        $codes =  $this->getCodes($code);
        return returnJson($message='OK',$code=ConstFile::API_RESPONSE_SUCCESS,$codes);
    }
    /*2019-05-08
    * 添加采购单
    * type 1是添加采购单  其他表示 保存草稿
    */
    public function setAdd($user,$arr) {
        $user_id =$user->id;
        $user_name=$user->chinese_name;
        if(empty($arr['type'])){
            return returnJson($message='类型不能为空！',$code=ConstFile::API_RESPONSE_FAIL);
        }
        $type=intval($arr['type']);
        if(empty($arr['code'])){
            return returnJson($message='采购单号不能为空！',$code=ConstFile::API_RESPONSE_FAIL);
        }
        $codes=Purchase::where('code',trim($arr['code']))->count('id');
        if($codes){
            return returnJson($message='采购单号已存在！',$code=ConstFile::API_RESPONSE_FAIL);
        }
        if(empty($arr['day'])){
            return returnJson($message='业务日期不能为空！',$code=ConstFile::API_RESPONSE_FAIL);
        }
        if(empty($arr['supplier_id'])){
            return returnJson($message='供应商不能为空！',$code=ConstFile::API_RESPONSE_FAIL);
        }
        if(empty($arr['apply_id'])){
            return returnJson($message='经手人不能为空！',$code=ConstFile::API_RESPONSE_FAIL);
        }

        if(!empty($arr['remark']) && isset($arr['remark'])){//备注
            $data['remark']=trim($arr['remark']);
        }
        if(!empty($arr['earnest_money']) && isset($arr['earnest_money'])){//定金
            $data['earnest_money']=trim($arr['earnest_money']);
        }
        if(empty($arr['discount'])){//定金
            return returnJson($message='整单折扣不能为空！',$code=ConstFile::API_RESPONSE_FAIL);
        }
        if(!empty($arr['discount']) && isset($arr['discount'])){//定金
            $data['discount']=intval($arr['discount']);
            //var_dump((float)$arr['discount']);die;
            if($data['discount']>100){
                return returnJson($message='整单折扣不能大于100！',$code=ConstFile::API_RESPONSE_FAIL);
            }
        }
        $cost=[];
        if(!empty($arr['cost']) && isset($arr['cost'])) {
            $cost=$arr['cost'];
        }
        if(intval($arr['payable_money']==0)){//此前应付钱
            $data['payable_money']=0;
        }else{
            if(empty($arr['payable_money'])){
                return returnJson($message='此前应付不能为空！',$code=ConstFile::API_RESPONSE_FAIL);
            }
            $data['payable_money']=intval($arr['payable_money']);
        }
        $data['user_id']=$user_id;
        $data['code']=trim($arr['code']);
        $data['business_date']=trim($arr['day']);
        $data['supplier_id']=intval($arr['supplier_id']);
        $infoArr=app()->make(RpcRepository::class)->getCustomerById(intval($arr['supplier_id']));
        $supplierTitle = $infoArr['cusname'];
        if(!$supplierTitle){
            return returnJson($message='供应商数据错误！',$code=ConstFile::API_RESPONSE_FAIL);
        }
        $data['supplier_name']=$supplierTitle;
        $data['apply_id']=intval($arr['apply_id']);
        $chinese_name=User::where('id',$data['apply_id'])->value('chinese_name');
        if(!$chinese_name){
            return returnJson($message='经手人数据错误！',$code=ConstFile::API_RESPONSE_FAIL);
        }
        //dd($arr['cost']);
        $data['apply_name']=$chinese_name;
        $data['created_at']=date('Y-m-d H:i:s' , time());
        $data['updated_at']=date('Y-m-d H:i:s' , time());
        if(!isset($arr['goods']) && !is_array($arr['goods']) ){
            return returnJson($message='商品不能为空！',$code=ConstFile::API_RESPONSE_FAIL);
        }
        try{
            DB::transaction(function() use($data,$user,$type,$arr,$cost) {
                if($type==1){//直接添加数据
                    $data['status']=Purchase::API_STATUS_REVIEW;
                    $dataOne['title']='采购申请单号为'.trim($data['code']);
                    $entry = FlowCustomize::EntryFlow($dataOne, 'pas_purchase');//添加进销存采购申请单 审核流程
                    $data['entrise_id'] = $entry->id;
                }
                $n = Purchase::insertGetId($data);
                //$n=1;
                if($cost){
                    foreach($cost as $key=>&$val){
                        $val['type']=CostInformation::TYPE_PURCHASE;
                        $val['code_id']=$n;
                        $val['created_at']=date('Y-m-d H:i:s',time());
                        $val['updated_at']=date('Y-m-d H:i:s',time());
                    }
                    CostInformation::insert($cost);//添加费用信息
                }
                if(isset($arr['goods']) && is_array($arr['goods']) ){
                    $goods=$arr['goods'];
                    $sum=0;
                    $sumOne=0;
                    $purchase_commodity=[];
                    $commodity_content=[];
                    foreach($goods as $key=>$vals){
                        $goodsInfo=Goods::where('goods_id',intval($vals['goods_id']))->first(['goods_name','thumb_img']);
                        throw_if(!$goodsInfo, new Exception('您选择的商品不存在'));
                        $purchase_commodity['p_code']=$data['code'];//采购单号
                        $purchase_commodity['p_id']=$n;//采购表id
                        $purchase_commodity['c_url']=$goodsInfo->thumb_img;
                        $purchase_commodity['c_name']=$goodsInfo->goods_name;//商品名称
                        $purchase_commodity['c_id']=$vals['goods_id'];//商品id
                        $purchase_commodity['created_at']=date('Y-m-d H:i:s',time());
                        $purchase_commodity['updated_at']=date('Y-m-d H:i:s',time());
                        $sumTow=0;
                        $sumMoney=0;
                        foreach ($vals['sku'] as $k=>$va){
                            $commodity_content['p_code']=$data['code'];//采购单号
                            $commodity_content['p_id']=$n;//采购表id
                            $commodity_content['sku_id']=trim($va['sku_id']);//sku (规则组合)
                            $commodity_content['sku']=trim($va['sku']);//sku (规则组合)
                            $commodity_content['goods_url']=$goodsInfo->thumb_img;//商品名称
                            $commodity_content['goods_name']=$goodsInfo->goods_name;//商品名称
                            $commodity_content['goods_id']=$vals['goods_id'];//商品id
                            $commodity_content['price'] =$va['price'];//价格
                            $commodity_content['number']=intval($va['number']);//商品(sku)采购的数量
                            $commodity_content['money']=$va['price']*$va['number'];//总金额
                            $commodity_content['war_number']=intval($va['number']);//可入库数量
                            $commodity_content['created_at']=date('Y-m-d H:i:s',time());
                            $commodity_content['updated_at']=date('Y-m-d H:i:s',time());
                            $commodity_contentArr[]=$commodity_content;

                            $sum=$sum+$va['price']*$va['number'];//所有商品的金额
                            $sumOne=$sumOne+intval($va['number']);//所有商品的总数量
                            $sumTow=$sumTow+intval($va['number']);//商品的总数量
                            $sumMoney=$sumMoney+$va['price']*$va['number'];//商品的总金额
                        }
                        $purchase_commodity['number']=$sumTow;//商品的总数量
                        $purchase_commodity['money']=$sumMoney;//商品的总金额

                        $purchase_commodityArr[]=$purchase_commodity;
                    }

                    PurchaseCommodity::insert($purchase_commodityArr);
                    PurchaseCommodityContent::insert($commodity_contentArr);

                    $sumOne=floatval(($sum*$data['discount'])/100);
                    $datass['total_sum']=$sum;
                    $datass['turnover_amount']=$sumOne;
                    $datass['number']=$sumTow;
                    $wheress['id']=$n;
                    Purchase::where($wheress)->update($datass);
                }
            });
            if($type==1){
                return returnJson($message = '提交成功', $code = ConstFile::API_RESPONSE_SUCCESS);
            }else{
                return returnJson($message = '草稿保存成功', $code = ConstFile::API_RESPONSE_SUCCESS);
            }

        }catch (Exception $e){
            return returnJson($e->getMessage(),$code=ConstFile::API_RESPONSE_FAIL);
        }
    }

    /*2019-05-08
    * 修改采购单
    * type 1是添加采购单  其他表示 保存草稿
    */
    public function setUpdate($user,$arr) {
        $user_id =$user->id;
        $user_name=$user->chinese_name;
        if(empty($arr['id'])){
            return returnJson($message='参数不能为空！',$code=ConstFile::API_RESPONSE_FAIL);
        }
        if(empty($arr['type'])){
            return returnJson($message='类型不能为空！',$code=ConstFile::API_RESPONSE_FAIL);
        }
        $type=intval($arr['type']);
        if(empty($arr['code'])){
            return returnJson($message='采购单号不能为空！',$code=ConstFile::API_RESPONSE_FAIL);
        }
        $codes=Purchase::where('code',trim($arr['code']))->where('id','!=',trim($arr['id']))->count('id');

        if($codes){
            return returnJson($message='采购单号已存在！',$code=ConstFile::API_RESPONSE_FAIL);
        }
        if(empty($arr['day'])){
            return returnJson($message='业务日期不能为空！',$code=ConstFile::API_RESPONSE_FAIL);
        }
        if(empty($arr['supplier_id'])){
            return returnJson($message='供应商不能为空！',$code=ConstFile::API_RESPONSE_FAIL);
        }
        if(empty($arr['apply_id'])){
            return returnJson($message='经手人不能为空！',$code=ConstFile::API_RESPONSE_FAIL);
        }
        if(!empty($arr['discount']) && isset($arr['discount'])){//定金
            $data['discount']=(float)$arr['discount'];
            //var_dump((float)$arr['discount']);die;
            if($data['discount']>100){
                return returnJson($message='整单折扣不能大于100！',$code=ConstFile::API_RESPONSE_FAIL);
            }
        }
        $cost=[];
        if(!empty($arr['cost']) && isset($arr['cost'])) {
            $cost=$arr['cost'];
        }
        if(!empty($arr['remark']) && isset($arr['remark'])){//备注
            $data['remark']=trim($arr['remark']);
        }
        if(!empty($arr['earnest_money']) && isset($arr['earnest_money'])){//定金
            $data['earnest_money']=trim($arr['earnest_money']);
        }
        if(intval($arr['payable_money']==0)){//此前应付钱
            $data['payable_money']=0;
        }else{
            if(empty($arr['payable_money'])){
                return returnJson($message='此前应付不能为空！',$code=ConstFile::API_RESPONSE_FAIL);
            }
            $data['payable_money']=intval($arr['payable_money']);
        }
        if(empty($arr['cost']) && isset($arr['cost'])){

        }

        $where['id']=$arr['id'];
        $data['user_id']=$user_id;
        $data['code']=trim($arr['code']);
        $data['business_date']=trim($arr['day']);
        $data['supplier_id']=trim($arr['supplier_id']);
        $infoArr=app()->make(RpcRepository::class)->getCustomerById(intval($arr['supplier_id']));
        $supplierTitle = $infoArr['cusname'];
        if(!$supplierTitle){
            return returnJson($message='供应商数据错误！',$code=ConstFile::API_RESPONSE_FAIL);
        }
        $data['supplier_name']=$supplierTitle;
        $data['apply_id']=intval($arr['apply_id']);
        $chinese_name=User::where('id',$data['apply_id'])->value('chinese_name');
        if(!$chinese_name){
            return returnJson($message='经手人数据错误！',$code=ConstFile::API_RESPONSE_FAIL);
        }
        $data['apply_name']=$chinese_name;
        $data['updated_at']=date('Y-m-d H:i:s' , time());
        //dd($arr['cost']);
        try{
            DB::transaction(function() use($data,$user,$type,$arr,$cost)   {
                $where['id']=intval($arr['id']);
                $where['user_id']=$user->id;
                $entrise_id = Purchase::where($where)->value('entrise_id');
                if($type==1){//直接添加数据
                    $data['status']=Purchase::API_STATUS_REVIEW;
                    if(!$entrise_id){
                        $dataOne['title']='采购申请单号为'.trim($data['code']);
                        $entry = FlowCustomize::EntryFlow($dataOne, 'pas_purchase');//添加进销存采购申请单 审核流程
                        $data['entrise_id'] = $entry->id;
                    }
                }

                $n = Purchase::where($where)->update($data);

                $wheres['code_id']=intval($arr['id']);
                $wheres['type']=1;
                if($cost) {
                    foreach ($arr['cost'] as $key => $val) {
                        $wheres['id'] = $val['id'];
                        if(!$val['id']){
                            unset($val['id']);
                            $val['type']=CostInformation::TYPE_PURCHASE;
                            $val['code_id'] = intval($arr['id']);
                            $val['created_at'] = date('Y-m-d H:i:s', time());
                            $val['updated_at'] = date('Y-m-d H:i:s', time());
                            CostInformation::insert($val);
                        }else{
                            unset($val['id']);
                            $val['updated_at'] = date('Y-m-d H:i:s', time());
                            CostInformation::where($wheres)->update($val);
                        }

                    }
                }
                if(isset($arr['goods']) && is_array($arr['goods']) ) {
                    $goods = $arr['goods'];
                    $sum = 0;
                    $sumOne = 0;
                    $purchase_commodity = [];
                    $commodity_content = [];
                    foreach ($goods as $key => $vals) {
                        $goodsInfo=Goods::where('goods_id',intval($vals['goods_id']))->first(['goods_name','thumb_img']);
                        throw_if(!$goodsInfo, new Exception('您选择的商品不存在'));
                        $purchase_commodity['id'] = $vals['id'];//采购单号
                        $purchase_commodity['p_code'] = $data['code'];//采购单号
                        $purchase_commodity['c_url']=$goodsInfo->thumb_img;
                        $purchase_commodity['c_name']=$goodsInfo->goods_name;//商品名称
                        $purchase_commodity['c_id'] = $vals['goods_id'];//商品id
                        $purchase_commodity['updated_at'] = date('Y-m-d H:i:s', time());
                        $sumTow = 0;
                        $sumMoney = 0;
                        foreach ($vals['sku'] as $k => $va) {
                            $commodity_content['id'] = $va['id'];//采购单号
                            $commodity_content['p_code'] = $data['code'];//采购单号
                            $commodity_content['sku_id']=trim($va['sku_id']);//sku (规则组合)
                            $commodity_content['sku'] = trim($va['sku']);//sku (规则组合)
                            $commodity_content['goods_url'] = $goodsInfo->thumb_img;//商品名称
                            $commodity_content['goods_name'] = $goodsInfo->goods_name;//商品名称
                            $commodity_content['goods_id'] = $vals['goods_id'];//商品id
                            $commodity_content['price'] = $va['price'];//价格
                            $commodity_content['number'] = intval($va['number']);//商品(sku)采购的数量
                            $commodity_content['money'] = $va['price'] * $va['number'];//总金额
                            $commodity_content['war_number'] = intval($va['number']);//可入库数量
                            $commodity_content['updated_at'] = date('Y-m-d H:i:s', time());
                            $commodity_contentArr[] = $commodity_content;

                            $sum = $sum + $va['price'] * $va['number'];//所有商品的金额
                            $sumOne = $sumOne + intval($va['number']);//所有商品的总数量
                            $sumTow = $sumTow + intval($va['number']);//商品的总数量
                            $sumMoney = $sumMoney + $va['price'] * $va['number'];//商品的总金额
                        }
                        $purchase_commodity['number'] = $sumTow;//商品的总数量
                        $purchase_commodity['money'] = $sumMoney;//商品的总金额

                        $purchase_commodityArr[] = $purchase_commodity;
                    }
                    app()->make(GoodsRepository::class)->updateBatch('pas_purchase_commodity',$purchase_commodityArr);
                    app()->make(GoodsRepository::class)->updateBatch('pas_purchase_commodity_content',$commodity_contentArr);

                    $sumThne = floatval(($sum * $data['discount'])/10);
                    $datass['total_sum'] = $sum;
                    $datass['turnover_amount'] = $sumThne;
                    $datass['number'] = $sumOne;
                    $wheress['id'] = intval($arr['id']);;
                    Purchase::where($wheress)->update($datass);
                }
            });
            return returnJson($message = '修改成功', $code = ConstFile::API_RESPONSE_SUCCESS);
        }catch (Exception $e){
            return returnJson($e->getMessage(),$code=ConstFile::API_RESPONSE_FAIL);
        }
    }

    /**
     * 获取我在供应商那此前应付多少钱
     * @param $arr
     */
    public function getPayableMoney($user,$arr)
    {
        if(empty($arr['id'])){
            return returnJson($message = '访问错误请正确访问！', $code = ConstFile::API_RESPONSE_FAIL);
        }
        $id = $arr['id'];
        $where['supplier_id']=$id;
        $where['status']=1;
        $money = PurchasePayableMoney::where($where)->value('money');
        if(!$money){
            $money=0;
        }
        return returnJson($message = 'ok', $code = ConstFile::API_RESPONSE_SUCCESS,$money);
    }
    /**
     * 采购单列表
     */
    public function getPurchaseList($user,$arr)
    {
        $user_id = $user->id;
        $where['user_id'] = $user_id;
        $where['status'] = Purchase::API_STATUS_SUCCESS;
        if(!empty($arr['p_status']) && isset($arr['p_status'])){
            $where['p_status'] = 0;
        }
        if(!empty($arr['w_status']) && isset($arr['w_status'])){
            $where['w_status'] = 0;
        }

        if(!empty($arr['title']) && isset($arr['title'])){
            $list = Purchase::where($where)->where(function ($query) use($arr){
                $query->orWhere('code','like','%'.trim($arr['title']).'%')
                    ->orWhere('supplier_name','like','%'.trim($arr['title']).'%');
            })->orderBy('created_at','desc')->select(['id','code','created_at','number','apply_name','turnover_amount'])->paginate(10);
        }else{
            $list = Purchase::where($where)->orderBy('created_at','desc')->select(['id','code','created_at','number','apply_name','turnover_amount'])->paginate(10);
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
        return returnJson($message = 'OK', $code = ConstFile::API_RESPONSE_SUCCESS,$list);
    }


    /**
     * 我申请的采购单列表
     */
    public function getWeList($user,$arr)
    {
        $user_id = $user->id;
        $where['user_id']=$user_id;

        if($arr['status']==0){
            $where['status'] = 0;
        }
        if(!empty($arr['status']) && isset($arr['status'])) {
            if(intval($arr['status'])!=-1){
                $where['status'] = intval($arr['status']);
            }
        }
        if(!empty($arr['title']) && isset($arr['title'])){
            $list = Purchase::where($where)->where(function ($query) use($arr){
                $query->orWhere('code','like','%'.trim($arr['title']).'%')
                    ->orWhere('supplier_name','like','%'.trim($arr['title']).'%');
            })->orderBy('created_at','desc')->select(['id','code','created_at','number','total_sum','supplier_name','status'])->paginate(10);
        }else{
            $list = Purchase::where($where)->orderBy('created_at','desc')->select(['id','code','created_at','number','total_sum','supplier_name','status'])->paginate(10);
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
        return returnJson($message = 'OK', $code = ConstFile::API_RESPONSE_SUCCESS,$list);
    }
    //采购单列表
    public function getOrderList($user,$arr)
    {
        $user_id = $user->id;
        if($arr['status']==0){
            $where['status'] = 0;
        }
        if(!empty($arr['status']) && isset($arr['status'])) {
            if(intval($arr['status'])!=-1){
                $where['status'] = intval($arr['status']);
            }
        }
        $where[]=['id','>',0];
        if(!empty($arr['title']) && isset($arr['title'])){
            $list = Purchase::where($where)->where(function ($query) use($arr){
                $query->orWhere('code','like','%'.trim($arr['title']).'%')
                    ->orWhere('supplier_name','like','%'.trim($arr['title']).'%');
            })->orderBy('created_at','desc')->select(['id','code','created_at','number','total_sum','supplier_name','status'])->paginate(10);
        }else{
            $list = Purchase::where($where)->orderBy('created_at','desc')->select(['id','code','created_at','number','total_sum','supplier_name','status'])->paginate(10);
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
        return returnJson($message = '修改成功', $code = ConstFile::API_RESPONSE_SUCCESS,$list);
    }
    /**
     * 我申请的采购单详情
     */
    public function getPurchaseInfo($user,$arr)
    {
        $user_id = $user->id;
        $where['user_id']=$user_id;
        $where['id']=intval($arr['id']);
        $info = Purchase::with(['costList','goodsList'])->where($where)->first();
        if($info){
            $info=$info->toArray();
            $where=[];
            foreach ($info['goods_list'] as $key=>$value){
               $where['p_id']= $value['p_id'];
               $where['goods_id']= $value['c_id'];
               $info['goods_list'][$key]['sku_list']= PurchaseCommodityContent::where($where)->get(['goods_id','sku','sku_id','number','price','money','status','id']);
            }
            if($info['status']==0){
                $info['process'] =[];
            }else{
                $entry = Entry::findOrFail(intval($info['entrise_id']));
                $info['process'] = $this->fetchEntryProcess($entry);
            }
        }else{
            $info=[];
        }
        return returnJson($message = 'OK', $code = ConstFile::API_RESPONSE_SUCCESS,$info);
    }

    /**
     * 我申请的采购单详情
     */
    public function getPurchaseInfoOne($user,$arr)
    {
        $user_id = $user->id;
        $where['user_id']=$user_id;
        $where['entrise_id']=intval($arr['id']);
        $info = Purchase::with(['costList','goodsList'])->where($where)->first();
        if($info){
            $info=$info->toArray();
            $where=[];
            foreach ($info['goods_list'] as $key=>$value){
                $where['p_id']= $value['p_id'];
                $where['goods_id']= $value['c_id'];
                $info['goods_list'][$key]['sku_list']= PurchaseCommodityContent::where($where)->get(['goods_id','sku','sku_id','number','price','money','status','id']);
            }
            $entry = Entry::findOrFail(intval($arr['id']));
            $info['process'] = $this->fetchEntryProcess($entry);
        }else{
            $info=[];
        }
        return returnJson($message = 'OK', $code = ConstFile::API_RESPONSE_SUCCESS,$info);
    }


    /**
     * 我审核的采购单详情
     */
    public function getTrialPurchaseInfo($user,$arr)
    {
        $user_id = $user->id;

        if(empty($arr['id'])){
            return returnJson($message = '参数错误!', $code = ConstFile::API_RESPONSE_FAIL);
        }
        $where['id']=intval($arr['id']);
        $where['user_id']=$user->id;
        //var_dump($where);die;
        $entry_id = Proc::where($where)->value('entry_id');
        if(!$entry_id){
            return returnJson($message = '参数错误!', $code = ConstFile::API_RESPONSE_FAIL);
        }
        $wheres['entrise_id']=$entry_id;
        $info = Purchase::with('costList','goodsList')->where($wheres)->first();

        if($info){
            $info=$info->toArray();
            $info['wp_id']=intval($arr['id']);
            $where=[];
            foreach ($info['goods_list'] as $key=>$value){
                $where['p_id']= $value['p_id'];
                $where['goods_id']= $value['c_id'];
                $info['goods_list'][$key]['sku_list']= PurchaseCommodityContent::where($where)->get(['goods_id','sku','sku_id','number','price','money','status','id']);
            }
            $entry = Entry::findOrFail($entry_id);
            $info['process'] = $this->fetchEntryProcess($entry);
        }else{
            $info=[];
        }
        return returnJson($message = 'OK', $code = ConstFile::API_RESPONSE_SUCCESS,$info);
    }
    /**
     * 4-28
     * gaolu
     * 工作流申请审核人的状态数据转换
     */
    private function fetchEntryProcess(Entry $entry)
    {

        $processes = (new Workflow())->getProcs($entry);
        //dd($entry->toArray());die;
        if (empty($processes)) {
            throw new Exception('流程没有配置审批节点');
        }
        $status=$entry->status;
        $processAuditors = $temp = [];
        foreach ($processes as $key=> $process) {
            $temp['process_name'] = $process->process_name;
            $temp['auditor_name'] = '';
            $temp['approval_content'] = $process->proc ? $process->proc->content : '';

            if ($process->proc && $process->proc->auditor_name) {
                $temp['auditor_name'] = $process->proc->auditor_name;
            } elseif ($process->proc && $process->proc->user_name) {
                $temp['auditor_name'] = $process->proc->user_name;
            } else {
                $temp['auditor_name'] = $process->auditors;
            }

            $temp['status'] = $process->proc ? $process->proc->status : '';
            $temp['status_name'] = '';
            if($status==-2){
                //echo 1;die;
                if ($process->proc && $process->proc->status == Proc::STATUS_PASSED) {
                    $temp['status_name'] = '已撤回';
                } else {
                    $temp['status_name'] = '完成';
                }

            }else{
                if ($process->proc && $process->proc->status == Proc::STATUS_REJECTED) {
                    $temp['status_name'] = '驳回';
                } elseif ($process->proc && $process->proc->status == Proc::STATUS_PASSED) {
                    $temp['status_name'] = '完成';
                } else {
                    $temp['status_name'] = '待处理';
                }
            }
            $processAuditors[] = $temp;
        }

        return $processAuditors;
    }
    /**
     * 删除采购单中的费用信息数据
     */
    public function delCost($user,$arr)
    {
        $user_id = $user->id;
        //$where['user_id']=$user_id;
        $where['id']=intval($arr['id']);
        $data['status']=0;
        $n = CostInformation::where($where)->update($data);
        if($n){
            return returnJson($message = '费用信息删除成功', $code = ConstFile::API_RESPONSE_SUCCESS);
        }
        return returnJson($message = '费用信息删除失败', $code = ConstFile::API_RESPONSE_FAIL);
    }

    /**
     * 获取采购订单中商品信息接口
     */

    /**
     * 采购订单撤回
     */

    public function withdraw($user,$arr){
        $where['user_id']=$user->id;
        $where['status']=Purchase::API_STATUS_REVIEW;
        if(empty($arr['id'])){
            return returnJson($message = '参数错误！', $code = ConstFile::API_RESPONSE_FAIL);
        }
        $where['id']=intval($arr['id']);
        $data['status']=Purchase::API_STATUS_WITHDRAW;
        $data['updated_at']=date('Y-m-d H:i:s',time());
        try{
                Purchase::where($where)->update($data);
                $where['status']=Purchase::API_STATUS_WITHDRAW;
                $entrise_id= Purchase::where($where)->value('entrise_id');

                //$wheres['id']=$entrise_id;
                //Entry::where($wheres)->update();
                $n=$this->cancel($entrise_id,'',$user->id);//流的撤销
            if(!$n){
                return returnJson($message = '已有审核记录，不能撤销操作', $code = ConstFile::API_RESPONSE_FAIL);
            }
            return returnJson($message = '撤销成功', $code = ConstFile::API_RESPONSE_SUCCESS);
        }catch (Exception $e){
            return returnJson($e->getMessage(),$code=ConstFile::API_RESPONSE_FAIL);
        }
    }

    /**
     * 撤销
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|string
     */
    public function cancel($entry_id,$content='',$uid){

        $entryObj = Entry::find($entry_id);
        //已经有审核的记录了 不让撤销
        $cnt = Proc::query()->where('entry_id', '=', $entry_id)
            ->where('status', '=', Proc::STATUS_PASSED)
            ->where('user_id', '!=', $uid)
            ->count();
        if($cnt > 0){
            return 0;
        }
        if(empty($content)){
            $content = Carbon::now()->toDateString() . '用户:' . Auth::user()->chinese_name . '撤销了ID为' . $entry_id . '的流程申请';
        }
        $data = [
            'operate_user_id' => $uid,
            'action' => 'cancel',
            'type' => OperateLog::TYPE_WORKFLOW,
            'object_id' => $entry_id,
            'object_name' => $entryObj->title,
            'content' => $content,
        ];
        OperateLog::query()->insert($data);
        $entryObj->status = Entry::STATUS_CANCEL;
        $res = $entryObj->save();
        //
        return $res;
    }
}
