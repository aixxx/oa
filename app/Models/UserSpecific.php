<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\UserSpecific
 *
 * @property int $id 主键id
 * @property int $user_id users表主键id
 * @property string $contract_department 合同部门
 * @property string $belong_to_business 归属业务
 * @property string $contract_company 合同公司
 * @property string $contract_nature 合同性质
 * @property string $employee_type 员工类别
 * @property string $specific_support_fee_contract_company 专项服务费-合同公司
 * @property string $specific_support_fee_wechat_department_first_level 专项服务费-微信部门一级
 * @property string $specific_support_fee_wechat_department_second_level 专项服务费-微信部门二级
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserSpecific whereBelongToBusiness($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserSpecific whereContractCompany($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserSpecific whereContractDepartment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserSpecific whereContractNature($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserSpecific whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserSpecific whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserSpecific whereEmployeeType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserSpecific whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserSpecific whereSpecificSupportFeeContractCompany($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserSpecific whereSpecificSupportFeeTransactionAccount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserSpecific whereSpecificSupportFeeTransactionIdNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserSpecific whereSpecificSupportFeeWechatDepartmentFirstLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserSpecific whereSpecificSupportFeeWechatDepartmentSecondLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserSpecific whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserSpecific whereUserId($value)
 * @mixin \Eloquent
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserSpecific whereSpecificSupportFeeBankCard($value)
 * @property string $specific_support_fee_transaction_account 专项费用-打款用户名
 * @property string $specific_support_fee_transaction_id_number 专项费用-打款身份证号
 * @property string $specific_support_fee_bank_card 专项费用-打款银行卡号
 * @property string $new_business_wechat_first_level 新企业微信部门一级
 * @property string $new_business_wechat_second_level 新企业微信部门二级
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserSpecific whereNewBusinessWechatFirstLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UserSpecific whereNewBusinessWechatSecondLevel($value)
 */
class UserSpecific extends Model
{
    protected $table    = "user_specific";
    public    $fillable =
        [
            'user_id',
            'contract_department',
            'belong_to_business',
            'contract_company',
            'contract_nature',
            'position',
            'employee_type',
            'specific_support_fee_contract_company',
            'specific_support_fee_wechat_department_first_level',
            'specific_support_fee_wechat_department_second_level',
            'specific_support_fee_card_number',
        ];
}
