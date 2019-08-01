<?php

namespace App\Models\Performance;
   
use Illuminate\Database\Eloquent\Model;

class PerformanceTemplateSon extends Model {

    protected $table = 'performance_template_son';


    protected $fillable = ["id","pt_id","title","numb","approval_id"];

}