<?php
/**
 * Created by PhpStorm.
 * User: qsq_lipf
 * Date: 18/7/11
 * Time: 下午8:21
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
/**
 * App\Models\UsersDimission
 *
 * @property int $id 内部系统uid
 * @property int $user_id 员工ID
 * @property int $is_voluntary 是否主动离职(1:是;2:否)
 * @property int|null $is_sign 直属领导是否已签字(1:已签;2:未签)
 * @property int|null $is_complete 流程手续是否已走完(1:是;2:否)
 * @property string|null $reason 原因
 * @property string|null $note 备注信息
 * @property string|null $interview_result 面谈结论
 * @property int $status 状态1.有效；2.删除
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersDimission whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersDimission whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersDimission whereInterviewResult($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersDimission whereIsComplete($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersDimission whereIsSign($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersDimission whereIsVoluntary($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersDimission whereNote($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersDimission whereReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersDimission whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersDimission whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\UsersDimission whereUserId($value)
 * @mixin \Eloquent
 */
class UsersDimission extends Model
{
    const STATUS_YES = 1; //是
    const STATUS_NO  = 2; //否

    protected $table = 'users_dimission';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['user_id', 'is_voluntary','is_sign','is_complete','reason', 'interview_result','note','status'];
}