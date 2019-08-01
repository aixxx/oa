<?php

namespace App\Models\PAS\Warehouse;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\PAS\Warehouse\Logistics
 *
 * @property int $id
 * @property int|null $status 状态
 * @property string|null $title 物流名
 * @property string|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\Logistics newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\Logistics newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\Logistics query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\Logistics whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\Logistics whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\Logistics whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\Logistics whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\Logistics whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\Logistics whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Logistics extends Model
{
    const STATUS_ON = 1;
    const STATUS_OFF = 0;
    //
    protected $table = 'pas_logistics';
    protected $fillable = [
        'title',
        'status',
        'updated_at',
        'created_at',
        'deleted_at',
    ];

    public function point(){
        return $this->hasMany(LogisticsPoint::class, 'logistics_id', 'id');
    }
}
