<?php

namespace App\Http\Controllers\Rpc;

use App\Models\Department;
use App\Models\PAS\SaleOrder;
use App\Models\TransactionLog;
use App\Models\UserAccount;
use App\Repositories\UsersRepository;
use JWTAuth;

class FinanceController extends HproseController
{
    public function getOneList($date) {
        $usersRepository = app()->make(UsersRepository::class);
        $model = new TransactionLog();
        $d = strtotime($date);
        $firstday = date("Y-m-01", $d);
        $lastday = date("Y-m-d", strtotime("$firstday +1 month -1 day"));
        $model = $model->where('status_start_time', '>=', $firstday)->where('status_start_time', '<', $lastday);

        $user = \Auth::user();

        $organize = $usersRepository->getAllDept($dept_id = '1', $user);
        $apply_basic_info['organize'] = $organize;

        $departs = $usersRepository->getChild($organize[0]['id']);

        $departs=explode(',',$departs);
        unset($departs[0]);
        $depatlist = Department::whereIn('id',$departs)->select('id','name')->get();

 
        $data = [];
        foreach ($depatlist as $dep) {
            $ddds = $usersRepository->getChild($dep->id);
            $ddds=explode(',',$ddds);
            $modelTemp1 = clone $model;
            $modelTemp2 = clone $model;
            $modelTemp1->whereIn('department_id', $ddds);
            $modelTemp2->whereIn('department_id', $ddds);
            $in = $modelTemp1->where(['in_out'=>1])->sum('amount');
            $out = $modelTemp2->where(['in_out'=>2])->sum('amount');
            $data[] = [
                'dep_name' => $dep['name'],
                'in' => sprintf("%.2f",$in/100),
                'out' => sprintf("%.2f",$out/100),
            ];
        }
        return $data;
    }

    public function getPublicList($start = '', $end = '', $dept = 0) {
        $model = TransactionLog::where(['is_more_department'=>1]);

        if ($dept > 0) {
            $usersRepository = app()->make(UsersRepository::class);
            $deptIds = $usersRepository->getChild($dept);
            $deptIds = explode(',', $deptIds);
            $model->whereIn('department_id', $deptIds);
        }

        if($start != '' && $end != '') {
            $model->where('status_end_time', '>=', $start)->where('status_end_time', '<=', $end);
        }
        $list = $model->get();
        if(empty($list)) {
            return [
                'total'=>0,
                'list' => []
            ];
        }

        $data = [
            'total'=>$res = sprintf("%.2f",$model->sum('amount')/100),
            'list' => $list->toArray()
        ];
        return $data;
    }
}
