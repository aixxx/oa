<?php

namespace App\Repositories;

use App\Constant\ConstFile;

use App\Models\Comments\TotalComment;
use App\Models\MyTask\MyTask;
use App\Models\Task\TaskScore;
use App\Repositories\Repository;
use App\Models\Comments\Comments;
use App\Repositories\UsersRepository;
use Exception;
use Request;
use DB;
use Auth;

class CommentsRepository extends Repository {

    public function model() {
        return Comments::class;
    }

    //评分
    public function addcomments($user_id){
        $datas = Request::all();

        if(!isset($user_id) || empty($user_id)){
            $result['code'] = -1;
            $result['message'] = "请登录";
            return $result;
        }else{
            $datas['create_user_id'] = $user_id;
        }

        if(!isset($datas['user_id']) || empty($datas['user_id'])){
            $result['code'] = -1;
            $result['message'] = "执行者ID不能为空";
            return $result;
        }

        if(!isset($datas['o_id']) || empty($datas['o_id'])){
            $result['code'] = -1;
            $result['message'] = "任务ID不能为空";
            return $result;
        }

        $status = DB::table('my_task')->where('create_user_id', $user_id)->where('tid',$datas['o_id'])->where('uid',$datas['user_id'])->value('status');

        if($status != 3){
            $result['code'] = -1;
            $result['message'] = "此任务还未执行,暂时不能评分";
            return $result;
        }

        if(!isset($datas['score']) || empty($datas['score'])){
            $result['code'] = -1;
            $result['message'] = "评分分数不能为空";
            return $result;
        }

        $count = DB::table('comments')->where('create_user_id', $user_id)->where('o_id',$datas['o_id'])->where('user_id',$datas['user_id'])->count();
        if(!empty($count)){
            $result['code'] = -1;
            $result['message'] = "你已评分过此任务";
            return $result;
        }

        $datas['comment_time'] = date("Y-m-d H:i:s");

        $list = DB::table('comments')->insert($datas);

        if($list){
            $where['tid'] = $datas['o_id'];
            $where['uid'] = $datas['user_id'];
            $where['create_user_id'] = $user_id;
            DB::table('my_task')->where($where)->update(array('status'=>4));   //评价后变成完成状态
            $result['code'] = 200;
            $result['message'] = "操作成功";
        }else{
            $result['code'] = -1;
            $result['message'] = "操作失败";
        }
        return $result;
    }

    //算上个月的平均分-创建者视角
    public function avescore($user_id){

        if(!isset($user_id) || empty($user_id)){
            $result['code'] = -1;
            $result['message'] = "请登录";
            return $result;
        }

        $count = DB::table('comments')->where('create_user_id', $user_id)->count();
        if(empty($count)){
            $result['code'] = -1;
            $result['message'] = "未找到你的评分记录";
            return $result;
        }

        $start = date('Y-m-01 00:00:00',strtotime('-1 month')); //上个月的开始日期
//        $firstday = strtotime($start);

        $end = date("Y-m-d 23:59:59", strtotime(-date('d').'day')); //上个月的结束日期
//        $lastday = strtotime($end);

        //上月创建者打分的平均分
        $avgscore = ceil(Comments::whereBetween('comment_time', [$start,$end])->where('create_user_id', $user_id)->avg('score'));

        //上月我分配的总任务数
        $nocomments = DB::table('my_task')->whereBetween('comment_time', [$start,$end])->where('create_user_id', $user_id)->where('user_type',1)->count();

        //上月我打分数量
        $mycomments = DB::table('comments')->whereBetween('comment_time', [$start,$end])->where('create_user_id', $user_id)->where('type',0)->count();

        //未评分数
        $noscore = $nocomments - $mycomments;

        if($noscore >= 10){
            //需要扣除的分数
            $avgscore = $avgscore - ceil($noscore/10);
            $avgscores=['avgscore'=>$avgscore];
            return returnJson($message='ok',$code = 200,$data=$avgscores);
        }else{
            $avgscores=['avgscore'=>$avgscore];
            return returnJson($message='ok',$code = 200,$data=$avgscores);
        }

    }


    //获取评分列表
    public function getscorelist($user_id){

        if(!isset($user_id) || empty($user_id)){
            $result['code'] = -1;
            $result['message'] = "请登录";
            return $result;
        }
        $myscorelist = Comments::where('create_user_id', $user_id)->get();
        return returnJson($message='ok',$code = 200,$data=$myscorelist);
    }

    //根据任务id和执行者id查出此执行者做的任务的评分
    public function getscorebyoid(){

        $datas = Request::all();

        if(!isset($datas['user_id']) || empty($datas['user_id'])){
            return 0;
        }

        if(!isset($datas['o_id']) || empty($datas['o_id'])){
            return 0;
        }

        $scorecount = DB::table('comments')->where('o_id',$datas['o_id'])->where('user_id',$datas['user_id'])->count();
        if(empty($scorecount)){
            return 0;
        }

        $oscore = DB::table('comments')->where('o_id',$datas['o_id'])->where('user_id',$datas['user_id'])->value('score');
        return $oscore;

    }

    // 总评论
    public function total_comments($info,$user_id){
        $arr = [1,2,3,4,5,6,7,8,9,10,11,12];           // 类型(1任务,2投票,3反馈,4审批,5出差,6加班,7请假,8外勤,9补卡,10汇报11工作流，12督办)
        if (!$user_id) {
            return returnJson($message = '请登录！', $code = '1001');
        }

        if (!$info['type']) {
            return returnJson($message = '类型不能为空！', $code = '1002');
        }
        if(!in_array($info['type'],$arr)){
            return returnJson($message = '类型有误！', $code = '1003');
        }
        if (!$info['comment_text'] && $info['type'] != TotalComment::TYPE_TASK) {
            return returnJson($message = '评论内容不能为空！', $code = '1004');
        }

        if($info['type'] == 1){
            try{
                DB::transaction(function () use ($info,$user_id) {
                    $mat['id'] = $info['relation_id'];
                    $mat['create_user_id'] = $user_id;
                    $mat['status'] = 3;
                    $mats = DB::table('my_task')->where($mat)->count();
                    if (empty($mats)) {
                        throw new Exception('数据有误，请检查！',1005);
                    }
                    $hes['type'] = $info['type'];
                    $hes['relation_id'] = $info['relation_id'];
                    $hes['uid'] = $user_id;
                    $total = DB::table('total_comment')->where($hes)->first();

                    $res['type'] = $info['type'];
                    $res['relation_id'] = $info['relation_id'];
                    $res['uid'] = $user_id;
                    $res['comment_text'] = $info['comment_text'];
                    $res['comment_img'] = $info['comment_img']??'';
                    $res['comment_field'] = $info['comment_field']??'';
                    $res['entry_id'] = isset($info['entry_id']) ? $info['entry_id'] : '';
                    if(empty($total)){
                        $res_id = DB::table('total_comment')->insertGetId($res);
                        $cat['pid'] = $res_id;
                        $cat['my_task_id'] = $info['relation_id'];
                        $cat['admin_id'] = Auth::id();
                        $cat['user_id'] = MyTask::query()->where('id', $info['relation_id'])->pluck('uid')->first();
                        $cat['score'] = $info['score'];
                        //$result = DB::table('task_score')->insert($cat);
                        $result = TaskScore::query()->create($cat);
                        if(!$result){
                            throw new Exception('任务评价失败！',1006);
                        }
                    }else{
                        $resat['comment_text'] = $info['comment_text'];
                        $resat['comment_img'] = $info['comment_img']??'';
                        $resat['comment_field'] = $info['comment_field']??'';
                        $result = DB::table('total_comment')->where('id',$total->id)->update($resat);
                        if (!$result) {
                            throw new Exception('评论失败！',1007);
                        }
                    }
                    if($result){
                        $res = MyTask::where('id', $info['relation_id'])->update(['status'=> 4, 'comment_time'=>date('Y-m-d H:i:s', time())]);
                        if (!$res) {
                            throw new Exception('评论失败！',1007);
                        }
                    }
                });
            }catch(Exception $e){
                return returnJson($message = $e->getMessage(), $code = '1008');
            }
            return returnJson($message = '评价成功', $code = '1009');
        }else{
            $res['type'] = $info['type'];
            $res['relation_id'] = $info['relation_id'];
            $res['uid'] = $user_id;
            $res['comment_text'] = $info['comment_text'];
            $res['comment_img'] = $info['comment_img']??'';
            $res['comment_field'] = $info['comment_field']??'';
            $met = DB::table('total_comment')->insert($res);
            if ($met) {
                return returnJson($message = '评论成功', $code = '200');
            }else{
                return returnJson($message = '评论失败', $code = '1010');
            }
        }

    }

    //评论列表
    public function comment_list($info){
        $arr = [1,2,3,4,5,6,7,8,9,10,11,12];           // 类型(1任务,2投票,3反馈,4审批,5出差,6加班,7请假,8外勤,9补卡,10汇报)
        if (!$info['type']) {
            return returnJson($message = '类型不能为空！', $code = '1002');
        }
        if(!in_array($info['type'],$arr)){
            return returnJson($message = '类型有误！', $code = '1003');
        }
        $cat['type'] = $info['type'];
        $cat['relation_id'] = $info['relation_id'];
        $res = DB::table('total_comment')->where($cat)->get(['uid','comment_text','comment_img','comment_time']);
        $result = array_map('get_object_vars', $res->toArray());
        $sst_arr = [];
        if(!empty($result)){
            foreach($result as $k =>$v){
                $result[$k]['user_name'] = DB::table('users')->where('id',$v['uid'])->value('chinese_name');
                $result[$k]['avatar'] = DB::table('users')->where('id',$v['uid'])->value('avatar');
                $result[$k]['sort_time'] = $v['comment_time'] ? strtotime($v['comment_time']) : 0;
            }
            $last_names = array_column($result, 'sort_time');
            array_multisort($last_names, SORT_DESC, $result);

            foreach ($result as $kay => $kaz) {
                unset($result[$kay]['sort_time']);
            }
            $sst_arr = array_slice($result, ($info['page'] - 1) * $info['limit'], $info['limit']);
            if (empty($sst_arr)) {
                $sst_arr = [];
            }
        }
        return returnJson('获取成功', 200, $sst_arr);
    }




}
