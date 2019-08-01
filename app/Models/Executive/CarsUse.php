<?php

namespace App\Models\Executive;

use App\Models\Department;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CarsUse extends Model
{
    use SoftDeletes;
    protected $table = 'executive_cars_use';

    public $timestamps  = true;

    /**
     * 可以被批量赋值的属性.
     *
     * @var array
     */
    protected $fillable = [
        'title','driver_id',
        'department_id','people_number','begin_time',
        'end_time','mileage','cause','user_id','status','entrise_id',
        'remark','created_at','updated_at','deleted_at',
    ];
    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [

    ];

    protected $appends = [];

    public function belongsToManyCarsUseRelationCar(){
        return $this->belongsToMany(Cars::class,'executive_cars_use_relation_car'
            ,'cars_use_id', 'cars_id')
            ->select();
    }

    public function user(){
        return $this->hasOne(User::class, 'id', 'driver_id')
            ->select(['id','chinese_name']);
    }

    public function department(){
        return $this->hasOne(Department::class,'id','department_id')
            ->select(['id','name']);
    }

    public function sendback(){
        return $this->hasOne(CarsSendback::class, 'cars_use_id', 'id');
    }
}
