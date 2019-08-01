<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\DepartmentMapCentre
 *
 * @property int $id
 * @property int $user_id 操作员工ID
 * @property string $department_full_path 部门全路径
 * @property string $centre_name 部门对应的中心名称
 * @property int $times 次数
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\DepartmentMapCentre whereCentreName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\DepartmentMapCentre whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\DepartmentMapCentre whereDepartmentFullPath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\DepartmentMapCentre whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\DepartmentMapCentre whereTimes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\DepartmentMapCentre whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\DepartmentMapCentre whereUserId($value)
 * @mixin \Eloquent
 * @property string $department_level1 一级部门
 * @property string $department_level2 二级部门
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\DepartmentMapCentre whereDepartmentLevel1($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\DepartmentMapCentre whereDepartmentLevel2($value)
 */
class DepartmentMapCentre extends Model
{
    public $fillable = ['user_id', 'department_level1', 'department_level2', 'department_full_path', 'centre_name', 'times'];

    public function user()
    {
        return $this->hasOne('App\Models\User', 'id', 'user_id');
    }
}
