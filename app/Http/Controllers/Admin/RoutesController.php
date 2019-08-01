<?php

namespace App\Http\Controllers\Admin;

use App;
use App\Constant\ConstFile;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreVueActionRequest;
use App\Models\Power\Routes;
use App\Models\Power\RoutesRoles;
use App\Models\Power\VueAction;
use App\Repositories\VueAction\ApiRoutesRepository;
use App\Repositories\VueAction\VueActionRepository;
use DB;
use Illuminate\Http\Request;
use Response;

class RoutesController extends Controller
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
        $routes = $this->fetchRoutes()->simplePaginate(ConstFile::PAGE_SIZE, ['*']);
        return view('admin.routes.index', compact('routes'));
    }


    public function fetchRoutes()
    {
        return Routes::query()->orderBy('id', 'desc');
    }


    public function create()
    {
        $data = ['title' => '新增', 'items' => '', 'type' => 'create'];

        return view('admin.routes.create', $data);
    }

    public function store(Request $request)
    {
        try {
            $data = $request->all();
            $msg = ['code' => ConstFile::API_RESPONSE_SUCCESS, 'message' => ConstFile::API_RESPONSE_SUCCESS_MESSAGE];

            if (!isset($data['itemd']) || empty($data['itemd'])) {
                throw new \Exception('角色名称已经存在');
            }
            \DB::beginTransaction();
            if ($data['itemd']) {
                $route = [];
                foreach ($data['itemd'] as $key => $val) {
                    $error = $this->checkData($val);
                    if ($error) {
                        throw new \Exception('请求参数错误：' . $error);
                    }
                    $route[$key]['path'] = $val['path'];
                    $route[$key]['title'] = $val['path'];
                }
                $this->apiRoutesRepository->insert($route);
            }
            \DB::commit();

        } catch (\Exception $exc) {
            \DB::rollBack();
            $msg['code'] = ConstFile::API_RESPONSE_FAIL;
            $msg['message'] = $exc->getMessage();
        }
        return \Response::json($msg);
    }

    public function edit($id)
    {
        try {
            $obj = $this->apiRoutesRepository->find($id);
        } catch (\Exception $exc) {
            return redirect(route('admin.routes.index'));
        }
        $data = ['title' => '编辑', 'items' => $obj, 'type' => 'edit'];
        return view('admin.routes.edit', $data);
    }

    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $info = $request->all();
            $route = Routes::findOrFail($id);
            $this->checkData($info, $id);
            $route->update($info);
            DB::commit();
            return Response::json(['code' => 0, 'status' => 'success', 'message' => '修改成功']);
        } catch (\Exception $e) {
            DB::rollBack();
            return Response::json(['code' => $e->getCode(), 'status' => 'failed', 'message' => $e->getMessage()]);
        }
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
        return redirect(route('admin.routes.edit', [$actionId]));
    }

    public function show()
    {
    }

    public function checkData($data)
    {
        if (empty($data)) {
            return '请求数据不能为空';
        }
        if (!isset($data['path']) || empty($data['path'])) {
            return '接口别名不能为空';
        }
        if (!isset($data['title']) || empty($data['title'])) {
            return '接口名称不能为空';
        }
        return null;
    }
}
