<?php

namespace App\Repositories;

use App\Models\Company;
use App\Models\Contract\Contract;
use App\Models\Department;
use App\Models\DepartUser;
use App\Models\SocialSecurity\SocialSecurityRelation;
use App\Models\UserFamily;
use App\Models\User;
use App\Constant\ConstFile;
use App\Services\WorkflowUserService;
use Carbon\Carbon;
use function GuzzleHttp\Psr7\_caseless_remove;
use Illuminate\Http\Request;
use App\Models\UsersDetailInfo;
use App\Models\Describe\Describe;
use Illuminate\Support\Facades\Schema;
use App\Models\UserUrgentContact;
use DB;
use Cache;
use DevFixException;
use Exception;
use App\Http\Services\SmsTrait;
use Auth;

/**
 * Class UsersRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class UsersRepository extends ParentRepository
{
    use SmsTrait;

    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return User::class;
    }

    public function __construct()
    {

    }

    /**
     * @deprecated 获取工资包比例
     * @param $request
     * @return mixed
     */
    public function proportion($userId)
    {
        try {
            if (empty($userId)) {
                throw new Exception('员工id为空');
            }
            //判断值是否为数组
            if (!is_array($userId)) {
                $userIds[] = $userId;
            } else {
                $userIds = $userId;
            }
            //获取工资包比例
            $array = Describe::whereIn('user_id', $userIds)
                ->select(['user_id', 'wage_classes', 'salary_scale', 'points_scale'])
                ->get();
            $data = [];
            if (!empty($array)) {
                $arr = $array->toArray();
                foreach ($arr as $key => $val) {
                    $data[$val['user_id']] = $val;
                }
            }
        } catch (Exception $e) {
            return false;
        };
        return $data;
    }

    /**
     * @deprecated 获取用户信息名片
     * @param $id
     * @return false|\Illuminate\Http\JsonResponse|string
     * @throws \Exception
     */
    public function userCard($id)
    {
        try {
            $first = [
                'id',
                'chinese_name',
                'mobile',
                'company_id',
                'avatar',
                'gender',
                'position',
                'work_name',
                'join_at',
                'work_address'
            ];

            $user = User::where('id', '=', $id)
                ->first($first);

            $userDetail = UsersDetailInfo::where('user_id', '=', $id)
                ->first(['born_time', 'address', 'user_status']);

            $user->mobile = isset($user->mobile) ? decrypt_no_user_exception($user->mobile) : "";
            $user->born_time = isset($userDetail->born_time) ? decrypt_no_user_exception($userDetail->born_time) : "";
            $user->address = isset($userDetail->address) ? decrypt_no_user_exception($userDetail->address) : "";
            $user->user_status = isset($userDetail->user_status) ? $this->judge($userDetail->user_status) : "";

            $tops = WorkflowUserService::fetchUserPrimaryDeptPath($id);
            $tops = explode('/', $tops);

            $user->topDepartment = $tops[0];
            $user->firstDepartment = isset($tops[1]) ? $tops[1] : $tops[0];

            $array = Contract::where('user_id', $id)
                ->where('status', 2)
                ->orderBy('created_at', 'decs')
                ->select('id', 'user_id', 'version', 'probation', 'contract', 'entry_at', 'contract_end_at')
                ->get();
            if (!empty($array) && count($array) > 0) {
                $data = $array->toArray();
                foreach ($data as $val) {
                    if ($val['version'] == '1') {
                        $user->firstTime = $val['entry_at'];
                        $user->firstEndTime = $val['contract_end_at'];
                    }
                }
                if (count($data) > 0) {
                    $user->Time = $data[0]['entry_at'];
                    $user->EndTime = $data[0]['contract_end_at'];
                    if (!empty($user->join_at)) {
                        //试用期
                        $probation
                            = date("Y-m-d", strtotime("+" . $data[0]['probation'] .
                            "months", strtotime($user->join_at)));

                        $user->probation = $user->join_at . '-' . $probation;

                        //转正期
                        $user->positive
                            = $probation . '-' . date("Y-m-d", strtotime("+" . $data[0]['contract'] .
                                "years", strtotime($probation)));
                        //合同期限
                        $user->timeLimit = $data[0]['contract'];
                    }
                }
                $user->renew_count = count($data) - 1;
            } else {
                $user->firstTime = "";
                $user->firstEndTime = "";
                $user->Time = "";
                $user->EndTime = "";
                $user->probation = "";
                $user->positive = "";
                $user->timeLimit = "";
                $user->renew_count = "";
            }
        } catch (Exception $e) {
            $this->message = $e->getMessage();
            $this->code = $e->getCode();
            return returnJson($this->message, $this->code);
        }
        return returnJson(ConstFile::API_RESPONSE_SUCCESS_MESSAGE_DATA, ConstFile::API_RESPONSE_SUCCESS, $user);
    }

    /**
     * @deprecated 判断
     * @param $status
     * @param $type
     * @return mixed
     */
    public function judge($status)
    {
        if ($status == ConstFile::STAFF_TYPE_FULL_TIME) {
            $userStatus = ConstFile::$staffTypeList[ConstFile::STAFF_TYPE_FULL_TIME];
        } elseif ($status == ConstFile::STAFF_TYPE_PART_TIME) {
            $userStatus = ConstFile::$staffTypeList[ConstFile::STAFF_TYPE_PART_TIME];
        } elseif ($status == ConstFile::STAFF_TYPE_LABOR) {
            $userStatus = ConstFile::$staffTypeList[ConstFile::STAFF_TYPE_LABOR];
        } elseif ($status == ConstFile::STAFF_TYPE_OUT_SOURCE) {
            $userStatus = ConstFile::$staffTypeList[ConstFile::STAFF_TYPE_OUT_SOURCE];
        } else {
            $userStatus = ConstFile::$staffTypeList[ConstFile::STAFF_TYPE_REHIRE];
        }
        return $userStatus;
    }

    /**
     * @deprecated 名片修改
     * @param $request
     * @param string $id
     * @return false|\Illuminate\Http\JsonResponse|string
     */
    public function userCardEdit($request, $id = '')
    {

        try {
            $array = $request->all();
            if (empty($array['type'])) {
                throw  new \Exception('类型不能为空', '400');
            }
            if (empty($array['word'])) {
                throw  new \Exception('参数不能为空', '400');
            }
            switch ($array['type']) {
                case 1://头像
                    User::where('id', '=', $id)->update(['avatar' => $array['word']]);
                    break;
                case 2://昵称
                    User::where('id', '=', $id)->update(['chinese_name' => $array['word']]);
                    break;
                case 3://电话
                    //验证码错误
                    $res = $this->checkUsersCode($array['word'], $array['code']);
                    if (!$res) {
                        throw new Exception("短信验证码错误");
                    }

                    //清除缓存
                    $key = 'users_' . date('YmdH') . '_' . trim($array['word']);

                    $this->checkMobile($array['word']);
                    $code = $this->traitGetCacheCode($key, false);
                    $res = $this->traitCheckCode($code, $array['code']);
                    if ($res == true) {
                        User::where('id', '=', $id)->update(['mobile' => $array['word']]);
                        $this->traitClearCache($key);
                    } else {
                        throw  new \Exception('验证码不一致', '400');
                    }
                    break;
                case 4://性别
                    User::where('id', '=', $id)->update(['gender' => $array['word']]);
                    UsersDetailInfo::where('user_id', '=', $id)->update(['gender' => $array['word']]);
                    break;
                case 5://生日
                    $word = encrypt($array['word']);
                    UsersDetailInfo::where('user_id', '=', $id)->update(['born_time' => $word]);
                    break;
                case 6://家住地址
                    $word = encrypt($array['word']);
                    UsersDetailInfo::where('user_id', '=', $id)->update(['address' => $word]);
                    break;
                case 7://工作状态
                    User::where('id', '=', $id)->update(['work_name' => $array['word']]);
                    break;
            }
        } catch (Exception $e) {
            $this->message = $e->getMessage();
            $this->code = $e->getCode();
            return returnJson($this->message, $this->code);
        }
        return returnJson(ConstFile::API_RESPONSE_SUCCESS_MESSAGE, ConstFile::API_RESPONSE_SUCCESS);
    }

    /**
     * @deprecated 紧急联系人信息
     * @param $request
     * @param $id
     * @return false|\Illuminate\Http\JsonResponse|string
     * @throws \DevFixException
     */
    public function userDankCard($request, $id)
    {
        $allData = $request->all();
        try {
            UserUrgentContact::createUrgentContacts($id, $allData);
        } catch (Exception $e) {
            $messages = $e->getMessage();
            return returnJson($messages, ConstFile::API_RESPONSE_FAIL);
        }
        return returnJson(ConstFile::API_RESPONSE_SUCCESS_MESSAGE, ConstFile::API_RESPONSE_SUCCESS);
    }

    /**
     * @deprecated 家庭信息
     * @param $request
     * @param $id
     * @return false|\Illuminate\Http\JsonResponse|string
     * @throws \DevFixException
     */
    public function userFamily($request, $id)
    {
        $allData = $request->all();
        try {
            UserFamily::createUserFamilys($id, $allData);
        } catch (Exception $e) {
            $messages = $e->getMessage();
            return returnJson($messages, ConstFile::API_RESPONSE_FAIL);
        }
        return returnJson(ConstFile::API_RESPONSE_SUCCESS_MESSAGE, ConstFile::API_RESPONSE_SUCCESS);
    }

    /**
     * @deprecated 删除家庭信息
     * @param $request
     * @param $id
     * @return false|\Illuminate\Http\JsonResponse|string
     * @throws \DevFixException
     */
    public function userFamilyDelete($id)
    {
        $user = User::find($id);
        try {
            if (empty($user) && count($user)<0) {
                throw new DevFixException('用户子女信息为空，无法操作');
            }
            $res = $user->family()->delete();
            if (!$res) {
                throw new DevFixException('删除失败，请重新删除');
            }
            User::where('id', $id)->update(['is_family_perfect' => 0]);
        } catch (Exception $e) {
            $messages = $e->getMessage();
            return returnJson($messages, ConstFile::API_RESPONSE_FAIL);
        }
        return returnJson(ConstFile::API_DELETE_SUCCESS, ConstFile::API_RESPONSE_SUCCESS);
    }

    /**
     * @deprecated  查询数据
     * @param string $id
     * @return false|\Illuminate\Http\JsonResponse|string
     */

    public function userFilesQuery($id = "")
    {
        try {
            $first = [
                'id',
                'chinese_name',
                'gender',
                'mobile',
                'is_person_perfect',
                'is_card_perfect',
                'is_edu_perfect',
                'is_pic_perfect',
                'is_family_perfect',
                'is_urgent_perfect'
            ];
            //获取信息
            $user = User::with('detail', 'urgentUser', 'family')
                ->where('id', '=', $id)
                ->first($first);

            if (empty($user)) {
                return returnJson(ConstFile::API_RECORDS_NOT_EXIST_MESSAGE, ConstFile::API_RESPONSE_FAIL);
            }

            //完善度
            $data[] = $user->is_person_perfect;
            $data[] = $user->is_card_perfect;
            $data[] = $user->is_edu_perfect;
            $data[] = $user->is_pic_perfect;
            $data[] = $user->is_family_perfect;
            $data[] = $user->is_urgent_perfect;

            $user->employee_num = $user->getPrefixEmployeeNum();

            $users = [];
            if (!empty($user->detail)) {
                $users['id'] = $user->id;
                $users['chinese_name'] = $user->chinese_name;
                $users['mobile'] = $user->mobile;

                /****************************身份信息*******************************/
                $detail = UsersDetailInfo::decryptSelectDatas($user->detail);
                unset($users['detail']);
                unset($users['depart_user']);
                $users['detail']['user_id'] = $detail['user_id'];
                $users['detail']['id_name'] = $detail['id_name'];
                $users['detail']['gender'] = $detail['gender'];
                $users['detail']['ethnic'] = $detail['ethnic'];
                $users['detail']['born_time'] = $detail['born_time'];
                $users['detail']['id_card_number'] = $detail['id_card_number'];
                $users['detail']['validity_certificate'] = $detail['validity_certificate'];
                $users['detail']['id_address'] = $detail['id_address'];
                $users['detail']['id_detailed_address'] = $detail['id_detailed_address'];
                $users['detail']['address'] = $detail['address'];
                $users['detail']['detailed_address'] = $detail['detailed_address'];
                $users['detail']['census_type'] = $detail['census_type'];
                $users['detail']['marital_status'] = $detail['marital_status'];
                $users['detail']['politics_status'] = $detail['politics_status'];
                $users['detail']['first_work_time'] = $detail['first_work_time'];
                $users['detail']['per_social_account'] = $detail['per_social_account'];
                $users['detail']['per_fund_account'] = $detail['per_fund_account'];
                /****************************学习信息*******************************/
                $users['education']['user_id'] = $detail['user_id'];
                $users['education']['highest_education'] = $detail['highest_education'];
                $users['education']['graduate_institutions'] = $detail['graduate_institutions'];
                $users['education']['graduate_time'] = $detail['graduate_time'];
                $users['education']['major'] = $detail['major'];
                /****************************银行信息*******************************/
                $users['bank_card']['user_id'] = $detail['user_id'];
                $users['bank_card']['bank'] = $detail['bank'];
                $users['bank_card']['branch_bank'] = $detail['branch_bank'];
                $users['bank_card']['bank_card'] = $detail['bank_card'];
                $users['bank_card']['alipay_account'] = $detail['alipay_account'];
                $users['bank_card']['wechat_account'] = $detail['wechat_account'];
                /****************************个人资料*******************************/
                $users['file']['user_id'] = $detail['user_id'];
                $users['file']['pic_id_pos'] = $detail['pic_id_pos'];
                $users['file']['pic_id_neg'] = $detail['pic_id_neg'];
                $users['file']['pic_edu_background'] = $detail['pic_edu_background'];
                $users['file']['pic_degree'] = $detail['pic_degree'];
                $users['file']['pic_pre_company'] = $detail['pic_pre_company'];
                $users['file']['pic_user'] = $detail['pic_user'];
                /***********************************工作信息***********************************************/

            }
            /***********************************紧急联系人************************************************/
            if (count($user->urgentUser) > 0) {
                foreach ($user->urgentUser as $key => $value) {
                    $users['urgent_user'][$key] = UsersDetailInfo::decryptSelectData($value);
                }
            } else {
                $users['urgent_user'] = "";
            }
            /***********************************家庭信息***********************************************/
            if (count($user->family) > 0) {
                foreach ($user->family as $key => $value) {
                    $users['family'][$key] = UsersDetailInfo::decryptSelectData($value);
                }
            } else {
                $users['family'] = '';
            }

            $users['count_num'] = array_sum($data);
        } catch (Exception $e) {
            $messages = $e->getMessage();
            return returnJson($messages, ConstFile::API_RESPONSE_FAIL);
        }
        return returnJson(ConstFile::API_RESPONSE_SUCCESS_MESSAGE_DATA, ConstFile::API_RESPONSE_SUCCESS, $users);
    }


    public function isNoPercent($request, $id = '')
    {
        try {
            $user = User::where('id', '=', $id)->first([
                'is_person_perfect',
                'is_card_perfect',
                'is_edu_perfect',
                'is_pic_perfect',
                'is_family_perfect',
                'is_urgent_perfect']);
        } catch (Exception $e) {
            $this->message = "查询失败，请重新查询";
            $this->code = $e->getCode();
            return returnJson($this->message, $this->code);
        }

        return returnJson(
            ConstFile::API_RESPONSE_SUCCESS_MESSAGE_DATA,
            ConstFile::API_RESPONSE_SUCCESS,
            array_sum($user->toArray())
        );
    }

    /**
     * @deprecated 个人资料(身份信息,学历信息,银行卡信息,个人材料)数据添加与编辑
     * @param $request
     * @param string $id
     * @return false|\Illuminate\Http\JsonResponse|string
     * @throws \Exception
     */
    public function userDetailInfo($request, $id = "")
    {
        $user = User::findOrFail($id);
        if (empty($user)) {
            return returnJson("用户不存在，请检查token", ConstFile::API_RESPONSE_FAIL);
        }
        //接值
        $requestData = $request->all();
        $percent = percent($requestData);

        $oldUserData = $user->toArray();

        DB::beginTransaction();
        try {
            //添加信息
            $detailedColumns = Schema::getColumnListing('users_detail_info');
            //信息加密
            $userDetailedData = $this->getUpdateData($detailedColumns, $requestData, $request, 'detail');
            if ($userDetailedData) {
                $userDetailInfo = UsersDetailInfo::where('user_id', '=', $id)->first();
                if ($userDetailInfo) {
                    unset($userDetailedData['user_id']);
                    $userDetailStatus = $userDetailInfo->update($userDetailedData);
                    //完善信息
                    if ($userDetailStatus) {
                        $data = [];
                        switch ($requestData['infotype']) {
                            case "person":
                                if (!empty($userDetailedData['gender'])) {
                                    $data['gender'] = $userDetailedData['gender'];
                                }
                                //修改百分比
                                if (decrypt_no_user_exception($userDetailInfo->marital_status) == "未婚") {
                                    $data['is_family_perfect'] = 17;
                                }
                                //dd($oldUserData['is_person_perfect']);
                                if ($oldUserData['is_person_perfect'] != $percent) {
                                    $data['is_person_perfect'] = $percent;
                                }
                                break;
                            case "edu":
                                //修改百分比
                                if ($oldUserData['is_edu_perfect'] != $percent) {
                                    $data['is_edu_perfect'] = $percent;
                                }
                                break;
                            case "card":
                                //修改百分比
                                if ($oldUserData['is_card_perfect'] != $percent) {
                                    $data['is_card_perfect'] = $percent;
                                }
                                break;
                            case "pic":
                                //修改百分比
                                if ($oldUserData['is_pic_perfect'] != $percent) {
                                    $data['is_pic_perfect'] = $percent;
                                }
                                break;
                            default:
                                break;
                        }
                        if ($data) {
                            $this->is_perfect($data, $id);
                        }
                    }
                } else { //不存在添加一条用户详细信息
                    $userDetailedData['user_id'] = $id;
                    $userDetail = new UsersDetailInfo;
                    $userDetail->fill($userDetailedData);
                    $userDetailStatus = $userDetail->save();
                }
                if (!$userDetailStatus) {
                    throw new DevFixException("员工详细信息更新失败");
                }
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $messages = $e->getMessage();
            return returnJson($messages, ConstFile::API_RESPONSE_FAIL);
        }
        return returnJson(ConstFile::API_RESPONSE_SUCCESS_MESSAGE, ConstFile::API_RESPONSE_SUCCESS);
    }

    /**
     * @deprecated 修改完善度
     * @param $percent
     * @param $user_id
     */
    protected function is_perfect($percent, $user_id)
    {
        $percent['updated_at'] = date('Y-m-d H:i:s', time());
        $res = User::where(['id' => $user_id])->update($percent);
        if (!$res) {
            throw new Exception('信息添加失败');
        }
    }

    /**
     * @param  update columns $columns
     * @param  form data $requestData
     * @param  request object $request
     * @return array  $returnData
     */
    private function getUpdateData($columns, $requestData, $request, $datatype)
    {

        $returnData = [];

        $dataColumns = [
            'join_at',
            'leave_at',
            'after_probation',
            'born_time',
            'validity_certificate',
            'first_work_time',
            'graduate_time',
            'first_contract_start_time',
            'first_contract_end_time',
            'cur_contract_start_time',
            'cur_contract_end_time',
        ];

        foreach ($columns as $value) {
            if ($request->has($value)) {
                if ($value == 'password') {
                    $returnData[$value] = \Hash::make($requestData[$value]);
                } elseif ($value == 'mobile') {
                    $returnData[$value] = encrypt($requestData[$value]);
                } elseif ($value == 'name') {
                    $returnData[$value] = strtolower(trim($requestData[$value]));
                } elseif ($value == 'work_title') {
                    $returnData[$value] = $requestData['work_type'] != User::WORK_TYPE_SCHEDULE ? $requestData[$value] :
                        '';
                } elseif ($value == 'employee_num') {
                    $returnData[$value] = User::parseEmployeeNum($requestData[$value]);
                } else {
                    $returnData[$value] = $requestData[$value];
                }
                if (in_array($value, $dataColumns)) {
                    if ($requestData[$value]) {
                        $returnData[$value] = date('Y-m-d', strtotime($requestData[$value]));
                    }

                    //数据库存储的是完整日期时间格式
                    if (in_array($value, ['join_at', 'leave_at'])) {
                        if ($requestData[$value]) {
                            $returnData[$value] = date('Y-m-d H:i:s', strtotime($requestData[$value]));
                        }
                    }
                }
            }
        }
        // deal with picture
        $pic_columns = ['pic_id_pos', 'pic_id_neg', 'pic_edu_background', 'pic_degree', 'pic_pre_company', 'pic_user'];


        if ($datatype == 'detail') {
            foreach ($returnData as $key => $every) {
                if (in_array($key, $pic_columns)) {
                    $returnData[$key] = $every;
                }
            }
        }
        $array = [
            'id',
            'branch_bank',
            'bank',
            'gender',
            'pic_id_pos',
            'pic_user',
            'pic_edu_background',
            'pic_degree',
            'pic_pre_company'
        ];
        //用户详细信息加密
        if ($datatype == 'detail') {
            foreach ($returnData as $key => &$every) {
                if ($every) {
                    if (!in_array($key, $array)) {
                        $every = encrypt($every);
                    }
                }
            }
        }
        return $returnData;
    }


    /*
   * 获取当前用户的部门
   * @param 当前用户的对象
   * @return String | NULL 失败返回null
   */

    //获取当前用户的主部门
    public function getCurrentDept($user)
    {
        $list = [];
        $dept = [];
        $dept = Q($user, 'getPrimaryDepartment');
        if (Q($dept, 'department')) {
            $list = $dept->department->toArray();
        }
        return $list;
    }

    //通过当前部门获取它的所有父级部门
    public function getTopPid($data, $dept_id)
    {
        $arr = array();
        foreach ($data as $v) {
            if ($v['id'] == $dept_id) {
                $arr[] = $v;// $arr[$v['id']]=$v['name'];
                $arr = array_merge($this->getTopPid($data, $v['parent_id']), $arr);
            }
        }
        return $arr;
    }

    //根据当前用户的部门最顶层id
    public function getDeptTopId($user)
    {

        $topId = 1;
        $data = Department::all();
        $dept = $this->getCurrentDept($user);
        if (isset($dept['id'])) {
            $data = $this->getTopPid($data->toArray(), $dept['id']);
            if ($data) {
                $topId = $data[0]['id'];
            }
        }
        return $topId;
    }

    /*
   * 根据参数查询所有的部门
   * @param $dept_id String 传过来的部门id
   * @param $id String 当前用户的id
   * @return String | NULL 失败返回null
   */

    public function getAllDept($dept_id = '', $user)
    {
        $data = [];
        if (empty($dept_id)) {
            $dept_id = $this->getDeptTopId($user);
            $data = Department::where('id', $dept_id)->select('id', 'name', 'parent_id')->first()->toArray();
        } else {
            $data = Department::where('parent_id', $dept_id)->select('id', 'name', 'parent_id')->get()->toArray();
        }
        return $data;

    }

    //获取公司下面的所有部门层级
    /*
     * 获取所有下级
     * @param $id String 待查找的id
     * @return String | NULL 失败返回null
     */
    public function getDeptAllChild($user, $dept_id = '', $keywords = '')
    {
        $key = 'dept_user_' . Q($user, 'id');
        $data = [];
        if (Cache::has($key)) {
            $data = Cache::get($key);
        } else {
            if (empty($dept_id)) {
                $dept_id = $this->getDeptTopId($user);
            }
            $ids = $this->getChild($dept_id);
            $ids = explode(',', $ids);
            if (count($ids) > 1) {
                unset($ids[0]);
            }

            if ($keywords) {
                $departments = Department::whereIn('id', $ids)
                    ->where('name', 'like', '%' . $keywords . '%')
                    ->select('id', 'name', 'parent_id', 'deepth')->get()->toArray();
            } else {
                $departments = Department::whereIn('id', $ids)->select('id', 'name', 'parent_id', 'deepth')
                    ->get()->toArray();
            }

            if (count($departments) > 1) {
                $data = $this->treeNodes($departments, $dept_id);
            } elseif (count($departments) == 1) {
                $data = $departments;
            }

            if ($data) {
                Cache::put($key, $data, 120);
            }

        }

        return $data;


    }
//获取公司下面的所有部门层级
    /*
     * 获取所有下级
     * @param $id String 待查找的id
     * @return String | NULL 失败返回null
     */
    public function getDeptAllChildForRpc($user, $dept_id = '', $keywords = '')
    {
        $data = [];
        if (empty($dept_id)) {
            $dept_id = $this->getDeptTopId($user);
        }
        $ids = $this->getChild($dept_id);
        $ids = explode(',', $ids);
        if (count($ids) > 1) {
            unset($ids[0]);
        }

        if ($keywords) {
            $departments = Department::whereIn('id', $ids)
                ->where('name', 'like', '%' . $keywords . '%')
                ->select('id', 'name', 'parent_id', 'deepth')->get()->toArray();
        } else {
            $departments = Department::whereIn('id', $ids)->select('id', 'name', 'parent_id', 'deepth')
                ->get()->toArray();
        }

        if (count($departments) > 1) {
            $data = $this->treeNodes($departments, $dept_id);
        } elseif (count($departments) == 1) {
            $data = $departments;
        }
        return $data;
    }

    /**
     * 整理排序所有分类
     * @param  array $data 从数据库获取的分类
     * @param  integer $parentId 父id,默认一级分类
     * @return array
     */
    public function treeNodes($data, $parentId = 0)
    {
        // 用于保存整理好的分类节点
        $node = [];
        // 循环所有分类
        foreach ($data as $key => $value) {
            // 如果当前分类的父id等于要寻找的父id则写入$node数组,并寻找当前分类id下的所有子分类
            if ($parentId == $value ['parent_id']) {
                $node [$key] = $value;
                $node [$key]['select'] = 'false';
                $node [$key]['count'] = count($this->getChildUsers($value['id']));
                $node [$key]['users'] = $this->getDeptUsersDetail($value['id']);
                $node [$key] ['childer'] = array_values($this->treeNodes($data, $value ['id']));
            }
        }
        return $node;
    }

    //获取公司下面的所有部门层级
    /*
     * 获取所有下级
     * @param $id String 待查找的id
     * @return String | NULL 失败返回null
     */
    public function getDeptAllChildUsers($user, $dept_id = '', $keywords = '')
    {
        if (empty($dept_id)) {
            $dept_id = $this->getDeptTopId($user);
        }
        $ids = $this->getChild($dept_id);
        $ids = explode(',', $ids);
        unset($ids[0]);
        if ($keywords) {
            $departments = Department::whereIn('id', $ids)
                ->where('name', 'like', '%' . $keywords . '%')
                ->select('id', 'name', 'parent_id', 'deepth')->get()->toArray();
        } else {
            $departments = Department::whereIn('id', $ids)->select('id', 'name', 'parent_id', 'deepth')
                ->get()->toArray();
        }
        $socialSecurityRelation = SocialSecurityRelation::get();
        $data = [];

        if (isset($departments)) {
            foreach ($departments as $key => &$every) {
                $every['cur_path'] = Department::getDeptPath($every['id']);
                $getChildUsers = $this->getDeptChildUsers($every['id'], $socialSecurityRelation);
                $every['count'] = count($getChildUsers);
                $every['users'] = $getChildUsers;
                $departmentInfo = Department::where('id', '=', $every['parent_id'])->first();
                $every['parent_name'] = $departmentInfo ? $departmentInfo->name : '';
                $every['parent_path'] = Department::getDeptPath($every['parent_id']);
            }
        }
        if (count($departments) > 1) {
            $data = $this->treeNode($departments, $dept_id);
        } elseif (count($departments) == 1) {
            $data = $departments;
        }
        return $data;


    }

    //根据传参部门id获取该部门所有用户
    public function getDeptUsers($dept_id = '')
    {
        $dataUsers = [];
        $detpData = Department::where('id', $dept_id)->first();
        if ($detpData) {
            $dataUsers = $detpData->userInfo->toArray();
        }

        return $dataUsers;
    }

    //根据传参部门id获取该部门所有用户详情
    public function getDeptUsersDetail($dept_id = '')
    {
        $dataUsers = [];
        $detpData = Department::where('id', $dept_id)->first();
        if ($detpData) {
            $department = Department::getDeptPath($dept_id);
            $dataUsers = $detpData->userInfoPrimary->each(function ($item, $key) use ($department, $dept_id) {
                $item['select'] = 'false';
                $item['department'] = $department;
                $item['depar_id'] = $dept_id;
            })->toArray();
        }
        return $dataUsers;
    }

    /**
     * 整理排序所有分类
     * @param  array $data 从数据库获取的分类
     * @param  integer $parentId 父id,默认一级分类
     * @return array
     */
    public function treeNode($data, $parentId = 0)
    {
        // 用于保存整理好的分类节点
        $node = [];
        // 循环所有分类
        foreach ($data as $key => $value) {
            // 如果当前分类的父id等于要寻找的父id则写入$node数组,并寻找当前分类id下的所有子分类
            if ($parentId == $value ['parent_id']) {
                $node [$key] = $value;
                $node [$key]['select'] = 'false';
                $node [$key] ['childer'] = array_values($this->treeNode($data, $value ['id']));
            }
        }
        return $node;
    }


    /*
     * 获取所有下级
     * @param $id String 待查找的id
     * @return String | NULL 失败返回null
     */
    public function getChild($dept_id)
    {
        //$this->dept = app()->make(DepartmentRespository::class);
        $res = Department::where('parent_id', $dept_id)->select('id', 'name')->get()->toArray();
        $ids = $dept_id;
        if (!empty($res)) {
            foreach ($res as $key => $val) {
                $ids .= "," . $this->getChild($val['id']);
            }
        }

        return $ids;
    }

    /*
     * 获取所有下级
     * @param $id String 待查找的id
     * @return String | NULL 失败返回null
     */
    public function getVoteChild($dept_id)
    {
        //$this->dept = app()->make(DepartmentRespository::class);
        $res = Department::whereIn('parent_id', $dept_id)->select('id', 'name')->get()->toArray();
        $ids = implode(',', $dept_id);
        if (!empty($res)) {
            foreach ($res as $key => $val) {
                $ids .= "," . $this->getChild($val['id']);
            }
        }
        return $ids;

    }

    /*
    * 获取部门下以及子部门下的所有员工
    * @param $id String 待查找的部门id
    * @return String | NULL 失败返回null
    */
    public function getChildUsers($dept_id)
    {

        $ids = $this->getChild($dept_id);
        $ids = explode(',', $ids);
        $userIds = DepartUser::whereIn('department_id', $ids)->distinct()->pluck('user_id')->toArray();
        $uses = User::whereIn('id', $userIds)
            ->where("status", User::STATUS_JOIN)
            ->distinct()->get(['id as user_id', 'chinese_name', 'position', 'employee_num'])->toArray();

        return $uses;
    }

    /*
   * 获取RPC部门下以及子部门下的所有员工
   * @param $id String 待查找的部门id
   * @return String | NULL 失败返回null
   */
    public function getRpcChildUsers($dept_id)
    {
        $ids = $this->getChild($dept_id);
        $ids = explode(',', $ids);
        $userIds = DepartUser::with(['user' => function ($query) {
            $query->select(['id', 'chinese_name', 'employee_num', 'avatar', 'join_at', 'position']);
        }])->whereIn('department_id', $ids)->distinct()->get(['user_id'])->toArray();
        return $userIds;
    }

    /*
    * 获取部门下以及子部门下的所有员工
    * @param $id String 待查找的部门id
    * @return String | NULL 失败返回null
    */
    public function getDeptChildUsers($dept_id, $socialSecurityRelation)
    {
        $relationUserId = array_column($socialSecurityRelation->toArray(), 'user_id');
        $ids = $this->getChild($dept_id);
        $ids = explode(',', $ids);
        $userIds = DepartUser::with(['user' => function ($query) use ($relationUserId) {
            $query->select(['id', 'chinese_name'])->whereNotIn('id', $relationUserId);
        }])->whereIn('department_id', $ids)->distinct()->pluck('user_id')->toArray();
        $uses = User::whereIn('id', $userIds)
            ->where("status", User::STATUS_JOIN)
            ->distinct()->get(['id as user_id', 'chinese_name', 'position', 'employee_num'])->toArray();

        return $uses;
    }

    /*
    * 获取部门下以及子部门下的所有员工
    * @param $id String 待查找的部门id
    * @return String | NULL 失败返回null
    */
    public function getVoteChildUsers($dept_id)
    {
        $ids = $this->getVoteChild($dept_id);
        $ids = explode(',', $ids);
        $userIds = DepartUser::with('user')->whereIn('department_id', $ids)->distinct()->pluck('user_id')->toArray();
        $uses = User::whereIn('id', $userIds)
            ->where("status", User::STATUS_JOIN)
            ->distinct()->get(['id as user_id', 'chinese_name', 'position', 'employee_num', 'avatar'])->toArray();

        return $uses;
    }

    /*
  * 根据不同的用户职级获取不同的投票次数
  * @param $id String 当前用户的$id
  *  @param $v_id String 参与的投票$v_id
  * @return String | NULL 失败返回null
  */
    function getVoteCount($id, $v_id = 0)
    {
        $count = 1;
        $votes = app()->make(VoteRepository::class);
        $config = $votes->find($v_id);//获取投票主题
        if (!$config) {
            return false;
        }
        if ($config->getVoteRule) {//获取投票主题的规则
            if ($config->getVoteRule->getUserRank) {//获取职级的权重
                $vote_number = $config->getVoteRule->vote_number;
                $initLevel = $config->getVoteRule->getUserRank->level;//
            }
        }
        $users = User::find($id, ['id', 'name']);//获取当前用户的信息
        if ($users) {
            if (Q($users, 'profile', 'job_grade')) {
                if (Q($users, 'profile', 'userRank')) {
                    if (Q($users, 'profile', 'userRank', 'level') <= $initLevel) {//权重越靠前越大
                        $count = $vote_number;
                    }
                }
            }
            unset($users->profile);

            $users->count = $count;
            $users->passing_rate = $config->getVoteRule->passing_rate;
        }
        return $users->toArray();

    }

    public function getAllDepartmentList()
    {
        $list = Department::where('parent_id', '>', 0)->get(['id', 'name', 'parent_id']);
        return $list;
    }


    public function getAllDepartmentUserList()
    {
        $STATUS_LEAVE = User::STATUS_JOIN;
        $list = DepartUser::leftJoin('users as b', 'department_user.user_id', 'b.id')->where('b.status', $STATUS_LEAVE)->get(['b.id', 'b.chinese_name', 'b.avatar', 'department_user.department_id']);
        return $list;
    }

    public function getUserFirstAndSecond($user_id)
    {
        $arr = [];
        $tops = WorkflowUserService::fetchUserPrimaryDeptPath($user_id);
        $departments = explode('/', $tops);
        $result['top_department'] = $departments['0'] ?? '不存在';
        $result['first_department'] = $departments['1'] ?? $departments['0'];
        $result['second_department'] = $departments['2'] ?? $result['first_department'];
        $user1 = Department::where('name', $result['top_department'])->first();
        $user2 = Department::where('name', $result['first_department'])->first();
        $user3 = Department::where('name', $result['second_department'])->first();

        $arr = [
            'top_id' => Q($user1, 'id'),
            'top_department' => $result['top_department'],
            'first_id' => Q($user2, 'id'),
            'first_department' => $result['first_department'] ? $result['first_department'] : '',
            'second_id' => Q($user3, 'id'),
            'second_department' => $result['second_department'] ? $result['second_department'] : ''
        ];
        return $arr;
    }
}
