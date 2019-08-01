<?php

namespace XinApp\Http\Controllers\Api;

use App\Repositories\Basic\BasicOaTypeRespository;
use Illuminate\Http\Request;
use Dingo\Api\Routing\Helpers;
use App\Http\Controllers\Controller;


class BaseController extends Controller
{
    use Helpers;

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
    //protected $oTypeRespository;

    /****
     * BaseController constructor.
     */
    public function __construct()
    {
        $this->oTypeRespository = app()->make(BasicOaTypeRespository::class);
    }

    /*
    * 获取不同的OA基础选项
    * @param $code String 参数code
    * @return array 失败返回[]
    * */
    function getCodeOption($code)
    {
        $data = [];
        $oaType = $this->oTypeRespository->findWhere(['code'=>$code,'status'=>1],['id','title']);

        if ($oaType) {
            $data = $oaType->getOption->pluck( 'title','id')->toArray();
        }
        return $data;

    }


}
