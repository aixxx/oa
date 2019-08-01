<?php

namespace App\Repositories\AttendanceApi;

use App\Constant\ConstFile;
use App\Http\Requests\AttendanceApi\AttendanceApiRequest;
use App\Models\AttendanceApi\AttendanceApi;
use App\Models\AttendanceApi\AttendanceApiDepartment;
use App\Models\AttendanceApi\AttendanceApiOvertimeRule;
use App\Models\AttendanceApi\AttendanceApiScheduling;
use App\Models\AttendanceApi\AttendanceApiStaff;
use App\Models\Department;
use App\Repositories\UsersRepository;
use App\Services\AttendanceApi\AttendanceApiService;
use Carbon\Carbon;
use DB;
use \Exception;
use App\Repositories\Repository;

class AttendanceApiRespository extends Repository {

    public function model() {
        return AttendanceApi::class;
    }

    /**
     *   获取考勤人员信息
     */
    public function getAttendanceDepartment($data, $user){
        try{
            $data = Department::query()->with(['attendanceDepartment.attendance','attendanceDepartmentUser.user.attendanceStaff.attendance'])->get();
            return returnJson(ConstFile::API_RESPONSE_SUCCESS_MESSAGE, ConstFile::API_RESPONSE_SUCCESS, $data);

        }catch (Exception $e){
            return returnJson($e->getMessage(), ConstFile::API_RESPONSE_FAIL);
        }
    }

    /**
    *   获取考勤组列表
     */
    public function getList($data, $user){
        try{
            $data = AttendanceApi::query()
                ->with(['classes','staffAll','attendanceDepartment.departmentInfo'])
                ->get();
            foreach ($data as $k=>$v){
                $department_name = [];
                if(Q($v, 'attendanceDepartment')){
                    foreach ($v->attendanceDepartment as $k1=>$v1){
                        if(Q($v1, 'departmentInfo', 'name')){
                            $department_name[] = Q($v1, 'departmentInfo', 'name');
                        }
                    }
                }
                $data[$k]->department_name = implode(',', $department_name);
                if(!Q($v, 'classes')){
                    unset($data[$k]->classes);
                    $data[$k]->classes = [
                        'work_time_begin1' => Q($v, 'clock_node'),
                        'work_time_end1' => Q($v, 'clock_node'),
                    ];
                }
            }
            return returnJson(ConstFile::API_RESPONSE_SUCCESS_MESSAGE, ConstFile::API_RESPONSE_SUCCESS, $data);

        }catch (Exception $e){
            return returnJson($e->getMessage(), ConstFile::API_RESPONSE_FAIL);
        }
    }

    /*
     * 是否参与其他考勤组
     * */
    public function isUser($ids, $id = 0){
        if($id == 0){
            $is_ids = AttendanceApiStaff::query()
                ->rightJoin('attendance_api as b','attendance_api_staff.attendance_id','=','b.id')
                ->whereIn('user_id', $ids)
                ->get();
        }else{
            $is_ids = AttendanceApiStaff::query()
                ->where('attendance_id','!=', $id)
                ->whereIn('user_id', $ids)
                ->get();
        }

        if($is_ids->isNotEmpty()) {
            $user_title = [];
            foreach ($is_ids as $k=>$v){
                $user_title[] = $v->userInfo['chinese_name'];
            }

            return returnJson('员工 '.implode(', ',$user_title) .' 已经被其他考勤组设置成参加考勤人员', ConstFile::API_RESPONSE_SUCCESS, true);
        }else{
            return returnJson('',ConstFile::API_RESPONSE_SUCCESS, false);
        }
    }

    //获取部门包括子部门数据
    public static function getAllDepartment($ids){
        if ($ids == '-1'){
            //部门全选
            $dept_ids = Department::query()->pluck('id')->all();
            return $dept_ids;
        }else if(is_array($ids)) {
            //选中部分部门
            $dept_ids = [];
            foreach ($ids as $v){
                $list[] = app()->make(UsersRepository::class)->getChild($v);
            }
            $list_to_string = implode(',', $list);
            $dept_ids = explode(',', $list_to_string);
            $dept_ids = array_unique($dept_ids);
            return $dept_ids;
        }else{
            return returnJson(ConstFile::API_PARAMETER_MISS,ConstFile::API_RESPONSE_FAIL);
        }
    }

    public function isDepartment($ids, $id = 0){
        //获取部门包括子部门数据
        $dept_ids = self::getAllDepartment($ids);

        $is_ids = AttendanceApiDepartment::query()
            ->rightJoin('attendance_api as b','attendance_api_department.attendance_id','=','b.id')
            ->whereIn('department_id', $dept_ids);

        if($id != 0) $is_ids->where('attendance_id','!=', $id);

        $dept_list = $is_ids->get();

        if($dept_list->isNotEmpty()) {
            $depart_title = [];
            foreach ($dept_list as $k=>$v){
                $depart_title[] = $v->departmentInfo['name'];
            }
            return returnJson('部门 '.implode(', ',$depart_title) .' 已经被其他考勤组设置过', ConstFile::API_RESPONSE_SUCCESS, true);
        }else{
            return returnJson('',ConstFile::API_RESPONSE_SUCCESS, false);
        }
    }

    /**
    *   根据ID查看
     */
    public function getAttendanceById($id){
        try{
            //.departmentInfo
            $data = AttendanceApi::query()
                ->where('id', $id)
                ->with(['classes','headUser','overtimeRule','isAttendanceTrue' ,'isAttendanceFalse',
                    'attendanceDepartment'=> function($query){
                        $query->with('departmentInfo')->whereHas('departmentInfo');
                    }])
                ->first();
            $data->classes_id = explode(',', $data->classes_id);
            $total = 0;
            if(Q($data, 'classes', 'type')){
                $v = Q($data, 'classes');
                for($i = 1; $i <= $v['type']; $i++){
                    if($v["work_time_end{$i}"] <= $v["work_time_begin{$i}"]){
                        $begin = Carbon::parse($v["work_time_begin{$i}"]);
                        $end = Carbon::parse($v["work_time_end{$i}"])->addDay();
                        $total += $end->diffInHours($begin);
                    }else{
                        $begin = Carbon::parse($v["work_time_begin{$i}"]);
                        $end = Carbon::parse($v["work_time_end{$i}"]);
                        $total += $end->diffInHours($begin);
                    }
                }
                if($v['is_siesta'] == AttendanceApiService::ATTENDANCE_CLASSES_SIESTA){
                    $total -= Carbon::parse($v["end_siesta_time"])
                        ->diffInHours(Carbon::parse($v["begin_siesta_time"]));
                }
                $data->total = $total;
            }
            return returnJson(ConstFile::API_RESPONSE_SUCCESS_MESSAGE, ConstFile::API_RESPONSE_SUCCESS, $data);

        }catch (Exception $e){
            return returnJson($e->getMessage(), ConstFile::API_RESPONSE_FAIL);
        }
    }

    /**
    *   添加考勤组
     */
    public function addAttendance($data, $user){
        try{
            //验证
            $check_result = app()->make(AttendanceApiRequest::class)->attendanceValidatorForm($data);
            if($check_result !== true) return $check_result;

            DB::transaction(function() use($data, $user){
                $data['classes_id'] = isset($data['classes_id']) ? implode(',', $data['classes_id']) : '';
                $data['weeks'] = isset($data['weeks']) ? implode(',', $data['weeks']) : '';
                //设置加班规则
                if(!isset($data['overtime_rule_id']) || !$data['overtime_rule_id']){
                    //添加默认加班规则
                    $data['overtime_rule_id'] = AttendanceApiService::DEFAULT_ID;
                }
                //记录考勤规则
                $data['admin_id'] = $user->id;
                $attendance = AttendanceApi::query()->create($data);
                //记录考勤部门， 考勤参与人员， 不参加考勤人员
                $YmdHis = date('Y-m-d H:i:s');
                //设置参加考勤的用户
                $is_other_user_ids = [];
                //设置参加考勤的部门
                $is_other_department_ids = [];
                //设置已经参加的排班
                $is_other_scheduling_ids = [];
                if(isset($data['department_ids'])){
                    //获取部门包括子部门数据
                    $dept_ids = self::getAllDepartment($data['department_ids']);
                    $department = [];
                    foreach ($dept_ids as $k => $v){
                        $department[$v] = [
                            'attendance_id' => $attendance->id,
                            'department_id' => $v,
                            'created_at' => $YmdHis,
                            'updated_at' => $YmdHis,
                        ];
                        $is_other_department_ids[] = $v;
                    }
                    AttendanceApiDepartment::query()->whereIn('department_id',$is_other_department_ids)->delete();
                    AttendanceApiDepartment::query()->insert($department);
                }

                $staff_ids = [];
                if(isset($data['staff_true_ids'])){
                    foreach ($data['staff_true_ids'] as $k => $v){
                        $staff_ids[$v] = [
                            'attendance_id' => $attendance->id,
                            'user_id' => $v,
                            'is_attendance' => ConstFile::ATTENDANCE_STAFF_TRUE,
                            'created_at' => $YmdHis,
                            'updated_at' => $YmdHis,
                        ];
                        $is_other_user_ids[] = $v;
                    }
                }

                if(isset($data['staff_false_ids'])){
                    foreach ($data['staff_false_ids'] as $k => $v){
                        $staff_ids[$v] = [
                            'attendance_id' => $attendance->id,
                            'user_id' => $v,
                            'is_attendance' => ConstFile::ATTENDANCE_STAFF_FALSE,
                            'created_at' => $YmdHis,
                            'updated_at' => $YmdHis,
                        ];
                        $is_other_user_ids[] = $v;
                    }
                }
                if($staff_ids){
                    AttendanceApiStaff::query()->whereIn('user_id',$is_other_user_ids)->delete();
                    AttendanceApiStaff::query()->insert($staff_ids);
                }
                //排班信息
                if(isset($data['scheduling']['data']) && $data['scheduling']['data']){
                    foreach ($data['scheduling']['data'] as $k=>$v) {
                        foreach ($v['scheduling'] as $k1=>$v1){
                            $list[$v['id']] = [
                                'attendance_id' => $attendance->id,
                                'user_id' => $v['id'],
                                'dates' => Carbon::parse($v1['dates'])->toDateString(),
                                'classes_id' => isset($v1['id']) ? intval($v1['id']) : 0,
                                'take_effect_dates' => $data['scheduling']['take_effect_dates'],
                                'created_at' => $YmdHis,
                                'updated_at' => $YmdHis,
                                'admin_id' => $user->id,
                            ];
                            $is_other_scheduling_ids[] = $v['id'];
                        }
                    }
                    AttendanceApiScheduling::query()->whereIn('user_id',$is_other_scheduling_ids)->delete();
                    AttendanceApiScheduling::query()->insert($list);
                }
            });
            return returnJson(ConstFile::API_RESPONSE_SUCCESS_MESSAGE, ConstFile::API_RESPONSE_SUCCESS);
        }catch (Exception $e){
            return returnJson($e->getMessage(), ConstFile::API_RESPONSE_FAIL);
        }
    }

    /**
     *   编辑考勤组
     */
    public function updateAttendance($id, $data, $user){
        try{
            //验证
            $id = intval($id);
            if(!$id) return returnJson('ID参数错误', ConstFile::API_RESPONSE_FAIL);

            $check_result = app()->make(AttendanceApiRequest::class)->attendanceValidatorForm($data, $id);
            if($check_result !== true) return $check_result;
            DB::transaction(function() use($id, $data, $user){
                //编辑考勤规则
                $attendance_list = [
                    'title' => $data['title'],
                    'system_type' => $data['system_type'],
                    'add_clock_num' => $data['add_clock_num'],
                    'address' => $data['address'],
                    'clock_range' => $data['clock_range'],
                    'wifi_title' => $data['wifi_title'],
                    'admin_id' => $user->id,
                    'classes_id' => isset($data['classes_id']) ? implode(',', $data['classes_id']) : '',
                    'weeks' => isset($data['weeks']) ? implode(',', $data['weeks']) : '',
                    'cycle_id' => isset($data['cycle_id']) ? $data['cycle_id'] : 0,
                    'clock_node' => isset($data['clock_node']) ? $data['clock_node'] : '',
                    'head_user_id' => isset($data['head_user_id']) ? $data['head_user_id'] : 0,
                    'is_getout_clock' => isset($data['is_getout_clock']) ? $data['is_getout_clock'] : 0,
                    'overtime_rule_id' => $data['overtime_rule_id'],
                    'lng' => isset($data['lng']) ? $data['lng'] : 0,
                    'lat' => isset($data['lat']) ? $data['lat'] : 0,
                ];

                AttendanceApi::query()->where('id', $id)->update($attendance_list);
                //记录考勤部门， 考勤参与人员， 不参加考勤人员
                AttendanceApiDepartment::query()->where('attendance_id', $id)->delete();
                //设置参加考勤的用户
                $is_other_user_ids = [];
                //设置参加考勤的部门
                $is_other_department_ids = [];
                //设置已经参加的排班
                $is_other_scheduling_ids = [];
                $YmdHis = date('Y-m-d H:i:s');
                if(isset($data['department_ids'])){
                    $dept_ids = self::getAllDepartment($data['department_ids']);
                    $department = [];
                    foreach ($dept_ids as $k => $v){
                        $department[$v] = [
                            'attendance_id' => $id,
                            'department_id' => $v,
                            'created_at' => $YmdHis,
                            'updated_at' => $YmdHis,
                        ];
                        $is_other_department_ids[] = $v;
                    }
                    AttendanceApiDepartment::query()->whereIn('department_id', $is_other_department_ids)->delete();
                    AttendanceApiDepartment::query()->insert($department);
                }

                //删除相关联的人员ID
                AttendanceApiStaff::query()->where('attendance_id', $id)->delete();
                $staff_ids = [];
                if(isset($data['staff_true_ids'])){
                    foreach ($data['staff_true_ids'] as $k => $v){
                        $staff_ids[$v] = [
                            'attendance_id' => $id,
                            'user_id' => $v,
                            'is_attendance' => ConstFile::ATTENDANCE_STAFF_TRUE,
                            'created_at' => $YmdHis,
                            'updated_at' => $YmdHis,
                        ];
                        $is_other_user_ids[] = $v;
                    }
                }

                if(isset($data['staff_false_ids'])){
                    foreach ($data['staff_false_ids'] as $k => $v){
                        $staff_ids[$v] = [
                            'attendance_id' => $id,
                            'user_id' => $v,
                            'is_attendance' => ConstFile::ATTENDANCE_STAFF_FALSE,
                            'created_at' => $YmdHis,
                            'updated_at' => $YmdHis,
                        ];
                        $is_other_user_ids[] = $v;
                    }
                }
                //重建相关联的人员ID
                if($staff_ids) {
                    AttendanceApiStaff::query()->whereIn('user_id', $is_other_user_ids)->delete();
                    AttendanceApiStaff::query()->insert($staff_ids);
                }
                //排班信息
                if(isset($data['scheduling']['data']) && $data['scheduling']['data']){
                    foreach ($data['scheduling']['data'] as $k=>$v) {
                        foreach ($v['scheduling'] as $k1=>$v1){
                            $list[$v['id']] = [
                                'attendance_id' => $id,
                                'user_id' => $v['id'],
                                'dates' => Carbon::parse($v1['dates'])->toDateString(),
                                'classes_id' => isset($v1['id']) ? intval($v1['id']) : 0,
                                'take_effect_dates' => $data['scheduling']['take_effect_dates'],
                                'created_at' => $YmdHis,
                                'updated_at' => $YmdHis,
                                'admin_id' => $user->id,
                            ];
                            $is_other_scheduling_ids[] = $v['id'];
                        }
                    }
                    AttendanceApiScheduling::query()->whereIn('user_id',$is_other_scheduling_ids)->delete();
                    AttendanceApiScheduling::query()->where('attendance_id',$id)->delete();
                    AttendanceApiScheduling::query()->insert($list);
                }
            });
            return returnJson(ConstFile::API_RESPONSE_SUCCESS_MESSAGE, ConstFile::API_RESPONSE_SUCCESS);
        }catch (Exception $e){
            return returnJson($e->getMessage(), ConstFile::API_RESPONSE_FAIL);
        }
    }

    /**
    *   删除考勤组
     */
    public function delAttendance($id, $user){
        try{
            $id = intval($id);
            if(!$id) return returnJson('ID错误', ConstFile::API_RESPONSE_FAIL);
            $data = [
                'admin_id' => $user->id,
                'deleted_at' => date('Y-m-d H:i:s'),
            ];
            $res = AttendanceApi::where('id', $id)->update($data);
            if($res){
                return returnJson(ConstFile::API_RESPONSE_SUCCESS_MESSAGE, ConstFile::API_RESPONSE_SUCCESS);
            }else{
                return returnJson(ConstFile::API_RESPONSE_FAIL_MESSAGE, ConstFile::API_RESPONSE_FAIL);
            }
        }catch (Exception $e){
            return returnJson($e->getMessage(), ConstFile::API_RESPONSE_FAIL);
        }
    }
}
