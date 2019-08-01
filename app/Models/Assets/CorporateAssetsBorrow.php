<?php

namespace App\Models\Assets;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CorporateAssetsBorrow extends Model
{
    use SoftDeletes;

    protected $table = 'corporate_assets_borrow';

    public $timestamps = true;

    /**
     * 可以被批量赋值的属性.
     *
     * @var array
     */
    protected $fillable = [
        'num',
        'borrowing_at',
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

    public static function getLastBorrowInfo()
    {
        return self::query()->orderBy('id', SORT_DESC)->limit(1)->first();
    }

    public function hasOneUser()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}
