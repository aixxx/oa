<?php

namespace App\Repositories;

use App\Constant\ConstFile;
use App\Models\Like;
use Exception;
use Request;
use DB;
use Carbon\Carbon;

class LikeRepository extends Repository {

    public function __construct(){
        //$this->user = Auth::user();
        //$this->user = DB::table('users')->find(1);

        //点赞关联表名 [1:汇报]
        $this->s_table = [
            '1' => 'reports'
        ];
    }


    /*
     * 添加取消点赞
     * */
    public function addLike($data, $user){
        if(empty($data['type'])){
            return returnJson('请填写点赞类型', ConstFile::API_RESPONSE_FAIL);
        }
        if(empty($data['relate_id'])){
            return returnJson('请选择点赞的内容', ConstFile::API_RESPONSE_FAIL);
        }

        //$info = DB::table($this->s_table[$data['type']])->find($data['relate_id']);
        $info = DB::table($this->s_table[$data['type']])->where('id', $data['relate_id'])->first();
        if(empty($info)){
            return returnJson(ConstFile::API_RECORDS_NOT_EXIST_MESSAGE, ConstFile::API_RESPONSE_FAIL);
        }

        $likes = Like::withTrashed()->where([['relate_id', $data['relate_id']],['user_id', $user->id],['type', $data['type']]])->first();
        //print_r($likes);die;
        if(!empty($likes)){
            //已存在，更新数据
            if($likes->deleted_at){
                //更新点赞
                $res = Like::where('id', $likes->id)->restore();
            }else{
                //取消点赞
                $res = Like::where('id', $likes->id)->delete();
            }
        }else{
            //不存在，增加数据
            $da = [
                'user_id' => $user->id,
                'relate_id' => $data['relate_id'],
                'type' => $data['type']
            ];
            $res = Like::create($da);
        }

        if(empty($res)){
            return returnJson(ConstFile::API_RESPONSE_FAIL_MESSAGE, ConstFile::API_RESPONSE_FAIL);
        }else{
            return returnJson(ConstFile::API_RESPONSE_SUCCESS_MESSAGE);
        }
    }


    /*
     * 点赞列表
     * */
    public function likeList($data){
        if (!$data['type']) {
            return returnJson('类型不能为空！', ConstFile::API_RESPONSE_FAIL);
        }

        if(!in_array($data['type'],array_keys($this->s_table))){
            return returnJson('类型有误！', ConstFile::API_RESPONSE_FAIL);
        }

        $list = Like::where([['relate_id', $data['relate_id']],['type', $data['type']]])
            ->leftJoin('users', 'users.id', '=', 'likes.user_id')
            ->get(['likes.user_id','likes.relate_id','likes.type','users.chinese_name','users.position','users.avatar'])
            ->paginate($data['limit'])
            ->toArray();

        $result['total'] = $list['total'];
        $result['total_page'] = $list['last_page'];
        $result['data'] = $list['data'];

        return returnJson(ConstFile::API_RESPONSE_SUCCESS_MESSAGE_DATA,ConstFile::API_RESPONSE_SUCCESS, $result);
    }

}
