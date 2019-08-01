<?php
/**
 * 财务管理后台
 * Finance类
 * lee 2019-4-15
 */

namespace App\Http\Controllers\Customer;

use Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Response;
use Hprose\Http\Client;
use Illuminate\Pagination\LengthAwarePaginator;


class CustomerController extends Controller
{
    public $page_size = 30; //分页数量

    public function __construct()
    {
        //rpc调用
        $domain = config('app.rpc_cus_local_domain').'/hprose/sys/start';
        $this->client = new Client($domain, false);
    }

    //分页类
    private function getShowPage(Request $request, $array_items)
    {
        // Get current page form url e.x. &page=1
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        // Create a new Laravel collection from the array data
        $itemCollection = collect($array_items);
        // Define how many items we want to be visible in each page
        $perPage = $this->page_size;
        // Slice the collection to get the items to display in current page
        $currentPageItems = $itemCollection->slice(($currentPage * $perPage) - $perPage, $perPage)->all();
        // Create our paginator and pass it to the view
        $paginatedItems = new LengthAwarePaginator($currentPageItems , count($itemCollection), $perPage);
        $paginatedItems->setPath($request->url());
        return $paginatedItems;
    }

    //客户标签列表
    public function customerType()
    {
//        $types = $this->client->getCustomerList(64);
        $types = $this->client->customerType();
        return view('customer.customerType.index',compact('types'));
    }

    //客户分类添加
    public function customerTypeAdd()
    {
        return view('customer.customerType.create');
    }

    //客户标签编辑
    public function customerTypeEdit(Request $request)
    {
        $id = $request->get('id');
        $types = $this->client->getOneCustomerType($id);
        return view('customer.customerType.create',compact('types'));
    }

    //客户标签保存
    public function customerTypeStore(Request $request)
    {
        $data = $request->all();
        $type_data = [
            'type_name' => $data['type_name'],
            'discount' => $data['discount'],
            'payment_day' => $data['payment_day'],
            'orderby' => $data['orderby'],
            'created_time' => time(),
        ];
        if(!$data['type_name'] || !$data['discount'] || !$data['payment_day'] || !$data['orderby']){
            return Response::json(['code' => 201, 'message' => '全是必填项哦']);
        }

        if(!preg_match("/^[1-9][0-9]*$/",$data['discount']))
            return Response::json(['code' => 202, 'message' => '折扣率为正整数']);

        if(!preg_match("/^[1-9][0-9]*$/",$data['payment_day']))
            return Response::json(['code' => 202, 'message' => '付款期限为正整数']);

        if(!preg_match("/^[1-9][0-9]*$/",$data['orderby']))
            return Response::json(['code' => 202, 'message' => '排序为正整数']);

        if(isset($data['id']) && $data['id']) {
            $this->client->saveCustomerType($type_data, $data['id']);
            return Response::json(['code' => 200, 'message' => '编辑客户标签成功']);
        }
        $flag = $this->client->saveCustomerType($type_data);
        if(!$flag) {
            return Response::json(['code' => 203, 'message' => '保存客户标签失败']);
        }
        return Response::json(['code' => 200, 'message' => '保存客户标签成功']);
    }

    //客户标签删除
    public function customerTypeDel(Request $request)
    {
        $id = $request->get('id');

        $types = $this->client->getOneCustomerType($id);

        if(!$types)
            return Response::json(['code' => 201, 'message' => '客户标签数据出错']);

        $is_customer = $this->client->isCustomerByType($id);
        if($is_customer)
            return Response::json(['code' => 203, 'message' => '客户标签已被使用，不能删除']);

        $flag = $this->client->saveCustomerType(['status' => 0],$id);

        if(!$flag)
            return  Response::json(['code' => 202, 'message' => '删除失败，请稍微再试']);

        return Response::json(['code' => 200, 'message' => '删除成功']);
    }

    //公海设置
    public function seasPublic()
    {
        $settings = $this->client->getCustomerSettings();
        return view('customer.seasPublic.index',compact('settings'));
    }

    //公海设置添加或修改
    public function seasPublicStore(Request $request)
    {
        $data = $request->all();
        $this->user = Auth::user()->toArray();
        $type_data = [
            'over_contact' => $data['over_contact'],
            'company_id'  => $this->user['company_id'],
        ];
        if(!$data['over_contact']){
            return Response::json(['code' => 201, 'message' => '是必填项哦']);
        }
        if(!preg_match("/^[1-9][0-9]*$/",$data['over_contact']))
            return Response::json(['code' => 204, 'message' => '天数为正整数']);

        if(isset($data['id']) && $data['id']) {
            $this->client->saveCustomerSettings($type_data, $data['id']);
            return Response::json(['code' => 200, 'message' => '设置成功']);
        }
        $flag = $this->client->saveCustomerSettings($type_data);
        if(!$flag) {
            return Response::json(['code' => 203, 'message' => '设置失败']);
        }
        return Response::json(['code' => 200, 'message' => '设置成功']);
    }

    //防撞单设置
    public function avoidance()
    {
        $settings = $this->client->getCustomerSettings();
        return view('customer.avoidance.index',compact('settings'));
    }

    //防撞单设置添加或修改
    public function avoidanceStore(Request $request)
    {
        $data = $request->all();
        $this->user = Auth::user()->toArray();
        $type_data = [
            'avoidance' => $data['avoidance'],
            'filled' => $data['filled'],
            'company_id'  => $this->user['company_id'],
        ];

        if(!in_array($data['avoidance'],[0,1]) || !in_array($data['filled'],[0,1])){
            return Response::json(['code' => 201, 'message' => '数据出错']);
        }

        if(isset($data['id']) && $data['id']) {
            $this->client->saveCustomerSettings($type_data, $data['id']);
            return Response::json(['code' => 200, 'message' => '设置成功']);
        }
        $flag = $this->client->saveCustomerSettings($type_data);
        if(!$flag) {
            return Response::json(['code' => 203, 'message' => '设置失败']);
        }
        return Response::json(['code' => 200, 'message' => '设置成功']);
    }

}