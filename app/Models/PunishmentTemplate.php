<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DevFixException;

class PunishmentTemplate extends Model
{
    protected $table = "punishment_template";

    public $fillable = ['company_id', 'title', 'user_id', 'penalty_multiple', 'status'];

}