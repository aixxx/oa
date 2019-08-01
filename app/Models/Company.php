<?php

namespace App\Models;

use App\Models\Attendance\AnnualRule;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use DevFixException;
use UserFixException;

/**
 * App\Models\Company
 *
 * @property int $id
 * @property string|null $name
 * @property string|null $legal_person
 * @property string|null $local
 * @property int $capital
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property string|null $code 统一社会信用代码/注册号
 * @property string|null $category 类型如'其他有限责任公司'
 * @property string|null $establishment 成立日期
 * @property string|null $business_start 营业期限开始日
 * @property string|null $business_end 营业期限截止日
 * @property string|null $registration_authority 登记机关
 * @property string|null $approval_at 核准日期
 * @property int|null $register_status 登记状态(1开业、2在业、3吊销、4注销、5迁入、6迁出、7停业、8清算)
 * @property string|null $scope 经营范围
 * @property string|null $contact 企业联系电话
 * @property int|null $employe_num 从业人数
 * @property int|null $female_num 其中女性从业人数
 * @property string|null $email 企业电子邮箱
 * @property int|null $parent_id 上级公司
 * @property int $status 状态1.有效；2.删除
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\User[] $users
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Company whereApprovalAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Company whereBusinessEnd($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Company whereBusinessStart($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Company whereCapital($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Company whereCategory($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Company whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Company whereContact($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Company whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Company whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Company whereEmployeNum($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Company whereEstablishment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Company whereFemaleNum($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Company whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Company whereLegalPerson($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Company whereLocal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Company whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Company whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Company whereRegisterStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Company whereRegistrationAuthority($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Company whereScope($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Company whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Company whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property string|null $abbr 公司名称缩写
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Company whereAbbr($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Company findNoDelete()
 */
class Company extends Model
{

    const STATUS_NO_DELETE = 1;   //未删除
    const STATUS_DELETE = 2;    //删除

    public $fillable = [
        'name',
        'legal_person',
        'local',
        'capital',
        'code',
        'category',
        'establishment',
        'business_start',
        'business_end',
        'registration_authority',
        'approval_at',
        'register_status',
        'scope',
        'contact',
        'employe_num',
        'female_num',
        'email',
        'parent_id',
        'status',
        'abbr',
    ];

    //登记状态
    public static $registerStatusMap = [
        '1' => '开业',
        '2' => '在业',
        '3' => '吊销',
        '4' => '注销',
        '5' => '迁入',
        '6' => '迁出',
        '7' => '停业',
        '8' => '清算',
        '9' => '存续（在营、开业、在册）',
    ];

    public function annualRules()
    {
        return $this->belongsToMany(AnnualRule::class, 'company_annual_rule',
            'company_id', 'rule_id');
    }


    public function users()
    {
        return $this->hasMany('App\Models\User', 'company_id', 'id');
    }

    /**
     * 校验公司名称
     * @param $companyName
     */
    public static function validCompanyName($companyName)
    {
        $name = trim($companyName);
        return self::whereName($name)->whereStatus(self::STATUS_NO_DELETE)->first() ? true : false;
    }

    /**
     * 获取公司Id
     * @param $companyName
     * @return Company|\Illuminate\Database\Eloquent\Builder|Model|null|object
     */
    public static function getCompanyIdByName($companyName)
    {
        $name = trim($companyName);
        return self::whereName($name)->whereStatus(self::STATUS_NO_DELETE)->first();
    }


    /**
     * 查询公司下是否有员工
     */
    public static function hasEmployee($companies)
    {
        $users = User::where('status', '=', 1)->get()->toArray();
        $usersCompanies = [];

        if ($users) {
            foreach ($users as $key => $every) {
                if ($every['company_id']) {
                    $everyCompany = explode(',', $every['company_id']);
                    if ($everyCompany) {
                        array_map(function ($entry) use (&$usersCompanies) {
                            array_push($usersCompanies, $entry);
                        }, $everyCompany);
                    }
                }
            }
        }

        if ($usersCompanies) {
            if (!is_array($companies)) {
                if (in_array($companies, $usersCompanies)) {
                    return ['status' => 'failed', 'messages' => '公司内部有员工无法删除！'];
                }
            } else {
                foreach ($companies as $key => $value) {
                    if (in_array($value, $usersCompanies)) {
                        return ['status' => 'failed', 'messages' => '公司内部有员工无法删除！'];
                    }
                }
            }
            return false;
        }

        return false;
    }

    /**
     * 获取字段注释
     */
    static public function getComment()
    {
        $map = [
            'name' => '公司名称',
            'legal_person' => '法人',
            'local' => '地点',
            'capital' => '注册资本',
            'code' => '统一社会信用代码/注册号',
            'category' => '类型',
            'establishment' => '成立日期',
            'business_start' => '营业期限开始日',
            'business_end' => '营业期限截止日',
            'registration_authority' => '登记机关',
            'approval_at' => '核准日期',
            'register_status' => '登记状态',
            'scope' => '经营范围',
            'contact' => '企业联系电话',
            'employe_num' => '从业人数',
            'female_num' => '其中女性从业人数',
            'email' => '企业电子邮箱',
            'parent_id' => '上级公司',
        ];

        return $map;
    }

    /**
     * 获取公司列表
     */
    public static function getCompanyList()
    {
        return static::orderBy('id')->get();
    }


    /**
     * 对比公司营业执照信息
     * @param $companyId
     * @param $requestCompanyInfo
     * @return array
     */
    public static function compareCompany($companyId, $requestCompanyInfo)
    {
        $companyChangeData = [];
        $originCompanyData = Company::findOrFail($companyId);
        foreach ($requestCompanyInfo as $key => $value) {
            if ($value != $originCompanyData->$key) {
                $companyChangeData['edit'][$companyId][$key]['comment'] = Company::getComment()[$key];
                $companyChangeData['edit'][$companyId][$key]['before_value'] = $originCompanyData->$key;
                $companyChangeData['edit'][$companyId][$key]['after_value'] = $value;
                if ($key == 'register_status') {
                    $companyChangeData['edit'][$companyId][$key]['before_comment'] = Company::$registerStatusMap[$originCompanyData->$key];
                    $companyChangeData['edit'][$companyId][$key]['after_comment'] = Company::$registerStatusMap[$value];
                } elseif ($key == 'parent_id') {
                    $originCompanyParentData = Company::find($originCompanyData->$key);
                    if ($originCompanyParentData) {
                        $companyChangeData['edit'][$companyId][$key]['before_comment'] = $originCompanyParentData->name;
                    } else {
                        $companyChangeData['edit'][$companyId][$key]['before_comment'] = "";
                    }

                    $changeCompanyParentData = Company::find($value);
                    if ($changeCompanyParentData) {
                        $companyChangeData['edit'][$companyId][$key]['after_comment'] = $changeCompanyParentData->name;
                    } else {
                        $companyChangeData['edit'][$companyId][$key]['after_comment'] = "";
                    }
                } else {
                    $companyChangeData['edit'][$companyId][$key]['before_comment'] = $originCompanyData->$key;
                    $companyChangeData['edit'][$companyId][$key]['after_comment'] = $value;
                }
            }
        }
        return $companyChangeData;
    }

    /**
     * 对比股东信息
     * @param $companyId
     * @param $requestShareholder
     * @return array
     */
    public static function compareShareHolder($companyId, $requestShareholder, $requestAddShareholder, $requestDelShareholder)
    {
        //股权信息
        $shareHolderChange = [];
        $shareHolderIds = array_keys($requestShareholder);
        $originShareholderData = CompanyShareholders::where('company_id', '=', $companyId)->whereIn('id', $shareHolderIds)->get();
        $originShareholderDelData = CompanyShareholders::where('company_id', '=', $companyId)->whereIn('id', $requestDelShareholder)->get();
        $originShareholderDataArr = !$originShareholderData->isEmpty() ? $originShareholderData->toArray() : [];
        $originShareholderDelDataArr = !$originShareholderDelData->isEmpty() ? $originShareholderDelData->toArray() : [];
        $shareHolderEdit = []; //编辑股东
        $shareHolderDel = []; //删除股东
        $shareHolderAdd = []; //添加股东
        $companyShareHoldersMap = CompanyShareholders::getComment();
        $shareHolderTypeMap = CompanyShareholders::$shareholderTypeMap;
        $certificateTypeMap = CompanyShareholders::$certificateTypeMap;
        if ($requestShareholder) {
            foreach ($requestShareholder as $key => $value) {
                array_map(function ($every) use ($key, $value, &$shareHolderEdit, $companyShareHoldersMap, $shareHolderTypeMap, $certificateTypeMap) {
                    if ($key == $every['id']) {
                        if ($value['name'] != $every['name'] || $value['shareholder_type'] != $every['shareholder_type'] ||
                            $value['certificate_type'] != $every['certificate_type'] || $value['id_number'] != $every['id_number']
                        ) {
                            $shareHolderEdit[$key]['name']['comment'] = $companyShareHoldersMap['name'];
                            $shareHolderEdit[$key]['name']['before_value'] = $every['name'];
                            $shareHolderEdit[$key]['name']['after_value'] = $value['name'];
                            $shareHolderEdit[$key]['name']['before_comment'] = $every['name'];
                            $shareHolderEdit[$key]['name']['after_comment'] = $value['name'];


                            $shareHolderEdit[$key]['shareholder_type']['comment'] = $companyShareHoldersMap['shareholder_type'];
                            $shareHolderEdit[$key]['shareholder_type']['before_value'] = $every['shareholder_type'];
                            $shareHolderEdit[$key]['shareholder_type']['after_value'] = $value['shareholder_type'];
                            $shareHolderEdit[$key]['shareholder_type']['before_comment'] = $every['shareholder_type'] ? $shareHolderTypeMap[$every['shareholder_type']] : "";
                            $shareHolderEdit[$key]['shareholder_type']['after_comment'] = $value['shareholder_type'] ? $shareHolderTypeMap[$value['shareholder_type']] : "";


                            $shareHolderEdit[$key]['certificate_type']['comment'] = $companyShareHoldersMap['certificate_type'];
                            $shareHolderEdit[$key]['certificate_type']['before_value'] = $every['certificate_type'];
                            $shareHolderEdit[$key]['certificate_type']['after_value'] = $value['certificate_type'];
                            $shareHolderEdit[$key]['certificate_type']['before_comment'] = $every['certificate_type'] ? $certificateTypeMap[$every['certificate_type']] : "";
                            $shareHolderEdit[$key]['certificate_type']['after_comment'] = $value['certificate_type'] ? $certificateTypeMap[$value['certificate_type']] : "";


                            $shareHolderEdit[$key]['id_number']['comment'] = $companyShareHoldersMap['id_number'];
                            $shareHolderEdit[$key]['id_number']['before_value'] = $every['id_number'];
                            $shareHolderEdit[$key]['id_number']['after_value'] = $value['id_number'];
                            $shareHolderEdit[$key]['id_number']['before_comment'] = $every['id_number'];
                            $shareHolderEdit[$key]['id_number']['after_comment'] = $value['id_number'];
                        }

                    }

                }, $originShareholderDataArr);

            }
        }

        if ($requestAddShareholder) {
            foreach ($requestAddShareholder as $key => $value) {
                foreach ($value as $k => $v) {
                    $shareHolderAdd[$key][$k]['key'] = $k;
                    $shareHolderAdd[$key][$k]['comment'] = $companyShareHoldersMap[$k];
                    $shareHolderAdd[$key][$k]['before_value'] = '';
                    $shareHolderAdd[$key][$k]['after_value'] = $v ? $v : '';
                    $shareHolderAdd[$key][$k]['before_comment'] = '';
                    if ($k == 'shareholder_type') {
                        $shareHolderAdd[$key][$k]['after_comment'] = $v ? $shareHolderTypeMap[$v] : "";
                    } elseif ($k == 'certificate_type') {
                        $shareHolderAdd[$key][$k]['after_comment'] = $v ? $certificateTypeMap[$v] : "";
                    } else {
                        $shareHolderAdd[$key][$k]['after_comment'] = $v ? $v : '';
                    }

                }
            }
        }

        if ($originShareholderDelDataArr) {
            $del_info_column = ['name', 'shareholder_type', 'certificate_type', 'id_number'];
            foreach ($originShareholderDelDataArr as $key => $value) {
                foreach ($value as $k => $v) {
                    if (in_array($k, $del_info_column)) {
                        $shareHolderDel[$value['id']][$k]['comment'] = $companyShareHoldersMap[$k];
                        $shareHolderDel[$value['id']][$k]['before_value'] = $v ? $v : '';
                        $shareHolderDel[$value['id']][$k]['after_value'] = '';
                        if ($k == 'shareholder_type') {
                            $shareHolderDel[$value['id']][$k]['before_comment'] = $v ? $shareHolderTypeMap[$v] : "";
                        } elseif ($k == 'certificate_type') {
                            $shareHolderDel[$value['id']][$k]['before_comment'] = $v ? $certificateTypeMap[$v] : "";
                        } else {
                            $shareHolderDel[$value['id']][$k]['before_comment'] = $v ? $v : '';
                        }
                        $shareHolderDel[$value['id']][$k]['after_comment'] = '';

                    }

                }
            }

        }

        if ($shareHolderDel) {
            $shareHolderChange['delete'] = $shareHolderDel;
        }

        if ($shareHolderEdit) {
            $shareHolderChange['edit'] = $shareHolderEdit;
        }

        if ($shareHolderAdd) {
            $shareHolderChange['add'] = $shareHolderAdd;
        }

        return $shareHolderChange;
    }

    /**
     * 对比主要人员信息
     * @param $companyId
     * @param $requestPersonnel
     * @param $requestAddPersonnel
     * @return array
     */
    public static function comparePersonnel($companyId, $requestPersonnel, $requestAddPersonnel, $requestDelPersonnel)
    {

        //主要人员信息
        $personnelChange = [];
        $personnelIds = array_keys($requestPersonnel);
        $originPersonnelData = CompanyMainPersonnels::where('company_id', '=', $companyId)->whereIn('id', $personnelIds)->get();
        $originPersonnelDelData = CompanyMainPersonnels::where('company_id', '=', $companyId)->whereIn('id', $requestDelPersonnel)->get();
        $originPersonnelDataArr = !$originPersonnelData->isEmpty() ? $originPersonnelData->toArray() : [];
        $originPersonnelDelDataArr = !$originPersonnelDelData->isEmpty() ? $originPersonnelDelData->toArray() : [];

        $personnelEdit = [];//编辑主要人员
        $personnelDel = [];//删除主要人员
        $personnelAdd = [];//添加主要人员

        $personnelsComment = CompanyMainPersonnels::getComment();

        if ($requestPersonnel) {
            foreach ($requestPersonnel as $key => $value) {
                array_map(function ($every) use ($key, $value, &$personnelEdit, $personnelsComment) {
                    if ($key == $every['id']) {
                        if ($value['name'] != $every['name'] || $value['position'] != $every['position']) {
                            $personnelEdit[$key]['name']['comment'] = $personnelsComment['name'];
                            $personnelEdit[$key]['name']['before_value'] = $every['name'];
                            $personnelEdit[$key]['name']['after_value'] = $value['name'];
                            $personnelEdit[$key]['name']['before_comment'] = $every['name'];
                            $personnelEdit[$key]['name']['after_comment'] = $value['name'];


                            $personnelEdit[$key]['position']['comment'] = $personnelsComment['position'];
                            $personnelEdit[$key]['position']['before_value'] = $every['position'];
                            $personnelEdit[$key]['position']['after_value'] = $value['position'];
                            $personnelEdit[$key]['position']['before_comment'] = $every['position'];
                            $personnelEdit[$key]['position']['after_comment'] = $value['position'];
                        }
                    }
                }, $originPersonnelDataArr);
            }
        }

        if ($originPersonnelDelDataArr) {
            $del_column_info = ['name', 'position'];

            foreach ($originPersonnelDelDataArr as $key => $value) {
                foreach ($value as $k => $v) {
                    if (in_array($k, $del_column_info)) {
                        $personnelDel[$value['id']][$k]['comment'] = $personnelsComment[$k];
                        $personnelDel[$value['id']][$k]['before_value'] = $v ? $v : '';
                        $personnelDel[$value['id']][$k]['after_value'] = '';
                        $personnelDel[$value['id']][$k]['before_comment'] = $v ? $v : '';
                        $personnelDel[$value['id']][$k]['after_comment'] = '';
                    }
                }
            }
        }


        if ($requestAddPersonnel) {
            foreach ($requestAddPersonnel as $key => $value) {
                foreach ($value as $k => $v) {
                    $personnelAdd[$key][$k]['key'] = $k;
                    $personnelAdd[$key][$k]['comment'] = $personnelsComment[$k];
                    $personnelAdd[$key][$k]['before_value'] = "";
                    $personnelAdd[$key][$k]['after_value'] = $v ? $v : "";
                    $personnelAdd[$key][$k]['before_comment'] = "";
                    $personnelAdd[$key][$k]['after_comment'] = $v ? $v : "";
                }
            }
        }


        if ($personnelDel) {
            $personnelChange['delete'] = $personnelDel;
        }

        if ($personnelEdit) {
            $personnelChange['edit'] = $personnelEdit;
        }

        if ($personnelAdd) {
            $personnelChange['add'] = $personnelAdd;
        }

        return $personnelChange;
    }

    /**
     * 对比股权出质信息
     * @param $companyId
     * @param $requestPledge
     * @param $requestAddPledge
     * @return array
     */
    public static function comparePledge($companyId, $requestPledge, $requestAddPledge, $requestDelPledge)
    {
        //股权出质信息
        $pledgeChange = [];
        $pledgeIds = array_keys($requestPledge);
        $originPledgeData = CompanyEquityPledge::where('company_id', '=', $companyId)->whereIn('id', $pledgeIds)->get();
        $originPledgeDelData = CompanyEquityPledge::where('company_id', '=', $companyId)->whereIn('id', $requestDelPledge)->get();
        $originPledgeDataArr = !$originPledgeData->isEmpty() ? $originPledgeData->toArray() : [];
        $originPledgeDelDataArr = !$originPledgeDelData->isEmpty() ? $originPledgeDelData->toArray() : [];

        $pledgeEdit = [];//编辑股权出质
        $pledgeDel = [];//删除股权出质
        $pledgeAdd = [];//添加股权出质


        $columnComment = CompanyEquityPledge::getComment();
        $pledgeStatusMap = CompanyEquityPledge::$pledgeStatusMap;
        if ($requestPledge) {
            foreach ($requestPledge as $key => $value) {
                array_map(function ($every) use ($key, $value, &$pledgeEdit, $columnComment, $pledgeStatusMap) {
                    if ($key == $every['id']) {
                        if ($value['code'] != $every['code'] || $value['pledgor'] != $every['pledgor'] || $value['pledgor_id_number'] != $every['pledgor_id_number'] ||
                            $value['amount'] != $every['amount'] || $value['pledgee'] != $every['pledgee'] || $value['pledgee_id_number'] != $every['pledgee_id_number'] ||
                            $value['register_date'] != $every['register_date'] || $value['pledge_status'] != $every['pledge_status'] ||
                            $value['public_at'] != $every['public_at']
                        ) {
                            $pledgeEdit[$key]['code']['comment'] = $columnComment['code'];
                            $pledgeEdit[$key]['code']['before_value'] = $every['code'];
                            $pledgeEdit[$key]['code']['after_value'] = $value['code'];
                            $pledgeEdit[$key]['code']['before_comment'] = $every['code'];
                            $pledgeEdit[$key]['code']['after_comment'] = $value['code'];


                            $pledgeEdit[$key]['pledgor']['comment'] = $columnComment['pledgor'];
                            $pledgeEdit[$key]['pledgor']['before_value'] = $every['pledgor'];
                            $pledgeEdit[$key]['pledgor']['after_value'] = $value['pledgor'];
                            $pledgeEdit[$key]['pledgor']['before_comment'] = $every['pledgor'];
                            $pledgeEdit[$key]['pledgor']['after_comment'] = $value['pledgor'];


                            $pledgeEdit[$key]['pledgor_id_number']['comment'] = $columnComment['pledgor_id_number'];
                            $pledgeEdit[$key]['pledgor_id_number']['before_value'] = $every['pledgor_id_number'];
                            $pledgeEdit[$key]['pledgor_id_number']['after_value'] = $value['pledgor_id_number'];
                            $pledgeEdit[$key]['pledgor_id_number']['before_comment'] = $every['pledgor_id_number'];
                            $pledgeEdit[$key]['pledgor_id_number']['after_comment'] = $value['pledgor_id_number'];


                            $pledgeEdit[$key]['amount']['comment'] = $columnComment['amount'];
                            $pledgeEdit[$key]['amount']['before_value'] = $every['amount'];
                            $pledgeEdit[$key]['amount']['after_value'] = $value['amount'];
                            $pledgeEdit[$key]['amount']['before_comment'] = $every['amount'];
                            $pledgeEdit[$key]['amount']['after_comment'] = $value['amount'];


                            $pledgeEdit[$key]['pledgee']['comment'] = $columnComment['pledgee'];
                            $pledgeEdit[$key]['pledgee']['before_value'] = $every['pledgee'];
                            $pledgeEdit[$key]['pledgee']['after_value'] = $value['pledgee'];
                            $pledgeEdit[$key]['pledgee']['before_comment'] = $every['pledgee'];
                            $pledgeEdit[$key]['pledgee']['after_comment'] = $value['pledgee'];


                            $pledgeEdit[$key]['pledgee_id_number']['comment'] = $columnComment['pledgee_id_number'];
                            $pledgeEdit[$key]['pledgee_id_number']['before_value'] = $every['pledgee_id_number'];
                            $pledgeEdit[$key]['pledgee_id_number']['after_value'] = $value['pledgee_id_number'];
                            $pledgeEdit[$key]['pledgee_id_number']['before_comment'] = $every['pledgee_id_number'];
                            $pledgeEdit[$key]['pledgee_id_number']['after_comment'] = $value['pledgee_id_number'];


                            $pledgeEdit[$key]['register_date']['comment'] = $columnComment['register_date'];
                            $pledgeEdit[$key]['register_date']['before_value'] = $every['register_date'];
                            $pledgeEdit[$key]['register_date']['after_value'] = $value['register_date'];
                            $pledgeEdit[$key]['register_date']['before_comment'] = $every['register_date'];
                            $pledgeEdit[$key]['register_date']['after_comment'] = $value['register_date'];

                            $pledgeEdit[$key]['pledge_status']['comment'] = $columnComment['pledge_status'];
                            $pledgeEdit[$key]['pledge_status']['before_value'] = $every['pledge_status'];
                            $pledgeEdit[$key]['pledge_status']['after_value'] = $value['pledge_status'];
                            $pledgeEdit[$key]['pledge_status']['before_comment'] = $every['pledge_status'] ? $pledgeStatusMap[$every['pledge_status']] : "";
                            $pledgeEdit[$key]['pledge_status']['after_comment'] = $value['pledge_status'] ? $pledgeStatusMap[$value['pledge_status']] : "";


                            $pledgeEdit[$key]['public_at']['comment'] = $columnComment['public_at'];
                            $pledgeEdit[$key]['public_at']['before_value'] = $every['public_at'];
                            $pledgeEdit[$key]['public_at']['after_value'] = $value['public_at'];
                            $pledgeEdit[$key]['public_at']['before_comment'] = $every['public_at'];
                            $pledgeEdit[$key]['public_at']['after_comment'] = $value['public_at'];
                        }

                    }
                }, $originPledgeDataArr);
            }
        }


        if ($originPledgeDelDataArr) {
            $del_info_column = [
                'code',
                'pledgor',
                'pledgor_id_number',
                'pledgor_id_number',
                'pledgee',
                'pledgee_id_number',
                'register_date',
                'pledge_status',
                'public_at',
            ];
            foreach ($originPledgeDelDataArr as $key => $value) {
                foreach ($value as $k => $v) {
                    if (in_array($k, $del_info_column)) {
                        $pledgeDel[$value['id']][$k]['comment'] = $columnComment[$k];
                        $pledgeDel[$value['id']][$k]['before_value'] = $v ? $v : '';
                        $pledgeDel[$value['id']][$k]['after_value'] = '';

                        if ($k == 'pledge_status') {
                            $pledgeDel[$value['id']][$k]['before_comment'] = $v ? $pledgeStatusMap[$v] : '';
                        } else {
                            $pledgeDel[$value['id']][$k]['before_comment'] = $v ? $v : '';
                        }

                        $pledgeDel[$value['id']][$k]['after_comment'] = '';
                    }
                }
            }
        }

        if ($requestAddPledge) {
            foreach ($requestAddPledge as $key => $value) {
                foreach ($value as $k => $v) {
                    $pledgeAdd[$key][$k]['key'] = $k;
                    $pledgeAdd[$key][$k]['comment'] = $columnComment[$k];
                    $pledgeAdd[$key][$k]['before_value'] = '';
                    $pledgeAdd[$key][$k]['after_value'] = $v ? $v : '';
                    $pledgeAdd[$key][$k]['before_comment'] = '';

                    if ($k == 'pledge_status') {
                        $pledgeAdd[$key][$k]['after_comment'] = $v ? $pledgeStatusMap[$v] : "";
                    } else {
                        $pledgeAdd[$key][$k]['after_comment'] = $v ? $v : '';
                    }
                }
            }
        }

        if ($pledgeDel) {
            $pledgeChange['delete'] = $pledgeDel;
        }

        if ($pledgeEdit) {
            $pledgeChange['edit'] = $pledgeEdit;
        }

        if ($pledgeAdd) {
            $pledgeChange['add'] = $pledgeAdd;
        }

        return $pledgeChange;
    }

    /**
     * 检查数据是否都是null
     * @param $data
     * @return mixed
     */
    public static function dataIsNull($data)
    {
        foreach ($data as $key => $every) {
            $isNull = true;
            foreach ($every as $value) {
                if ($value != null) {
                    $isNull = false;
                    break;
                }

            }

            if ($isNull) {
                unset($data[$key]);
            }

        }
        return $data;
    }

    public static function storeDataFromFlow($flowData)
    {

        $flowData = array_shift($flowData);
        $companyChangeInfo = json_decode($flowData['form_data']['company_change_info']['value'], true);
        $companyId = $flowData['form_data']['company_id']['value'];
        $applyUserId = $flowData['entry']['user_id'];
        $currentTime = date('Y-m-d H:i:s', time());
        $totalData = [];
        /**-----------------------------营业执照信息------------------------------------**/
        $companyChangeUpdateData = [];
        if (isset($companyChangeInfo['company_change']['edit'])) {
            $companyChangeEdit = $companyChangeInfo['company_change']['edit'][$companyId];
            foreach ($companyChangeEdit as $key => $value) {
                $companyChangeUpdateData[$key] = $value['after_value'];
            }
        }

        /**-----------------------------股东及出资------------------------------------**/
        $shareHoldersUpdateData = [];
        if (isset($companyChangeInfo['share_holder']['edit'])) {
            $shareHoldersEdit = $companyChangeInfo['share_holder']['edit'];
            foreach ($shareHoldersEdit as $key => $every) {
                foreach ($every as $k => $value) {
                    $shareHoldersUpdateData[$key][$k] = $value['after_value'];
                }
            }
        }

        $shareHoldersAddData = [];
        if (isset($companyChangeInfo['share_holder']['add'])) {
            $shareHoldersAdd = $companyChangeInfo['share_holder']['add'];
            foreach ($shareHoldersAdd as $key => $every) {
                foreach ($every as $k => $value) {
                    $shareHoldersAddData[$key][$k] = $value['after_value'];
                }
                $shareHoldersAddData[$key]['company_id'] = $companyId;
                $shareHoldersAddData[$key]['status'] = CompanyShareholders::STATUS_NO_DELETE;
                $shareHoldersAddData[$key]['created_at'] = $currentTime;
                $shareHoldersAddData[$key]['updated_at'] = $currentTime;
            }
        }

        $shareHoldersDelData = [];
        if (isset($companyChangeInfo['share_holder']['delete'])) {
            $shareHoldersDelData = array_keys($companyChangeInfo['share_holder']['delete']);
        }

        /**-----------------------------主要人员信息------------------------------------**/
        $personnelUpdateData = [];
        if (isset($companyChangeInfo['personnel']['edit'])) {
            $personnelEdit = $companyChangeInfo['personnel']['edit'];
            foreach ($personnelEdit as $key => $every) {
                foreach ($every as $k => $value) {
                    $personnelUpdateData[$key][$k] = $value['after_value'];
                }
            }
        }

        $personnelAddData = [];
        if (isset($companyChangeInfo['personnel']['add'])) {
            $personnelAdd = $companyChangeInfo['personnel']['add'];
            foreach ($personnelAdd as $key => $every) {
                foreach ($every as $k => $value) {
                    $personnelAddData[$key][$k] = $value['after_value'];
                }
                $personnelAddData[$key]['company_id'] = $companyId;
                $personnelAddData[$key]['status'] = CompanyMainPersonnels::STATUS_NO_DELETE;
                $personnelAddData[$key]['created_at'] = $currentTime;
                $personnelAddData[$key]['updated_at'] = $currentTime;
            }
        }

        $personnelDelData = [];
        if (isset($companyChangeInfo['personnel']['delete'])) {
            $personnelDelData = array_keys($companyChangeInfo['personnel']['delete']);
        }

        /**-----------------------------股权出质登记信息------------------------------------**/
        $pledgeUpdateData = [];
        if (isset($companyChangeInfo['pledge']['edit'])) {
            $pledgeEdit = $companyChangeInfo['pledge']['edit'];
            foreach ($pledgeEdit as $key => $every) {
                foreach ($every as $k => $value) {
                    $pledgeUpdateData[$key][$k] = $value['after_value'];
                }
            }
        }

        $pledgeAddData = [];
        if (isset($companyChangeInfo['pledge']['add'])) {
            $pledgeAdd = $companyChangeInfo['pledge']['add'];
            foreach ($pledgeAdd as $key => $every) {
                foreach ($every as $k => $value) {
                    $pledgeAddData[$key][$k] = $value['after_value'];
                }
            }
            $pledgeAddData[$key]['company_id'] = $companyId;
            $pledgeAddData[$key]['status'] = CompanyEquityPledge::STATUS_NO_DELETE;
            $pledgeAddData[$key]['created_at'] = $currentTime;
            $pledgeAddData[$key]['updated_at'] = $currentTime;
        }

        $pledgeDelData = [];
        if (isset($companyChangeInfo['pledge']['delete'])) {
            $pledgeDelData = array_keys($companyChangeInfo['pledge']['delete']);
        }

        $totalData['company_change_update'] = $companyChangeUpdateData;
        $totalData['share_holder_update'] = $shareHoldersUpdateData;
        $totalData['share_holder_add'] = $shareHoldersAddData;
        $totalData['share_holder_del'] = $shareHoldersDelData;
        $totalData['personnel_update'] = $personnelUpdateData;
        $totalData['personnel_add'] = $personnelAddData;
        $totalData['personnel_del'] = $personnelDelData;
        $totalData['pledge_update'] = $pledgeUpdateData;
        $totalData['pledge_add'] = $pledgeAddData;
        $totalData['pledge_del'] = $pledgeDelData;

        DB::beginTransaction();
        try {
            //保存变更信息
            self::saveChangeData($totalData, $companyId);
            //记录变更日志
            CompanyChange::saveChangeLog($applyUserId, $companyChangeInfo, $companyId);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $message = $e->getMessage();
            dd($message);
        }
    }


    /**
     * @param $totalData
     * @param $companyId
     * @throws \Exception
     */
    private static function saveChangeData($totalData, $companyId)
    {
        if ($totalData['company_change_update']) {
            $companyChangeUpdateResult = Company::where('id', '=', $companyId)->update($totalData['company_change_update']);
            if (!$companyChangeUpdateResult) {
                throw new DevFixException("营业执照信息编辑失败");
            }
        }

        if ($totalData['share_holder_update']) {
            foreach ($totalData['share_holder_update'] as $key => $updateData) {
                $shareHoldersUpdateResult = CompanyShareholders::where('id', '=', $key)->where('company_id', '=', $companyId)->update($updateData);
                if (!$shareHoldersUpdateResult) {
                    throw new DevFixException("股东及出资信息编辑失败");
                }
            }
        }

        if ($totalData['share_holder_add']) {
            $shareHoldersAddResult = CompanyShareholders::insert($totalData['share_holder_add']);
            if (!$shareHoldersAddResult) {
                throw new DevFixException("股东及出资信息添加失败");
            }
        }

        if ($totalData['share_holder_del']) {
            foreach ($totalData['share_holder_del'] as $value) {
                $shareHoldersDeleteResult = CompanyShareholders::where('id', $value)->where('company_id', '=',
                    $companyId)->update(['status' => CompanyShareholders::STATUS_DELETE]);
                if (!$shareHoldersDeleteResult) {
                    throw new DevFixException("股东及出资信息删除失败");
                }
            }
        }

        if ($totalData['personnel_update']) {
            foreach ($totalData['personnel_update'] as $key => $updateData) {
                $personnelUpdateResult = CompanyMainPersonnels::where('id', '=', $key)->where('company_id', '=', $companyId)->update($updateData);
                if (!$personnelUpdateResult) {
                    throw new DevFixException("主要人员信息编辑失败");
                }
            }
        }

        if ($totalData['personnel_add']) {
            $personnelAddResult = CompanyMainPersonnels::insert($totalData['personnel_add']);
            if (!$personnelAddResult) {
                throw new DevFixException("主要人员信息添加失败");
            }
        }

        if ($totalData['personnel_del']) {
            foreach ($totalData['personnel_del'] as $value) {
                $personnelDeleteResult = CompanyMainPersonnels::where('id', '=', $value)->where('company_id', '=',
                    $companyId)->update(['status' => CompanyMainPersonnels::STATUS_DELETE]);
                if (!$personnelDeleteResult) {
                    throw new DevFixException("主要人员信息删除失败");
                }
            }
        }

        if ($totalData['pledge_update']) {
            foreach ($totalData['pledge_update'] as $key => $updateData) {
                $pledgeUpdateResult = CompanyEquityPledge::where('id', '=', $key)->where('company_id', '=', $companyId)->update($updateData);
                if (!$pledgeUpdateResult) {
                    throw new DevFixException("股权出质登记信息编辑失败");
                }
            }
        }

        if ($totalData['pledge_add']) {
            $pledgeAddResult = CompanyEquityPledge::insert($totalData['pledge_add']);
            if (!$pledgeAddResult) {
                throw new DevFixException("股权出质登记信息添加失败");
            }
        }

        if ($totalData['pledge_del']) {
            foreach ($totalData['pledge_del'] as $value) {
                $pledgeDeleteResult = CompanyEquityPledge::where('id', '=', $value)->where('company_id', '=',
                    $companyId)->update(['status' => CompanyEquityPledge::STATUS_DELETE]);
                if (!$pledgeDeleteResult) {
                    throw new DevFixException("股权出质登记信息删除失败");
                }
            }
        }
    }

    /**
     * 查询有效公司
     * @param $query
     * @return mixed
     */
    public static function scopeFindNoDelete($query)
    {
        return $query->whereStatus(self::STATUS_NO_DELETE);
    }

    /**
     * 获取公司列表
     * @return $this
     */
    public static function fetchCompanyList()
    {
        return $companies = Company::FindNoDelete()->orderBy('id','desc')->pluck('name', 'id');
    }

}
