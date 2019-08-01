<?php

namespace App\Models\Seals;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SealsType extends Model
{
    use SoftDeletes;

    protected $table = 'company_seals_type';

    public $timestamps  = true;

    /**
     * 可以被批量赋值的属性.
     *
     * @var array
     */
    protected $fillable = [
        'company_id',
        'seal_type_name',
        'create_user_id',
        'created_at',
        'updated_at',
        'deleted_at'
    ];
    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [

    ];

    protected $appends = [];

    public static function getlist($user){
        $where['company_id']=$user->company_id;
        return self::where($where)->get(['id','seal_type_name']);
    }
}
