<?php

namespace App\Models\Power;

use Illuminate\Database\Eloquent\Model;

class Routes extends Model
{

    protected $table = 'api_routes';

    public $timestamps = true;

    /**
     * 可以被批量赋值的属性.
     *
     * @var array
     */
    protected $fillable = [
        'path',
        'title',
        'parent_id',
        'action_id',
    ];
    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];

    protected $appends = [];

}
