<?php

namespace App\Models\Executive;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CarsRecord extends Model
{
    use SoftDeletes;
    //1-年检，2-保险，3-违章，4-事故，5-保养，6-维修，7-加油
    const MOT = 1;
    const INSURE = 2;
    const VIOLATION_OF_REGULATIONS = 3;
    const ACCIDENT = 4;
    const MAINTAIN = 5;
    const REPAIR = 6;
    const OIL = 7;

    protected $table = 'executive_cars_record';

    public $timestamps  = true;

    /**
     * 可以被批量赋值的属性.
     *
     * @var array
     */
    protected $fillable = [
        'cars_id','type','status','dates',
        'address','append',
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
}
