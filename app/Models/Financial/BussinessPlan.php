<?php

namespace App\Models\Financial;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BussinessPlan extends Model
{
    use SoftDeletes;
    protected $guarded = [];


    /*
     * 计划关联多个类目计划
     * */
    public function hasManyChildPlan(){
        return $this->hasMany('App\Models\Financial\BussinessCategoryPlan', 'plan_id', 'id');
    }
}
