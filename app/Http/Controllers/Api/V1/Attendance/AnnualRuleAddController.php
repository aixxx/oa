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
use App\Models\CompanyAnnualRule;
use Illuminate\Http\Request;

class AnnualRuleAddController extends ApiController
{
    public function __construct()
    {
        parent::__construct();
        $this->middleware('auth:api');
    }

    public function run(Request $request){
        $rules = $request->get('rules');
        $user = \Auth::user();

        /** @var Company $company */
        $company = $user->company;
        if(empty($company)){
            throw new DiyException('数据异常', '500');
        }
        foreach ($rules as $rule){
            list($id, $min, $max, $value, $type, $description) = [0,0,0,0,1,0];
            extract($rule);
            $data = [
                'min' => $min,
                'max' => $max,
                'value' => $value,
                'type' => $type,
//                'company_id' => $company->id,
                'description' => $description,
            ];
            try{
                if($id){
                    AnnualRule::query()->where('company_id', '=', $company->id)->where('id', '=', $id)->update($data);
                }else{
                    $res = AnnualRule::query()->create($data);
                    CompanyAnnualRule::query()->create(['company_id'=> $company->id, 'rule_id' => $res->id, 'type' => $type]);
                }
            }catch(\Exception $exception){
                throw $exception;
            }
        }
        return true;
    }

}