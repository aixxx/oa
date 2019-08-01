<?php

namespace App\Models\PAS\Warehouse;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\PAS\Warehouse\StockCheck
 *
 * @property int $id
 * @property string|null $check_no 盘点no
 * @property int|null $warehouse_id 仓库id
 * @property int|null $check_user_id 盘点人
 * @property int|null $number 盘点数量
 * @property string|null $remark 备注
 * @property string|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\StockCheck newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\StockCheck newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\StockCheck query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\StockCheck whereCheckNo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\StockCheck whereCheckUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\StockCheck whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\StockCheck whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\StockCheck whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\StockCheck whereNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\StockCheck whereRemark($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\StockCheck whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\StockCheck whereWarehouseId($value)
 * @mixin \Eloquent
 */
class StockCheck extends Model
{
    const STATUS_DRAFT= 0;
    const STATUS_OK= 1;
    const STATUS_CANCEL= 2;
    public static $_status = [
        self::STATUS_DRAFT => '草稿',
        self::STATUS_OK => '已盘点',
        self::STATUS_CANCEL => '已撤销',
    ];
    //
    protected $table = 'pas_stock_check';
    protected $fillable = [
        'check_no',
        'warehouse_id',
        'check_user_id',
        'number',
        'status',
        'remark',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function goods(){
        return $this->hasMany(StockCheckGoods::class , 'check_id', 'id')->select(['id','goods_id','sku_id','number']);
    }

    public function warehouse(){
        return $this->hasOne(Warehouse::class, 'id', 'warehouse_id')->select(['id','title']);
    }

    public function check_user(){
        return $this->hasOne(User::class, 'id','check_user_id')->select(['id','chinese_name']);
    }
}
