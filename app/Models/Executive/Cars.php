<?php

namespace App\Models\Executive;

use App\Models\Department;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cars extends Model
{
    use SoftDeletes;

    //0-空闲,1-使用中,2-维修中,3-事故中,4-报废,5-已预订
    const CAR_STATUS_NORMAL = 0;
    const CAR_STATUS_USEING = 1;
    const CAR_STATUS_REPAIR = 2;
    const CAR_STATUS_ACCIDENT = 3;
    const CAR_STATUS_SCRAP = 4;
    const CAR_STATUS_SUBSCRIBE = 5;

    protected $table = 'executive_cars';

    public $timestamps  = true;

    /**
     * 可以被批量赋值的属性.
     *
     * @var array
     */
    protected $fillable = [
        'title','car_number','color',
        'brand','type','displacement',
        'seat_size','load','entrise_id',
        'fuel_type','engine_number','buy_money',
        'buy_date','car_status','driver_id',
        'department_id','created_at','updated_at','deleted_at',
        'remark',
    ];
    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [

    ];

    protected $appends = [];

    /*
     * 修改车辆状态
     * */
    public static function updateCarStatus($id, $car_status){
        return self::query()
            ->whereIn('id', $id)
            ->update(['car_status'=> $car_status]);
    }

    public function user(){
        return $this->hasOne(User::class, 'id', 'driver_id')->select(['id','chinese_name']);
    }

    public function department(){
        return $this->hasOne(Department::class,'id','department_id')->select(['id','name']);
    }
}
