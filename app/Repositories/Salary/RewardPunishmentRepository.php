<?php

namespace App\Repositories\Salary;

use App\Constant\ConstFile;
use App\Http\Helpers\Dh;
use App\Http\Requests\Salary\RewardPunishmentRequest;
use App\Models\DepartUser;
use App\Models\Feedback\FeedbackContent;
use App\Models\Meeting\Meeting;
use App\Models\Message\Message;
use App\Models\MyTask\MyTask;
use App\Models\Salary\RewardPunishment;
use App\Models\User;
use App\Models\Workflow\Entry;
use App\Repositories\Repository;
use App\Services\Workflow\FlowCustomize;
use Carbon\Carbon;
use Exception;
use DB;
use Auth;

class RewardPunishmentRepository extends Repository {
    public function model() {
        return RewardPunishment::class;
    }


    public function add($data){
        $check_result = (new RewardPunishmentRequest())->add($data);
        if($check_result !== true) return $check_result;

        try{
            $data['admin_id'] = Auth::id();
            RewardPunishment::query()->create($data);
            return returnJson('',ConstFile::API_RESPONSE_SUCCESS);
        }catch (Exception $e){
            return returnJson($e->getMessage(), ConstFile::API_RESPONSE_FAIL);
        }
    }

    public function getList($data){
        try{
            $result = RewardPunishment::query();
            $result->where('admin_id', Auth::id());
            if(isset($data['user_id']) && intval($data['user_id']))
                $result->where('user_id', $data['user_id']);
            if(isset($data['department_id']) && intval($data['department_id']))
                $result->where('department_id', $data['department_id']);
            if(isset($data['title']) && $data['title'])
                $result->where('title', 'like', "%{$data['title']}%");
            if(isset($data['dates']) && $data['dates']){
                $t = Dh::getBeginEndByMonth($data['dates']);
                $result->whereBetween('dates', $t);
            }

            $list = $result->with(['user', 'department'])
                ->orderBy('id', 'desc')
                ->get();
            return returnJson('',ConstFile::API_RESPONSE_SUCCESS, $list);
        }catch (Exception $e){
            return returnJson($e->getMessage(), ConstFile::API_RESPONSE_FAIL);
        }
    }

    public function getMyInfo(){
        return RewardPunishment::query()
            ->where('user_id', Auth::id())
            ->with(['user', 'department', 'complain'])
            ->get();
    }

    public function getInfo($id){
        $info = RewardPunishment::query()
            ->with(['user', 'complain'])
            ->where('id', $id)
            ->first();
        if($info){
            $da= DepartUser::with('getPrimaryDepartmentA')
                            ->where('is_primary', DepartUser::DEPARTMENT_PRIMARY_YES)
                            ->where('user_id',$info->user_id)->first(['department_id']);
            $info->department=$da->toArray()['get_primary_department_a'];
            return returnJson('',ConstFile::API_RESPONSE_SUCCESS, $info);
        }else{
            return returnJson('参数错误', ConstFile::API_RESPONSE_FAIL);
        }
    }

    public function delete($id){
        $res = RewardPunishment::query()
            ->where('id', $id)
            ->delete();
        if($res){
            return returnJson('操作成功',ConstFile::API_RESPONSE_SUCCESS);
        }else{
            return returnJson('操作失败', ConstFile::API_RESPONSE_FAIL);
        }
    }

    /*
     * 根据任务ID 添加惩罚
     * */
    public function addByTask($data){
        $check_result = (new RewardPunishmentRequest())->addByTask($data);
        if($check_result !== true) return $check_result;

        $info = MyTask::query()
            ->where('id', $data['id'])
            ->first();
        if(empty($info))
            return returnJson('数据不存在', ConstFile::API_RESPONSE_FAIL);
        if(!empty($data['day']) && isset($data['day'])){
            $dates =$data['day'];
        }else{
            $dates = Carbon::now()->toDateString();
        }

        try{
            $param = [
                'title' => $data['title'],
                'admin_id' => Auth::id(),
                'type' => 2,
                'user_id' => $info['uid'],
                'money' => $data['money'],
                'dates' => $dates,
                'task_id' => $data['id'],
            ];
            $n = RewardPunishment::query()->create($param);
            if($n){
                $dataOne = [
                    'receiver_id' => $info['uid'],//接收者（申请人）
                    'sender_id' => Auth::id(),//发送者（最后审批人）
                    'content'=> $data['title'],//内容
                    'type' => Message::MESSAGE_TYPE_REWARDPUNISHMENT,
                    'relation_id' => $n->id,//任务 的 id
                    'created_at'=>date('Y-m-d H:i:s',time()),
                    'updated_at'=>date('Y-m-d H:i:s',time())
                ];
                Message::insert($dataOne);
            }
            return returnJson('',ConstFile::API_RESPONSE_SUCCESS);
        }catch (Exception $e){
            return returnJson($e->getMessage(), ConstFile::API_RESPONSE_FAIL);
        }

    }
    /*
     * 奖惩申诉
     * */
    public function RewardPunishmentAppeal($data, $userid){
        $info = RewardPunishment::query()->find($data['pr_id']);
        if (empty($info))
            return returnJson('参数错误', ConstFile::API_RESPONSE_FAIL);

        $param = [
            'tid' => FeedbackContent::TYPE_APPEAL,
            'title' => '奖惩申诉 - '. $info->title,
            'content' => $data['content'],
            'way' => FeedbackContent::WAY_REALNAME,
            'publish_time' => Carbon::now()->toDateTimeString(),
            'status' => FeedbackContent::STATUS_UNANSWERED,
            'uid' => $userid,
            'image' => isset($data['image']) ? $data['image'] : "",
            'relation_type' => FeedbackContent::RELATION_REWARD_PUNISHMENT,
            'relation_id' => $info->id,
        ];
        try{
            FeedbackContent::query()->create($param);
            return returnJson('ok', ConstFile::API_RESPONSE_SUCCESS);
        }catch (Exception $e){
            return returnJson($e->getCode(), $e->getMessage());
        }
    }

}