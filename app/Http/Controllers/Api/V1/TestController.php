<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Repositories\AccountRepository;
use App\Models\UserAccountProfit;
use App\Models\Investment;
use App\UserAccount\Account;


class TestController extends Controller
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

    public function list() {
        return $this->repository->getList();
    }



    public function testNewAttr() {
//
//        $account = new Account();
//        $users = User::all();
//        foreach ($users as $user) {
//            $account->setUser($user->id)->create();
//            echo $user->id, PHP_EOL;
//        }

		$account = new Account();
		$data = [];
		$data['title'] = "发工资";
		$data['sub'] = "发工资";

		echo $account->setUser(1791)->setAmount(30)->setParams($data)->increment(1);
		die;

		$investment = Investment::where('id', '=', '2')->first();
		$data =   [
			'user_id' => 1791,
			'amount'=>45,
			'model' => $investment
		];
		$account = new Account();
		$account->setParams($data)->increment(1);
	}
}
