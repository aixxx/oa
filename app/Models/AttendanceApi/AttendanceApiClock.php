<?php
namespace App\Models\AttendanceApi;

use App\Constant\ConstFile;
use App\Http\Helpers\Dh;
use App\Models\User;
use App\Models\Vacations\UserVacation;
use App\Models\Workflow\Entry;
use App\Models\Workflow\Flow;
use App\Services\AttendanceApi\AttendanceApiService;
use Carbon\Carbon;
use function foo\func;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use \Exception;
use \DB;
use phpDocumentor\Reflection\Types\Self_;

class AttendanceApiClock extends Model
{
    use SoftDeletes;

    protected $table = 'attendance_api_clock';

    public $timestamps  = true;

    /**
     * 可以被批量赋值的属性.
     *
     * @var array
     */
    protected $fillable = [
        'user_id','dates','datetimes',
        'remark','remark_image','type',
        'clock_address_type','clock_nums',
        'created_at','updated_at','deleted_at',
        'classes_id','status','clock_address',
    ];
    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [

    ];

    protected $appends = [];

    public $user;
    public $data;
    public $rules;

    /**
    *   上班打卡
     * $data array 前端参数
     * $user object 用户
     * $rules object 规则
     * $YmdHis datetime 打卡时间
     * $work_time 工作时间
     */
    public function addBeginWorkClock($data, $user, $rules, $YmdHis, $work_time){
        /**
        *   需要参与考勤， 并且没有请假记录， 并且不在国家法定节假日
         */
        try{
            DB::transaction(function () use ($data, $user, $rules, $YmdHis, $work_time){
                //记录打卡信息
                $status = AttendanceApiService::API_STATUS_NORMAL;
                if($data['clock_address_type'] == AttendanceApiService::CLOCK_ADDRESS_OUT){
                    $status = self::getWorkflowInfo(Entry::WORK_FLOW_NO_OUTSIDE_PUNCH,$data, $user) === false
                        ? AttendanceApiService::API_STATUS_INVALID
                        : AttendanceApiService::API_STATUS_NORMAL;
                }
                $clockInfo = [
                    'user_id' => $user->id,
                    'dates' => $data['dates'],
                    'datetimes' => $YmdHis,
                    'remark' => isset($data['remark']) ? $data['remark'] : "",
                    'remark_image' => isset($data['remark_image']) ? $data['remark_image'] : "",
                    'type' => $data['type'],
                    'clock_address_type' => $data['clock_address_type'],
                    'clock_nums' => $work_time['clock_nums'],
                    'classes_id' => self::getClassesId($rules, $user->id, $data['dates']),
                    'status'=> $status,
                    'clock_address' => $data['clock_address'],
                ];

                $clock = self::query()->create($clockInfo);
                //迟到情况 排除 不参与考勤， 休息日或节假日， 自由制， 出差， 请假， 外出
                if(Dh::compare2Dates($YmdHis, $work_time['begin_work_time']) &&
                    Q($rules, 'is_attendance') !== AttendanceApiService::ATTENDANCE_STAFF_FALSE &&
                    AttendanceApiService::isWorkingDay($data['dates'], $rules) === true &&
                    Q($rules,'attendance','system_type') !== AttendanceApiService::ATTENDANCE_CLASSES_THR &&
                    self::getWorkflowInfo(Entry::VACATION_TYPE_BUSINESS_TRIP,$data, $user) === false &&
                    self::getWorkflowInfo(Entry::VACATION_TYPE_LEAVE,$data, $user) === false &&
                    self::getWorkflowInfo(Entry::WORK_FLOW_NO_OUTSIDE_PUNCH,$data, $user) === false
                ){
                    //迟到 判断是否设置弹性时间
                    $classes = Q($rules,'attendance','classes');
                    if(Q($classes, 'elastic_min') > 0){
                        //$begin_work_time = date('Y-m-d H:i:s', $classes->elastic_min * 60 + strtotime($work_time['begin_work_time']));
                        $begin_work_time = Dh::calcAddTime($work_time['begin_work_time'], $classes->elastic_min);
                        if(Dh::compare2Dates($YmdHis, $begin_work_time)){
                            self::addBeginAnomalyInfo($clock->id, $user->id, $data['dates'],
                                AttendanceApiService::CLOCK_ANOMALY_LATE, Dh::timeDiff($begin_work_time, $YmdHis),
                                $classes->serious_late_min, $classes->absenteeism_min);
                        }
                    }else{
                        //记录异常信息
                        self::addBeginAnomalyInfo($clock->id, $user->id, $data['dates'], AttendanceApiService::CLOCK_ANOMALY_LATE, Dh::timeDiff($work_time['begin_work_time'], $YmdHis),
                            $classes->serious_late_min, $classes->absenteeism_min);
                    }
                }
            });
            return returnJson(ConstFile::API_RESPONSE_SUCCESS_MESSAGE, ConstFile::API_RESPONSE_SUCCESS);
        }catch (Exception $e){
            return returnJson($e->getMessage(), ConstFile::API_RESPONSE_FAIL);
        }

    }

    /**
    *   记录上班异常情况
     * $user_id int 用户ID
     * $dates array 打卡日期
     * $anomaly_type int 异常类型 1-迟到 2-早退 3-加班
     * $anomaly_time int 异常时间
     * $serious_late_min int 后台设置严重迟到标准
     * $absenteeism_min int 后台设置旷工标准
     */
    public static function addBeginAnomalyInfo($clock_id, $user_id, $dates, $anomaly_type, $anomaly_time, $serious_late_min = 0, $absenteeism_min = 0, $is_count = 0){
        //判断是否严重迟到，旷工
        $is_absenteeism = $is_serious_late = 0;
        if($absenteeism_min > 0 && $anomaly_time > $absenteeism_min){
            $is_absenteeism = AttendanceApiService::ABSENTEEISM;
            $anomaly_type = AttendanceApiService::CLOCK_ANOMALY_ABSENTEEISM;
        }elseif ($serious_late_min > 0 && $anomaly_time > $serious_late_min){
            $is_serious_late = AttendanceApiService::SERIOUS_LATE;
        }
        $overtime_date_type = AttendanceApiService::OVERTIME_DATE_WORKINGDAY;
        if($anomaly_type == AttendanceApiService::CLOCK_ANOMALY_ADDWORK){
            //加班判断是 工作日  休息日  节假日
            $holidays = AttendanceApiNationalHolidays::query()->where(['dates'=> $dates])->first();
            if(empty($holidays)){
                $week = Dh::getWeek($dates);
                if($week == 6 || $week == 7){
                    $overtime_date_type = AttendanceApiService::OVERTIME_DATE_WEEKEND;
                }
            }else{
                if ($holidays['type'] == AttendanceApiService::WORKING_TO_REST) {
                    $overtime_date_type = AttendanceApiService::OVERTIME_DATE_HOLIDAYS;
                }
            }
        }

        $clock_nums = self::query()->where('id', $clock_id)->pluck('clock_nums')->first();
        $anomalyInfo = [
            'clock_id'=> $clock_id,
            'user_id' => $user_id,
            'dates' => $dates,
            'anomaly_type' => $anomaly_type,
            'anomaly_time' => $anomaly_time,
            'is_serious_late' => $is_serious_late,
            'is_absenteeism' => $is_absenteeism,
            'is_count' => $is_count,
            'overtime_date_type' => $overtime_date_type,
            'clock_nums' => $clock_nums,
        ];
        AttendanceApiAnomaly::query()->where([
            'user_id'=> $user_id,
            'dates' => $dates,
            'anomaly_type' => $anomaly_type
        ])->delete();
        AttendanceApiAnomaly::query()->create($anomalyInfo);
    }

    /**
     *   下班打卡
     * $data array 前端参数
     * $user object 用户
     * $rules object 规则
     * $YmdHis datetime 打卡时间
     * $work_time 工作时间
     */
    public function addEndWorkClock($data, $user, $rules, $YmdHis, $work_time, $begin_time){
        /**
         *   需要参与考勤， 并且没有可以不打卡的申请记录， 并且不在国家法定节假日
         */
        try{
            DB::transaction(function () use ($data, $user, $rules, $YmdHis, $work_time, $begin_time){
                //记录打卡信息
                $status = AttendanceApiService::API_STATUS_NORMAL;
                if($data['clock_address_type'] == AttendanceApiService::CLOCK_ADDRESS_OUT){
                    $status = self::getWorkflowInfo(Entry::WORK_FLOW_NO_OUTSIDE_PUNCH,$data, $user) === false
                        ? AttendanceApiService::API_STATUS_INVALID
                        : AttendanceApiService::API_STATUS_NORMAL;
                }

                $clockInfo = [
                    'user_id' => $user->id,
                    'dates' => $data['dates'],
                    'datetimes' => $YmdHis,
                    'remark' => isset($data['remark']) ? $data['remark'] : "",
                    'remark_image' => isset($data['remark_image']) ? $data['remark_image'] : "",
                    'type' => $data['type'],
                    'clock_address_type' => $data['clock_address_type'] ? $data['clock_address_type'] : 1,
                    'clock_nums' => $work_time['clock_nums'],
                    'classes_id' => self::getClassesId($rules, $user->id, $data['dates']),
                    'status' => $status,
                    'clock_address' => $data['clock_address'],
                ];
                self::query()->where([
                    'user_id' => $user->id,
                    'type' => $data['type'],
                    'clock_nums' => $work_time['clock_nums'],
                ])->delete();

                $clock = self::query()->create($clockInfo);
                if(Dh::compare2Dates($work_time['end_work_time'], $YmdHis)){
                    //早退-> 参加考勤， 工作日， 没有出差，没有请假， 没有出勤 , 今天没有旷工
                    //是否参与考勤，默认是true
                    $rules['is_attendance'] = Q($rules, 'is_attendance') ? Q($rules, 'is_attendance') : AttendanceApiService::ATTENDANCE_STAFF_TRUE;
                    if(
                        $rules['is_attendance'] == AttendanceApiService::ATTENDANCE_STAFF_TRUE &&
                        AttendanceApiService::isWorkingDay($data['dates'], $rules) === true &&
                        $rules['attendance']['system_type'] !== AttendanceApiService::ATTENDANCE_CLASSES_THR &&
                        self::getWorkflowInfo(Entry::VACATION_TYPE_BUSINESS_TRIP,$data, $user) === false &&
                        self::getWorkflowInfo(Entry::VACATION_TYPE_LEAVE,$data, $user) === false &&
                        self::getWorkflowInfo(Entry::WORK_FLOW_NO_OUTSIDE_PUNCH,$data, $user) === false
                    ){
                        //查看今天是否有旷工记录
                        $absenteeism_info = AttendanceApiAnomaly::query()
                            ->where('dates', $data['dates'])
                            ->where('user_id', $user->id)
                            ->where('anomaly_type', AttendanceApiService::CLOCK_ANOMALY_ABSENTEEISM)
                            ->first();
                        //记录异常信息
                        if(empty($absenteeism_info)){
                            self::addBeginAnomalyInfo(
                                $clock->id,
                                $user->id,
                                $data['dates'],
                                AttendanceApiService::CLOCK_ANOMALY_LEAVE_EARLY,
                                Dh::timeDiff($YmdHis, $work_time['end_work_time'])
                            );
                        }
                    }
                }else {
                    //清空早退信息
                    AttendanceApiAnomaly::query()->where([
                        'user_id'=> $user->id,
                        'dates' => $data['dates'],
                        'anomaly_type' => AttendanceApiService::CLOCK_ANOMALY_LEAVE_EARLY,
                    ])->delete();
                    //加班-> 参加考勤，固定制考勤，排班制考勤，加班申请， 工作日根据下班时间计算，非工作日打卡时间计算
                    if (
                        (!Q($rules, 'is_attendance') || Q($rules, 'is_attendance') == AttendanceApiService::ATTENDANCE_STAFF_TRUE) &&
                        Q($rules, 'attendance', 'system_type') !== AttendanceApiService::ATTENDANCE_CLASSES_THR
                    ) {
                        if (AttendanceApiService::isWorkingDay($data['dates'], $rules) === true) {
                            //工作日
                            $check_info = self::getWorkflowInfo(Entry::VACATION_TYPE_EXTRA,$data, $user);
                            $overtime = AttendanceApiService::getWorkOvertime($rules, $YmdHis, $check_info, $data['dates']);
                            $is_count = 0;
                            //调休时间先减去已经统计的加班时间
                            if (!empty($check_info) && $overtime > 0) $is_count = self::updateVacationsTxdays($data, $user,$overtime);

                            //记录加班信息
                            if($overtime > 0) self::addBeginAnomalyInfo($clock->id,$user->id,$data['dates'],AttendanceApiService::CLOCK_ANOMALY_ADDWORK,
                                $overtime,0,0,$is_count);
                        }else{
                            //非工作日
                            $check_info = self::getWorkflowInfo(Entry::VACATION_TYPE_EXTRA,$data, $user);
                            $overtime = AttendanceApiService::getRestOvertime($rules, $YmdHis, $check_info, $begin_time);
                            $is_count = 0;
                            //调休时间先减去已经统计的加班时间
                            if (!empty($check_info) && $overtime > 0) $is_count = self::updateVacationsTxdays($data, $user,$overtime);
                            //记录加班信息
                            if($overtime > 0) self::addBeginAnomalyInfo($clock->id,$user->id, $data['dates'],AttendanceApiService::CLOCK_ANOMALY_ADDWORK,
                                $overtime,0,0,$is_count);
                        }
                    }
                }

            });
            return returnJson(ConstFile::API_RESPONSE_SUCCESS_MESSAGE, ConstFile::API_RESPONSE_SUCCESS);
        }catch (Exception $e){
            return returnJson($e->getMessage(), ConstFile::API_RESPONSE_FAIL);
        }
    }

    public static function getClassesId($rules, $user_id, $dates){
        switch (Q($rules,"attendance","system_type")){
            case AttendanceApiService::ATTENDANCE_SYTTEM_FIXED:
                $classes_id = Q($rules,"attendance","classes","id");
                break;
            case AttendanceApiService::ATTENDANCE_SYTTEM_SORT:
                $scheduling = AttendanceApiScheduling::getSchedulingInfo($user_id, $dates);
                $classes_id = $scheduling['classes_id'];
                break;
            case AttendanceApiService::ATTENDANCE_SYTTEM_FREE:
                $classes_id = 0;
                break;
        }
        return $classes_id;
    }

    /**
    *   修改调休时长
     */
    public function updateVacationsTxdays($data, $user, $overtime){
        $is_count_overtime = AttendanceApiAnomaly::query()->where([
            'user_id' => $user->id,
            'dates' => $data['dates'],
            'anomaly_type' => AttendanceApiService::CLOCK_ANOMALY_ADDWORK,
            'is_count' => AttendanceApiService::IS_COUNT_YES,
        ])->first();
        if($is_count_overtime){
            if($is_count_overtime->anomaly_time == $overtime){
                return AttendanceApiService::IS_COUNT_YES;
            }else{
                UserVacation::query()->where(['user_id' => $user->id])->decrement('rest_time', $is_count_overtime->anomaly_time);
            }
        }
        $vacationObj = UserVacation::query()->where('user_id', '=', $user->id)->first();
        if($vacationObj){
            //更新调休时间
            UserVacation::query()->where(['user_id' => $user->id])->increment('rest_time', $overtime);
            return AttendanceApiService::IS_COUNT_YES;
        }else{
            UserVacation::query()->create([
                'user_id' => $user->id,
                'rest_time' => $overtime
            ]);
            return AttendanceApiService::IS_COUNT_YES;
        }

    }


    /**
    *   请假
     */
    public static function getWorkflowInfo($type, $data, $user) {
        $work_flow = Flow::query()->where([
            'flow_no'=> $type,//Entry::WORK_FLOW_NO_HOLIDAY,
            'is_abandon'=> 0
        ])->orderBy("id", "desc")->first(['id']);
        $enrtys = Entry::fetchEntry($user->id, $work_flow["id"], $data['dates'], $data['dates']);
        //dd($enrty);
        if(empty($enrtys)){
            return false;
        }else{
            return $enrtys;
        }
    }

    public function classes(){
        return $this->hasOne(AttendanceApiClasses::class, 'id', 'classes_id');
    }


    public function anomaly(){
        return $this->hasOne(AttendanceApiAnomaly::class, 'clock_id', 'id')
            ->select(['id','clock_id','dates','anomaly_type','anomaly_time','is_serious_late']);
    }

    public function user(){
        return $this->hasOne(User::class, 'id','user_id')
            ->select(['id','avatar','chinese_name']);
    }

    public static function getClockList($user_id, $begin, $end){
        return self::query()->where([
            ['user_id','=',$user_id],
            ['dates','>=',$begin],
            ['dates','<=',$end],
            ['status','=',AttendanceApiService::API_STATUS_NORMAL],
        ])->select(['id','dates','datetimes','remark','remark_image','type','clock_address_type','clock_nums','classes_id'])
            ->get()->groupBy('dates');
    }
}
