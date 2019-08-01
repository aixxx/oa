<?php

namespace App\Models\Performance;
   
use Illuminate\Database\Eloquent\Model;

class PerformanceApplicationSon extends Model {

    const API_STATUS_DRAFT =0;//草稿
    const API_STATUS_SUCCESS =1;//打分完成
    const API_STATUS_REJECT =2;//驳回
    protected $table = 'performance_application_son';


    protected $fillable = ['id',"pa_id","auditor_id","pts_id","ptq_id","completion_value","completion_rate",'score','status','total_score'];

}