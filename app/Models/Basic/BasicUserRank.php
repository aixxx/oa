<?php

namespace App\Models\Basic;

use Illuminate\Database\Eloquent\Model;

class BasicUserRank extends Model
{
    protected $table = 'basic_user_rank';
    /**
     * 可以被批量赋值的属性.
     *
     * @var array
     */
    protected $fillable = [
        'title','info','level'
    ];
}
