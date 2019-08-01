<?php

namespace App\Http\Controllers;

use App\Models\Workflow\Entry;
use Illuminate\Http\Request;
use Bouncer;
use App\Services\WorkflowMessageService;
use App\Services\WorkflowUserService;
use App\Models\User;
use App\Http\Helpers\Signature;
use App\Models\Workflow\Proc;
use App\Models\Workflow\Workflow;
use App\Models\Contract;
use Event;
use App\Services\pay\ExpensesReimburse;
use App\Services\Attendance\ChecktimeService;

use App\Events\GoToStatusActiveLeaveEvent;
use App\Events\GoToStatusConfirmLeaveEvent;
use App\Events\GoToWantedHandOverEvent;
use App\Events\GoToStatusFireEvent;
use App\Events\GoToWantedContractEvent;
use Exception;


class TestController extends Controller
{

    public function __construct()
    {
//        if (!config('app.debug')) {
//            echo "没有权限";
//            exit;
//        }
    }

    public function index(Request $request)
    {
        //$a = 'zhang';

                //dd(event(new GoToStatusConfirmLeaveEvent(4797)));
        $result = User::findOrFail(1857)->update(['status'=>User::STATUS_JOIN]);
        throw_if(!$result, new Exception('待合同:更新用户状态为 入职 没有成功'));
        //dd(event(new GoToStatusActiveLeaveEvent(4785)));
        //GoToStatusActiveLeaveEvent
        //dd(event(new WorkflowExpensesReimburseEvent(1030)));
//        $list = $this->fetchQnnCompanyList();
//        //$list = mb_convert_encoding($list, 'utf-8', 'gbk');
//        //dd(mb_strpos('账号', '号'));
//        if (false !== mb_strpos($list, $c)) {
//            dd('1');
//        } else {
//            dd('2');
//        }
//        exit;
        //$re = WorkflowUserService::fetchLeaderInfo(1833, 1);
        //$re = WorkflowUserService::fetchDepartmentMasterInfo(381);
        //$re = WorkflowUserService::fetchUserMainDepartmentInfo(1192);

        //dd(event(new WorkflowFinancePaymentFinishEvent(554)));
//        dd(event(new WorkflowFinancePaymentFinishEvent(554)));
//        dd(event(new WorkflowFinancePaymentFinishEvent(554)));

//        dd(event(new WorkflowFinanceInvoiceSignFinishEvent(642)));
//        $re = WorkflowUserService::fetchLeaderInfo(1192, 2);
//        dd($re);
//        $departmentId = 134;
//        $re = WorkflowUserService::fetchDeepList($departmentId);
//        print_r($re);
//        $start = '2018-08-19';
//        $end = '2018-08-20';
//        $dd = (new ChecktimeService())->generateSheet($start,$end);
//dd($dd);


//        $data = WorkflowUserService::fetchUserMainDepartmentInfo(1192);
//        dd($data);

//        $orderNo = '1111';
//        $id      = 8;
//        $entry   = Entry::find($id);
//        $dd = (new ExpensesReimburse)->entryToPayParams($orderNo, $entry);
//        dd($dd);
//        $signKey   = config('capital.sign_key');
//        $signature = new Signature($signKey);
//        $params    = ['key_words' => ''];
//        $arr       = $signature->attachSign($params);
//        print_r($arr);
        //Event::fire(new WorkflowContractApplyPlainEvent(113));
//        $info = WorkflowRole::getCompanyRoleUserByIds(1, [1, 2, 3]);
//        dd($info);
    }
}
