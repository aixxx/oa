<?php

namespace App\Models\Performance;
   
use Illuminate\Database\Eloquent\Model;

class PerformanceBasics extends Model {

    protected $table = 'performance_basics';


    protected $fillable = ["title"];

}