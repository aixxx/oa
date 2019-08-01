<?php

namespace App\Models\Vacations;

use Illuminate\Database\Eloquent\Model;

class CompanyLeaveUnit extends Model
{
    //
    protected $table = 'company_leave_unit';

    protected $fillable = ['c_id', 'l_id', 'type', 'n_id'];

}
