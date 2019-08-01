<?php

namespace App\Models\Performance;
   
use Illuminate\Database\Eloquent\Model;

class PerformanceTemplate extends Model {

    const API_STATUS_SUCCESS =1;
    const API_STATUS_DELETE =0;
    protected $table = 'performance_template';


    protected $fillable = ["id","user_id","title","status","created_at"];

    public function performanceTemplateContenList()
    {
        return $this->hasMany('App\Models\Performance\PerformanceTemplateContent', 'pt_id', 'id')
            ->where('status',1)
            ->where('type',1);
    }
    public function performancetct()
    {
        return $this->hasMany('App\Models\Performance\PerformanceTemplateContent', 'pt_id', 'id')
            ->where('status',1)
            ->where('type',2);

    }


}