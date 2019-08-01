<?php

namespace App\Http\Controllers\Rpc;
use App\Models\Investment;
use App\Models\TransactionLog;
use App\Models\User;
use App\UserAccount\Account;
use App\UserAccount\AccountLog;
use App\UserAccount\TransactionLogs;
use Hprose\Http\Client;
use App\Http\Controllers\Controller;
class TestController extends Controller
{

    public function index($id = 0) {


        $domain=config('app.rpc_domain').'/service';
        //$domain=config('app.rpc_local_domain') . '/hprose/cost/start';
        $client = new Client($domain, false);

        $search=[
            'selectdepts'=>[1,59,61]
        ];
        dd($client->accountReceivablePayable($search));
        dd($client->getRecordList($financial_id=371));
        //dd($client->getPicInfo($finance_ids=[]);
        dd($client->changeStatusByIds([
            ['financial_id'=>'382','status'=>8],
            ['financial_id'=>'384','sum_money'=>150],
            ['financial_id'=>'383','status'=>8]
        ]));

        $data=[
            'dept_id'=>'283',//组织id
            'flow_id'=>'35',
            'status'=>3,
            'create_begin'=>'2019-05-01',
            'create_end'=>'2019-05-21',
            'page'=>1
        ];

        $tables1 = [
            "user_id" => 1791,
            "department_id" => 1,
            "outer_id" => 1,
            "model_name"=> 'fina',
            "amount"=> '500',
            "category"=> '1',
            "type"=> '2',
            "is_bill"=> '1',
            "is_jysr"=> '1',
            "status_start_time"=> 'Y-m-d H:i:s',
            "is_more_department"=> '1',
        ];
        AccountLog::PerInsert($tables1);
        die;
        /*$t = new TransactionLogs();
        print_r($t->insert(
            [
                "user_id" => 1791,
                "department_id" => 2,
                "outer_id" => 2,
                "model_name"=> '模型，app/models/文件名',
                "amount"=> 10000,
                "category"=> 1,
                "type"=> 1,
                "is_bill"=> 1,
                "is_jysr"=> 1,
                "is_more_department"=> 1,
            ]
        ));
        die;*/


    }


    public function test() {
        //dd(111);
        /**
         * 客户相关的操作需要传送一个文字过来
         * 例如， 客户生成订单xxx
         * @author Ringo
         * @param int $customer_id = 0
         * @param string $log = "审核通过"
         */
        //$client = new Client('http://customer.liyunnetwork.com/hprose/customer/start', false);
        //$client->createWorklog($customer_id = 10, $log = '审核通过')
        $domain=config('app.rpc_domain').'/service';
        //$domain=config('app.rpc_local_domain') . '/hprose/cost/start';

        //$domain=config('app.rpc_local_domain') . '/hprose/cost/start';
       // $domain= config('app.rpc_mission_local_domain') . '/hprose/project/start';
        $client = new Client($domain, false);
        //dd($client->getRecordList($financial_id=376));
        $data=[
            'dept_id'=>'1',//组织id
            'flow_id'=>'43',
            'status'=>0,
            'create_begin'=>'2019-01-26',
            'create_end'=>'2019-06-26',
            'page'=>1
        ];
        $search=[
            'is_join'=>1,
            'selectdepts'=>1
            ];
        dd($client->accountReceivablePayable($search));
        $search=[
            'deptIds'=>[1,2],
        ];


       // dd($client->nowTime($uid=1861));
       //dd($client->getPriceByConditions ($data));
       // dd($client->getUsersRole($company_id='64',55));


        /**
         * 例如， 客户ids 获取 客户信息
         * @author Ringo
         * @param array $ids
         */
        $client = new Client('http://customer.liyunnetwork.com/hprose/customer/start', false);
        $client->getCustomerByIds([1,2]);


        $client = new Client('http://erpcaiwu.yuns.net:9999/hprose/cost/start', false);
        $client->insertSupplier(['']);
    }
}
