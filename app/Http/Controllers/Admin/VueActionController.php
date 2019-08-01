<?php

namespace App\Http\Controllers\Admin;

use App;
use App\Constant\ConstFile;
use App\Http\Controllers\Controller;
use App\Models\Power\VueAction;
use App\Http\Requests\Admin\StoreVueActionRequest;
use App\Repositories\VueAction\VueActionRepository;
use App\Repositories\VueAction\ApiRoutesRepository;
use DB;
use App\Models\Power\Routes;
use App\Models\Power\RoutesRoles;
use App\Models\Power\VueRoutes;
use Response;
use Request;

class VueActionController extends Controller
{
    protected $operateLog;

    protected $repository;

    protected $apiRoutesRepository;

    public function __construct()
    {
        $this->repository = app()->make(VueActionRepository::class);
        $this->apiRoutesRepository = app()->make(ApiRoutesRepository::class);
        $this->middleware('adminAuth:admin');
        $this->operateLog = App::make('operatelog');
        $this->middleware('afterlog:admin')->only('store', 'update', 'destroy');
    }

    public function index()
    {
        $vueAction = $this->fetchRoutes()->simplePaginate(ConstFile::PAGE_SIZE, ['*']);

        $links = str_replace(['&amp;laquo;', '&amp;raquo;'], ['<<', '>>'], $vueAction->links());
        return view('admin.vueaction.index', compact('vueAction', 'links'));
        //return view('admin.users.index');
    }


    public function fetchRoutes()
    {
        return VueAction::with('belongsToManyRotes')->orderBy('id', 'desc');
    }


    public function create()
    {
        $parentId = Request::get('parentId');
        $routes = Routes::all();
        $data = ['title' => '新增', 'items' => $routes, 'type' => 'create', 'parent_id' => $parentId];

        return view('admin.vueaction.create', $data);
    }

    public function store(StoreVueActionRequest $form)
    {
        try {
            $data = $form->request->all();
            $msg = ['code' => ConstFile::API_RESPONSE_SUCCESS, 'message' => ConstFile::API_RESPONSE_SUCCESS_MESSAGE];

            $id = $form->get('id', null);
            $routesData = [];
            if(isset($data['name_item']) && $data['name_item']){
                $routesData = $data['name_item'];
            }
            unset($data['name_item']);
            unset($data['id']);
            if (!$id) {
                \DB::beginTransaction();
                $ret = $this->repository->create($data);
                if ($routesData) {
                    $route = [];
                    foreach ($routesData as $key => $val) {
                        $route[$key]['route_id'] = $val;
                        $route[$key]['action_id'] = $ret->id;
                    }
                    VueRoutes::query()->insert($route);
                }
                \DB::commit();
            } else {
                \DB::beginTransaction();
                $ret = $this->repository->update($data, $id);
                if ($routesData) {
                    foreach ($routesData as $val) {

                        if (isset($val['id']) && $val['id']) {
                            $this->apiRoutesRepository->update([
                                'path' => $val['path'],
                                'title' => $val['title'],
                                'action_id' => $ret->id
                            ], $val['id']);
                        } else {
                            $this->apiRoutesRepository->create([
                                'path' => $val['path'],
                                'title' => $val['title'],
                                'action_id' => $ret->id
                            ]);
                        }
                    }
                }
                \DB::commit();
            }
        } catch (\Exception $exc) {
            \DB::rollBack();
            echo $exc->getMessage();exit;
            $msg['code'] = ConstFile::API_RESPONSE_FAIL;
            $msg['message'] = ConstFile::API_RESPONSE_FAIL_MESSAGE;
        }
        return \Response::json($msg);
    }

    public function edit($id)
    {

        try {
            $obj = $this->repository->with('hasManyRoutes')->find($id);

        } catch (\Exception $exc) {

            return redirect(route('admin.vueaction.index'));
        }
        $data = ['title' => '编辑', 'items' => $obj, 'type' => 'edit'];
        return view('admin.vueaction.create', $data);
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $vueAction = VueAction::findOrFail($id);
            Routes::where('action_id', $id)->delete();
            RoutesRoles::where('action_id', $id)->delete();
            $vueAction->delete();
            DB::commit();
            return Response::json(['code' => 0, 'status' => 'success', 'message' => '删除成功']);
        } catch (\Exception $e) {
            DB::rollBack();
            return Response::json(['code' => $e->getCode(), 'status' => 'failed', 'message' => $e->getMessage()]);
        }
    }

    public function destroyRoutes()
    {
        DB::beginTransaction();
        try {
            $id = Request::get('id');
            $actionId = Request::get('action_id');
            Routes::where('id', $id)->delete();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
        }
        return redirect(route('admin.vueaction.edit', [$actionId]));
    }

    public function show()
    {
    }
}
