<?php

namespace App\Http\Controllers;

use App\Models\Basic\BasicSet;
use Illuminate\Http\Request;


class BasicController extends Controller
{

    public function __construct()
    {
    }

    public function index(Request $request)
    {
        $result = BasicSet::query()->where('id','>',0)->first();

        if(!$result){
            $data =[
                    'website_name'=>'后台系统名称',
                    'login_greetings'=>'欢迎您',
                    'created_at'=>date('Y-m-d H:i:s'),
                    'updated_at'=>date('Y-m-d H:i:s'),
                ];
            BasicSet::query()->insert($data);
            $result = BasicSet::query()->where('id','>',0)->first();
        }
        return view('basic.index')->with(compact("result"));
    }
    public function update(Request $request)
    {
        $name = $request->post('name');
        if(empty($name)){
            return response()->json([
                'success'=>-1,
                'message'=>'系统名称不能为空'
            ]);
        }
        $greetings = $request->post('greetings');
        if(empty($greetings)){
            return response()->json([
                'success'=>-1,
                'message'=>'欢迎词不能为空'
            ]);
        }
        $id = $request->post('id');
        $data =[
            'website_name'=>$name,
            'login_greetings'=>$greetings,
            'created_at'=>date('Y-m-d H:i:s'),
            'updated_at'=>date('Y-m-d H:i:s'),
        ];
        $results = BasicSet::query()->where('id',$id)->update($data);
        return redirect()->route('basic.index')->with(compact(['success'=>1,'message'=>'添加成功']));
    }
}
