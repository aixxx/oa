<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Repositories\AccountRepository;
use Illuminate\Http\Request;

class AccountsController extends Controller
{
    /**
     *
     * @var UserRespository
     */
    protected $respository;


    //构造函数
    function __construct()
    {
        $this->repository = app()->make(AccountRepository::class);
    }
    public function index() {
        return $this->repository->getInfo();
    }

    public function list(Request $request) {
        return $this->repository->getList($request);
    }

//    public function insert() {
//
////        $params = $request->all();
//
////        return response()->json(['status' => 'success', 'messages' => "取消部门领导成功！"]);
//        return $this->repository->insertInfo($title,$type,$account_type,$balance);
//    }
}
