<?php
namespace App\Models\AttendanceApi;

use App\Models\User;
use App\Services\AttendanceApi\AttendanceApiService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AttendanceApiUpdateUserClockForHr extends Model
{
    use SoftDeletes;

    protected $table = 'attendance_api_hr_update_clock_log';

    public $timestamps  = true;

    /**
     * 可以被批量赋值的属性.
     *
     * @var array
     */
    protected $fillable = [
        'admin_id','user_id','classes_id',
        'dates','remark','remark_image',
        'type','anomaly_type','anomaly_time',
        'clock_nums','work_time','anomaly_id',
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
