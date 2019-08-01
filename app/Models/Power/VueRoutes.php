<?php

namespace App\Models\Power;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Power\VueRoutes
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Power\VueRoutes newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Power\VueRoutes newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Power\VueRoutes query()
 * @mixin \Eloquent
 */
class VueRoutes extends Model
{
    //
    protected $table = 'api_vue_routes';

    public $timestamps = true;

    /**
     * 可以被批量赋值的属性.
     *
     * @var array
     */
    protected $fillable = [
        'route_id',
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
