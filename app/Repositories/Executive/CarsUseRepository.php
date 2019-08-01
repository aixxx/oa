<?php

namespace App\Repositories\Executive;

use App\Constant\ConstFile;
use App\Exceptions\DiyException;
use App\Http\Requests\Executive\CarsRecordRequest;
use App\Http\Requests\Executive\CarsUseRequest;
use App\Models\Executive\Cars;
use App\Models\Executive\CarsUse;
use App\Models\Executive\CarsUseRelationCar;
use App\Models\Workflow\Entry;
use App\Repositories\Repository;
use App\Services\Workflow\FlowCustomize;
use Carbon\Carbon;
use Exception;
use DB;
use Auth;

class CarsUseRepository extends Repository {
    public function model() {
        return CarsUse::class;
    }

    public function getList($data){
        try{
            $list = CarsUse::query()->with(['sendback','belongsToManyCarsUseRelationCar','user','department'])->get();
            return returnJson('',ConstFile::API_RESPONSE_SUCCESS, $list);
        }catch (Exception $e){
            return returnJson($e->getMessage(), ConstFile::API_RESPONSE_FAIL);
        }
    }

    public function add($data){
        $check_result = (new CarsUseRequest())->add($data);
        if($check_result !== true) return $check_result;

        $cars = Cars::query()
            ->whereIn('id',$data['cars_id'])
            ->where('status', Entry::STATUS_FINISHED)
            ->where('car_status', Cars::CAR_STATUS_NORMAL)
            ->get();

        if (count($cars) < count($data['cars_id']))
            return returnJson('参数错误，请重新选择车辆', ConstFile::API_RESPONSE_FAIL);

        try{
            DB::transaction(function ($query) use ($data){
                //进入审核
                $entry = FlowCustomize::EntryFlow($data, Entry::WORK_FLOW_NO_EXECUTIVE_CREATE_CAR_USE);
                $data['entrise_id'] = $entry->id;
                $data['user_id'] = $data['driver_id'] = Auth::id();
                $data['department_id'] = Auth::user()->departUserPrimary->department_id;
                //保存记录
                $cars_use = CarsUse::query()->create($data);
                foreach ($data['cars_id'] as $v){
                    $list[] = [
                        'cars_id' => $v,
                        'cars_use_id' => $cars_use->id,
                        'created_at' => Carbon::now()->toDateTimeString(),
                        'updated_at' => Carbon::now()->toDateTimeString(),
                    ];
                }
                if(!$list)
                    throw new DiyException("车辆ID不能为空", ConstFile::API_RESPONSE_FAIL);
                CarsUseRelationCar::query()->insert($list);
                //修改申请车辆状态
                Cars::query()->whereIn('id', $data['cars_id'])
                    ->update(['car_status'=> Cars::CAR_STATUS_SUBSCRIBE]);
            });
            return returnJson('',ConstFile::API_RESPONSE_SUCCESS);
        }catch (Exception $e){
            return returnJson($e->getMessage(), ConstFile::API_RESPONSE_FAIL);
        }
    }
}