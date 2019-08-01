<?php

namespace App\Repositories;

use App\Constant\ConstFile;

use App\Models\Vacations\CompanyLeaveUnit;
use App\Repositories\Repository;
use App\Models\Vacations\Vacations;
use App\Repositories\UsersRepository;
use Exception;
use Request;
use DB;
use Auth;

class VacationsRepository extends Repository {

    public function model() {
        return Vacations::class;
    }

    //录入员工剩余假期时长
    public function adduservacations(){
        $datas = Request::all();

        if(!isset($datas['user_id']) || empty($datas['user_id'])){
            $result['code'] = -1;
            $result['message'] = "员工ID不能为空";
            return $result;
        }

        $count = Vacations::where('user_id',$datas['user_id'])->count();
        if(!empty($count)){
            $result['code'] = -1;
            $result['message'] = "已录入该人员的假期汇总信息";
            return $result;
        }

        if(empty($datas['ndays'])){
            $result['code'] = -1;
            $result['message'] = "年假时长不能为空";
            return $result;
        }

        if(empty($datas['txdays'])){
            $result['code'] = -1;
            $result['message'] = "调休时长不能为空";
            return $result;
        }

        if(empty($datas['cdays'])){
            $result['code'] = -1;
            $result['message'] = "产假时长不能为空";
            return $result;
        }

        if(empty($datas['pcdays'])){
            $result['code'] = -1;
            $result['message'] = "陪产假时长不能为空";
            return $result;
        }

        if(empty($datas['hdays'])){
            $result['code'] = -1;
            $result['message'] = "婚假时长不能为空";
            return $result;
        }

        if(empty($datas['ldays'])){
            $result['code'] = -1;
            $result['message'] = "例假时长不能为空";
            return $result;
        }

        if(empty($datas['sdays'])){
            $result['code'] = -1;
            $result['message'] = "丧假时长不能为空";
            return $result;
        }

        if(empty($datas['brdays'])){
            $result['code'] = -1;
            $result['message'] = "哺乳假时长不能为空";
            return $result;
        }

        $datas['created_at'] = date("Y-m-d H:i:s");
        $datas['updated_at'] = date("Y-m-d H:i:s");

        $leave = DB::table('vacations')->insert($datas);

        if($leave){
            $result['code'] = 200;
            $result['message'] = "操作成功";
        }else{
            $result['code'] = -1;
            $result['message'] = "操作失败";
        }
        return $result;
    }

    //获取我的各种假期时长
    public function getmyleave($user_id){

        $count = db::table('vacations')->where('user_id',$user_id)->count();

        if(empty($count)){
            $result['code'] = -1;
            $result['message'] = "待获取的相关信息不存在，请检查！";
            return $result;
        }

        $leavelist = db::table('vacations')->where('user_id',$user_id)->get();

        if($leavelist)
        {
            return returnJson($message='ok',$code = 200,$data=$leavelist);
        }else{
            $result['code'] = -1;
            $result['message'] = "操作失败";
            return $result;
        }
    }

    //根据公司id获取请假模板字段
    public function gettembycomid($arr){

    $c_id = $arr['c_id'];

    if(!isset($c_id) || empty($c_id)){
        $result['code'] = -1;
        $result['message'] = "公司id不能为空";
        return $result;
    }

    //获取该公司请假模板字段

    $tid  =  DB::table('addwork_company')
            ->leftJoin('addwork_field', 'addwork_company.field_id', '=', 'addwork_field.id')
        ->where('addwork_company.company_id', '=', $c_id)
        ->where('addwork_field.type','<>','def')
        ->where('addwork_field.status','4')
        ->get(['addwork_field.id','addwork_field.type','addwork_field.name','addwork_field.e_name']);

    $l_id = DB::table('company_leave_unit')
        ->leftJoin('vacation_type','company_leave_unit.l_id','=','vacation_type.id')
        ->leftJoin('leave_unit','company_leave_unit.n_id','=','leave_unit.id')
        ->where('company_leave_unit.c_id','=',$c_id)
        ->get(['vacation_type.id','vacation_type.vacname','leave_unit.uname']);

    $tid[2]->vacname = $l_id;
    $arr['template'] =  $tid;

    if($arr)
    {
        return returnJson($message='ok',$code = 200,$data=$arr);
    }else{
        $result['code'] = -1;
        $result['message'] = "操作失败";
        return $result;
    }

    }

    //根据假期类型获取员工的该假期剩余时长
    public function getusertimes($user_id){

        $datas = Request::all();
        if(!isset($datas['v_id']) || empty($datas['v_id'])){
            $result['code'] = -1;
            $result['message'] = "请选择假期";
            return $result;
        }

        $count = DB::table('vacations')->where('user_id',$user_id)->count();
        if(empty($count)){
            $result['code'] = -1;
            $result['message'] = "未获取到相关信息，请检查";
            return $result;
        }

        switch ($datas['v_id'])
        {
            case 1:
                $leave_type_times = db::table('vacations')->where('user_id',$user_id)->get(['ndays']);
                break;

            case 2:
                $leave_type_times = [0];
                break;

            case 3:
                $leave_type_times = [0];
                break;

            case 4:
                $leave_type_times = db::table('vacations')->where('user_id',$user_id)->get(['txdays']);
                break;

            case 5:
                $leave_type_times = db::table('vacations')->where('user_id',$user_id)->get(['cdays']);
                break;

            case 6:
                $leave_type_times = db::table('vacations')->where('user_id',$user_id)->get(['pcdays']);
                break;

            case 7:
                $leave_type_times = db::table('vacations')->where('user_id',$user_id)->get(['hdays']);
                break;

            case 8:
                $leave_type_times = db::table('vacations')->where('user_id',$user_id)->get(['ldays']);
                break;

            case 9:
                $leave_type_times = db::table('vacations')->where('user_id',$user_id)->get(['sdays']);
                break;

            case 10:
                $leave_type_times = db::table('vacations')->where('user_id',$user_id)->get(['brdays']);
                break;
        }

        if($leave_type_times)
        {
            return returnJson($message='ok',$code = 200,$data=$leave_type_times);
        }else{
            $result['code'] = -1;
            $result['message'] = "操作失败";
            return $result;
        }


    }

    //请假
    public function absence($arr){
        DB::beginTransaction();
        try{
            //开启事务

            $datas = Request::all();

            if(!isset($arr['user_id']) || empty($arr['user_id'])){
                $result['code'] = -1;
                $result['message'] = "申请人员ID不能为空";
                return $result;
            }

            if(!isset($datas['stime']) || empty($datas['stime'])){
                $result['code'] = -1;
                $result['message'] = "假期开始时间不能为空";
                return $result;
            }

            if(strtotime($datas['stime']) < time()){
                $result['code'] = -1;
                $result['message'] = "假期开始时间应选择当前时间以后的时间";
                return $result;
            }

            if(!isset($datas['etime']) || empty($datas['etime'])){
                $result['code'] = -1;
                $result['message'] = "假期截止时间不能为空";
                return $result;
            }

            if(strtotime($datas['stime']) >= strtotime($datas['etime'])){
                $result['code'] = -1;
                $result['message'] = "假期开始时间应小于假期截止时间";
                return $result;
            }

            //获取该员工上次申请假期开始时间和假期截止时间
            $last_leave_stime = db::table('leave')->where('user_id',$arr['user_id'])->orderBy('id','desc')->value('stime');

            $last_leave_etime = db::table('leave')->where('user_id',$arr['user_id'])->orderBy('id','desc')->value('etime');


            if(!empty($last_leave_stime) && !empty($last_leave_etime)){
                $last_le_stime = strtotime($last_leave_stime);//上次请假开始时间（时间戳）
                $last_le_etime = strtotime($last_leave_etime);//上次请假截止时间（时间戳）

                if(strtotime($datas['stime']) >= $last_le_stime && strtotime($datas['stime']) <= $last_le_etime){
                    $result['code'] = -1;
                    $result['message'] = "本次请假时间和上次请假时间冲突";
                    return $result;
                }

                if(strtotime($datas['etime']) >= $last_le_stime && strtotime($datas['etime']) <= $last_le_etime){
                    $result['code'] = -1;
                    $result['message'] = "本次请假时间和上次请假时间冲突";
                    return $result;
                }

                if(strtotime($datas['etime']) >= $last_le_stime && strtotime($datas['stime']) <= $last_le_stime){
                    $result['code'] = -1;
                    $result['message'] = "本次请假时间和上次请假时间冲突";
                    return $result;
                }

            }

            if(!isset($datas['times']) || empty($datas['times'])){
                $result['code'] = -1;
                $result['message'] = "请假时长不能为空！";
                return $result;
            }

            if(!preg_match("/^[1-9][0-9]*$/",$datas['times'])){
                $result['code'] = -1;
                $result['message'] = "请假时长格式为正整数！";
                return $result;
            }

            if(!isset($datas['v_id']) || empty($datas['v_id'])){
                $result['code'] = -1;
                $result['message'] = "请假类型不能为空！";
                return $result;
            }

            //判断选择的请假类型是否在该公司请假类型表里面
            $vcount = db::table('company_leave_unit')->where('l_id',$datas['v_id'])->where('c_id',$arr['c_id'])->count();
            if(empty($vcount)){
                $result['code'] = -1;
                $result['message'] = "找不到该请假类型！";
                return $result;
            }

            //根据公司id和假期类型id和类型id获取最小请假单位id
            $n_id = db::table('company_leave_unit')
                ->where('c_id',$arr['c_id'])
                ->where('l_id',$datas['v_id'])
                ->where('type',1)
                ->value('n_id');

            switch ($n_id)
            {
                case 3:
                    $datas['times'] = $datas['times']*8;
                    break;

                case 2:
                    $datas['times'] = $datas['times']*4;
                    break;

            }

            switch ($datas['v_id'])
            {
                case 1:
                    $ndays = db::table('vacations')->where('user_id', $arr['user_id'])->value('ndays');
                    if($datas['times'] > $ndays){
                        $result['code'] = -1;
                        $result['message'] = "你的年假余额不足！";
                        return $result;
                    }
                    break;

                case 4:
                    $ndays = db::table('vacations')->where('user_id', $arr['user_id'])->value('txdays');
                    if($datas['times'] > $ndays){
                        $result['code'] = -1;
                        $result['message'] = "你的调休余额不足！";
                        return $result;
                    }
                    break;

                case 5:
                    $ndays = db::table('vacations')->where('user_id', $arr['user_id'])->value('cdays');
                    if($datas['times'] > $ndays){
                        $result['code'] = -1;
                        $result['message'] = "你的产假余额不足！";
                        return $result;
                    }
                    break;

                case 6:
                    $ndays = db::table('vacations')->where('user_id', $arr['user_id'])->value('pcdays');
                    if($datas['times'] > $ndays){
                        $result['code'] = -1;
                        $result['message'] = "你的陪产假余额不足！";
                        return $result;
                    }
                    break;

                case 7:
                    $ndays = db::table('vacations')->where('user_id', $arr['user_id'])->value('hdays');
                    if($datas['times'] > $ndays){
                        $result['code'] = -1;
                        $result['message'] = "你的婚假余额不足！";
                        return $result;
                    }
                    break;

                case 8:
                    $ndays = db::table('vacations')->where('user_id', $arr['user_id'])->value('ldays');
                    if($datas['times'] > $ndays){
                        $result['code'] = -1;
                        $result['message'] = "你的例假余额不足！";
                        return $result;
                    }
                    break;

                case 9:
                    $ndays = db::table('vacations')->where('user_id', $arr['user_id'])->value('sdays');
                    if($datas['times'] > $ndays){
                        $result['code'] = -1;
                        $result['message'] = "你的丧假余额不足！";
                        return $result;
                    }
                    break;

                case 10:
                    $ndays = db::table('vacations')->where('user_id', $arr['user_id'])->value('brdays');
                    if($datas['times'] > $ndays){
                        $result['code'] = -1;
                        $result['message'] = "你的哺乳假余额不足！";
                        return $result;
                    }
                    break;
            }

            if(!isset($datas['message']) || empty($datas['message'])){
                $result['code'] = -1;
                $result['message'] = "请假事由不能为空！";
                return $result;
            }

            $position = db::table('users')->where('id',$arr['user_id'])->value('position');

            $datas['user_id'] = $arr['user_id'];
            $datas['bm_id'] = $arr['bm_id'];
            $datas['c_id'] = $arr['c_id'];
            $datas['sqtime']= date('Y-m-d h:i:s', time());
            $datas['job'] = $position;
            $datas['leavenum'] = '201903310151000000457';//请假单号，先写死
            $datas['anum'] = '201903310151000000458';//审批单号，先写死

            $lastid = DB::table('leave')->insertGetId($datas);//获取插入请假表的最后一条id

            //模拟审批、抄送人
            $exam_arr = [1,2];//第一个数字审批人id，第二个数字审批人状态
            $copy_arr = [3,4];//第一个数字抄送人id，第二个数字抄送人状态

            foreach ($exam_arr as $v){
                DB::table('examined_copy')->insert(['l_id'=>$lastid,'type'=>1,'user_id'=>$v]);
            }

            foreach ($copy_arr as $ve){
                DB::table('examined_copy')->insert(['l_id'=>$lastid,'type'=>2,'user_id'=>$ve]);
            }



            if($lastid)
            {
                $result['code'] = 200;
                $result['message'] = "操作成功";
            }else{
                $result['code'] = -1;
                $result['message'] = "操作失败";
            }

            DB::commit();//关闭事务
            return $result;

        }catch (Exception $e) {
            DB::rollBack();//回滚事务
        }

    }

    //请假申请记录列表
    public function leastatuslist($user_id){
        $datas = Request::all();

        //历史记录
        if(!empty($datas['user_id'])){
            $BeginDate=date('Y-m-01 00:00', strtotime(date("Y-m-d")));

            $EndDate = date('Y-m-d 23:59', strtotime("$BeginDate +1 month -1 day"));

            $leavelist = DB::table('leave')
                ->Join('vacation_type', 'vacation_type.id', '=', 'leave.v_id')
                ->where('user_id',$datas['user_id']) //查找条件（这个人的）
                ->where('leave.sqtime','>',$BeginDate)
                ->where('leave.sqtime','<',$EndDate)
                ->get(['leave.id','leave.anum','leave.sqtime','leave.job','leave.stime','leave.etime','leave.times','vacation_type.vacname','leave.message','leave.image','leave.leavenum','leave.phone','leave.phone','leave.tel','leave.address','leave.status']);

            $leavelists = DB::table('leave')
                ->Join('vacation_type', 'vacation_type.id', '=', 'leave.v_id')
                ->where('user_id',$datas['user_id']) //查找条件（这个人的）
                ->where('leave.sqtime','<',$BeginDate)
                ->get(['leave.id','leave.anum','leave.sqtime','leave.job','leave.stime','leave.etime','leave.times','vacation_type.vacname','leave.message','leave.image','leave.leavenum','leave.phone','leave.phone','leave.tel','leave.address','leave.status']);

            $profile = db::table('users')->where('id',$datas['user_id'])->get(['name','avatar']);

            $list['user_info'][] = $profile[0]->name;
            $list['user_info'][] = $profile[0]->avatar;

            $list['ymonth'] = $leavelist;
            $list['nmonth'] = $leavelists;

            if($list)
            {
                return returnJson($message='ok',$code = 200,$data=$list);
            }else{
                $result['code'] = -1;
                $result['message'] = "获取失败";
                return $result;
            }

        }elseif (!empty($datas['type']) && $datas['type'] == 1){   //个人视角

            $BeginDate=date('Y-m-01 00:00', strtotime(date("Y-m-d")));

            $EndDate = date('Y-m-d 23:59', strtotime("$BeginDate +1 month -1 day"));

            $leavelist = DB::table('leave')
                ->Join('vacation_type', 'vacation_type.id', '=', 'leave.v_id')
                ->where('user_id',$user_id) //查找条件（这个人的）
                ->where('leave.sqtime','>',$BeginDate)
                ->where('leave.sqtime','<',$EndDate)
                ->get(['leave.id','leave.anum','leave.sqtime','leave.job','leave.stime','leave.etime','leave.times','vacation_type.vacname','leave.message','leave.image','leave.leavenum','leave.phone','leave.phone','leave.tel','leave.address','leave.status']);

            $leavelists = DB::table('leave')
                ->Join('vacation_type', 'vacation_type.id', '=', 'leave.v_id')
                ->where('user_id',$user_id) //查找条件（这个人的）
                ->where('leave.sqtime','<',$BeginDate)
                ->get(['leave.id','leave.anum','leave.sqtime','leave.job','leave.stime','leave.etime','leave.times','vacation_type.vacname','leave.message','leave.image','leave.leavenum','leave.phone','leave.phone','leave.tel','leave.address','leave.status']);

            $profile = db::table('users')->where('id',$user_id)->get(['name','avatar']);

            if(!empty($profile)){
                $list['user_info'][] = $profile[0]->name;
                $list['user_info'][] = $profile[0]->avatar;

                $list['ymonth'] = $leavelist;
                $list['nmonth'] = $leavelists;

                return returnJson($message='ok',$code = 200,$data=$list);

            }else{

                $result['code'] = -1;
                $result['message'] = "获取失败";
                return $result;

            }


        }elseif (!empty($datas['type']) && $datas['type'] == 2){   //人事视角

            $tocount = db::table('total_audit')->where('uid',$user_id)->where('type',3)->count();
            if(empty($tocount)){
                $result['code'] = -1;
                $result['message'] = "未获取到和你相关的信息！";
                return $result;
            }

            //获取审批人关联的请假id
            $total_leave_id = db::table('total_audit')->where('uid',$user_id)->where('type',3)->distinct('relation_id')->pluck('relation_id');
            //return $total_leave_id;

            $BeginDate=date('Y-m-01 00:00', strtotime(date("Y-m-d")));

            $EndDate = date('Y-m-d 23:59', strtotime("$BeginDate +1 month -1 day"));

            $leavelist = DB::table('leave')
                ->Join('vacation_type', 'vacation_type.id', '=', 'leave.v_id')
                ->whereIn('leave.id',$total_leave_id) //查找条件（这个人的）
                ->where('leave.sqtime','>',$BeginDate)
                ->where('leave.sqtime','<',$EndDate)
                ->get(['leave.id','leave.anum','leave.sqtime','leave.job','leave.stime','leave.etime','leave.times','vacation_type.vacname','leave.message','leave.image','leave.leavenum','leave.phone','leave.phone','leave.tel','leave.address','leave.status']);

            $leavelists = DB::table('leave')
                ->Join('vacation_type', 'vacation_type.id', '=', 'leave.v_id')
                ->whereIn('leave.id',$total_leave_id) //查找条件（这个人的）
                ->where('leave.sqtime','<',$BeginDate)
                ->get(['leave.id','leave.anum','leave.sqtime','leave.job','leave.stime','leave.etime','leave.times','vacation_type.vacname','leave.message','leave.image','leave.leavenum','leave.phone','leave.phone','leave.tel','leave.address','leave.status']);

            $user_leave_id = db::table('leave')->whereIn('id',$total_leave_id)->value('user_id');
            $profile = db::table('users')->where('id',$user_leave_id)->get(['name','avatar']);

            $list['user_info'][] = $profile[0]->name;
            $list['user_info'][] = $profile[0]->avatar;

            $list['ymonth'] = $leavelist;
            $list['nmonth'] = $leavelists;

            if($list)
            {
                return returnJson($message='ok',$code = 200,$data=$list);
            }else{
                $result['code'] = -1;
                $result['message'] = "获取失败";
                return $result;
            }

        }else{

            $result['code'] = -1;
            $result['message'] = "获取失败";
            return $result;

        }

    }


    //请假单详情页
    public function leave_detail(){
        $datas = Request::all();

        if(!isset($datas['id']) || empty($datas['id'])){
            $result['code'] = -1;
            $result['message'] = "待查看信息id不存在！";
            return $result;
        }

        $lcount = db::table('leave')->where('id',$datas['id'])->count();
        if(empty($lcount)){
            $result['code'] = -1;
            $result['message'] = "待查看信息不存在！";
            return $result;
        }

        $leave_detail = db::table('leave')
            ->Join('users', 'users.id', '=', 'leave.user_id')
//            ->Join('department', 'department.id', '=', 'leave.bm_id')
            ->where('leave.id',$datas['id'])
            ->get(['leave.id','users.name','users.avatar','leave.anum','users.position','leave.stime','leave.etime','leave.times','leave.message',]);

        $total_audit = DB::table('total_audit')
            ->leftJoin('users', 'total_audit.uid', '=', 'users.id')
            ->where('total_audit.relation_id',$datas['id'])
            ->where('total_audit.type',3)
            ->get(['total_audit.id','total_audit.is_success','total_audit.status','users.name','users.avatar']);

        $exam_copy = DB::table('examined_copy')
            ->leftJoin('users', 'examined_copy.user_id', '=', 'users.id')
            ->where('examined_copy.l_id',$datas['id'])
            ->where('examined_copy.type',2)
            ->get(['examined_copy.id','users.name','users.avatar']);

        $leave_detail[0]->total_audit = $total_audit;

        $leave_detail[0]->exam_copy = $exam_copy;

        if($leave_detail)
        {
            return returnJson($message='ok',$code = 200,$data=$leave_detail);
        }else{
            $result['code'] = -1;
            $result['message'] = "获取失败";
            return $result;
        }

    }


    //请假审批操作
    public function leaappoper($user_id){
        DB::beginTransaction();

        try{

            $datas = Request::all();

            if(!isset($datas['id']) || empty($datas['id'])){
                $result['code'] = -1;
                $result['message'] = "待审批信息id不存在！";
                return $result;
            }

            $lcount = db::table('leave')->where('id',$datas['id'])->where('status',0)->count();
            if(empty($lcount)){
                $result['code'] = -1;
                $result['message'] = "待审批信息不存在！";
                return $result;
            }

            if(!isset($datas['type']) || empty($datas['type'])){
                $result['code'] = -1;
                $result['message'] = "审批状态不存在！";
                return $result;
            }

            $v_id = db::table('leave')->where('id',$datas['id'])->where('status',0)->value('v_id');
//            $u_id = db::table('leave')->where('id',$datas['id'])->where('status',0)->value('user_id');
            $times = db::table('leave')->where('id',$datas['id'])->where('status',0)->value('times');

            //查询最后一个审批人id
            $exam_id = db::table('examined_copy')->where('type',1)->where('l_id',$datas['id'])->orderBy('id','desc')->value('user_id');

            //获取审批人姓名
            $exam_name = db::table('users')->where('id',$user_id)->value('name');

            //获取当前时间
            $audit_time = date('Y-m-d h:i:s', time());

            //获取创建者id
            $create_id = db::table('leave')->where('id',$datas['id'])->where('status',0)->value('user_id');

            switch ($v_id)
            {
                case 1:
                    $ndays = db::table('vacations')->where('user_id', $create_id)->value('ndays');

                    if($exam_id == $user_id){


                        if($datas['type'] == 1){

                            $a = db::table('leave')
                                ->where('id', $datas['id'])
                                ->update(['status' => 1]);

                            $b = db::table('vacations')
                                ->where('user_id', $create_id)
                                ->update(['ndays' => $ndays-$times]);

                            $c = db::table('total_audit')->insert(['type' =>3,'relation_id'=>$datas['id'],'uid'=>$user_id,'user_name'=>$exam_name,'status'=>1,'audit_time'=>$audit_time,'create_user_id'=>$create_id,'is_success'=>1]);

                        }else{

                            $a = db::table('leave')
                                ->where('id', $datas['id'])
                                ->update(['status' => 2]);

                            $c = db::table('total_audit')->insert(['type' =>3,'relation_id'=>$datas['id'],'uid'=>$user_id,'user_name'=>$exam_name,'status'=>-1,'audit_time'=>$audit_time,'create_user_id'=>$create_id,'is_success'=>1]);

                        }


                    }else{

                        if($datas['type'] == 1){
                            $c = db::table('total_audit')->insert(['type' =>3,'relation_id'=>$datas['id'],'uid'=>$user_id,'user_name'=>$exam_name,'status'=>1,'audit_time'=>$audit_time,'create_user_id'=>$create_id,'is_success'=>0]);
                        }else{
                            $c = db::table('total_audit')->insert(['type' =>3,'relation_id'=>$datas['id'],'uid'=>$user_id,'user_name'=>$exam_name,'status'=>-1,'audit_time'=>$audit_time,'create_user_id'=>$create_id,'is_success'=>0]);
                        }

                    }



                    break;

                case 4:
                    $ndays = db::table('vacations')->where('user_id', $create_id)->value('txdays');

                    if($exam_id == $user_id){


                        if($datas['type'] == 1){

                            $a = db::table('leave')
                                ->where('id', $datas['id'])
                                ->update(['status' => 1]);

                            $b = db::table('vacations')
                                ->where('user_id', $create_id)
                                ->update(['txdays' => $ndays-$times]);

                            $c = db::table('total_audit')->insert(['type' =>3,'relation_id'=>$datas['id'],'uid'=>$user_id,'user_name'=>$exam_name,'status'=>1,'audit_time'=>$audit_time,'create_user_id'=>$create_id,'is_success'=>1]);

                        }else{

                            $a = db::table('leave')
                                ->where('id', $datas['id'])
                                ->update(['status' => 2]);

                            $c = db::table('total_audit')->insert(['type' =>3,'relation_id'=>$datas['id'],'uid'=>$user_id,'user_name'=>$exam_name,'status'=>-1,'audit_time'=>$audit_time,'create_user_id'=>$create_id,'is_success'=>1]);

                        }


                    }else{

                        if($datas['type'] == 1){
                            $c = db::table('total_audit')->insert(['type' =>3,'relation_id'=>$datas['id'],'uid'=>$user_id,'user_name'=>$exam_name,'status'=>1,'audit_time'=>$audit_time,'create_user_id'=>$create_id,'is_success'=>0]);
                        }else{
                            $c = db::table('total_audit')->insert(['type' =>3,'relation_id'=>$datas['id'],'uid'=>$user_id,'user_name'=>$exam_name,'status'=>-1,'audit_time'=>$audit_time,'create_user_id'=>$create_id,'is_success'=>0]);
                        }

                    }


                    break;

                case 5:
                    $ndays = db::table('vacations')->where('u_id', $create_id)->value('cdays');

                    if($exam_id == $user_id){


                        if($datas['type'] == 1){

                            $a = db::table('leave')
                                ->where('id', $datas['id'])
                                ->update(['status' => 1]);

                            $b = db::table('vacations')
                                ->where('user_id', $create_id)
                                ->update(['cdays' => $ndays-$times]);

                            $c = db::table('total_audit')->insert(['type' =>3,'relation_id'=>$datas['id'],'uid'=>$user_id,'user_name'=>$exam_name,'status'=>1,'audit_time'=>$audit_time,'create_user_id'=>$create_id,'is_success'=>1]);

                        }else{

                            $a = db::table('leave')
                                ->where('id', $datas['id'])
                                ->update(['status' => 2]);

                            $c = db::table('total_audit')->insert(['type' =>3,'relation_id'=>$datas['id'],'uid'=>$user_id,'user_name'=>$exam_name,'status'=>-1,'audit_time'=>$audit_time,'create_user_id'=>$create_id,'is_success'=>1]);

                        }


                    }else{

                        if($datas['type'] == 1){
                            $c = db::table('total_audit')->insert(['type' =>3,'relation_id'=>$datas['id'],'uid'=>$user_id,'user_name'=>$exam_name,'status'=>1,'audit_time'=>$audit_time,'create_user_id'=>$create_id,'is_success'=>0]);
                        }else{
                            $c = db::table('total_audit')->insert(['type' =>3,'relation_id'=>$datas['id'],'uid'=>$user_id,'user_name'=>$exam_name,'status'=>-1,'audit_time'=>$audit_time,'create_user_id'=>$create_id,'is_success'=>0]);
                        }

                    }


                    break;

                case 6:
                    $ndays = db::table('vacations')->where('u_id', $create_id)->value('pcdays');

                    if($exam_id == $user_id){


                        if($datas['type'] == 1){

                            $a = db::table('leave')
                                ->where('id', $datas['id'])
                                ->update(['status' => 1]);

                            $b = db::table('vacations')
                                ->where('user_id', $create_id)
                                ->update(['pcdays' => $ndays-$times]);

                            $c = db::table('total_audit')->insert(['type' =>3,'relation_id'=>$datas['id'],'uid'=>$user_id,'user_name'=>$exam_name,'status'=>1,'audit_time'=>$audit_time,'create_user_id'=>$create_id,'is_success'=>1]);

                        }else{

                            $a = db::table('leave')
                                ->where('id', $datas['id'])
                                ->update(['status' => 2]);

                            $c = db::table('total_audit')->insert(['type' =>3,'relation_id'=>$datas['id'],'uid'=>$user_id,'user_name'=>$exam_name,'status'=>-1,'audit_time'=>$audit_time,'create_user_id'=>$create_id,'is_success'=>1]);

                        }


                    }else{

                        if($datas['type'] == 1){
                            $c = db::table('total_audit')->insert(['type' =>3,'relation_id'=>$datas['id'],'uid'=>$user_id,'user_name'=>$exam_name,'status'=>1,'audit_time'=>$audit_time,'create_user_id'=>$create_id,'is_success'=>0]);
                        }else{
                            $c = db::table('total_audit')->insert(['type' =>3,'relation_id'=>$datas['id'],'uid'=>$user_id,'user_name'=>$exam_name,'status'=>-1,'audit_time'=>$audit_time,'create_user_id'=>$create_id,'is_success'=>0]);
                        }

                    }

                    break;

                case 7:
                    $ndays = db::table('vacations')->where('u_id', $create_id)->value('hdays');

                    if($exam_id == $user_id){


                        if($datas['type'] == 1){

                            $a = db::table('leave')
                                ->where('id', $datas['id'])
                                ->update(['status' => 1]);

                            $b = db::table('vacations')
                                ->where('user_id', $create_id)
                                ->update(['hdays' => $ndays-$times]);

                            $c = db::table('total_audit')->insert(['type' =>3,'relation_id'=>$datas['id'],'uid'=>$user_id,'user_name'=>$exam_name,'status'=>1,'audit_time'=>$audit_time,'create_user_id'=>$create_id,'is_success'=>1]);

                        }else{

                            $a = db::table('leave')
                                ->where('id', $datas['id'])
                                ->update(['status' => 2]);

                            $c = db::table('total_audit')->insert(['type' =>3,'relation_id'=>$datas['id'],'uid'=>$user_id,'user_name'=>$exam_name,'status'=>-1,'audit_time'=>$audit_time,'create_user_id'=>$create_id,'is_success'=>1]);

                        }


                    }else{

                        if($datas['type'] == 1){
                            $c = db::table('total_audit')->insert(['type' =>3,'relation_id'=>$datas['id'],'uid'=>$user_id,'user_name'=>$exam_name,'status'=>1,'audit_time'=>$audit_time,'create_user_id'=>$create_id,'is_success'=>0]);
                        }else{
                            $c = db::table('total_audit')->insert(['type' =>3,'relation_id'=>$datas['id'],'uid'=>$user_id,'user_name'=>$exam_name,'status'=>-1,'audit_time'=>$audit_time,'create_user_id'=>$create_id,'is_success'=>0]);
                        }

                    }

                    break;

                case 8:
                    $ndays = db::table('vacations')->where('u_id', $create_id)->value('ldays');

                    if($exam_id == $user_id){


                        if($datas['type'] == 1){

                            $a = db::table('leave')
                                ->where('id', $datas['id'])
                                ->update(['status' => 1]);

                            $b = db::table('vacations')
                                ->where('user_id', $create_id)
                                ->update(['ldays' => $ndays-$times]);

                            $c = db::table('total_audit')->insert(['type' =>3,'relation_id'=>$datas['id'],'uid'=>$user_id,'user_name'=>$exam_name,'status'=>1,'audit_time'=>$audit_time,'create_user_id'=>$create_id,'is_success'=>1]);

                        }else{

                            $a = db::table('leave')
                                ->where('id', $datas['id'])
                                ->update(['status' => 2]);

                            $c = db::table('total_audit')->insert(['type' =>3,'relation_id'=>$datas['id'],'uid'=>$user_id,'user_name'=>$exam_name,'status'=>-1,'audit_time'=>$audit_time,'create_user_id'=>$create_id,'is_success'=>1]);

                        }


                    }else{

                        if($datas['type'] == 1){
                            $c = db::table('total_audit')->insert(['type' =>3,'relation_id'=>$datas['id'],'uid'=>$user_id,'user_name'=>$exam_name,'status'=>1,'audit_time'=>$audit_time,'create_user_id'=>$create_id,'is_success'=>0]);
                        }else{
                            $c = db::table('total_audit')->insert(['type' =>3,'relation_id'=>$datas['id'],'uid'=>$user_id,'user_name'=>$exam_name,'status'=>-1,'audit_time'=>$audit_time,'create_user_id'=>$create_id,'is_success'=>0]);
                        }

                    }

                    break;

                case 9:
                    $ndays = db::table('vacations')->where('u_id', $create_id)->value('sdays');

                    if($exam_id == $user_id){


                        if($datas['type'] == 1){

                            $a = db::table('leave')
                                ->where('id', $datas['id'])
                                ->update(['status' => 1]);

                            $b = db::table('vacations')
                                ->where('user_id', $create_id)
                                ->update(['sdays' => $ndays-$times]);

                            $c = db::table('total_audit')->insert(['type' =>3,'relation_id'=>$datas['id'],'uid'=>$user_id,'user_name'=>$exam_name,'status'=>1,'audit_time'=>$audit_time,'create_user_id'=>$create_id,'is_success'=>1]);

                        }else{

                            $a = db::table('leave')
                                ->where('id', $datas['id'])
                                ->update(['status' => 2]);

                            $c = db::table('total_audit')->insert(['type' =>3,'relation_id'=>$datas['id'],'uid'=>$user_id,'user_name'=>$exam_name,'status'=>-1,'audit_time'=>$audit_time,'create_user_id'=>$create_id,'is_success'=>1]);

                        }


                    }else{

                        if($datas['type'] == 1){
                            $c = db::table('total_audit')->insert(['type' =>3,'relation_id'=>$datas['id'],'uid'=>$user_id,'user_name'=>$exam_name,'status'=>1,'audit_time'=>$audit_time,'create_user_id'=>$create_id,'is_success'=>0]);
                        }else{
                            $c = db::table('total_audit')->insert(['type' =>3,'relation_id'=>$datas['id'],'uid'=>$user_id,'user_name'=>$exam_name,'status'=>-1,'audit_time'=>$audit_time,'create_user_id'=>$create_id,'is_success'=>0]);
                        }

                    }

                    break;

                case 10:
                    $ndays = db::table('vacations')->where('u_id', $create_id)->value('brdays');

                    if($exam_id == $user_id){


                        if($datas['type'] == 1){

                            $a = db::table('leave')
                                ->where('id', $datas['id'])
                                ->update(['status' => 1]);

                            $b = db::table('vacations')
                                ->where('user_id', $create_id)
                                ->update(['brdays' => $ndays-$times]);

                            $c = db::table('total_audit')->insert(['type' =>3,'relation_id'=>$datas['id'],'uid'=>$user_id,'user_name'=>$exam_name,'status'=>1,'audit_time'=>$audit_time,'create_user_id'=>$create_id,'is_success'=>1]);

                        }else{

                            $a = db::table('leave')
                                ->where('id', $datas['id'])
                                ->update(['status' => 2]);

                            $c = db::table('total_audit')->insert(['type' =>3,'relation_id'=>$datas['id'],'uid'=>$user_id,'user_name'=>$exam_name,'status'=>-1,'audit_time'=>$audit_time,'create_user_id'=>$create_id,'is_success'=>1]);

                        }


                    }else{

                        if($datas['type'] == 1){
                            $c = db::table('total_audit')->insert(['type' =>3,'relation_id'=>$datas['id'],'uid'=>$user_id,'user_name'=>$exam_name,'status'=>1,'audit_time'=>$audit_time,'create_user_id'=>$create_id,'is_success'=>0]);
                        }else{
                            $c = db::table('total_audit')->insert(['type' =>3,'relation_id'=>$datas['id'],'uid'=>$user_id,'user_name'=>$exam_name,'status'=>-1,'audit_time'=>$audit_time,'create_user_id'=>$create_id,'is_success'=>0]);
                        }

                    }

                    break;
            }

            if($exam_id == $user_id&&$datas['type'] == 1){

                if($a&&$b&&$c)
                {
                    $result['code'] = 200;
                    $result['message'] = "操作成功";

                }

            }elseif($exam_id == $user_id&&$datas['type'] == 2){

                if($a&&$c)
                {
                    $result['code'] = 200;
                    $result['message'] = "操作成功";

                }

            }elseif($exam_id != $user_id){

                if($c)
                {
                    $result['code'] = 200;
                    $result['message'] = "操作成功";

                }

            }else{
                $result['code'] = -1;
                $result['message'] = "操作失败";

            }

            DB::commit();//关闭事务
            return $result;

        }catch (Exception $e) {
            DB::rollBack();//回滚事务
        }

    }

    //撤销
    public function revocation($user_id){

        $datas = Request::all();

        if(!isset($datas['id']) || empty($datas['id'])){
            $result['code'] = -1;
            $result['message'] = "待撤销信息id不存在！";
            return $result;
        }

        $count = db::table('leave')->where('id',$datas['id'])->where('user_id',$user_id)->count();
        if(empty($count)){
            $result['code'] = -1;
            $result['message'] = "待撤销信息不存在！";
            return $result;
        }

        if(!empty($datas['reason'])){
            $newStr = preg_replace('/[^\x{4e00}-\x{9fa5}]/u', '', $datas['reason']);
            $reasonstr = mb_strlen($newStr,"utf-8");
            if($reasonstr>50){
                $result['code'] = -1;
                $result['message'] = "撤销理由不能超过50个字！";
                return $result;
            }

        }

        //获取待撤销请假信息审批状态
        $status = db::table('leave')->where('id',$datas['id'])->where('user_id',$user_id)->value('status');

        if($status == 1){
            $result['code'] = -1;
            $result['message'] = "待撤销信息已审批通过，不能撤销！";
            return $result;
        }

        if($status == 3){
            $result['code'] = -1;
            $result['message'] = "你已撤销该信息，不能重复撤销！";
            return $result;
        }

        if(empty($datas['reason'])){
            $arr=array('status'=>3);
        }else{
            $arr=array('status'=>3,'reason'=>$datas['reason']);
        }

        $revocation = db::table('leave')->where('id',$datas['id'])->where('user_id',$user_id)->update($arr);

        if($revocation)
        {
            $result['code'] = 200;
            $result['message'] = "操作成功";
            return $result;
        }else{
            $result['code'] = -1;
            $result['message'] = "操作失败";
            return $result;
        }

    }

    //假期管理信息-人事视角
    public function leave_management_list($arr){
        $list = DB::table('vacation_type')->get(['id','vacname']);

        foreach ($list as $k=>$v){
            $res = DB::table('company_leave_unit')
                ->leftJoin('leave_unit','company_leave_unit.n_id','=','leave_unit.id')
                ->where('company_leave_unit.l_id','=',$v->id)
                ->where('company_leave_unit.c_id','=',$arr['c_id'])
                ->get(['leave_unit.uname']);
            if(count($res)==0) {
                $res = "";
            }else{
                $v->leave_unit = '请假单位：'.$res[0]->uname . '请假';
                $v->leave_length = $res[1]->uname;
            }
        }

        if($list){
            return returnJson($message='ok',$code = 200,$data=$list);
        }else{
            $result['code'] = -1;
            $result['message'] = "获取失败";
            return $result;
        }

    }

    //查看假期详情-人事视角
    public function leavetypedetail($arr){
        $datas = Request::all();
        if(!isset($datas['id']) || empty($datas['id'])){
            return '待查看信息id不存在！';
        }

        $count = db::table('vacation_type')->where('id',$datas['id'])->count();
        if(empty($count)){
            return '待查看信息不存在！';
        }

        $list = DB::table('vacation_type')->where('id',$datas['id'])->get(['id','vacname']);

        foreach ($list as $k=>$v){
            $res = DB::table('company_leave_unit')
                ->leftJoin('leave_unit','company_leave_unit.n_id','=','leave_unit.id')
                ->where('company_leave_unit.l_id','=',$v->id)
                ->where('company_leave_unit.c_id','=',$arr['c_id'])
                ->get(['leave_unit.uname']);
            if(count($res)==0) {
                $res = "";
            }else{
                $v->leave_unit = $res[0]->uname;
                $v->leave_length = $res[1]->uname;
            }
        }

        if($list){
            return returnJson($message='ok',$code = 200,$data=$list);
        }else{
            $result['code'] = -1;
            $result['message'] = "获取失败";
            return $result;
        }

    }

    //添加、编辑假期规则
    public function addupleaverule($arr){
        $datas = Request::all();

        if(!isset($datas['l_id']) || empty($datas['l_id'])){
            return '假期类型id不存在！';
        }

        $count = db::table('vacation_type')->where('id',$datas['l_id'])->count();
        if(empty($count)){
            return '假期类型不存在！';
        }

        if(!isset($arr) || empty($arr)){
            return '所属公司id不存在！';
        }

        $count = db::table('company_leave_unit')->where('l_id',$datas['l_id'])->where('c_id',$arr)->count();

        if(!empty($count)){
            $department = [];
            if(isset($datas['field'])) {

                foreach ($datas['field'] as $k => $v) {
                    $department[] = [
                        'c_id' => $arr['c_id'],
                        'l_id' => $datas['l_id'],
                        'n_id' => $v['n_id'],
                        'type' => $v['type']
                    ];
                }
            }

            $upleavetyperule = CompanyLeaveUnit::query()->where('c_id',$arr['c_id'])->where('l_id',$datas['l_id'])->update($department);


        }else{
            $department = [];
            if(isset($datas['field'])) {

                foreach ($datas['field'] as $k => $v) {
                    $department[] = [
                        'c_id' => $arr['c_id'],
                        'l_id' => $datas['l_id'],
                        'n_id' => $v['n_id'],
                        'type' => $v['type']
                    ];
                }
            }

            $upleavetyperule = CompanyLeaveUnit::query()->insert($department);
        }



        if($upleavetyperule){
            $result['code'] = 200;
            $result['message'] = "操作成功";
            return $result;
        }else{
            $result['code'] = -1;
            $result['message'] = "操作失败";
            return $result;
        }

    }


}
