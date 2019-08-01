<?php

namespace App\Repositories;

use App\Constant\ConstFile;
use App\Models\Workflow\Entry;
use Mockery\Exception;
use Illuminate\Http\Request;
use Auth;
use Hash;
use DB;
use App\Models\User;
use Hprose\Http\Client;


class RpcRepository extends ParentRepository
{
    /**
     * @var UsersRepository
     */
    protected $users;
    protected $userRepository;
    protected $domain;
    protected $client;
    protected $cus_domain;

    public function model()
    {
        return Entry::class;
    }

    public function __construct()
    {
        $this->domain = config('app.rpc_local_domain') . '/hprose/cost/start';
        $this->miss_domain = config('app.rpc_mission_local_domain') . '/hprose/project/start';
        $this->cus_domain=config('app.rpc_cus_local_domain').'/hprose/customer/start';
        $this->userRepository = app()->make(UsersRepository::class);
        $this->client = new Client($this->domain, false);
    }
    //通过预算单id获取预算单信息
    public function getBudgetsById($budget_id)
    {
        return $this->client->getBudgetsById($budget_id);
    }
    //通过用户id获取项目信息
    public function getProjectList($user_id)
    {
        $client= new Client($this->miss_domain, false);
        return $client->getProjectList($user_id);
    }
    //通过项目id获取项目信息
    public function getProjectById($id)
    {
        $client= new Client($this->miss_domain, false);
        return $client->getProjectById($id);
    }

    /*
     * 获取多个项目信息
     * $ids array() 项目id集合
     * */
    public function getMoreProjectInfo($ids){
        $client = new Client($this->miss_domain, false);
        return $client->getProjectByIds($ids);
    }


    //类别
    public function getBudgetsItem($id){
       return $this->client->getBudgetItemById($id);
    }
    //获取客户列表 c_type:销售客户 2：供应商客户
    public function getCustomerListByCompanyId($company_id, $c_type=1){
        $client = new Client($this->cus_domain, false);
        return $client->getCustomerListByCompanyId($company_id,$c_type);
    }
    //通过客户id获取客户名称
    public function getCustomerById($id){
        $client = new Client($this->cus_domain, false);
        return $client->getCustomerById($id);
    }

    /*
     * 获取多个客户、供应商信息
     * $ids array() 客户id集合
     * */
    public function getMoreCustomerInfo($ids){
        $client = new Client($this->cus_domain, false);
        return $client->getCustomerByIds($ids);
    }


    //获取报销价格限制
    public function getPriceByConditions ($data){
        $client = new Client( $this->domain, false);
        return $client->getPriceByConditions($data);
    }


    /**
     * 获取银行账户类型信息
     * @param $type 1 现金账户  2 银行账户
     * @return mixed
     */
    public function getFlowAccount($type_name){
        if($type_name=='现金账户'){
            $type=1;
        }else{
            $type=2;
        }
        $client = new Client( $this->domain, false);
        return $client->getFlowAccount($type);
    }

    //部门财务的账户流水
    public function getDeptAccountState($search){
        $client = new Client( $this->domain, false);
        return $client->getDeptAccountState($search);
    }

    //获取流水分类
    public function getFlowCategory(){
        $client = new Client($this->domain, false);
        return $client->getFlowCategory();
    }
    //通过流水类别id 获取类别名称
    public function getFlowCateName($id){
        $client = new Client($this->domain, false);
        return $client->getFlowCateName($id);
    }


    //通过流水类别ids 获取类别名称
    public function getFlowCateNameByIds($ids){
        $client = new Client($this->domain, false);
        return $client->getFlowCateNameByIds($ids);
    }

    //通过预算单明细id获取分类信息
    public function getCateInfoById($id){
        $client = new Client($this->domain, false);
        return $client->getCateNameByItemId($id);
    }

    /*
    * 通过流水类目id获取获取预算单明细的ids
    */
    public function getItemIdByCateId($id){
        $client = new Client($this->domain, false);
        return $client->getItemIdByCateId($id);
    }

    /**
     * 获取分账列表
     * @param $financial_id
     */
    public function getRecordList($financial_id){
        $client = new Client($this->domain, false);
        return $client->getRecordList($financial_id);
    }


}
