<?php

namespace App\Services;

use App\Models\User;
use App\Models\Department;
use App\Models\DepartUser;
use App\Models\Workflow\Entry;
use App\Models\Workflow\EntryData;
use App\Models\Workflow\Flow;
use App\Models\DepartmentMapCentre;
use App\Models\UserBankCard;
use Illuminate\Support\Facades\Auth;
use DevFixException;

class WorkflowUserService
{
    //员工状态
    const USER_STATUS_IN = 1;
    const USER_STATUS_OUT = 9;

    //员工是否部门领导
    const USER_IS_DEPARTMENT_LEADER = 1;
    const USER_IS_NOT_DEPARTMENT_LEADER = 0;

    //部门是否为主部门
    const USER_IS_MASTER_DEPARTMENT = 1;
    const USER_IS_NOT_MASTER_DEPARTMENT = 0;

    /**
     * 获取用户主部门信息
     *
     * @param $userId
     * @return array
     * @throws DevFixException
     */
    public static function fetchUserMainDepartmentInfo($userId)
    {
        $user = User::with('departUser', 'departUser.department', 'detail')
            ->where('id', $userId)
            ->first();

        if (!$user || $user->status != self::USER_STATUS_IN) {
            return [];
        }
        $departmentUser = $user->departUser->toArray();
        $department = [];

        foreach ($departmentUser as $d) {
            if ($d['is_primary'] == self::USER_IS_MASTER_DEPARTMENT) {
                $department = [
                    'id' => $d['department']['id'],
                    'name' => $d['department']['name'],
                    'parent_id' => $d['department']['parent_id'],
                    'is_leader' => $d['is_leader'],
                    'id_card_no' => Q($user, 'detail', 'id_number'),
                ];
            }
        }

        if (!isset($department['id'])) {
            throw new DevFixException('没有对应的部门');
        }

        $deepList = self::fetchDeepList($department['parent_id']);
        $childLevelList = self::fetchChildLevel($deepList);

        $department['level'] = count($childLevelList) + 1;
        unset($department['parent_id']);
        $masterBankCardInfo = UserBankCard::getByTypeAndUser($userId, UserBankCard::BANK_CARD_TYPE_MAIN)->toArray();
        $masterBankCardInfo = $masterBankCardInfo[0] ?? '';
        if (empty($masterBankCardInfo)) {
            //throw new DevFixException('没有对应的银行卡主卡');
            $department['id_card_no'] = 'idcardxxxxxxxxxxxxxxxxxx';
            $department['bank_card_no'] = 'banknoxxxxxxxxxxxxxxxxxx';
            $department['payee_bank_branch'] = '银行地址';
            $department['payee_bank_subbranch'] = '支行地址';
            $department['payee_bank_province'] = '银行所属省份';
            $department['payee_bank_city'] = '银行所属城市';
        } else {
            $department['id_card_no'] = !empty($department['id_card_no']) ? decrypt($department['id_card_no']) :
                null;
            $department['bank_card_no'] = !empty($masterBankCardInfo['card_num']) ?
                decrypt($masterBankCardInfo['card_num']) : null;
            $department['payee_bank_branch'] = !empty($masterBankCardInfo['bank']) ?
                decrypt($masterBankCardInfo['bank']) : null;
            $department['payee_bank_subbranch'] = !empty($masterBankCardInfo['branch_bank']) ?
                decrypt($masterBankCardInfo['branch_bank']) : null;
            $department['payee_bank_province'] = !empty($masterBankCardInfo['bank_province']) ?
                decrypt($masterBankCardInfo['bank_province']) : null;
            $department['payee_bank_city'] = !empty($masterBankCardInfo['bank_city']) ?
                decrypt($masterBankCardInfo['bank_city']) : null;
        }

        return $department;
    }

    /**
     * 获取用户部门层级关系
     * 注意：部门不一定有领导，某个层级的部门不一定存在。
     *
     * @param $userId
     * @param $deep
     *
     * @return array
     */
    public static function fetchUserDepartmentDeepInfo($userId, $deep)
    {
        $departmentInfo = self::fetchMasterDepartmentInfo($userId)->toArray();

        if (!isset($departmentInfo['0']) || empty($departmentInfo['0'])) {
            throw new DevFixException('没有取到用户对应的部门信息');
        }

        $parentId = $departmentInfo['0']['parent_id'];

        if (0 == $parentId) {
            throw new DevFixException('上级部门不能为顶级部门');
        }

        $departments = self::fetchDeepList($parentId, $departmentInfo);

        if (empty($departments)) {
            throw new DevFixException('没有上级部门');
        }

        $deepInfo = self::fetchChildLevel($departments);

        $flag = false;
        foreach ($deepInfo as $d) {
            if ($flag || $deep == $d['level']) {
                if (!empty($d['leaders'])) {
                    return $d['leaders'];
                } else {
                    $flag = true;
                }
            }
        }

        if (empty($d['leaders'])) {
            throw new DevFixException('上级部门没有审批主管');
        }

        return $d['leaders'];
    }

    /**
     * 获取新的审批关系
     * @param $userId
     * @param $level
     * @return mixed
     * @throws DevFixException
     */

    public static function fetchLeaderInfo($userId, $level)
    {
        $departmentInfo = self::fetchMasterDepartmentInfo($userId);
        if ($departmentInfo->isEmpty()) {
            throw new DevFixException('没有取到用户对应的部门信息');
        }

        $parentId = $departmentInfo->first()->parent_id;
        if (0 == $parentId) {
            throw new DevFixException('上级部门不能为顶级部门');
        }
        $deepList = $departmentInfo->first()->is_leader ? [] : $departmentInfo->toArray();
        $departments = self::fetchDeepList($parentId, $deepList);

        if (empty($departments)) {
            throw new DevFixException('没有上级部门');
        }

        $deepInfo = self::fetchChildLevel($departments);
        $flag = false;
        $level--;
        foreach ($deepInfo as $k => $d) {
            if ($flag || $level == $k) {
                if (!empty($d['leaders'])) {
                    return $d['leaders'];
                } else {
                    $flag = true;
                }
            }
        }

        if (empty($d['leaders'])) {
            throw new DevFixException('上级部门没有审批主管');
        }

        return $d['leaders'];
    }

    /**
     * 获取用户汇报关系
     *
     * @param $userId
     * @param $level
     *
     * @return array|string
     */
    public static function fetchUserReportRelationShip($userId, $level)
    {
        $userInfo = User::where('status', self::USER_STATUS_IN)
            ->where('id', $userId)->firstOrFail();

        if (1 == $level) {
            return $userInfo->superior_leaders;
        }
        $temp[] = $userInfo->superior_leaders;

        $reportList = self::fetchReportList($userInfo->superior_leaders, $temp);
        array_pop($reportList);

        return $reportList[$level - 1] ?? array_pop($reportList);
    }

    /**
     * 递归获取某一用户的部门层级树
     *
     * @param       $departmentId
     * @param array $deepList
     *
     * @return array
     */
    public static function fetchDeepList($departmentId, & $deepList = [])
    {
        $temp = [];
        if (0 != $departmentId) {
            $departmentInfo = Department::find($departmentId);
            //判断是否为空
            if ($departmentInfo) {
                $temp['id'] = $departmentId;
                $temp['name'] = $departmentInfo->name;
                $temp['parent_id'] = $departmentInfo->parent_id;
                array_push($deepList, $temp);
                self::fetchDeepList($departmentInfo['parent_id'], $deepList);
            }
        }
        return $deepList;
    }

    /**
     * 递归获取某一用户的汇报关系层级树
     *
     * @param       $superior_leaders
     * @param array $reportList
     *
     * @return array
     */

    public static function fetchReportList($superior_leaders, & $reportList = [])
    {
        if (0 != $superior_leaders) {
            $userInfo = User::where('status', self::USER_STATUS_IN)
                ->where('id', $superior_leaders)
                ->first();
            array_push($reportList, $userInfo['superior_leaders']);
            self::fetchReportList($userInfo['superior_leaders'], $reportList);
        }

        return $reportList;
    }

    /**
     * 获取用户主部门路径
     *
     * @param $userId
     *
     * @return string
     */
    public static function fetchUserPrimaryDeptPath($userId, $type = 0)
    {
        $user = User::find($userId);
        $primaryDept = '';
        if (User::STATUS_JOIN == $user->status) {
            if (Q($user, 'getPrimaryDepartment')) {
                $primaryDept = $user->getPrimaryDepartment;
            }
        } else {
            $primaryDept = $user->getLeaveStaffPrimaryDepartment;
        }
        if ($primaryDept) {
            $deptList = self::fetchDeepList($primaryDept->department_id);
            $deptList = array_reverse($deptList);
            if($type){
                return $deptList;
            }else{
                return implode('/', array_column($deptList, 'name'));
            }
        }
    }

    /**
     * 获取用户一级和二级部门
     *
     * @param $userId
     *
     * @return string
     */
    public static function fetchDepartmentFirstAndSecond($userId)
    {
        $path = self::fetchUserPrimaryDeptPath($userId);
        $departments = explode('/', $path);
        array_shift($departments);
        $result['first_department'] = $departments['0'] ?? '不存在';
        $result['second_department'] = $departments['1'] ?? $departments['0'];
        return $result;
    }

    /**
     * 根据部门id获取领导信息
     *
     * @param $departmentId
     *
     * @return array
     */
    public static function fetchDepartmentMasterInfo($departmentId)
    {
        $leaderInfo = DepartUser::with('department', 'user')
            ->where('department_id', $departmentId)
            ->where('is_leader', self::USER_IS_DEPARTMENT_LEADER)
            ->get()->toArray();
        $result = $temp = [];
        if (!empty($leaderInfo)) {
            foreach ($leaderInfo as $l) {
                if ($l['user'] && $l['user']['status'] == self::USER_STATUS_IN) {
                    $temp['id'] = $l['department']['id'];
                    $temp['name'] = $l['department']['name'];
                    $temp['user_id'] = $l['user']['id'];
                    $temp['chinese_name'] = $l['user']['chinese_name'];
                    $result[] = $temp;
                }
            }
        }
        return $result;
    }


    /**
     * 指定部门的领导信息
     *
     * @param $departmentId []
     *
     * @return array
     */
    public static function fetchMoreMasterInfoByDepartment($departmentId)
    {
        $leaderInfo = DepartUser::with('user')
            ->whereIn('department_id', $departmentId)
            ->where('is_leader', self::USER_IS_DEPARTMENT_LEADER)
            ->where('is_primary', self::USER_IS_MASTER_DEPARTMENT)
            ->get()->toArray();

        $result = $temp = [];
        if (!empty($leaderInfo)) {
            foreach ($leaderInfo as $l) {
                if ($l['user'] && $l['user']['status'] == self::USER_STATUS_IN) {
                    $temp['id'] = $l['user']['id'];
                    $temp['chinese_name'] = $l['user']['chinese_name'];
                    $temp['avatar'] = $l['user']['avatar'];
                    $result[] = $temp;
                }
            }
        }
        return $result;
    }



    public static function fetchChildLevel($departments)
    {
        $deepList = array_reverse($departments);
        $deepInfo = [];

        if (!empty($deepList)) {
            $level = 0;
            foreach ($deepList as $l) {
                if (0 !== $l['parent_id']) {
                    $level++;
                    $l['level'] = $level;
                    $l['leaders'] = self::fetchDepartmentMasterInfo($l['id']);
                    $deepInfo[] = $l;
                }
            }
        }

        return array_reverse($deepInfo);
    }

    /**
     * 用户本月剩余补签次数
     *
     * @param $userId
     *
     * @return bool|int
     */
    public static function fetchAttendanceRetroactiveTimes($userId)
    {
        $leftTimes = config('workflow.attendance_retroactive_times') -
            Entry::fetchInProcessNum(Entry::WORK_FLOW_NO_ATTENDANCE_RETROACTIVE, $userId);

        return $leftTimes > 0 ? $leftTimes : 0;
    }

    public static function getLeaveListByDate($userId, $startDate, $endDate, $entry = null)
    {
        $entries = Entry::join('workflow_flows', 'flow_id', 'workflow_flows.id')
            ->where('user_id', $userId)
            ->where('status', Entry::STATUS_FINISHED)
            ->where('flow_no', Entry::WORK_FLOW_NO_HOLIDAY)
            ->where('workflow_entries.created_at', '>=', $startDate)
            ->where('workflow_entries.created_at', '<', $endDate)
            ->get(['title', 'workflow_entries.id']);

        $result = $entries->pluck('title', 'id');

        $resumedEntries = Entry::join('workflow_flows', 'workflow_entries.flow_id', 'workflow_flows.id')
            ->join('workflow_entry_data', 'workflow_entries.id', 'workflow_entry_data.entry_id')
            ->where('user_id', $userId)
            ->whereIn('status', [Entry::STATUS_IN_HAND, Entry::STATUS_FINISHED])
            ->where('workflow_entries.created_at', '>=', $startDate)
            ->where('workflow_entries.created_at', '<', $endDate)
            ->where('field_name', '=', 'resumption_leave_list')
            ->get(['field_value']);

        $resumptions = $resumedEntries->pluck('field_value', 'field_value')->toArray();
        if ($resumptions) {
            if ($entry) {
                $resumptionLeaveId = EntryData::getFieldValue($entry->id, 'resumption_leave_list');
                unset($resumptions[$resumptionLeaveId]);
            }
        }

        //被销假的请假记录过滤
        foreach ($result as $entry_id => $title) {
            if (in_array($entry_id, $resumptions)) {
                unset($result[$entry_id]);
            }
        }

        return $result;
    }

    /**
     * 获取用户主部门信息
     *
     * @param $userId
     *
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Support\Collection|static[]
     */
    public static function fetchMasterDepartmentInfo($userId)
    {
        $user = User::find($userId);

        if (!$user) {
            return [];
        }

        return User::leftJoin('department_user as du', 'du.user_id', '=', 'users.id')
            ->leftJoin('departments as ds', 'ds.id', '=', 'du.department_id')
            ->where('users.id', '=', $userId)
            ->where('du.is_primary', '=', self::USER_IS_MASTER_DEPARTMENT)
            ->orderBy('du.created_at', 'desc')
            ->get([
                'ds.id',
                'ds.name',
                'ds.parent_id',
                'du.is_leader',
            ]);
    }

    public static function fetchUserCanSeeWorkflowIds($userId)
    {
        $allFlows = Flow::where('is_publish', Flow::PUBLISH_YES)->where('is_abandon', Flow::ABANDON_NO)->get();

        return $allFlows->filter(function ($item, $key) use ($userId) {
            if (!$item->can_view_users && !$item->can_view_departments) {
                return true;
            }

            if ($item->can_view_users) {
                if (in_array($userId, json_decode($item->can_view_users))) {
                    return true;
                }
            }

            $flag = false;

            if ($item->can_view_departments) {
                $departments = json_decode($item->can_view_departments, true);
                $userDepartments = DepartUser::where('user_id', $userId)->get()->pluck('department_id', 'id');

                if (!empty($userDepartments)) {
                    foreach ($userDepartments as $d) {
                        if (in_array($d, $departments)) {
                            $flag = true;
                        }
                    }
                }
            }

            return $flag;
        })->pluck('id', 'id');
    }

    public static function fetchCostCenters($userId)
    {
        $departmentList = self::fetchUserPrimaryDeptPath($userId);
        $path = explode('/', $departmentList);
        $departmentLevel1 = $path['1'];
        $departmentLevel2 = $path['2'] ?? $path['1'];//管理层的一级和二级部门都是管理层
        $times = DepartmentMapCentre::max('times');

        return DepartmentMapCentre::where('department_level1', $departmentLevel1)
            ->where('times', $times)
            ->where('department_level2', $departmentLevel2)
            ->get(['centre_name'])
            ->pluck('centre_name', 'centre_name');
    }
}
