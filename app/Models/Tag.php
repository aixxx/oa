<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Tag
 *
 * @property int $id
 * @property string $name
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \App\Models\Company $company
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Department[] $departments
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\User[] $users
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Tag whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Tag whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Tag whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Tag whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Tag extends Model
{
    const TAG_NAME_COMPANY = '公司:'; //公司标签

    protected $fillable = ['id','name'];

    public function users()
    {
        return $this->belongsToMany('App\Models\User');
    }

    public function departments()
    {
       // return $this->belongsToMany('App\Models\Department');
        return $this->belongsToMany('App\Models\Tag', 'department_tag', 'tag_id', 'department_id', 'id', 'id');
    }

    public function company()
    {
        return $this->hasOne('App\Models\Company','name','name');
    }
}