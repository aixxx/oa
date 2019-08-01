<?php

namespace App\Models\PAS\Warehouse;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\PAS\Warehouse\LogisticsPoint
 *
 * @property int $id
 * @property int|null $status 状态
 * @property string|null $point 网点
 * @property string|null $tel 电话
 * @property string|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $logistics_id 物流ID
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\LogisticsPoint newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\LogisticsPoint newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\LogisticsPoint query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\LogisticsPoint whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\LogisticsPoint whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\LogisticsPoint whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\LogisticsPoint whereLogisticsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\LogisticsPoint wherePoint($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\LogisticsPoint whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\LogisticsPoint whereTel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\LogisticsPoint whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class LogisticsPoint extends Model
{
    const STATUS_ON = 1;
    const STATUS_OFF = 0;
    //
    protected $table = 'pas_logistics_point';

    protected $fillable = [
        'status',
        'point',
        'tel',
        'logistics_id',
        'updated_at',
        'created_at',
        'deleted_at',
    ];

    public function logistics(){
        return $this->hasOne(Logistics::class, 'id', 'logistics_id');
    }
}
