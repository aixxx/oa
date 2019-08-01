<?php

namespace App\Repositories;

use App\Models\Contract\Contract;
use App\Models\Department;
use App\Models\DepartUser;
use App\Models\Message\Message;
use App\Models\Report;
use App\Models\User;
use App\Models\Attendance\AttendanceVacation;
use App\Constant\ConstFile;
use App\Models\UsersDetailInfo;
use App\Models\Workflow\Entry;
use App\Models\Workflow\Flow;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;
use Mockery\Exception;
use App\Services\WorkflowUserService;

/**
 * Class UsersRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class RosterRepository extends ParentRepository
{
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
        $this->userlog = \app()->make('userlog');
//        $this->middleware('afterlog')->only('store', 'update', 'destroy');
    }

    public function holiday($request)
    {
        try {
            $userId = $request->get('userId');
            if (empty($userId)) {
                throw new Exception('参数是否为空');
            }
            $holidays = [];
            $holiday = AttendanceVacation::where('user_id', $userId)->first();
            if ($holiday) {
                $holidays = $holiday->toArray();
            }

            foreach ($holidays as $key => $val) {
                if ($key != 'vacation_id') {
                    if ($key != 'user_id') {
                        $holidays[$key] = $this->getNumTime($val);
                    }
                }
            }

            $report_count = Report::where('user_id', $userId)->where(function ($query) {
                $query->whereNull('deleted_at')->orWhere('deleted_at', 0);
            })->count();

            $flowIds = Flow::query()->whereIn('type_id', [Flow::TYPE_ATTENTION])->pluck('id');

            $applyCnt = Entry::query()->where('user_id', '=', $userId)
                ->whereIn('flow_id', $flowIds->toArray())->count();

            $this->data = [
                "holiday" => isset($holidays) && !empty($holiday) ? $holidays : AttendanceVacation::$holiday,
                "report_count" => $report_count,
                "apply_cnt" => $applyCnt
            ];
        } catch (Exception $e) {
            $this->message = $e->getMessage();
            $this->code = $e->getCode();
        }
        return $this->returnApiJson();
    }

    public function getNumTime($hours)
    {
        if ($hours < 8) {
            $arr = $hours . "小时";
        } else {
            $day = floor($hours / 8) . "天";
            $hours = $hours % 8 . "小时";
            $arr = $day . $hours;
        }
        return $arr;
    }

    public function userNumber()
    {
        try {
            $user = User::where("status", User::STATUS_JOIN)->count();//在职

            $fullTime = UsersDetailInfo::where('user_status', 1)
                ->whereHas('user', function ($query) {
                    $query->where("status", User::STATUS_JOIN);
                })->count();//全职

            $internship = UsersDetailInfo::where('user_status', 6)
                ->whereHas('user', function ($query) {
                    $query->where("status", User::STATUS_JOIN);
                })->count();//实习

            $partTime = UsersDetailInfo::where('user_status', 2)
                ->whereHas('user', function ($query) {
                    $query->where("status", User::STATUS_JOIN);
                })->count();//兼职

            $this->data = [
                "user" => $user,
                'fullTime' => $fullTime,
                'internship' => $internship,
                'partTime' => $partTime
            ];
        } catch (Exception $e) {
            $this->message = $e->getMessage();
            $this->code = $e->getCode();
        }
        return $this->returnApiJson();
    }

    /**
     * @在职人员
     * @param $request
     * @param $users
     * @return false|\Illuminate\Http\JsonResponse|string
     */
    public function rosterShows()
    {
        try {
            $first = ['users.id', 'chinese_name', 'avatar', 'employee_num', 'join_at', 'position', 'mobile'];
            //联表
            $result = User::with(['primaryDepartUser', 'primaryDepartUser.department', 'company'])
                ->where("status", User::STATUS_JOIN)
                ->select($first)
                ->get();
            //数据处理
            $prefix = config('employee.employee_num_prefix');
            if (!empty($result)) {
                $userInfo = $result->toArray();
                foreach ($userInfo as $key => &$item) {
                    $item['avatar'] = $item['avatar'] ?? config('user.const.avatar');
                    $item['employee_num'] = $prefix . $item['employee_num'];
                    $userInfo[$key]['department'] = $item['primary_depart_user']['department']['name'];
                    $userInfo[$key]['company'] = $item['company']['name'];
                    unset($item['primary_depart_user']);
                }
            }

            $userInfo = isset($userInfo) ? $userInfo : "";
        } catch (Exception $e) {
            $this->message = $e->getMessage();
            $this->code = $e->getCode();
            return returnJson($this->message, $this->code);
        }
        return returnJson(
            ConstFile::API_RESPONSE_SUCCESS_MESSAGE_DATA,
            ConstFile::API_RESPONSE_SUCCESS,
            ['userInfo' => $userInfo]
        );
    }


    /**
     * @deprecated 类型筛选
     * @param $request
     * @return false|\Illuminate\Http\JsonResponse|string
     */
    public function shows($request)
    {
        try {
            //接值
            $user_type = $request->get('user_type');
            $user_status = $request->get('user_status');
            $where = [];
            if (!empty($user_type)) {
                $where['user_type'] = $user_type;
            }
            if (!empty($user_status)) {
                $where['user_status'] = $user_status;
            }
            $user = UsersDetailInfo::with(['user', 'user.primaryDepartUser', 'user.primaryDepartUser.department'])
                ->whereHas('user', function ($query) {
                    $query->where("status", User::STATUS_JOIN);
                })
                ->where($where)
                ->get();

            if (!empty($user)) {
                $userInfo = $user->toArray();
                $data = [];
                foreach ($userInfo as $key => &$item) {
                    if (!empty($item['user'])) {
                        $data[$key] = $item['user'];
                        $data[$key]['department'] = $item['user']['primary_depart_user']['department']['name'];
                        $data[$key]['avatar'] = $item['user']['avatar'] ?? config('user.const.avatar');
                        unset($data[$key]['primary_depart_user']);
                    }
                }
            }
            $data = isset($data) ? $data : [];

        } catch (Exception $e) {
            $this->message = $e->getMessage();
            $this->code = $e->getCode();
            return returnJson($this->message, $this->code);
        }
        return returnJson(ConstFile::API_RESPONSE_SUCCESS_MESSAGE_DATA, ConstFile::API_RESPONSE_SUCCESS, $data);
    }

    /**
     * @deprecated 直属部门人员筛选
     * @param $request
     * @return false|\Illuminate\Http\JsonResponse|string
     */
    public function show($request)
    {
        try {
            //接值
            $detail = $request->all();
//            $detail = ['61'];
            if (empty($detail) && count($detail) <= 0) {
                return returnJson(ConstFile::API_PARAMETER_MISS, ConstFile::API_RESPONSE_FAIL);
            }
            $user = Department::with(['userInfo'])->whereIn('id', $detail)->get();

            $userInfo = $this->departUserDispose($user);
        } catch (Exception $e) {
            $this->message = $e->getMessage();
            $this->code = $e->getCode();
            return returnJson($this->message, $this->code);
        }
        return returnJson(ConstFile::API_RESPONSE_SUCCESS_MESSAGE_DATA, ConstFile::API_RESPONSE_SUCCESS, $userInfo);
    }

    /**
     * @deprecated 全部部门人员
     * @param $request
     * @return false|\Illuminate\Http\JsonResponse|string
     */
    public function showAll($request)
    {
        try {
            //接值
            $detail = $request->all();
            if (empty($detail['id']) && count($detail['id']) <= 0) {
                return returnJson(ConstFile::API_PARAMETER_MISS, ConstFile::API_RESPONSE_FAIL);
            }
            $detailId = $detail['id'];
            $idAll = $this->getChilds($detailId);
            //根据顶级部门id获取所有人员
            $user = Department::with(['userInfo'])->whereIn('id', $idAll)->get();

            $userId = isset($detail['userId']) ? $detail['userId'] : [];

            $userInfo = $this->departUserDispose($user, $userId);

        } catch (Exception $e) {
            $this->message = $e->getMessage();
            $this->code = $e->getCode();
            return returnJson($this->message, $this->code);
        }
        return returnJson(ConstFile::API_RESPONSE_SUCCESS_MESSAGE_DATA, ConstFile::API_RESPONSE_SUCCESS, $userInfo);
    }

    /**
     * @deprecated 基本模糊搜索
     * @param $request
     * @param $users
     * @return false|\Illuminate\Http\JsonResponse|string
     */
    public function search($request)
    {
        try {
            $keyword = $request->get('keyword');
            if (empty($keyword)) {
                return returnJson(ConstFile::API_PARAMETER_MISS, ConstFile::API_RESPONSE_FAIL);
            }

            $first = ['users.id', 'chinese_name', 'avatar', 'employee_num', 'join_at', 'position'];
            //联表
            $result = User::with(['primaryDepartUser', 'primaryDepartUser.department'])
                ->whereHas('primaryDepartUser.department', function ($query) use ($keyword) {
                    $query->where('name', 'like', "%" . $keyword . "%");
                })
                ->where("status", User::STATUS_JOIN)
                ->orWhere('employee_num', 'like', "%" . $keyword . "%")
                ->orWhere('position', 'like', "%" . $keyword . "%")
                ->orWhere('chinese_name', 'like', "%" . $keyword . "%")
                ->select($first)
                ->get();

            $userInfo = $this->userDepartDispose($result);

        } catch (Exception $e) {
            $this->message = $e->getMessage();
            $this->code = $e->getCode();
            return returnJson($this->message, $this->code);
        }
        return returnJson(ConstFile::API_RESPONSE_SUCCESS_MESSAGE_DATA, ConstFile::API_RESPONSE_SUCCESS, $userInfo);
    }


    /**
     * @deprecated 人员搜索
     * @param $request
     * @param $users
     * @return false|\Illuminate\Http\JsonResponse|string
     */
    public function userSearch($request)
    {
        try {
            $userIdAll = $request->all();
            if (empty($userIdAll['id']) && count($userIdAll['id']) <= 0) {
                return returnJson(ConstFile::API_PARAMETER_MISS, ConstFile::API_RESPONSE_FAIL);
            }

            $userAll = $userIdAll['id'];
            $first = ['users.id', 'chinese_name', 'avatar', 'employee_num', 'join_at', 'position', 'mobile'];
            //联表
            $result = User::with(['primaryDepartUser', 'primaryDepartUser.department'])
                ->whereIn('id', $userAll)
                ->where("status", User::STATUS_JOIN)
                ->select($first)
                ->get();

            $userInfo = $this->userDepartDispose($result);
        } catch (Exception $e) {
            $this->message = $e->getMessage();
            $this->code = $e->getCode();
            return returnJson($this->message, $this->code);
        }
        return returnJson(ConstFile::API_RESPONSE_SUCCESS_MESSAGE_DATA, ConstFile::API_RESPONSE_SUCCESS, $userInfo);
    }

    /**
     * @deprecated 根据用户信息查询部门信息
     * @param $userInfo
     * @return array
     */
    private function userDepartDispose($result)
    {
        if (!empty($result) && count($result) > 0) {
            $userInfo = $result->toArray();
            foreach ($userInfo as $key => &$item) {
                $item['avatar'] = $item['avatar'] ?? config('user.const.avatar');
                if (!empty($item['primary_depart_user']['department'])) {
                    $userInfo[$key]['department'] = $item['primary_depart_user']['department']['name'];
                } else {
                    $userInfo[$key]['department'] = '';
                }
                unset($item['primary_depart_user']);
            }
        }
        return isset($userInfo) ? $userInfo : [];
    }

    /**
     * @deprecated 根据部门信息查询用户信息
     * @param $userInfo
     * @return array
     */
    private function departUserDispose($user, $userId = [])
    {
        if (!empty($user) && count($user) > 0) {
            $users = $user->toArray();
            //数据出来
            $userInfo = [];
            foreach ($users as $key => $val) {
                foreach ($val['user_info'] as $keys => $value) {
                    if (!in_array($value['id'], $userId)) {
                        $value['avatar'] = $value['avatar'] ?? config('user.const.avatar');
                        $value['department'] = $val['name'];
                        $value['depar_id'] = $val['id'];
                        unset($value['pivot']);
                        $userInfo[] = $value;
                    }
                }
            }
        }
        return isset($userInfo) ? $userInfo : [];
    }


    /**
     * @deprecated  个人文档
     * @param string $id
     * @return false|\Illuminate\Http\JsonResponse|string
     */
    public function userFilesQuery($id = "")
    {
        if (empty($id)) {
            return returnJson(ConstFile::API_PARAMETER_MISS, ConstFile::API_RESPONSE_FAIL);
        }
        $first = [
            'id', 'chinese_name',
            'email', 'avatar',
            'gender', 'mobile',
            'position', 'employee_num',
            'work_address', 'join_at',
            'is_positive',
            'is_wage',
            'contract_status',
            'is_person_perfect',
            'is_card_perfect',
            'is_edu_perfect',
            'is_pic_perfect',
            'is_family_perfect',
            'is_urgent_perfect'
        ];

        //获取信息
        $user = User::with('detail', 'workClass', 'growth', "contract")
            ->where('id', '=', $id)->where("status", User::STATUS_JOIN)->first($first);

        if (empty($user)) {
            return returnJson(ConstFile::API_RECORDS_NOT_EXIST_MESSAGE, ConstFile::API_RESPONSE_FAIL);
        }
        $user->employee_num = $user->getPrefixEmployeeNum();

        $tops = WorkflowUserService::fetchUserPrimaryDeptPath($id);
        $tops = explode('/', $tops);

        $users = [];
        //出来信息
        $detail = UsersDetailInfo::decryptSelectDatas($user->detail);
        $users['id'] = judge($user->id);
        $users['avatar'] = $user->fetchAvatar();
        $users['chinese_name'] = judge($user->chinese_name);
        $users['gender'] = judge($user->gender);
        $users['email'] = judge($user->email);
        $users['mobile'] = judge($user->mobile);
        $users['position'] = judge($user->position);
        $users['department'] = isset($tops[1]) ? $tops[1] : $tops[0];
        $users['employee_num'] = judge($user->employee_num);
        $users['work_address'] = judge($user->work_address);
        $users['is_positive'] = judge($user->is_positive);
        $users['is_wage'] = judge($user->is_wage);
        $users['contract_status'] = judge($user->contract_status);
        $users['join_at'] = isset($user->join_at) ? date('Y-m-d', strtotime($user->join_at)) : "";
        $users['note'] = judge($detail['note']);
        $users['born_time'] = judge($detail['born_time']);
        $users['probation'] = judge($detail['probation']);
        $users['after_probation'] = judge($detail['after_probation']);
        $users['user_type'] = judge($detail['user_type']);
        $users['user_status'] = judge($detail['user_status']);
        $users['growth'] = isset($user->growth) ? $user->growth : "";
        if (!empty($user->contract && count($user->contract) > 0)) {
            $data = $user->contract->toArray();

            if (!empty($user->join_at)) {
                //试用期
                $probation
                    = date("Y-m-d", strtotime("+" . $data[0]['probation'] .
                    "months", strtotime($user->join_at)));

                $users['probation'] = $user->join_at . '-' . $probation;
                //转正期
                $users['positive']
                    = $probation . '-' . date("Y-m-d", strtotime("+" . $data[0]['contract'] .
                        "years", strtotime($probation)));
            }
        } else {
            $users['probation'] = "";
            $users['positive'] = "";
        }
        $users['perfect'] = array_sum(array(
            $user->is_person_perfect,
            $user->is_card_perfect,
            $user->is_edu_perfect,
            $user->is_pic_perfect,
            $user->is_family_perfect,
            $user->is_urgent_perfect
        ));
        $users['turn_positive'] = 0;

        $users['turn_renewal'] = 0;
        if ($user->is_wage == User::STATUS_ISNO_WAGE && $user->is_positive == User::STATUS_ISNO_POSITIVE) {
            $contract = Contract::where(['user_id' => $user->id, 'status' => Contract::CONTRACT_STATUS_TWO])->orderBy('version', 'desc')->first();
            if ($contract) {
                $expireAt = Carbon::createFromFormat('Y-m-d H:i:s', $contract->entry_at)->addMonths(Contract::$contractMonths[$contract->probation])->timestamp;
                $surplusDays = ($expireAt - time()) / ConstFile::HOUR;
                if ($surplusDays <= 15) {
                    $users['turn_positive'] = 1;
                }
                $contractEndAt = Carbon::createFromFormat('Y-m-d H:i:s', $contract->contract_end_at)->timestamp;
                $turnRenewal = ($contractEndAt - time()) / ConstFile::HOUR;
                if ($turnRenewal <= 15) {
                    $users['turn_renewal'] = 1;
                }
            }
        }
        return returnJson(ConstFile::API_RESPONSE_SUCCESS_MESSAGE_DATA, ConstFile::API_RESPONSE_SUCCESS, $users);
    }

    /**
     * @deprecated 个人档案不完善信息统计列表
     * @return false|\Illuminate\Http\JsonResponse|string
     */
    public function userNoPerfect()
    {
        try {
            $first = [
                'id',
                'is_person_perfect',
                'is_card_perfect',
                'is_edu_perfect',
                'is_pic_perfect',
                'is_family_perfect',
                'is_urgent_perfect'
            ];
            $perfect = $result = User::where("status", User::STATUS_JOIN)->select($first)->get();
            $isNoPerfect = [];
            $perfects = $perfect->toArray();
            foreach ($perfects as $key => $value) {
                unset($value['id']);
                if (array_sum($value) < 100) {
                    $isNoPerfect[$key]['id'] = $perfects[$key]['id'];
                    $isNoPerfect[$key]['is_perfect'] = array_sum($value);
                }
            }
        } catch (Exception $e) {
            $this->message = $e->getMessage();
            $this->code = $e->getCode();
            return returnJson($this->message, $this->code);
        }
        return returnJson(ConstFile::API_RESPONSE_SUCCESS_MESSAGE_DATA, ConstFile::API_RESPONSE_SUCCESS, $isNoPerfect);
    }

    /**
     * @param array $data
     * @return false|\Illuminate\Http\JsonResponse|string
     */
    public function sendImprovingDataMsg(array $data)
    {
        try {
            if (!isset($data['uid']) || empty($data['uid']) || !isset($data['content']) || empty($data['content'])) {
                throw new Exception(sprintf('请求参数错误：用户ID不能为空'));
            }
            $messageData = [];
            foreach ($data['uid'] as $key => $val) {
                $messageData[$key]['receiver_id'] = $val;
                $messageData[$key]['sender_id'] = \Auth::id();
                $messageData[$key]['content'] = $data['content'];
                $messageData[$key]['relation_id'] = $val;
                $messageData[$key]['type'] = Message::MESSAGE_IMPROVING_DATA;
                $messageData[$key]['created_at'] = date('Y-m-d H:i:s');
            }
            Message::query()->insert($messageData);
        } catch (Exception $e) {
            return returnJson($e->getMessage(), ConstFile::API_RESPONSE_FAIL);
        }
        return returnJson(ConstFile::API_RESPONSE_SUCCESS_MESSAGE, ConstFile::API_RESPONSE_SUCCESS);
    }

    /**
     * @param array $data
     * @return false|\Illuminate\Http\JsonResponse|string
     */
    public function sendTurnPositiveMsg(array $data)
    {
        try {
            if (!isset($data['uid']) || empty($data['uid'])) {
                throw new Exception(sprintf('请求参数错误：用户ID不能为空'));
            }
            Message::addMessage($data['uid'], \Auth::id(), '您的转正时间到了，请即时提交转正申请', $data['uid'], Message::MESSAGE_TYPE_TURN_POSITIVE);
        } catch (Exception $e) {
            return returnJson($e->getMessage(), ConstFile::API_RESPONSE_FAIL);
        }
        return returnJson(ConstFile::API_RESPONSE_SUCCESS_MESSAGE, ConstFile::API_RESPONSE_SUCCESS);
    }

    /**
     * @deprecated 处理数据
     * @param $dept_id
     * @return array
     */
    private function getChilds($dept_id)
    {
        $arr = Department::whereIn('parent_id', $dept_id)->select('id')->get()->toArray();
        $idAll = array_column($arr, 'id');
        $ids = [];
        if (!empty($idAll) && count($idAll) > 0) {
            $ids = array_unique(array_merge($this->getChilds($idAll), $idAll, $dept_id));
        } else {
            $ids = $dept_id;
        }
        return $ids;
    }

}
