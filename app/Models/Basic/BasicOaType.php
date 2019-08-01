<?php

namespace App\Models\Basic;
   
use Illuminate\Database\Eloquent\Model;

class BasicOaType extends Model {
    protected $table = 'basic_oa_type';

    protected $fillable = ["title","code","status","describe"];
    public function getOption(){
        return $this->hasMany(BasicOaOption::class,'type_id','id');
    }

}