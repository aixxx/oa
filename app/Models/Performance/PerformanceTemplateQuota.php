<?php

namespace App\Models\Performance;
   
use Illuminate\Database\Eloquent\Model;

class PerformanceTemplateQuota extends Model {

    protected $table = 'performance_template_quota';


    protected $fillable = ["id","title","standard","weight","value",'pts_id'];


}