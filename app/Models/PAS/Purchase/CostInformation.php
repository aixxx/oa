<?php

namespace App\Models\PAS\Purchase;
   
use Illuminate\Database\Eloquent\Model;

class CostInformation extends Model {

    const TYPE_PURCHASE =1;
    const TYPE_RETURN_ORDER =2;
    protected $table = 'pas_cost_information';


    protected $fillable = ['id',"type","code_id","title","money","nature","payment",'status','created_at'];

}