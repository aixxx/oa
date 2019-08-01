<?php

namespace App\Models\Vote;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class VoteDepartment extends Model
{
    use SoftDeletes;
    
    protected $table = 'vote_department';
    
    public $timestamps  = true;
    
    /**
     * 可以被批量赋值的属性.
     *
     * @var array
     */
    protected $fillable = [
        'company_id',
        'department_id',
        'v_id'
    ];
    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];
    
    protected $appends = [];

}
