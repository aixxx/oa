<?php

namespace App\Repositories;

use App\Constant\ConstFile;
use App\Models\Addwork\AddworkCompany;
use App\Models\Contract\Contract;
use App\Models\Salary\Salary;
use App\Models\Salary\UsersSalaryData;
use App\Models\Salary\UsersSalaryRelation;
use Carbon\Carbon;
use Prettus\Repository\Eloquent\BaseRepository;


use Exception;
use Request;
use DB;
use Auth;

class SalaryRepository extends BaseRepository
{

    public function model()
    {
        return Salary::class;
    }

    /**
     * @description 添加薪资模板
     * @author liushaobo
     * @time 2019\3\29
     * @param array $data
     * @param $user
     * @return false|\Illuminate\Http\JsonResponse|string
     * @throws \Throwable
     */
    public function salaryCreateTemplate(array $data, $user)
    {
        try {
            $error = $this->checkData($data);
            if ($error) {
                throw new Exception(sprintf('请求参数错误：' . $error), ConstFile::API_RESPONSE_FAIL);
            }


            DB::beginTransaction();
            //创建薪资模板
            $fieldArray = $data['field'];
            unset($data['field_id']);
            $data['create_salary_user_id'] = $user->id;
            $data['create_salary_user_name'] = $user->name;
            $data['company_id'] = $user->company_id;
            $salary = Salary::create($data);
            //闭包
            Collect($fieldArray)->each(function ($item, $key) use ($data, $salary) {
                //创建薪资和字典的关联
                $salaryRelationData = array();
                $salaryRelationData['template_id'] = $salary->id;
                $salaryRelationData['field_id'] = $item['field_id'];
                $salaryRelationData['status'] = $item['status'];
                UsersSalaryRelation::create($salaryRelationData);
            });
            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            return returnJson($e->getMessage(), $e->getCode());
        }
        return returnJson(ConstFile::API_RESPONSE_SUCCESS_MESSAGE, ConstFile::API_RESPONSE_SUCCESS, $salary);

    }

    /**
     * @description 修改薪资模板
     * @author liushaobo
     * @time 2019\3\29
     * @param array $data
     * @param $user
     * @return false|\Illuminate\Http\JsonResponse|string
     * @throws \Throwable
     */
    public function salaryEditTemplate(array $data, $user)
    {
        try {
            $error = $this->checkData($data);
            if ($error) {
                throw new Exception(sprintf('请求参数错误：' . $error), ConstFile::API_RESPONSE_FAIL);
            }
            if (!isset($data['id']) || empty($data['id'])) {
                throw new Exception(sprintf('请求参数错误：id不能为空'), ConstFile::API_RESPONSE_FAIL);
            }
            //这里是权限判断，先保留


            DB::transaction(function () use ($data, $user) {
                //创建薪资模板


                $fieldArray = $data['field'][0];

                $salary = Salary::find($data['id']);
                if (!$salary) {
                    throw new Exception(sprintf('不存id为%s的薪资组', $data['id']), ConstFile::API_RESPONSE_FAIL);
                }
                $data['create_salary_user_id'] = $user->id;
                $data['create_salary_user_name'] = $user->name;
                $data['company_id'] = $user->company_id;
                unset($data['id']);
                unset($data['field_id']);
                $result = $salary->fill($data)->save();
                if (!$result) {
                    throw new Exception(sprintf('更新薪资组失败'));
                }

                $salary->hasManySalaryRelation()->delete();
                //闭包
                Collect($fieldArray)->each(function ($item, $key) use ($data, $salary) {
                    //创建薪资和字典的关联
                    $salaryRelationData = array();
                    $salaryRelationData['template_id'] = $salary->id;
                    $salaryRelationData['field_id'] = $item[0]['field_id'];
                    $salaryRelationData['status'] = $item[0]['status'];
                    UsersSalaryRelation::create($salaryRelationData);
                });
            });
        } catch (Exception $e) {
            return returnJson($e->getMessage(), $e->getCode());
        }
        return returnJson(ConstFile::API_RESPONSE_SUCCESS_MESSAGE);

    }

    /**
     * @description 获取模板列表
     * @author liushaobo
     * @time 2019\3\29
     * @param $user
     * @return false|\Illuminate\Http\JsonResponse|string
     */
    public function fetchSalaryTemplates($user)
    {
        try {
            $temp = Salary::where(['company_id' => $user->company_id])->get();
            if (empty($temp)) {
                throw new Exception(sprintf('没有数据了'), ConstFile::API_RESPONSE_SUCCESS);
            }
            $salaryLists = array();
            foreach ($temp as $key => $val) {
                $salaryLists[$key]['id'] = $val->id;
                $salaryLists[$key]['template_name'] = $val->template_name;
                $salaryLists[$key]['created_at'] = $val->created_at;
            }
        } catch (Exception $e) {
            return returnJson($e->getMessage(), $e->getCode());
        }
        return returnJson(ConstFile::API_RESPONSE_SUCCESS_MESSAGE_DATA, ConstFile::API_RESPONSE_SUCCESS, $salaryLists);
    }


    /**
     * @description 获取模板列表
     * @author liushaobo
     * @time 2019\3\29
     * @param $id
     * @param $user
     * @return false|\Illuminate\Http\JsonResponse|string
     */
    public function fetchSalaryTemplate($id, $user)
    {
        try {
            if (!isset($id) || empty($id)) {
                throw new Exception(sprintf('请求参数错误：id不能为空'), ConstFile::API_RESPONSE_FAIL);
            }
            $salary = Salary::where(['id' => $id, 'company_id' => $user->company_id])->first();

            if (!$salary) {
                throw new Exception(sprintf('不存在id为%s的薪资组', $id), ConstFile::API_RESPONSE_FAIL);
            }
            $fieldlist = array();
            $temp = $salary->hasManySalaryRelation;
            $salarystatus = Salary::$salaryRelationStatus;
            foreach ($salarystatus as $k => $v) {

                $fieldlists = array();
                foreach ($temp as $key => $item) {
                    $field = DB::table('addwork_company')
                        ->leftJoin('addwork_field', 'addwork_company.field_id', '=', 'addwork_field.id')
                        ->select('addwork_field.name', 'addwork_field.e_name', 'addwork_field.type', 'addwork_field.api', 'addwork_field.validate')
                        ->where('addwork_company.company_id', '=', $user->company_id)
                        ->where('addwork_company.type', '=', '2')
                        ->where('addwork_field.status', '=', '2')
                        ->where('addwork_field.id', '=', $item->field_id)
                        ->first();
                    if ($field && $item->status == $k) {
                        $fieldsalary['id'] = $item->id;
                        $fieldsalary['field_id'] = $item->field_id;
                        $fieldsalary['name'] = $field->name;
                        $fieldsalary['e_name'] = $field->e_name;
                        $fieldsalary['type'] = $field->type;
                        $fieldsalary['api'] = $field->api;
                        $fieldsalary['validate'] = $field->validate;
                        $fieldlists[] = $fieldsalary;
                    }
                }
                if ($fieldlists) {
                    $fielddata['salarystatus'] = $v;
                    $fielddata['fieldlists'] = $fieldlists;
                    $fieldlist[] = $fielddata;
                }
            }
            $data['id'] = $salary->id;
            $data['template_name'] = $salary->template_name;
            $data['created_at'] = $salary->created_at;
            $data['field'] = $fieldlist;
        } catch (Exception $e) {
            return returnJson($e->getMessage(), $e->getCode());
        }
        return returnJson(ConstFile::API_RESPONSE_SUCCESS_MESSAGE_DATA, ConstFile::API_RESPONSE_SUCCESS, $data);
    }

    /**
     * @description 薪资数据录入
     * @author liushaobo
     * @time 2019\3\29
     * @param array $data
     * @param $user
     * @return false|\Illuminate\Http\JsonResponse|string
     */
    public function salaryCreateData(array $data, $user)
    {

        try {
            $error = $this->checkSearchData($data);
            if ($error) {
                throw new Exception(sprintf('请求参数错误：' . $error), ConstFile::API_RESPONSE_FAIL);
            }

            $userId = $data['user_id'];
            $templateId = $data['template_id'];
            $dataArray = $data['data'][0][0];
            $salary = Salary::find($templateId);
            if (!$salary) {
                throw new Exception(sprintf('不存id为%s的薪资组', $templateId), ConstFile::API_RESPONSE_FAIL);
            }
            $salary->hasManyUsersSalaryData()->delete();
            //闭包
            Collect($dataArray)->each(function ($item, $key) use ($userId, $templateId, $data, $user) {

                //创建薪资和字典的关联
                $usersSalaryData = array();
                $usersSalaryData['template_id'] = $templateId;
                $usersSalaryData['field_id'] = $item['field_id'];
                $usersSalaryData['field_name'] = $item['field_name'];
                $usersSalaryData['relation_id'] = $item['relation_id'];
                $usersSalaryData['field_data'] = $item['field_data'];
                $usersSalaryData['company_id'] = $user->company_id;
                $usersSalaryData['create_salary_user_id'] = $user->id;
                $usersSalaryData['create_salary_user_name'] = $user->name;
                $usersSalaryData['type'] = 1;
                UsersSalaryData::create($usersSalaryData);
            });
        } catch (Exception $e) {
            return returnJson($e->getMessage(), $e->getCode());
        }
        return returnJson(ConstFile::API_RESPONSE_SUCCESS_MESSAGE);
    }

    /**
     * @description 薪资数据录入
     * @author liushaobo
     * @time 2019\3\29
     * @param $user
     * @return false|\Illuminate\Http\JsonResponse|string
     */
    public function getSalaryField($user)
    {
        try {

            $companyField = AddworkCompany::with(['hasOneAddWorkFieldGet'])->where(['company_id' => $user->company_id, 'type' => ConstFile::ADD_WORK_COMPANY_TYPE_SALARY])->get()->toArray();//AddworkCompany::where(['company_id'=>$departmentArray['id'],'type'=>ConstFile::ADD_WORK_COMPANY_TYPE_SALARY])->get();
            $data = array();
            foreach ($companyField as $key => $val) {
                $data[$key]['id'] = $val['id'];
                $data[$key]['field_id'] = $val['field_id'];
                $data[$key]['name'] = $val['has_one_add_work_field_get']['name'];
            }
        } catch (Exception $e) {
            return returnJson($e->getMessage(), $e->getCode());
        }
        return returnJson(ConstFile::API_RESPONSE_SUCCESS_MESSAGE_DATA, ConstFile::API_RESPONSE_SUCCESS, $data);
    }

    /**
     * @param $userId
     * @return array|bool
     */
    public function getUserSalary($userId)
    {
        try {
            if(empty($userId)){
                throw new Exception(sprintf('请求参数错误：'));
            }
            $userIds = $userId;
            if (!is_array($userId)) {
                $userIds[] = $userId;
            }
            $contract = Contract::whereIn('user_id', $userIds)->select(DB::raw('user_id,max(version) as v'))->groupBy('user_id');
            $contractSalary = DB::table('contract')
                ->rightJoin(DB::raw("({$contract->toSql()}) AS cv "), function ($join) use ($userIds) {
                    $join->on('contract.user_id', '=', 'cv.user_id')->on('contract.version', '=', 'cv.v');
                })
                ->mergeBindings($contract->getQuery())
                ->leftJoin('users_salary', function ($join) {
                    $join->on('contract.id', '=', 'users_salary.contract_id')->on('contract.salary_version', '=', 'users_salary.version');
                })->select(['users_salary.*'])->where('users_salary.status','<>',Salary::SALARY_RELATION_STATUS_BONUS)->get();


            $data = array();
            foreach ($contractSalary as $key => $val) {
                $user = in_array($val->user_id,$userIds);
                if ($user) {
                    if ($val->status == Salary::SALARY_RELATION_STATUS_SALARY) {
                        $data[$val->user_id]['basic_salary'] = intval($data[$val->user_id]['basic_salary'] ?? 0) + intval(trim($val->field_data));
                        $data[$val->user_id]['subsidy_salary'] = intval($data[$val->user_id]['basic_salary'] ?? 0);
                        $field['name'] = trim($val->field_name ?? '');
                        $field['value'] = intval(trim($val->field_data ?? 0));
                        $data[$val->user_id]['basic_salary_list'][] = $field;
                        $data[$val->user_id]['subsidy_salary_list'] = $data[$val->user_id]['subsidy_salary_list'] ?? [];
                    } elseif ($val->status == Salary::SALARY_RELATION_STATUS_SUBSIDY) {
                        $data[$val->user_id]['basic_salary'] = intval($data[$val->user_id]['basic_salary'] ?? 0);
                        $data[$val->user_id]['subsidy_salary'] = intval($data[$val->user_id]['subsidy_salary'] ?? 0) + intval(trim($val->field_data));
                        $data[$val->user_id]['basic_salary_list'] = $data[$val->user_id]['basic_salary_list'] ?? [];
                        $field['name'] = trim($val->field_name ?? '');
                        $field['value'] = intval(trim($val->field_data ?? 0));
                        $data[$val->user_id]['subsidy_salary_list'][] = $field;
                    }
                }
            }
        } catch (Exception $e) {
            return false;
        }
        return $data;
    }

    /**
     * @description 检查参数
     * @author liushaobo
     * @time 2019\3\21
     * @param $data
     * @return string|null
     */
    private function checkData($data)
    {
        if (empty($data)) {
            return '请求数据不能为空';
        }
        if (!isset($data['template_name']) || empty($data['template_name'])) {
            return '请填写模板名称';
        }
        if (!isset($data['field']) || empty($data['field'])) {
            return '请选择选项字段';
        }
        return null;
    }

    private function checkSearchData($data)
    {
        if (empty($data)) {
            return '请求参数不能为空';
        }
        if (!isset($data['template_id'])) {
            return '模板的ID不能为空';
        }
        return null;
    }
}
