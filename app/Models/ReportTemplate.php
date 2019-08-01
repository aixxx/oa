<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReportTemplate extends Model
{
    use SoftDeletes;
    protected $guarded = [];


    /*
     * 模板关联模板字段
     * */
    public function formField(){
        return $this->hasMany('App\Models\ReportTemplateForm', 'template_id');
    }


    /*
     * 模板关联汇报
     * */
    public function report(){
        return $this->hasMany('App\Models\Report', 'template_id', 'id');
    }
}
