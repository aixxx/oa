<?php
namespace App\Models\AttendanceApi;

use App\Constant\ConstFile;
use App\Http\Helpers\Dh;
use App\Models\User;
use App\Models\Vacations\UserVacation;
use App\Repositories\AttendanceApi\AttendanceApiClockRespository;
use App\Services\AttendanceApi\AttendanceApiService;
use App\Services\AttendanceApi\CountsService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;

class AttendanceApiAnomaly extends Model
{
    use SoftDeletes;

    const TRIP = 1;    //出差
    const LEAVEOUT = 2;    //出勤
    const ADDRESS_TYPE_LEAVE = 3;  //请假

    const NORMAL = 0;   //正常
    const MISSING = 4;  //缺卡
    const ABSENTEEISM = 5;    //旷工

    protected $table = 'attendance_api_anomaly';

    public $timestamps  = true;

    /**
     * 可以被批量赋值的属性.
     *
     * @var array
     */
    protected $fillable = [
        'user_id','dates','anomaly_type',
        'is_serious_late','is_absenteeism','is_count',
        'anomaly_time','updated_at','deleted_at',
        'overtime_date_type','clock_id','clock_nums'
    ];
    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [

    ];

    protected $appends = [];

    public function user(){
        return $this->hasOne(User::class, 'id','user_id');
    }

    public function clock(){
        return $this->hasOne(AttendanceApiClock::class, 'id', 'clock_id');
    }

    public static function getAnomalyList($user_id, $begin, $end){
        return self::query()->where([
            ['user_id', '=', $user_id],
            ['dates', '>=', $begin],
            ['dates', '<=', $end],
        ])->get();
    }

    /**
     * $type 类型 1-出差， 2-出勤， 3-请假
     * @param $user_id
     * @param $begin_time
     * @param $end_time
     * @param $type
     * @return array
     * @throws \Throwable
     */
    public static function workflowCheck($user_id, $begin_time, $end_time, $type){
        try{
            \DB::transaction(function () use ($user_id, $begin_time, $end_time, $type) {
                //删除异常表中 出勤时间段的数据
                $begin_date = substr($begin_time,0,10);
                $end_date = substr($end_time,0,10);
                self::query()
                    ->where('user_id', $user_id)
                    ->whereBetween('dates', [$begin_date, $end_date])
                    ->where(
                        function ($query){
                            /** @var Builder $query */
                            $query->orWhere('anomaly_type', AttendanceApiService::CLOCK_ANOMALY_LATE)
                                ->orWhere('anomaly_type', AttendanceApiService::CLOCK_ANOMALY_LEAVE_EARLY);
                    })->delete();
                //修改打卡表
                $update_data = [];
                switch ($type){
                    case self::TRIP:
                        $update_data = [
                            'status'=> AttendanceApiService::API_STATUS_NORMAL,
                            'clock_address_type' => self::TRIP,
                        ];
                        break;
                    case self::LEAVEOUT:
                        $update_data = [
                            'status'=> AttendanceApiService::API_STATUS_NORMAL,
                            'clock_address_type' => self::LEAVEOUT,
                        ];
                        break;
                    default:
                        break;
                }
                if($update_data){
                    AttendanceApiClock::query()->where([
                        ['datetimes', '>=', $begin_time],
                        ['datetimes', '<=', $end_time],
                        ['user_id', '=', $user_id],
                        ['clock_address_type', '=', AttendanceApiService::CLOCK_ADDRESS_OUT],
                        ['status', '=', AttendanceApiService::API_STATUS_INVALID]
                    ])->update($update_data);
                }
            });
            return [
                'code' => ConstFile::API_RESPONSE_SUCCESS,
                'message' => '',
            ];
        }catch (\Exception $e){
            return [
                'code' => ConstFile::API_RESPONSE_FAIL,
                'message' => $e->getMessage(),
            ];
        }
    }

    /*
     * 加班申请验证
     * */
    public static function overtimeValidator($user_id, $begin_time, $end_time){
        //计算时间区间
        $between_date = Dh::getbetweenDay($begin_time, $end_time);
        $overtime_type_check = $overtime_day = [];
        foreach ($between_date as $k=>$v){
            $rules = AttendanceApiStaff::getUserAttendanceRule(User::find($user_id),['dates'=>$v]);

            $overtime_rule = Q($rules, 'attendance', 'overtimeRule');
            //工作日
            if(AttendanceApiService::isWorkingDay($v, $rules) === true &&
                !empty(Q($rules, 'attendance', 'classes'))
            ){
                if($overtime_rule['is_working_overtime'] == AttendanceApiService::WORKING_OVERTIME_YES){
                    if($overtime_rule['working_overtime_type'] == AttendanceApiService::OVERTIME_TYPE_CHECK){
                        //以审批单为准。 返回给审批流程处理
                        $overtime_type_check[] = $v;
                    }else{
                        $overtime_day[] = $v;
                    }
                }else{
                    return [
                        'code' => ConstFile::API_RESPONSE_FAIL,
                        'message' => "申请日期中包含工作日: {$v}，该考勤组工作日不允许加班",
                    ];
                }
            }else{
                if($overtime_rule['is_rest_overtime'] == AttendanceApiService::REST_OVERTIME_YES){
                    if($overtime_rule['rest_overtime_type'] == AttendanceApiService::OVERTIME_TYPE_CHECK){
                        //以审批单为准。 返回给审批流程处理
                        $overtime_type_check[] = $v;
                    }else{
                        $overtime_day[] = $v;
                    }
                }else{
                    return [
                        'code' => ConstFile::API_RESPONSE_FAIL,
                        'message' => "申请日期中包含休息日: {$v}，该考勤组休息日不允许加班",
                    ];
                }
            }
        }
        return [
            'code'=> ConstFile::API_RESPONSE_SUCCESS,
            'data'=> [
                'overtime_type_check'=> $overtime_type_check,  //以审批单为准的日期
                'overtime_day'=> $overtime_day,  //以打卡为准的日期
            ],
        ];
    }

    /**
     *  加班审核通过
     * */
    public static function overtimeCheck($user_id, $begin_time, $end_time){
        try{
            $overtime = self::overtimeValidator($user_id, $begin_time, $end_time);
            if($overtime['code'] != ConstFile::API_RESPONSE_SUCCESS) return $overtime;
            $overtime_type_check = $overtime['data']['overtime_type_check'];
            $overtime_day = $overtime['data']['overtime_day'];
            $rest_time = 0;
            $anomaly_id = [];
            //处理已打卡为准的数据
            foreach ($overtime_day as $k=>$v){
                $info = AttendanceApiAnomaly::query()->where([
                    'user_id'=> $user_id,
                    'dates'=> $v,
                    'anomaly_type'=> AttendanceApiService::CLOCK_ANOMALY_ADDWORK,
                    'is_count' => 0,
                ])->first();
                if ($info){
                    $rest_time += $info['anomaly_time'];
                    $anomaly_id[] = $info['id'];
                }
            }

            \DB::transaction(function () use ($user_id, $rest_time, $anomaly_id) {
                $vacation = UserVacation::query()->where('user_id', $user_id)->first();
                if(empty($vacation)){
                    UserVacation::query()->create([
                        'user_id'=> $user_id,
                        'rest_time'=> $rest_time,
                    ]);
                }else{
                    UserVacation::query()->where([
                        'user_id'=> $user_id,
                    ])->increment('rest_time', $rest_time);
                }
                if($anomaly_id){
                    AttendanceApiAnomaly::query()
                        ->whereIn('id', $anomaly_id)
                        ->update(['is_count'=> AttendanceApiService::IS_COUNT_YES]);
                }
            });
            return [
                'code' => ConstFile::API_RESPONSE_SUCCESS,
                'message' => '',
                'rest_time'=> $rest_time,
                'cannot'=> $overtime_type_check,
            ];
        }catch (\Exception $e){
            return [
                'code' => ConstFile::API_RESPONSE_FAIL,
                'message' => $e->getMessage(),
            ];
        }
    }

    /*
     * 补卡审核通过
     * $type 类型 1-上班 2-下班
     * $dates 日期 （可能出现加班到第二天打卡的情况）
     * $datetimes 打卡时间
     * $user_id 用户ID
     * */
    public static function addClock($type, $dates, $datetimes, $user_id){
        $clock = app()->make(AttendanceApiClockRespository::class);
        if($type == AttendanceApiService::BEGIN_WORK){
            AttendanceApiClock::query()->where([
                'dates'=> $dates,
                'user_id'=> $user_id,
                'type'=> $type
            ])->delete();
            self::query()->where([
                'dates'=> $dates,
                'user_id'=> $user_id,
                'anomaly_type'=> AttendanceApiService::CLOCK_ANOMALY_LATE,
            ])->delete();
        }else{
            self::query()->where([
                'dates'=> $dates,
                'user_id'=> $user_id,
                'anomaly_type'=> AttendanceApiService::CLOCK_ANOMALY_LEAVE_EARLY,
            ])->delete();
        }
        return $clock->clock(['dates'=> $dates,'type'=> $type], User::query()->find($user_id), $datetimes);
    }
}

