<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VoteOption extends Model
{
    use SoftDeletes;

    protected $table = 'vote_option';

    public $timestamps = true;

    /**
     * 可以被批量赋值的属性.
     *
     * @var array
     */
    protected $fillable = [
        'v_id',
        'option_name'
    ];
    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
    ];

    protected $appends = [];

    /**
     * @discription 获取投票的记录
     * @author liushaobo
     * @time 2019\3\21
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function hasManyVoteRecord()
    {
        return $this->hasMany(VoteRecord::class, 'vo_id');
    }

    /**
     * @discription 获取投票的记录总数
     * @author liushaobo
     * @time 2019\3\21
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function hasManyVoteRecordCount()
    {
        return $this->hasMany(VoteRecord::class, 'vo_id')->count();
    }

    public function hasManyMyVoteRecord()
    {
        return $this->hasMany(VoteRecord::class, 'vo_id');
    }
}
