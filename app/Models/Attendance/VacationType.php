<?php

namespace App\Models\Attendance;

use Illuminate\Database\Eloquent\Model;

class VacationType extends Model
{
    public static $_name_ids = [
            '年假' => 1,
            '调休' => 2,
            '病假' => 3,
            '婚假' => 4,
            '陪产假' => 5,
            '例假' => 6,
            '事假' => 7,
            '特假' => 8
        ];
    //
    protected $table = 'vacation_type';

    protected $fillable = ['vacname', 'created_at', 'updated_at'];
}
