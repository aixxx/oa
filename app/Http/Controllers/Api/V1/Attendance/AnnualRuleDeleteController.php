<?php
/**
 * Created by PhpStorm.
 * User: chenzhikui
 * Date: 2019/4/9
 * Time: 2:14 PM
 */

namespace App\Http\Controllers\Api\V1\Attendance;


use App\Exceptions\DiyException;
use App\Exceptions\SystemException;
use App\Http\Controllers\Api\V1\ApiController;
use App\Models\Attendance\AnnualRule;
use App\Models\Company;
use Illuminate\Http\Request;

class AnnualRuleDeleteController extends ApiController
{
    public function __construct()
    {
        parent::__construct();
        $this->middleware('auth:api');
    }

    public function run(Request $request){
        $id = intval($request->get('id'));
        /** @var AnnualRule $obj */
        $obj = AnnualRule::query()->find($id);
        return $obj->delete();
    }

}