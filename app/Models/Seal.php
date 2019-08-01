<?php
/**
 * Created by PhpStorm.
 * User: lizhennan
 * Date: 2018/8/20
 * Time: 下午3:05
 */

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use DevFixException;
/**
 * App\Models\Seal
 *
 * @property int $id
 * @property string $seal_main_body 印章主体
 * @property string $seal_type
 * @property string $seal_hold_user_id 当前责任人
 * @property \Carbon\Carbon $created_at 创建时间
 * @property \Carbon\Carbon $updated_at 更新时间
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Seal whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Seal whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Seal whereSealHoldUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Seal whereSealMainBody($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Seal whereSealType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Seal whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property string|null $company_id 印章主体(公司id)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Seal whereCompanyId($value)
 */
class Seal extends Model
{
    //印章类型
    const SEAL_TYPE_CONTRACT = '合同章';
    const SEAL_TYPE_OFFICIAL = '公章';
    const SEAL_TYPE_INVOICE = '发票专用章';
    const SEAL_TYPE_FINANCE = '财务专用章';
    const SEAL_TYPE_CORPORATE = '法人章';
    const SEAL_TYPE_BUSINESS = '营业执照';
    const SEAL_TYPE_CREDIT_CERTIFICATE = '信用代码证';
    const SEAL_TYPE_OPENING_PERMIT = '开户许可证';
    //印章是否外带
    const SEAL_TAKE_OUT_YES = '是';
    const SEAL_TAKE_OUT_NO = '否';
    //ability能力
    const SEAL_MANAGE_ABILITY_CREATE = 'sealManage_create';//excel数据导入能力
    const SEAL_MANAGE_ABILITY_EXPORT = 'sealManage_export';//模版下载能力

    public $fillable = [
        'company_id',//印章主体（所属公司）
        'seal_type',
        'seal_hold_user_id',//当前责任人
    ];
    public static $sealType = [
        '0' => '合同章',
        '1' => '公章',
        '2' => '发票专用章',
        '3' => '财务专用章',
        '4' => '法人章',
        '5' => '营业执照',
        '6' => '信用代码证',
        '7' => '开户许可证',
        '8' => '人事章'
    ];
    
}