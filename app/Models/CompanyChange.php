<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 2018/6/29
 * Time: 16:35
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DevFixException;
/**
 * App\Models\CompanyChange
 *
 * @property int $id
 * @property int $company_id 关联企业
 * @property string|null $title 变更事项
 * @property string|null $before 变更前内容
 * @property string|null $after 变更后内容
 * @property string|null $change_at 变更日期
 * @property \Carbon\Carbon|null $created_at 创建时间
 * @property \Carbon\Carbon|null $updated_at 修改时间
 * @property int|null $operate_user_id 操作人ID
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CompanyChange whereAfter($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CompanyChange whereBefore($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CompanyChange whereChangeAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CompanyChange whereCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CompanyChange whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CompanyChange whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CompanyChange whereOperateUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CompanyChange whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CompanyChange whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property string $json_data 变更原始数据
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\CompanyChange whereJsonData($value)
 */
class CompanyChange extends Model
{

    /*
     * 公司变更类型
    */
    const ADD_COMPANY = "add_company";
    const EDIT_COMPANY = "edit_company";
    const DELETE_COMPANY = "delete_company";


    /**
     * 公司信息模块
     */
    const MODULE_COMPANY = "module_company";
    const MODULE_EQUITY_PLEDGE = "module_equity_pledge";
    const MODULE_MAIN_PERSONNELS = "module_main_personnels";
    const MODULE_SHARE_HOLDERS = "module_share_holders";

    const COMPANY_CHANGE_EDIT = "营业执照信息编辑";
    const SHARE_HOLDER_EDIT = "股东及出资信息编辑";
    const SHARE_HOLDER_ADD = "股东及出资信息新增";
    const SHARE_HOLDER_DEL = "股东及出资信息删除";
    const PERSONNEL_EDIT = "主要人员信息编辑";
    const PERSONNEL_ADD = "主要人员信息新增";
    const PERSONNEL_DEL = "主要人员信息删除";
    const PLEDGE_EDIT = "股权出质登记信息编辑";
    const PLEDGE_ADD = "股权出质登记信息新增";
    const PLEDGE_DEL = "股权出质登记信息删除";


    protected $table = "company_change";

    public $fillable = [
        'company_id', 'title', 'before', 'after', 'change_at', 'operate_user_id'
    ];

    /**
     *
     *获取公司变更信息
     * @param $data
     *
     * @return string
     */
    public static function getChangeMessages($data, $module)
    {
        $companyComment               = Company::getComment();
        $shareholdersComment          = CompanyShareholders::getComment();
        $companyMainPersonnelsComment = CompanyMainPersonnels::getComment();
        $companyEquityPledgeComment   = CompanyEquityPledge::getComment();

        $messages = "";
        if ($data) {
            foreach ($data as $key => $value) {
                switch ($module) {
                    case self::MODULE_COMPANY:
                        if (isset($companyComment[$key]) && $companyComment[$key]) {
                            switch ($key) {
                                case 'register_status':
                                    if ($value == 1) {
                                        $messages .= $companyComment[$key] . ":" . '开业' . "/";
                                    } elseif ($value == 2) {
                                        $messages .= $companyComment[$key] . ":" . '在业' . "/";
                                    } elseif ($value == 3) {
                                        $messages .= $companyComment[$key] . ":" . '吊销' . "/";
                                    } elseif ($value == 4) {
                                        $messages .= $companyComment[$key] . ":" . '注销' . "/";
                                    } elseif ($value == 5) {
                                        $messages .= $companyComment[$key] . ":" . '迁入' . "/";
                                    } elseif ($value == 6) {
                                        $messages .= $companyComment[$key] . ":" . '迁出' . "/";
                                    } elseif ($value == 7) {
                                        $messages .= $companyComment[$key] . ":" . '停业' . "/";
                                    } elseif ($value == 8) {
                                        $messages .= $companyComment[$key] . ":" . '清算' . "/";
                                    } else {
                                        // TODO
                                    }
                                    break;
                                case "parent_id":
                                    if ($value == 0) {
                                        $messages .= $companyComment[$key] . ":" . '无' . "/";
                                    } else {
                                        $companyInfo = Company::findOrFail($value);
                                        $messages    .= $companyComment[$key] . ":" . $companyInfo->name . "/";
                                    }
                                    break;
                                default:
                                    $messages .= $companyComment[$key] . ":" . $value . "/";
                                    break;
                            }
                        }
                        break;
                    case self::MODULE_SHARE_HOLDERS:
                        if (isset($shareholdersComment[$key]) && $shareholdersComment[$key]) {
                            switch ($key) {
                                case "shareholder_type":
                                    if ($value == 1) {
                                        $messages .= $shareholdersComment[$key] . ":" . "自然人股东" . "/";
                                    } elseif ($value == 2) {
                                        $messages .= $shareholdersComment[$key] . ":" . "法人股东" . "/";
                                    } else {
                                        //TODO
                                    }
                                    break;
                                case "certificate_type":
                                    if ($value == 1) {
                                        $messages .= $shareholdersComment[$key] . ":" . "非公示项" . "/";
                                    } elseif ($value == 2) {
                                        $messages .= $shareholdersComment[$key] . ":" . "非公司企业法人营业执照" . "/";
                                    } elseif ($value == 3) {
                                        $messages .= $shareholdersComment[$key] . ":" . "合伙企业营业执照" . "/";
                                    } elseif ($value == 4) {
                                        $messages .= $shareholdersComment[$key] . ":" . "企业法人营业执照（公司）" . "/";
                                    } else {
                                        // TODO
                                    }
                                    break;
                                default:
                                    $messages .= $shareholdersComment[$key] . ":" . $value . "/";
                                    break;
                            }
                        }
                        break;
                    case self::MODULE_MAIN_PERSONNELS:
                        if (isset($companyMainPersonnelsComment[$key]) && $companyMainPersonnelsComment[$key]) {
                            $messages .= $companyMainPersonnelsComment[$key] . ":" . $value . "/";
                        }
                        break;
                    case self::MODULE_EQUITY_PLEDGE:
                        if (isset($companyEquityPledgeComment[$key]) && $companyEquityPledgeComment[$key]) {
                            switch ($key) {
                                case "pledge_status":
                                    if ($value == 1) {
                                        $messages .= $companyEquityPledgeComment[$key] . ":" . "有效" . "/";
                                    } elseif ($value == 2) {
                                        $messages .= $companyEquityPledgeComment[$key] . ":" . "无效" . "/";
                                    } else {
                                        // TODO
                                    }
                                    break;
                                default:
                                    $messages .= $companyEquityPledgeComment[$key] . ":" . $value . "/";
                                    break;
                            }
                        }
                        break;
                    default:
                        break;
                }
            }
        }
        return $messages;
    }

    public static function saveChangeOld($loginId, $companyId, $before, $after, $title, $type, $module)
    {

        $changeInfo = [];

        switch ($type) {
            case self::ADD_COMPANY:
                $moduleList = [self::MODULE_COMPANY, self::MODULE_SHARE_HOLDERS, self::MODULE_MAIN_PERSONNELS];
                if (in_array($module, $moduleList)) {
                    $changeInfo['after']['name'] = $after['name'];
                }

                if ($module == self::MODULE_EQUITY_PLEDGE) {  //股权出质信息添加
                    $changeInfo['after']['code'] = $after['code'];
                }

                $changeInfo['company_id']      = $companyId;
                $changeInfo['operate_user_id'] = $loginId;
                $changeInfo['title']           = $title;
                $changeInfo['before']          = null;
                $changeInfo['after']           = self::getChangeMessages($changeInfo['after'], $module);
                $changeInfo['change_at']       = date('Y-m-d', time());
                break;
            case self::EDIT_COMPANY:
                if ($before && $after) {
                    foreach ($before as $key => $value) {
                        if (isset($after[$key]) && $after[$key] != $value) {
                            $changeInfo['before'][$key] = $value;
                            $changeInfo['after'][$key]  = $after[$key];
                        }
                    }
                }

                if (!$changeInfo) //未做任何修改则不记录数据
                {
                    return True;
                }

                //变更信息需要制定人员信息的
                $moduleList = [self::MODULE_SHARE_HOLDERS, self::MODULE_MAIN_PERSONNELS];
                if (in_array($module, $moduleList)) {
                    if (!isset($changeInfo['before']['name'])) {
                        $changeInfo['before']['name'] = $before['name'];
                    }

                    if (!isset($changeInfo['after']['name'])) {
                        $changeInfo['after']['name'] = $after['name'];
                    }
                }

                //股权处置信息记录携带编号
                if ($module == self::MODULE_EQUITY_PLEDGE) {
                    if (!isset($changeInfo['before']['code'])) {
                        $changeInfo['before']['code'] = $before['code'];
                    }

                    if (!isset($changeInfo['after']['code'])) {
                        $changeInfo['after']['code'] = $after['code'];
                    }
                }

                $changeInfo['company_id']      = $companyId;
                $changeInfo['title']           = $title;
                $changeInfo['operate_user_id'] = $loginId;
                $changeInfo['before']          = self::getChangeMessages($changeInfo['before'], $module);
                $changeInfo['after']           = self::getChangeMessages($changeInfo['after'], $module);
                $changeInfo['change_at']       = date('Y-m-d', time());

                break;
            case self::DELETE_COMPANY:

                $moduleList = [self::MODULE_SHARE_HOLDERS, self::MODULE_MAIN_PERSONNELS];
                if (in_array($module, $moduleList)) {
                    $changeInfo['before']['name'] = $before['name'];
                    $changeInfo['before']         = self::getChangeMessages($changeInfo['before'], $module);
                }

                if ($module == self::MODULE_EQUITY_PLEDGE) {
                    $changeInfo['before']['code'] = $before['code'];
                    $changeInfo['before']         = self::getChangeMessages($changeInfo['before'], $module);
                }

                if ($module == self::MODULE_COMPANY) {
                    $changeInfo['before']['name'] = $before['name'];
                    $changeInfo['before']         = self::getChangeMessages($changeInfo['before'], $module);
                }

                $changeInfo['company_id']      = $companyId;
                $changeInfo['title']           = $title;
                $changeInfo['operate_user_id'] = $loginId;
                $changeInfo['after']           = "";
                $changeInfo['change_at']       = date('Y-m-d', time());
                break;
            default:
                break;
        }


        $companyChange = new CompanyChange;
        $companyChange->fill($changeInfo);
        if ($companyChange->save()) {
            return true;
        } else {
            return false;
        }
    }

    public static function saveChangeLog($operateUserId, $data, $companyId)
    {
        $date    = date('Y-m-d', time());
        $curTime = date('Y-m-d H:i:s', time());
        if (isset($data['company_change']['edit'])) {
            $companyChangeBefore         = '';
            $companyChangeAfter          = '';
            $companyChange['company_id'] = $companyId;
            $companyChange['title']      = self::COMPANY_CHANGE_EDIT;
            foreach ($data['company_change']['edit'][$companyId] as $key => $value) {
                $companyChangeBefore .= $value['comment'] . ':' . $value['before_comment'] . '/';
                $companyChangeAfter  .= $value['comment'] . ':' . $value['after_comment'] . '/';
            }
            $companyChange['before']          = $companyChangeBefore;
            $companyChange['after']           = $companyChangeAfter;
            $companyChange['change_at']       = $date;
            $companyChange['operate_user_id'] = $operateUserId;
            $companyChange['json_data']       = json_encode($data['company_change']['edit'][$companyId]);
            $logModel                         = new CompanyChange();
            $logModel->fill($companyChange);
            if (!$logModel->save()) {
                throw new DevFixException(self::COMPANY_CHANGE_EDIT . "日志保存失败");
            }
        }

        if (isset($data['share_holder']['edit'])) {
            self::storeLog($data['share_holder']['edit'], self::SHARE_HOLDER_EDIT, $companyId, 'edit', $operateUserId);
        }

        if (isset($data['share_holder']['add'])) {
            self::storeLog($data['share_holder']['add'], self::SHARE_HOLDER_ADD, $companyId, 'add', $operateUserId);
        }

        if (isset($data['share_holder']['delete'])) {
            self::storeLog($data['share_holder']['delete'], self::SHARE_HOLDER_DEL, $companyId, 'delete', $operateUserId);
        }

        if (isset($data['personnel']['edit'])) {
            self::storeLog($data['personnel']['edit'], self::PERSONNEL_EDIT, $companyId, 'edit', $operateUserId);
        }

        if (isset($data['personnel']['add'])) {
            self::storeLog($data['personnel']['add'], self::PERSONNEL_ADD, $companyId, 'add', $operateUserId);
        }

        if (isset($data['personnel']['delete'])) {
            self::storeLog($data['personnel']['delete'], self::PERSONNEL_DEL, $companyId, 'delete', $operateUserId);
        }

        if (isset($data['pledge']['edit'])) {
            self::storeLog($data['pledge']['edit'], self::PLEDGE_EDIT, $companyId, 'edit', $operateUserId);
        }

        if (isset($data['pledge']['add'])) {
            self::storeLog($data['pledge']['add'], self::PLEDGE_ADD, $companyId, 'add', $operateUserId);
        }

        if (isset($data['pledge']['delete'])) {
            self::storeLog($data['pledge']['delete'], self::PLEDGE_DEL, $companyId, 'delete', $operateUserId);
        }
    }


    public static function storeLog($data, $title, $companyId, $type, $operateUserId)
    {
        $date    = date('Y-m-d', time());
        $curTime = date('Y-m-d H:i:s', time());
        if ($type == 'edit') {
            foreach ($data as $key => $entry) {
                $editBefore                   = '';
                $editAfter                    = '';
                $jsonData                     = json_encode($entry);
                $editData[$key]['company_id'] = $companyId;
                $editData[$key]['title']      = $title;
                foreach ($entry as $value) {
                    $editBefore .= $value['comment'] . ':' . $value['before_comment'] . '/';
                    $editAfter  .= $value['comment'] . ':' . $value['after_comment'] . '/';
                }
                $editData[$key]['before']          = $editBefore;
                $editData[$key]['after']           = $editAfter;
                $editData[$key]['change_at']       = $date;
                $editData[$key]['operate_user_id'] = $operateUserId;
                $editData[$key]['json_data']       = $jsonData;
                $editData[$key]['created_at']      = $curTime;
                $editData[$key]['updated_at']      = $curTime;
            }

            if (!CompanyChange::insert($editData)) {
                throw new DevFixException($title . "日志保存失败");
            }
        } elseif ($type == 'add') {
            foreach ($data as $key => $entry) {
                $addAfter                    = '';
                $jsonData                    = json_encode($entry);
                $addData[$key]['company_id'] = $companyId;
                $addData[$key]['title']      = $title;
                $addData[$key]['before']     = "";
                foreach ($entry as $k => $value) {
                    if ($k == 'name' ||$k == 'code') {
                        $addAfter .= $value['comment'] . ':' . $value['after_comment'] . '/';
                        break;
                    }
                }
                $addData[$key]['after']           = $addAfter;
                $addData[$key]['change_at']       = $date;
                $addData[$key]['operate_user_id'] = $operateUserId;
                $addData[$key]['json_data']       = $jsonData;
                $addData[$key]['created_at']      = $curTime;
                $addData[$key]['updated_at']      = $curTime;
            }
            if (!CompanyChange::insert($addData)) {
                throw new DevFixException($title . "日志保存失败");
            }
        } elseif ($type == 'delete') {
            foreach ($data as $key => $entry) {
                $delBefore                   = "";
                $jsonData                    = json_encode($entry);
                $delData[$key]['company_id'] = $companyId;
                $delData[$key]['title']      = $title;
                foreach ($entry as $k => $value) {
                    if ($k == 'name' || $k == 'code') {
                        $delBefore = $value['comment'] . ':' . $value['before_comment'] . "/";
                        break;
                    }
                }
                $delData[$key]['before']          = $delBefore;
                $delData[$key]['after']           = "";
                $delData[$key]['change_at']       = $date;
                $delData[$key]['operate_user_id'] = $operateUserId;
                $delData[$key]['json_data']       = $jsonData;
                $delData[$key]['created_at']      = $curTime;
                $delData[$key]['updated_at']      = $curTime;
            }

            if (!CompanyChange::insert($delData)) {
                throw new DevFixException($title . "日志保存失败");
            }
        }

    }

}