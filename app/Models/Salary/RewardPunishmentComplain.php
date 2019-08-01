<?php

namespace App\Models\Salary;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RewardPunishmentComplain extends Model
{
    use SoftDeletes;

    protected $table = 'salary_reward_punishment_complain';

    public $timestamps  = true;

    /**
     * 可以被批量赋值的属性.
     *
     * @var array
     */
    protected $fillable = [
        'pr_id','remark','remark_img',
        'created_at','updated_at','deleted_at',
    ];
    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [

    ];

    protected $appends = [];
}
