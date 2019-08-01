<?php

namespace App\Models\Comments;

// use App\Models\UsersSalaryRelation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Comments extends Model
{
//    use SoftDeletes;
    
    protected $table = 'comments';
    
    public $timestamps  = true;
    
    /**
     * 可以被批量赋值的属性.
     *
     * @var array
     */
    protected $fillable = [
        
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
