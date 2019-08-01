<?php

namespace App\Models\CaiWu;

use App\Models\Workflow\Flow;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FlowClass extends Model
{
//    use SoftDeletes;

    protected $table = 'flow_class';
	protected $connection = 'caiwudb';
    protected $primaryKey = 'id';

    public $timestamps  = false;

    /**
     * 可以被批量赋值的属性.
     *
     * @var array
     */
    protected $fillable = [
        'type',
        'code',
        'name',
        'create_time',
        'create_uid',
        'sof_id',
        'status'
    ];
    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [

    ];

    protected $appends = [];
    public function  getCaiWuFlow(){
        $this->hasMany(Flow::class,'class_id','id');
    }
}
