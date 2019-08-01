<?php

namespace App\Repositories\Addwork;

use App\Models\AttendanceApi\AttendanceApiStaff;
use Mockery\Exception;
use App\Repositories\Repository;
//use XinModules\Clock\Repositories\ClockRespository;
use App\Models\Addwork\Addwork;
use Request;
use App\Constant\ConstFile;
use DB;

class AddworkRespository extends Repository {

    public function model() {
        return Addwork::class;
    }

    /* 加班字段呈现 */
    public function addwork_field($arr){
        $user_id = $arr['user_id'];
        $company_id = $arr['company_id'];
        $rules = $arr['rules'];
//        // 查找所有的字段
//        $re1 = DB::table('addwork_field')
//            ->where('status','=',3)
//            ->where('type','<>','def')
//            ->get(['e_name', 'type']);

        // 查找公司选中的字段
        $re2 = DB::table('addwork_company')
            ->leftJoin('addwork_field','addwork_company.field_id','=','addwork_field.id')
            ->where('addwork_company.company_id', '=', $company_id)
            ->where('addwork_company.type','=',3)
            ->where('addwork_field.type','<>','def')
            ->get(['addwork_field.e_name', 'addwork_field.type']);

        // 查找本人的排班时间
       /* $time = DB::table('user')
            ->leftJoin('clock_rule','user.shifts_id','=','clock_rule.shifts_id')
            ->where('user.id','=',$user_id)
            ->where('clock_rule.status','=',1)
            ->get(['clock_rule.work_day', 'clock_rule.work_start','clock_rule.work_over','clock_rule.work_overtime_start']);*/

//        $list['all'] = $re1;
//        $list['company'] = $re2;
        $list['field'] = $re2;
        $list['rules '] = $rules;
//        $list['time'] = $time;

        return returnJson($message='ok',$code='200',$data=$list);
    }

    /* 加班申请写入 */
    public function addwork($arr)
    {
        DB::beginTransaction(); // 开启事务
        try {
            $data = Request::all();
            $user_id = $arr['user_id']; // 本人的id
            $department_id = $arr['department_id']; // 本人的部门id
            $company_id = $arr['company_id']; // 本人的公司id

            $now_time = date('H:i:s', time());
            $now_w = date('w'); // 获取周几
            if(empty($data['begin_time'])){
                $result['code'] = ConstFile::API_RESPONSE_FAIL;
                $result['message'] = 'no';
                $result['data'] = 'begin_time参数不存在';
                return $result;
            }elseif(empty($data['end_time'])){
                $result['code'] = ConstFile::API_RESPONSE_FAIL;
                $result['message'] = 'no';
                $result['data'] = 'end_time参数不存在';
                return $result;
            }elseif(empty($data['duration'])){
                $result['code'] = ConstFile::API_RESPONSE_FAIL;
                $result['message'] = 'no';
                $result['data'] = 'duration参数不存在';
                return $result;
            }else{
                $begin_time2 = strtotime($data['begin_time']);
                $begin_h = date('H', $begin_time2);
                $end_time2 = strtotime($data['end_time']);
                $end_h = date('H', $end_time2);
                if($begin_h >= $end_h){
                    $result['code'] = ConstFile::API_RESPONSE_FAIL;
                    $result['message'] = 'no';
                    $result['data'] = 'begin_time参数必须小于end_time参数';
                    return $result;
                }
                /*检测时间是否在排班时间内*/
                // 获取排班信息
                /*$time = DB::table('user')
                    ->leftJoin('clock_rule','user.shifts_id','=','clock_rule.shifts_id')
                    ->where('user.id','=',$user_id)
                    ->where('clock_rule.status','=',1)
                    ->get(['clock_rule.work_day', 'clock_rule.work_start','clock_rule.work_over','clock_rule.work_overtime_start']);
                $work_start_h = date('H', strtotime($time[0]->work_start));
                $work_over_h = date('H', strtotime($time[0]->work_over));
                if(strstr($time[0]->work_day,$now_w) != false){ // 说明在排班天内
                    if(($work_start_h<$begin_h && $begin_h<$work_over_h) || ($work_start_h<$end_h && $end_h<$work_over_h)){
                        return '申请时间和排班时间冲突';
                    }
                }*/
            }

            // 检测该时间段内是否已有无拒绝无撤销申请
            $begin_time = $data['begin_time'];
            $end_time = $data['end_time'];
            //        return $end_time;
            $check_begin = Addwork::where('begin_time','<=',$begin_time)
                ->where('end_time','>',$begin_time)
                ->where('user_id','=',$user_id)
                ->where('status','<>',4)
                ->count();
            $check_end = Addwork::where('begin_time','<',$end_time)
                ->where('end_time','>=',$end_time)
                ->where('user_id','=',$user_id)
                ->where('user_id','=',$user_id)
                ->where('status','<>',4)
                ->count();

            if($check_begin != 0 || $check_end != 0){
                $result['code'] = ConstFile::API_RESPONSE_FAIL;
                $result['message'] = 'no';
                $result['data'] = '您在该时间段内已有加班申请';
                return $result;
            }

            $list['user_id'] = $user_id;
            $list['company_id'] = $company_id;
            $list['begin_time'] = $begin_time;
            $list['end_time'] = $end_time;
            $list['duration'] = $data['duration'];
            if(!empty($data['cause'])){
                $list['cause'] = $data['cause'];
            }elseif(empty($data['cause'])){
                $list['cause'] = "";
            }

            // 获取后台自动创建的字段
            $re = DB::table('addwork_company')
                ->leftJoin('addwork_field','addwork_company.field_id','=','addwork_field.id')
                ->where('addwork_company.company_id', '=', $company_id)
                ->where('addwork_field.type','=','def')
                ->get(['addwork_field.e_name', 'addwork_field.type']);
            foreach($re as $v){
                switch ($v->e_name)
                {
                    case 'name':
                        // 获取名字
                        $name = DB::table('users')->where('id','=',$user_id)->get(['chinese_name']);
                        $list['name'] = $name[0]->chinese_name;
                        break;
                    case 'add_time':
                        $list['add_time'] = date('Y-m-d H:i:s', time()); // 创建申请时间
                        break;
                    case 'department_id':
                        $list['department_id'] = $department_id;
                        break;
                    case 'position':
                        // 获取职位
                        $re = DB::table('users')->where('id','=',$user_id)->get(['position']);

                        $list['position'] = $re[0]->position;
                        break;
                }
            }

            // 创建审批编号
            $numbres = '1412412424512';
            $list['numbers']=$numbres;
            $addwork_id = Addwork::insertGetId($list);
            // 检测是否有图片
            if(!empty($data['image'])){
                $image =$data['image'];
                $da = DB::table('addwork_image')->insert(['addwork_id'=>$addwork_id,'name'=>$image]);
            }

            //模拟审批、抄送人
            $exam_arr = [1792=>'林汉威',1793=>'张文辉'];// 审批人id
            $copy_arr = [1794=>'张艳',1795=>'曹雨彤'];// 抄送人ids
            foreach ($exam_arr as $k=>$v){
                DB::table('addwork_audit_peoples')->insert(['addwork_id'=>$addwork_id,'type'=>1,'user_id'=>$k,'user_name'=>$v]);
            }
            foreach ($copy_arr as $kk=>$vv){
                DB::table('addwork_audit_peoples')->insert(['addwork_id'=>$addwork_id,'type'=>2,'user_id'=>$kk,'user_name'=>$vv]);
            }

            $result['code'] = 200;
            $result['message'] = 'ok';

            DB::commit(); // 执行并关闭事务
            return $result;
        }catch(Exception $e){
            DB::rollBack();
        }
    }

    /* 加班申请列表-提交人视角 */
    /*public function addwork_list_submit($user_id){
        $re = DB::table('addwork')
            ->leftJoin('department','addwork.department_id','=','department.id')
            ->where('user_id','=',$user_id)
            ->orderBy('addwork.add_time','desc')
            ->get(['addwork.id', 'addwork.name', 'department.title',
                'addwork.position','addwork.add_time','addwork.begin_time',
                'addwork.end_time', 'addwork.duration','addwork.cause','addwork.status']);
        return returnJson($message='ok',$code='200',$data=$re);
    }*/

    /* 加班申请列表-审批人视角 */
    /*public function addwork_list_audit($arr){
        $company_id = $arr['company_id']; // 公司的id
        $user_id = $arr['user_id']; // 本人的id
        $audit = DB::table('addwork_audit_peoples')
            ->where('company_id','=',$company_id)
            ->where('user_id','=',$user_id)
            ->where('status','=',1)
            ->count();
        if($audit == 0){
            $result['code'] = ConstFile::API_RESPONSE_FAIL;
            $result['message'] = 'no';
            $result['data'] = '该用户不是本公司加班审批人';
            return $result;
        }
        $re = DB::table('addwork')
                ->leftJoin('department','addwork.department_id','=','department.id')
                ->where('company_id','=',$company_id)
                ->orderBy('addwork.add_time','desc')
                ->get(['addwork.id', 'addwork.name', 'department.title',
                    'addwork.position','addwork.add_time','addwork.begin_time',
                    'addwork.end_time', 'addwork.duration','addwork.cause','addwork.status']);
        return returnJson($message='ok',$code='200',$data=$re);
    }*/

     /* 加班申请详情*/
    public function detail($user_id){
        $data = Request::all();
        if(empty($data['id'])){ // 申请的id
            $result['code'] = ConstFile::API_RESPONSE_FAIL;
            $result['message'] = 'no';
            $result['data'] = 'id参数不存在';
            return $result;
        }
        $id = $data['id'];
        $avatar = DB::table('addwork')
            ->leftJoin('users','users.id','=','addwork.user_id')
            ->where('addwork.id','=',$id)
            ->get(['users.avatar']);
        $list['detail']['avatar'] = $avatar[0]->avatar;
        $res = DB::table('addwork')
            ->leftJoin('departments','departments.auto_id','=','addwork.department_id')
            ->where('addwork.id','=',$id)
            ->get(['addwork.id','addwork.user_id','addwork.name','addwork.numbers','addwork.add_time','departments.name as departments_name','addwork.begin_time','addwork.end_time','addwork.duration','addwork.cause','addwork.status']);
        $list['detail']['data'] = $res[0];

        // 查看本人用户的个人信息
        $me = DB::table('users')
            ->where('id','=',$user_id)
            ->get(['chinese_name']);
        // 查看审批人、抄送人信息
        $res2 = DB::table('addwork_audit_peoples')
            ->leftJoin('users','users.id','=','addwork_audit_peoples.user_id')
            ->where('addwork_id','=',$id)
            ->get(['addwork_audit_peoples.id','addwork_audit_peoples.user_id','addwork_audit_peoples.addwork_id','addwork_audit_peoples.user_name','addwork_audit_peoples.type','users.avatar']);
        $list1 = [];
        foreach($res2 as $k=>$v){
            $res4 = DB::table('total_audit')
                ->where('type','=',2)
                ->where('relation_id','=',$v->addwork_id)
                ->where('uid','=',$v->user_id)
                ->get(['id','user_name','status','is_success','audit_time']);
            if(count($res4) != 0){ // 说明存在审批记录，查找是否有审批意见
                $res5 = DB::table('total_comment')
                    ->where('audit_id','=',$res4[0]->id)
                    ->get(['comment_text']);
                if(count($res5) != 0){ // 存在审批意见
                    $res4[0]->comment_text = $res5[0]->comment_text;
                    $res4[0]->avatar = $v->avatar;
                }else{
                    $res4[0]->comment_text = "";
                }
                $list1[$k] = $res4;
            }else{ // 说明还没有审批，或者还没轮到你审批
                // 获取最后一条审批记录
                /*$res5 = DB::table('total_audit')
                    ->where('type','=',2)
                    ->where('relation_id','=',$v->addwork_id)
                    ->orderBy('audit_time','desc')
                    ->first();
                $list1[$k] = $res5->relation_id;*/
                $list1[$k] = 111; // 模拟
            }
        }
        $list['audit'] = $list1;
        return returnJson($message='ok',$code='200',$data=$list);
    }

    /* 加班申请详情页-审批人视角*/
    /*public function detail_audit($user_id){

        $data = Request::all();
        if(empty($data['id'])){
            $result['code'] = ConstFile::API_RESPONSE_FAIL;
            $result['message'] = 'no';
            $result['data'] = 'id参数不存在';
            return $result;
        }
        $id = $data['id'];
        $avatar = DB::table('addwork')
            ->leftJoin('profile','profile.user_id','=','addwork.user_id')
            ->where('addwork.id','=',$id)
            ->get(['profile.avatar']);
        $list['detail']['avatar'] = $avatar[0]->avatar;
        $res = DB::table('addwork')
            ->leftJoin('department','department.id','=','addwork.department_id')
            ->where('addwork.id','=',$id)
            ->get(['addwork.id','addwork.user_id','addwork.name','addwork.numbers','addwork.add_time','department.title','addwork.begin_time','addwork.end_time','addwork.duration','addwork.cause','addwork.status']);
        $list['detail']['data'] = $res;
        $res2 = DB::table('addwork_audits')->where('addwork_id','=',$id)->where('user_id','=',$user_id)->where('status','=',1)->get();
        if(count($res2) == 0){
            $result['code'] = ConstFile::API_RESPONSE_FAIL;
            $result['message'] = 'no';
            $result['data'] = '您不是该申请的审批人';
            return $result;
        }elseif($res2[0]->audit == 1){
            $result['code'] = ConstFile::API_RESPONSE_FAIL;
            $result['message'] = 'no';
            $result['data'] = '您暂时无权查看该条申请';
            return $result;
        }
        // 查询审批人抄送人
        $res3 = DB::table('addwork_audits')
//            ->leftJoin('user','user.id','=','addwork_audits.user_id')
            ->leftJoin('profile','profile.user_id','=','addwork_audits.user_id')
            ->where('addwork_audits.addwork_id','=',$id)
            ->get(['profile.name','profile.avatar','profile.user_id','addwork_audits.status','addwork_audits.cause','addwork_audits.audit','addwork_audits.audit_time']);
        $list['audit'] = $res3;

        // 查询该审批人的信息
        $res4 = DB::table('addwork_audits')
//            ->leftJoin('user','user.id','=','addwork_audits.user_id')
            ->leftJoin('profile','profile.user_id','=','addwork_audits.user_id')
            ->where('addwork_audits.addwork_id','=',$id)
            ->where('addwork_audits.user_id','=',$user_id)
            ->get(['profile.name','profile.avatar','profile.user_id','addwork_audits.status','addwork_audits.cause','addwork_audits.audit','addwork_audits.audit_time']);
        $list['me'] = $res4;
        return returnJson($message='ok',$code='200',$data=$list);
    }*/

    /*加班申请审批*/
    public function audit($user){

        DB::beginTransaction(); // 开启事务
        try {
            $user_id = $user->id;
            $user_name = $user->chinese_name;
            $data = Request::all();
            if(empty($data['addwork_id'])){
                $result['code'] = ConstFile::API_RESPONSE_FAIL;
                $result['message'] = 'no';
                $result['data'] = 'addwork_id参数不存在';
                return $result;
            }elseif(empty($data['type'])){
                $result['code'] = ConstFile::API_RESPONSE_FAIL;
                $result['message'] = 'no';
                $result['data'] = 'type参数不存在';
                return $result;
            }
            $addwork_id = $data['addwork_id'];
            $type = $data['type'];
            /*$res2 = DB::table('addwork_audits')
                ->where('addwork_id','=',$addwork_id)
                ->where('user_id','=',$user_id)
                ->where('status','=',1)
                ->get();

            if(count($res2) == 0){
                $result['code'] = ConstFile::API_RESPONSE_FAIL;
                $result['message'] = 'no';
                $result['data'] = '您不是该申请的审批人';
                return $result;
            }elseif($res2[0]->audit == 1){
                $result['code'] = ConstFile::API_RESPONSE_FAIL;
                $result['message'] = 'no';
                $result['data'] = '您暂时无权查看该条申请';
                return $result;
            }elseif($res2[0]->audit == 3 || $res2[0]->audit == 4){
                $result['code'] = ConstFile::API_RESPONSE_FAIL;
                $result['message'] = 'no';
                $result['data'] = '您已经审批过该条申请';
                return $result;
            }*/

            $res = DB::table('addwork')->where('id','=',$addwork_id)->count();
            if($res == 0){
                $result['code'] = ConstFile::API_RESPONSE_FAIL;
                $result['message'] = 'no';
                $result['data'] = '该条数据不存在，无法进行审批操作';
                return $result;
            }

            $audit_time = date('Y-m-d H:i:s', time());
            if($type == 1){ // 同意
                // 查看是否是最后一级审批人
                $re = DB::table('addwork_audit_peoples')
                    ->where('addwork_id','=',$addwork_id)
                    ->where('user_id','=',$user_id)
                    ->get();
                $id = $re[0]->id; // 这个审批人的id
                $re2 = DB::table('addwork_audit_peoples')
                    ->where('addwork_id','=',$addwork_id)
                    ->where('id','>',$id)
                    ->where('type','=',1)
                    ->get();
                $audit_time = date('Y-m-d H:i:s', time());
                if(count($re2) == 0){ // 说明是最后一个审批人
                    // 新增进审批记录

                    $audit_id = DB::table('total_audit')->insertGetId(['type'=>2,'relation_id'=>$addwork_id,'uid'=>$user_id,'user_name'=>$user_name,'status'=>1,'audit_time'=>$audit_time,'is_success'=>1]);
                    DB::table('addwork')->where('id','=',$addwork_id)->update(['status'=>3]);
                    if(!empty($data['comment_text'])){ // 说明存在审批意见
                        DB::table('total_comment')->insert(['type'=>2,'audit_id'=>$audit_id,'relation_id'=>$addwork_id,'uid'=>$user_id,'user_name'=>$user_name,'comment_text'=>$data['comment_text'],'comment_time'=>$audit_time]);
                    }
                }else{ // 说明不是最后一个审批人
                    $audit_id = DB::table('total_audit')->insertGetId(['type'=>2,'relation_id'=>$addwork_id,'uid'=>$user_id,'user_name'=>$user_name,'status'=>1,'audit_time'=>$audit_time]);
                    if(!empty($data['comment_text'])){ // 说明存在审批意见
                        DB::table('total_comment')->insert(['type'=>2,'audit_id'=>$audit_id,'relation_id'=>$addwork_id,'uid'=>$user_id,'user_name'=>$user_name,'comment_text'=>$data['comment_text'],'comment_time'=>$audit_time]);
                    }
                }
            }elseif($type == -1){ // 拒绝
                $audit_id = DB::table('total_audit')->insertGetId(['type'=>2,'relation_id'=>$addwork_id,'uid'=>$user_id,'user_name'=>$user_name,'status'=>-1,'audit_time'=>$audit_time,'is_success'=>-1]);
                DB::table('addwork')->where('id','=',$addwork_id)->update(['status'=>4]);
                if(!empty($data['comment_text'])){ // 说明存在审批意见
                    DB::table('total_comment')->insert(['type'=>2,'audit_id'=>$audit_id,'relation_id'=>$addwork_id,'uid'=>$user_id,'user_name'=>$user_name,'comment_text'=>$data['comment_text'],'comment_time'=>$audit_time]);
                }
            }else{
                $result['code'] = ConstFile::API_RESPONSE_FAIL;
                $result['message'] = 'no';
                $result['data'] = 'type参数错误，无法进行审批操作';
                return $result;
            }
            /*if($type == 3){ // 同意
                $re = DB::table('addwork_audits')
                    ->where('addwork_id','=',$addwork_id)
                    ->where('user_id','=',$user_id)
                    ->update(['audit'=>3,'audit_time'=>$audit_time,'cause'=>$cause]);
                // 查看是否是最后一个审批人，是的话不需要再对下一个审批人进行设置，也就不需要通知，不是最后一个审批人人，需要对下一个设置，并且通知他
                $id = $res2[0]->id; // 这个审批的id
                $re2 = DB::table('addwork_audits')
                    ->where('addwork_id','=',$addwork_id)
                    ->where('id','>',$id)
                    ->where('status','=',1)
                    ->get();
                if(count($re2) == 0){ // 说明是最后一个审批人
                    DB::table('addwork')->where('id','=',$addwork_id)->update(['status'=>3]);
                    // 修改抄送人的权限，并通知抄送人
                    DB::table('addwork_audits')
                        ->where('addwork_id','=',$addwork_id)
                        ->where('status','=',2)
                        ->update(['audit'=>6]);
                }else{ // 说明不是最后一个审批人
                    $first = DB::table('addwork_audits')
                        ->where('addwork_id','=',$addwork_id)
                        ->where('id','>',$id)
                        ->first();
                    // 把审批权转移给下一个审批人，并通知他
                    DB::table('addwork_audits')->where('id','=',$first->id)->update(['audit'=>2]);
                }
    //            $clockRespository = app()->make(ClockRespository::class);
    //            $clockRespository->addCountByAddWord($addwork_id);
            }elseif($type == 4){ // 拒绝
                $re = DB::table('addwork_audits')
                    ->where('addwork_id','=',$addwork_id)
                    ->where('user_id','=',$user_id)
                    ->update(['audit'=>4,'audit_time'=>$audit_time,'cause'=>$cause]);
                DB::table('addwork')->where('id','=',$addwork_id)->update(['status'=>4]);
                // 拒绝后不需要通知下一个审批人了，审批流直接结束
            }else{
                $result['code'] = ConstFile::API_RESPONSE_FAIL;
                $result['message'] = 'no';
                $result['data'] = 'type参数错误，无法进行审批操作';
                return $result;
            }*/
            $arr['code'] = 200;
            $arr['message'] = 'ok';

            DB::commit(); // 执行并关闭事务
            return $arr;
        }catch(Exception $e){
            DB::rollBack();
        }
    }

    // 加班申请撤销
    public function revocation(){
        $data = Request::all();
        if(empty($data['id'])){
            $result['code'] = ConstFile::API_RESPONSE_FAIL;
            $result['message'] = 'no';
            $result['data'] = 'id参数不存在';
            return $result;
        }elseif(!empty($data['revocation_cause'])){
            $revocation_cause = trim($data['revocation_cause']);
            $num = strlen($revocation_cause);
            if($num>150){
                $result['code'] = ConstFile::API_RESPONSE_FAIL;
                $result['message'] = 'no';
                $result['data'] = 'revocation_cause字数超出限制';
                return $result;
            }
        }elseif(empty($data['revocation_cause'])){
            $revocation_cause = "";
        }
        $id = $data['id'];
        $count = DB::table('addwork')->where('id','=',$id)->count();
        if($count == 0){
            $result['code'] = ConstFile::API_RESPONSE_FAIL;
            $result['message'] = 'no';
            $result['data'] = '数据不存在';
            return $result;
        }
        $res = DB::table('addwork')->where('id','=',$id)->get();
        if($res[0]->status == 3 || $res[0]->status == 4){
            $result['code'] = ConstFile::API_RESPONSE_FAIL;
            $result['message'] = 'no';
            $result['data'] = '该条申请已审批完成，不可以撤销';
            return $result;
        }elseif($res[0]->status == 5){
            $result['code'] = ConstFile::API_RESPONSE_FAIL;
            $result['message'] = 'no';
            $result['data'] = '该条申请已被撤销';
            return $result;
        }
        $re = DB::table('addwork')->where('id','=',$id)->update(['status'=>5,'revocation_cause'=>$revocation_cause]);
        $arr['code'] = 200;
        $arr['message'] = 'ok';
        return $arr;
    }

    // 个人/人事申请列表，历史记录
    public function history_list($user_id){
        $data = Request::all();
        if(!empty($data['user_id'])){ // 说明是历史记录
            $user_id = $data['user_id'];
            $time_ymd = date('Y-m', time());

            // 获取本年本月的数据
            $res = DB::table('addwork')
                ->leftJoin('users','users.id','=','addwork.user_id')
                ->where('addwork.user_id','=',$user_id)
                ->where('addwork.add_time','like','%'.$time_ymd.'%')
                ->orderBy('addwork.add_time','desc')
                ->get(['addwork.id','addwork.name','users.avatar','addwork.begin_time','addwork.end_time','addwork.add_time','addwork.status']);
            $list = [];
            if(count($res) != 0){
                // 获取相应的审批信息
                foreach ($res as $k=>$v){
                    $list[$k]['detail'] = $v;
                    $res_data = DB::table('total_audit')
                        ->where('relation_id','=',$v->id)
                        ->get();
                    if(count($res_data) == 0){ // 说明无人审批，自动显示第一个人审批中
                        $res_data2 = DB::table('addwork_audit_peoples')
                            ->where('addwork_id','=',$v->id)
                            ->first(['user_name']);
                        $list[$k]['audit'] = $res_data2->user_name;
                    }else{ // 说明有审批记录
                        $res_data2 = DB::table('total_audit')
                            ->where('relation_id','=',$v->id)
                            ->where('is_success','<>',0)
                            ->get();
                        if(count($res_data2) == 0){ // 说明没有审批完
                            $res_data3 = DB::table('total_audit')
                                ->where('relation_id','=',$v->id)
                                ->orderBy('audit_time','desc')
                                ->first(['uid']);
                            $res_data4 = DB::table('addwork_audit_peoples')
                                ->where('addwork_id','=',$v->id)
                                ->where('user_id','=',$res_data3->uid)
                                ->get(['id']);
                            $res_data5 = DB::table('addwork_audit_peoples')
                                ->where('addwork_id','=',$v->id)
                                ->where('id','>',$res_data4[0]->id)
                                ->where('type','=',1)
                                ->first(['user_name']);
                            $list[$k]['audit'] = $res_data5->user_name;
                        }else{ // 说明审批完了
                            if($res_data2[0]->is_success = 1){
                                $list[$k]['audit'] = 1;// 审批通过
                            }elseif($res_data2[0]->is_success = -1){
                                $list[$k]['audit'] = -1;// 审批通过
                            }
                        }
                    }
                }
            }

            $list1['this_month'] = $list;

            // 获取不是本年本月的数据
            $res2 = DB::table('addwork')
                ->leftJoin('users','users.id','=','addwork.user_id')
                ->where('addwork.user_id','=',$user_id)
                ->where('addwork.add_time','not like','%'.$time_ymd.'%')
                ->orderBy('addwork.add_time','desc')
                ->get(['addwork.id','addwork.name','users.avatar','addwork.begin_time','addwork.end_time','addwork.add_time','addwork.status']);
            // 获取相应的审批信息
            $list2 = [];
            // 获取相应的审批信息
            if(count($res2) != 0) {
                foreach ($res2 as $k=>$v){
                    $list2[$k]['detail'] = $v;
                    $res_dataa = DB::table('total_audit')
                        ->where('relation_id','=',$v->id)
                        ->get();
                    if(count($res_dataa) == 0){ // 说明无人审批，自动显示第一个人审批中
                        $res_dataa2 = DB::table('addwork_audit_peoples')
                            ->where('addwork_id','=',$v->id)
                            ->first(['user_name']);
                        $list2[$k]['audit'] = $res_dataa2->user_name;
                    }else{ // 说明有审批记录
                        $res_dataa2 = DB::table('total_audit')
                            ->where('relation_id','=',$v->id)
                            ->where('is_success','<>',0)
                            ->get();
                        if(count($res_dataa2) == 0){ // 说明没有审批完
                            $res_dataa3 = DB::table('total_audit')
                                ->where('relation_id','=',$v->id)
                                ->orderBy('audit_time','desc')
                                ->first(['uid']);
                            $res_dataa4 = DB::table('addwork_audit_peoples')
                                ->where('addwork_id','=',$v->id)
                                ->where('user_id','=',$res_dataa3->uid)
                                ->get(['id']);
                            $res_dataa5 = DB::table('addwork_audit_peoples')
                                ->where('addwork_id','=',$v->id)
                                ->where('id','>',$res_dataa4[0]->id)
                                ->where('type','=',1)
                                ->first(['user_name']);
                            $list2[$k]['audit'] = $res_dataa5->user_name;
                        }else{ // 说明审批完了
                            if($res_dataa2[0]->is_success = 1){
                                $list2[$k]['audit'] = 1;// 审批通过
                            }elseif($res_dataa2[0]->is_success = -1){
                                $list2[$k]['audit'] = -1;// 审批通过
                            }
                        }
                    }
                }
            }
            $list1['no_month'] =$list2;

            return returnJson($message='ok',$code='200',$data=$list1);
        }
        if(!empty($data['type'])){
            if($data['type'] == 1){ // 说明是以个人身份的申请列表
                $time_ymd = date('Y-m', time());
                // 获取本年本月的数据
                $res = DB::table('addwork')
                    ->leftJoin('users','users.id','=','addwork.user_id')
                    ->where('addwork.user_id','=',$user_id)
                    ->where('addwork.add_time','like','%'.$time_ymd.'%')
                    ->orderBy('addwork.add_time','desc')
                    ->get(['addwork.id','addwork.name','users.avatar','addwork.begin_time','addwork.end_time','addwork.add_time','addwork.status']);
                // 获取相应的审批信息
                $list = [];
                if(count($res) != 0){
                    // 获取相应的审批信息
                    foreach ($res as $k=>$v){
                        $list[$k]['detail'] = $v;
                        $res_data = DB::table('total_audit')
                            ->where('relation_id','=',$v->id)
                            ->get();
                        if(count($res_data) == 0){ // 说明无人审批，自动显示第一个人审批中
                            $res_data2 = DB::table('addwork_audit_peoples')
                                ->where('addwork_id','=',$v->id)
                                ->first(['user_name']);
                            $list[$k]['audit'] = $res_data2->user_name;
                        }else{ // 说明有审批记录
                            $res_data2 = DB::table('total_audit')
                                ->where('relation_id','=',$v->id)
                                ->where('is_success','<>',0)
                                ->get();
                            if(count($res_data2) == 0){ // 说明没有审批完
                                $res_data3 = DB::table('total_audit')
                                    ->where('relation_id','=',$v->id)
                                    ->orderBy('audit_time','desc')
                                    ->first(['uid']);
                                $res_data4 = DB::table('addwork_audit_peoples')
                                    ->where('addwork_id','=',$v->id)
                                    ->where('user_id','=',$res_data3->uid)
                                    ->get(['id']);
                                $res_data5 = DB::table('addwork_audit_peoples')
                                    ->where('addwork_id','=',$v->id)
                                    ->where('id','>',$res_data4[0]->id)
                                    ->where('type','=',1)
                                    ->first(['user_name']);
                                $list[$k]['audit'] = $res_data5->user_name;
                            }else{ // 说明审批完了
                                if($res_data2[0]->is_success == 1){
                                    $list[$k]['audit'] = 1;// 审批通过
                                }elseif($res_data2[0]->is_success == -1){
                                    $list[$k]['audit'] = -1;// 审批拒绝
                                }
                            }
                        }
                    }
                }
                $list1['this_month'] = $list;

                // 获取不是本年本月的数据
                $res2 = DB::table('addwork')
                    ->leftJoin('users','users.id','=','addwork.user_id')
                    ->where('addwork.user_id','=',$user_id)
                    ->where('addwork.add_time','not like','%'.$time_ymd.'%')
                    ->orderBy('addwork.add_time','desc')
                    ->get(['addwork.id','addwork.name','users.avatar','addwork.begin_time','addwork.end_time','addwork.add_time','addwork.status']);
                // 获取相应的审批信息
                $list2 = [];
                // 获取相应的审批信息
                if(count($res2) != 0) {
                    foreach ($res2 as $k=>$v){
                        $list2[$k]['detail'] = $v;
                        $res_dataa = DB::table('total_audit')
                            ->where('relation_id','=',$v->id)
                            ->get();
                        if(count($res_dataa) == 0){ // 说明无人审批，自动显示第一个人审批中
                            $res_dataa2 = DB::table('addwork_audit_peoples')
                                ->where('addwork_id','=',$v->id)
                                ->first(['user_name']);
                            $list2[$k]['audit'] = $res_dataa2->user_name;
                        }else{ // 说明有审批记录
                            $res_dataa2 = DB::table('total_audit')
                                ->where('relation_id','=',$v->id)
                                ->where('is_success','<>',0)
                                ->get();
                            if(count($res_dataa2) == 0){ // 说明没有审批完
                                $res_dataa3 = DB::table('total_audit')
                                    ->where('relation_id','=',$v->id)
                                    ->orderBy('audit_time','desc')
                                    ->first(['uid']);
                                $res_dataa4 = DB::table('addwork_audit_peoples')
                                    ->where('addwork_id','=',$v->id)
                                    ->where('user_id','=',$res_dataa3->uid)
                                    ->get(['id']);
                                $res_dataa5 = DB::table('addwork_audit_peoples')
                                    ->where('addwork_id','=',$v->id)
                                    ->where('id','>',$res_dataa4[0]->id)
                                    ->where('type','=',1)
                                    ->first(['user_name']);
                                $list2[$k]['audit'] = $res_dataa5->user_name;
                            }else{ // 说明审批完了
                                if($res_dataa2[0]->is_success == 1){
                                    $list2[$k]['audit'] = 1;// 审批通过
                                }elseif($res_dataa2[0]->is_success == -1){
                                    $list2[$k]['audit'] = -1;// 审批通过
                                }
                            }
                        }
                    }
                }
                $list1['no_month'] =$list2;
                return returnJson($message='ok',$code='200',$data=$list1);
            }elseif($data['type'] == 2){ // 说明是以审批人身份的申请列表
                $result['code'] = ConstFile::API_RESPONSE_FAIL;
                $result['message'] = 'no';
                $result['data'] = '敬请期待...';
                return $result;
            }
        }
    }

    /*加班申请评论写入*/
    /*public function comment($user){
        DB::beginTransaction(); // 开启事务
        try {
            $data = Request::all();
            if (empty($data['relation_id'])) {
                $result['code'] = ConstFile::API_RESPONSE_FAIL;
                $result['message'] = 'no';
                $result['data'] = 'relation_id参数不存在';
                return $result;
            } elseif (empty($data['comment_text'])) {
                $result['code'] = ConstFile::API_RESPONSE_FAIL;
                $result['message'] = 'no';
                $result['data'] = 'comment_text参数不存在';
                return $result;
            }
            $data['uid'] = $user->id;
            $data['user_name'] = $user->name;
            $data['type'] = 2;
            $data['comment_time'] = date('Y-m-d H:i:s', time());
            $data['comment_img'] = '/public/img';
            $data['comment_field'] = '/public/field';
            DB::table('total_comment')->insert($data);

            $arr['code'] = 200;
            $arr['message'] = 'ok';
            DB::commit(); // 执行并关闭事务
            return $arr;
        }catch(Exception $e){
            DB::rollBack();
        }
    }*/
}
