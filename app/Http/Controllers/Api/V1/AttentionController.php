<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\AttentionRepository;
use Auth;

class AttentionController extends Controller
{
    protected $attention;

    //构造函数
    function __construct(AttentionRepository $attention) {
        $this->attention = $attention;
    }


    /*
     * 添加取消关注
     * */
    public function addAttention(Request $request){
        $user = Auth::user();
        return $this->attention->addAttention($request->all(), $user);
    }


    /*
     * 关注列表
     * */
    public function attentionList(Request $request){
        return $this->attention->attentionList($request->all());
    }
}
