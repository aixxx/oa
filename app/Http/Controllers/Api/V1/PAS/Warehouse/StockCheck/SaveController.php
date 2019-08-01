<?php

namespace App\Http\Controllers\Api\V1\PAS\Warehouse\StockCheck;

use App\Constant\ConstFile;
use App\Exceptions\DiyException;
use App\Http\Controllers\Api\V1\ApiController;
use App\Models\PAS\Warehouse\GoodsAllocation;
use App\Models\PAS\Warehouse\GoodsAllocationGoods;
use App\Models\PAS\Warehouse\StockCheck;
use App\Models\PAS\Warehouse\StockCheckGoods;
use Carbon\Carbon;
use Illuminate\Http\Request;
use DB;


class SaveController extends ApiController
{


    public function run(Request $request)
    {
        $id = $request->input('id');
        $check_no = $request->input('check_no');
        $warehouse_id = $request->input('warehouse_id');
        $check_user_id = $request->input('check_user_id');
        $status = $request->input('status', 0);
        $number = $request->input('number');
        $remark = $request->input('remark');
        $rules = ['warehouse_id' => 'required'];
        $messages = ['warehouse_id.required'=> '请输入仓库名称'];
        $this->checkParam($request, $rules, $messages);
        if(!empty($id) && isset($id)){
            $obj = StockCheck::query()->find($id);
            $typeStatus=$obj->status;
            if(!$obj){
                $message ='数据不存在！';
                throw  new DiyException($message, ConstFile::API_PARAM_ERROR);
            }
        }else{
            $obj = new StockCheck();
        }
        $obj->check_no = $check_no;
        $obj->warehouse_id = $warehouse_id;
        $obj->check_user_id = $check_user_id;
        $obj->number = $number;
        $obj->remark = $remark;
        $obj->status = $status;
        $obj->created_at = date('Y-m-d H:i:s');
        $obj->updated_at = date('Y-m-d H:i:s');
        DB::beginTransaction();
        if($id){
            $res =  $obj->save();
            $ids = intval($id);
        }else{
            $res =  StockCheck::query()->create($obj->toArray());
            $ids = $res->id;
        }

        $skuInfo = $request->get('allocation_sku_info');
        if(empty($skuInfo)){
            $message ='盘点商品不能为空！';
            throw  new DiyException($message, ConstFile::API_PARAM_ERROR);
        }
        foreach ($skuInfo as $item){
            list($goods_id, $skuId, $number,$profit_loss) = [0,0,0,0];
            extract($item);
            $where['sku_id']=$skuId;
            $where['warehouse_id']=$warehouse_id;
            $current_number = GoodsAllocationGoods::query()->where($where)->groupBy('sku_id')->sum('number');
            $data = [
                'check_id' => $ids,
                'goods_id' => $goods_id,
                'sku_id' => $skuId,
                'profit_loss'=>$profit_loss,
                'number' => $number,
                'current_number'=>$current_number,
                'status' => 1,
                'updated_at' => date('Y-m-d H:i:s'),
            ];
            if(!empty($item['id']) && isset($item['id'])){
                $n = StockCheckGoods::query()->where('id',intval($item['id']))->update($data);
            }else{
                $data['created_at'] =date('Y-m-d H:i:s');
                $n = StockCheckGoods::query()->create($data);
            }
            if($profit_loss!=0 && $status ==1){
                if(!empty($item['list']) && isset($item['list'])){
                    foreach($item['list'] as $val){
                        $allocation_id=intval($val['allocation_id']);
                        $number=intval($val['number']);
                        $where['allocation_id']=$allocation_id;
                        $where['goods_id']=$goods_id;
                        $where['sku_id']=$skuId;
                        $where['warehouse_id']=$warehouse_id;

                        $datas['check_id']=$ids;
                        $datas['allocation_id']=$allocation_id;
                        $datas['number']=$number;
                        $datas['created_at']=date('Y-m-d H:i:s');
                        $datas['updated_at']=date('Y-m-d H:i:s');
                        if($profit_loss>0){
                            $datas['type']=1;//表示加数量
                            $n = GoodsAllocationGoods::where($where)->increment('number',$number);
                        }else{
                            $datas['type']=2;//表示减数量
                            $n = GoodsAllocationGoods::where($where)->decrement('number',$number);
                        }
                        $datasArr[]=$datas;
                    }
                    $n =  DB::table('pas_stock_check_allocation')->insert($datasArr);
                }
            }
        }
        if($n){
            DB::commit();
            return $ids;
        }
        DB::rollBack();
        $message='添加失败';
        throw  new DiyException($message, ConstFile::API_PARAM_ERROR);
    }

    public function initAllocation($warehouseId, $allocationNum, $allocationRowNum, $areaPer){
        $perNum = floor($allocationNum/$allocationRowNum); //一排多少个货位
        for ($i=1; $i<= $allocationNum; $i++){
            $rowNum = ceil($i/$perNum);
            $data= [
                'warehouse_id' => $warehouseId,
                'no' => 'HQ' . $i,
                'row_num' => $rowNum,
                'capacity' => $areaPer
            ] ;
            GoodsAllocation::query()->insert($data);
        }
    }
}
