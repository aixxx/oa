<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
//use Illuminate\Database\Eloquent\SoftDeletes;

class ReportFieldInfo extends Model
{
    /*protected $table = 'report_rules';//指定表名
    protected $incrementing = false;//不是自增主键时设置
    protected $keyType = 'string';//主键不是整型时设置
    protected $timestamps  = false;//表中没有created_at 和 updated_at时设置*/
    protected $primaryKey = 'auto_id';//指定主键
    protected $guarded = [];
    //use SoftDeletes;


    /*
     * 关联规则与模板
     * */
    public function ruleTemplate(){
        return $this->hasOne('App\Models\ReportTemplate', 'id', 'report_type');
    }

}
