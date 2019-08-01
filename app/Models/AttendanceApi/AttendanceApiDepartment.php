<?php
namespace App\Models\AttendanceApi;

use App\Models\Department;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AttendanceApiDepartment extends Model
{
    use SoftDeletes;

    protected $table = 'attendance_api_department';

    public $timestamps  = true;

    /**
     * 可以被批量赋值的属性.
     *
     * @var array
     */
    protected $fillable = [
        'attendance_id','department_id',
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


    public function departmentInfo(){
        return $this->hasOne(Department::class, 'id', 'department_id');
    }

    public function attendance(){
        return $this->hasOne(AttendanceApi::class, 'id', 'attendance_id');
    }
}
