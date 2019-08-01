<?php

namespace App\Models\Executive;

use App\Models\Department;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CarsSendback extends Model
{
    use SoftDeletes;
    protected $table = 'executive_cars_sendback';

    public $timestamps  = true;

    /**
     * 可以被批量赋值的属性.
     *
     * @var array
     */
    protected $fillable = [
        'title','cars_id','cars_use_id','people_number',
        'begin_time','end_time','mileage',
        'remark','entrise_id','status','user_id',
        'created_at','updated_at','deleted_at',
    ];
    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [

    ];

    protected $appends = [];

    public function cars(){
        return $this->hasOne(Cars::class,'id','cars_id')
            ->select(['id','title']);
    }

    public function user(){
        return $this->hasOne(User::class, 'id', 'driver_id')
            ->select(['id','chinese_name']);
    }

    public function department(){
        return $this->hasOne(Department::class,'id','department_id')
            ->select(['id','name']);
    }

    public function carsuse(){
        return $this->hasOne(CarsUse::class, 'id', 'cars_use_id');
    }
}
