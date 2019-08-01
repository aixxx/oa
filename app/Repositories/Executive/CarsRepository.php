<?php

namespace App\Repositories\Executive;

use App\Constant\ConstFile;
use App\Http\Requests\Executive\CarsRequest;
use App\Models\Executive\Cars;
use App\Models\Executive\CarsRecord;
use App\Models\Workflow\Entry;
use App\Models\Workflow\Workflow;
use App\Repositories\Repository;
use App\Repositories\UsersRepository;
use App\Services\Workflow\FlowCustomize;
use App\Services\WorkflowUserService;
use Doctrine\DBAL\Driver\IBMDB2\DB2Driver;
use Exception;
use DB;
use Auth;

class CarsRepository extends Repository {
    public function model() {
        return Cars::class;
    }

    public function getList($data){
        $cars = Cars::query();
        if(isset($data['status']) && $data['status'])
            $cars->where('status', $data['status']);
        if(isset($data['department_id']) && $data['department_id'])
            $cars->where('department_id', $data['department_id']);
        if(isset($data['car_number']) && $data['car_number'])
            $cars->where('car_number', 'like', "%{$data['car_number']}%");
        return $cars->with(['user', 'department'])->get();
        //return Cars::query()->with(['user', 'department'])->get();
    }


    public function getInfo($id){
        return Cars::query()->where('entrise_id', $id)->find();
    }

    public function add($data){
        try{
            $check_result = (new CarsRequest())->add($data);
            if($check_result !== true) return $check_result;

            DB::transaction(function () use ($data){
                $entry = FlowCustomize::EntryFlow($data, Entry::WORK_FLOW_NO_EXECUTIVE_CREATE_CAR);
                $data['entrise_id'] = $entry->id;
                //根据负责人ID 获取该用户的顶级部门
                $top = app()->make(UsersRepository::class)->getUserFirstAndSecond(Auth::id());
                $data['department_id'] = $top['top_id'];

                $info = Cars::query()->create($data);
                CarsRecord::query()
                    ->whereIn('id', $data['record'])
                    ->update(['cars_id'=> $info->id]);
            });
            return returnJson('',ConstFile::API_RESPONSE_SUCCESS);
        }catch (Exception $e){
            return returnJson($e->getMessage(), ConstFile::API_RESPONSE_FAIL);
        }
    }

    public function updated($data){
        try{
            $check_result = (new CarsRequest())->add($data);
            if($check_result !== true) return $check_result;

            if(isset($data['record'])) unset($data['record']);
            DB::transaction(function () use ($data){
                $entry_id = Cars::query()->where('id',$data['id'])->pluck('entrise_id')->first();
                FlowCustomize::EntryFlow($data, Entry::WORK_FLOW_NO_EXECUTIVE_CREATE_CAR, $entry_id);
                //重新审核
                $data['status'] = Entry::STATUS_IN_HAND;
                Cars::query()->where('id',$data['id'])->update($data);
            });
            return returnJson('操作成功',ConstFile::API_RESPONSE_SUCCESS);
        }catch (Exception $e){
            return returnJson($e->getMessage(), ConstFile::API_RESPONSE_FAIL);
        }
    }


    public function delete($id){
        try{
            $res = Cars::query()->where('id', $id)->delete();
            if($res){
                return returnJson('操作成功',ConstFile::API_RESPONSE_SUCCESS);
            }else{
                return returnJson('操作失败',ConstFile::API_RESPONSE_FAIL);
            }
        }catch (Exception $e){
            return returnJson($e->getMessage(), ConstFile::API_RESPONSE_FAIL);
        }
    }
}