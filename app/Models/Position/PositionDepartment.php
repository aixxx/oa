<?php

namespace App\Models\Position;

use App\Models\Department;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use DB;
use \Exception;

class PositionDepartment extends Model
{
    use SoftDeletes;

    protected $table = 'position_department';

    protected $dates = ['deleted_at'];
    protected $primaryKey = 'id';
    public $fillable = [
        'position_id',
        'department_id'
    ];

    public function position()
    {
        return $this->hasOne('App\Models\Position\Position', 'id', 'position_id');
    }

    public function hasOneDepartment(){
        return $this->hasOne(Department::class, 'id', 'department_id');
    }
}
