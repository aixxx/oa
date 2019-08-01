<?php

namespace App\Models\Entry;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EntryType extends Model
{
    use SoftDeletes;

    const ENTRY_RELATION_TYPE_SALARY = '1';
    const ENTRY_RELATION_TYPE_PERFORMANCE = '2';
    public $entryRelationTypeList = [
        self::ENTRY_RELATION_TYPE_SALARY => '工资条信息',
        self::ENTRY_RELATION_TYPE_PERFORMANCE => '绩效信息',
    ];

    protected $table = 'entry_type';

    public $timestamps = true;

    /**
     * 可以被批量赋值的属性.
     *
     * @var array
     */
    protected $fillable = [
        'entry_id',
        'type_key',
        'type_id_value',
    ];
    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [

    ];
}
