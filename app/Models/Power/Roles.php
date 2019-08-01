<?php

namespace App\Models\Power;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Roles extends Model
{
    use SoftDeletes;

    protected $table = 'api_roles';

    public $timestamps = true;

    /**
     * 可以被批量赋值的属性.
     *
     * @var array
     */
    protected $fillable = [
        'title'
    ];
    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];

    protected $appends = [];

    public function belongsToManyVueAction()
    {
        return $this->belongsToMany(VueAction::class, 'api_routes_roles', 'role_id', 'action_id')->select(['api_vue_action.id', 'api_vue_action.vue_path', 'api_vue_action.title']);
    }


}
