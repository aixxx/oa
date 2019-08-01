<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/5/9
 * Time: 13:16
 */

namespace App\Models\Intelligence;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class IntelligenceType extends Model
{
    protected $table = "intelligence_type";
    use SoftDeletes;

//    public $timestamps  = false;

    protected $fillable = [
        'class_name',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];
}