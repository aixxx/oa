<?php

namespace App\Models\Assets;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CorporateAssetsRelation extends Model
{
    use SoftDeletes;

    protected $table = 'corporate_assets_relation';

    public $timestamps  = true;

    /**
     * 可以被批量赋值的属性.
     *
     * @var array
     */
    protected $fillable = [
        'assets_id',
        'event_id',
        'type',
        'remarks',
        'apply_user_id',
        'user_id',
        'entry_id',
        'user_name',
        'type_name'
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
