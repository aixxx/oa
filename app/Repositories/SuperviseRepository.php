<?php

namespace App\Repositories;

use App\Models\Attention;
use App\Models\Message\Message;
use App\Models\MyTask\MyTask;
use App\Models\Task\Task;
use App\Models\User;
use Exception;
use App\Constant\ConstFile;
use DB;
use Carbon\Carbon;
use App\Models\Supervise;

class SuperviseRepository extends Repository
{
    public function __construct(){
        //关注关联表名 [1:任务]
        $this->s_table = [
            '1' => 'my_task'
        ];
    }

    public function model()
    {
        return Task::class;
    }

    /*
     * 督办列表
     * */
    public function superviseList($param, $user){
        //权限判断
        try{
            $condition = $this->listCondition($param, $user);//验证数据

            $list = MyTask::where([['my_task.user_type', '=', 1],['my_task.status', '>', 0]])->whereNull('my_task.deleted_at')
                ->leftJoin('users', 'my_task.uid', '=', 'users.id')
                ->leftJoin('task', 'task.id', '=', 'my_task.tid')
                ->select('my_task.*', 'task.info', 'task.deadline', 'users.name', 'users.employee_num', 'users.chinese_name', 'users.position', 'users.avatar')->orderBy('my_task.id', 'desc');

            if(!empty($condition['whereRaw'])){
                $list->whereRaw($condition['whereRaw']);
            }
            if(!empty($condition['where'])){
                $list->where($condition['where']);
            }
            if(!empty($condition['where_in'])){
                foreach ($condition['where_in'] as $v){
                    $list->whereIn($v[0], $v[1]);
                }
            }
            $list = $list->paginate($param['limit'])->toArray();

            $time = date('Y-m-d H:i:s', time());
            foreach ($list['data'] as &$v){
                $v['is_show_supervise'] = ($v['status'] < 3) ? 1 : 0;//未完成做催办
                $v['is_show_punish'] = ($v['status'] >= 3 && $v['end_time'] < $v['finish_time']) ? 1 : 0;//超时完成才处罚
                $v['is_supervise'] = (!empty($condition['supervise']) && in_array($v['id'], $condition['supervise'])) ? 1 : 0;
                $v['is_attention'] = (!empty($condition['attention']) && in_array($v['id'], $condition['attention'])) ? 1 : 0;
                //$v['status_name'] = ($v['status'] < 3 && $v['end_time'] < $time ? '已超时' : (($v['status'] >= 3 && $v['finish_time'] > $v['end_time']) ? '超时完成' : ConstFile::$taskStatus[$v['status']]));
                //$v['tag'] = ($v['status'] < 3 && $v['end_time'] < $time ) ? '已逾期'.ceil(((time()-strtotime($v['end_time']))/(3600*24))).'天' : '';

                $v['tag'] = '';
                if($v['status'] < 3 && $v['end_time'] < $time){
                    $v['tag'] = '已逾期'.ceil(((time()-strtotime($v['end_time']))/(3600*24))).'天';
                }else if($v['status'] >= 3 && $v['end_time'] < $v['finish_time']){
                    $v['tag'] = '已逾期'.ceil((strtotime($v['finish_time'])-strtotime($v['end_time']))/(3600*24)).'天';
                }

                $v['status_name'] = '';
                $v['show_status'] = 1;
                if($v['status'] == 2){
                    if($v['start_time'] > $time){
                        $v['status_name'] = '未开始';
                        $v['show_status'] = 1;
                    }else{
                        $v['status_name'] = '处理中';
                        $v['show_status'] = 2;
                    }
                }else if($v['status'] == 3){
                    if($v['finish_time'] > $v['deadline']){
                        $v['status_name'] = '超时完成';
                        $v['show_status'] = 6;
                    }else{
                        $v['status_name'] = '已完成';
                        $v['show_status'] = 3;
                    }
                }else if($v['status'] == 4){
                    if((strtotime($v['comment_time']) - strtotime($v['updated_at'])) > 7*24*3600){
                        $v['status_name'] = '超时评价';
                        $v['show_status'] = 5;
                    }else{
                        $v['status_name'] = '已评价';
                        $v['show_status'] = 4;
                    }
                }
            }

            $param['stime_show'] = isset($param['stime']) ? date('Y年m月d日', strtotime($param['stime'])) : '';
            $param['etime_show'] = isset($param['etime']) ? date('Y年m月d日', strtotime($param['etime'])) : '';
            $param['status_note'] = isset($param['status']) ? ConstFile::$taskStatus[$param['status']] : '';

            $result['task_status'] = ConstFile::$taskStatus;
            $result['param'] = $param;
            $result['total'] = $list['total'];
            $result['total_page'] = $list['last_page'];
            $result['data'] = $list['data'];

            return returnJson(ConstFile::API_RESPONSE_SUCCESS_MESSAGE_DATA,ConstFile::API_RESPONSE_SUCCESS,$result);
        } catch (Exception $e){
            return returnJson($e->getMessage(), $e->getCode());
        }
    }


    /*
     * 添加取消督办
     * */
    public function addCancelSupervise($data, $user){
        try{
            if(empty($data['type'])){
                return returnJson('请填写督办类型', ConstFile::API_RESPONSE_FAIL);
            }
            if(empty($data['relate_id'])){
                return returnJson('请选择督办的内容', ConstFile::API_RESPONSE_FAIL);
            }

            $info = DB::table($this->s_table[$data['type']])->where('id', $data['relate_id'])->whereNull('deleted_at')->first();
            if(empty($info)){
                return returnJson(ConstFile::API_RECORDS_NOT_EXIST_MESSAGE, ConstFile::API_RESPONSE_FAIL);
            }

            $likes = Supervise::where([['relate_id', $data['relate_id']],['user_id', $user->id],['type', $data['type']]])->first();
            if(!empty($likes)){
                //已存在，更新数据
                if($likes['deleted_at'] > 0){
                    //更新点赞
                    $da['deleted_at'] = '';
                    $da['created_at'] = Carbon::now();
                }else{
                    //取消点赞
                    $da['deleted_at'] = Carbon::now();
                }
                $res = Supervise::find($likes['id'])->fill($da)->save();
            }else{
                //不存在，增加数据
                $da = [
                    'user_id' => $user->id,
                    'relate_id' => $data['relate_id'],
                    'type' => $data['type']
                ];
                $res = Supervise::create($da);
            }

            if(empty($res)){
                return returnJson(ConstFile::API_RESPONSE_FAIL_MESSAGE, ConstFile::API_RESPONSE_FAIL);
            }else{
                return returnJson(ConstFile::API_RESPONSE_SUCCESS_MESSAGE);
            }
        } catch (Exception $e){
            return returnJson($e->getMessage(), $e->getCode());
        }
    }


    /*
     * 督办统计
     * */
    public function superviseStatistics($param, $user){
        try{
            if(empty($param['time'])){
                return returnJson('请选择统计的日期', ConstFile::API_RESPONSE_FAIL);
            }
            if(empty($param['type'])){
                return returnJson('请选择统计的类型', ConstFile::API_RESPONSE_FAIL);
            }

            $time = strtotime($param['time']);
            $now_time = date('Y-m-d H:i:s', time());
            $stime = date('Y-m-d H:i:s', mktime(0,0,0, date('m', $time),1,date('Y', $time)));
            $etime = date('Y-m-d H:i:s', mktime(23,59,59, date('m', $time)+1,0,date('Y', $time)));

            $data = ['all_where'=>0, 'unstart_where'=>0, 'handle_where'=>0, 'finish_where'=>0, 'comment_where'=>0];
            MyTask::whereNull('deleted_at')->where('user_type', 1)->chunk(1000, function($res) use(&$data, $stime, $etime, &$now_time) {
                foreach ($res as &$v) {
                    if($v['created_at'] >= $stime && $v['created_at'] <= $etime){
                        $data['all_where'] ++;//总数
                    }
                    if($v['status'] == 2 && $v['start_time'] >= $stime && $v['start_time'] <= $etime && $v['start_time'] > $now_time){
                        $data['unstart_where'] ++;//未开始数量
                    }
                    if($v['status'] == 2 && $v['start_time'] >= $stime && $v['start_time'] <= $etime && $v['start_time'] <= $now_time){
                        $data['handle_where'] ++;//处理中数量
                    }
                    if($v['status'] == 3 && $v['finish_time'] >= $stime && $v['finish_time'] <= $etime){
                        $data['finish_where'] ++;//已完成数量
                    }
                    if($v['status'] == 4 && $v['finish_time'] >= $stime && $v['finish_time'] <= $etime){
                        $data['comment_where'] ++;//已评分数量
                    }
                }
            });

            $data['my_supervise'] = Supervise::where([['user_id', $user->id],['type', $param['type']]])->where(function($query){
                $query->where('deleted_at', 0)->orWhereNull('deleted_at');
            })->count();//我督办的数量
            $data['my_attention'] = Attention::where([['user_id', $user->id],['type', $param['type']]])->where(function($query){
                $query->where('deleted_at', 0)->orWhereNull('deleted_at');
            })->count();//我关注的数量

            return returnJson(ConstFile::API_RESPONSE_SUCCESS_MESSAGE_DATA,ConstFile::API_RESPONSE_SUCCESS, $data);
        } catch (Exception $e){
            return returnJson($e->getMessage(), $e->getCode());
        }
    }


    /*
     * 指派督办
     * */
    public function appointSupervise($param, $user){
        //判断权限

        try{
            $error = $this->checkAppointData($param);
            if ($error) {
                throw new Exception($error,ConstFile::API_RESPONSE_FAIL);
            }

            $user_ids = explode(',', $param['users']);//选择的员工
            $supervise_user = Supervise::where([['relate_id', $param['id']],['deleted_at', 0], ['type', $param['type']]])->pluck('user_id', 'id')->toArray();//已经督办的员工

            DB::transaction(function () use ($user_ids, $supervise_user, $param, $user) {
                //更新已督办的数据
                $update_user = array_intersect($user_ids,$supervise_user);
                if(!empty($update_user)){
                    Supervise::where([['relate_id', $param['id']],['deleted_at', 0], ['type', $param['type']]])->whereIn('user_id', $user_ids)->update(['from_user_id'=>$user->id, 'updated_at'=>Carbon::now()]);
                }
                //添加督办的数据
                $add_user = array_diff($user_ids,$supervise_user);
                if(!empty($add_user)){
                    $add_data = [];
                    foreach ($add_user as $v){
                        $add_data[] = [
                            'user_id' => $v,
                            'relate_id' => $param['id'],
                            'type' => $param['type'],
                            'from_user_id' => $user->id,
                            'created_at' => Carbon::now(),
                            'updated_at' => Carbon::now()
                        ];
                    }
                    Supervise::insert($add_data);
                }
                //删除未选中
                $del_user = array_diff($supervise_user, $user_ids);
                if(!empty($del_user)){
                    Supervise::where([['relate_id', $param['id']],['deleted_at', 0], ['type', $param['type']]])->whereIn('user_id', $del_user)->update(['from_user_id'=>$user->id, 'deleted_at'=>Carbon::now()]);
                }
            });
        }catch(Exception $e){
            return returnJson($e->getMessage(), $e->getCode());
        }
        return returnJson(ConstFile::API_RESPONSE_SUCCESS_MESSAGE);
    }


    /*
     * 创建子任务
     * */
    public function createChildTask($param, $user)
    {
        try {
            $result = ['code' => ConstFile::API_RESPONSE_SUCCESS, 'message' => ConstFile::API_RESPONSE_SUCCESS_MESSAGE];
            $error = $this->checkTaskData($param);
            if ($error) {
                throw new Exception($error);
            }
            $data = $param['task'];
            $executorArray = $data['executor_ids'];  //执行人
            unset($data['executor_ids']);

            if ($data['copy_person_ids']) {
                $copypersonArray = $data['copy_person_ids'];  //抄送人
            } else {
                $copypersonArray = '';
            }

            $task = MyTask::where('id', $data['task_id'])->first();
            throw_if(empty($task), new Exception('任务不存在'));

            $max = MyTask::where('parent_id', $data['task_id'])->max('temp_id');
            DB::transaction(function () use ($data, $executorArray, $copypersonArray, $max, $task, $user) {
                //执行人
                $exe_arr = array_filter(explode(',', $executorArray));
                //创建任务
                $now_time = Carbon::now()->toDateTimeString();
                //创建任务和参与人的对应记录
                $userData = [];
                foreach ($exe_arr as $k => $v) {
                    if(intval($v)){
                        $userData[] = [
                            'tid' => $task->tid,
                            'create_user_id' => $user->id,
                            'status' => 2,  //已确认
                            'uid' => intval($v),
                            'pid' => 0,
                            'parent_id' => $data['task_id'],
                            'level' => $task->level+1,
                            'parent_ids' => 0,
                            'temp_id' => $max+1,
                            'user_type' => 1,  //接收人类
                            'start_time' => Carbon::parse($data['start_at'])->toDateTimeString(),
                            'end_time' => Carbon::parse($data['deadline'])->toDateTimeString(),
                            'accept_time' => Carbon::now()->toDateTimeString(),
                            'created_at' => $now_time,
                            'updated_at' => $now_time,
                            'content' => $data['info']
                        ];
                    }
                }
                if(!empty($userData)){
                    DB::table('my_task')->insert($userData);
                }
                //抄送人
                $receiverIds = [];
                if (!empty($copypersonArray)) {
                    $message = [];
                    $copy_arr = array_filter(explode(',', $copypersonArray));
                    foreach ($copy_arr as $a => $b) {
                        if(intval($b)){
                            $receiverIds[] = intval($b);
                            $message[] = [
                                'receiver_id' => intval($b),
                                'sender_id' => $user->id,
                                'content' => $data['info'],
                                'type' => Message::MESSAGE_TYPE_REWARDPUNISHMENT0NE,
                                'relation_id' => $data['task_id'],
                                'created_at' => $now_time,
                                'updated_at' => $now_time,
                            ];
                        }
                    }
                    if(!empty($message)){
                        DB::table('message')->insert($message);
                    }
                }
            });
        } catch (Exception $e) {
            $result['code'] = ConstFile::API_RESPONSE_FAIL;
            $result['message'] = $e->getMessage();
        }
        return $result;
    }


    /*
     * 任务详情
     * */
    public function taskDetail($param){
        try{
            throw_if(empty($param['id']), new Exception('请选择任务'));
            $task = MyTask::where('my_task.id', $param['id'])->with('child')->with('punishment')
                ->leftJoin('task', 'task.id', '=', 'my_task.tid')
                ->select('my_task.*', 'task.info')
                ->first()->toArray();
            throw_if(empty($task), new Exception('任务不存在'));

            $task['content'] = $task['content'] ? $task['content'] : ($task['info'] ? $task['info'] : '');
            $data = $this->showStatusInfo($task);

            $user_ids = array_merge(['0'=>$task['uid']],array_column($data['child'], 'uid'));
            $user_info = User::whereIn('id', $user_ids)->pluck('chinese_name', 'id')->toArray();

            if(!empty($data['child'])){
                foreach($data['child'] as &$v){
                    $v['user_name'] = $user_info[$v['uid']];
                    $v = $this->showStatusInfo($v);
                }
            }
            $data['user_name'] = $user_info[$task['uid']];
            $is_supervise = Supervise::where(['relate_id'=>$task['id'], 'type'=>1, 'deleted_at'=>0])->count();
            $is_attention = Attention::where(['relate_id'=>$task['id'], 'type'=>1, 'deleted_at'=>0])->count();
            $data['is_supervise'] = $is_supervise ? 1 : 0;
            $data['is_attention'] = $is_attention ? 1 : 0;

            return returnJson(ConstFile::API_RESPONSE_SUCCESS_MESSAGE_DATA,ConstFile::API_RESPONSE_SUCCESS, $data);
        } catch (Exception $e){
            return returnJson($e->getMessage(), $e->getCode());
        }
    }
    public function taskDetail1($param){
        try{
            throw_if(empty($param['id']), new Exception('请选择任务'));
            $task = MyTask::where('my_task.id', $param['id'])->with('child')->with('punishment')
                ->leftJoin('task', 'task.id', '=', 'my_task.tid')
                ->select('my_task.*', 'task.info')
                ->first();
            throw_if(empty($task), new Exception('任务不存在'));

            $task->content = $task->content ? $task->content : ($task->info ? $task->info : '');
            if(!empty($task->child)){
                Collect($task->child)->each(function (&$item, $key) {
                    $item = $this->showStatusInfo($item);
                });
            }
            $data = $this->showStatusInfo($task);

            $is_supervise = Supervise::where(['relate_id'=>$task['id'], 'type'=>1, 'deleted_at'=>0])->count();
            $is_attention = Attention::where(['relate_id'=>$task['id'], 'type'=>1, 'deleted_at'=>0])->count();
            $data->is_supervise = $is_supervise ? 1 : 0;
            $data->is_attention = $is_attention ? 1 : 0;

            return returnJson(ConstFile::API_RESPONSE_SUCCESS_MESSAGE_DATA,ConstFile::API_RESPONSE_SUCCESS, $data);
        } catch (Exception $e){
            return returnJson($e->getMessage(), $e->getCode());
        }
    }


    private function showStatusInfo($task){
        if(empty(array_filter($task))){
            return [];
        }
        $task['is_show_supervise'] = ($task['status'] < 3) ? 1 : 0;//未完成做催办
        $task['is_show_punish'] = ($task['status'] >= 3 && $task['end_time'] < $task['finish_time']) ? 1 : 0;//超时完成才处罚

        $task['tag'] = '';
        $time = date('Y-m-d H:i:s', time());
        if($task['status'] < 3 && $task['end_time'] < $time){
            $task['tag'] = '已逾期'.ceil(((time()-strtotime($task['end_time']))/(3600*24))).'天';
        }else if($task['status'] >= 3 && $task['end_time'] < $task['finish_time']){
            $task['tag'] = '已逾期'.ceil((strtotime($task['finish_time'])-strtotime($task['end_time']))/(3600*24)).'天';
        }

        $task['status_name'] = '';
        $task['show_status'] = 1;
        if($task['status'] == 2){
            if($task['start_time'] > $time){
                $task['status_name'] = '未开始';
                $task['show_status'] = 1;
            }else{
                $task['status_name'] = '处理中';
                $task['show_status'] = 2;
            }
        }else if($task['status'] == 3){
            if($task['finish_time'] > $task['deadline']){
                $task['status_name'] = '超时完成';
                $task['show_status'] = 6;
            }else{
                $task['status_name'] = '已完成';
                $task['show_status'] = 3;
            }
        }else if($task['status'] == 4){
            if((strtotime($task['comment_time']) - strtotime($task['updated_at'])) > 7*24*3600){
                $task['status_name'] = '超时评价';
                $task['show_status'] = 5;
            }else{
                $task['status_name'] = '已评价';
                $task['show_status'] = 4;
            }
        }
        return $task;
    }



    /*
     * 组合列表查询条件
     * */
    private function listCondition($param, $user){
        $whereRaw = '';
        $where = $where_in = [];
        $time = date('Y-m-d H:i:s', time());
        if($param['type'] == 1){
            //督办大厅
            if(!isset($param['status'])){
                $where[] = ['my_task.status', 2];//未完成的，已接受的
            }else if($param['status'] == 1){
                //未开始
                $where[] = ['my_task.status', 2];
                $where[] = ['my_task.start_time', '>', $time];
            }else if($param['status'] == 2){
                //处理中
                $where[] = ['my_task.status', 2];
                $where[] = ['my_task.start_time', '<=', $time];
            }
        }else{
            if(isset($param['status'])){
                if($param['status'] == 1){
                    //未开始
                    $where[] = ['my_task.status', 2];
                    $where[] = ['my_task.start_time', '>', $time];
                }if($param['status'] == 2){
                    //已开始，待处理
                    $where[] = ['my_task.status', 2];
                    $where[] = ['my_task.start_time', '<=', $time];
                }else if(in_array($param['status'], [3,4])){
                    $where[] = ['my_task.status', $param['status']];
                }else if($param['status'] == 5){
                    //超时评价
                    $where[] = ['my_task.status', 4];
                    $whereRaw = 'date_sub(my_task.comment_time, interval "7 00:00:00" day_second) > my_task.updated_at';
                }else if($param['status'] == 6){
                    //超时完成
                    $where[] = ['my_task.status', 3];
                    $whereRaw = 'my_task.finish_time > my_task.end_time';
                }else{
                    $where[] = ['my_task.status', '>=', 2];
                }
            }else{
                $where[] = ['my_task.status', '>=', 2];
            }
        }
        if(!empty($param['stime'])){
            $where[] = ['my_task.start_time', '>=', $param['stime']];
        }
        if(!empty($param['etime'])){
            $where[] = ['my_task.end_time', '<=', $param['etime']];
        }
        if(!empty($param['key'])){
            $where[] = ['task.info', 'like', '%'.$param['key'].'%'];
        }
        $supervise_ids = Supervise::where([['user_id', $user->id],['deleted_at', 0]])->pluck('relate_id')->toArray();//我督办的
        if(!empty($param['is_my_supervise'])){
            $where_in[] = ['my_task.id', $supervise_ids];
        }
        $attention_ids = Attention::where([['user_id', $user->id],['deleted_at', 0]])->pluck('relate_id')->toArray();//我关注的
        if(isset($param['is_attention'])){
            $where_in[] = ['my_task.id', $attention_ids];
        }
        return ['where'=>$where, 'where_in'=>$where_in, 'whereRaw'=>$whereRaw, 'supervise'=>$supervise_ids, 'attention'=>$attention_ids];
    }


    /*
     * 检测指派督办的数据
     * */
    private function checkAppointData($param){
        $msg = '';

        if(empty($param['users'])){
            $msg = '请选择指派督办的员工';
        }
        if(empty($param['id'])){
            $msg = '请选择指派督办的内容';
        }
        if(empty($param['type']) || !in_array($param['type'], array_keys($this->s_table))){
            $msg = '请选择督办的类型';
        }

        return $msg;
    }


    private function checkTaskData($param)
    {
        if (!isset($param['task']) || empty($param['task'])) {
            return '请填写任务数据';
        }
        $data = $param['task'];

        if (!isset($data['info']) || empty($data['info'])) {
            return '任务内容不能为空';
        }
        if (!isset($data['executor_ids']) || empty($data['executor_ids'])) {
            return '请选择执行人';
        }
        $data['executor_ids'] = array_filter(explode(',', $data['executor_ids']));
        if (empty($data['executor_ids'])) {
            return '请选择执行人';
        }
        if (!isset($data['start_at']) || empty($data['start_at'])) {
            return '请填写任务开始时间';
        }
        if (!isset($data['deadline']) || empty($data['deadline'])) {
            return '截止时间不能为空';
        }
        return '';
    }


    /*
     * 多级任务数据
     * */


}
