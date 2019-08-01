<?php
/**
 * Created by PhpStorm.
 * User: chenzhikui
 * Date: 2019/4/9
 * Time: 2:14 PM
 */

namespace App\Http\Controllers\Api\V1\Attendance;


use App\Exceptions\DiyException;
use App\Http\Controllers\Api\V1\ApiController;
use App\Models\Attendance\AnnualRule;
use App\Models\Company;
use App\Models\PAS\Warehouse\VacationRule;
use Illuminate\Http\Request;

class VacationSetController extends ApiController
{
    public function __construct()
    {
        parent::__construct();
        $this->middleware('auth:api');
    }

    public function run(Request $request){
        $title = $request->get('title');              //假期名
        $unit = $request->get('unit');              //最小请假单位
        $leave_type = $request->get('leave_type');  //请假方式
        $is_balance = $request->get('is_balance');  //是否开启假期余额
        $balance_type = $request->get('balance_type');      //余额发放类型
        $balance_value = $request->get('balance_value');    //发放天数
        $expire_rule = $request->get('expire_rule');                  //有效期
        $is_add_expire = $request->get('is_add_expire');    //是否延长有效期
        $add_expire_value = $request->get('add_expire_value');  //有效期时长
        $newer_start_type = $request->get('newer_start_type');  //新员工开始时长
        $salary_percent = $request->get('salary_percent', 0);  //新员工开始时长


        $user = \Auth::user();

        /** @var Company $company */
        $company = $user->company;
        if(empty($company)){
//            throw new \Exception('数据异常', '500');
            throw new DiyException('数据异常', '500');
        }
        $data = [
            'title' => $title,
            'unit' => $unit,
            'leave_type' => $leave_type,
            'is_balance' => $is_balance,
            'balance_type' => $balance_type,
            'balance_value' => $balance_value,
            'expire_rule' => $expire_rule,
            'is_add_expire' => $is_add_expire,
            'add_expire_value' => $add_expire_value,
            'newer_start_type' => $newer_start_type,
            'salary_percent' => $salary_percent,
            'company_id' => $company->id,
            'cursor' => $user->id,
            'status' => 0
        ];
        try{
            $res = VacationRule::query()->create($data);
        }catch (\Exception $exception){
            throw $exception;
        }

        return $res;
    }

}