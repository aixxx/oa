<?php

namespace App\Http\Controllers\Api\V1\PAS\Warehouse\Plan;

use App\Exceptions\DiyException;
use App\Http\Controllers\Api\V1\ApiController;
use App\Models\PAS\Purchase\WarehousingApply;
use App\Models\PAS\Warehouse\GoodsFlow;
use App\Models\PAS\Warehouse\WarehouseInCard;
use App\Models\PAS\Warehouse\WarehouseInGoods;
use App\Repositories\RpcRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;


class CreateController extends ApiController
{


    public function run(Request $request)
    {
        $apply_id = $request->get('apply_id');  //申请单ID
        $type = $request->get('type', WarehouseInCard::TYPE_BUY);
        $res = [];
        switch ($type){
            case WarehouseInCard::TYPE_BUY:
                $res = $this->buyShow($apply_id);
                break;
            case WarehouseInCard::TYPE_BACK:
                $res = $this->backShow($apply_id);
                break;
            case WarehouseInCard::TYPE_ALLOT:
            default:
                //采购单
                $res = $this->buyShow($apply_id);
                break;
        }
        $res['in_no'] = 'IN' . getCode();
        return $res;
    }

    /**
     * 仓库未安排 --详情
     * @param WarehousingApply  $obj
     * @return mixed
     */
    public function createBuyHouseNo($obj)
    {
        $apply_goods = $obj->applyGoods;
        $arr = [];
        $cntArr = [];
        foreach ($apply_goods as $apply_good) {
            if (empty($apply_good)) continue;
            $skuObj = $apply_good->sku;
            if (empty($skuObj)) continue;
            $skuObj->diff_num = $skuObj->number - $skuObj->r_number;
            $apply_good->sku = $skuObj;
            $i = isset($cntArr[$apply_good->sku_id]) ? $cntArr[$apply_good->sku_id] : 0;
            $cntArr[$apply_good->sku_id] = $i + $apply_good->sku->diff_num;

            $apply_good->sku->diff_num = $cntArr[$apply_good->sku_id];
            $apply_good->sku->in_num = $cntArr[$apply_good->sku_id];
            $apply_good->sku->number = $cntArr[$apply_good->sku_id];
            $arr[$apply_good->sku_id] = $apply_good;
        }
        return array_values($arr);
    }

    /**
     * 仓库已安排 --详情
     * @param $inCardObj
     * @return array
     * @internal param int $apply_id
     * @internal param WarehouseInCard $inCardObj 仓库安排表
     * @internal param WarehousingApply $obj
     */
    public function createBuyHouseYes($inCardObj)
    {
        $inCardGoods = $inCardObj->inCardGoods;
        $resArr = [];
        foreach ($inCardGoods as $key => $cardGood) {
            if (empty($cardGood->warehouse_id) || empty($cardGood->warehouse)) continue;
            $resArr[$cardGood->warehouse_id]['warehouse'] = $cardGood->warehouse->title;
            $resArr[$cardGood->warehouse_id]['warehouse_id'] = $cardGood->warehouse_id;

            /** @var WarehouseInGoods $cardGood */
            $item = new \stdClass();
            $item->sku_id = $cardGood->sku_id;
            $item->goods_name = empty($cardGood->goodsInfo) ? '' : $cardGood->goodsInfo->goods_name;
            $item->goods_sn = empty($cardGood->goodsInfo) ? '' : $cardGood->goodsInfo->goods_sn;
            $item->diff_num = $cardGood->in_num; //- $cardGood->stored_num;
            $item->goods_id = empty($cardGood->goodsInfo) ? '' : $cardGood->goodsInfo->goods_id;
            //绕圈圈
            $item->sku_name = empty($cardGood->skuInfo) ? '' : $cardGood->skuInfo->key_name;// empty($cardGood->goodsInfo) ||
            $resArr[$cardGood->warehouse_id]['goods'][] = $item;
        }
        $res = array_values($resArr);
        return $res;
    }

    /**
     * 部分入库 -- 详情
     * @param WarehousingApply $obj
     * @return array []
     */
    public function createBuyPart($obj)
    {
        $applyId  = $obj->id;
        $inCardObjs = WarehouseInCard::query()
            ->with(['inCardGoods', 'inCardGoods.goodsInfo','inCardGoods.skuInfo'])
            ->where('apply_id', '=', $applyId)
            ->where('type', '=', 1)
            ->get();
        $res = [];
        foreach ($inCardObjs as $k => $inCardObj) {

            $inCardGoods = $inCardObj->inCardGoods;
            $resArr = [];
            foreach ($inCardGoods as $key => $cardGood) {
                if (empty($cardGood->warehouse_id) || empty($cardGood->warehouse)) continue;
                $resArr[$cardGood->warehouse_id]['warehouse'] = $cardGood->warehouse->title;
                $resArr[$cardGood->warehouse_id]['warehouse_id'] = $cardGood->warehouse_id;

                $goodsFlows = GoodsFlow::query()
                    ->where('plan_id', '=', $inCardObj->id)
                    ->where('warehouse_id', '=', $cardGood->warehouse_id)
                    ->with(['allocation'])
                    ->get();
                $allocationArr = [];
                foreach ($goodsFlows as $goodsFlow){
                    $allocation = $goodsFlow->allocation;
                    $allocationArr[] = $allocation->no;
                }
                $resArr[$cardGood->warehouse_id]['allocations'] = implode(';', $allocationArr);

                /** @var WarehouseInGoods $cardGood */
                $item = new \stdClass();
                $item->sku_id = $cardGood->sku_id;
                $item->goods_name = empty($cardGood->goodsInfo) ? '' : $cardGood->goodsInfo->goods_name;
                $item->goods_sn = empty($cardGood->goodsInfo) ? '' : $cardGood->goodsInfo->goods_sn;
                $item->in_num = $cardGood->in_num;
                $item->stored_num = $cardGood->stored_num;
                $item->diff_num = intval($cardGood->in_num) - intval($cardGood->stored_num);
                $item->goods_id = empty($cardGood->goodsInfo) ? '' : $cardGood->goodsInfo->goods_id;
                //绕圈圈
                $item->sku_name = empty($cardGood->skuInfo) ? '' : $cardGood->skuInfo->key_name;// empty($cardGood->goodsInfo) ||
                $resArr[$cardGood->warehouse_id]['goods'][] = $item;
            }
            $res = array_merge(array_values($resArr), $res);
        }
        return $res;
    }

    /**
     * 已入库 -- 详情
     * @param WarehousingApply $obj
     * @return array
     */
    public function createBuyOk($obj)
    {
        $applyId  = $obj->id;
        $inCardObjs = WarehouseInCard::query()
            ->with(['inCardGoods', 'inCardGoods.goodsInfo','inCardGoods.skuInfo'])
            ->where('apply_id', '=', $applyId)
            ->where('type', '=', 1)
            ->get();
        $res = [];
        foreach ($inCardObjs as $k => $inCardObj){
            $inCardGoods = $inCardObj->inCardGoods;
            $resArr = [];
            foreach ($inCardGoods as  $key => $cardGood){
                if(empty($cardGood->warehouse_id) || empty($cardGood->warehouse)) continue;
                $resArr[$cardGood->warehouse_id]['warehouse'] = $cardGood->warehouse->title;
                $resArr[$cardGood->warehouse_id]['warehouse_id'] = $cardGood->warehouse_id;

                $goodsFlows = GoodsFlow::query()
                    ->where('plan_id', '=', $inCardObj->id)
                    ->where('warehouse_id', '=', $cardGood->warehouse_id)
                    ->with(['allocation'])
                    ->get();
                $allocationArr = [];
                foreach ($goodsFlows as $goodsFlow){
                    $allocation = $goodsFlow->allocation;
                    $allocationArr[] = $allocation->no;
                }
                $resArr[$cardGood->warehouse_id]['allocations'] = implode(';', $allocationArr);

                /** @var WarehouseInGoods $cardGood */
                $item = new \stdClass();
                $item->sku_id = $cardGood->sku_id;
                $item->goods_name = empty($cardGood->goodsInfo) ? '' : $cardGood->goodsInfo->goods_name;
                $item->goods_sn = empty($cardGood->goodsInfo) ? '' : $cardGood->goodsInfo->goods_sn;
                $item->diff_num = $cardGood->in_num; //- $cardGood->stored_num;
                $item->goods_id = empty($cardGood->goodsInfo) ? '' : $cardGood->goodsInfo->goods_id;
                //绕圈圈
                $item->sku_name = empty($cardGood->skuInfo) ? '' : $cardGood->skuInfo->key_name;// empty($cardGood->goodsInfo) ||
                $resArr[$cardGood->warehouse_id]['goods'][] = $item;
            }
            $res = array_merge(array_values($resArr), $res);
        }
        return $res;
    }

    /**
     * @param $apply_id
     * @return WarehousingApply|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|null|static|static[]
     */
    public function buyShow($apply_id)
    {
        $res = [];
        $obj = WarehousingApply::query()->with(['applyGoods'])->find($apply_id);
        if(empty($obj)) return $obj;
        $res['id'] = $obj->id;
        $res['apply_sn'] = $obj->p_code;
        $res['status'] = $obj->status;
        $res['status_str'] = isset(WarehousingApply::$_status[$obj->status]) ?? WarehousingApply::$_status[$obj->status];

        $res['supplier_name'] = $obj->supplier_name;
//        $infoArr=app()->make(RpcRepository::class)->getCustomerById(111);
        $res['contact'] = '';
        $res['cargo_user_id'] = empty($obj->in_card_info) ?? $obj->in_card_info->cargo_user_id;
        $res['business_date'] = $obj->business_date;
        $res['apply_id'] = $obj->apply_id;
        $res['apply_name'] = $obj->apply_name;
        $res['remarks'] = $obj->remarks;
        switch ($obj->status){
            case WarehousingApply::STATUS_HOUSE_NO:
                $res['goods'] = $this->createBuyHouseNo($obj);
                break;
            case WarehousingApply::STATUS_HOUSE_YES:
                $inCardObj = WarehouseInCard::query()
                    ->with(['inCardGoods', 'inCardGoods.goodsInfo','inCardGoods.skuInfo', 'inCardGoods.warehouse'])
                    ->where('apply_id', '=', $apply_id)
                    ->where('type', '=', 1)
                    ->orderBy('id', 'desc')
                    ->first();
                $res['plan_id'] = $inCardObj->id;
                $res['goods'] = $this->createBuyHouseYes($inCardObj);
                break;
            case WarehousingApply::STATUS_PART:
                $res['goods'] = $this->createBuyPart($obj);
                break;
            case WarehousingApply::STATUS_OK:
            default:
            $res['goods'] = $this->createBuyOk($obj);
                break;
        }

        return $res;
    }

    public function backShow($apply_id)
    {
        $obj = WarehousingApply::query()->with(['applyGoods'])->find($apply_id);
        if(empty($obj)) return $obj;

        switch ($obj->status){
            case WarehousingApply::STATUS_HOUSE_NO:
                $obj = $this->createBuyHouseNo($obj);
                break;
            case WarehousingApply::STATUS_HOUSE_YES:
                $obj = $this->createBuyHouseYes($obj, null);
                break;
            case WarehousingApply::STATUS_PART:
                $obj = $this->createBuyPart($obj);
                break;
            case WarehousingApply::STATUS_OK:
            default:
                $obj = $this->createBuyOk($obj);
                break;
        }
        return $obj;
    }
}
