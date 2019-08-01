<?php

namespace App\Repositories\Executive;

use App\Constant\ConstFile;
use App\Http\Requests\Executive\CarsRecordRequest;
use App\Http\Requests\Executive\CarsRequest;
use App\Models\Executive\CarsRecord;
use App\Repositories\Repository;
use Exception;
use DB;

class CarsRecordRepository extends Repository {
    public function model() {
        return CarsRecord::class;
    }

    public function getList($data){
        $check_result = (new CarsRecordRequest())->getList($data);
        if($check_result !== true) return $check_result;

        try{
            $map = [
                ['type', '=', $data['type']],
                ['cars_id', '=',$data['cars_id']],
            ];
            if(isset($data['wq']) && $data['wq']){
                $map[] = ['address', 'like', '%'.$data['wq'].'%'];
            }
            $list = CarsRecord::query()
                ->where($map)
                ->get();

            return returnJson('',ConstFile::API_RESPONSE_SUCCESS, $list);
        }catch (Exception $e){
            return returnJson($e->getMessage(), ConstFile::API_RESPONSE_FAIL);
        }
    }

    public function add($data){
        $check_result = (new CarsRecordRequest())->add($data);
        if($check_result !== true) return $check_result;

        try{
            $info = CarsRecord::query()->create($data);
            return returnJson('',ConstFile::API_RESPONSE_SUCCESS, ['id'=> $info->id]);
        }catch (Exception $e){
            return returnJson($e->getMessage(), ConstFile::API_RESPONSE_FAIL);
        }
    }
}