<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Basic\BasicOaType;
use App\Repositories\Basic\BasicOaTypeRespository;
//use Dingo\Api\Routing\Helpers; // trait
use App\Http\Controllers\Controller;


class BaseController extends Controller
{
    //use Helpers;

    /**
     * @var array
     */
    protected $user;
    /**
     * @var int
     */
    protected $userId;

    /**
     * @var BasicOaTypeRespository
     */
    protected $oTypeRespository;

    /****
     * BaseController constructor.
     */
    public function __construct()
    {
        $this->oTypeRespository = app()->make(BasicOaTypeRespository::class);

    }

    /*
     * 获取不同的OA基础选项 UserCode
     * @param $code String 参数code
     * @return array 失败返回[]
     * */
    function getCodeOption($code)
    {
        $data = [];
        $oaType = BasicOaType::where(['code'=>$code,'status'=>1])->select('id','title')->first();
        if ($oaType) {
            $data = $oaType->getOption->pluck( 'title','id')->toArray();
        }
        return $data;

    }


}
