<?php

namespace App\Repositories;

use App\Models\UserAccount;
use App\Models\UserAccountRecord;
use App\Models\Workflow\Entry;
use Auth;
use Illuminate\Http\Request;

class AccountRepository extends ParentRepository
{
    public function model()
    {
        return UserAccount::class;
    }

    public function getInfo()
    {
        // 获取本人用户的信息
        $user = Auth::user();
        $user_id = $user->id;
        // 获取用户账户
        $userAccount = UserAccount::where('user_id', '=', $user_id)->first();

        $data = [];
        $data['init_status']=0;
        $data['balance']=$data['all_profit']=$data['all_profit']=$data['new_profit']=$data['investment_profit']
            =$data['wage_profit']=$data['dividend_profit']="0.00";
        if(empty($userAccount)) {
            $this->data = $data;
            return $this->returnApiJson();
        }
        $data['balance'] = $userAccount->balance;

        // 累计收益
        $data['all_profit'] = UserAccountRecord::where('user_id', '=', $user_id)->sum('balance');
        $before = date("Y-m-d H:i:S", strtotime("-7days"));

        // 近期收益
        $data['new_profit'] = UserAccountRecord::where([['user_id', '=', $user_id], ['created_at', '>', $before]])->sum('balance')/100;


        // 投资收益
        $data['investment_profit'] = UserAccountRecord::where([['user_id', '=', $user_id], ['account_type_id', '=', 1]])->sum('balance')/100;

        // 投资收益
        $data['wage_profit'] = UserAccountRecord::where([['user_id', '=', $user_id], ['account_type_id', '=', 2]])->sum('balance')/100;

        // 工资收益
        $data['wage_profit'] = UserAccountRecord::where([['user_id', '=', $user_id], ['account_type_id', '=', 2]])->sum('balance')/100;

        // 分红收益
        $data['dividend_profit'] = UserAccountRecord::where([['user_id', '=', $user_id], ['account_type_id', '=', 3]])->sum('balance')/100;

        $this->data = $data;
        return $this->returnApiJson();
    }

    public function getList(Request $request)
    {
        // 获取本人用户的信息

        $params = $request->get('type');
        $user = Auth::user();
        $user_id = $user->id;
        $record_type = $request->get('type');
        $month_start = $request->get('create_begin');
        $month_end = $request->get('create_end');
        $size = $request->get('size', 10);
        $page = $request->get('page', 1);
        $where[] = ['user_id', '=', $user_id];

        if (!empty($record_type)){
            $where[] = ['type', '=', $record_type];
        }

        if (!empty($month_start)){
            $where[] = ['created_at', '>=', $month_start];
        }

        if (!empty($month_end)){
            $where[] = ['created_at', '<=', $month_end];
        }

        $count = UserAccountRecord::where($where)->orderBy('created_at', 'desc')->orderBy('id', 'desc')->count();
        $list = UserAccountRecord::where($where)->orderBy('created_at', 'desc')->orderBy('id', 'desc')->paginate($size,['*'], 'page', $page);
        $all_list = UserAccountRecord::where($where)->orderBy('created_at', 'desc')->orderBy('id', 'desc')->get();


        $temp = [];
        $prev = '';
        if (empty($list)) {
            $this->data = [];
            return $this->returnApiJson();
        }
        $spending = 0;//总支出
        $income   = 0;//总收入
        foreach ($all_list as $k => $v){
            if ($v->type){
                $spending += $v->balance;
            }else{
                $income += $v->type;
            }
        }

        foreach ($list as $key => $val) {
            $temp[$key]['title'] = $val->title;
            if ($val->type){
                $temp[$key]['type'] = '支出';
                $temp[$key]['balance'] = '-'.$val->balance;
            }else{
                $temp[$key]['type'] = '收益';
                $temp[$key]['balance'] = '+'.$val->balance;
            }
        //报销类型
        switch ($val->account_type) {
            case UserAccountRecord::ACCOUNT_INVESTMENT:
                $temp[$key]['account_type'] = UserAccountRecord::ACCOUNT_INVESTMENT_NAME;//投资收益
                break;
            case UserAccountRecord::ACCOUNT_WAGE:
                $temp[$key]['account_type'] = UserAccountRecord::ACCOUNT_WAGE_NAME;//工资收益
                break;
            case UserAccountRecord::ACCOUNT_SHARE:
                $temp[$key]['account_type'] = UserAccountRecord::ACCOUNT_SHARE_NAME;//分红收益
                break;
            case UserAccountRecord::ACCOUNT_EXPENSE_ACCOUNT:
                $temp[$key]['account_type'] = UserAccountRecord::ACCOUNT_EXPENSE_ACCOUNT_NAME;//报销
                break;
            case UserAccountRecord::ACCOUNT_PAY:
                $temp[$key]['account_type'] = UserAccountRecord::ACCOUNT_PAY_NAME;//支付
                break;
            case UserAccountRecord::ACCOUNT_BORROWING:
                $temp[$key]['account_type'] = UserAccountRecord::ACCOUNT_BORROWING_NAME;//借款
                break;
            case UserAccountRecord::ACCOUNT_REIMBURSEMENT:
                $temp[$key]['account_type'] = UserAccountRecord::ACCOUNT_REIMBURSEMENT_NAME;//还款
                break;
            case UserAccountRecord::ACCOUNT_COLLECTION:
                $temp[$key]['account_type'] = UserAccountRecord::ACCOUNT_COLLECTION_NAME;//收款
                break;
            default :
                $temp[$key]['account_type'] = '未知';
                break;
        }
            $temp[$key]['created_at'] = date('Y-m-d H:i:s', strtotime($val->created_at));
//            $temp[] = $val;
//            if (){
//
//            }
//            dump($val);die();
//            if ($key == 1) {
//                $prev = date('Y-m-d', strtotime($val['created_at']));;
//                $val['first'] = $prev;
//            }
//            // 显示时间
//            if ($prev != date('Y-m-d', strtotime($val['created_at']))) {
//                $prev = date('Y-m-d', strtotime($val['created_at']));
//                $val['created_at_show'] = date('Y-m-d', strtotime($val['created_at']));
//                $val['first'] = $prev;
//                $val['is_show_title'] = true;
//            } else {
//                $val['is_show_title'] = false;
//            }
//            $val['project_name'] = $val->record_title;
//            $val['account_type_show'] = $val->account_type_id > 0 ? '收益' : '支出';
//            $val['account_type_small_show'] = $val->account_type_id == 1 ? '投' : '发';
//            $temp[] = $val;
        }

        $data['list'] = $temp;
        $data['spending'] = sprintf("%.2f", $spending);
        $data['income'] = sprintf("%.2f", $income);
        $data['count'] = $count;
        $data['page'] = $page;

        $this->data = $data;
        return $this->returnApiJson();
    }

/*
 * auth wpc
 * $title string 标题
 * $type int 类型  App\Models\UserAccountRecord
 * $account_type int 收益类型 App\Models\UserAccountRecord
 * $balance float 金额
 * */
    public function insertInfo($title,$type,$account_type,$balance){

      /*  if (empty($title) || !is_string($title)){
            return false;
        }
        if (empty($type) || !is_int($title)){
            return false;
        }
        if (empty($account_type) || !is_int($title)){
            return false;
        }
        if (empty($balance) || !is_float($title)){
            return false;
        }*/
//        //报销类型
//        switch ($account_type) {
//            case ACCOUNT_INVESTMENT:
//                $category = AccountType::ACCOUNT_INVESTMENT;//投资收益
//                break;
//            case ACCOUNT_WAGE:
//                $category = AccountType::ACCOUNT_WAGE;//工资收益
//                break;
//            case ACCOUNT_SHARE:
//                $category = AccountType::ACCOUNT_SHARE;//分红收益
//                break;
//            case ACCOUNT_EXPENSE_ACCOUNT:
//                $category = AccountType::ACCOUNT_EXPENSE_ACCOUNT;//报销
//                break;
//            case ACCOUNT_PAY:
//                $category = AccountType::ACCOUNT_PAY;//支付
//                break;
//            case ACCOUNT_BORROWING:
//                $category = AccountType::ACCOUNT_BORROWING;//借款
//                break;
//            case ACCOUNT_REIMBURSEMENT:
//                $category = AccountType::ACCOUNT_REIMBURSEMENT;//还款
//                break;
//            case ACCOUNT_COLLECTION:
//                $category = AccountType::ACCOUNT_COLLECTION;//收款
//                break;
//            default :
//                return false;
//                break;
//        }
        $user = Auth::user();
        $user_id = $user->id;

        $params = [
            'user_id'   => $user_id,
            'title'   => $title,
            'type' => $type,
            'account_type'   => $account_type,
            'balance'   => $balance,
            'created_at'   => strtotime('Y-m-d H:i:s',time()),
        ];

        return UserAccountRecord::create($params);

    }
}
