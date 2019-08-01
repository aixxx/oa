<?php

namespace App\Models\SocialSecurity;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class SocialSecurity extends Model
{
    use SoftDeletes;
    
    protected $table = 'social_security';
    
    public $timestamps  = true;
    
    /**
     * 可以被批量赋值的属性.
     *
     * @var array
     */
    protected $fillable = [
        'company_id',
        'name',
        'english_name',
        'company_proportion',
        'personal_proportion',
        'create_user_id',
        'create_user_name',
        'payment_standard'
    ];
    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];
    
    protected $appends = [];

}
