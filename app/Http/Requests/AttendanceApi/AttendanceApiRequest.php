<?php

namespace App\Http\Requests\AttendanceApi;

use App\Models\AttendanceApi\AttendanceApiDepartment;
use App\Models\AttendanceApi\AttendanceApiStaff;
use App\Services\AttendanceApi\AttendanceApiService;
use Illuminate\Foundation\Http\FormRequest;
use Validator;
use App\Constant\ConstFile;

class AttendanceApiRequest extends FormRequest {

	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize()
	{
		return true;
	}

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules()
	{
		return [
			
		];
	}

	public function getAttendanceRules(){
	    return [
            'title' => 'required',
            'system_type' => 'required|numeric|in:1,2,3',
            //'classes_id' => 'required|numeric',
            //'add_clock_num' => 'required|numeric',
        ];
    }

    public function getAttendanceMessage(){
	    return [
            'title.required' => '考勤组名称不能为空',
            'system_type.required' => '制度类型不能为空',
            'system_type.numeric' => '制度类型必须为数字',
            'system_type.in' => '制度类型参数错误',
            'classes_id.required' => '班次ID不能为空',
            'classes_id.array' => '班次ID格式错误',
            //'add_clock_num.required' => '补卡次数不能为空',
            //'add_clock_num.numeric' => '补卡次数必须为数字',
            'cycle.required' => '周期不能为空',
            'cycle.numeric' => '周期必须为数字',
            'cycle.in' => '周期选择错误',
            'clock_node.required' => '考勤打卡节点不能为空',
            'clock_node.numeric' => '考勤打卡节点格式错误: H:i:s',
        ];
    }

    public function attendanceValidatorForm($data, $id = 0){

        $rules = $this->getAttendanceRules();
        $messages = $this->getAttendanceMessage();
        //手动验证
        $weeks = '';
        if($data['system_type'] == AttendanceApiService::ATTENDANCE_SYTTEM_FIXED){
            //固定班次必须设定工作日
            if(!isset($data['weeks']) || !$data['weeks']) return returnJson('工作日不能为空!', ConstFile::API_RESPONSE_FAIL);
            $res = $this->checkArrayNum($data['weeks']);
            if(!$res) return returnJson('工作日选择错误', ConstFile::API_RESPONSE_FAIL);
            //固定班次只能设定一个班次
            if(count($data['classes_id']) !== 1) return returnJson('固定班次只能设定一个班次', ConstFile::API_RESPONSE_FAIL);
            //固定班次必须设定班次
            if(!isset($data['classes_id']) || !$data['classes_id']) return returnJson('班次不能为空!', ConstFile::API_RESPONSE_FAIL);
            $res = $this->checkArrayNum($data['classes_id']);
            if(!$res) return returnJson('班次选择错误', ConstFile::API_RESPONSE_FAIL);
        }
        if($data['system_type'] == AttendanceApiService::ATTENDANCE_SYTTEM_SORT){
            //排班制必须设定班次
            if(!isset($data['classes_id']) || !$data['classes_id']) return returnJson('班次ID不能为空!', ConstFile::API_RESPONSE_FAIL);
            $res = $this->checkArrayNum($data['classes_id']);
            if(!$res) return returnJson('班次ID必须为数字', ConstFile::API_RESPONSE_FAIL);
            //排班制必须设定周期
            $rules['classes_id'] = 'required|array';
            $rules['cycle_id'] = 'required|numeric';
        }
        if($data['system_type'] == AttendanceApiService::ATTENDANCE_SYTTEM_FREE){
            //自由制可以不设定工作日
            if(!isset($data['weeks']) || !$data['weeks']) {
                $res = $this->checkArrayNum($data['weeks']);
                if(!$res) return returnJson('工作日选择错误', ConstFile::API_RESPONSE_FAIL);
            }
            //自由之必须设定考勤打卡节点
            $rules['clock_node'] = 'required|date_format:H:i:s';
        }

        $validator = Validator::make($data, $rules, $messages);
        if($validator->fails()){
            return returnJson('参数错误: '. $validator->errors()->first(), ConstFile::API_RESPONSE_FAIL);
        }

        //验证参加考勤部门
        if(isset($data['department_ids'])){

        }

        if(isset($data['staff_true_ids']) && isset($data['staff_false_ids'])){
            $res = array_intersect($data['staff_true_ids'], $data['staff_false_ids']);
            if($res){
                return returnJson('参加考勤人员 和 不参加考勤人员 数据有重复', ConstFile::API_RESPONSE_FAIL);
            }
        }


        //验证参加考勤人员
        if(isset($data['staff_true_ids'])){
            $res = $this->checkArrayNum($data['staff_true_ids']);
            if(!$res) return returnJson('参加考勤人员选择错误', ConstFile::API_RESPONSE_FAIL);

            /*$ids = [];
            foreach ($data['staff_true_ids'] as $k => $v){
                $ids[] = $v;
            }

            if($id == 0){
                $is_ids = AttendanceApiStaff::query()->whereIn('user_id', $ids)->where('is_attendance', ConstFile::ATTENDANCE_STAFF_TRUE)->first();
            }else{
                $is_ids = AttendanceApiStaff::query()->where('attendance_id','!=', $id)->whereIn('user_id', $ids)->where('is_attendance', ConstFile::ATTENDANCE_STAFF_TRUE)->first();
            }
            if(!empty($is_ids)) {
                return returnJson("ID: ". $is_ids['user_id']." ,NAME: ".$is_ids->userInfo['chinese_name'].' 员工已经被其他考勤组设置成参加考勤人员', ConstFile::API_RESPONSE_FAIL);
            }*/
        }

        //验证不参加考勤人员
        if(isset($data['staff_false_ids'])){
            $res = $this->checkArrayNum($data['staff_false_ids']);
            if(!$res) return returnJson('不参加考勤人员选择错误', ConstFile::API_RESPONSE_FAIL);

            /*$ids = [];
            foreach ($data['staff_false_ids'] as $k => $v){
                $ids[] = $v;
            }
            if($id == 0){
                $is_ids = AttendanceApiStaff::query()->whereIn('user_id', $ids)->where('is_attendance', ConstFile::ATTENDANCE_STAFF_FALSE)->first();
            }else{
                $is_ids = AttendanceApiStaff::query()->where('attendance_id','!=', $id)->whereIn('user_id', $ids)->where('is_attendance', ConstFile::ATTENDANCE_STAFF_FALSE)->first();
            }
            if(!empty($is_ids)) {
                return returnJson("ID: ". $is_ids['user_id']." ,NAME: ".$is_ids->userInfo['name'].' 员工已经被其他考勤组设置成不参加考勤人员', ConstFile::API_RESPONSE_FAIL);
            }*/
        }
        return true;
    }

    public function checkArrayNum($data) {
        foreach ($data as $k => $v){
            if(!is_numeric($v)) return false;
        }
        return true;
    }

}
