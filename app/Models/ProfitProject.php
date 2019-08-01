<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProfitProject extends Model
{
    protected $fillable = ['project_id', 'account_profits_id', 'model_name'];
}
