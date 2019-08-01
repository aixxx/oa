<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Http\Helpers\Dh;
use Exception;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use DevFixException;
use UserFixException;

/**
 * App\Models\Contract
 *
 * @property int $id
 * @property string $number 合同编号
 * @property string $name 合同名称
 * @property int $applicant 申请人
 * @property int $business_type 业务类型
 * @property int $payment_type 付款方式
 * @property int $income_expenses_type 收支类型
 * @property float $amount 合同金额
 * @property int $matured_status 到期情形
 * @property int $electronic_id 电子版合同上传id
 * @property int $scan_id 扫描件合同上传id
 * @property string $apply_at 合同发起日期
 * @property string $check_at 合同审核结束日期
 * @property string $start_at 合同生效日期
 * @property string $end_at 合同结束日期
 * @property int $status 合同状态
 * @property string $parties 合同主体 json数据
 * @property string $remark 合同备注
 * @property string $company_abbr 公司缩写
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Contract whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Contract whereApplicant($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Contract whereApplyAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Contract whereBusinessType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Contract whereCheckAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Contract whereCompanyAbbr($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Contract whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Contract whereElectronicId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Contract whereEndAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Contract whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Contract whereIncomeExpensesType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Contract whereMaturedStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Contract whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Contract whereNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Contract whereParties($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Contract wherePaymentType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Contract whereRemark($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Contract whereScanId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Contract whereStartAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Contract whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Contract whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property string $accessory 附件
 * @property int $filing 是否归档
 * @property int|null $entry_id 流程申请id
 * @property-read \App\Models\Workflow\Entry $entry
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Contract whereAccessory($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Contract whereEntryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Contract whereFiling($value)
 * @property string $contract_business_side 合同业务方
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Contract whereContractBusinessSide($value)
 */
class Contract extends Model
{
    const CONTRACT_BUSINESS_TYPE_EXPAND          = 1; //推广
    const CONTRACT_BUSINESS_TYPE_PERSONNEL       = 2; //人事行政
    const CONTRACT_BUSINESS_TYPE_RISK_MANAGEMENT = 3; //风控
    const CONTRACT_BUSINESS_TYPE_BRAND           = 4; //品牌
    const CONTRACT_BUSINESS_TYPE_CAPITAL         = 5; //资金合作
    const CONTRACT_BUSINESS_TYPE_STB             = 6; //STB商务合作
    const CONTRACT_BUSINESS_TYPE_CHANNEL         = 7; // 通道（支付/短信）
    const CONTRACT_BUSINESS_TYPE_INTERNAL        = 8; //内部合作
    const CONTRACT_BUSINESS_TYPE_FINANCE         = 9; //财务
    const CONTRACT_BUSINESS_TYPE_GR              = 10; //财务
    const CONTRACT_BUSINESS_TYPE_LAW             = 11; //法务
    /**
     * 业务类型
     *
     * @var array
     */
    public static $businessType = [
        self::CONTRACT_BUSINESS_TYPE_EXPAND          => "推广",
        self::CONTRACT_BUSINESS_TYPE_PERSONNEL       => "人事行政",
        self::CONTRACT_BUSINESS_TYPE_RISK_MANAGEMENT => "风控",
        self::CONTRACT_BUSINESS_TYPE_BRAND           => "品牌",
        self::CONTRACT_BUSINESS_TYPE_CAPITAL         => "资金合作",
        self::CONTRACT_BUSINESS_TYPE_STB             => "STB商务合作",
        self::CONTRACT_BUSINESS_TYPE_CHANNEL         => "通道（支付/短信）",
        self::CONTRACT_BUSINESS_TYPE_INTERNAL        => "内部合作",
        self::CONTRACT_BUSINESS_TYPE_FINANCE         => "财务",
        self::CONTRACT_BUSINESS_TYPE_GR              => "GR",
        self::CONTRACT_BUSINESS_TYPE_LAW             => "法务",
    ];


    const CONTRACT_INCOME_TYPE          = 1; //收入
    const CONTRACT_EXPENSES_TYPE        = 2; //支出
    const CONTRACT_INCOME_EXPENSES_NONE = 3; //无资金往来
    /**
     * 收支类型
     *
     * @var array
     */
    public static $incomeExpensesType = [
        self::CONTRACT_INCOME_TYPE          => "收入",
        self::CONTRACT_EXPENSES_TYPE        => "支出",
        self::CONTRACT_INCOME_EXPENSES_NONE => "无资金往来",
    ];

    public static $incomeExpensesCode = [
        'R' => "收入",
        'E' => "支出",
        'F' => "无资金往来",
    ];

    const CONTRACT_PAYMENT_TYPE_SINGLE = 1; //一次性付款
    const CONTRACT_PAYMENT_TYPE_STAGE  = 2; //分期付款
    const CONTRACT_PAYMENT_TYPE_NONE   = 3; //无资金往来
    /**
     * 付款方式
     *
     * @var array
     */
    public static $paymentType = [
        self::CONTRACT_PAYMENT_TYPE_SINGLE => "一次性付款",
        self::CONTRACT_PAYMENT_TYPE_STAGE  => "分期付款",
        self::CONTRACT_PAYMENT_TYPE_NONE   => "无资金往来",
    ];


    const CONTRACT_STATUS_CANCEL            = -1; //已撤销
    const CONTRACT_STATUS_PROCESSING        = 1; //审批中
    const CONTRACT_STATUS_REJECTED          = 2; //已驳回
    const CONTRACT_STATUS_SEAL_TO_BE_USED   = 3; //已审批待用印
    const CONTRACT_STATUS_TO_BE_ARCHIVED    = 4; //已用印待存档
    const CONTRACT_STATUS_HAS_BEEN_ARCHIVED = 5; //已存档
    /**
     * 合同状态
     *
     * @var array
     */
    public static $status = [
        self::CONTRACT_STATUS_CANCEL            => "已撤销",
        self::CONTRACT_STATUS_PROCESSING        => "审批中",
        self::CONTRACT_STATUS_REJECTED          => "已驳回",
        self::CONTRACT_STATUS_SEAL_TO_BE_USED   => "已审批待用印",
        self::CONTRACT_STATUS_TO_BE_ARCHIVED    => "已用印待存档",
        self::CONTRACT_STATUS_HAS_BEEN_ARCHIVED => "已存档",
    ];


    const CONTRACT_PARTY_TYPE_COMPANY = 1; // 公司
    const CONTRACT_PARTY_TYPE_SINGLE  = 2; // 个人
    /**
     * 合同主体类型
     *
     * @var array
     */
    public static $partyType = [
        self::CONTRACT_PARTY_TYPE_COMPANY => "公司",
        self::CONTRACT_PARTY_TYPE_SINGLE  => "个人",
    ];


    const CONTRACT_MATURED_STATUS_AUTO     = 1; //自动续期
    const CONTRACT_MATURED_STATUS_NON_AUTO = 2; // 非自动续期
    /**
     * 到期情形
     *
     * @var array
     */
    public static $maturedStatus = [
        self::CONTRACT_MATURED_STATUS_AUTO     => "自动续期",
        self::CONTRACT_MATURED_STATUS_NON_AUTO => "非自动续期",
    ];

    /**
     * 公司列表
     *
     * @var array
     */
    public static $companyMap = [
        'DH'     => '上海淡红金融科技有限公司',
        'F&F'    => 'Fortune&Future Inc.',
        'MX'     => '墨讯信息科技(上海)有限公司',
        'QZ'     => '上海乾昭信息技术有限公司',
        'SHQSQ'  => '上海乾生乾金融信息服务有限公司',
        'CY'     => '上海氚悦信息科技有限公司',
        'QX'     => '上海前夕金融科技有限公司',
        'QSL'    => '上海乾升利金融信息服务有限公司',
        'YQ'     => '萍乡市云桥科技有限公司',
        'FL'     => '成都芬睐科技有限公司',
        'QJJ'    => '深圳市钱京京商业保理有限公司',
        'WZ'     => '上海万枝投资管理有限公司',
        'FG'     => '北京芬果畅游科技有限公司',
        'YL'     => '上海隐隆资产管理有限公司',
        'SR'     => '成都双睿信息技术有限公司',
        'XY'     => '萍乡市星游网络科技有限公司',
        'SZQSQ'  => '深圳市钱生钱互联网金融服务有限公司',
        'F&F HK' => 'Fortune&Future Hong Kong Limited.',
        'TM'     => '扬州市泰美信息技术有限公司',
        'DX'     => '扬州市达迅信息技术有限公司',
        'SS'     => '扬州舜水信息技术有限公司',
        'YTJ'    => '深圳市云通聚科技有限公司',
        'YZ'     => '萍乡市云智网络科技有限公司',
        'PXQZ'   => '萍乡市乾昭信息技术有限公司',
        'PXWD'   => '萍乡唯渡金融服务外包有限公司',
        'YJ'     => '重庆杨隽有限公司',
        'WE'     => '萍乡市维易信息科技有限公司',
        'WX'     => '萍乡唯信信息科技有限公',
        'TS'     => '上海腾梭科技有限公司',
        'QL'     => '萍乡浅蓝信息科技有限公司',
        'JF'     => '萍乡嘉法科技有限公司',
        'WH'     => '成都维翰信息技术有限公司',
        'YT'     => '成都云亭信息技术有限公司',
        'YX'     => '上海翼寻信息技术有限公司',
        'SY'     => '上海数运贸易有限公司',
        'HM'     => '上海火穆信息技术有限公司',
        'CN'     => '创逆互联网金融信息服务(上海)有限公司',
        'YC'     => '上海源涔信息技术有限公司',
    ];


    protected $fillable = [
        'number',
        'name',
        'applicant',
        'business_type',
        'income_expenses_type',
        'payment_type',
        'amount',
        'matured_status',
        'accessory',
        'apply_at',
        'check_at',
        'start_at',
        'end_at',
        'status',
        'parties',
        'remark',
        'company_abbr',
        'filing',
        'electronic_id',
        'scan_id',
        'entry_id',
        'contract_business_side',
    ];

    /**
     * 生成合同编号
     *
     * @param $data
     *
     * @return string
     */

    public static function generateContractNumber($data)
    {
        $data = [
            'first_code'  => self::getCompanyNameList()->search(self::fetchSubjectCompany($data['contract_subject_info']['value'])),
            'second_code' => collect(self::$incomeExpensesCode)->search($data['in_out_type']['value']),
            'third_code'  => date('Ym'),
        ];

        $currentYear = date('Y', time());
        $total       = self::where('company_abbr', '=', $data['first_code'])
            ->whereYear('created_at', '=', $currentYear)
            ->count();
        if ($total > 0) {
            if (strlen(strval($total)) < 4) {
                $newSerial = str_pad(strval($total + 1), 4, "0", STR_PAD_LEFT);
            } else {
                $newSerial = strval($total + 1);
            }
        } else {
            $newSerial = str_pad("1", 4, "0", STR_PAD_LEFT);
        }

        $newContractNumber = $data['first_code'] . '-' . $data['second_code'] . '-' . $data['third_code'] . '-' .
            $newSerial;

        return $newContractNumber;
    }

    public static function fetchSubjectCompany($contractSubjectInfo)
    {
        $filter = collect(json_decode($contractSubjectInfo, true))->filter(function ($item, $key) {
            return $item['0'] == '甲方';
        })->pluck('2', '0');

        return $filter['甲方'];
    }

    /**
     *  获取公司列表
     *
     * @return array
     */
    public static function getCompanyNameList()
    {
        $companies = Company::where('status', '=', Company::STATUS_NO_DELETE)->get();
        $return    = $companies->pluck('name', 'abbr');
        return $return;
    }

    public static function getCompanyIdNameList()
    {
        $companies = Company::all();
        $return    = $companies->pluck('name', 'id');
        return $return;
    }


    /**
     * 获取申请人发起的合同
     *
     * @param null $startAt
     * @param null $endAt
     *
     * @return mixed
     * @throws Exception
     */
    public static function getLatestContract($startAt = null, $endAt = null)
    {
        if ((!$startAt && $endAt) || ($startAt && !$endAt)) {
            throw new UserFixException("日期传入有误");
        }

        if ($startAt && $endAt) {
            $contracts = self::select('*')->where('apply_at', '>=', $startAt)->where('apply_at', '<=', $endAt)
                ->where('applicant', '=', auth()->id())->orderBy('apply_at', 'desc')->where('status', self::CONTRACT_STATUS_REJECTED)->get();
        }

        if (!$startAt && !$endAt) {
            $contracts = self::where('applicant', '=', auth()->id())->orderBy('apply_at', 'desc')->where('status', self::CONTRACT_STATUS_REJECTED)->get();
        }

        return $contracts;
    }

    /**
     * 解析数据
     *
     * @param $data
     *
     * @return mixed
     */
    public static function formatData($data)
    {
        if ($data instanceof LengthAwarePaginator || $data instanceof Collection) {
            $data->each(function ($item, $key) {
                $item->business_type        = self::$businessType[$item->business_type];
                $item->payment_type         = self::$paymentType[$item->payment_type];
                $item->income_expenses_type = self::$incomeExpensesType[$item->income_expenses_type];
                $item->matured_status       = self::$maturedStatus[$item->matured_status];
                $item->status_name          = self::$status[$item->status];
                $item->primary_dept_name    = self::getPrimaryDeptName($item->applicant);

                if ($item->parties) {
                    $item->parties = json_decode($item->parties);
                }
                if (strtotime($item->apply_at) < 0) {
                    $item->apply_at = "";
                }
                if (strtotime($item->check_at) < 0) {
                    $item->check_at = "";
                }
                if (strtotime($item->start_at) < 0) {
                    $item->start_at = "";
                }
                if (strtotime($item->end_at) < 0) {
                    $item->end_at = "";
                }

                if ($item->contract_business_side) {
                    $item->contract_business_side = self::getCompanyIdNameList()[$item->contract_business_side];
                }

            });
        } elseif ($data instanceof Contract) {
            $data->business_type        = self::$businessType[$data->business_type];
            $data->payment_type         = self::$paymentType[$data->payment_type];
            $data->income_expenses_type = self::$incomeExpensesType[$data->income_expenses_type];
            $data->matured_status       = self::$maturedStatus[$data->matured_status];
            $data->status_name          = self::$status[$data->status];
            $data->primary_dept_name    = self::getPrimaryDeptName($data->applicant);
            if ($data->parties) {
                $data->parties = json_decode($data->parties);
            }
            if (strtotime($data->apply_at) < 0) {
                $data->apply_at = "";
            }
            if (strtotime($data->check_at) < 0) {
                $data->check_at = "";
            }
            if (strtotime($data->start_at) < 0) {
                $data->start_at = "";
            }
            if (strtotime($data->end_at) < 0) {
                $data->end_at = "";
            }

            if ($data->contract_business_side) {
                $data->contract_business_side = self::getCompanyIdNameList()[$data->contract_business_side];
            }
        }

        return $data;
    }

    /**
     * 保存或者更新合同信息
     *
     * @param $data
     *
     * @throws Exception
     */
    public static function saveOrUpdateContract($data)
    {
        if (!$data) {
            throw new UserFixException('数据不能为空');
        }

        $arrInfo = collect($data)->first();
        return $arrInfo['form_data']['contract_no_type']['value'] == self::updateContract($data);
    }

    /**
     * 更新合同状态
     *
     * @param $contractNumber
     * @param $status
     *
     * @return bool
     * @throws Exception
     */
    public static function updateContractStatus($contractNumber, $status)
    {
        if (!$contractNumber) {
            throw new UserFixException("未传递合同编号");
        }

        if (!$status) {
            throw new UserFixException("未传递合同状态值");
        }

        $contract = Contract::where('number', $contractNumber)->first();

        if (!$contract) {
            throw new UserFixException(sprintf("编号为%s的合同不存在", $contractNumber));
        }

        if (!$contract->update(['status' => $status])) {
            throw new DevFixException(sprintf("编号为%s的合同更新状态失败", $contractNumber));
        }

        return true;
    }


    /**
     * 保存合同信息
     *
     * @param $data
     *
     * @return bool
     * @throws \Exception
     */
    public static function storeContract($data)
    {
        $contractModel = new Contract();
        $contractModel->fill(self::fetchFormData($data));
        if (!$contractModel->save()) {
            throw  new DevFixException("合同信息保存失败");
        }

        return true;
    }

    public static function fetchFormData($infoData)
    {
        $info      = array_pop($infoData);
        $formData  = $info['form_data'];
        $entryData = $info['entry'];


        if (isset($formData['company_name'])) {
            $companyInfo = Company::getCompanyIdByName($formData['company_name']['value']);
        }

        $data = [
            'entry_id'               => $entryData['id'],
            'number'                 => isset($formData['contract_no_type']) ? $formData['contract_no_type']['value'] : '',
            'name'                   => isset($formData['contract_name']) ? $formData['contract_name']['value'] : '',
            'applicant'              => $info['entry']['user_id'], //申请人userId
            'business_type'          => isset($formData['business_type']) ? collect(self::$businessType)->search(trim($formData['business_type']['value'])) : '',
            'income_expenses_type'   => isset($formData['in_out_type']) ? collect(self::$incomeExpensesType)->search(trim($formData['in_out_type']['value'])) : '',
            'payment_type'           => isset($formData['pay_type']) ? collect(self::$paymentType)->search(trim($formData['pay_type']['value'])) : '',
            'amount'                 => isset($formData['contract_amount']) ? $formData['contract_amount']['value'] : '',
            'matured_status'         => isset($formData['due_condition']) ? collect(self::$maturedStatus)->search(trim($formData['due_condition']['value'])) : '',
            'apply_at'               => isset($formData['contract_start_date_select']) && isset($formData['contract_start_date']) && trim($formData['contract_start_date_select']['value']) == '有明确生效日' ?
                $formData['contract_start_date']['value'] : '',
            'check_at'               => isset($formData['contract_end_date_select']) && isset($formData['contract_end_date']) && trim($formData['contract_end_date_select']['value']) == '有明确终止日' ?
                $formData['contract_end_date']['value'] : '',
            'electronic_id'          => isset($formData['file_upload']) ? $formData['file_upload']['value'] : '',
            'remark'                 => isset($formData['note']) ? $formData['note']['value'] : '',
            'contract_business_side' => (isset($companyInfo) && $companyInfo) ? $companyInfo->id : 0,
            'parties'                => isset($formData['contract_subject_info']) ? $formData['contract_subject_info']['value'] : '',
            'status'                 => self::CONTRACT_STATUS_PROCESSING,
            'company_abbr'           => isset($formData['contract_subject_info']) ? self::getCompanyNameList()->search(self::fetchSubjectCompany($formData['contract_subject_info']['value'])) : '',
        ];

        return $data;
    }

    /**
     * @param $contractId
     * @param $data
     *
     * @return bool
     * @throws Exception
     */
    public static function updateContract($data)
    {

        $updateData = self::fetchFormData($data);

        $contract = self::where('number', '=', $updateData['number'])->first();

        if (!$contract || ($contract && $contract->status == Contract::CONTRACT_STATUS_CANCEL || $contract->status == Contract::CONTRACT_STATUS_REJECTED)) {   //驳回和撤回状态的合同编号可以再次使用
            self::storeContract($data);
        } else {
            return false;
            /* if (!$contract->update($updateData)) {
                 throw new Exception("合同信息编辑失败");
             }*/
        }
        return true;
    }


    /**
     * 校验合同是否属于个人
     *
     * @param $userId
     * @param $number
     *
     * @throws Exception
     */
    public static function validContactNumber($userId, $number)
    {
        $contact = self::where('number', $number)->first();
        if ($number && $contact) {
            if ($contact->applicant != $userId) {
                throw new UserFixException("该合同不属于该合同发起人");
            }
            if ($contact->status != self::CONTRACT_STATUS_SEAL_TO_BE_USED) {
                throw new UserFixException("该合同状态目前无法用印");
            }
        }
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function user()
    {
        return $this->hasOne('App\Models\User', 'id', 'applicant');
    }

    /**
     * 获取主部门id
     *
     * @param $userId
     *
     * @return string
     */
    public static function getPrimaryDeptName($userId)
    {
        $primaryUser = DepartUser::with('department')
            ->where('user_id', '=', $userId)
            ->where('is_primary', '=', DepartUser::DEPARTMENT_PRIMARY_YES)->first();

        return $primaryUser ? $primaryUser->department->name : "";
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function entry()
    {
        return $this->hasOne('App\Models\Workflow\Entry', 'id', 'entry_id');
    }

    public static function getByNumber($number)
    {
        $contract = self::where('number', '=', $number)->first();
        return $contract;
    }

    public static function fetchContractInUse($number)
    {
        return self::where('number', '=', $number)
            ->whereNotIn('status', [self::CONTRACT_STATUS_REJECTED, self::CONTRACT_STATUS_CANCEL])->first();
    }

    public static function updateContractStatusByEntryId($entryId, $status)
    {
        $contract = Contract::where('entry_id', $entryId)->first();

        if (!$contract) {
            throw new DevFixException(sprintf("合同申请ID为%s的合同不存在", $entryId));
        }

        return $contract->update(['status' => $status]);
    }
}
