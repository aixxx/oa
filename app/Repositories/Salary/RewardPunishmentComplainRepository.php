<?php

namespace App\Repositories\Salary;

use App\Constant\ConstFile;
use App\Http\Requests\Salary\RewardPunishmentComplainRequest;
use App\Models\MyTask\MyTask;
use App\Models\Salary\RewardPunishmentComplain;
use App\Models\User;
use App\Models\Workflow\Entry;
use App\Repositories\Repository;
use App\Services\Workflow\FlowCustomize;
use Carbon\Carbon;
use Exception;
use DB;
use Auth;

class RewardPunishmentComplainRepository extends Repository {
    public function model() {
        return RewardPunishmentComplain::class;
    }


    public function add($data){
        $check_result = (new RewardPunishmentComplainRequest())->add($data);
        if($check_result !== true) return $check_result;

        try{
            RewardPunishmentComplain::query()->create($data);
            return returnJson('',ConstFile::API_RESPONSE_SUCCESS);
        }catch (Exception $e){
            return returnJson($e->getMessage(), ConstFile::API_RESPONSE_FAIL);
        }
    }
}