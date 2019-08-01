<?php

namespace App\Models\Power;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class RolesUsers extends Model
{
    use SoftDeletes;

    protected $table = 'api_roles_users';

    public $timestamps = false;

    /**
     * 可以被批量赋值的属性.
     *
     * @var array
     */
    protected $fillable = [
    ];
    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'deleted_at'
    ];

    protected $appends = [];

    public function hasManyRoutesRoles()
    {
        return $this->hasMany(RoutesRoles::class, 'role_id', 'role_id')
            ->leftJoin('api_vue_action', 'api_routes_roles.action_id', '=', 'api_vue_action.id');
    }

    public function hasManyRoutes()
    {
        return $this->hasMany(RoutesRoles::class, 'role_id', 'role_id')
            ->leftJoin('api_routes', 'api_routes_roles.action_id', '=', 'api_routes.id');
    }

    public function belongsToManyVueAction()
    {
        return $this->belongsToMany(VueAction::class, 'api_routes_roles', 'role_id', 'action_id','role_id')->whereNull('api_routes_roles.deleted_at')->whereNull('api_vue_action.deleted_at')->select(['api_vue_action.id', 'api_vue_action.vue_path', 'api_vue_action.title'])->groupBy('api_vue_action.vue_path');
    }



}
