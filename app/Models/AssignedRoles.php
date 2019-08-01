<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\AssignedRoles
 *
 * @property int $role_id
 * @property int $entity_id
 * @property string $entity_type
 * @property int|null $scope
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AssignedRoles whereEntityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AssignedRoles whereEntityType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AssignedRoles whereRoleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AssignedRoles whereScope($value)
 * @mixin \Eloquent
 */
class AssignedRoles extends Model
{
    protected $table = "assigned_roles";

}
