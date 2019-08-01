<?php

namespace App\Repositories\Salary;

use App\Constant\ConstFile;
use App\Http\Helpers\Dh;
use App\Models\MyTask\MyTask;
use App\Models\Salary\RewardPunishment;
use App\Models\Task\Task;
use App\Models\User;
use App\Repositories\Repository;
use App\Repositories\UsersRepository;
use Validator;
use DB;
use Auth;

class ScoreRepository extends Repository {
    public function model() {
        return Task::class;
    }

    public function getList($data){
        $validator = Validator::make($data, ['dates' => 'required|date_format:Y-m']);
        if($validator->fails()){
            return returnJson('参数错误: '. $validator->errors()->first(), ConstFile::API_RESPONSE_FAIL);
        }

        $users = new UsersRepository();
        /*$dept_id = Auth::user()->primaryDepartUser->department->id;
        $dept = $users->getChildUsers($dept_id);
        dd($dept);*/
        $t = Dh::getBeginEndByMonth($data['dates']);
        $res = MyTask::query()
            ->rightJoin('total_comment','my_task.id', '=', 'total_comment.relation_id')
            ->rightJoin('task_score','total_comment.id','=','task_score.pid')
            ->where('my_task.status', 3)
            ->where('total_comment.type', 1)
            ->where('task_score.score', '>', '0')
            ->whereBetween('my_task.created_at', $t)
            ->groupBy('my_task.create_user_id')
            ->selectRaw('AVG(score) as avg_score,create_user_id')
            ->get();
        $list = [
            '0'=> [],'1'=> [],'2'=> [],'3'=> [],'4'=> [],
        ];
        foreach ($res as $k=>$v){
            if($v['avg_score'] < 20){
                $list[0][] = $v['create_user_id'];
            }elseif ($v['avg_score'] < 40){
                $list[1][] = $v['create_user_id'];
            }elseif ($v['avg_score'] < 80){
                $list[2][] = $v['create_user_id'];
            }elseif ($v['avg_score'] < 90){
                $list[3][] = $v['create_user_id'];
            }elseif ($v['avg_score'] >= 90){
                $list[4][] = $v['create_user_id'];
            }
        }
        return returnJson('', ConstFile::API_RESPONSE_SUCCESS, $list);
    }

    public function getListInfo($data){
        $validator = Validator::make($data, ['dates' => 'required|date_format:Y-m']);
        if($validator->fails()){
            return returnJson('参数错误: '. $validator->errors()->first(), ConstFile::API_RESPONSE_FAIL);
        }

        $t = Dh::getBeginEndByMonth($data['dates']);
        $res = MyTask::query()
            ->rightJoin('total_comment','my_task.id', '=', 'total_comment.relation_id')
            ->rightJoin('task_score','total_comment.id','=','task_score.pid')
            ->where('my_task.status', 3)
            ->where('total_comment.type', 1)
            ->where('task_score.score', '>', '0')
            ->whereBetween('my_task.created_at', $t)
            ->whereIn('my_task.create_user_id', $data['user_ids'])
            ->groupBy('my_task.create_user_id')
            ->selectRaw('AVG(score) as avg_score,create_user_id')
            ->with(['user.fetchPrimaryDepartment'])
            ->get();
        return returnJson('', ConstFile::API_RESPONSE_SUCCESS, $res);
    }
}