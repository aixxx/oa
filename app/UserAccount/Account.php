<?php
namespace App\UserAccount;


use App\Models\ProfitProject;
use App\Models\User;
use App\Models\UserAccount;
use App\Models\UserAccountRecord;
use Illuminate\Support\Facades\DB;

/**
 * 用户账户 增删改查
 * Class Account
 * @author 潘迪峰
 * @package App\UserAccount
 */
class Account
{
	protected $amount = 0;
	protected $params = '';
	protected $model = null;
	protected $user_id = 0;

    public function getUser() {
        return $this->user_id;
    }
    public function setUser($user_id) {
        $this->user_id = $user_id;
        return $this;
    }

    public function getModel() {
        return $this->model;
    }
    public function setModel($model) {
        $this->model = $model;
        return $this;
    }
	public function getParams() {
		return $this->params;
	}
	public function setParams($params) {
		$this->params = $params;
		return $this;
	}
    public function getAmount() {
        return $this->amount;
    }
    public function setAmount($amount) {
        $this->amount = $amount;
        return $this;
    }
    /**
     * 新建用户账户, 主要用户入职的时候写入
     * @param int $user_id 用户ID
     * @return bool
     */
    public function create() {
        $user_id = $this->getUser();
        if ($user_id <= 0) return false;

        // 判断用户是否存在
        $user = User::where('id', '=', $user_id)->get();

        if ($user->isEmpty()) return false;

        // 判断账户是否存在
        $user = UserAccount::where('user_id', '=', $user_id)->get();

        if (!$user->isEmpty()) return false;

        // 保存用户账户
        $userAccount = new UserAccount();
        if ($userAccount->firstOrCreate ( ['user_id' => $user_id], [
            'user_id'=>$user_id,
            'balance'=>0,
        ]) ) return true;

        return false;
    }

    


    public function increment($account_type_id =  0 ) {
		if($account_type_id == 0) {
			return '必须传入参数: 1:投资 2:工资 3:分红 , -1 支出';
		}
		$params = $this->getParams();
		$user_id = $this->getUser();
		$model = $this->getModel();
		$amount = $this->getAmount();
		$titles['title'] = isset($params['title'])? $params['title']:'';
		$titles['sub'] = isset($params['sub'])? $params['sub']:'';
		if($user_id <= 0) {
			return '未设置用户';
		}
		
		if($model == null && !isset($params['title']) && !isset($params['sub'])) {
			return '单例模式 标题/副标题 必须传 ->getParams([title:...,sub:...])';
		}

		if ($amount == 0){
			return '金额未设置:setAmount($amount)';
		}
		
		// 判段模型是否支持
		if( $model != null && class_exists($model) && !method_exists($model,'getAccountRecordTitle')) {
			return '不可用模型';
		}

		if($model != null) {
			$titles = $model->getAccountRecordTitle();
			if(!isset($titles['title']) && !isset($titles['sub'])) {
				return '模型中getAccountRecordTitle： 返回值[title:...,sub:...]';
		       }
		}
		

		if($amount == 0 || $amount == '' || $amount == null) {
			return '金额必须不等于 0';
		}

		$userAccount = new UserAccount();
		$userAccount = $userAccount->where('user_id', '=', $user_id)->first();
		if (!$userAccount) {
			return '账户不存在';
		}

		if ( $amount<0) {
			$sub = $userAccount->balance + $amount;
			if ($sub < 0) {return '少于账户金额';}
		}

		// 插入事务
		DB::transaction(function () use (&$user_id, &$amount, &$account_type_id ,&$model, &$titles) {
			$userAccount = new UserAccount();
			// 账户扣款
			$userAccount->increment('balance', $amount);
			$userAccountRecord = new UserAccountRecord();
			$data = [];
			if($model !=  null ) {
				$data['model_id'] = $model->id;
				$data['is_correlation_model'] = 1;
				$data['model_name'] = get_class($model);
				$titles = $model->getAccountRecordTitle();
			} else {
				$data['model_id'] = 0;
				$data['is_correlation_model'] = 0;
				$data['model_name'] = '' ;
			}
			$data['user_id'] = $user_id;
			$data['account_type_id'] = $account_type_id;
			$data['title']  = $titles['title'];
			$data['sub']  = $titles['sub'];
			$data['balance'] = $amount;

			$user_account_record = $userAccountRecord->create($data);
			if ($user_account_record->id > 0) {
				DB::rollBack();
			}
		});
		return true;
    }
}