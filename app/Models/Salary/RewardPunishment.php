<?php

namespace App\Models\Salary;

use App\Http\Helpers\Dh;
use App\Http\Requests\Salary\RewardPunishmentComplainRequest;
use App\Models\Department;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RewardPunishment extends Model
{
    use SoftDeletes;

    const TYPE_REWARD = 1;
    const TYPE_PUNISHMENT = 2;

    protected $table = 'salary_reward_punishment';

    public $timestamps  = true;

    /**
     * 可以被批量赋值的属性.
     *
     * @var array
     */
    protected $fillable = [
        'admin_id','type','user_id','department_id',
        'money','dates','title',
        'entrise_id','status','created_at',
        'updated_at','deleted_at','task_id',
    ];
    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [

    ];

    protected $appends = [];


    public function user(){
        return $this->hasOne(User::class, 'id', 'user_id')
            ->select(['id','chinese_name','avatar']);
    }

    public function department(){
        return $this->hasOne(Department::class,'id','department_id')
            ->select(['id','name']);
    }

    public function complain(){
        return $this->hasMany(RewardPunishmentComplain::class, 'pr_id', 'id');
    }


    /*
     * 根据user_id 和 月份 获取整个月的奖惩数据
     * int $user_id 用户ID
     * data $datas 月份 //2019-05
     * */
    public static function getCountResult($user_id, $dates = '2019-05'){
        $t = Dh::getBeginEndByMonth($dates);
        $result['reward'] = RewardPunishment::query()
            ->where('user_id',  $user_id)
            ->whereBetween('dates', $t)
            ->where('type', self::TYPE_REWARD)
            ->sum('money');

        $result['punishment'] = RewardPunishment::query()
            ->where('user_id',  $user_id)
            ->whereBetween('dates', $t)
            ->where('type', self::TYPE_PUNISHMENT)
            ->sum('money');
        //dd($result);
        return $result;
    }
}
