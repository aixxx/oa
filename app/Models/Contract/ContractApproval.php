<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/3/19
 * Time: 10:47
 */

namespace App\Models\Contract;

use App\Models\User;
use App\Models\UsersSalaryData;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContractApproval extends Model {

    use SoftDeletes;

    protected $table = 'contract_approval';

    public $timestamps = true;

    /**
     * 可以被批量赋值的属性.
     *
     * @var array
     */
    protected $fillable = [
        'contract_id',
        'user_id',
        'level',
        'create_user_id',
    ];

    /**
     * 不能被批量赋值的属性
     *
     * @var array
     */
    protected $guarded = [
        "id"
    ];
    protected $hidden = [
        'deleted_at'
    ];

    /**
     *
     * @return User
     */
    public function user(){
        return $this->belongsTo(User::class);
    }

    /**
     * @description 薪资合同数据关联表
     * @author liushaobo
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function hasManyUserSalaryDataContract(){
        return $this->hasMany(UsersSalaryData::class,'contract_id')->where('type',2);
    }
}
