<?php

namespace App\Models\Power;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class VueAction extends Model
{

    protected $table = 'api_vue_action';

    public $timestamps = true;

    /**
     * 可以被批量赋值的属性.
     *
     * @var array
     */
    protected $fillable = [
        'vue_path',
        'title',
        'parent_id',
    ];
    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];

    protected $appends = [];


    public function hasManyChildren()
    {
        return $this->hasMany(self::class, 'parent_id', 'id');
    }

    public function belongsToManyRotes()
    {
        return $this->belongsToMany(Routes::class, 'api_vue_routes', 'action_id', 'route_id')->select(['api_routes.id','api_routes.path','api_routes.title']);
    }
}
