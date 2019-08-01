<?php

namespace App\Models\Executive;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CarsAppointRelationCar extends Model
{
    use SoftDeletes;
    protected $table = 'executive_cars_appoint_relation_car';

    public $timestamps  = true;

    /**
     * 可以被批量赋值的属性.
     *
     * @var array
     */
    protected $fillable = [
        'cars_appoint_id','cars_id',
        'created_at','updated_at','deleted_at',
    ];
}
