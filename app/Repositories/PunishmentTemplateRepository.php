<?php

namespace App\Repositories;
use App\Models\AttendanceApi\AttendanceApiScheduling;
use App\Constant\ConstFile;
use App\Models\Contract\Contract;
use App\Models\User;
use App\Repositories\AttendanceApi\CountsRespository;
use App\Services\AttendanceApi\AttendanceApiService;
use Prettus\Repository\Eloquent\BaseRepository as Base;
use App\Models\PunishmentTemplate;
use Auth;
use Request;
use DB;

class PunishmentTemplateRepository extends Base {



    public function model() {
        return PunishmentTemplate::class;
    }
    //加班费添加修改
    public function setOvertimePay($user,$arr)
    {
        $user_id = $user->id;
        $company_id = $user->company_id;
        //type 1 表示一小时  2表示一天
        if(empty($arr['type'])){
            return returnJson($message='加班费计算类型不能为空',$code=ConstFile::API_RESPONSE_FAIL);
        }
        if(empty($arr['money'])){
            return returnJson($message='加班费计算金额不能为空',$code=ConstFile::API_RESPONSE_FAIL);
        }
        if(empty($arr['overtime_type'])){//加班类型1 工作日加班 2休息日加班  3节假日加班
            return returnJson($message='加班类型不能为空',$code=ConstFile::API_RESPONSE_FAIL);
        }
        $tyrps=intval($arr['type']);
        $money=floatval($arr['money']);
        $moneyovertime_type=floatval($arr['overtime_type']);
        if(!empty($arr['id']) && isset($arr['id'])){
            $where['type']=1;
            $where['id']=intval($arr['id']);
            $data['types']=$tyrps;
            $data['money']=$money;
            $data['overtime_type']=$moneyovertime_type;
            $data['company_id']=$company_id;
            $data['user_id']=$user_id;
            $data['updated_at']=date('Y-m-d H:i:s', time());
            PunishmentTemplate::where($where)->update($data);
            return returnJson($message='修改成功',$code=ConstFile::API_RESPONSE_SUCCESS);
        }else{
            $where['type']=1;
            $where['types']=$tyrps;
            $where['overtime_type']=$moneyovertime_type;
            $n =PunishmentTemplate::where($where)->count('id');
            if($n){
                return returnJson($message='数据已存在成功',$code=ConstFile::API_RESPONSE_FAIL);
            }
            $data['type']=1;
            $data['types']=$tyrps;
            $data['money']=$money;
            $data['company_id']=$company_id;
            $data['overtime_type']=$moneyovertime_type;
            $data['user_id']=$user_id;
            $data['created_at']=date('Y-m-d H:i:s', time());
            $data['updated_at']=date('Y-m-d H:i:s', time());
            PunishmentTemplate::insert($data);
            return returnJson($message='添加成功',$code=ConstFile::API_RESPONSE_SUCCESS);
        }
    }

    /**
     * 加班费列表
     */
    public function getOvertimePay($user,$arr)
    {
        $listArr=[];
        $list = PunishmentTemplate::where('type',1)->where('status',1)->orderBy('overtime_type','asc')->get(['id','type','types','overtime_type','money']);
        if($list) {
            $list = $list->toArray();
            foreach ($list as $key => $lists) {
                $overtime_type = $lists['overtime_type'];
                $listArr[$overtime_type] = $lists;
            }
        }
        return returnJson($message='OK',$code=ConstFile::API_RESPONSE_SUCCESS,$listArr);
    }

    /**
     * 加班费详情
     */
    public function getOvertimePayInfo($user,$arr)
    {
        if (empty($arr['id'])){
            return returnJson($message='参数错误请正确传参!',$code=ConstFile::API_RESPONSE_FAIL);
        }
        $list = PunishmentTemplate::where('type',1)->where('id',intval($arr['id']))->first(['id','type','types','overtime_type','money']);
        if($list){
            $list=$list->toArray();
        }else{
            $list=[];
        }
        return returnJson($message='OK',$code=ConstFile::API_RESPONSE_SUCCESS,$list);
    }

    //迟到扣费设置
    public function setLatePay($user,$arr){
        $user_id = $user->id;
        $company_id = $user->company_id;
        //type 1 表示迟到1-10  2表示迟到10-30分钟  3表示迟到30分钟以上
        $count = $arr['count'];
        $type = intval($arr['type']);
        if(empty($arr['type'])){
            return returnJson($message='类型不能为空',$code=ConstFile::API_RESPONSE_FAIL);
        }
        if(intval($arr['type'])==1){
            if(is_array($count)) {
                foreach ($count as $key => $val) {
                    if (empty($val['types'])) {
                        return returnJson($message = '迟到类型不能为空', $code = ConstFile::API_RESPONSE_FAIL);
                    }
                    if ($val['money'] != 0) {
                        if (empty($val['money'])) {
                            return returnJson($message = '迟到扣费金额不能为空', $code = ConstFile::API_RESPONSE_FAIL);
                        }
                    }
                    $tyrps = intval($val['types']);
                    $money = floatval($val['money']);
                    $data['type'] = 2;
                    $data['types'] = $tyrps;
                    $data['money'] = $money;
                    $data['company_id'] = $company_id;
                    $data['user_id'] = $user_id;
                    $data['created_at'] = date('Y-m-d H:i:s', time());
                    $data['updated_at'] = date('Y-m-d H:i:s', time());
                    $dataArr[]=$data;
                }
                $n = PunishmentTemplate::insert($dataArr);
                if($n){
                    return returnJson($message = '添加成功', $code = ConstFile::API_RESPONSE_SUCCESS,$n);
                }
                return returnJson($message = '添加失败', $code = ConstFile::API_RESPONSE_FAIL);
            }else{
                return returnJson($message = '参数结构错误', $code = ConstFile::API_RESPONSE_FAIL);
            }
        }else{
            if(is_array($count)){
                foreach ($count as $key =>$val){
                    if(empty($val['types'])){
                        return returnJson($message='迟到类型不能为空',$code=ConstFile::API_RESPONSE_FAIL);
                    }
                    if($val['money']!=0){
                        if(empty($val['money'])){
                            return returnJson($message='迟到扣费金额不能为空',$code=ConstFile::API_RESPONSE_FAIL);
                        }
                    }
                    $tyrps=intval($val['types']);
                    $money=floatval($val['money']);
                    if(!empty($val['id']) && isset($val['id'])){
                        $where['type']=2;
                        $where['id']=intval($val['id']);
                        $data['types']=$tyrps;
                        $data['money']=$money;
                        $data['company_id']=$company_id;
                        $data['user_id']=$user_id;
                        $data['updated_at']=date('Y-m-d H:i:s', time());
                        $n = PunishmentTemplate::where($where)->update($data);
                    }
                }
                return returnJson($message='修改成功',$code=ConstFile::API_RESPONSE_SUCCESS);
            }else{
                return returnJson($message='参数结构错误',$code=ConstFile::API_RESPONSE_FAIL);
            }
        }
    }


    /**
     * 迟到扣钱列表
     */
    public function getLatePayList($user,$arr)
    {
       $list = PunishmentTemplate::where('type',2)->orderBy('types','asc')->get(['id','type','types','money']);
       if($list){
           $list = $list->toArray();

       }else{
           $list =[];
       }
        return returnJson($message='OK',$code=ConstFile::API_RESPONSE_SUCCESS,$list);
    }

    /**
     * 旷工扣钱列表
     */
    public function setAbsenteeism($user,$arr)
    {
        $user_id = $user->id;
        $company_id = $user->company_id;
        //type 1 表示旷工1天  2表示迟旷工2天
        if(empty($arr['count']) && !is_array($arr['count'])){
            return returnJson($message='参数错误请正确访问',$code=ConstFile::API_RESPONSE_FAIL);
        }
        if(empty($arr['type'])){
            return returnJson($message='类型数据错误',$code=ConstFile::API_RESPONSE_FAIL);
        }
        $type = intval($arr['type']);
        if($type == 1){
            $count = $arr['count'];
            foreach ($count as $key=>$value){
                if(empty($value['types'])){
                    return returnJson($message='旷工天数',$code=ConstFile::API_RESPONSE_FAIL);
                }
                if(empty($value['money'])){
                    return returnJson($message='旷工扣费数据不能为空!',$code=ConstFile::API_RESPONSE_FAIL);
                }
                $data['type']=3;
                $data['types']=intval($value['types']);
                $data['money']=floatval($value['money']);
                $data['company_id']=$company_id;
                $data['user_id']=$user_id;
                $data['created_at']=date('Y-m-d H:i:s', time());
                $data['updated_at']=date('Y-m-d H:i:s', time());
                $dataArr[]=$data;
            }
            $n = PunishmentTemplate::insert($dataArr);
            if($n){
                return returnJson($message='添加成功',$code=ConstFile::API_RESPONSE_SUCCESS);
            }
            return returnJson($message='添加失败',$code=ConstFile::API_RESPONSE_FAIL);
        }else{
            $count = $arr['count'];
            foreach ($count as $key=>$value) {
                if (empty($value['types'])) {
                    return returnJson($message = '旷工天数', $code = ConstFile::API_RESPONSE_FAIL);
                }
                if (empty($value['money'])) {
                    return returnJson($message = '旷工扣费数据不能为空!', $code = ConstFile::API_RESPONSE_FAIL);
                }
                $where['id'] = intval($value['id']);
                $where['type'] = 3;
                $data['types'] = intval($value['types']);
                $data['money'] = floatval($value['money']);
                $data['company_id'] = $company_id;
                $data['user_id'] = $user_id;
                $data['created_at'] = date('Y-m-d H:i:s', time());
                $data['updated_at'] = date('Y-m-d H:i:s', time());
                $n = PunishmentTemplate::where($where)->update($data);
            }
            if($n){
                return returnJson($message='修改成功',$code=ConstFile::API_RESPONSE_SUCCESS);
            }
            return returnJson($message='修改失败',$code=ConstFile::API_RESPONSE_FAIL);
        }
    }
    /**
     * 旷工扣钱列表
     */
    public function getAbsenteeismList($user,$arr){
        $list = PunishmentTemplate::where('type',3)->orderBy('types','asc')->get(['id','type','types','money']);
        if($list){
            $list = $list->toArray();
        }else{
            $list =[];
        }
        //$list=$this->punishmentCalculation();
        return returnJson($message='OK',$code=ConstFile::API_RESPONSE_SUCCESS,$list);
    }

    /**
     * 旷工扣钱列表
     */
    public function setLeave($user,$arr)
    {
        $user_id = $user->id;
        $company_id = $user->company_id;
        //type 1 表示事假  2表示病假
        if(empty($arr['count']) && !is_array($arr['count'])){
            return returnJson($message='参数错误请正确访问',$code=ConstFile::API_RESPONSE_FAIL);
        }
        if(empty($arr['type'])){
            return returnJson($message='类型数据错误',$code=ConstFile::API_RESPONSE_FAIL);
        }
        $type = intval($arr['type']);
        if($type == 1){
            $count = $arr['count'];
            foreach ($count as $key=>$value){
                if(empty($value['types'])){
                    return returnJson($message='请假类型不能为空!',$code=ConstFile::API_RESPONSE_FAIL);
                }
                if(empty($value['money'])){
                    return returnJson($message='请假扣钱数据不能为空!',$code=ConstFile::API_RESPONSE_FAIL);
                }
                $data['type']=4;
                $data['types']=intval($value['types']);
                $data['money']=floatval($value['money']);
                $data['company_id']=$company_id;
                $data['user_id']=$user_id;
                $data['created_at']=date('Y-m-d H:i:s', time());
                $data['updated_at']=date('Y-m-d H:i:s', time());
                $dataArr[]=$data;
            }
            $n = PunishmentTemplate::insert($dataArr);
            if($n){

                return returnJson($message='添加成功',$code='20');
            }
            return returnJson($message='添加失败',$code='20');
        }else{
            $count = $arr['count'];
            foreach ($count as $key=>$value) {
                if (empty($value['types'])) {
                    return returnJson($message='请假类型不能为空!',$code=ConstFile::API_RESPONSE_FAIL);
                }
                if (empty($value['money'])) {
                    return returnJson($message='请假扣钱数据不能为空!',$code=ConstFile::API_RESPONSE_FAIL);
                }
                $where['id'] = intval($value['id']);
                $where['type'] = 4;
                $data['types'] = intval($value['types']);
                $data['money'] = floatval($value['money']);
                $data['company_id'] = $company_id;
                $data['user_id'] = $user_id;
                $data['created_at'] = date('Y-m-d H:i:s', time());
                $data['updated_at'] = date('Y-m-d H:i:s', time());
                $n = PunishmentTemplate::where($where)->update($data);
            }
            return returnJson($message='修改成功',$code=ConstFile::API_RESPONSE_SUCCESS);
        }
        return returnJson($message='修改成功',$code=ConstFile::API_RESPONSE_SUCCESS);
    }
    /**
     * 旷工扣钱列表
     */
    public function setLeaveList($user,$arr){
        $list = PunishmentTemplate::where('type',4)->orderBy('types','asc')->get(['id','type','types','money']);
        if($list){
            $list = $list->toArray();
        }else{
            $list =[];
        }
        return returnJson($message='OK',$code=ConstFile::API_RESPONSE_SUCCESS,$list);
    }
    //惩罚规则模板计算惩罚金额    病假   事假 （计算扣除薪资）
    public function punishmentCalculation($uid=1791, $dates = '2019-05'){
        $user = User::find($uid);
        //获取考勤数据
        $res = app()->make(CountsRespository::class)->getOneMonthForHr($user, ['dates'=> $dates]);
        $res = json_decode($res->getContent(), true)['data'];
        $rules=$res['rules'];
        $t=$res['t'];
        $leave_of_absence=$res['leave_of_absence'];//事假 leave_of_absence
        $leave_sick_leave=$res['leave_sick_leave'];//病假 leave_sick_leave
        $absenteeism=count($res['absenteeism']);   //旷工 absenteeism
        $late=$res['late'];                         //迟到 late
        $leave=$res['leave'];                       //早退 leave
        $overtime=$res['overtime'];                 //加班数据

        //$money=1000;//总薪资
        if(Q($rules,'attendance','system_type') == AttendanceApiService::ATTENDANCE_SYTTEM_SORT){
            $day = count(AttendanceApiScheduling::getSchedulingList($user->id, $t['month_start'], $t['month_end']));
        }else{
            $day=21.75;//上班天数
        }
        //日薪资计算  获取总薪资
        $salaryInfo =  Contract::where('user_id',$uid)->first(['salary','probation_ratio']);

        if($user->is_wage==2){
            $money=$salaryInfo->salary;
        }else{
            $money=round(($salaryInfo->salary*$salaryInfo->probation_ratio)/100,2);
        }
        $daysalary=round($money/$day,2);//日薪资
        $absenteeism=count($res['absenteeism']);//旷工 absenteeism
        if($absenteeism==1){
            $money = PunishmentTemplate::where('type',3)->where('types',1)->value('money');
            $absenteeism_money=$money* $daysalary; //旷工一次
        }else{
            $money = PunishmentTemplate::where('type',3)->where('types',2)->value('money');
            $absenteeism_money=$money* $daysalary*$absenteeism; //旷工2次级以上
        }

        $list['absenteeism_number']=$absenteeism;//旷工次数
        $list['absenteeism_money']=$absenteeism_money;//旷工扣费

        $overtime_money=0;
        if($overtime){//加班
            foreach ($overtime as $key=>$va){
                $PunishmentTemplate = PunishmentTemplate::where('type',1)->where('overtime_type',$va['overtime_date_type'])->first(['money','types'])->toArray();
                if($PunishmentTemplate['types']==1){//小时结束
                    $daysalarys = round($daysalary/8,2);//计算小时薪资
                    $overtime_money = $overtime_money+($daysalarys*$va['anomaly_time']*$PunishmentTemplate['money']);
                }else{//天结算
                    $dayss =  round($va['anomaly_time']/8,2);//小时换算成天
                    $overtime_money = $overtime_money+($daysalary* $dayss * $PunishmentTemplate['money']);
                }
            }
        }
        $list['overtime_number']=$res['overtime_nums'];//加班时间
        $list['overtime_money']= round($overtime_money,2);//加班金额
        //dd($late);
        //迟到
        $late_money=0;
        if($late){
           foreach($late as $key=>$val){
                if($val['anomaly_time']>30){
                    $PunishmentTemplate = PunishmentTemplate::where('type',2)->where('types',3)->value('money');
                }elseif($val['anomaly_time']>10){
                    $PunishmentTemplate = PunishmentTemplate::where('type',2)->where('types',2)->value('money');
                }else{
                    $PunishmentTemplate = PunishmentTemplate::where('type',2)->where('types',1)->value('money');
                }
                if($PunishmentTemplate==1){
                    $late_money=$late_money+$daysalary;
                }else{
                    $late_money=$late_money+$PunishmentTemplate;
                }
           }
        }

        //早退
        $leave_money=0;
        if($leave){
            foreach($leave as $key=>$val){
                if($val['anomaly_time']>30){
                    $PunishmentTemplate = PunishmentTemplate::where('type',2)->where('types',3)->value('money');
                }elseif($val['anomaly_time']>10){
                    $PunishmentTemplate = PunishmentTemplate::where('type',2)->where('types',2)->value('money');
                }else{
                    $PunishmentTemplate = PunishmentTemplate::where('type',2)->where('types',1)->value('money');
                }
                if($PunishmentTemplate==1){
                    $leave_money=$leave_money+$daysalary;
                }else{
                    $leave_money= round($leave_money+$PunishmentTemplate,2);
                }
            }
        }
        //迟到早退参数加一起
        $list['leave_number']=count($leave) + count($late);//迟到早退数据
        $list['leave_money']=$leave_money + $late_money;//迟到早退扣费

        //病假
        $leave_sick_leave_number = count($leave_sick_leave);
        $leave_sick_leave_money=0;
        if($leave_sick_leave){
            $PunishmentTemplate = PunishmentTemplate::where('type',4)->where('types',2)->value('money');//病假
            foreach ($leave_sick_leave as $key =>$val){
                if($PunishmentTemplate<=1){
                    $leave_sick_leave_money=$leave_sick_leave_money+($daysalary*$PunishmentTemplate);
                }else{
                    $leave_sick_leave_money=$leave_sick_leave_money+$PunishmentTemplate;
                }
            }
        }
        $list['leave_sick_leave_number']=$leave_sick_leave_number;
        $list['leave_sick_leave_money']= round($leave_sick_leave_money,2);

        //事假
        $leave_of_absence_number = count($leave_of_absence);
        $leave_of_absence_money=0;
        if($leave_of_absence){
            $PunishmentTemplate = PunishmentTemplate::where('type',4)->where('types',1)->value('money');//病假
            foreach ($leave_of_absence as $key =>$val){
                if($PunishmentTemplate<=1){
                    $leave_of_absence_money=$leave_of_absence_money+($daysalary*$PunishmentTemplate);
                }else{
                    $leave_of_absence_money=$leave_of_absence_money+$PunishmentTemplate;
                }
            }
        }
        $list['leave_of_absence_number']=$leave_of_absence_number;
        $list['leave_of_absence_money']= round($leave_of_absence_money,2);
        return $list;
    }

}

