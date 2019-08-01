<?php

namespace App\Repositories\Executive;

use App\Constant\ConstFile;
use App\Exceptions\DiyException;
use App\Http\Requests\Executive\CarsUseRequest;
use App\Models\Executive\Cars;
use App\Models\Executive\CarsAppoint;
use App\Models\Executive\CarsAppointRelationCar;
use App\Models\Executive\CarsUse;
use App\Models\Executive\CarsUseRelationCar;
use App\Models\Message\Message;
use App\Models\Workflow\Entry;
use App\Repositories\Repository;
use App\Services\Workflow\FlowCustomize;
use Carbon\Carbon;
use Exception;
use DB;
use Auth;

class CarsAppointRepository extends Repository {
    public function model() {
        return CarsAppoint::class;
    }

    public function option($data){
        $info = CarsAppoint::query()
            ->with('belongsToManyCarsAppointRelationCar')
            ->where('id', $data['id'])
            ->where('driver_id', Auth::id())
            ->where('status', Entry::STATUS_FINISHED)
            ->first();
        if (empty($info))
            return returnJson('数据不存在', ConstFile::API_RESPONSE_FAIL);

        if($data['type'] == 1){
            //同意派车
            try{
                DB::transaction(function () use ($data, $info){
                    CarsAppoint::query()
                        ->where('id', $info->id)
                        ->update(['status'=> CarsAppoint::STATUS_AGREE]);
                    $cars_info = $info->toArray();
                    unset($cars_info['id']);
                    //记录车辆使用信息
                    $cars_use = CarsUse::query()->create($cars_info);
                    //记录关联车辆
                    foreach ($info->belongsToManyCarsAppointRelationCar as $v){
                        $list[] = [
                            'cars_id' => $v->cars_id,
                            'cars_use_id' => $cars_use->id,
                            'created_at' => Carbon::now()->toDateTimeString(),
                            'updated_at' => Carbon::now()->toDateTimeString(),
                        ];
                    }
                    if(!$list)
                        throw new DiyException("车辆ID不能为空", ConstFile::API_RESPONSE_FAIL);
                    CarsUseRelationCar::query()->insert($list);
                    //更改车辆状态
                    Cars::query()
                        ->where('id', $info->cars_id)
                        ->update(['car_status'=> Cars::CAR_STATUS_USEING]);
                });
                return returnJson('',ConstFile::API_RESPONSE_SUCCESS);
            }catch (Exception $e){
                return returnJson($e->getMessage(), ConstFile::API_RESPONSE_FAIL);
            }
        }else{
            try{
                DB::transaction(function () use ($data, $info){
                    CarsAppoint::query()
                        ->where('id', $info->id)
                        ->update(['status'=> CarsAppoint::STATUS_REFUSE]);
                    Cars::query()
                        ->where('id', $info->cars_id)
                        ->update(['car_status'=> Cars::CAR_STATUS_NORMAL]);
                });
                return returnJson('',ConstFile::API_RESPONSE_SUCCESS);
            }catch (Exception $e){
                return returnJson($e->getMessage(), ConstFile::API_RESPONSE_FAIL);
            }
        }
    }

    public function getList($data){
        try{
            $list = CarsAppoint::query()
                ->with(['belongsToManyCarsAppointRelationCar','user','department'])
                ->get();

            return returnJson('',ConstFile::API_RESPONSE_SUCCESS, $list);
        }catch (Exception $e){
            return returnJson($e->getMessage(), ConstFile::API_RESPONSE_FAIL);
        }
    }

    public function add($data){
        $check_result = (new CarsUseRequest())->add($data);
        if($check_result !== true) return $check_result;

        $cars = Cars::query()
            ->where('id',$data['cars_id'])
            ->where('status', Entry::STATUS_FINISHED)
            ->where('car_status', Cars::CAR_STATUS_NORMAL)
            ->first();

        if (empty($cars))
            return returnJson('参数错误，请重新选择车辆', ConstFile::API_RESPONSE_FAIL);

        try{
            DB::transaction(function ($query) use ($data){
                //进入审核
                // 派车无需审核
                //$entry = FlowCustomize::EntryFlow($data, Entry::WORK_FLOW_NO_EXECUTIVE_CREATE_CAR_APPOINT);
                $data['entrise_id'] = 0;//$entry->id;
                $data['user_id'] = Auth::id();
                $data['status'] = Entry::STATUS_FINISHED;
                //保存记录
                $cars_appoint = CarsAppoint::query()->create($data);
                //保存关联车辆记录
                foreach ($data['cars_id'] as $v){
                    $list[] = [
                        'cars_id' => $v,
                        'cars_appoint_id' => $cars_appoint->id,
                        'created_at' => Carbon::now()->toDateTimeString(),
                        'updated_at' => Carbon::now()->toDateTimeString(),
                    ];
                }
                if(!$list)
                    throw new DiyException("车辆ID不能为空", ConstFile::API_RESPONSE_FAIL);
                CarsAppointRelationCar::query()->insert($list);
                //修改申请车辆状态
                Cars::query()->where('id', $data['cars_id'])
                    ->update(['car_status'=> Cars::CAR_STATUS_SUBSCRIBE]);
                //给用车人发送通知
                Message::addMessage($data['user_id'], $data['driver_id'], '用车确认',
                    $cars_appoint->id, Message::MESSAGE_CARS_APPOINT);
            });
            return returnJson('',ConstFile::API_RESPONSE_SUCCESS);
        }catch (Exception $e){
            return returnJson($e->getMessage(), ConstFile::API_RESPONSE_FAIL);
        }
    }
}