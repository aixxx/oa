<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\BasicOaTypeForm;
use App\Models\Basic\BasicOaOption;
use App\Repositories\Basic\BasicOaOptionRespository;
use App\Repositories\Basic\BasicOaTypeRespository;
use Request;


class AbilitiesController extends Controller
{
    /**
     *
     * @var BasicOaTypeRespository
     */
    protected $oATypeRespository;
    /**
     *
     * @var BasicOaOptionRespository
     */
    protected $optionRespository;

    public function __construct()
    {
        $this->oATypeRespository = app()->make(BasicOaTypeRespository::class);
        $this->optionRespository = app()->make(BasicOaOptionRespository::class);
        $this->middleware('adminAuth:admin');
    }

    public function index()
    {
        $all = Request::all();
        $abilities = $this->oATypeRespository->getList($all);

        return view('admin.abilities.index', compact('abilities'));
    }

    public function create()
    {

        $data = ['title' => '新增', 'items' => '', 'type' => 'create'];
        return view('admin.abilities.create', $data);
    }

    public function edit($id)
    {

        try {
            $obj = $this->oATypeRespository->with('getOption')->find($id);

        } catch (\Exception $exc) {

            return redirect(route('admin.abilities.index'));
        }
        $data = ['title' => '编辑', 'items' => $obj, 'type' => 'edit'];
        return view('admin.abilities.create', $data);
    }

    public function store(BasicOaTypeForm $form)
    {
        $data = $form->request->all();
        $id = $form->get('id', null);

        $optionData = $data['itemd'];
        unset($data['itemd']);
        unset($data['id']);

        if (!$id) {
            \DB::beginTransaction();
            try {
                $ret = $this->oATypeRespository->create($data);

                if ($optionData) {
                    foreach ($optionData as $val) {

                        $oret = $this->optionRespository->create([
                            'title' => $val['title'],
                            'type_id' => $ret->id,
                            'describe' => $val['describe']?$val['describe']:'',
                            'level' => is_int($val['describe'])?$val['describe']:'',
                            'status' => $val['status']
                        ]);


                    }
                }
                \DB::commit();

                $msg = [
                    'code' => 200,
                    'message' => '添加数据成功'
                ];
            } catch (\Exception $exc) {
                dd($exc->getMessage());
                \DB::rollBack();
                return redirect(route('admin.abilities.index'));
            }

        } else {
            \DB::beginTransaction();
            try {
                $ret = $this->oATypeRespository->update($data, $id);
                if ($optionData) {
                    foreach ($optionData as $val) {

                        if (isset($val['id']) && $val['id']) {
                            $this->optionRespository->update([
                                'title' => $val['title'],
                                'type_id' => $val['type_id'],
                                'describe' => $val['describe']?$val['describe']:'',
                                'level' => is_int($val['describe'])?$val['describe']:'',
                                'status' => $val['status']
                            ], $val['id']);


                        } else {

                            $this->optionRespository->create([
                                'title' => $val['title'],
                                'type_id' => $val['type_id'],
                                'describe' => $val['describe']?$val['describe']:'',
                                'level' => is_int($val['describe'])?$val['describe']:'',
                                'status' => $val['status']
                            ]);
                        }

                    }
                }
                \DB::commit();

            } catch (\Exception $exc) {
                dd($exc->getMessage());
                \DB::rollBack();
                return redirect(route('admin.abilities.index'));
            }

        }
        return \Response::json(['code' => 200, 'message' => '添加数据成功']);
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $id = Request::get('id');
            $this->oATypeRespository->delete($id);
            $this->optionRespository->deleteWhere(['type_id'=>$id]);

            DB::commit();
            return Response::json(['code' => 0, 'status' => 'success', 'message' => '删除成功']);
        } catch (\Exception $e) {
            DB::rollBack();
            return Response::json(['code' => $e->getCode(), 'status' => 'failed', 'message' => $e->getMessage()]);
        }
    }
}
