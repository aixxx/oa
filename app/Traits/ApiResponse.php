<?php
/**
 * Created by PhpStorm.
 * User: chenzhikui
 * Date: 2019/4/8
 * Time: 4:50 PM
 */

namespace App\Traits;


use App\Constant\ConstFile;
use App\Exceptions\DiyException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

trait ApiResponse
{
    public function index(Request $request){
        try{
            $res = $this->run($request);
            return [
                'message' =>  ConstFile::API_RESPONSE_SUCCESS_MESSAGE,
                'code' => ConstFile::API_RESPONSE_SUCCESS,
                'data' => $res
            ];
        }catch (DiyException $exception){
            $data = [];
            return [
                'message' =>  $exception->getMessage(),
                'code' => $exception->getCode(),
                'data' => $data
            ];
        }catch (\Exception $exception){
            $data = [
                'message' => $exception->getMessage(),
                'code' => $exception->getCode(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'trace' => $exception->getTrace(),
            ];
            Log::debug('api request error:', $data);

            if(!config('app.debug')){
                $data = [];
            }
            return [
                'message' =>  ConstFile::API_RESPONSE_FAIL_MESSAGE,
                'code' => ConstFile::API_RESPONSE_FAIL,
                'data' => $data
            ];
        }

    }

    public function run(Request $request){
        return [];
    }
}