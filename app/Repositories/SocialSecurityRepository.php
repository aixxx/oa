<?php

namespace App\Repositories;

use App\Constant\ConstFile;
use App\Models\Contract\Contract;
use App\Models\Contract\ContractApproval;
use App\Models\SocialSecurity\SocialSecurity;
use App\Models\SocialSecurity\SocialSecurityRelation;
use Carbon\Carbon;
use Prettus\Repository\Eloquent\BaseRepository;
use Exception;
use Request;
use DB;
use Auth;

class SocialSecurityRepository extends BaseRepository
{

    public function model()
    {
        return Contract::class;
    }

    /**
     * @description 创建社保
     * @author liushaobo
     * @time 2019/4/12
     * @param array $data
     * @param $user
     * @return false|\Illuminate\Http\JsonResponse|string
     * @throws \Throwable
     */
    public function created(array $data, $user)
    {
        try {
            DB::transaction(function () use ($data, $user) {
                $pinyin = app('pinyin');
                Collect($data['data'])->each(function ($item, $key) use ($data,$user,$pinyin) {
                    $error = $this->checkData($item);
                    if ($error) {
                        throw new Exception('请求参数错误：' . $error, ConstFile::API_RESPONSE_FAIL);
                    }
                    //创建薪资和字典的关联
                    $socialSecurityData = array();
                    $socialSecurityData['company_id'] = $user->company_id;
                    $socialSecurityData['create_user_id'] = $user->id;
                    $socialSecurityData['create_user_name'] = $user->name;
                    $socialSecurityData['name'] = $item['name'];
                    $socialSecurityData['english_name'] = preg_replace('# #','',$pinyin->sentence($item['name']));
                    $socialSecurityData['company_proportion'] = $item['company_proportion'];
                    $socialSecurityData['personal_proportion'] = $item['personal_proportion'];
                    $socialSecurityData['payment_standard'] = $item['payment_standard'];
                    SocialSecurity::create($socialSecurityData);
                });
                SocialSecurityRelation::query()->where(['company_id'=>$user->company_id])->delete();
                Collect($data['user_id'])->each(function ($item, $key) use ($data,$user) {
                    $relationData = array();
                    $relationData['user_id'] = $item;
                    $relationData['create_user_id'] = $user->id;
                    $relationData['create_user_name'] = $user->chinese_name;
                    $relationData['company_id'] = $user->company_id;
                    SocialSecurityRelation::create($relationData);
                });
            });
        } catch (Exception $e) {
            return returnJson($e->getMessage(), ConstFile::API_RESPONSE_FAIL);
        }

        return returnJson(ConstFile::API_RESPONSE_SUCCESS_MESSAGE, ConstFile::API_RESPONSE_SUCCESS);
    }

    /**
     * @description 删除社保
     * @author liushaobo
     * @time 2019/4/12
     * @param $id
     * @return false|\Illuminate\Http\JsonResponse|string
     */
    public function deleted($id)
    {
        $socialSecurity = SocialSecurity::find($id);
        if (empty($socialSecurity)) {
            return returnJson(ConstFile::API_DELETE_RECORDS_NOT_EXIST_MESSAGE, ConstFile::API_RESPONSE_FAIL);
        }
        if ($socialSecurity->delete()) {
            return returnJson(ConstFile::API_RESPONSE_SUCCESS_MESSAGE);
        }
        return returnJson(ConstFile::API_RESPONSE_FAIL_MESSAGE, ConstFile::API_RESPONSE_FAIL);
    }

    /**
     * @description 查看个人社保
     * @author liushaobo
     * @time 2019/4/12
     * @param $user
     * @return false|\Illuminate\Http\JsonResponse|string
     */
    public function fetchUserSocialSecurity($user)
    {
        try {
            $department = app()->make(UsersRepository::class);
            $departmentArray = $department->getAllDept(0, $user);//这是通过部门获取模拟用户id
            if (!$departmentArray) {
                throw new Exception(sprintf('请求参数错误：'), ConstFile::API_RESPONSE_FAIL);
            }
            $contract = Contract::where(['company_id'=>$departmentArray['id'],'user_id'=>$user->id,'status'=>ConstFile::CONTRACT_STATUS_TWO])->first();
            $salarySum = $contract->belongsToManySalaryData->sum('field_data');
            $socialsecurity = SocialSecurity::get()->toArray();
            $data = array();
            foreach($socialsecurity as $key=>$val){
                $data[$key]['name'] = $val['name'];
                $data[$key]['personal_proportion'] = $val['personal_proportion'];
                $data[$key]['salary'] = ($salarySum * $val['personal_proportion']) / 100;
            }
        } catch (Exception $e) {
            return returnJson($e->getMessage(), ConstFile::API_RESPONSE_FAIL);
        }
        return returnJson(ConstFile::API_RESPONSE_SUCCESS_MESSAGE_DATA, ConstFile::API_RESPONSE_SUCCESS,$data);
    }

    /**
     * @param $companyId
     * @return array|string
     */
    public function getSocialSecurity($companyId){
        try {
            $socialSecurity = SocialSecurity::where('company_id',$companyId)->select()->get();
            $list = $socialSecurity->toArray();
            $data = array();
            foreach($list as $key=>$val){
                $data[$val['english_name']]=['company_proportion'=>$val['company_proportion'],'personal_proportion'=>$val['personal_proportion']];
            }
        } catch (Exception $e) {
            return $e->getMessage();
        }
        return $data;
    }

    /**
     * @param $companyId
     * @return string
     */
    public function getUserSocialSecurity($companyId){
        try {
            $socialSecurityUser = SocialSecurityRelation::where('company_id',$companyId)->pluck('user_id');
            $data = $socialSecurityUser->toArray();
        } catch (Exception $e) {
            return $e->getMessage();
        }
        return $data;
    }
    /**
     * @description 提交社保参与人
     * @author liushaobo
     * @time 2019/4/28
     * @param array $data
     * @param $user
     * @return false|\Illuminate\Http\JsonResponse|string
     * @throws \Throwable
     */
    public function createParticipant(array $data,$user){
        try {
            if(empty($data) || !is_array($data)){
                throw new Exception(sprintf('请求参数错误：'), ConstFile::API_RESPONSE_FAIL);
            }
            DB::transaction(function () use ($data, $user) {
                SocialSecurityRelation::query()->where(['company_id'=>$user->company_id])->delete();
                Collect($data['user_id'])->each(function ($item, $key) use ($data,$user) {
                    $relationData = array();
                    $relationData['user_id'] = $item;
                    $relationData['create_user_id'] = $user->id;
                    $relationData['create_user_name'] = $user->chinese_name;
                    $relationData['company_id'] = $user->company_id;
                    SocialSecurityRelation::create($relationData);
                });
            });
        } catch (Exception $e) {
            return returnJson($e->getMessage(), $e->getCode());
        }

        return returnJson(ConstFile::API_RESPONSE_SUCCESS_MESSAGE, ConstFile::API_RESPONSE_SUCCESS);

    }



    /**
     * @description 检查参数
     * @author liushaobo
     * @time 2019\4\12
     * @param $data
     * @return string|null
     */
    private function checkData($data)
    {
        if (empty($data)) {
            return '请求数据不能为空';
        }
        if (!isset($data['name']) || empty($data['name'])) {
            return '社保名称不能为空';
        }
        if (!isset($data['company_proportion']) || empty($data['company_proportion'])) {
            return '公司缴纳比例不能为空';
        }

        if (!isset($data['personal_proportion']) || empty($data['personal_proportion'])) {
            return '个人缴纳比例不能为空';
        }
        if (!isset($data['payment_standard']) || empty($data['payment_standard'])) {
            return '工资缴纳标准不能为空';
        }
        return null;
    }
}
