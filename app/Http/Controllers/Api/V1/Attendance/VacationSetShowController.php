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

class VacationSetShowController extends ApiController
{
    public function __construct()
    {
        parent::__construct();
        $this->middleware('auth:api');
    }

    public function run(Request $request){
        $id = $request->get('id');
        $res = [];
        $res['units'] = VacationRule::$_units;
        $res['leave_types'] = VacationRule::$_leave_types;
        $res['balance_type'] = VacationRule::$_balance_types;
        $res['expire_rules'] = VacationRule::$_expire_rules;
        $res['newer_start_type'] = VacationRule::$_newer_start_types;
        if($id){
            /** @var VacationRule $vacation */
            $vacation = VacationRule::query()->find($id);
            $vacation->unit_str = VacationRule::$_units[$vacation->unit];
            $vacation->leave_type_str = VacationRule::$_leave_types[$vacation->leave_type];
            $vacation->balance_type_str = VacationRule::$_balance_types[$vacation->balance_type];
            $vacation->expire_rule_str = VacationRule::$_expire_rules[$vacation->expire_rule];
            $vacation->newer_start_type_str = VacationRule::$_newer_start_types[$vacation->newer_start_type];
            $res['vacation_rule'] = $vacation;
        }

        return $res;
    }

}