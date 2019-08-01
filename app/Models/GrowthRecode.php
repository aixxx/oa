<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/5/14
 * Time: 15:50
 */

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GrowthRecode extends Model
{
    protected $table = "growth_recode";
    use SoftDeletes;


    protected $fillable = [
        'user_id',
        'content',
        'type',
    ];

    protected $hidden = [
        'updated_at',
        'deleted_at',
    ];

    //添加成长记录
    public static function UserGrowthRecode($params){
        if(is_array($params)){
            GrowthRecode::create($params);
        }
    }
}