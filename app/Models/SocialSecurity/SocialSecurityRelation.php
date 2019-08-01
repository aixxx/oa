<?php

namespace App\Models\SocialSecurity;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class SocialSecurityRelation extends Model
{
    use SoftDeletes;
    
    protected $table = 'social_security_relation';
    
    public $timestamps  = true;
    
    /**
     * 可以被批量赋值的属性.
     *
     * @var array
     */
    protected $fillable = [
        'ss_id',
        'create_user_id',
        'create_user_name',
        'user_id',
        'user_name',
        'company_id',
    ];
    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];
    
    protected $appends = [];

}
