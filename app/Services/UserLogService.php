<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 2018/6/22
 * Time: 18:03
 */

namespace App\Services;

use App\Contracts\LogContract;
use App\Models\UserBankCard;
use App\Models\UserLog;
use App\Models\User;
use App\Models\UsersDetailInfo;
use App\Models\Company;
use App\Models\Department;

class UserLogService implements LogContract
{
    /**
     * int $loginId 当前登录用户
     * array $userInfo 员工更新后的信息
     * string $note 备注信息
     * object $initInfo 更新前的员工数据
     */

    /**
     * action 类型
     */
    const ADD_USER = "adduser"; //添加员工
    const MODIFY_USER_BASIC = "modify_user_basic"; //修改基础信息
    const MODIFY_USER_JOB = "modify_user_job"; //修改工作信息
    const MODIFY_USER_ID = "modify_user_id"; // 修改身份信息
    const MODIFY_USER_EDU = "modify_user_edu"; //修改学历信息
    const MODIFY_USER_CARD = "modify_user_card"; //修改银行卡信息
    const MODIFY_USER_CONTRACT = "modify_user_contract"; //修改合同信息
    const MODIFY_USER_EMERGE = "modify_user_emerge"; //修改紧急联系人信息
    const MODIFY_USER_FAMILY = "modify_user_family"; //修改家庭信息
    const MODIFY_USER_PIC = "modify_user_pic"; //修改个人材料
    const USER_RESIGNATION = "user_resignation"; //员工离职
    const ADD_USER_CARD = "add_user_card"; //添加银行卡信息
    const DEL_USER_CARD = "del_user_card"; //删除银行卡信息


    //type 变动信息类型 for search
    const TYPE_ADD_USER = 1; //入职
    const TYPE_USER_RESIGNATION = 2; //离职
    const TYPE_USER_POSITION = 3; //职位
    const TYPE_USER_COMPANY = 4; //公司
    const TYPE_USER_DEPARTMENT = 5; //部门
    const TYPE_USER_OTHER = 6; //其他

    public function record($loginId, $targetInfo = null, $userId, $note, $action, $initInfo = null, $type = null)
    {
        $userLogModel = new UserLog;
        switch ($action) {
            case self::ADD_USER:
            case self::USER_RESIGNATION:
            case self::ADD_USER_CARD:
                $logInfo['target_user_id']  = $userId;
                $logInfo['operate_user_id'] = $loginId;
                $logInfo['action']          = $action;
                $logInfo['note']            = $note;
                $logInfo['type']            = $type;
                if ($action == self::ADD_USER_CARD) {
                    if ($targetInfo['bank_type'] == UserBankCard::BANK_CARD_TYPE_MAIN) {
                        $bankType = "主卡";
                    } else {
                        $bankType = "副卡";
                    }
                    $logInfo['target_data']      = encrypt("银行卡类型：" . $bankType . '/' . "银行卡号：" . $targetInfo['bank_card_num']);
                    $logInfo['target_json_data'] = encrypt(json_encode($targetInfo));
                }
                $userLogModel->fill($logInfo);
                if ($userLogModel->save()) {
                    return true;
                } else {
                    return false;
                }
                break;
            case self::DEL_USER_CARD:
                $logInfo['target_user_id']  = $userId;
                $logInfo['operate_user_id'] = $loginId;
                $logInfo['action']          = $action;
                $logInfo['note']            = $note;
                $logInfo['type']            = $type;
                if ($initInfo['bank_type'] == UserBankCard::BANK_CARD_TYPE_MAIN) {
                    $bankType = "主卡";
                } else {
                    $bankType = "副卡";
                }
                $logInfo['init_data']      = encrypt("银行卡类型：" . $bankType . '/' . "银行卡号：" . decrypt($initInfo['card_num']));
                $logInfo['init_json_data'] = encrypt(json_encode($initInfo));
                $userLogModel->fill($logInfo);
                if ($userLogModel->save()) {
                    return true;
                } else {
                    return false;
                }
                break;
            case self::MODIFY_USER_BASIC:
            case self::MODIFY_USER_JOB:
            case self::MODIFY_USER_ID:
            case self::MODIFY_USER_EDU:
            case self::MODIFY_USER_CARD:
            case self::MODIFY_USER_CONTRACT:
            case self::MODIFY_USER_EMERGE:
            case self::MODIFY_USER_FAMILY:
            case self::MODIFY_USER_PIC:
                $data = [];
                foreach ($initInfo as $key => $value) {
                    if (isset($targetInfo[$key])) {
                        static $i = 0;
                        if ($targetInfo[$key] != $value) {
                            $data[$i]['init_data'][$key]   = $value;
                            $data[$i]['target_data'][$key] = $targetInfo[$key];
                            $i++;
                        }
                    }
                }

                if (empty($data)) //没有修改任何字段则不记录日志
                {
                    return true;
                }

                $status = $this->saveLog($data, $userId, $loginId, $action, $note);

                return $status ? true : false;
                break;
            default:
                break;
        }

    }

    private function saveLog($data, $userId, $loginId, $action, $note)
    {
        foreach ($data as $key => $value) {
            $logModel                    = new UserLog();
            $logInfo['init_data']        = encrypt($this->getChangeMessage($value['init_data'], null));
            $logInfo['target_data']      = encrypt($this->getChangeMessage(null, $value['target_data']));
            $logInfo['init_json_data']   = encrypt(json_encode($value['init_data']));
            $logInfo['target_json_data'] = encrypt(json_encode($value['target_data']));
            $logInfo['target_user_id']   = $userId;
            $logInfo['operate_user_id']  = $loginId;
            $logInfo['action']           = $action;
            $logInfo['note']             = $note;
            if (isset($value['init_data']['company_id']) || isset($value['target_data']['company_id'])) {
                $logInfo['type'] = self::TYPE_USER_COMPANY;
            } elseif (isset($value['init_data']['position']) || isset($value['target_data']['position'])) {
                $logInfo['type'] = self::TYPE_USER_POSITION;
            } else {
                $logInfo['type'] = self::TYPE_USER_OTHER;
            }
            $logModel->fill($logInfo);
            if (!$logModel->save()) {
                return false;
            }
        }

        return true;
    }

    public function recordDeptUser($loginId, $userId, $initInfo, $updateInfo, $note)
    {
        $logInfo = [];

        //原部门
        $initDept = array_map(function ($every) {
            //return $every->department_id;
            return $every['department_id'];
        }, $initInfo);
        //原部门领导
        $initDeptLeader = array_map(function ($every) {
            if ($every['is_leader'] == 1) {
                //return $every->department_id;
                return $every['department_id'];
            }
        }, $initInfo);
        $initDeptLeader = array_filter($initDeptLeader);

        //原主部门
        $pri_dept_id = null;
        foreach ($initInfo as $key => $value) {
            if ($value['is_primary'] == 1) {
                $pri_dept_id = $value['department_id'];
                break;
            }
        }

        //判断部门是否一致
        $deptCompare1 = array_diff($updateInfo['departments'], $initDept);
        $deptCompare2 = array_diff($initDept, $updateInfo['departments']);
        if ($deptCompare1 || $deptCompare2) {
            $logInfo['init_data']['departments']        = $initDept;
            $logInfo['init_data']['departments_name']   = $this->getDept($initDept);
            $logInfo['target_data']['departments']      = $updateInfo['departments'];
            $logInfo['target_data']['departments_name'] = $this->getDept($updateInfo['departments']);
        }


        //判断部门领导是否一致
        $leaderCompare1 = array_diff($updateInfo['deptleader'], $initDeptLeader);
        $leaderCompare2 = array_diff($initDeptLeader, $updateInfo['deptleader']);

        if (($leaderCompare1 || $leaderCompare2)) {
            $logInfo['init_data']['deptleader']   = $initDeptLeader;
            $logInfo['target_data']['deptleader'] = $updateInfo['deptleader'];
            if ($initDeptLeader) {
                $logInfo['target_data']['deptleader_name'] = $logInfo['init_data']['deptleader_name'] = $this->getDept($initDeptLeader);
            } else {
                $logInfo['target_data']['deptleader_name'] = $logInfo['init_data']['deptleader_name'] = $this->getDept($updateInfo['deptleader']);
            }
        }


        //判断主部门是否一致
        if ($pri_dept_id != $updateInfo['pri_dept_id']) {
            $logInfo['init_data']['pri_dept_id']     = $pri_dept_id;
            $priDeptId[]=$pri_dept_id;//变成数组
            $logInfo['init_data']['pri_dept_name']   = $this->getDept($priDeptId);
            $logInfo['target_data']['pri_dept_id']   = $updateInfo['pri_dept_id'];
            $updatePriDeptId[]=$updateInfo['pri_dept_id'];//变成数组
            $logInfo['target_data']['pri_dept_name'] = $this->getDept($updatePriDeptId);
        }
        if (empty($logInfo)) //部门信息未变更则不记录日志
        {
            return true;
        }

        $logInfo['target_user_id']   = $userId;
        $logInfo['operate_user_id']  = $loginId;
        $logInfo['action']           = self::MODIFY_USER_BASIC;
        $logInfo['note']             = $note;
        $logInfo['init_data']        = encrypt($this->getChangeMessage($logInfo['init_data'],null));
        $logInfo['target_data']      = encrypt($this->getChangeMessage(null, $logInfo['target_data']));
        $logInfo['init_json_data']   = encrypt(json_encode($initInfo));
        $logInfo['target_json_data'] = encrypt(json_encode($updateInfo));
        $logInfo['type']             = self::TYPE_USER_DEPARTMENT;
        $userLogModel                = new UserLog;

        $userLogModel->fill($logInfo);

        if ($userLogModel->save()) {
            return true;
        } else {
            return false;
        }

    }

    public function getLog($userId)
    {
        // TODO: Implement getLog() method.
        $logInfo = UserLog::where('target_user_id', '=', $userId)->orderBy('created_at', 'desc')->get()
            ->each(function ($item, $key) {
                if ($item->init_data) {
                    $item->init_messages = decrypt($item->init_data);
                }
                if ($item->target_data) {
                    $item->target_messages = decrypt($item->target_data);
                }

                if ($item->operate_user_id) {
                    $operateUserInfo = User::find($item->operate_user_id);
                    if ($operateUserInfo) {
                        $item->operate_user_name = $operateUserInfo->chinese_name;
                    } else {
                        $item->operate_user_name = "";
                    }

                }

                if ($item->target_user_id) {
                    $targetUserInfo = User::find($item->target_user_id);
                    if ($targetUserInfo) {
                        $item->target_user_name = $targetUserInfo->chinese_name;
                    } else {
                        $item->target_user_name = "";
                    }
                }

                $item->action_name = "";

                if ($item->action) {
                    switch ($item->action) {
                        case self::ADD_USER:
                            $item->action_name = "添加员工";
                            break;
                        case self::MODIFY_USER_BASIC:
                            $item->action_name = "修改基础信息";
                            break;
                        case self::MODIFY_USER_JOB:
                            $item->action_name = "修改工作信息";
                            break;
                        case self::MODIFY_USER_ID:
                            $item->action_name = "修改身份信息";
                            break;
                        case self::MODIFY_USER_EDU:
                            $item->action_name = "修改学历信息";
                            break;
                        case self::MODIFY_USER_CARD:
                            $item->action_name = "修改银行卡信息";
                            break;
                        case self::MODIFY_USER_CONTRACT:
                            $item->action_name = "修改合同信息";
                            break;
                        case self::MODIFY_USER_EMERGE:
                            $item->action_name = "修改紧急联系人信息";
                            break;
                        case self::MODIFY_USER_FAMILY:
                            $item->action_name = "修改家庭信息";
                            break;
                        case self::MODIFY_USER_PIC:
                            $item->action_name = "修改个人材料";
                            break;
                        case self::USER_RESIGNATION:
                            $item->action_name = "员工离职";
                            break;
                        case self::ADD_USER_CARD:
                            $item->action_name = "添加银行卡";
                            break;
                        case self::DEL_USER_CARD:
                            $item->action_name = "删除银行卡";
                            break;
                        default:
                            break;
                    }
                }

            });

        return $logInfo;
    }


    /**
     * 整理变更信息
     * $init 原始数据
     * $target 目标数据
     */
    private function getChangeMessage($init = null, $target = null)
    {
        $basicComment  = User::basicColMapComment();
        $detailComment = UsersDetailInfo::detailColMapComment();

        $messsages = "";

        if ($init) {
            $messsages = $this->mergeMessage($init, $basicComment, $detailComment);
        }

        if ($target) {
            $messsages = $this->mergeMessage($target, $basicComment, $detailComment);
        }

        return $messsages;
    }


    /**
     * $data 需要拼装的数据
     *
     */
    private function mergeMessage($data, $basicComment, $detailComment)
    {
        $messages = "";
        foreach ($data as $key => $value) {
            if (isset($basicComment[$key]) && $basicComment[$key]) {
                switch ($key) {
                    case "company_id":
                        $companyInfo = Company::where('id', '=', $value)->first();
                        if ($companyInfo) {
                            $messages = $messages . $basicComment[$key] . ":" . $companyInfo->name . "/";
                        }
                        break;
                    case "is_sync_wechat":
                        if ($value == 1) {
                            $messages = $messages . $basicComment[$key] . ":" . "是" . "/";
                        } else {
                            $messages = $messages . $basicComment[$key] . ":" . "否" . "/";
                        }
                        break;
                    case "gender":
                        if ($value == 1) {
                            $messages = $messages . $basicComment[$key] . ":" . "男" . "/";
                        } elseif ($value == 2) {
                            $messages = $messages . $basicComment[$key] . ":" . "女" . "/";
                        } else {
                            $messages = $messages . $basicComment[$key] . ":" . "未知" . "/";
                        }
                        break;
                    case "work_address":
                        if ($value == "shanghai") {
                            $messages = $messages . $basicComment[$key] . ":" . "上海" . "/";
                        } elseif ($value == "beijing") {
                            $messages = $messages . $basicComment[$key] . ":" . "北京" . "/";
                        } elseif ($value == "chengdu") {
                            $messages = $messages . $basicComment[$key] . ":" . "成都" . "/";
                        } elseif ($value == "shenzhen") {
                            $messages = $messages . $basicComment[$key] . ":" . "深圳" . "/";
                        } elseif ($value == "pingxiang") {
                            $messages = $messages . $basicComment[$key] . ":" . "萍乡" . "/";
                        }
                        break;
                    case "isleader":
                        if ($value == "1") {
                            $messages = $messages . $basicComment[$key] . ":" . "是" . "/";
                        } else {
                            $messages = $messages . $basicComment[$key] . ":" . "否" . "/";
                        }
                        break;
                    case "superior_leaders":
                        $userInfo = User::find($value);
                        if ($userInfo) {
                            $messages = $messages . $basicComment[$key] . ":" . $userInfo->chinese_name . "/";
                        }
                        break;
                    default:
                        $messages = $messages . $basicComment[$key] . ":" . $value . "/";
                        break;
                }
            }

            if (isset($detailComment[$key]) && $detailComment[$key]) {
                switch ($key) {
                    case "user_type":
                        if ($value == "full-time") {
                            $messages = $messages . $detailComment[$key] . ":" . "全职" . "/";
                        } elseif ($value == "part-time") {
                            $messages = $messages . $detailComment[$key] . ":" . "兼职" . "/";
                        } elseif ($value == "internship") {
                            $messages = $messages . $detailComment[$key] . ":" . "实习" . "/";
                        } elseif ($value == "labor-dispatch") {
                            $messages = $messages . $detailComment[$key] . ":" . "劳务派遣" . "/";
                        } elseif ($value == "hire-retired") {
                            $messages = $messages . $detailComment[$key] . ":" . "退休返聘" . "/";
                        } elseif ($value == "labor-outsourcing") {
                            $messages = $messages . $detailComment[$key] . ":" . "劳务外包" . "/";
                        } elseif ($value == "counselor") {
                            $messages = $messages . $detailComment[$key] . ":" . "顾问" . "/";
                        } else {
                            # TODO
                        }
                        break;
                    case "user_status":
                        if ($value == "regular") {
                            $messages = $messages . $detailComment[$key] . ":" . "正式" . "/";
                        } elseif ($value == "non-regular") {
                            $messages = $messages . $detailComment[$key] . ":" . "非正式" . "/";
                        }
                        break;
                    case "census_type":
                        if ($value == "agriculture") {
                            $messages = $messages . $detailComment[$key] . ":" . "农业" . "/";
                        } elseif ($value == "non-agriculture") {
                            $messages = $messages . $detailComment[$key] . ":" . "非农业" . "/";
                        }
                        break;
                    case "politics_status":
                        if ($value == "party_member") {
                            $messages = $messages . $detailComment[$key] . ":" . "党员" . "/";
                        } elseif ($value == "masses") {
                            $messages = $messages . $detailComment[$key] . ":" . "群众" . "/";
                        }
                        break;
                    case "marital_status":
                        if ($value == "unmarried") {
                            $messages = $messages . $detailComment[$key] . ":" . "未婚" . "/";
                        } elseif ($value == "married") {
                            $messages = $messages . $detailComment[$key] . ":" . "已婚" . "/";
                        } elseif ($value == "divorced") {
                            $messages = $messages . $detailComment[$key] . ":" . "离异" . "/";
                        } elseif ($value == "widowed") {
                            $messages = $messages . $detailComment[$key] . ":" . "丧偶" . "/";
                        }
                        break;

                    case "contract_type":
                        if ($value == "fixed") {
                            $messages = $messages . $detailComment[$key] . ":" . "全职" . "/";
                        } elseif ($value == "non-fixed") {
                            $messages = $messages . $detailComment[$key] . ":" . "兼职" . "/";
                        }
                        break;
                    case "has_children":
                        if ($value == 1) {
                            $messages = $messages . $detailComment[$key] . ":" . "有" . "/";
                        } else {
                            $messages = $messages . $detailComment[$key] . ":" . "无" . "/";
                        }
                        break;
                    case "child_gender":
                        if ($value == 1) {
                            $messages = $messages . $detailComment[$key] . ":" . "男" . "/";
                        } else {
                            $messages = $messages . $detailComment[$key] . ":" . "女" . "/";
                        }
                        break;
                    default:
                        $messages = $messages . $detailComment[$key] . ":" . $value . "/";
                        break;
                }

            }

            //部门信息变更记录
            if ($key == "departments_name") {
                $messages = $messages . "部门" . ":";
                foreach ($value as $k => $v) {
                    $messages .= '<span data-toggle="tooltip" title="' . Department::getDeptPath($k) . $k . '" data-placement="bottom">' . $v . '</span>' . "、";
                }
                $messages .= ";";
            }

            if ($key == "deptleader_name") {
                $messages = $messages . "部门领导" . ":";

                foreach ($value as $k => $v) {
                    $messages .= '<span data-toggle="tooltip" title="' . Department::getDeptPath($k) . $k . '" data-placement="bottom">' . $v . '</span>' . "、";
                }
                $messages .= ";";
            }

            if ($key == "pri_dept_name") {
                $messages = $messages . "主部门" . ":";

                foreach ($value as $k => $v) {
                    $messages .= '<span data-toggle="tooltip" title="' . Department::getDeptPath($k) . $k . '" data-placement="bottom">' . $v . '</span>' . "、";
                }
                $messages .= ";";
            }
        }

        return $messages;

    }


    /**
     * 查询部门信息
     */

    private function getDept($deptId)
    {
        $deptsName = [];
        $deptInfo  = Department::getByIds($deptId)->toArray();
        if ($deptInfo) {
            if (count($deptInfo) == count($deptInfo, 1)) {
                return [$deptInfo['id'] => $deptInfo['name']];
            } else {
                foreach ($deptInfo as $key => $value) {
                    if ($value['name']) {
                        $deptsName[$value['id']] = $value['name'];
                    }
                }
            }
        }

        return $deptsName;
    }

    public function save($data)
    {
        // TODO: Implement save() method.
    }

    public function get($Id)
    {
        // TODO: Implement get() method.
    }
}