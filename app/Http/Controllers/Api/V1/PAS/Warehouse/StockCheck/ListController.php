<?php

namespace App\Http\Controllers\Api\V1\PAS\Warehouse\StockCheck;

use App\Http\Controllers\Api\V1\ApiController;
use App\Models\PAS\Warehouse\StockCheck;
use Illuminate\Http\Request;
use Auth;


class ListController extends ApiController
{


    public function run(Request $request)
    {
        $status = $request->input('status', 1);
        $type = $request->input('type', 1);
        $limit = $request->input('limit', 10);
        $query = StockCheck::query()->with(['warehouse', 'check_user']);
        if($type==1){
            $user= Auth::user();
            $query->where('status', '=', $user->id);
        }
        if($status){
            $query->where('status', '=', $status);
        }
        $list = $query->select('id,check_no,created_at,status')->paginate($limit);
        if($list){
            $list=$list->toArray();
        }else{
            $list=[];
        }
        return $list;
    }
}
