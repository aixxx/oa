<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 2018/5/31
 * Time: 16:31
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

/**
 * App\Models\UsersDetailInfo
 *
 * @property int $id
 * @property int $user_id 员工ID
 * @property string|null $office_location 办公地点
 * @property string|null $note 备注
 * @property string|null $probation 试用期
 * @property string|null $after_probation 转正日期
 * @property string|null $grade 岗位职级
 * @property string|null $id_name 身份证姓名
 * @property string|null $id_number 证件号码
 * @property string|null $born_time 出生日期
 * @property string|null $ethnic 民族
 * @property string|null $id_address 身份证地址
 * @property string|null $validity_certificate 证件有效期
 * @property string|null $address 住址
 * @property string|null $first_work_time 首次参见工作时间
 * @property string|null $per_social_account 个人社保账号
 * @property string|null $per_fund_account 个人公积金账号
 * @property string|null $highest_education 最高学历
 * @property string|null $graduate_institutions 毕业院校
 * @property string|null $graduate_time 毕业时间
 * @property string|null $major 所学专业
 * @property string|null $bank_card 银行卡号
 * @property string|null $bank 开户行
 * @property string|null $contract_company 合同公司
 * @property string|null $first_contract_start_time 首次合同起始日
 * @property string|null $first_contract_end_time 首次合同到期日
 * @property string|null $cur_contract_start_time 现合同起始日
 * @property string|null $cur_contract_end_time 现合同到期日
 * @property string|null $contract_term 合同期限
 * @property string|null $renew_times 续签次数
 * @property string|null $emergency_contact 紧急联系人姓名
 * @property string|null $contact_relationship 联系人关系
 * @property string|null $contact_mobile 联系人电话
 * @property string|null $has_children 有无子女
 * @property string|null $child_name 子女姓名
 * @property string|null $child_gender 子女性别(1.男;2.女;0.未知)
 * @property string|null $child_born_time 子女出生日期
 * @property mixed|null $pic_id_pos
 * @property mixed|null $pic_id_neg
 * @property mixed|null $pic_edu_background
 * @property mixed|null $pic_degree
 * @property mixed|null $pic_pre_company
 * @property mixed|null $pic_user
 * @property string|null $user_type 员工类型
 * @property string|null $user_status 员工状态
 * @property string|null $census_type 户籍类型
 * @property string|null $politics_status 政治面貌
 * @property string|null $marital_status 婚姻状况
 * @property string|null $contract_type 合同类型
 * @property string|null $decripted_first_work_time 明文
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersDetailInfo whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersDetailInfo whereAfterProbation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersDetailInfo whereBank($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersDetailInfo whereBankCard($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersDetailInfo whereBornTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersDetailInfo whereCensusType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersDetailInfo whereChildBornTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersDetailInfo whereChildGender($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersDetailInfo whereChildName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersDetailInfo whereContactMobile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersDetailInfo whereContactRelationship($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersDetailInfo whereContractCompany($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersDetailInfo whereContractTerm($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersDetailInfo whereContractType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersDetailInfo whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersDetailInfo whereCurContractEndTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersDetailInfo whereCurContractStartTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersDetailInfo whereEmergencyContact($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersDetailInfo whereEthnic($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersDetailInfo whereFirstContractEndTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersDetailInfo whereFirstContractStartTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersDetailInfo whereFirstWorkTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersDetailInfo whereGrade($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersDetailInfo whereGraduateInstitutions($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersDetailInfo whereGraduateTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersDetailInfo whereHasChildren($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersDetailInfo whereHighestEducation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersDetailInfo whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersDetailInfo whereIdAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersDetailInfo whereIdName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersDetailInfo whereIdNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersDetailInfo whereMajor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersDetailInfo whereMaritalStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersDetailInfo whereNote($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersDetailInfo whereOfficeLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersDetailInfo wherePerFundAccount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersDetailInfo wherePerSocialAccount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersDetailInfo wherePicDegree($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersDetailInfo wherePicEduBackground($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersDetailInfo wherePicIdNeg($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersDetailInfo wherePicIdPos($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersDetailInfo wherePicPreCompany($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersDetailInfo wherePicUser($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersDetailInfo wherePoliticsStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersDetailInfo whereProbation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersDetailInfo whereRenewTimes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersDetailInfo whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersDetailInfo whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersDetailInfo whereUserStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersDetailInfo whereUserType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersDetailInfo whereValidityCertificate($value)
 * @mixin \Eloquent
 * @property string|null $branch_bank 支行名称
 * @property string|null $bank_province 银行卡属地：省
 * @property string|null $bank_city 银行卡属地：市
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersDetailInfo whereBankCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersDetailInfo whereBankProvince($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersDetailInfo whereBranchBank($value)
 */
class UsersDetailInfo extends Model
{
    protected $table = 'users_detail_info';

    protected $decripted_first_work_time;

    const STAFF_TYPE_FULL_TIME = 1;
    const STAFF_TYPE_PART_TIME = 2;
    const STAFF_TYPE_LABOR = 3;
    const STAFF_TYPE_OUT_SOURCE = 4;
    const STAFF_TYPE_REHIRE = 5;
    const STAFF_TYPE_INTERNSHIP = 6;
    public static $staffTypeList = [
        self::STAFF_TYPE_FULL_TIME => '全职',
        self::STAFF_TYPE_PART_TIME => '兼职',
        self::STAFF_TYPE_LABOR => '劳务派遣',
        self::STAFF_TYPE_OUT_SOURCE => '劳务外包',
        self::STAFF_TYPE_REHIRE => '退休返聘',
        self::STAFF_TYPE_INTERNSHIP => '实习',
    ];

    const STATUS_IS_POSITIVE = 1;
    const STAFF_PROBATION_PERIOD = 2;
    const STATUS_LEAVE = 3;

    public static $staffStateList = [
        self::STATUS_IS_POSITIVE => '正式',
        self::STAFF_PROBATION_PERIOD => '试用期',
        self::STATUS_LEAVE => '离职',
    ];
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'user_id',
        'office_location',
        'note',
        'probation',
        'after_probation',
        'grade',
        'id_name',
        'id_number',
        'born_time',
        'ethnic',
        'id_address',
        'validity_certificate',
        'address',
        'first_work_time',
        'per_social_account',
        'per_fund_account',
        'highest_education',
        'graduate_institutions',
        'graduate_time',
        'major',
        'bank_card',
        'bank',
        'contract_company',
        'first_contract_start_time',
        'first_contract_end_time',
        'cur_contract_start_time',
        'cur_contract_end_time',
        'contract_term',
        'renew_times',
        'emergency_contact',
        'contact_relationship',
        'contact_mobile',
        'has_children',
        'child_name',
        'child_gender',
        'child_born_time',
        'pic_id_pos',
        'pic_id_neg',
        'pic_edu_background',
        'pic_degree',
        'pic_pre_company',
        'pic_user',
        'user_type',
        'user_status',
        'census_type',
        'politics_status',
        'marital_status',
        'contract_type',
        'created_at',
        'updated_at',
        'branch_bank',
        'bank_province',
        'bank_city',
        'nationality',
        'gender',
        'alipay_account',
        'wechat_account',
        'id_detailed_address',
        'detailed_address',
        'id_card_number',
        'certificate_type',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    public function user()
    {
        return $this->hasOne('App\Models\User', 'id', 'user_id')->select('users.id', 'chinese_name', 'avatar', 'employee_num', 'join_at', 'position');
    }


    /**
     * @param Object $detailObj
     * 解密查询出来的详细信息
     */
    static public function decryptSelectData($detailObj)
    {
        $noNeedDecrypt = [
            'id',
            'user_id',
            'created_at',
            'updated_at',
            'first_work_time',
            'user_type',
            'user_status',
            "id_number",
            "certificate_type",
            "bank",
            "branch_bank",
            "pic_id_pos",
            "pic_user"
        ];
        $detailArr = [];
        if ($detailObj) {
            $detailArr = $detailObj->toArray();
            foreach ($detailArr as $key => &$value) {
                if (!in_array($key, $noNeedDecrypt)) {
                    if ($value) {
                        $value = decrypt($value);
                    }
                }
            }
        }


        return $detailArr;
    }


    /**
     * @param Object $detailObj
     * 解密查询出来的详细信息
     */
    static public function decryptSelectDatas($detailObj)
    {
        $noNeedDecrypt = [
            "id_name",
            "ethnic",
            "born_time",
            "id_card_number",
            "validity_certificate",
            "id_address",
            "id_detailed_address",
            "address",
            "detailed_address",
            "census_type",
            "marital_status",
            "politics_status",
            "per_social_account",
            "per_fund_account",
            'highest_education',
            'graduate_institutions',
            'graduate_time',
            'major',
            "bank_card",
            "alipay_account",
            "wechat_account",
        ];
        $detailArr = [];
        if ($detailObj) {
            $detailArr = $detailObj->toArray();
            foreach ($detailArr as $key => &$value) {
                if (in_array($key, $noNeedDecrypt)) {
                    if ($value) {
                        $value = decrypt($value);
                    }
                }
            }
        }


        return $detailArr;
    }

    /**
     * @param array $data
     * 解密将要更新的信息
     */
    static public function decryptUpdateData($data)
    {
        foreach ($data as $key => &$value) {
            if ($value) {
                $value = decrypt($value);
            }
        }
        return $data;
    }

    static public function detailColMapComment()
    {
        $map = [
            'office_location' => '办公地点',
            'note' => '备注',
            'probation' => '试用期',
            'after_probation' => '转正日期',
            'grade' => '岗位职级',
            'id_name' => '身份证姓名',
            'id_number' => '证件号码',
            'born_time' => '出生日期',
            'ethnic' => '民族',
            'id_address' => '身份证地址',
            'validity_certificate' => '证件有效期',
            'address' => '住址',
            'first_work_time' => '首次参见工作时间',
            'per_social_account' => '个人社保账号',
            'per_fund_account' => '个人公积金账号',
            'highest_education' => '最高学历',
            'graduate_institutions' => '毕业院校',
            'graduate_time' => '毕业时间',
            'major' => '所学专业',
            'bank_card' => '银行卡号',
            'bank' => '开户行',
            'contract_company' => '合同公司',
            'first_contract_start_time' => '首次合同起始日',
            'first_contract_end_time' => '首次合同到期日',
            'cur_contract_start_time' => '现合同起始日',
            'cur_contract_end_time' => '现合同到期日',
            'contract_term' => '合同期限',
            'renew_times' => '续签次数',
            'emergency_contact' => '紧急联系人姓名',
            'contact_relationship' => '联系人关系',
            'contact_mobile' => '联系人电话',
            'has_children' => '有无子女',
            'child_name' => '子女姓名',
            'child_gender' => '子女性别',
            'child_born_time' => '子女出生日期',
            'pic_id_pos' => '身份证（人像面）',
            'pic_id_neg' => '身份证（国徽面）',
            'pic_edu_background' => '学历证书',
            'pic_degree' => '学位证书',
            'pic_pre_company' => '前公司离职证明',
            'pic_user' => '员工照片',
            'user_type' => '员工类型',
            'user_status' => '员工状态',
            'census_type' => '户籍类型',
            'politics_status' => '政治面貌',
            'marital_status' => '婚姻状况',
            'contract_type' => '合同类型',
            'nationality' => '国籍',
            'gender' => '性别',
            'alipay_account' => '支付宝账号',
            'wechat_account' => '微信账号',
            'branch_bank' => '支行名称',
            'certificate_type' => '证件类型'
        ];

        return $map;
    }

    public function getFirstWorkTimeAttribute()
    {
        if (!$this->decripted_first_work_time) {
            $this->decripted_first_work_time = $this->attributes['first_work_time'] ? decrypt($this->attributes['first_work_time']) : $this->attributes['first_work_time'];
        }
        return $this->decripted_first_work_time;
    }

    public function setFirstWorkTimeAttribute($value)
    {
        if (strtotime($value)) {
            $this->decripted_first_work_time = $value;
            $this->attributes['first_work_time'] = encrypt($value);
        } else {
            $this->attributes['first_work_time'] = $value;
        }
    }

    public static function findByUserId($user_id)
    {
        return UsersDetailInfo::where('user_id', $user_id)->first();
    }
}