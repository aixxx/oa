<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Roles
 *
 * @property int $id
 * @property string $name
 * @property string|null $title
 * @property int|null $level
 * @property int|null $scope
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property AssignedRoles[] $assigned_roles
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Roles whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Roles whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Roles whereLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Roles whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Roles whereScope($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Roles whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Roles whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Roles extends Model
{
    protected $table = "roles";

    const NAME_WORKFLOW_MANAGER = 'workflow_manager';

    public function assigned_roles()
    {
        return $this->hasMany(\App\Models\AssignedRoles::class, 'role_id', 'id');
    }

    /**
     * @param $name
     * @return Model|null|object|static
     * @author hurs
     */
    public static function firstByName($name)
    {
        return self::where('name', $name)->first();
    }
}
