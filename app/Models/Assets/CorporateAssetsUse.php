<?php

namespace App\Models\Assets;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CorporateAssetsUse extends Model
{
    use SoftDeletes;

    protected $table = 'corporate_assets_use';

    public $timestamps = true;

    /**
     * 可以被批量赋值的属性.
     *
     * @var array
     */
    protected $fillable = [
        'num',
        'apply_user_id',
        'user_id',
        'remarks',
        'entry_id',
        'use_at',
    ];
    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [

    ];

    protected $appends = [];


    public function hasOneUser()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}
