<?php

namespace App\Models\Salary;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Salary extends Model
{
    use SoftDeletes;

    const SALARY_RELATION_STATUS_SALARY         = 1;//薪资
    const SALARY_RELATION_STATUS_SUBSIDY        = 2;//补贴
    const SALARY_RELATION_STATUS_BONUS          = 3;//奖金
    public static $salaryRelationStatus = [
        self::SALARY_RELATION_STATUS_SALARY     => '薪资',
        self::SALARY_RELATION_STATUS_SUBSIDY    => '补贴',
        self::SALARY_RELATION_STATUS_BONUS      => '奖金',
    ];
    const SALARY_DATA_TYPE_TEMPLATE             = 1;//薪资模板
    const SALARY_DATA_TYPE_CONTRACT             = 2;//合同设置

    protected $table = 'users_salary_template';
    
    public $timestamps  = true;
    
    /**
     * 可以被批量赋值的属性.
     *
     * @var array
     */
    protected $fillable = [
        'create_salary_user_name',
        'create_salary_user_id',
        'template_name',
        'company_id'
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
     * @description 薪资模板字典关联表
     * @author liushaobo
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function hasManySalaryRelation(){
        return $this->hasMany(UsersSalaryRelation::class,'template_id');
    }

    /**
     * @description 薪资模板数据关联表
     * @author liushaobo
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function hasManyUsersSalaryData(){
        return $this->hasMany(UsersSalaryRelation::class,'template_id');
    }


}
