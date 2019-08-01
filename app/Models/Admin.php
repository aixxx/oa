<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticate;

class Admin extends Authenticate
{
    protected $table = "admins";

    protected $fillable = [
        'name',
        'email',
        'wechat_name',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public static function fetchAdmin($name)
    {
        return self::where('name', $name)->first();
    }
}
