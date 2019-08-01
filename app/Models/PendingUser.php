<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\PendingUser
 *
 * @property int $id 内部系统uid
 * @property string $given_name 中文-名
 * @property string $family_name 中文-姓
 * @property string $email 邮箱
 * @property string $mobile 手机号
 * @property string $position 职位
 * @property int $gender 性别(1.男;2.女;0.未知)
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property string|null $join_at 入职时间
 * @property int $status
 * @property string|null $deleted_at
 * @property int $company_id 所属公司id
 * @property int $department_id 所属部门id
 * @property string $english_name 英文名
 * @property int $is_leader 是否是高管
 * @property int $is_sync_wechat 是否要同步企业微信
 * @property string $name 企业微信账号名 例如allenwang
 * @property string|null $work_address 工作地点
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PendingUser whereCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PendingUser whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PendingUser whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PendingUser whereDepartmentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PendingUser whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PendingUser whereEnglishName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PendingUser whereFamilyName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PendingUser whereGender($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PendingUser whereGivenName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PendingUser whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PendingUser whereIsLeader($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PendingUser whereIsSyncWechat($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PendingUser whereJoinAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PendingUser whereMobile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PendingUser whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PendingUser wherePosition($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PendingUser whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PendingUser whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PendingUser whereWorkAddress($value)
 * @mixin \Eloquent
 */
class PendingUser extends Model
{
    const STATUS_DELETED        = -1;  //已删除
    const WORK_ADDRESS_SHANGHAI = 'shanghai';  //工作地址-上海
    const WORK_ADDRESS_CHENGDU  = 'chengdu';  //工作地址-成都
    const WORK_ADDRESS_BEIJING  = 'beijing';  //工作地址-北京
    const WORK_ADDRESS_SHENZHEN = 'shenzhen';  //工作地址-深圳

    protected $table = 'pending_users';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'family_name',
        'given_name',
        'english_name',
        'name',
        'gender',
        'email',
        'join_at',
        'mobile',
        'position',
        'gender',
        'company_id',
        'department_id',
        'is_leader',
        'is_sync_wechat',
        'status',
        'work_address',
    ];


    protected $dates = ['entry_at'];
}
