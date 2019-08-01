<?php

namespace App\Repositories;

use App\Constant\ConstFile;

use App\Models\AttendanceApi\AttendanceApiStaff;
use App\Repositories\Repository;
use App\Models\Leaveout\Leaveout;
use App\Repositories\UsersRepository;
use Exception;
use Request;
use DB;
use Auth;

class LeaveoutRepository extends Repository {
    public function model()
    {
        return Leaveout::class;
    }
   protected static $config = [
        'name' => '外出',
        'introduce' => '用户外出申请',
        'flow_nodes' => [
            [
                'type' => 1,//1会签 2或签
                'profile' => 2,//职位
                'self' => 1,//是否本部门审批
                'need' => 1,//是否可以必须
                'copy' =>'1802',
                'user'=>'1802,1791', //审批人id

            ],
            [
                'type' => 2,
                'profile' => 4,
//                'copy' => 4,//抄送人id
                'need' => 1,
                'user'=>'1791,1802'//审批人id

            ]
        ],
        'department_id' => NULL,//部门id
        'company_id' => NULL,//公司id
        'profile_id' => NULL,//职位id
        'user_id' => NULL,//审批人id
    ];

    

    private $company_field = [];

    //外出申请字段列表
    public function leaveout_field()
    {
        $userinfo = Auth::user();
        $company_id = $userinfo->company_id;
        $company_id=$company_id==64?2:$company_id;
        //查询公司选择 的字段
        $fields = DB::table('addwork_company')
            ->leftJoin('addwork_field', 'addwork_company.field_id', '=', 'addwork_field.id')
            ->where('addwork_company.company_id', '=', $company_id)
            ->where('addwork_company.type', '=', 5)
            ->where('addwork_field.type', '<>', 'def')
            ->get(['addwork_field.e_name', 'addwork_field.type']);
        // 查找当前用户的排班情况
        // $work_time = DB::table('user')
        //     ->leftJoin('clock_rule', 'user.shifts_id', '=', 'clock_rule.shifts_id')
        //     ->where('clock_rule.status', '=', 1)
        //     ->where('clock_rule.user_id', '=', $id)
        //     ->get(['clock_rule.work_day', 'clock_rule.work_start', 'clock_rule.work_over']);

        // $data['work_time'] = $work_time;
        $data['fields'] = $fields;

        return returnJson($message = 'ok', $code = '200', $data = $data);
    }


    /**
     * 审批通过完结
     *
     * @param integer $programID 审批项目id
     * @return array
     */
    private static function successOver($programID)
    {
        $successdata    = [
            'check_user_id'   => NULL,
            'check_profile_id'   => NULL,
            'check_department_id' => NULL,
            'status'        => 1,
        ];
        $result=DB::table('leaveout_program')->where('id',$programID)->update($successdata);
        if($result){
            DB::commit();
            return returnJson('审批提交成功', 200, '');

        }else{
           DB::rollBack();
            return returnJson('审批提交失败', 404, '');
        }
    }

    /**
     * 获取到达节点的审批人
     *
     * @param array $flowNodes 流程节点数组
     * @param integer $nextNode 下一个节点id
     * @return array 下个节点的数据
     */
    private static function getNextNode($flowNodes, $nextNode)
    {
            $resdata['now_node']     = $nextNode;
            $resdata['check_user_id'] = ','. $flowNodes[$nextNode]['user'] .',';
            return $resdata;
    }

    /**
     * 把项目转到下一个流程
     *
     * @param array $flowNodes 流程数据数组
     * @param array $program 申请项目数据数组
     * @return array 执行结果
     */
    private static function goNextNode($flowNodes, $program)
    {
        $nextNode       = $program['now_node'] = $program['now_node']+1;
        $programdata    = DB::table('leaveout')->where('id',$program['leaveout_id'])->first();
        $programdata=get_object_vars($programdata);
        if ($flowNodes[$nextNode]['need'] == 1) {
            $nextRes    = self::getNextNode($flowNodes, $nextNode);
        } else if (empty($flowNodes[$nextNode])) {
            return self::successOver($program['id']);
        } else {
            foreach ($flowNodes[$nextNode]['need'] as $val) {
                if ($val['type'] == 1) {
                    if ($programdata[$val['field']] > $val['value']) {
                        $need   = 1;
                        if ($flowNodes[$nextNode]['needtype'] == 2) {
                            break;
                        }
                    } else {
                        $need   = 0;
                        if ($flowNodes[$nextNode]['needtype'] == 1) {
                            break;
                        }
                    }
                } else if ($val['type'] == 2) {
                    if ($programdata[$val['field']] == $val['value']) {
                        $need   = 1;
                        if ($flowNodes[$nextNode]['needtype'] == 2) {
                            break;
                        }
                    } else {
                        $need   = 0;
                        if ($flowNodes[$nextNode]['needtype'] == 1) {
                            break;
                        }
                    }
                } else if ($val['type'] == 3) {
                    if ($programdata[$val['field']] < $val['value']) {
                        $need   = 1;
                        if ($flowNodes[$nextNode]['needtype'] == 2) {
                            break;
                        }
                    } else {
                        $need   = 0;
                        if ($flowNodes[$nextNode]['needtype'] == 1) {
                            break;
                        }
                    }
                }
            }
            if ($need == 1) {
                $nextRes    = self::getNextNode($flowNodes, $nextNode);
            } else {
                return self::goNextNode($flowNodes, $program);
            }
        }
        $updata     = [
            'check_user_id'       => $nextRes['check_user_id'],
//            'check_profile_id'       => $nextRes['check_profile_id'],
//            'check_department_id' => $nextRes['check_department_id'],
            'now_node'           => $nextNode,
        ];
        if (!empty($flowNodes[$nextNode]['copy'])&&!empty($program['copy_user_id'])) {
            $updata['copy_user_id']   = ','.implode(',', array_merge(
                    explode(',', trim($program['copy_user_id'], ',')),
                    explode(',', $flowNodes[$nextNode]['copy'])
                )).',';
        }
        $result=DB::table('leaveout_program')->where('id',$program['id'])->update($updata);
        if($result){
            DB::commit();
            return returnJson('审批提交成功', 200, '');

        }else{
            DB::rollBack();
            return returnJson('审批提交失败', 404, '');
        }
    }


    /**
     * 计算新申请的应到节点、审批人、申请编号
     *
     * @param array $flowNodes 流程节点数组
     * @param integer $userID 申请人id
     * @param integer $nowNode 当前节点
     * @return array 应到节点、审批人、申请编号
     */
    private static function getNewNodeAndCheckUser($flowNodes, $userId, $nowNode)
    {
        $nowNode = intval($nowNode);
        $resdata = [];
        /* 编号计算 */
        $orderNum = date('Ymdhis', time());

        if (!empty($flowNodes[$nowNode]['copy'])) {
            $resdata['copy_user_id'] = ',' . $flowNodes[$nowNode]['copy'] . ',';
        }else{
            $resdata['copy_user_id']='';
        }


        $resdata['now_node'] = $nowNode;
        $resdata['check_user_id'] = ',' . $flowNodes[$nowNode]['user'] . ',';
        $resdata['shenpi_no'] = $orderNum;

        return $resdata;
    }

    /**
     * 流程计算
     *
     * @param integer $userID 用户id
     * @param array $data 数据
     * @param integer $nowNode 节点
     * @return array 流程数据
     */
    private static function getNewCheck($userId, $data, $nowNode = 0)
    {
        $flowNodes = self::$config['flow_nodes'];
        if ($flowNodes[$nowNode]['need'] == 1) {
            //如果流程是必须经过的
            return self::getNewNodeAndCheckUser($flowNodes, $userId, $nowNode);
        } else if (is_array($flowNodes[$nowNode]['need'])) {
            /*   'type'  => // 1:会签  2:或签
             *              'role'  => // 角色id
             *              'department => // 部门id
             *              'user'=> // 审批人id
             *              'self'  => // 1：本部门对应角色签批
             *              'copy'  => // 抄送人id
             *              'need'  => [
             *                  [
             *                      'field' => // 字段
             *                      'type'  => // 1:值大于  2:值相等  3:值小于
             *                      'value' => // 需要时字段条件值
             *                  ]
             *                  [
             *                      'field' => // 字段
             *                      'type'  => // 1:值大于  2:值相等  3:值小于
             *                      'value' => // 需要时字段条件值
             *                  ]
             *              ]
             *              'needtype'  => // 1:且  2:或
                    /* 确定流程是否需要 */
            foreach ($flowNodes[$nowNode]['need'] as $val) {
                if ($val['type'] == 1) {
                    if ($data[$val['field']] > $val['value']) {
                        $need = 1;
                        if ($flowNodes[$nowNode]['needtype'] == 2) {
                            break;
                        }
                    } else {
                        $need = 0;
                        if ($flowNodes[$nowNode]['needtype'] == 1) {
                            break;
                        }
                    }
                } else if ($val['type'] == 2) {
                    if ($data[$val['field']] == $val['value']) {
                        $need = 1;
                        if ($flowNodes[$nowNode]['needtype'] == 2) {
                            break;
                        }
                    } else {
                        $need = 0;
                        if ($flowNodes[$nowNode]['needtype'] == 1) {
                            break;
                        }
                    }
                } else if ($val['type'] == 3) {
                    if ($data[$val['field']] < $val['value']) {
                        $need = 1;
                        if ($flowNodes[$nowNode]['needtype'] == 2) {
                            break;
                        }
                    } else {
                        $need = 0;
                        if ($flowNodes[$nowNode]['needtype'] == 1) {
                            break;
                        }
                    }
                }
            }
            if ($need == 1) {
                return self::getNewNodeAndCheckUser($flowNodes, $userId, $nowNode);
            } else {
                return self::getNewCheck($userId, $data, intval($nowNode) + 1);
            }
        }
    }

    //写入外出申请
    public function create_leaveout($data)
    {
        $userinfo = Auth::user();
//        return $userinfo;
        $id=$userinfo->id;
        $company_id = $userinfo->company_id;
        $company_id=$company_id==64?2:$company_id;
        $dept=$this->get_dept($id);

        $result = ['code' => ConstFile::API_RESPONSE_SUCCESS, 'message' => ConstFile::API_RESPONSE_SUCCESS_MESSAGE];
        $error = $this->checkData($data);
        if ($error) {
            return returnJson($error, 404, '');
        }

        //自动生成的字段
        $data['user_id'] = isset($id) ? $id : '';  //用户id
        $data['company_id'] = $company_id;
        $data['department_id'] = isset($dept[0]->department_id) ? $dept[0]->department_id: ''; //部门 id
        $data['position'] = $userinfo->position;
        $data['add_time'] = date('Y-m-d H:i:s', time());

        //判断申请时间是否合理
        $begin = isset($data['begin_time']) ? $data['begin_time'] : '';
        $end = isset($data['end_time']) ? $data['end_time'] : '';
        $data['address'] = isset($data['address']) ? $data['address'] : '';

        $c_begin = Leaveout::where("begin_time", '<=', $begin)
            ->where("end_time", '>=', $begin)
            ->where('user_id', '=', $id)

            ->count();
        $c_end = Leaveout::where("begin_time", '<=', $end)
            ->where("end_time", '>=', $end)
            ->where('user_id', '=', $id)

            ->count();

        if ($c_begin != 0 || $c_end != 0) {
            return returnJson('你申请的外出时间冲突', 404, '');
        }

        //判断申请时间是否在上班期间
        /*$now_w=date('w',time());
        $work_time = DB::table('user')
            ->leftJoin('clock_rule','user.shifts_id','=','clock_rule.shifts_id')
            ->where('clock_rule.status','=',1)
            ->where('clock_rule.user_id','=',$id)
            ->get(['clock_rule.work_day', 'clock_rule.work_start','clock_rule.work_over']);
        $begin_work_at=date('H',strtotime($work_time[0]->work_start));
        $end_work_at=date('H',strtotime($work_time[0]->work_over));
        if(strstr($work_time[0]->work_day,$now_w)){

        }
        if(!in_array($day,$work_day)){
            return returnJson('申请时间不在工作日');
        }
        $work_start=$work_time['work_start'];
        $work_over=$work_time['work_over'];*/


        //时长 为空就自动计算

        $data['duration'] = isset($data['duration']) ? $data['duration'] : floor((strtotime($data['end_time']) - strtotime($data['begin_time'])) % 86400 / 3600); //自动生成
        DB::beginTransaction();
        $img=$data['image'];
        unset($data['image']);
        $res = DB::table('leaveout')->insertGetId($data);


        if ($res) {
            if (!empty($img)) {
                $re = DB::table('leaveout_img')->insert(['image' => $img, 'leaveout_id' => $res]);
                if (!$re) {
                    DB::rollBack();
                    return returnJson('图片插入失败', 404, []);
                }
            }
            $program = [
                'user_id' => $id,
                'leaveout_id' => $res,
                'add_time' => date('Y-m-d H:i:s', time()),
            ];
            $checkdata = self::getNewCheck($id, $data);

            if ((empty($checkdata['now_node']) && $checkdata['now_node'] !== 0) || empty($checkdata['shenpi_no'])) {
                DB::rollBack();
                return returnJson('流程处理失败', 404, '');
            }
            if (empty($checkdata['check_user_id']) && empty($checkdata['check_profit_id']) && empty($checkdata['check_department_id'])) {
                DB::rollBack();
                return returnJson('流程处理失败', 404, '');
            }
            $program['shenpi_no'] = $checkdata['shenpi_no'];
            $program['now_node'] = $checkdata['now_node'];
            $program['check_user_id'] = $checkdata['check_user_id'];
            $program['copy_user_id'] = $checkdata['copy_user_id'];
//            $program['check_department_id'] = $checkdata['check_department_id'];
//            $program['check_profit_id'] = $checkdata['check_profit_id'];
            $re = DB::table('leaveout_program')->insertGetId($program);
            if (!$re) {
                DB::rollBack();
                return returnJson('审批人插入失败', 404, []);

            } else {
                DB::commit();
                return returnJson('你的外出申请已提交成功', 200,['program_id'=>$re]);
            }
        } else {

            return returnJson('网路异常,请稍后再试', 404, '');
        }

    }


    //已提交待审批页面-申请人 必须传递program_id
    public function leaveout_check($data)
    {


        //判断查看详情的人是谁


        $userinfo = Auth::user();

        $id=$userinfo->id;

        $company_id = $userinfo->company_id;



        if (!isset($data['program_id'])) {
            return returnJson('program_id不能为空', 404, '');
        }
        $program_id = $data['program_id'];


        $c_id =  $company_id;

        $program = DB::table('leaveout_program as LP')
            ->leftJoin('leaveout as L','LP.leaveout_id','=','L.id')
            ->where('LP.id', $program_id)
            ->first(['LP.shenpi_no','LP.leaveout_id','LP.now_node','LP.id as program_id','LP.user_id','LP.check_user_id','LP.copy_user_id','LP.status','LP.add_time','L.add_time as create_time','L.department_id','L.begin_time','L.end_time','L.duration','L.reason']);
        if(!$program){
            return returnJson('program_id参数错误');
        }
        $data=get_object_vars($program);

        if($data['status']==1){
            //审批通过后查询审批人和审批意见


            $comment=DB::table('total_audit as ta')
                ->leftJoin('total_comment as tc','ta.id','=','tc.audit_id')
                ->where('ta.relation_id','=',$data['program_id'])
                ->where('ta.type','=',4)
                ->orderBy('comment_time','desc')
                ->get(['tc.comment_text','tc.comment_img','tc.comment_field','tc.comment_time','ta.*']);
            foreach($comment as $k=>$v){
               $v->photo=$userinfo->avatar;
            }
            $data['check_user']=$comment;


        }else if($data['status']==-1){
            $comment=DB::table('total_audit as ta')
                ->leftJoin('total_comment as tc','ta.id','=','tc.audit_id')
                ->where('ta.relation_id','=',$data['program_id'])
//                ->where('ta.status','=',-1)
                ->where('ta.type','=',4)
                ->orderBy('comment_time','desc')
                ->get(['tc.comment_text','tc.comment_img','tc.comment_field','tc.comment_time','ta.*']);
            foreach($comment as $k=>$v){
                $v->photo=$userinfo->avatar;
            }
            $data['check_user']=$comment;
        }else if($data['status']==0&&$data['now_node']!=0){
            $comment=DB::table('total_audit as ta')
                ->leftJoin('total_comment as tc','ta.id','=','tc.audit_id')
                ->where('ta.relation_id','=',$data['program_id'])
                ->orderBy('comment_time','desc')
                ->where('ta.type','=',4)
                ->get(['tc.comment_text','tc.comment_img','tc.comment_field','tc.comment_time','ta.*']);
            foreach($comment as $k=>$v){
                $v->photo=$userinfo->avatar;
            }
            $data['check_user']=$comment;

        }else{
            $check_user_id=explode(',',trim($data['check_user_id'],','));

            $data['check_user']=$this->get_user_info($check_user_id);
            unset($data['check_user_id']);
        }
        /*供多个页面使用 不加限制*/
//        if ($data['status'] != 0||empty($data['check_user_id'])) {
//            return returnJson('该申请已审批结束', 404, []);
//        }


        //获取当前审批人
//        $res=DB::table('leaveout_shenpi')
//            ->where('program_id',$program_id)
//            ->get();
//        if(empty($res)){
            //没有被审批过

//        查询该申请的普通评论

       $comment=DB::table('total_comment')
           ->where('relation_id',$data['program_id'])
           ->where('audit_id','=',null)
           ->orderBy('comment_time','desc')
           ->where('type',4)
           ->get();
       $data['comment']=isset($comment)?$comment:[];


            if(!empty($data['copy_user_id'])){
                $copy_user_id=explode(',',trim($data['copy_user_id'],','));
                $data['copy_user']=$this->get_user_info($copy_user_id);


            }else{
                $data['copy_user'] = [];
            }
            unset($data['copy_user_id']);
//        }else{
//
//        }
//
//        $data['user_name']=$this->get_user_info($data['user_id']);
//
//        $data['user_photo']=DB::table('users')->where('id',$data['user_id'])->value('avatar');
//        $data['department'] = $this->get_dept($data['user_id']);
        $data['create_user']['user']=$this->get_user_info($data['user_id']);

        $data['create_user']['department']=$this->get_dept($data['user_id'])[0];
        $data['nowlogin_user']=$this->get_user_info($id);
        if(!empty($data['check_user_id'])){
            $check_user_id=explode(',',trim($data['check_user_id'],','));
            $data['next_check_user']=$this->get_user_info($check_user_id);
        }else{
            $data['next_check_user']=[];
        }
        return returnJson('ok', 200, $data);

    }

    //个人/人事外出申请列表 type : 1个人/2人事
    public function leaveout_list($data, $id)
    {

//        $data['type']=1;
        if(!isset($data['type'])) return returnJson('视角类型必填',404);


        //判断id是否是人事？
        if($data['type']==1){
            //个人视角
            $where=['L.user_id'=>$id];
        }else if($data['type']==2){
            //人事视角
            $where=[];
        }

        $start = mktime(0, 0, 0, date('m'), 1, date('Y'));
        $end = mktime(23, 59, 59, date('m'), date('t'), date('Y'));
        //查询当月
        $now_month = DB::table('leaveout as L')
            ->leftJoin('leaveout_program as LP','L.id','=','LP.leaveout_id')
            ->where('L.add_time', '>=', date('Y-m-d H:i:s', $start))
            ->where('L.add_time', '<=', date('Y-m-d', $end))
            ->where($where)
            ->orderBy('add_time', 'desc')
            ->get(['L.begin_time', 'L.end_time', 'L.add_time','LP.user_id','LP.id as program_id','LP.status','LP.check_user_id','LP.is_edit']);

        foreach($now_month as $k=>$v){
            if($v->is_edit==1){
                if($v->status==0){
                    $v->status=-1;
                }
            }else{
                $v->check_user_id=explode(',',trim($v->check_user_id,','));
                $v->check_user=$this->get_user_info($v->check_user_id);

            }
            $v->user=$this->get_user_info($v->user_id);
            $v->time_style=$this->time_trans($v->add_time);
        }

        //查询其他月
        $other_month = DB::table('leaveout as L')
            ->leftJoin('leaveout_program as LP','L.id','=','LP.leaveout_id')
            ->where('L.add_time', '<', date('Y-m-d H:i:s', $start))
            ->where('L.add_time', '>', date('Y-m-d', $end))
            ->where($where)
            ->orderBy('add_time', 'desc')
            ->get(['L.begin_time', 'L.end_time', 'L.add_time','LP.user_id','LP.status','LP.check_user_id','LP.is_edit']);
        foreach($other_month as $k=>$v){
            if($v->is_edit==1){
                $v->status=2;
            }else{
                $v->check_user_id=explode(',',trim($v->check_user_id,','));
                $v->check_user=$this->get_user_info($v->check_user_id);

            }
            $v->user=$this->get_user_info($v->user_id);
            $v->time_styel=$this->time_trans($v->add_time);

        }
        $data['now_month']=$now_month;
        $data['other_month']=$other_month;
        if (!isset($data)) {
            return returnJson('当前人员没有外出申请', 404, '');
        }

        return returnJson('ok', 200, $data);
    }

    //审批外出申请 参数 program_id status值1通过 -1拒绝
    public function leaveout_shenpi($data)
    {
        $userinfo = Auth::user();
        $user_id = $userinfo->id;
        if (!isset($data['status'])) {
            return returnJson('status不能为空', 404, '');
        }else if($data['status']!=1&&$data['status']!=-1&&$data['status']!=3){
            return returnJson('status参数有误',404,[]);
        }
        if (!isset($data['program_id'])) {
            return returnJson('program_id不能为空', 404, '');
        }


        //流程项目信息
        $program    =DB::table('leaveout_program')->where('id', '=', $data['program_id'])->first();
        if(!$program) return returnJson('program_id参数有误',404,[]);
        $program=get_object_vars($program);
        $data['leaveout_id']=$program['leaveout_id'];
//        var_dump(strpos($program['check_user_id'], ",$user_id,"));

//        die;
        /* 审批前验证 */
        //审批人部门职位验证......
        if ($program['is_edit'] == 1 || $program['status'] != 0) {
//            return returnJson('该阶段不能审核', 404, '');
            return returnJson('该申请已完成，不能审批', 404, '');
        }


        if (strpos($program['check_user_id'], ",$user_id,") === false) {
//            return returnJson('审核人id错误', 404, '');
            return returnJson('该用户没有审批权限', 404, '');
        }

        if ($program['deleted_at']) {
            return returnJson('审批项不存在', 404, '');
        }
        $name=$this->get_user_info($user_id)['name'];
        //生成审批日志
        $checklog   = [
            'type'          =>4,
            'relation_id'     => $program['id'],
            'uid'        => $user_id, //审批人id
//            'is_success'        => $program['now_node'],
////            'audit_info'       => isset($data['audit_info'])?$data['audit_info']:'',
//            'audit_info'       => isset($data['audit_info'])?$data['audit_info']:'',
//
////            'audit_field'         => isset($data['audit_field'])?implode(',',$data['audit_field']):'',
//            'audit_field'         => isset($data['audit_field'])?$data['audit_field']:'',
//
////            'audit_img'         => isset($data['audit_img'])?implode(',',$data['audit_img']):'',
//            'audit_img'         => isset($data['audit_img'])?$data['audit_img']:'',

            'status'        => $data['status'],
            'audit_time'    => date('Y-m-d H:i:s',time()),
            'user_name'     =>$name,
            'create_user_id'=>$program['user_id']

        ];
        DB::beginTransaction();
        $result=DB::table('total_audit')->insertGetId($checklog);
        if(!$result) {
            DB::rollBack();
            return returnJson('日志保存失败', 404, '');
        }
        if(!empty($data['comment_text'])){

            //插入评论表
            $comment=[
                'type'=>4,
                'audit_id'=>$result,
                'relation_id'=>$program['id'],
                'uid'=>$user_id,
                'user_name'=>$name,
                'comment_text'=>$data['comment_text'],
                'comment_time'=>date('Y-m-d H:i:s',time()),
            ];
            $re=DB::table('total_comment')->insert($comment);
            if(!$re){
                DB::rollBack();
                return returnJson('审批意见保存失败',404,'');
            }
        }
        $flowNodes = self::$config['flow_nodes'];//流程节点数据
        $formdata = DB::table('leaveout')->where('id', '=', $data['leaveout_id'])->first();//申请的数据
        $formdata=get_object_vars($formdata);
        $status=$data['status'];
        $programID=$program['id'];
        switch ($status) {
                case '1'://1通过
                    //如果是会签
                    if ($flowNodes[$program['now_node']]['type'] == 1) {
                        $checkUserID = explode(',', trim($program['check_user_id'], ','));
                        if (!in_array($user_id, $checkUserID)) {
                            DB::rollBack();
                            return returnJson('审核人id错误', 404, '');
                        }
                        $checkUserID = array_diff($checkUserID, [$user_id]);
                        /* 当前会签完成 */
                        if (empty($checkUserID)) {
                            if (empty($flowNodes[$program['now_node'] + 1])) {
                                /* 审批通过结束 */
//
                                return self::successOver($programID);
                            } else {
                                /* 转到下一个环节 */
                                return self::goNextNode($flowNodes, $program);
                            }
                        } else {
                            /* 会签 */
                            $checkUserID = ',' . implode(',', $checkUserID) . ',';
                            $result = DB::table('leaveout_program')->where('id',$programID)->update(['check_user_id' => $checkUserID]);
                            if ($result) {
                                DB::commit();
//                                DB::table('total_audit')->where('relation_id',$programID)->update(['is_success'=>1]);
                                return returnJson('审批提交成功', 200, '');

                            } else {
                                DB::rollBack();
                                return returnJson('审批提交失败', 404, '');
                            }
                        }
                    } else {
                        /* 或签 */
                        if (empty($flowNodes[$program['now_node'] + 1])) {
                            /* 审批通过结束 */
                            DB::table('total_audit')->where('id',$result)->update(['is_success'=>1]);
                            return self::successOver($program['id']);
                        } else {
                            /* 转到下一个环节 */
                            return self::goNextNode($flowNodes, $program);
                        }
                    }
                    break;
                case '-1'://拒绝
                    DB::table('total_audit')->where('id',$result)->update(['is_success'=>1]);
                    if ($flowNodes[$program['now_node']]['type'] == 1) {
                        $nodeRes    = self::getNextNode($flowNodes, $program['now_node']);
                        if (empty($nodeRes['check_user_id'])) {
                            DB::rollBack();
                            return returnJson('节点信息查询失败', 404, '');
                        }
                        $update = [
                            'check_user_id'       => $nodeRes['check_user_id'],
                            'is_edit'            => 1,
                            'status'            =>-1
//                            'check_profile_id'       => NULL,
//                            'check_department_id' => NULL,
                        ];
                        $result = DB::table('leaveout_program')->where('id',$programID)->update($update);
                        if ($result) {
                            DB::table('total_audit')->where('id',$result)->update(['is_success'=>1]);
                            DB::commit();


                            return returnJson('审批提交成功', 200, '');

                        } else {
                            DB::rollBack();
                            return returnJson('审批提交失败', 404, '');
                        }
                    } else {
                        $nodeRes    = self::getNextNode($flowNodes, $program['now_node']);
                        if (empty($nodeRes['check_user_id']) && empty($nodeRes['check_profile_id']) && empty($nodeRes['check_department_id'])) {
                            DB::rollBack();
                            return returnJson('节点信息查询失败', 404, '');
                        }
                        $update = [
                            'check_user_id'       => $nodeRes['check_user_id'],
                            'is_edit'            => 1,
                            'status'            =>-1
//                            'check_profile_id'       => $nodeRes['check_profile_id'],
//                            'check_department_id' => $nodeRes['check_department_id'],
                        ];
                        $result = DB::table('leaveout_program')->where('id',$programID)->update($update);
                        if ($result) {

                            DB::commit();
//
                            return returnJson('审批提交成功', 200, '');

                        } else {
                            DB::rollBack();
                            return returnJson('审批提交失败', 404, '');
                        }
                    }
                    break;
                default:
                    return returnJson('status参数有误', '404');
            }


    }

    //撤回外出申请 撤销原因非必填 program_id必填
    //只能本人撤销
    public function revoke_leaveout($data, $id)
    {


        if (!isset($data['program_id'])) {
            return returnJson('program_id不能为空', 404, '');
        }

        $revoke_reason = isset($data['revoke_reason'])?$data['revoke_reason']:'';
        if (mb_strlen($revoke_reason, 'utf-8') > 50) {
            return returnJson('revoke_reason参数不能大于50字符', 404);
        }
        $res=DB::table('leaveout_program')->where('id',$data['program_id'])->get(['is_edit','user_id','status','leaveout_id']);

        if($id != $res[0]->user_id){
            return returnJson('仅限申请人操作',404,[]);
        }

//        $is_edit=DB::table('leaveout_program')->where('id',$data['program_id'])->value('is_edit');
//        if($is_edit==1){
//            return returnJson('已被拒绝不能撤销');
//        }
        if($res[0]->status==1){
            return returnJson('当前申请已通过，不能撤销',404,[]);
        }else if($res[0]->status==3){
            return returnJson('当前申请已撤销',404,[]);
        }
        DB::beginTransaction();
        $re=DB::table('leaveout')
            ->where('id',$res[0]->leaveout_id)
            ->update(['revoke_reason' => $revoke_reason]);
        $re2=DB::table('leaveout_program')
            ->where('id',$data['program_id'])
            ->update(['is_edit' => 1,'status'=>3]);
        $re3=DB::table('total_audit')
            ->where('relation_id',$data['program_id'])
            ->update(['is_success' => -1]);
        if(!$re2){
            DB::rollBack();
            return returnJson('网路异常,请稍后再试', 404, []);
        }else{
            DB::commit();
            return returnJson('撤销申请成功', 200, ['program_id'=>$data['program_id']]);
        }

    }
//    //评论申请
//    public function leaveout_comment($data,$userinfo){
//
//    }

    //当前申请人出勤明细 TODO 以后完成
    public function leaveout_detail($data, $user_id)
    {

    }


    private function checkData($data)
    {


        if (!isset($data['begin_time']) || empty($data['begin_time'])) {
            return '开始时间不能为空';
        }

        if (!isset($data['end_time']) || empty($data['end_time'])) {
            return '结束时间不能为空';
        }
        if (strtotime($data['begin_time']) > strtotime($data['end_time'])) {
            return '结束时间不能小于开始时间';
        }
        if (!isset($data['reason']) || empty($data['reason'])) {
            return '外出事由不能为空';
        }
        if (strlen($data['reason']) > 150) {
            return '外出事由超出150个字符';
        }

//        if(!isset($data['address']) || empty($data['address'])){
//            return '外出地址不能为空';
//        }
        if (!isset($data['duration']) || empty($data['duration'])) {
            return '外出时长不能为空';
        }

        return null;
    }
    //格式化时间
    private function time_trans($the_time)
    {
        $now_time = date("Y-m-d H:i:s", time());
        //echo $now_time;
        $now_time = strtotime($now_time);
        $show_time = strtotime($the_time);
        $dur = $now_time - $show_time;
        if ($dur < 0) {
            return $the_time;
        } else if ($dur < 43200) {
            return date('H:i', $show_time);

        } else if (date('d', $now_time) - date('d', $show_time) == 1) {
            return '一天前';
        } else {
            return date('Y-m-d', $show_time);
        }
    }

    private function get_user_info($ids){
        if(is_array($ids)){
            foreach($ids as $k=>$v){

                $user_info[$k]['name']=DB::table('users')->where('id',$v)->value('chinese_name');
                $user_info[$k]['photo']=DB::table('users')->where('id',$v)->value('avatar');
                $user_info[$k]['id']=$v;
            }

        }else{
            $user_info['name']=DB::table('users')->where('id',$ids)->value('chinese_name');
            $user_info['photo']=DB::table('users')->where('id',$ids)->value('avatar');
            $user_info['id']=$ids;
        }
        return $user_info;
    }
    private function get_dept($user_id){
       return DB::table('department_user as du')
            ->leftJoin('departments as ds','du.department_id','=','ds.auto_id')
            ->where('du.user_id','=',$user_id)
            ->get(['du.department_id','ds.name']);
    }
}
