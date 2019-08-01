<?php

namespace App\Models\Performance;
   
use Illuminate\Database\Eloquent\Model;

class PerformanceTemplateContent extends Model {
    const API_TYPE_REWARD =1;//奖励
    const API_TYPE_PUNISHMENT =2;//惩罚

    protected $table = 'performance_template_content';
    //type  1表示  奖励   2表示惩罚
    //start   最小值
    //end    最大值

    protected $fillable = ['id',"type","pt_id","title","start","end","value"];
    public function PerformanceBasics()
    {
        return $this->hasOne('App\Models\Performance\PerformanceBasics', 'id', 'title')
            ->where('status',1);
    }
}