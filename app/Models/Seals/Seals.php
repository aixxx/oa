<?php

namespace App\Models\Seals;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Seals extends Model
{
    use SoftDeletes;

    protected $table = 'company_seals';

    public $timestamps  = true;

    /**
     * 可以被批量赋值的属性.
     *
     * @var array
     */
    protected $fillable = [
        'seals_type_id',
        'seal_img',
        'upload_user_id',
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

    public static function  getSeals($id){
        $where['seals_type_id'] = $id;
        return self::where($where)->get(['id','seal_img']);
    }
}
