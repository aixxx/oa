<?php

namespace App\Repositories;

use App\Models\Seals\Seals;
use App\Models\Seals\SealsType;
use DB;

class SealsRepository extends Repository
{

    public function model()
    {
        return Seals::class;
    }

    public function seals_type_add($data,$user){
        if(!$data['seal_type_name'] || empty($data['seal_type_name'])){
            return returnJson($message = '类型名称不能为空', $code = '1001');
        }
        $name = trim($data['seal_type_name']);
        $where['company_id'] = $user->company_id;
        $where['seal_type_name'] = $name;
        $set = DB::table('company_seals_type')->where($where)->first();
        if($set){
            return returnJson($message = '类型名称已存在', $code = '1002');
        }
        $info['company_id'] = $user->company_id;
        $info['seal_type_name'] = $data['seal_type_name'];
        $info['create_user_id'] = $user->id;
        $seals = SealsType::create($info);
        if($seals){
            return returnJson($message = '添加成功', $code = '200');
        }else{
            return returnJson($message = '添加失败', $code = '1003');
        }
    }

    public function upload_seals($data,$user){

        $where['id'] = $data['seals_type_id'];
        $where['company_id'] = $user->company_id;
        $name = DB::table('company_seals_type')->where($where)->first();
        if(!$name || empty($name)){
            return returnJson($message = '印章类型有误', $code = '1001');
        }
        if($data['password'] != $data['confirmPassword']){
            return returnJson($message = '前后密码不一致', $code = '1004');
        }
        $insert['seals_type_id'] = $data['seals_type_id'];
        $insert['password'] = $data['password'];
        $insert['seal_img'] = $data['seal_img'];
        $insert['upload_user_id'] = $user->id;
        $sult = Seals::create($insert);
        if($sult){
            return returnJson($message = '上传成功', $code = '200');
        }else{
            return returnJson($message = '上传失败', $code = '-1');
        }
    }
}