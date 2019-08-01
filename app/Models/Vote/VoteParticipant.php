<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class VoteParticipant extends Model
{
    use SoftDeletes;

    protected $table = 'vote_participant';

    public $timestamps = true;

    const IS_CONFIRM_YES = 1;
    const IS_CONFIRM_NO = 0;

    /**
     * 可以被批量赋值的属性.
     *
     * @var array
     */
    protected $fillable = [
        'create_vote_user_id',
        'schedule_id',
        'describe',
        'user_id',
        'create_vote_user_name',
        'user_name',
        'confirm_yes',
        'v_id',
        'avatar',
    ];
    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [

    ];

    protected $appends = [];

    public function voteParticipantInfo()
    {
        return $this->hasOne(Vote::class);
    }

    public function hasManyVoteRecord()
    {
        return $this->hasMany(VoteRecord::class,'user_id','user_id');
    }
    //public function

    public function hasOneUser(){
        return $this->hasOne(User::class,'id','user_id');
    }

    public function fetchAvatar()
    {
        return $this->avatar??config('user.const.avatar');
    }
}
