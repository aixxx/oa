<?php

namespace App\Models\Salary;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UsersSalary extends Model
{
    use SoftDeletes;
    
    protected $table = 'users_salary';
    
    public $timestamps  = true;
    
    /**
     * 可以被批量赋值的属性.
     *
     * @var array
     */
    protected $fillable = [
        'template_id',
        'relation_id',
        'field_id',
        'status',
        'company_id',
        'field_name',
        'field_data',
        'user_id',
        'contract_id',
        'create_salary_user_id',
        'create_salary_user_name',
        'version',
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
