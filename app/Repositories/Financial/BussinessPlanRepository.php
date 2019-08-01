<?php
/**
 * Created by yyp.
 * User: yyp
 * Date: 2019/4/17
 * Time: 17:32
 */

namespace App\Repositories\Financial;

use App\Constant\ConstFile;
use App\Models\Department;
use App\Models\DepartUser;
use App\Models\Financial;
use App\Models\Financial\BussinessCategoryPlan;
use App\Models\Financial\BussinessPlan;
use App\Models\FinancialDetail;
use App\Repositories\FinanceLogRepository;
use App\Repositories\Repository;
use App\Repositories\RpcRepository;
use Illuminate\Support\Facades\DB;
use Exception;
use Carbon\Carbon;

class BussinessPlanRepository extends Repository
{
    const YES = 1;
    const NO = 0;

    private $user_department_id_ = 0;//主部门id
    private $user_department_name_ = '';//主部门名称
    public $limit; //默认分页展示数量

    private $unit_type_ = [
        '客户' => 1,
        '供应商' => 2,
        '内部单位' => 3
    ];

    public function __construct(){
        $this->limit = 10;
    }

    /*
     * 获取用户主部门id和名称
     * */
    public function setUserDepartment($user_id, $type = 0){
        $res = DepartUser::where(['is_primary'=> 1, 'user_id'=> $user_id])->with('getPrimaryDepartmentA')->first();
        if($type){
            //返回主部门信息
            return ['department_id'=>$res->getPrimaryDepartmentA->id, 'department_name'=>$res->getPrimaryDepartmentA->name];
        }else{
            //初始化用户主部门信息
            $this->user_department_id_ = $res->getPrimaryDepartmentA->id;
            $this->user_department_name_ = $res->getPrimaryDepartmentA->name;
        }
    }


    /*
     * 添加修改计划
     * */
    public function editBussinessPlan($user, $param){
        try{
            $error = $this->checkBussinessPlan($param);
            if($error){
                return returnJson($error, ConstFile::API_RESPONSE_FAIL);
            }
            $this->setUserDepartment($user->id);
            $data = [
                'title' => $param['title'],
                'month' => $param['month'],
                'user_id' => $user->id //换了部门后，其它有此权限的人可以修改
            ];
            $id= 0;
            if(!empty($param['id'])){
                //修改
                $info = BussinessPlan::where(['id' => $param['id'], 'department_id'=>$this->user_department_id_])->first();
                if(empty($info)){
                    return returnJson(ConstFile::API_RECORDS_NOT_EXIST_MESSAGE, ConstFile::API_RESPONSE_FAIL);
                }
                $data['updated_at'] = date('Y-m-d H:i:s', time());
                $res = BussinessPlan::where('id', $param['id'])->update($data);
                $id = $param['id'];
            }else{
                //添加
                $data['department_id'] = $this->user_department_id_;
                $data['company_id'] = $user->company_id;
                $res = BussinessPlan::create($data);
                $res && $id = $res->id;
            }
            if($res){
                return returnJson(ConstFile::API_RESPONSE_SUCCESS_MESSAGE, ConstFile::API_RESPONSE_SUCCESS, ['id'=>$id]);
            }else{
                return returnJson(ConstFile::API_RESPONSE_FAIL_MESSAGE, ConstFile::API_RESPONSE_FAIL);
            }
        }catch (Exception $e){
            return returnJson($e->getMessage(), $e->getCode());
        }
    }


    /*
     * 获取修改数据
     * */
    public function editPlanInfo($user, $param){
        try{
            if(empty($param['id'])){
                return returnJson('请选择计划', ConstFile::API_RESPONSE_FAIL);
            }
            $this->setUserDepartment($user->id);
            $info = BussinessPlan::where(['id' => $param['id'], 'department_id'=>$this->user_department_id_])->get(['id', 'user_id', 'title', 'month'])->first();//同一个部门有权限的人可以修改和查看（与换部门对应）
            if(empty($info)){
                return returnJson(ConstFile::API_RECORDS_NOT_EXIST_MESSAGE, ConstFile::API_RESPONSE_FAIL);
            }
            $info->department_name = $this->user_department_name_;

            return returnJson(ConstFile::API_RESPONSE_SUCCESS_MESSAGE_DATA,ConstFile::API_RESPONSE_SUCCESS,$info);
        }catch (Exception $e){
            return returnJson($e->getMessage(), $e->getCode());
        }
    }


    /*
     * 计划列表
     * */
    public function planList($user, $param){
        try{
            $this->setUserDepartment($user->id);
            $list = BussinessPlan::where(['department_id'=>$this->user_department_id_])->select(['id', 'user_id', 'title', 'month', 'updated_at'])->orderBy('created_at', 'DESC')->paginate($param['limit'])->toArray();

            if(!empty($list['data'])){
                foreach ($list['data'] as &$v){
                    $v['department_name'] = $this->user_department_name_;
                }
            }

            $result['total'] = $list['total'];
            $result['total_page'] = $list['last_page'];
            $result['data'] = $list['data'];

            return returnJson(ConstFile::API_RESPONSE_SUCCESS_MESSAGE_DATA,ConstFile::API_RESPONSE_SUCCESS,$list);
        }catch (Exception $e){
            return returnJson($e->getMessage(), $e->getCode());
        }
    }


    /*
     * 添加修改类目计划
     * */
    public function editCategoryPlan($user, $param){
        try{
            $error = $this->checkCategoryPlan($param, $user);
            if($error['status'] == self::NO){
                return returnJson($error['msg'], ConstFile::API_RESPONSE_FAIL);
            }
            $this->setUserDepartment($user->id);//初始化用户部门id和名称
            $data = $error['data'];
            $id= 0;
            if(!empty($param['id'])){
                //修改
                unset($data['id']);
                $info = BussinessCategoryPlan::where(['id' => $param['id'], 'department_id'=>$this->user_department_id_])->first();
                if(empty($info)){
                    return returnJson(ConstFile::API_RECORDS_NOT_EXIST_MESSAGE, ConstFile::API_RESPONSE_FAIL);
                }
                $data['updated_at'] = date('Y-m-d H:i:s', time());
                $res = BussinessCategoryPlan::where('id', $param['id'])->update($data);
                $id = $param['id'];
            }else{
                //添加
                $data['department_id'] = $this->user_department_id_;
                $data['company_id'] = $user->company_id;
                $res = BussinessCategoryPlan::create($data);
                $res && $id = $res->id; 
            }
            if($res){
                return returnJson(ConstFile::API_RESPONSE_SUCCESS_MESSAGE, ConstFile::API_RESPONSE_SUCCESS, ['id'=>$id]);
            }else{
                return returnJson(ConstFile::API_RESPONSE_FAIL_MESSAGE, ConstFile::API_RESPONSE_FAIL);
            }
        }catch (Exception $e){
            return returnJson($e->getMessage(), $e->getCode());
        }
    }


    /*
     * 类目计划列表
     * */
    public function categoryPlanList($user, $param){
        try{
            if(empty($param['id'])){
                return returnJson('请选择计划', ConstFile::API_RESPONSE_FAIL);
            }
            $this->setUserDepartment($user->id);
            $list = BussinessCategoryPlan::where(['department_id'=>$this->user_department_id_, 'plan_id'=>$param['id']])->select(['id', 'user_id', 'plan_id', 'category_id', 'content', 'money', 'unit_type', 'unit', 'program_id'])->orderBy('created_at', 'DESC')->paginate($param['limit'])->toArray();

            $data = $list['data'];
            if(!empty($data)){
                $rpc = new RpcRepository();
                $category_ids = array_unique(array_filter(array_column($data, 'category_id')));
                $program_ids = array_unique(array_filter(array_column($data, 'program_id')));
                $category_names = $rpc->getFlowCateNameByIds($category_ids);//类目信息
                $category_names = array_field_as_key($category_names, 'id');

                $department_ids = $customer_ids = $department_names = $project_names = [];
                foreach($data as $v){
                    if($v['unit_type']){
                        if($this->unit_type_[$v['unit_type']] == 3){
                            //单位
                            $department_ids[] = $v['unit'];
                        }else{
                            //客户、供应商
                            $customer_ids[] = $v['unit'];
                        }
                    }
                }

                if($customer_ids){
                    $customer_names = $rpc->getMoreCustomerInfo($customer_ids);
                    $customer_names = array_field_as_key($customer_names, 'id');
                }
                if(!empty($department_ids)){
                    $department_names = Department::whereIn('id', $department_ids)->pluck('name', 'id')->toArray();
                }
                if(!empty($program_ids)){
                    $project_names = $rpc->getMoreProjectInfo($program_ids);//项目信息
                    $project_names = array_field_as_key($project_names, 'id');
                }
                foreach ($data as &$v){
                    $v['category_name'] = !empty($category_names[$v['category_id']]['name']) ? $category_names[$v['category_id']]['name'] : '';
                    $v['program_name'] = !empty($project_names[$v['program_id']]['title']) ? $project_names[$v['program_id']]['title'] : '';
                    $v['unit_name'] = !empty($v['unit_type']) ? ($this->unit_type_[$v['unit_type']] == 3 ? (!empty($department_names[$v['unit']]) ? $department_names[$v['unit']] : '') : (!empty($customer_names[$v['unit']]['cusname']) ? $customer_names[$v['unit']]['cusname'] : '')) : '';
                }
            }

            $result['total'] = $list['total'];
            $result['total_page'] = $list['last_page'];
            $result['data'] = $data;
            return returnJson(ConstFile::API_RESPONSE_SUCCESS_MESSAGE_DATA,ConstFile::API_RESPONSE_SUCCESS,$result);
        }catch (Exception $e){
            return returnJson($e->getMessage(), $e->getCode());
        }
    }

    /*
     * 类目计划列表
     * */
    public function getCategoryPlanList($user, $param){
        try{
            if(empty($param['category_id'])){
                return returnJson('请选择类目', ConstFile::API_RESPONSE_FAIL);
            }
            if(!empty($param['limit']) && isset($param['limit'])){
                $limit = $param['limit'];
            }else{
                $limit = 10;
            }
            $this->setUserDepartment($user->id);
            $list = BussinessCategoryPlan::where(['department_id'=>$this->user_department_id_, 'category_id'=>$param['category_id']])->select(['id', 'user_id', 'plan_id', 'category_id', 'content', 'money', 'unit_type', 'unit', 'program_id'])->orderBy('created_at', 'DESC')->paginate($limit)->toArray();
            $data = $list['data'];
            if(!empty($data)){
                $rpc = new RpcRepository();
                $category_ids = array_unique(array_filter(array_column($data, 'category_id')));
                $program_ids = array_unique(array_filter(array_column($data, 'program_id')));
                $category_names = $rpc->getFlowCateNameByIds($category_ids);//类目信息
                $category_names = array_field_as_key($category_names, 'id');

                $department_ids = $customer_ids = $department_names = $project_names = [];
                foreach($data as $v){
                    if($v['unit_type']){
                        if($this->unit_type_[$v['unit_type']] == 3){
                            //单位
                            $department_ids[] = $v['unit'];
                        }else{
                            //客户、供应商
                            $customer_ids[] = $v['unit'];
                        }
                    }
                }

                if($customer_ids){
                    $customer_names = $rpc->getMoreCustomerInfo($customer_ids);
                    $customer_names = array_field_as_key($customer_names, 'id');
                }
                if(!empty($department_ids)){
                    $department_names = Department::whereIn('id', $department_ids)->pluck('name', 'id')->toArray();
                }
                if(!empty($program_ids)){
                    $project_names = $rpc->getMoreProjectInfo($program_ids);//项目信息
                    $project_names = array_field_as_key($project_names, 'id');
                }
                foreach ($data as &$v){
                    $v['category_name'] = !empty($category_names[$v['category_id']]['name']) ? $category_names[$v['category_id']]['name'] : '';
                    $v['program_name'] = !empty($project_names[$v['program_id']]['title']) ? $project_names[$v['program_id']]['title'] : '';
                    $v['unit_name'] = !empty($v['unit_type']) ? ($this->unit_type_[$v['unit_type']] == 3 ? (!empty($department_names[$v['unit']]) ? $department_names[$v['unit']] : '') : (!empty($customer_names[$v['unit']]['cusname']) ? $customer_names[$v['unit']]['cusname'] : '')) : '';
                }
            }

            $result['total'] = $list['total'];
            $result['total_page'] = $list['last_page'];
            $result['data'] = $data;
            return returnJson(ConstFile::API_RESPONSE_SUCCESS_MESSAGE_DATA,ConstFile::API_RESPONSE_SUCCESS,$result);
        }catch (Exception $e){
            return returnJson($e->getMessage(), $e->getCode());
        }
    }

    /*
     * 获取修改类目计划数据
     * */
    public function editCategoryPlanInfo($user, $param){
        try{
            if(empty($param['id'])){
                return returnJson('请选择类目计划', ConstFile::API_RESPONSE_FAIL);
            }
            $this->setUserDepartment($user->id);
            $info = BussinessCategoryPlan::where(['id' => $param['id'], 'department_id'=>$this->user_department_id_])->get(['id', 'user_id', 'plan_id', 'category_id', 'content', 'money', 'unit_type', 'unit', 'program_id'])->first();//同一个部门有权限的人可以修改和查看（与换部门对应）
            if(empty($info)){
                return returnJson(ConstFile::API_RECORDS_NOT_EXIST_MESSAGE, ConstFile::API_RESPONSE_FAIL);
            }
            $rpc = new RpcRepository();

            $info->category_name = $info->program_name = $info->unit_name = '';
            if($info->category_id){
                $category = $rpc->getFlowCateName($info->category_id);//类目信息
                $info->category_name = !empty($category['name']) ? $category['name'] : '';
            }
            if($info->program_id){
                $project = $rpc->getProjectById($info->program_id);//项目信息
                $info->program_name = !empty($project['title']) ? $project['title'] : '';
            }
            if($info->unit_type){
                if($this->unit_type_[$info->unit_type] == 3){
                    //单位
                    $department = $this->setUserDepartment($info->unit, 1);
                    $info->unit_name = !empty($department['department_name']) ? $department['department_name'] : '';
                }else{
                    //客户、供应商
                    $customer = $rpc->getCustomerById($info->unit);
                    $info->unit_name = !empty($customer['cusname']) ? $customer['cusname'] : '';
                }
            }

            return returnJson(ConstFile::API_RESPONSE_SUCCESS_MESSAGE_DATA,ConstFile::API_RESPONSE_SUCCESS,$info);
        }catch (Exception $e){
            return returnJson($e->getMessage(), $e->getCode());
        }
    }


    /*
     * 计划统计
     * */
    public function planStatistics($param){
        try{
            $where = app()->make(FinanceLogRepository::class)->searchData($param);

            $list = new BussinessCategoryPlan();//计划数据
            $finance = FinancialDetail::leftJoin('financial', 'financial.id', '=', 'financial_detail.financial_id');//财务数据
            if(!empty($where['create_begin'])){
                $list = $list->where('created_at', '>=', $where['create_begin']);
                $finance = $finance->where('financial_detail.created_at', '>=', $where['create_begin']);
            }
            if(!empty($where['create_end'])){
                $list = $list->where('created_at', '<=', $where['create_end']);
                $finance = $finance->where('financial_detail.created_at', '<=', $where['create_end']);
            }
            if(!empty($where['selectdepts'])){
                $list = $list->whereIn('department_id', $where['selectdepts']);
                $finance = $finance->whereIn('financial.primary_dept', $where['selectdepts']);
            }
            $list = $list->selectRaw('category_id, sum(money) as money')->groupBy('category_id')->paginate($param['limit'])->toArray();

            $data = [];
            if(!empty($list['data'])){
                $rpc = new RpcRepository();
                $category_id = array_column($list['data'], 'category_id');//类目id
                $category_names = $rpc->getFlowCateNameByIds($category_id);//类目信息
                $category_names = array_field_as_key($category_names, 'id');

                $finance = $finance->whereIn('financial_detail.projects_id', $category_id)->groupBy('financial_detail.projects_id')->selectRaw('financial_detail.projects_id as category_id, sum(money) as money')->get()->toArray();//关联财务的数据
                $finance = collect($finance)->pluck('money', 'category_id')->toArray();

                collect($list['data'])->each(function($v) use (&$data, $finance, $category_names){
                    $da = [
                        'category_id' => $v['category_id'],
                        'category_name' => !empty($category_names[$v['category_id']]['name']) ? $category_names[$v['category_id']]['name'] : '',
                        'plan_money' => $v['money'],
                        'real_money' => !empty($finance[$v['category_id']]) ? $finance[$v['category_id']] : 0
                    ];
                    if($da['plan_money'] == 0 || $da['real_money'] == 0){
                        $da['scale'] = '0%';
                    }else{
                        $da['scale'] = round(($finance[$v['category_id']]/$v['money'])*100, 2).'%';
                    }
                    $data[] = $da;
                });
            }

            $result['total'] = $list['total'];
            $result['total_page'] = $list['last_page'];
            $result['data'] = $data;

            return returnJson(ConstFile::API_RESPONSE_SUCCESS_MESSAGE_DATA,ConstFile::API_RESPONSE_SUCCESS,$result);
        }catch (Exception $e){
            return returnJson($e->getMessage(), $e->getCode());
        }
    }


    /*
     * 检测计划数据
     * */
    private function checkBussinessPlan($param){
        $msg = '';

        if(empty($param['title'])){
            $msg = '请填写计划名称';
        }
        if(empty($param['month'])){
            $msg = '请填写计划月份';
        }
        return $msg;
    }


    /*
     * 检测类目计划数据
     * */
    private function checkCategoryPlan($param, $user){
        $msg = '';

        if(empty($param['plan_id'])){
            $msg = '请选择计划';
        }
        if(empty($param['category_id'])){
            $msg = '请选择类目';
        }
        if(empty($param['content'])){
            $msg = '请填写计划内容';
        }
        if(empty($param['money']) || $param['money'] <=0 || $param['money'] >= 1000000000){
            $msg = '请填写合理的计划金额';
        }

        if($msg){
            return ['status'=>self::NO, 'msg'=>$msg, 'data'=>[]];
        }else{
            $data = $param;
            $data['user_id'] = $user->id; //换了部门后，其它有此权限的人可以修改
            /*$data = [
                'user_id' => $user->id, //换了部门后，其它有此权限的人可以修改
                'plan_id' => $param['plan_id'],
                'category_id' => $param['category_id'],
                'content' => $param['content'],
                'money' => $param['money'],
                'unit_type' => $param['unit_type'],
                'unit' => $param['unit'],
                'program_id' => $param['program_id']
            ];*/
            return ['status'=>self::YES, 'msg'=>$msg, 'data'=>$data];
        }
    }
}
