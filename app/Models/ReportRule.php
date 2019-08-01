<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReportRule extends Model
{
    /*protected $table = 'report_rules';//指定表名
    protected $primaryKey = 'id';//指定主键
    protected $incrementing = false;//不是自增主键时设置
    protected $keyType = 'string';//主键不是整型时设置
    protected $timestamps  = false;//表中没有created_at 和 updated_at时设置*/

    use SoftDeletes;

    protected $guarded = [];

    /*
     * 获取与规则关联的部门
     * */
    public function department(){
        return $this->hasMany('App\Models\ReportRuleDepartment', 'rule_id');
    }


    /*
     * 获取与规则关联的员工
     * */
    public function user(){
        return $this->hasMany('App\Models\ReportRuleUser', 'rule_id')->where('deleted_at', 0);
    }


    /*
     * 关联规则与模板
     * */
    public function ruleTemplate(){
        return $this->hasOne('App\Models\ReportTemplate', 'id', 'report_type');
    }

}
