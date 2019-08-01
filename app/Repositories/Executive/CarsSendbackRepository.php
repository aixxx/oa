<?php

namespace App\Repositories\Executive;

use App\Constant\ConstFile;
use App\Http\Requests\Executive\CarsRecordRequest;
use App\Http\Requests\Executive\CarsSendbackRequest;
use App\Http\Requests\Executive\CarsUseRequest;
use App\Models\Executive\Cars;
use App\Models\Executive\CarsSendback;
use App\Models\Executive\CarsUse;
use App\Models\Workflow\Entry;
use App\Repositories\Repository;
use App\Services\Workflow\FlowCustomize;
use Exception;
use DB;
use Auth;

class CarsSendbackRepository extends Repository {
    public function model() {
        return CarsSendback::class;
    }

    public function getList($data){
        try{
            $list = CarsSendback::query()->with(['carsuse','cars','user','department'])->get();
            return returnJson('',ConstFile::API_RESPONSE_SUCCESS, $list);
        }catch (Exception $e){
            return returnJson($e->getMessage(), ConstFile::API_RESPONSE_FAIL);
        }
    }

    public function add($data){
        $check_result = (new CarsSendbackRequest())->add($data);
        if($check_result !== true) return $check_result;

        $cars_use = CarsUse::query()
            ->where('id',$data['cars_use_id'])
            ->where('status', Entry::STATUS_FINISHED)
            ->first();

        if (empty($cars_use))
            return returnJson('参数错误，请重新选择用车记录', ConstFile::API_RESPONSE_FAIL);

        try{
            DB::transaction(function ($query) use ($data, $cars_use){
                //进入审核
                $entry = FlowCustomize::EntryFlow($data, Entry::WORK_FLOW_NO_EXECUTIVE_CREATE_CAR_SENDBACK);
                $data['entrise_id'] = $entry->id;
                $data['user_id'] = Auth::id();
                $data['cars_id'] = $cars_use->cars_id;
                //保存记录
                CarsSendback::query()->create($data);
            });
            return returnJson('',ConstFile::API_RESPONSE_SUCCESS);
        }catch (Exception $e){
            return returnJson($e->getMessage(), ConstFile::API_RESPONSE_FAIL);
        }
    }
}