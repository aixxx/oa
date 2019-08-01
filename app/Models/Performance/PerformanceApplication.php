<?php

namespace App\Models\Performance;
   
use Illuminate\Database\Eloquent\Model;

class PerformanceApplication extends Model {
    //0未评价 1执行中（审核）  2待确定  3申诉待处理 4申诉处理中 5申诉处理完成  6完成，7 被驳回
    const API_STATUS_UNEVALUATED =0;
    const API_STATUS_IMPLEMENT =1;
    const API_STATUS_PENDING =2;
    const API_STATUS_APPEAL =3;
    const API_STATUS_APPEAL_PROCESSING =4;
    const API_STATUS_APPEAL_SUCCESS =5;
    const API_STATUS_SUCCESS =6;
    const API_STATUS_REJECT =7;
    protected $table = 'performance_application';


    protected $fillable = ['id',"title","pt_id","result","status","amonth","is_status",'view_password','money'];

}