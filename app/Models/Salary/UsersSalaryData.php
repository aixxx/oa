<?php

namespace App\Models\Salary;

use App\Constant\ConstFile;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UsersSalaryData extends Model
{
    use SoftDeletes;
    
    protected $table = 'users_salary_data';
    
    public $timestamps  = true;
    
    /**
     * 可以被批量赋值的属性.
     *
     * @var array
     */
    protected $fillable = [
        'template_id',
        'relation_id',
        'field_name',
        'field_data',
        'user_id',
        'field_id',
        'company_id',
        'create_salary_user_id',
        'create_salary_user_name',
        'contract_id',
        'type',
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
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function hasOneUsersSalaryRelation()
    {
        return $this->hasOne(UsersSalaryRelation::class,'id','relation_id')->where(['status'=>Salary::SALARY_RELATION_STATUS_BONUS]);
    }

}
