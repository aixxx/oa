<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Services\SmsTrait;
use App\Models\Company;
use App\Models\Department;
use App\Models\DepartUser;
use App\Models\User;
use Mockery\Exception;
use Tymon\JWTAuth\JWTAuth;
use Auth;
use Request;
use App\Repositories\UsersRepository;


class PubilcController extends BaseController
{
    use SmsTrait;
    protected $jwt;
    protected $repository;

    public function __construct(JWTAuth $jwt)
    {
        $this->repository = app()->make(UsersRepository::class);
        $this->jwt = $jwt;
    }
    public function send(){
        try{
            $mobile=Request::input('mobile');
            $type=Request::input('type',1);
            $rt= $this->traitSendSmsCode($mobile,$type);
            if($rt){
                return returnJson('发送成功', 200,$data=['code'=>$rt]);
            }else{
                return returnJson('发送失败', 400);
            }
        }catch(\Exception $e){
            return returnJson($e->getMessage(), $e->getCode());
        }

    }
    //获取主部门领导
    public function getDeptLeader(){
        $user=Auth::user();
        $info=DepartUser::getPrimaryLeader($user->fetchPrimaryDepartment[0]->id);
        $info['department']=$user->fetchPrimaryDepartment[0]->name;
        return returnJson('发送成功', 200,$data=['data'=>$info]);
    }


}
