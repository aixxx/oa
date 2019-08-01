<?php

namespace App\Models\Assets;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CorporateAssetsSync extends Model
{
    use SoftDeletes;

    protected $table = 'corporate_assets_sync';

    public $timestamps  = true;

    /**
     * 可以被批量赋值的属性.
     *
     * @var array
     */
    protected $fillable = [
        'apply_user_id',
        'assets_id',
        'type',
        'status',
        'content_json',
        'confirm_at',
        'entry_id',
        'user_id',
    ];
    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [

    ];

    protected $appends = [];

}
