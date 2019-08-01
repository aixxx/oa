<?php

namespace App\Models\PAS\Warehouse;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\PAS\Warehouse\WarehouseDeliveryType
 *
 * @property int $id
 * @property string|null $delivery_type 发货方式
 * @property int|null $logistics_id 物流ID
 * @property int|null $point 网点
 * @property string|null $point_tel 网点电话
 * @property string|null $delivery_no 运单号
 * @property int|null $receiver 收件人
 * @property int|null $customer_info_id 客户信息ID
 * @property int|null $status 状态
 * @property string|null $address 收件地址
 * @property string|null $contact_tel 联系电话
 * @property string|null $freight_desc 运费说明
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\WarehouseDeliveryType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\WarehouseDeliveryType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\WarehouseDeliveryType query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\WarehouseDeliveryType whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\WarehouseDeliveryType whereContactTel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\WarehouseDeliveryType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\WarehouseDeliveryType whereCustomerInfoId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\WarehouseDeliveryType whereDeliveryNo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\WarehouseDeliveryType whereDeliveryType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\WarehouseDeliveryType whereFreightDesc($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\WarehouseDeliveryType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\WarehouseDeliveryType whereLogisticsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\WarehouseDeliveryType wherePoint($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\WarehouseDeliveryType wherePointTel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\WarehouseDeliveryType whereReceiver($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\WarehouseDeliveryType whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PAS\Warehouse\WarehouseDeliveryType whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class WarehouseDeliveryType extends Model
{
    //
    protected $table = 'pas_warehouse_delivery_type';
    protected $fillable = [
        'delivery_type',
        'logistics_id',
        'point',
        'point_tel',
        'delivery_no',
        'receiver',
        'customer_info_id',
        'status',
        'address',
        'contact_tel',
        'freight_desc',
        'updated_at',
        'created_at',
    ];
}
