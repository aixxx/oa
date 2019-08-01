<?php

namespace App\Models\Basic;
   
use Illuminate\Database\Eloquent\Model;

class BasicOaOption extends Model {

    protected $table = 'basic_oa_option';


    protected $fillable = ["title","status","describe","type_id","level"];

}