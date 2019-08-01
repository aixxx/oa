<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserAccount extends Model
{
    protected $fillable = ['user_id', 'balance'];
    public function getBalanceAttribute($value)
    {
        return sprintf("%.2f",$value/100);
    }
}
