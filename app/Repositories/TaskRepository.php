<?php

namespace App\Repositories;

use App\Http\Helpers\Dh;
use App\Models\Comments\TotalComment;
use App\Models\Feedback\FeedbackContent;
use App\Models\Message\CronPushRecord;
use App\Models\Message\Message;
use App\Models\MyTask\MyTask;
use App\Models\Salary\RewardPunishment;
use App\Models\Schedules\UserSchedules;
use App\Models\Task\Task;
use App\Models\Task\TaskScore;
use App\Models\Task\TaskScoreLog;
use App\Models\VoteParticipant;
use App\Models\Workflow\Entry;
use App\Models\Workflow\Flow;
use App\Models\Workflow\Proc;
use Carbon\Carbon;
use Exception;
use App\Constant\ConstFile;
use App\Repositories\UsersRepository;
use App\Repositories\CommentsRepository;
use DB;
use http\Env;

class TaskRepository extends Repository
{

    public function model()
    {
        return Task::class;
    }

    //创建任务
    public function create_task($data, $userid, $userinfo)
    {
        try {
            $result = ['code' => ConstFile::API_RESPONSE_SUCCESS, 'message' => ConstFile::API_RESPONSE_SUCCESS_MESSAGE];
            $error = $this->checkData($data);
            if ($error) {
                throw new Exception('请求参数错误：' . $error);
            }
            $executorArray = $data['executor_ids'];  //执行人
            unset($data['executor_ids']);


            if ($data['copy_person_ids']) {
                $copypersonArray = $data['copy_person_ids'];  //抄送人
            } else {
                $copypersonArray = '';
            }


//            $relationworkArray = $data['relation_work_ids'];  //关联工作 后续如果需要的话，要在下面的use中引入
//            unset($data['relation_work_ids']);

            DB::transaction(function () use ($data, $userid, $executorArray, $copypersonArray, $userinfo) {

                //执行人
                $exe_arr = explode(',', $executorArray);
                $exe_arr = array_unique($exe_arr);

                //创建任务
                $now_time = Carbon::now()->toDateTimeString();
                $data['create_user_id'] = $userid;
                $data['start_time'] = Carbon::parse($data['start_at'])->toDateTimeString();
                $data['send_time'] = $now_time;
                $task = Task::create($data);
                //创建任务和参与人的对应记录
                $userData = null;
                foreach ($exe_arr as $k => $v) {
                    /*$uname = DB::table('users')->where('id', $v)->value('name');
                    if (!$uname) {
                        return $this->api_result('执行人不存在', 1001);
                    }*/
                    if($v){
                        $userData[] = [
                            'tid' => $task->id,
                            'create_user_id' => $userid,
                            'status' => MyTask::STATUS_WAITING_FOR_PROCESSING,  //待确,
                            'uid' => $v,
                            'pid' => 0,
                            'type_name' => '',
                            'user_type' => MyTask::USER_TYPE_RECEIVE,  //接收人类,
                            'start_time' => Carbon::parse($data['start_at'])->toDateTimeString(),
                            'end_time' => Carbon::parse($data['deadline'])->toDateTimeString(),
                            'created_at' => $now_time,
                            'updated_at' => $now_time,
                        ];
                    }
                }
                DB::table('my_task')->insert($userData);
                //抄送人
                $receiverIds = [];
                if (!empty($copypersonArray)) {
                    $copy_arr = explode(',', $copypersonArray);
                    $copy_arr = array_unique($copy_arr);
                    foreach ($copy_arr as $a => $b) {
                        if($b){
                            $receiverIds[] = $b;
                            $message[] = [
                                'receiver_id' => $b,
                                'sender_id' => $data['create_user_id'],
                                'content' => $data['info'],
                                'type' => Message::MESSAGE_TYPE_CC,
                                'relation_id' => $task->id,
                                'created_at' => $now_time,
                                'updated_at' => $now_time,
                            ];
                        }
                    }
                    DB::table('message')->insert($message);
                }
            });

        } catch (Exception $e) {
            $result['code'] = ConstFile::API_RESPONSE_FAIL;
            $result['message'] = $e->getMessage();
        }
        return $result;
    }

    //待处理的接受、拒绝
    public function is_accept($info, $userid)
    {
        $set['tid'] = $info['task_id'];
        $set['uid'] = $userid;
        $set['status'] = MyTask::STATUS_WAITING_FOR_PROCESSING;
        $res = DB::table('my_task')->where($set)->first();
        if (!$res) {
            return $this->api_result('数据有误', 1001);
        }
        $mat = [];
        if (!in_array($info['is_accept'], [MyTask::STATUS_WAITING_FOR_PROCESSING, MyTask::STATUS_REFUSE])) {
            return $this->api_result('接受类型错误', 1002);
        } else {
            if ($info['is_accept'] == MyTask::STATUS_WAITING_FOR_PROCESSING) {
                $mat['status'] = MyTask::STATUS_WAITING_FOR_HANDLE;   //接受待办理
            }
            if ($info['is_accept'] == MyTask::STATUS_REFUSE) {
                $mat['status'] = MyTask::STATUS_REFUSE;   //拒绝
            }
        }
        $mat['accept_time'] = date("Y-m-d H:i:s", time());
        $update = DB::table('my_task')->where($set)->update($mat);
        if ($update) {
            return $this->api_result('更新成功', ConstFile::API_RESPONSE_SUCCESS);
        }
    }


    public function search_list($info,$userid){

        //我发出的任务
        $result = Task::with('hasManyMyTask')
            ->where('create_user_id', '=', $userid)
            ->where('info','like','%'.$info['keywords'].'%')
            ->get(['id', 'info', 'send_time']);
        $accpet = 0;
        $refuse = 0;
        collect($result)->each(function ($item, $key) use (&$result, $accpet, $refuse) {
            $count = count($item->hasManyMyTask->toArray());
            foreach ($item->hasManyMyTask->toArray() as $value) {
                if ($value['status'] > MyTask::STATUS_WAITING_FOR_PROCESSING) {
                    $result[$key]['accept_person'] .= $value['type_name'] . ',';
                    $accpet++;
                }
                if ($value['status'] == MyTask::STATUS_REFUSE) {
                    $refuse++;
                }
            }
            //接受
            if ($accpet == $count) {
                $result[$key]['accept_count'] = '全部接受';
            } else {
                $result[$key]['accept_count'] = $accpet;
            }
            //拒绝
            if ($refuse == $count) {
                $result[$key]['refuse_count'] = '全部拒绝';
            } else {
                $result[$key]['refuse_count'] = $refuse;
            }
            $result[$key]['type_number'] = 1;       //我发出的
            $result[$key]['sort_time'] = $item->send_time;
            unset($item->hasManyMyTask);
        });
        $res = $result->toArray();

        //我接受的任务
        $sql = DB::select("SELECT a.id,a.info,a.create_user_id,a.send_time,b.type_name,c.comment_time,c.comment_text,d.score FROM task AS a LEFT JOIN my_task AS b ON a.create_user_id = b.create_user_id LEFT JOIN total_comment AS c ON b.id = c.relation_id LEFT JOIN task_score AS d ON c.id = d.pid AND c.type = 1 WHERE a.info LIKE '%{$info['keywords']}%'AND b.uid = {$userid} AND a.id = b.tid AND b.user_type IN (1,2)");
//        dd($sql);
        $arr_b = array_map('get_object_vars', $sql);
        foreach($arr_b as $key => $val){
            $arr_b[$key]['create_name'] = DB::table('users')->where(['id' => $val['create_user_id']])->value('name');
            $arr_b[$key]['type_number'] = 2;
            $arr_b[$key]['sort_time'] = $val['comment_time'] ? strtotime($val['comment_time']) : 0;
            unset($arr_b[$key]['create_user_id']);
        }

        $arr_info = array_merge_recursive($res, $arr_b);
        $last_names = array_column($arr_info, 'sort_time');
        array_multisort($last_names, SORT_ASC, $arr_info);

        foreach ($arr_info as $kay => $kaz) {
            unset($arr_info[$kay]['sort_time']);
            unset($arr_info[$kay]['comment_time']);
        }
        $sst_arr = array_slice($arr_info, ($info['page'] - 1) * $info['limit'], $info['limit']);
        if (empty($sst_arr)) {
            $sst_arr = [];
        }
        return returnJson($message = 'ok', $code = ConstFile::API_RESPONSE_SUCCESS, $sst_arr);
    }


    //上个月的平均分
    public function avg_score($userid, $data)
    {
        if(Carbon::parse($data['dates'])->gte(Carbon::now()->startOfMonth())){
            return returnJson('只能查看本月之前的评分', ConstFile::API_RESPONSE_FAIL);
        }
        $t = Dh::getBeginEndByMonth($data['dates']);
        $start = $t['month_start'] . " 00:00:00";
        $end = $t['month_end'] . " 23:59:59";

        $avgscore = TaskScoreLog::query()
            ->where('user_id', $userid)
            ->where('dates', $t['month_start'])
            ->select(['id', 'score as avgscore'])
            ->first();
        if (!empty($avgscore))
            return returnJson('', ConstFile::API_RESPONSE_SUCCESS, $avgscore);

        //获取上个月总平均分数
        $score = DB::table('my_task as a')
            ->rightJoin('task_score as b', 'a.id', '=', 'b.my_task_id')
            ->where('a.status', MyTask::STATUS_OVER)
            ->where('a.uid', $userid)
            ->whereBetween('b.created_at', [$start, $end])
            ->selectRaw('AVG(score) as avg_score')
            ->pluck('avg_score')
            ->first();
        if (empty($score)) {
            return returnJson('本月暂无评分', ConstFile::API_RESPONSE_FAIL);
        }
        //本月 我分配的任务， 系统自动评分的
        $score_admin = DB::table('my_task as a')
            ->rightJoin('task_score as b', 'a.id', '=', 'b.my_task_id')
            ->where('a.status', MyTask::STATUS_OVER)
            ->where('a.create_user_id', $userid)
            ->where('b.admin_id', MyTask::STATUS_DEFAULT)
            ->whereBetween('b.created_at', [$start, $end])
            ->count();

        $param = [
            'dates' => $t['month_start'],
            'user_id' => $userid,
            'score' => $score - intval($score_admin / 10),
        ];
        $tl = TaskScoreLog::query()->create($param);

        return returnJson($message = 'ok', $code = ConstFile::API_RESPONSE_SUCCESS, ['id' => $tl->id, 'avgscore' => $param['score']]);
    }

    /**
     * @param $info
     * @param $userid
     * @return \Illuminate\Http\JsonResponse|string
     */
    public function task_list($info, $userid)
    {
        $page = $info['page'] ?: 1;
        $limit = $info['limit'] ?: 3;
        $c_nums = ($page - 1) * $limit;
        // type 1.发出的任务 2待确认 3待办理
        if ($info['type'] == 1) {
            $query = Task::with('hasManyMyTask.userByUid')
                ->where('create_user_id', '=', $userid)
                ->orderBy('send_time','desc');
            if(isset($info['keywords']) && $info['keywords']){
                $query->where('info','like','%'.$info['keywords'].'%');
            }
            $result = $query->offset($c_nums)
                ->limit($limit)
                ->get(['id', 'info', 'send_time']);
            $accpet = 0;
            $refuse = 0;
            collect($result)->each(function ($item, $key) use (&$result, $accpet, $refuse) {
                $count = count($item->hasManyMyTask->toArray());
                $accept_person = [];
                foreach ($item->hasManyMyTask->toArray() as $value) {
                    $accept_person[] = $value['user_by_uid']['chinese_name'];
                    if ($value['status'] > MyTask::STATUS_WAITING_FOR_PROCESSING) {
                        $accpet++;
                    }
                    if ($value['status'] == MyTask::STATUS_REFUSE) {
                        $refuse++;
                    }
                }
                $result[$key]['accept_person'] = implode(',', $accept_person);
                //接受
                if ($accpet == $count) {
                    $result[$key]['accept_count'] = '全部接受';
                } else {
                    $result[$key]['accept_count'] = $accpet;
                }
                //拒绝
                if ($refuse == $count) {
                    $result[$key]['refuse_count'] = '全部拒绝';
                } else {
                    $result[$key]['refuse_count'] = $refuse;
                }
                unset($item->hasManyMyTask);
            });
            $list = [
                'data' => $result,
                'count' => $this->task_count($userid),
            ];
            return returnJson($message = 'ok', $code = ConstFile::API_RESPONSE_SUCCESS, $list);

        }

        if ($info['type'] == 2) {
            //1 任务 //2 日程 //4 审批 //3 投票 通知（， 汇报）
            $select_sql = "(SELECT 
                    c.id, a.status, b.chinese_name as create_name, c.info, c.send_time,IFNULL(a.deleted_at,1) as type_num,
                    uid as user_id,c.id as flow_no
                    FROM my_task a 
                    LEFT JOIN users as b ON a.create_user_id = b.id
                    LEFT JOIN task as c ON a.tid = c.id
                    where (a.status = 1 or a.status = 2 or a.status = -1) and a.uid={$userid} and a.deleted_at is null
                    UNION ALL
                    SELECT 
                    id, confirm_yes as status, create_schedule_user_name as create_name, content as info, 
                    created_at as send_time,IFNULL(deleted_at,2) as type_num,create_schedule_user_id as user_id,id as flow_no
                    FROM user_schedules
                    WHERE user_id={$userid} and deleted_at is null
                    UNION ALL
                    SELECT
                    a.id, a.status, c.chinese_name as create_name, b.title as info, b.created_at as send_time,
                    IFNULL(b.deleted_at,4) as type_num, d.show_route_url as user_id, d.flow_no
                    FROM workflow_procs a
                    RIGHT JOIN workflow_entries b ON a.entry_id = b.id
                    RIGHT JOIN users c ON b.user_id = c.id
                    RIGHT JOIN workflow_flows d ON a.flow_id = d.id
                    AND b. STATUS = 0
                    WHERE a.user_id = {$userid} AND a.`status` = 0 AND a.auditor_id = 0 and b.deleted_at is null
                    UNION ALL
                    SELECT
                        a.relation_id AS id,a.`status`,IFNULL(b.chinese_name,'系统提醒') AS create_name,a.content as info,a.created_at as send_time,
                        a.type as type_num,d.show_route_url as user_id, d.flow_no
                    FROM message a
                    LEFT JOIN users b ON a.sender_id = b.id
                    LEFT JOIN workflow_entries c ON a.relation_id = c.id
                    LEFT JOIN workflow_flows d ON c.flow_id = d.id
                    WHERE a.receiver_id = {$userid} AND a.read_status = 0 
                    AND a.type != 0
                    ) a";
            $data = DB::select("select * from {$select_sql} order by a.send_time desc limit {$c_nums}, {$limit}");
            $res['count'] = $this->task_count($userid);
            $res['data'] = json_decode(json_encode($data), true);
            foreach ($res['data'] as $k2 => $p){
                switch ($p['type_num']){
                    case Message::MESSAGE_TYPE_WORK_FLOWS:
                    case Message::MESSAGE_TYPE_URGE:
                        if($p['type_num'] == Message::MESSAGE_TYPE_URGE){
                            $info = Proc::query()
                                ->leftJoin('workflow_entries as c','workflow_procs.entry_id','=','c.id')
                                ->leftJoin('workflow_flows as b','workflow_procs.flow_id','=','b.id')
                                ->where('workflow_procs.id',$p['id'])
                                ->select(['b.show_route_url','b.flow_no','c.status','c.id'])
                                ->first();
                            $res['data'][$k2]['user_id'] = Q($info, 'show_route_url');
                            $res['data'][$k2]['flow_no'] = Q($info, 'flow_no');

                            if(Q($info, 'status') == Entry::STATUS_IN_HAND){
                                $res['data'][$k2]['user_id'] .= "id={$p['id']}&qx=1";
                            }else{
                                $res['data'][$k2]['user_id'] .= "id=".Q($info, 'id')."&qx=2";
                            }
                        }else{
                            $res['data'][$k2]['user_id'] .= "id={$p['id']}&qx=1";
                        }

                        if (in_array($p['flow_no'], ['fee_expense', 'finance_loan', 'finance_repayment', 'finance_receivables', 'finance_payment'])) {
                            $res['data'][$k2]['url'] = route('api.finance.auditor_flow.show') . "?id=" . $p['id'];
                            $res['data'][$k2]['user_id'] .= "id={$p['id']}&qx=1";
                        } elseif (in_array($p['flow_no'], ['positive_apply', 'positive_wage_apply'])) {
                            $res['data'][$k2]['url'] = route('api.positive.auditor_flow.show') . "?id=" . $p['id'];
                        } elseif (in_array($p['flow_no'], ['official_contract'])) {
                            $res['data'][$k2]['url'] = route('api.administrative.contract.auditor_flow.show') . "?id=" . $p['id'];
                        } elseif (in_array($p['flow_no'], ['intelligence_apply'])) {
                            $res['data'][$k2]['url'] = route('api.inte.auditor_flow.show') . "?id=" . $p['id'];
                        } elseif (in_array($p['flow_no'], ['inspector_apply'])) {
                            $res['data'][$k2]['url'] = route('api.insp.auditor_flow.show') . "?id=" . $p['id'];
                        } elseif (in_array($p['flow_no'], Entry::CUSTOMIZE)) {
                            $res['data'][$k2]['url'] = route('api.flow.customize.auditor.flow.show') . "?id=" . $p['id'];
                        }  elseif (in_array($p['flow_no'], ['meeting_record_review'])) {//会议工作流
                            $res['data'][$k2]['url'] = route('meeting.meetingreviewedinfo') . "?id=" . $p['id'];
                        }  elseif (in_array($p['flow_no'], ['pas_purchase'])) {//进销存采购单
                            $res['data'][$k2]['url'] = route('purchase.gettrialinfo') . "?id=" . $p['id'];
                        }  elseif (in_array($p['flow_no'], ['pas_return_order'])) {//进销存退货单
                            $res['data'][$k2]['url'] = route('returnOrder.getinfotow') . "?id=" . $p['id'];

                        } elseif (in_array($p['flow_no'], ['pas_payment_order'])) {//进销存入库单
                            $res['data'][$k2]['url'] = route('payment.getinfotow') . "?id=" . $p['id'];
                        }else {
                            $res['data'][$k2]['url'] = route('api.auditor_flow.show') . "?id=" . $p['id'];
                        }
                        continue;
                    case Message::MESSAGE_TYPE_REPORT:
                        $res['data'][$k2]['user_id'] = "reportContent?id={$p['id']}";
                        continue;
                    case Message::MESSAGE_TYPE_WORKFLOW_PASS:
                    case Message::MESSAGE_TYPE_WORKFLOW_REJECT:
                        $res['data'][$k2]['user_id'] .= "id={$p['id']}&qx=2";
                        continue;
                    case Message::MESSAGE_TYPE_MEETING:
                        $res['data'][$k2]['user_id'] .= "meetingDetails?id={$p['id']}";
                        continue;
                    case Message::MESSAGE_TYPE_TASK_URGE:
                        $res['data'][$k2]['user_id'] = 'taskDetail?task_id='.$p['id'].'&type=2';
                        continue;
                    case Message::MESSAGE_TYPE_CC:
                        $res['data'][$k2]['user_id'] = 'taskDetail?task_id='.$p['id'].'&type=1';
                        continue;
                    case Message::MESSAGE_TYPE_ACHIEVEMENTS_ONE:
                        $res['data'][$k2]['user_id'] = 'performanceAccomplish?id='.$p['id'].'&qx=1';
                        continue;
                    case Message::MESSAGE_TYPE_USER_MEETING:
                        $res['data'][$k2]['user_id'] = 'meetingDetails?id='.$p['id'].'&qx=4';
                        continue;
                    case Message::MESSAGE_TYPE_REWARDPUNISHMENT:
                        $res['data'][$k2]['user_id'] = 'ADsupervise/punishDetail?id='.$p['id'];
                        continue;
                    case Message::MESSAGE_TYPE_REWARDPUNISHMENT0NE:
                        $res['data'][$k2]['user_id'] = 'ADsupervise/urge?id='.$p['id'];
                        continue;
                    case Message::MESSAGE_TYPE_CUSTOMER_TASK:
                        $res['data'][$k2]['user_id'] = env('RPC_CUS_LOCAL_DOMAIN') . '/index/details/missionpipinfo.html?id='.$p['id'];
                        continue;
                    case Message::MESSAGE_IMPROVING_DATA:
                        $res['data'][$k2]['user_id'] = 'file?';
                        continue;
                    case Message::MESSAGE_TYPE_TURN_POSITIVE:
                        $res['data'][$k2]['user_id'] = 'employeeApplicationCorrection?id='.$p['id'];
                        continue;
                }
            }
            return returnJson($message = 'ok', $code = ConstFile::API_RESPONSE_SUCCESS, $res);

        }

        if ($info['type'] == 3) {
            //任务
            $nowtime = Carbon::now()->toDateTimeString();
            $select_sql = "(SELECT 
                            c.id,b.chinese_name as create_name,c.info,a.accept_time as send_time,IFNULL(a.deleted_at,1) as type_num,uid as user_id
                            FROM my_task a 
                            LEFT JOIN users as b ON a.create_user_id = b.id
                            LEFT JOIN task as c ON a.tid = c.id
                            where a.status = 2 and a.uid={$userid}
                            UNION ALL
                            SELECT 
                            a.id, c.chinese_name as create_name, 
                            a.vote_title as info, IFNULL(b.created_at,0) as send_time,IFNULL(b.deleted_at,3) as type_num,b.user_id
                            FROM vote a
                            LEFT JOIN vote_participant b ON a.id=b.v_id
                            LEFT JOIN users c ON b.create_vote_user_id=c.id
                            WHERE b.confirm_yes = 0 and b.user_id={$userid} and a.end_at >= '{$nowtime}') a";
            $res['data'] = DB::select("select * from {$select_sql} order by a.send_time desc limit {$c_nums}, {$limit}");
            $res['count'] = $this->task_count($userid);
            return returnJson($message = 'ok', $code = ConstFile::API_RESPONSE_SUCCESS, $res);

        }

        if ($info['type'] == 4) {
            $info['type_number'] = isset($info['type_number']) ? $info['type_number'] : 1;
            $res['page'] = 0;
            $res['count'] = $this->task_count($userid);
            if($info['type_number'] == 1){
                $res['data'] = $this->getListByCreateUserId($userid,$c_nums,$limit);
                if(empty($res['data'])){
                    $res['page'] = $page - 1;
                    $res['data'] = $this->getListByUid($userid,0,$limit);
                    //$res['data'] = [];
                }
            }else{
                $res['data'] = $this->getListByUid($userid,$c_nums,$limit);
                //$res['data'] = [];
            }

            return returnJson($message = 'ok', $code = ConstFile::API_RESPONSE_SUCCESS, $res);
        }
    }


    public function getListByCreateUserId($userid,$c_nums,$limit){
        $select_sql = "SELECT 
                            b.type,a.id,a.end_time,d.info,a.create_user_id,d.send_time,f.chinese_name as type_name,
                            b.comment_time,b.comment_text,c.score,e.`chinese_name` as create_name,
                            IFNULL(a.deleted_at,1) as type_number,
                            IFNULL(c.score, -1) as score_status,
                            d.id as task_id, d.enclosure,d.enclosure_img
                        FROM my_task as a
                        LEFT JOIN total_comment as b ON a.id = b.relation_id
                        LEFT JOIN task_score as c ON a.id = c.my_task_id
                        LEFT JOIN task as d ON a.tid = d.id
                        LEFT JOIN users as e ON a.create_user_id = e.id
                        LEFT JOIN users as f ON a.uid = f.id
                        WHERE a.create_user_id = {$userid}
                        AND (a.`status` = 3 or a.`status` = 4)
                        AND b.type=13
                        order by a.`status` asc, comment_time asc, id desc";
        return DB::select($select_sql . " limit {$c_nums}, {$limit}");
        /*$select_sql = "SELECT
                          b.id,b.end_time,a.info,a.create_user_id,a.send_time,f.chinese_name as type_name,
                          g.comment_time,g.comment_text,d.score,e.`chinese_name` as create_name,
                          IFNULL(b.deleted_at,1) as type_number,
                          IFNULL(d.score, -1) as score_status,
                          a.id as task_id, a.enclosure,a.enclosure_img
                        FROM task AS a
                        LEFT JOIN my_task AS b ON a.create_user_id = b.create_user_id
                        LEFT JOIN total_comment AS c ON b.id = c.relation_id
                        LEFT JOIN task_score AS d ON c.id = d.pid
                        LEFT JOIN users AS e ON e.id = a.create_user_id
                        LEFT JOIN users AS f ON f.id = b.uid
                        LEFT JOIN total_comment AS g ON b.id = g.relation_id
                        WHERE a.create_user_id = {$userid}
                        AND c.type = 1
                        AND a.id = b.tid
                        AND b.user_type IN (1, 2)
                        AND (b.`status` = 3 or b.`status` = 4)
                        order by b.`status` asc, comment_time asc, id desc";
        return DB::select($select_sql . " limit {$c_nums}, {$limit}");*/
    }

    public function getListByUid($userid,$c_nums,$limit){
        $select_sql = "SELECT 
                            b.type,a.id,a.end_time,d.info,a.create_user_id,d.send_time,f.chinese_name as type_name,
                            b.comment_time,b.comment_text,c.score,e.`chinese_name` as create_name,
                            IFNULL(a.deleted_at,2) as type_number,
                            IFNULL(c.score, -1) as score_status,
                            d.id as task_id, d.enclosure,d.enclosure_img
                        FROM my_task as a
                        LEFT JOIN total_comment as b ON a.id = b.relation_id
                        LEFT JOIN task_score as c ON a.id = c.my_task_id
                        LEFT JOIN task as d ON a.tid = d.id
                        LEFT JOIN users as e ON a.create_user_id = e.id
                        LEFT JOIN users as f ON a.uid = f.id
                        WHERE a.uid = {$userid}
                        AND (a.`status` = 3 or a.`status` = 4)
                        AND b.type=13
                        order by a.`status` asc, comment_time asc, id desc";
        return DB::select($select_sql . " limit {$c_nums}, {$limit}");
    }

    //统计
    public function task_count($user_id){
        $my_task = MyTask::query()
            ->where('uid', $user_id)
            ->where(function ($query){
                $query->orWhere('status', MyTask::STATUS_WAITING_FOR_PROCESSING)
                    ->orWhere('status', MyTask::STATUS_WAITING_FOR_HANDLE)
                    ->orWhere('status', MyTask::STATUS_REFUSE);
            })->count();
        $user_schedules = UserSchedules::query()
            ->where('user_id', $user_id)
            ->count();
        $message = Message::query()
            ->where('receiver_id', $user_id)
            ->where('read_status', Message::READ_STATUS_NO)
            ->where('type','!=',Message::MESSAGE_TYPE_NORMAL)
            ->count();
        $proc = DB::table('workflow_procs as a')
            ->rightJoin('workflow_entries as b','a.entry_id','=','b.id')
            ->rightJoin('users as c','b.user_id','=','c.id')
            ->rightJoin('workflow_flows as d','a.flow_id','=','d.id')
            ->where('a.user_id', $user_id)
            ->where('a.status', Proc::STATUS_IN_HAND)
            ->where('a.auditor_id', MyTask::STATUS_DEFAULT)
            ->where('b.STATUS', Entry::STATUS_IN_HAND)
            ->count();

        $count['type_2'] = $my_task + $user_schedules + $message + $proc;

        $my_task2 = MyTask::query()
            ->where('status',MyTask::STATUS_WAITING_FOR_HANDLE)
            ->where('uid', $user_id)
            ->count();
        $vote_participant = VoteParticipant::query()
            ->rightJoin('vote as a', 'a.id','=','v_id')
            ->where('confirm_yes', VoteParticipant::IS_CONFIRM_NO)
            ->where('user_id', $user_id)
            ->where('a.end_at','>=', Carbon::now()->toDateTimeString())
            ->count();
        $count['type_3'] = $my_task2 + $vote_participant;

        $count['type_4'] = MyTask::query()
            ->leftJoin('total_comment','total_comment.relation_id','=','my_task.id')
            ->where('my_task.status', MyTask::STATUS_WAITING_FOR_COMMENT)
            ->where('my_task.user_type', MyTask::USER_TYPE_RECEIVE)
            ->where('my_task.create_user_id', $user_id)
            ->where('total_comment.type', TotalComment::TYPE_MY_TASK_HANDLE_COMMENT)
            //->whereNull('total_comment.comment_time')
            ->count();
        return $count;
    }

    //办理完成
    public function handle($info, $user_id)
    {
        $info['task_id'] = intval($info['task_id']);
        if(!$info['task_id'] || !$info['success_info'])
            return returnJson(ConstFile::API_PARAMETER_MISS, ConstFile::API_RESPONSE_FAIL);

        $my_task = MyTask::query()
            ->where('tid', intval($info['task_id']))
            ->where('uid', $user_id)
            ->where('status', MyTask::STATUS_WAITING_FOR_HANDLE)
            ->first();
        if (!$my_task)
            return $this->api_result('数据有误', 1002);

        try{
            DB::transaction(function () use ($my_task, $info, $user_id) {

                $my_task->finish_time = Carbon::now()->toDateTimeString();
                $my_task->status = MyTask::STATUS_WAITING_FOR_COMMENT;  //变成待评价
                $my_task->save();

                TotalComment::query()->create([
                    'type' => TotalComment::TYPE_MY_TASK_HANDLE_COMMENT,
                    'relation_id' => $my_task->id,
                    'uid' => $user_id,
                    'comment_text' => $info['success_info'],
                    'comment_time' => Carbon::now()->toDateTimeString(),
                ]);
            });
            return $this->api_result('更新成功', ConstFile::API_RESPONSE_SUCCESS);
            /*$where['tid'] = $info['task_id'];
            $where['uid'] = $userid;
            $where['status'] = MyTask::STATUS_WAITING_FOR_HANDLE;
            $mast = DB::table('my_task')->where($where)->first();
            if (!$mast) {
                return $this->api_result('数据有误', 1002);
            }
            $data['finish_time'] = date("Y-m-d H:i:s", time());
            $data['status'] = MyTask::STATUS_WAITING_FOR_COMMENT;   //变成待评价
            $res = DB::table('my_task')->where($where)->update($data);
            if ($res) {
                return $this->api_result('更新成功', ConstFile::API_RESPONSE_SUCCESS);
            } else {
                return $this->api_result('更新失败', 1001);
            }*/
        }catch (Exception $e){
            return returnJson($e->getCode(), $e->getMessage());
        }
    }

    public function task_detail($info, $userid)
    {
        //type=1 我发出的信息  type=2 我收到的信息
        $info['task_id'] = isset($info['task_id']) ? $info['task_id'] : $info['id'];
        if ($info['type'] == 1) {
            $result = Task::with('hasManyMyTask.userByUid')
                //->where('create_user_id', '=', $userid)
                ->where('id', $info['task_id'])
                ->select(['id', 'create_user_id', 'info', 'enclosure', 'send_time', 'deadline', 'start_time','enclosure_img'])
                ->first();

            if(empty($result) || !$result->hasManyMyTask)
                return returnJson('参数错误', ConstFile::API_RESPONSE_FAIL);

            $accpet = 0;
            $refuse = 0;
            $count = count($result->hasManyMyTask);
            $accept_person = $success = $no_success = [];

            foreach ($result->hasManyMyTask as $value) {
                $accept_person[] = Q($value, 'userByUid','chinese_name');
                if ($value['status'] > MyTask::STATUS_WAITING_FOR_PROCESSING) {
                    $accpet++;
                }
                if ($value['status'] == MyTask::STATUS_REFUSE) {
                    $refuse++;
                }
                //执行状态
                if ($value['status'] == MyTask::STATUS_WAITING_FOR_HANDLE) {
                    $no_success[] = Q($value, 'userByUid','chinese_name');
                }
                if ($value['status'] == MyTask::STATUS_WAITING_FOR_COMMENT) {
                    $success[] = Q($value, 'userByUid','chinese_name');
                }
            }
            $result->accept_person = implode(',', $accept_person);
            $result->no_success = implode(',', $no_success);
            $result->success = implode(',', $success);
            //逾期
            $overdue = Carbon::now()->timestamp - Carbon::parse($result->deadline)->timestamp;
            if($overdue > 0){
                $result->overdue = ceil($overdue / 86400);
            }else{
                $result->overdue = 0;
            }
            //接受
            if ($accpet == $count) {
                $result->accept_count = '全部接受';
            } else {
                $result->accept_count = $accpet;
            }
            //拒绝
            if ($refuse == $count) {
                $result->refuse_count = '全部拒绝';
            } else {
                $result->refuse_count = $refuse;
            }

            unset($result->hasManyMyTask);
            return returnJson($message = 'ok', $code = ConstFile::API_RESPONSE_SUCCESS, $result);
        }

        if ($info['type'] == 2) {
            $result = MyTask::query()
                ->where('uid', $userid)
                ->where('tid', $info['task_id'])
                ->where('user_type', MyTask::USER_TYPE_RECEIVE)
                ->where('status','>=', MyTask::STATUS_WAITING_FOR_HANDLE)
                ->with(['user','task'])
                ->first();
            if(empty($result))
                return returnJson('任务已处理', ConstFile::API_RESPONSE_FAIL);

            //逾期
            $overdue = Carbon::now()->timestamp - Carbon::parse($result->end_time)->timestamp;
            if($overdue > 0){
                $result_overdue = ceil($overdue / 86400);
            }else{
                $result_overdue = 0;
            }

            $task_info = [
                'id' => $result->tid,
                'sender_name' => Q($result, 'user', 'chinese_name'),
                'send_time' => Q($result, 'task', 'send_time'),
                'info' => Q($result, 'task', 'info'),
                'enclosure' => Q($result, 'task', 'enclosure'),
                'deadline' => Q($result, 'task', 'deadline'),
                'start_time' => $result->start_time,
                'enclosure_img' => Q($result, 'task', 'enclosure_img'),
                'overdue' => $result_overdue,
            ];
            return returnJson('', ConstFile::API_RESPONSE_SUCCESS, $task_info);
        }
    }

    public function task_status($info, $userid)
    {
        $res = MyTask::query();
        //  -1.拒绝 0.未接受 1.已接受 2.已完成
        switch ($info['status']) {
            case MyTask::STATUS_REFUSE:
                $res->where('status', MyTask::STATUS_REFUSE);
                break;
            case MyTask::STATUS_DEFAULT:
                $res->where('status', MyTask::USER_TYPE_RECEIVE);
                break;
            case MyTask::USER_TYPE_RECEIVE:
                $res->where('status', MyTask::STATUS_WAITING_FOR_HANDLE);
                break;
            case MyTask::STATUS_WAITING_FOR_HANDLE:
                $where['status'] = MyTask::STATUS_WAITING_FOR_COMMENT;
                $res->where('status', MyTask::STATUS_WAITING_FOR_COMMENT);
                break;
            default:
                echo "状态有误，请检查！";
        }
        $arr = [];
        $res->where('user_type', MyTask::USER_TYPE_RECEIVE)
            ->where('tid', $info['task_id'])
            ->where('create_user_id', $userid)
            ->with('userByUid')->get()
            ->each(function ($item, $key) use (&$arr){
                $arr[$key]['avatar'] = Q($item, 'userByUid', 'avatar');
                $arr[$key]['name'] = Q($item, 'userByUid', 'chinese_name');
            });
        return returnJson('获取成功', ConstFile::API_RESPONSE_SUCCESS, $arr);
    }

    public function get_type($info, $userid)
    {
        if ($info['type'] == 1) {         //我完成的任务，不显示分数
            $where['uid'] = $userid;
            $where['status'] = MyTask::STATUS_WAITING_FOR_COMMENT;
            $res = DB::table('my_task')->where($where)->get();
            if (!$res) {
                return $this->api_result('数据不存在', 1001);
            }
            $arr_info = array_map('get_object_vars', $res->toArray());
            foreach ($arr_info as $c => $d) {
                $met['id'] = $d['tid'];
                $met['create_user_id'] = $d['create_user_id'];
                $restb = DB::table('task')->where($met)->get();
                $rest = array_map('get_object_vars', $restb->toArray());
                foreach ($rest as $ke => $va) {
                    $arr_info[$c]['send_time'] = $va['send_time'];
                    $arr_info[$c]['info'] = $va['info'];
                    $arr_info[$c]['enclosure'] = $va['enclosure'];
                    $arr_info[$c]['deadline'] = $va['deadline'];
                }
            }
            if (!empty($arr_info)) {
                return $this->api_result('获取成功', ConstFile::API_RESPONSE_SUCCESS, $arr_info);
            } else {
                return $this->api_result('暂无数据', 1001);
            }
        }

        //我分配的任务，别人已完成的，带分数
        if ($info['type'] == 2) {
            $com = app()->make(CommentsRepository::class);
            $re = DB::table('task')
                ->leftJoin('my_task', 'task.id', '=', 'my_task.tid')
                ->where('task.create_user_id', '=', $userid)
                ->where('my_task.status', '=', MyTask::STATUS_WAITING_FOR_COMMENT)
                ->get(['task.id', 'task.info', 'task.send_time', 'my_task.success_info']);
            $ret = array_map('get_object_vars', $re->toArray());
            if ($info['status'] == 1) {           //有分数的
                $sss = [];
                foreach ($ret as $k => $v) {
                    $datas['o_id'] = $v['id'];
                    $score = $com->getscorebyoid($datas);
                    if ($score == 0) {
                        continue;
                    } else {
                        $sss[$k]['id'] = $v['id'];
                        $sss[$k]['info'] = $v['info'];
                        $sss[$k]['send_time'] = $v['send_time'];
                        $sss[$k]['success_info'] = $v['success_info'];
                        $sss[$k]['score'] = $score;
                    }
                }
                if (!empty($sss)) {
                    return $this->api_result('获取成功', ConstFile::API_RESPONSE_SUCCESS, $sss);
                } else {
                    return $this->api_result('暂无数据', 1001);
                }

            }

            if ($info['status'] == 2) {           //没有分数
                $mac = [];
                foreach ($ret as $kk => $vv) {
                    $datas['o_id'] = $vv['id'];
                    $score = $com->getscorebyoid($datas);
                    if ($score > 0) {
                        continue;
                    } else {
                        $mac[$kk]['id'] = $vv['id'];
                        $mac[$kk]['info'] = $vv['info'];
                        $mac[$kk]['send_time'] = $vv['send_time'];
                        $mac[$kk]['success_info'] = $vv['success_info'];
                        $mac[$kk]['score'] = '暂无评分';
                    }
                }
                if (!empty($mac)) {
                    return $this->api_result('获取成功', ConstFile::API_RESPONSE_SUCCESS, $mac);
                } else {
                    return $this->api_result('暂无数据', 1001);
                }
            }
        }
    }

    /*
     * 评分申诉
     * */
    public function scoreAppeal($data, $userid){
        $score_info = TaskScoreLog::query()->find($data['score_id']);
        if (empty($score_info))
            return returnJson('参数错误', ConstFile::API_RESPONSE_FAIL);

        $param = [
            'tid' => FeedbackContent::TYPE_APPEAL,
            'title' => '评分申诉 - '. $score_info->dates,
            'content' => $data['content'],
            'way' => FeedbackContent::WAY_REALNAME,
            'publish_time' => Carbon::now()->toDateTimeString(),
            'status' => FeedbackContent::STATUS_UNANSWERED,
            'uid' => $userid,
            'image' => isset($data['image']) ? $data['image'] : "",
            'relation_type' => FeedbackContent::RELATION_SCORE,
            'relation_id' => $score_info->id,
        ];
        try{
            FeedbackContent::query()->create($param);
            return returnJson('', ConstFile::API_RESPONSE_SUCCESS);
        }catch (Exception $e){
            return returnJson($e->getCode(), $e->getMessage());
        }
    }

    /*
     * 评分申诉 列表
     * */
    public function scoreAppealListByUserId($userid){
        try{
            $list = FeedbackContent::query()
                ->where('tid', FeedbackContent::TYPE_APPEAL)
                ->where('uid', $userid)
                ->where('relation_type', FeedbackContent::RELATION_SCORE)
                ->orderBy('id', 'desc')
                ->get();
            return returnJson('', ConstFile::API_RESPONSE_SUCCESS, $list);
        }catch (Exception $e){
            return returnJson($e->getCode(), $e->getMessage());
        }
    }

    /*
     * 催办
     * */
    public function urgeSave($info, $userid){
        try{
            $res = Task::query()
                ->where('id', $info['task_id'])
                ->where('create_user_id', $userid)
                ->first();
            if(empty($res))
                return returnJson('参数错误', ConstFile::API_RESPONSE_FAIL);
            $list = [];
            if($res->hasManyMyTask){
                foreach ($res->hasManyMyTask as $v){
                    if(in_array($v->status, [MyTask::STATUS_DEFAULT,MyTask::USER_TYPE_RECEIVE,MyTask::STATUS_WAITING_FOR_HANDLE])){
                        $list[] = [
                            'receiver_id' => $v->uid,
                            'sender_id' => $userid,
                            'content' => $res->info,
                            'status' => Message::MESSAGE_STATUS_ORDINARY,
                            'type' => Message::MESSAGE_TYPE_URGE,
                            'relation_id' => $v->id,
                            'created_at' => Carbon::now()->toDateTimeString(),
                            'updated_at' => Carbon::now()->toDateTimeString(),
                        ];
                    }
                }
            }
            DB::table('message')->insert($list);
            return returnJson('', ConstFile::API_RESPONSE_SUCCESS);
        }catch (Exception $e){
            return returnJson($e->getMessage(), $e->getCode());
        }
    }

    /*
     * 催办By id
     * */
    public function urgeSaveById($info, $userid){
        try{
            $res = MyTask::query()
                ->where('id', $info['id'])
                ->first();
            if(empty($res))
                return returnJson('参数错误', ConstFile::API_RESPONSE_FAIL);
            Message::addMessage($res->uid, $userid, $res->task->info, $res->tid, Message::MESSAGE_TYPE_TASK_URGE);
            return returnJson('', ConstFile::API_RESPONSE_SUCCESS);
        }catch (Exception $e){
            return returnJson($e->getMessage(), $e->getCode());
        }
    }

    /*
     * 获取平均分的计算来源。 查看任务
     * */
    public function taskAvgScoreByMonth($data, $userid){
        $t = Dh::getBeginEndByMonth($data['dates']);
        $t['month_start'] = $t['month_start']." 00:00:00";
        $t['month_end'] = $t['month_end']." 23:59:59";

        //获取上个月总平均分数
        $score = DB::table('my_task as a')
            ->rightJoin('task_score as b', 'a.id', '=', 'b.my_task_id')
            ->leftJoin('users as c','a.uid','=','c.id')
            ->leftJoin('users as d','a.create_user_id','=','d.id')
            ->leftJoin('task as e','a..tid','=','e.id')
            ->where('a.status', MyTask::STATUS_OVER)
            ->where('a.uid', $userid)
            ->whereBetween('b.created_at', $t)
            ->select(['a.id','e.info','e.send_time','d.chinese_name as create_name','c.chinese_name as accept_name'])
            ->get();
        //本月 我分配的任务， 系统自动评分的
        $score_admin = DB::table('my_task as a')
            ->rightJoin('task_score as b', 'a.id', '=', 'b.my_task_id')
            ->leftJoin('users as c','a.uid','=','c.id')
            ->leftJoin('users as d','a.create_user_id','=','d.id')
            ->leftJoin('task as e','a..tid','=','e.id')
            ->where('a.status', MyTask::STATUS_OVER)
            ->where('a.create_user_id', $userid)
            ->where('b.admin_id', MyTask::STATUS_DEFAULT)
            ->whereBetween('b.created_at', $t)
            ->select(['a.id','e.info','e.send_time','d.chinese_name as create_name','c.chinese_name as accept_name'])
            ->get();
        return returnJson('',
            ConstFile::API_RESPONSE_SUCCESS,
            $score->concat($score_admin));
    }

    /*
     * 点击待办的通知事项， 改为已读
     * */
    public function changeReadStatus($data, $user_id){
        try{
            $info = Message::query()
                ->where('receiver_id', $user_id)
                ->where('type', $data['type'])
                ->where('relation_id', $data['id'])
                ->where('read_status', Message::READ_STATUS_NO)
                ->first();
            if(empty($info))
                return returnJson('没有找到对应的数据', ConstFile::API_RESPONSE_FAIL);
            $info->read_status = Message::READ_STATUS_YES;
            $info->save();
            return returnJson('', ConstFile::API_RESPONSE_SUCCESS);
        }catch (Exception $e){
            return returnJson($e->getCode(), $e->getMessage());
        }

    }

    private function checkData($data)
    {
        if (!isset($data['info']) || empty($data['info'])) {
            return '任务内容不能为空';
        }
//        if (!isset($data['send_type']) || empty($data['send_type'])) {
//            return '发送类型不能为空';
//        }
//        if (!isset($data['deadline']) || empty($data['deadline'])) {
//            return '截止时间不能为空';
//        }
//        return null;
    }

    function api_result($msg, $code = 0, $data = [])
    {
        if ($data) {
            return ['code' => $code, 'msg' => $msg, 'data' => $data];
        } else {
            return ['code' => $code, 'msg' => $msg];
        }
    }
}
