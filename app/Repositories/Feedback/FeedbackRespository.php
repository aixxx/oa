<?php

namespace App\Repositories\Feedback;

use Mockery\Exception;
use App\Repositories\Repository;
use App\Models\Feedback\FeedbackContent;
use App\Models\Feedback\FeedbackReply;
use App\Models\Feedback\FeedbackAccssory;
use Request;
use App\Constant\ConstFile;
use DB;

class FeedbackRespository extends Repository {

    public function model() {
        return FeedbackContent::class;
    }

    // 我的反馈列表
    public function feedbacklist($uid) {
        $res = FeedbackContent::leftJoin('feedback_type', 'feedback_type.id', '=', 'feedback_content.tid')
            ->where("feedback_content.uid" ,"=",$uid)
            ->orderBy('feedback_content.publish_time','desc')
            ->get(['feedback_content.id', 'feedback_content.title', 'feedback_content.publish_time','feedback_content.status', 'feedback_content.content','feedback_type.type']);
        if(!$res){
            $res=[];
        }
        return  returnJson($message="ok",$code="200",$data=$res);
    }
    // 我的反馈列表
    public function getFeedbackList($uid) {
        $res = FeedbackContent::leftJoin('feedback_type', 'feedback_type.id', '=', 'feedback_content.tid')
            ->orderBy('feedback_content.publish_time','desc')
            ->select(['feedback_content.id', 'feedback_content.title', 'feedback_content.publish_time','feedback_content.status', 'feedback_content.content','feedback_type.type'])
            ->paginate(10);
        if($res){
            $list=$res->toArray();
            unset($list['first_page_url']);
            unset($list['from']);
            unset($list['last_page']);
            unset($list['last_page_url']);
            unset($list['next_page_url']);
            unset($list['path']);
            unset($list['prev_page_url']);
        }else{
            $list=[];
        }
        return  returnJson($message="ok",$code=ConstFile::API_RESPONSE_SUCCESS,$list);
    }
    // 反馈写入
    public function feedbackedit($uid){
        DB::beginTransaction();
        try {
            $data = Request::all();
            $publish_time = date('Y-m-d H:i:s', time());

            if (empty($data['tid'])) {
                $result['code'] = ConstFile::API_RESPONSE_FAIL;
                $result['message'] = 'no';
                $result['data'] = 'tid参数不存在';
                return $result;
            } elseif (empty($data['title'])) {
                $result['code'] = ConstFile::API_RESPONSE_FAIL;
                $result['message'] = 'no';
                $result['data'] = 'title参数不存在';
                return $result;
            } elseif (empty($data['way'])) {
                $result['code'] = ConstFile::API_RESPONSE_FAIL;
                $result['message'] = 'no';
                $result['data'] = 'way参数不存在';
                return $result;
            } elseif (empty($data['content'])) {
                $result['code'] = ConstFile::API_RESPONSE_FAIL;
                $result['message'] = 'no';
                $result['data'] = 'content参数不存在';
                return $result;
            }
            $addId = FeedbackContent::insertGetId(
                ['tid' => $data['tid'],
                    'title' => $data['title'],
                    'content' => $data['content'],
                    'way' => $data['way'],
                    'publish_time' => $publish_time,
                    'uid' => $uid,
                    'image' => $data['image'],
                    'video' => $data['video'],
                    'audio' => $data['audio'],
                    'other_file' => $data['other_file'],
                ]);
            $name = mt_rand(1, 99999) . date('YmdHis', time()) . '.png';
            $addAccessory = FeedbackAccssory::insert(['status'=>2,
                'rid' => $addId,
                'name' => $name,
                'type' => 'png',
                'size' => '1024'
            ]);
            $result['code'] = 200;
            $result['message'] = 'ok';
            DB::commit();
            return $result;
        }catch(Exception $e){
            DB::rollBack();
        }
    }

    // 反馈详情
    public function feedbackdetail() {
        if(empty(Request::get('id'))){
            $result['code'] = ConstFile::API_RESPONSE_FAIL;
            $result['message'] = 'no';
            $result['data'] = 'id参数不存在';
            return $result;
        }
        $id = Request::get('id');// 反馈的id
        $res = FeedbackContent::leftJoin('feedback_type','feedback_content.tid','=','feedback_type.id')
            ->leftJoin('users','users.id','=','feedback_content.uid')
            ->where("feedback_content.id" ,"=",$id)
            ->get(['feedback_content.*','users.chinese_name','users.avatar','feedback_type.type']);
        if($res[0]->avatar == null){
            $res[0]->avatar = 0;
        }
        $list['detail'] = $res;
        $status = $res[0]->status;
        $cid = $res[0]->id;
//        if($status != 2){ // 说明有回复
//            $da = FeedbackReply::leftJoin('users','feedback_reply.user_id','=','users.id')
//                ->where('feedback_reply.cid', $cid)
//                ->orderBy('feedback_reply.add_time', 'desc')
//                ->get(['users.chinese_name','feedback_reply.add_time','feedback_reply.content']);
            $da = DB::table('total_comment')
                ->leftJoin('users','total_comment.uid','=','users.id')
                ->where('total_comment.relation_id', $cid)
                ->orderBy('total_comment.comment_time', 'desc')
                ->get(['users.chinese_name', 'users.avatar','total_comment.comment_time','total_comment.comment_text','total_comment.comment_img','total_comment.comment_field']);
            $upd = FeedbackContent::where('id','=',$id)->update(['status'=>4]);
            $list['comments'] = $da;
//        }
        return returnJson($message="ok",$code="200",$data=$list);
    }

    // 回复写入
    public function reply($user){
        $data = Request::all();
        if(empty($data['relation_id'])){
            $result['code'] = ConstFile::API_RESPONSE_FAIL;
            $result['message'] = 'no';
            $result['data'] = 'relation_id参数不存在';
            return $result;
        }elseif(empty($data['comment_text'])){
            $result['code'] = ConstFile::API_RESPONSE_FAIL;
            $result['message'] = 'no';
            $result['data'] = 'comment_text参数不存在';
            return $result;
        }elseif(empty($data['type'])){
            $result['code'] = ConstFile::API_RESPONSE_FAIL;
            $result['message'] = 'no';
            $result['data'] = 'type参数不存在';
            return $result;
        }
        $data['comment_time'] = date('Y-m-d H:i:s', time());
        $data['uid'] = $user->id;
        $data['user_name'] = $user->chinese_name;

        $re = DB::table('total_comment')->insert($data);

        if($data['type']==5){ // 反馈
            $da = DB::table('feedback_content')->where('id','=',$data['relation_id'])->update(['status'=>3]);
        }
        $result['code'] = 200;
        $result['message'] = 'ok';
        return $result;
    }
}
