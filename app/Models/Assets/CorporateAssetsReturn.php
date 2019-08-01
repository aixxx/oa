<?php

namespace App\Models\Assets;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CorporateAssetsReturn extends Model
{
    use SoftDeletes;

    protected $table = 'corporate_assets_return';

    public $timestamps  = true;

    /**
     * 可以被批量赋值的属性.
     *
     * @var array
     */
    protected $fillable = [
        'num',
        'return_at',
        'apply_user_id',
        'user_id',
        'remarks',
        'entry_id',
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
