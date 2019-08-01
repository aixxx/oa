<?php

namespace App\Models\Vote;

use App\Models\VoteOption;
use App\Models\VoteParticipant;
use App\Models\VoteRecord;
use App\Models\VoteRule;
use App\Models\VoteType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Vote extends Model
{
    use SoftDeletes;

    //投票确认状态
    const VOTE_STATUS_CONFIRM_NO        = -1;   //已拒绝
    const VOTE_STATUS_CONFIRM_YSE       = 1;    //已确认
    const VOTE_STATUS_CONFIRM_DEFAULT   = 0;    //待确认
    public static $voteStatusList = [
        self::VOTE_STATUS_CONFIRM_NO      => '已拒绝',
        self::VOTE_STATUS_CONFIRM_YSE     => '已确认',
        self::VOTE_STATUS_CONFIRM_DEFAULT => '待确认',
    ];
    //投票状态
    const VOTE_STATE_NORMAL     = 1;    //正常
    const VOTE_STATE_CANCEL     = 2;    //已取消
    const VOTE_STATE_ADOPT      = 3;    //已通过
    const VOTE_STATE_INVALID    = 4;    //无效
    public static $voteStateList = [
        self::VOTE_STATE_NORMAL  => '正常',
        self::VOTE_STATE_CANCEL  => '已取消',
        self::VOTE_STATE_ADOPT   => '已通过',
        self::VOTE_STATE_INVALID => '无效',
    ];
    const VOTE_OPTION_STATE_ADOPT = 2;  //投票选项已通过

    protected $table = 'vote';
    
    public $timestamps  = false;

    /**
     * 可以被批量赋值的属性.
     *
     * @var array
     */
    protected $fillable = [
        'vote_title',
        'vote_type_id',
        'vote_type_name',
        'describe',
        'enclosure_url',
        'end_at',
        'create_vote_user_id',
        'created_at',
        'updated_at',
        'deleted_at',
        'prompt_type',
        'rule_id',
        'company_id',
        'passing_rate',
        'user_name',
        'state',
        'selection_type',
    ];
    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];
    
    protected $appends = [];

    /**
     * @description 获取投票的类型
     * @author liushaobo
     * @time 2019\3\21
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public  function voteType(){
        return $this->hasMany(VoteType::class);
    }

    /**
     * @description 获取投票的记录
     * @author liushaobo
     * @time 2019\3\21
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function voteRecord(){
        return $this->hasMany(VoteType::class);
    }

    /**
     * @description 获取投票人记录
     * @author liushaobo
     * @time 2019\3\21
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function voteParticipant(){
        return $this->hasMany(VoteParticipant::class);
    }

    public function voteOption(){
        return $this->hasMany(VoteOption::class,'v_id');
    }
    /**
     * @description 获取投票的规则
     * @author liushaobo
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public  function getVoteRule(){
        return $this->hasOne(VoteRule::class,'id','rule_id');
    }

    /**
     * @description 获取投票的记录
     * @author liushaobo
     * @time 2019\3\21
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function voteOptionRecord(){
        return $this->hasMany(VoteRecord::class,'vo_id');
    }

    public function hasManyVoteParticipant(){
        return $this->hasMany(VoteParticipant::class,'v_id','id');
    }
}
