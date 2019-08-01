<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VoteRecord extends Model
{
    use SoftDeletes;
    
    protected $table = 'vote_record';
    
    public $timestamps  = true;
    
    /**
     * 可以被批量赋值的属性.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'vo_id',
        'v_id',
        'v_number',
        'user_name',
        'avatar',
        'created_at',
        'updated_at',
        'deleted_at',
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
