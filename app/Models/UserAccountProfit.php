<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\ProfitProject;

class UserAccountProfit extends Model
{
    protected $fillable = ['user_id', 'account_type_id', 'balance'];
	
    public function getProjectTitleAttribute()
    {
		$pp = ProfitProject::where('account_profits_id', '=',  $this->id)->first();
              $project = new $pp['model_name']; 
 		 return  $project->where('id', '=', $pp->project_id)->first()->title;
    }
        public function getBalanceAttribute($value)
    {
        return sprintf("%.2f",$value/100);
    }
}
