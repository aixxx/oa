<?php

namespace App\Models\Financial;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BussinessCategoryPlan extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'plan_id',
        'category_id',
        'company_id',
        'content',
        'money',
        'unit_type',
        'unit',
        'program_id',
        'user_id',
        'department_id'
    ];



    
}
