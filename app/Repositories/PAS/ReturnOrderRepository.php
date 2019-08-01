<?php

namespace App\Repositories\PAS;
use App\Models\PAS\Purchase\CostInformation;
use App\Models\PAS\Purchase\PurchaseCommodityContent;
use App\Models\PAS\Purchase\PurchasePayableMoney;
use App\Models\PAS\Purchase\ReturnOrder;
use App\Constant\ConstFile;
use App\Models\PAS\Purchase\WarehousingApplyContent;
use App\Models\PAS\Warehouse\WarehouseDeliveryType;
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
use DB;


/**
 * Class UsersRepositoryEloquent.
 *退货单
 * @package namespace App\Repositories;
 */
class ReturnOrderRepository extends ParentRepository
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
        $codes =  $this->getCodes('TH');
        return returnJson($message='OK',$code=ConstFile::API_RESPONSE_SUCCESS,$codes);
    }
    /*2019-05-13
    * 添加退货单
    * type 1是添加退货单  其他表示 保存草稿
    */
    public function setAdd($user,$arr) {
        $user_id =$user->id;
        $user_name=$user->chinese_name;
        if(empty($arr['type'])){
            return returnJson($message='类型不能为空！',$code=ConstFile::API_RESPONSE_FAIL);
        }
        $type=intval($arr['type']);
        if(empty($arr['code'])){
            return returnJson($message='退货单号不能为空！',$code=ConstFile::API_RESPONSE_FAIL);
        }
        $ReturnOrder=ReturnOrder::where('code',trim($arr['code']))->count(['id']);
        if($ReturnOrder){
            return returnJson($message='退货单号已存在！',$code=ConstFile::API_RESPONSE_FAIL);
        }
        $Purchase =Purchase::where('code',trim($arr['p_code']))->first(['id','discount']);
        if(!$Purchase){
            return returnJson($message='采购单号错误！',$code=ConstFile::API_RESPONSE_FAIL);
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
        if(!empty($arr['is_warehousing']) && isset($arr['is_warehousing'])){//是否入库
            $data['type']=intval($arr['is_warehousing']);
        }else{
            $data['type']=0;
        }

        if(!empty($arr['remark']) && isset($arr['remark'])){//备注
            $data['remarks']=trim($arr['remark']);
        }
        if(!empty($arr['invoice_id']) && isset($arr['invoice_id'])){//发送方式ID
            $data['invoice_id']=trim($arr['invoice_id']);
        }

        if(intval($arr['payable_money']==0)){//此前应付钱
            $data['payable_money']=0;
        }else{
            if(empty($arr['payable_money'])){
                return returnJson($message='此前应付不能为空！',$code=ConstFile::API_RESPONSE_FAIL);
            }
            $data['payable_money']=intval($arr['payable_money']);
        }
        $cost=[];
        if(!empty($arr['cost']) && isset($arr['cost'])) {
            $cost=$arr['cost'];
        }

        $data['user_id']=$user_id;
        $data['code']=trim($arr['code']);
        $data['p_id']=$Purchase->id;
        $data['p_code']=trim($arr['p_code']);
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
        if(empty($arr['invoice_id'])){
            return returnJson($message='发货方式不能为空！',$code=ConstFile::API_RESPONSE_FAIL);
        }

        $counts= WarehouseDeliveryType::where('id',intval($arr['invoice_id']))->count('id');
        if(!$counts){
            return returnJson($message='发货方式不存在！',$code=ConstFile::API_RESPONSE_FAIL);
        }
        $data['invoice_id']=intval($arr['invoice_id']);
        //dd($arr['cost']);
        $data['apply_name']=$chinese_name;
        $data['created_at']=date('Y-m-d H:i:s' , time());
        $data['updated_at']=date('Y-m-d H:i:s' , time());
        if(!isset($arr['goods']) && !is_array($arr['goods']) ){
            return returnJson($message='商品不能为空！',$code=ConstFile::API_RESPONSE_FAIL);
        }
        if(!isset($arr['goods']) && !is_array($arr['goods']) ) {
            return returnJson($message='入库商品不能为空！',$code=ConstFile::API_RESPONSE_FAIL);
        }else{
            $goods= $arr['goods'];
            $towsum=0;
            $sumNumber=0;
            foreach($goods as $k=>&$value){
                $where['id']=intval($value['sku_id']);
                $info = PurchaseCommodityContent::where($where)->first(['number','r_number','rw_number','war_number','wa_number','goods_name','sku','price']);
                if($info){
                    $datason['id']=intval($value['sku_id']);
                    $info=$info->toArray();
                    if($data['type']==1){//入库成功过后的退货
                        $Pnumber=$info['wa_number']-$info['rw_number'];
                        if(intval($value['number'])>$Pnumber){
                            return returnJson($message='退货数量超过可退获数量！',$code=ConstFile::API_RESPONSE_FAIL);
                        }
                        $datason['rw_number']=$info['rw_number']+intval($value['number']);//退获数量
                    }else{//入库前的退货
                        $Pnumber=$info['war_number']-$info['r_number'];
                        if(intval($value['number'])>$Pnumber){
                            return returnJson($message='退货数量超过可退获数量！',$code=ConstFile::API_RESPONSE_FAIL);
                        }
                        $datason['r_number']=$info['r_number']+intval($value['number']);//退获数量
                    }
                    $value['money']=intval($value['number']) * $info['price'];
                    $towsum =$towsum + (intval($value['number']) * $info['price']);//商品退货总金额
                    $sumNumber=$sumNumber + intval($value['number']);//商品退货总数量
                    $datas[]=$datason;
                }else{
                    return returnJson($message='参数错误！',$code=ConstFile::API_RESPONSE_FAIL);
                }
            }
            $data['money']=round(($towsum * $Purchase->discount)/100,2);//退货单中的总价
            $data['number']=$sumNumber;//退货单中的总数量
        }
        try{
            DB::transaction(function() use($data,$user,$datas,$type,$arr,$goods,$cost) {
                $data['status']=ReturnOrder::STATUS_DEFAULT;
                if($type==1){//直接添加数据
                    $data['status']=ReturnOrder::STATUS_STAY;
                    $dataOne['title']='退货申请单号为'.trim($data['code']);
                    $entry = FlowCustomize::EntryFlow($dataOne, 'pas_return_order');//添加进销存采购申请单 审核流程
                    $data['entrise_id'] = $entry->id;
                }
                $n = ReturnOrder::insertGetId($data);

                //$n=1;
                if($cost){
                    foreach ($cost as $key => &$val) {
                        $val['type'] = CostInformation::TYPE_RETURN_ORDER;//
                        $val['code_id'] = $n;
                        $val['created_at'] = date('Y-m-d H:i:s', time());
                        $val['updated_at'] = date('Y-m-d H:i:s', time());
                    }
                    CostInformation::insert($cost);//添加费用信息
                }
                //dd($arr['goods']);

                if(isset($arr['goods']) && is_array($arr['goods']) ){
                    foreach ($goods as $k=>$va){
                        $dataR['p_id'] =$n;
                        $dataR['pcc_id'] =intval($va['sku_id']);
                        $dataR['sku_id'] =intval($va['gsku_id']);
                        $dataR['code'] =$data['code'];
                        $dataR['number'] =intval($va['number']);
                        $dataR['money'] =intval($va['money']);
                        $dataR['type']  =WarehousingApplyContent::TYPE_RETURN_ORDER;
                        $dataR['created_at']=date('Y-m-d H:i:s' , time());
                        $dataR['updated_at']=date('Y-m-d H:i:s' , time());
                        $dataArr[]=$dataR;
                    }
                    WarehousingApplyContent::insert($dataArr);
                    //dd($dataArr);
                    if($type==1){//提交申请
                        app()->make(GoodsRepository::class)->updateBatch('pas_purchase_commodity_content',$datas);
                    }
                }
            });
            if($type==1){
                return returnJson($message = '申请提交成功', $code = ConstFile::API_RESPONSE_SUCCESS);
            }else{
                return returnJson($message = '草稿保存成功', $code = ConstFile::API_RESPONSE_SUCCESS);
            }
        }catch (Exception $e){
            return returnJson($e->getMessage(),$code=ConstFile::API_RESPONSE_FAIL);
        }
    }
    /*2019-05-13
    * 添加退货单
    * type 1是添加退货单  其他表示 保存草稿
    */
    public function setUpdate($user,$arr) {
        $user_id =$user->id;
        $user_name=$user->chinese_name;
        if(empty($arr['id'])){
            return returnJson($message='退货单号id不能为空！',$code=ConstFile::API_RESPONSE_FAIL);
        }
        $id=intval($arr['id']);

        if(empty($arr['type'])){
            return returnJson($message='类型不能为空！',$code=ConstFile::API_RESPONSE_FAIL);
        }
        $type=intval($arr['type']);
        if(empty($arr['p_code'])){
            return returnJson($message='采购单号不能为空！',$code=ConstFile::API_RESPONSE_FAIL);
        }

        $Purchase =Purchase::where('code',trim($arr['p_code']))->first(['id','discount']);
        if(!$Purchase){
            return returnJson($message='采购单号错误！',$code=ConstFile::API_RESPONSE_FAIL);
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
        if(!empty($arr['is_warehousing']) && isset($arr['is_warehousing'])){//是否入库
            $data['type']=intval($arr['is_warehousing']);
        }else{
            $data['type']=0;
        }
        $cost=[];
        if(!empty($arr['cost']) && isset($arr['cost'])) {
            $cost=$arr['cost'];
        }
        if(!empty($arr['remark']) && isset($arr['remark'])){//备注
            $data['remarks']=trim($arr['remark']);
        }
        if(intval($arr['payable_money']==0)){//此前应付钱
            $data['payable_money']=0;
        }else{
            if(empty($arr['payable_money'])){
                return returnJson($message='此前应付不能为空！',$code=ConstFile::API_RESPONSE_FAIL);
            }
            $data['payable_money']=intval($arr['payable_money']);
        }
        if(empty($arr['invoice_id'])){
            return returnJson($message='发货方式不能为空！',$code=ConstFile::API_RESPONSE_FAIL);
        }

        $counts= WarehouseDeliveryType::where('id',intval($arr['invoice_id']))->count('id');
        if(!$counts){
            return returnJson($message='发货方式不存在！',$code=ConstFile::API_RESPONSE_FAIL);
        }
        $data['invoice_id']=intval($arr['invoice_id']);
        $data['user_id']=$user_id;
        $data['p_id']=$Purchase->id;
        $data['p_code']=trim($arr['p_code']);
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
        $data['updated_at']=date('Y-m-d H:i:s' , time());
        if(!isset($arr['goods']) && !is_array($arr['goods']) ){
            return returnJson($message='商品不能为空！',$code=ConstFile::API_RESPONSE_FAIL);
        }
        if(!isset($arr['goods']) && !is_array($arr['goods']) ) {
            return returnJson($message='入库商品不能为空！',$code=ConstFile::API_RESPONSE_FAIL);
        }else{
            $goods= $arr['goods'];
            $towsum=0;
            $sumNumber=0;
            foreach($goods as $k=>&$value){
                $where['id']=intval($value['sku_id']);
                $info = PurchaseCommodityContent::where($where)->first(['number','r_number','rw_number','war_number','wa_number','goods_name','sku','price']);
                if($info){
                    $datason['id']=intval($value['sku_id']);
                    $info=$info->toArray();
                    if($data['type']==1){//入库成功过后的退货
                        $Pnumber=$info['wa_number']-$info['rw_number'];
                        if(intval($value['number'])>$Pnumber){
                            return returnJson($message='退货数量超过可退获数量！',$code=ConstFile::API_RESPONSE_FAIL);
                        }
                        $datason['rw_number']=$info['rw_number']+intval($value['number']);//退获数量
                    }else{//入库前的退货
                        $Pnumber=$info['war_number']-$info['r_number'];
                        if(intval($value['number'])>$Pnumber){
                            return returnJson($message='退货数量超过可退获数量！',$code=ConstFile::API_RESPONSE_FAIL);
                        }
                        $datason['r_number']=$info['r_number']+intval($value['number']);//退获数量
                    }
                    $value['money']=intval($value['number']) * $info['price'];
                    $towsum =$towsum + (intval($value['number']) * $info['price']);//商品退货总金额
                    $sumNumber=$sumNumber + intval($value['number']);//商品退货总数量
                    $datas[]=$datason;
                }else{
                    return returnJson($message='参数错误！',$code=ConstFile::API_RESPONSE_FAIL);
                }
            }
            $data['money']=round(($towsum * $Purchase->discount)/100,2);//退货单中的总价
            $data['number']=$sumNumber;//退货单中的总数量
        }
        try{
            DB::transaction(function() use($data,$user,$datas,$type,$arr,$goods,$cost) {
                $where['id']=intval($arr['id']);
                $where['user_id']=$user->id;
                $entrise_id = Purchase::where($where)->value('entrise_id');
                $data['status']=ReturnOrder::STATUS_DEFAULT;
                if($type==1){//直接添加数据
                    $data['status']=ReturnOrder::STATUS_STAY;
                    if(!$entrise_id){
                        $dataOne['title']='退货申请单号为'.trim($data['code']);
                        $entry = FlowCustomize::EntryFlow($dataOne, 'pas_return_order');//添加进销存采购申请单 审核流程
                        $data['entrise_id'] = $entry->id;
                    }
                }
                $n = ReturnOrder::where($where)->update($data);

                //$n=1;
                if($cost) {
                    $cost = $arr['cost'];
                    foreach ($cost as $key => &$val) {
                        $val['updated_at'] = date('Y-m-d H:i:s', time());
                        if(!$val['id']){
                            unset($val['id']);
                            $val['type']= CostInformation::TYPE_RETURN_ORDER;
                            $val['code_id'] = intval($arr['id']);
                            $val['created_at'] = date('Y-m-d H:i:s', time());
                            $val['updated_at'] = date('Y-m-d H:i:s', time());
                            CostInformation::insert($val);
                            unset($cost[$key]);
                        }else{
                            $val['updated_at'] = date('Y-m-d H:i:s', time());
                        }
                    }
                    app()->make(GoodsRepository::class)->updateBatch('pas_cost_information',$cost);
                }

               // CostInformation::insert($cost);//添加费用信息
                if(isset($arr['goods']) && is_array($arr['goods']) ){
                    foreach ($goods as $k=>$va){
                        $dataR['id'] =intval($va['id']);
                        $dataR['number'] =intval($va['number']);
                        $dataR['money'] =intval($va['money']);
                        $dataR['updated_at']=date('Y-m-d H:i:s' , time());
                        $dataArr[]=$dataR;
                    }
                    app()->make(GoodsRepository::class)->updateBatch('pas_warehousing_apply_content',$dataArr);
                    //dd($dataArr);
                    if($type==1){//提交申请
                        app()->make(GoodsRepository::class)->updateBatch('pas_purchase_commodity_content',$datas);
                    }
                }
            });
            if($type==1){
                return returnJson($message = '申请提交成功', $code = ConstFile::API_RESPONSE_SUCCESS);
            }else{
                return returnJson($message = '草稿保存成功', $code = ConstFile::API_RESPONSE_SUCCESS);
            }

        }catch (Exception $e){
            return returnJson($e->getMessage(),$code=ConstFile::API_RESPONSE_FAIL);
        }
    }



    /**
     * 我申请的退货单列表
     */
    public function getWeList($user,$arr)
    {
        $user_id = $user->id;
        if (!empty($arr['type']) && isset($arr['type'])) {
            $where['type'] = 1;
        } else {
            $where['user_id'] = $user_id;
        }
        if($arr['status']==0){
            $where['status'] = 0;
        }
        if(!empty($arr['status']) && isset($arr['status'])) {
            if(intval($arr['status'])!=-1){
                $where['status'] = intval($arr['status']);
            }
        }
        if(!empty($arr['title']) && isset($arr['title'])){
            $list = ReturnOrder::where($where)->where(function ($query) use($arr){
                $query->orWhere('code','like','%'.trim($arr['title']).'%')
                    ->orWhere('supplier_name','like','%'.trim($arr['title']).'%');
            })->orderBy('created_at','desc')->select(['id','code','business_date','number','money','supplier_name','status'])->paginate(10);
        }else{
            $list = ReturnOrder::where($where)->orderBy('created_at','desc')->select(['id','code','business_date','number','money','supplier_name','status'])->paginate(10);
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
     * 退货单详情
     */
    public function getInfo($user,$arr)
    {
        if(!empty($arr['type']) && isset($arr['type'])) {
            $where['type'] = 1;
        }else{
            $user_id = $user->id;
            $where['user_id']=$user_id;
        }

        $where['id']=intval($arr['id']);
        $info = ReturnOrder::with('costList')->where($where)->first();

        if($info){
            $info=$info->toArray();
            $wheres['a.type']=WarehousingApplyContent::TYPE_RETURN_ORDER;
            $wheres['a.p_id']=$info['id'];
            $list = DB::table('pas_warehousing_apply_content as a')
                ->leftJoin('pas_purchase_commodity_content as b' ,'a.pcc_id','b.id')
                ->where($wheres)->get(['a.id as pccid','a.number','b.id','b.goods_id','b.goods_name','b.goods_url','b.number as sum_number','b.war_number','b.wa_number','b.r_number','b.rw_number','b.sku','b.sku_id','b.price']);
            $lists=[];
            if($list){
                $lists=$list->toArray();
            }
            $info['sku_list']=$lists;
            $entry_id=$info['entrise_id'];
            $info['process']=[];
            if($entry_id){
                $entry = Entry::findOrFail($entry_id);
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
    public function getInfoOne($user,$arr)
    {
        $user_id = $user->id;
        $where['user_id']=$user_id;
        $where['entrise_id']=intval($arr['id']);
        $info = ReturnOrder::with('costList')->where($where)->first();
        if($info){
            $info=$info->toArray();
            $wheres['a.type']=WarehousingApplyContent::TYPE_RETURN_ORDER;
            $wheres['a.p_id']=$info['id'];
            $list = DB::table('pas_warehousing_apply_content as a')
                ->leftJoin('pas_purchase_commodity_content as b' ,'a.pcc_id','b.id')
                ->where($wheres)->get(['a.id as pccid','a.number','b.goods_id','b.goods_name','b.goods_url','b.number as sum_number','b.sku','b.sku_id','b.price']);
            $lists=[];
            if($list){
                $lists=$list->toArray();
            }
            $info['sku_list']=$lists;
            $entry_id=$info['entrise_id'];
            $info['process']=[];
            if($entry_id){
                $entry = Entry::findOrFail($entry_id);
                $info['process'] = $this->fetchEntryProcess($entry);
            }
        }else{
            $info=[];
        }
        return returnJson($message = 'OK', $code = ConstFile::API_RESPONSE_SUCCESS,$info);
    }


    /**
     * 我审核的采购单详情
     */
    public function getInfoTow($user,$arr)
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
        $info = ReturnOrder::with('costList')->where($wheres)->first();
        $wheres=[];
        if($info){
            $info=$info->toArray();
            $wheres['a.type']=WarehousingApplyContent::TYPE_RETURN_ORDER;
            $wheres['a.p_id']=$info['id'];
            $info['w_id']=intval($arr['id']);
            $list = DB::table('pas_warehousing_apply_content as a')
                ->leftJoin('pas_purchase_commodity_content as b' ,'a.pcc_id','b.id')
                ->where($wheres)->get(['a.id as pccid','a.number','b.goods_id','b.goods_name','b.goods_url','b.number as sum_number','b.sku','b.sku_id','b.price']);
            $lists=[];
            if($list){
                $lists=$list->toArray();
            }
            $info['sku_list']=$lists;
            $entry_id=$info['entrise_id'];
            $info['process']=[];
            if($entry_id){
                $entry = Entry::findOrFail($entry_id);
                $info['process'] = $this->fetchEntryProcess($entry);
            }
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
    public function fetchEntryProcess(Entry $entry)
    {

        $processes = (new Workflow())->getProcs($entry);
        //dd($entry->toArray());die;
        if (empty($processes)) {
            throw new Exception('流程没有配置审批节点');
        }
        $status=$entry->status;
        $processAuditors = $temp = [];

        foreach ($processes as $key=> $process) {//print_r($process);die;
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
            $temp['updated_at'] = ($process->proc && $temp['status'] != Proc::STATUS_IN_HAND) ? $process->proc->updated_at : '';

            $temp['status_name'] = '';
            if($status==-2){
                if ($process->proc && $process->proc->status == Proc::STATUS_PASSED) {
                    $temp['status_name'] = '已撤回';
                } else {
                    $temp['status_name'] = '已完成';
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
     * 撤回
     */

    public function withdraw($user,$arr){
        $where['user_id']=$user->id;
        $where['status']=ReturnOrder::STATUS_STAY_PENDING;
        if(empty($arr['id'])){
            return returnJson($message = '参数错误！', $code = ConstFile::API_RESPONSE_FAIL);
        }
        $where['id']=intval($arr['id']);
        $entrise_id = ReturnOrder::where($where)->value('entrise_id');
        if(!$entrise_id){
            return returnJson($message = '参数错误！', $code = ConstFile::API_RESPONSE_FAIL);
        }
        $data['status']=ReturnOrder::STATUS_PART;
        $data['updated_at']=date('Y-m-d H:i:s',time());
        try{
            DB::transaction(function() use($data,$user,$where,$entrise_id) {
                $id=$where['id'];
                $list = DB::table('pas_warehousing_apply_content as a')
                    ->leftJoin('pas_purchase_commodity_content as b' ,'a.pcc_id','b.id')
                    ->where('a.p_id','=',$id)
                    ->where('a.type','=',WarehousingApplyContent::TYPE_RETURN_ORDER)
                    ->get(['a.number','b.id','b.r_number']);
                //var_dump($list);die;
                foreach($list as $key=>$values){
                    $whereone['id']=$values->id;
                    $dataOnes['r_number']=$values->r_number-$values->number?$values->r_number-$values->number:0;
                    $dataOnes['updated_at']=date('Y-m-d H:i:s',time());
                    PurchaseCommodityContent::where($whereone)->update($dataOnes);
                }
                ReturnOrder::where($where)->update($data);
                //$wheres['id']=$entrise_id;
                //Entry::where($wheres)->update();
                $n = $this->cancel($entrise_id, '', $user);//流的撤销
                if (!$n) {
                    return returnJson($message = '已有审核记录，不能撤销操作', $code = ConstFile::API_RESPONSE_FAIL);
                }
            });
            return returnJson($message = '撤销成功', $code = ConstFile::API_RESPONSE_SUCCESS);
        }catch (Exception $e){
            return returnJson($e->getMessage(),$code=ConstFile::API_RESPONSE_FAIL);
        }
    }
    /**
     * 查询退货单跟采购单关联页面的详情数据
     */
    public function  getRelationInfo($user,$arr){
        if(empty($arr['id'])){
            return returnJson($message='参数错误！',$code=ConstFile::API_RESPONSE_FAIL);
        }
        $where['id']=intval($arr['id']);
        $info=Purchase::with('skuList')->where($where)
            ->first(['id','code','supplier_id','supplier_name','apply_id','apply_name', 'earnest_money', 'turnover_amount','w_status', 'business_date']);

        if($info){
            $info->th_code =  $this->getCodes('TH');
            $money = PurchasePayableMoney::where('supplier_id',$info->supplier_id)->value('money');
            $info->payable_money =$money;
            return returnJson($message = 'ok', $code = ConstFile::API_RESPONSE_SUCCESS,$info->toArray());
        }

        return returnJson($message = 'ok', $code = ConstFile::API_RESPONSE_SUCCESS,[]);
    }


    /**
     * 撤销
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|string
     */
    public function cancel($entry_id,$content='',$user){

        $entryObj = Entry::find($entry_id);
        //已经有审核的记录了 不让撤销
        $cnt = Proc::query()->where('entry_id', '=', $entry_id)
            ->where('status', '=', Proc::STATUS_PASSED)
            ->where('user_id', '!=', $user->id)
            ->count();
        if($cnt > 0){
            return 0;
        }
        if(empty($content)){
            $content = Carbon::now()->toDateString() . '用户:' . $user->chinese_name . '撤销了ID为' . $entry_id . '的流程申请';
        }
        $data = [
            'operate_user_id' => $user->id,
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
