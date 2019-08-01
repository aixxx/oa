<?php

namespace App\Http\Controllers\Api\V1\PAS;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\PAS\SaleOrderRepository;
use App\Constant\ConstFile;
use Auth;
use Hprose\Http\Client;

class SaleOrderController extends Controller
{
    public $goods;
    public function __construct(SaleOrderRepository $goods)
    {
        //$user = Auth::user();
        $this->goods = $goods;
    }

    /*
     * 添加修改销售发货单
     * */
    public function editSaleInvoice(Request $request){
        return $this->goods->editSaleInvoice($request->all());
    }

    /*
     * 获取用户发货方式
     * */
    public function getUserAddress(Request $request){
        return $this->goods->getUserAddress($request->all());
    }


    /*
     * 销售发货单详情
     * */
    public function saleInvoiceInfo(Request $request){
        $user = Auth::user();
        return $this->goods->saleInvoiceInfo($request->all(), $user);
    }

    /*
     * 添加修改销售单
     * */
    public function editSaleOrder(Request $request){
        $user = Auth::user();
        return $this->goods->editSaleOrder($request->all(), $user);
    }

    /*
     * 修改销售单购买的商品
     * */
    public function changeBuyGoods(Request $request){
        return $this->goods->changeBuyGoods($request->all());
    }

    /*
     * 销售单详情
     * */
    public function saleOrderInfo(Request $request){
        $user = Auth::user();
        return $this->goods->saleOrderInfo($request->all(), $user);
    }

    /*
     * 取消销售单
     * */
    public function cancelSaleOrder(Request $request){
        $user = Auth::user();
        return $this->goods->cancelSaleOrder($request->all(), $user);
    }

    /*
     * 销售单列表
     * */
    public function saleOrderList(Request $request){
        return $this->goods->saleOrderList($request->all());
    }

    /*
     * 出库申请
     * */
    public function saleOutOfWarehouse(Request $request){
        $user = Auth::user();
        return $this->goods->saleOutOfWarehouse($request->all(), $user);
    }

    /*
     * 出库申请单详情
     * */
    public function saleOutWarehouseInfo(Request $request){
        $user = Auth::user();
        return $this->goods->saleOutWarehouseInfo($request->all(), $user);
    }

    /*
     * 销售出库单列表
     * */
    public function saleOutList(Request $request){
        $user = Auth::user();
        return $this->goods->saleOutList($request->all(), $user);
    }

    /*
     * 撤销出库申请单
     * */
    public function cancelSaleOutWarehouse(Request $request){
        $user = Auth::user();
        return $this->goods->cancelSaleOutWarehouse($request->all(), $user);
    }

    /*
     * 添加修改退货单
     * */
    public function editReturnOrder(Request $request){
        $user = Auth::user();
        return $this->goods->editReturnOrder($request->all(), $user);
    }

    /*
     * 销售退货单详情
     * */
    public function saleReturnOrderDetail(Request $request){
        $user = Auth::user();
        return $this->goods->saleReturnOrderDetail($request->all(), $user);
    }

    /*
     * 撤销退货单
     * */
    public function cancelSaleReturnOrder(Request $request){
        $user = Auth::user();
        return $this->goods->cancelSaleReturnOrder($request->all(), $user);
    }

    /*
     * 退货单列表
     * */
    public function saleReturnOrderList(Request $request){
        return $this->goods->saleReturnOrderList($request->all());
    }

    /*
     * 修改销售退货入库单详情
     * */
    /*public function editSaleReturnOrderInDetail(Request $request){
        return $this->goods->editSaleReturnOrderInDetail($request->all());
    }*/

    /*
     * 退货单入库申请
     * */
    public function returnInWarehouse(Request $request){
        $user = Auth::user();
        return $this->goods->returnInWarehouse($request->all(), $user);
    }

    /*
     * 入库申请单详情
     * */
    public function returnInWarehouseDetail(Request $request){
        $user = Auth::user();
        return $this->goods->returnInWarehouseDetail($request->all(), $user);
    }

    /*
     * 取消退货入库单
     * */
    public function cancelSaleInWarehouse(Request $request){
        $user = Auth::user();
        return $this->goods->cancelSaleInWarehouse($request->all(), $user);
    }

    /*
     * 入库列表
     * */
    public function saleReturnInList(Request $request){
        $user = Auth::user();
        return $this->goods->saleReturnInList($request->all(), $user);
    }

    /*
     * 客户列表
     * */
    public function getCustomerList(){
        $client = new Client(config('app.customer_url'), false);
        $list = $client->getCustomerListByCompanyId(64,1);
        return returnJson(ConstFile::API_RESPONSE_SUCCESS_MESSAGE,ConstFile::API_RESPONSE_SUCCESS, $list);
    }

    /*
     * 客户详情
     * */
    public function getCustomerDetail(Request $request){
        $param = $request->all();
        if(empty($param['id'])){
            return returnJson('参数错误：请选择客户',ConstFile::API_RESPONSE_FAIL);
        }

        $client = new Client(config('app.customer_url'), false);
        $list = $client->getCustomerById($param['id']);
        return returnJson(ConstFile::API_RESPONSE_SUCCESS_MESSAGE,ConstFile::API_RESPONSE_SUCCESS, $list);
    }

}
