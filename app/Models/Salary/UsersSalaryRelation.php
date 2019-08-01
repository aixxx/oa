<?php

namespace App\Models\Salary;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UsersSalaryRelation extends Model
{
    use SoftDeletes;
    
    protected $table = 'users_salary_relation';
    
    public $timestamps  = true;
    
    /**
     * 可以被批量赋值的属性.
     *
     * @var array
     */
    protected $fillable = [
        'template_id',
        'field_id',
        'status',
    ];
    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [

    ];
    
    protected $appends = [];

    public function user_salary_relation(){
        return $this->hasMany();
    }

}
