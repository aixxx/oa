<?php

namespace App\Http\Controllers;

use App\Constant\ConstFile;
use App\Http\Helpers\Dh;
use App\Models\AttendanceApi\AttendanceApiDepartment;
use App\Models\Position\Position;
use App\Models\Position\PositionDepartment;
use App\Models\User;
use App\Services\AttendanceApi\AttendanceApiService;
use App\Traits\FileUpload;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Department;
use Illuminate\Support\Facades\DB;
use App\Models\DepartUser;
use Illuminate\Support\Facades\Log;

class DepartmentController extends Controller
{
    use FileUpload;

    public function __construct()
    {
        $this->middleware('afterlog')->only('store', 'update', 'destroy');
    }

    public function index(Request $request)
    {
        /*  $id = $request->get('id',1);
          if($id > 1){
              $departments =  Department::with('users')->where('id', '=', $id)->orderBy('order', 'desc')->get();
          }else{
              $departments =  Department::with('users')->where('parent_id', '=', 1)->orderBy('order', 'desc')->get();
          }

          return view('department.index',compact('departments'));*/
        $dateTime   = $request->input('month');
        $month = $dateTime ?: Dh::now(Dh::DATETIME_FORMAT_TYPE_YM);
        $chooseTime = $dateTime ? date('Y-m-t 23:59:59', strtotime($dateTime)) : Dh::now('Y-m-d H:i:s');

        $userId            = auth()->id();
        $departments       = Department::getSecondDepartment($chooseTime);
        $departIds       = DepartUser::getDepartIdsByUserIdAndTime($userId, $chooseTime);
        $ability           = User::ABILITY_USERS_SETTING_SHOW;
        $isCanLookUserInfo = \Bouncer::can($ability);//是否有查看用户详情的权限
        //如果用户为管理员或者HR则人力地图全部展示
        $isAdmin         = false;
        $isAdministrator = \Bouncer::is(User::find($userId))->a(User::ROLES_ADMINISTRATOR);
        $isHR            = \Bouncer::is(User::find($userId))->a(User::ROLES_HR_MANAGER);
        if ($isAdministrator || $isHR) {
            $isAdmin = true;
        }
        if ($isAdmin) {//获取所有记录
           $departIds = DepartUser::getDepartIdsByUserIdAndTime(null, $chooseTime);
        }
        $allDepartUsers      = DepartUser::getDepartIdsByTime($chooseTime);//获取指定时间的所有部门员工关系记录
        $isPrimary = [];
        $isLeader = [];
        foreach ($allDepartUsers as $departUser) {
            $isPrimary[$departUser->user_id . '_' . $departUser->department_id] = $departUser->is_primary;
            $isLeader[$departUser->user_id . '_' . $departUser->department_id]  = $departUser->is_leader;
        }

        $allDepartments   = Department::getAllDepartmentByTime($chooseTime);//获取指定时间的所有部门记录
        $isHaveChildren   = [];
        foreach ($allDepartments as $department) {
            $isHaveChildren[$department->id] = Department::isHaveChildren($department->id, $chooseTime);
        }

        $personalDepartments = Department::getByIds($departIds);//获取登陆用户所能看到的部门
        //找出该用户部门的所有父部门和子部门
        $childrenDeparts  = $this->getDepartChildren($personalDepartments, $isAdmin, $userId, $chooseTime,$isLeader, $isHaveChildren);
        $parentDeparts    = $this->getDepartParent($personalDepartments);
        $allDepartAutoIds = array_unique(array_merge($childrenDeparts, $parentDeparts));
        return view('department.index', compact('departments', 'childrenDeparts', 'parentDeparts', 'allDepartAutoIds', 'isCanLookUserInfo', 'chooseTime', 'isPrimary', 'isLeader', 'isHaveChildren','month'));
    }

    public function getDepartChildren($departments, $isAdmin, $userId, $chooseTime, $isLeader, $isHaveChildren)
    {
        $departIds = [];
        foreach ($departments as $department) {
            if ($isAdmin ||  $isLeader[$userId.$department->id]) {//如果是部门领导则本部门算入子部门
                $departIds[] = $department->auto_id;
            }
            $childrenDeparts =$isHaveChildren[$department->id];//子部门
            if ($childrenDeparts) {
                foreach ($childrenDeparts as $childrenDepart) {
                    $departIds[] = $childrenDepart->auto_id;
                }
                $this->getDepartChildren($childrenDeparts, $isAdmin, $userId, $chooseTime,$isLeader, $isHaveChildren);
            }
        }
        return array_unique($departIds);
    }

    public function getDepartParent($departments)
    {
        $departIds = [];
        foreach ($departments as $department) {
            $departIds[] = $department->auto_id;
            $idArr       = $this->getDepartParentId($department);
            $departIds   = array_merge($departIds, $idArr);
        }
        return array_unique($departIds);
    }

    public function getDepartParentId($department)
    {
        $idArr = [];
        $parentId      = $department->parent_id;//父部门
        $newDepartment = Department::getByDepartId($parentId);
        $idArr[]       = $parentId;
        if ($parentId != Department::ROOT_DEPARTMENT_ID) {
            $this->getDepartParentId($newDepartment);
        }
        return $idArr;
    }

    public function leader()
    {
        $id = \request('id');

        return ['ok' => $id];
    }

    /**
     * 创建部门
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        $departmentInfo = $request->all();

        $insertData['name']           = $departmentInfo['name'];
        $insertData['order']          = !empty($departmentInfo['order']) ? $departmentInfo['order'] : 0;
        $insertData['is_sync_wechat'] = !isset($departmentInfo['is_sync_wechat']) ? Department::SYNC_WECHAT_YES : $departmentInfo['is_sync_wechat'];

        $flag = false;
        if (isset($departmentInfo['departmentId']) && isset($departmentInfo['parent_id'])) {
            //编辑部门
            $insertData['parent_id'] = $departmentInfo['parent_id'];
            $department              = Department::findOrFail($departmentInfo['departmentId']);
        } elseif (isset($departmentInfo['departmentId']) && !isset($departmentInfo['parent_id'])) {
            //在上级部门节点上创建
            $insertData['parent_id'] = $departmentInfo['departmentId'];
            $department              = new Department;
            $insertData['id']        = Department::generateDepartId();//部门Id
            $flag = true;
        } elseif (!isset($departmentInfo['departmentId']) && isset($departmentInfo['parent_id'])) {
            //直接创建
            $insertData['parent_id'] = $departmentInfo['parent_id'];
            $department              = new Department;
            $insertData['id']        = Department::generateDepartId();
            $flag = true;
        }


        $department->fill($insertData);

        if ($department->save()) {
            //创建部门时，根据父级的考勤ID， 创建新的考勤部门关联
            if($flag){
                $attendance_id = AttendanceApiDepartment::query()
                    ->where('department_id', $department->parent_id)
                    ->pluck('attendance_id')
                    ->first();
                if(!empty($attendance_id)){
                    $data = [
                        'department_id' => $department->id,
                        'attendance_id' => $attendance_id,
                    ];
                }else{
                    $data = [
                        'department_id' => $department->id,
                        'attendance_id' => AttendanceApiService::DEFAULT_ID,
                    ];
                }
                AttendanceApiDepartment::query()->create($data);
            }
            if (env('SYNC_WECHAT')) {
                if ($department->is_sync_wechat) {
                    $ret = $this->saveWechatDepartment($department->toArray());

                    if ($ret['errcode'] != 0) {
                        return response()->json(['status' => 'failed']);
                        //throw new \Exception("员工企业微信同步失败");
                    }
                }

            }

            return response()->json(['status' => 'success', 'dept_id' => $department->id]);
        } else {
            return response()->json(['status' => 'failed']);
        }
    }

    /**
     * 更新部门
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function update(Request $request)
    {
        $departmentInfo               = $request->all();
        $insertData['name']           = $departmentInfo['name'];
        $insertData['order']          = !empty($departmentInfo['order']) ? $departmentInfo['order'] : 0;
        $insertData['is_sync_wechat'] = !isset($departmentInfo['is_sync_wechat']) ? Department::SYNC_WECHAT_YES : $departmentInfo['is_sync_wechat'];

        if (isset($departmentInfo['departmentId']) && isset($departmentInfo['parent_id'])) {
            //编辑部门
            $insertData['parent_id'] = $departmentInfo['parent_id'];
            $department              = Department::findOrFail($departmentInfo['departmentId']);
        }

        //为了记录历史改变记录，编辑后软删除老记录新增新纪录
        $newDepartment    = new Department();
        $insertData['id'] = $department->id;
        $newDepartment->fill($insertData);
        $newDepartment->save();
        /*  $departUsers = DepartUser::getByDepartId($department->id);
          $this->updateDepartUser($departUsers, $newDepartment);*/
        $department->delete();

        //$department->fill($insertData);

        if ($department->save()) {

            if (env('SYNC_WECHAT')) {
                if ($department->is_sync_wechat) {
                    $ret = $this->saveWechatDepartment($department->toArray());

                    if ($ret['errcode'] != 0) {
                        return response()->json(['status' => 'failed']);
                        //throw new \Exception("员工企业微信同步失败");
                    }
                }

            }

            return response()->json(['status' => 'success']);
        } else {
            return response()->json(['status' => 'failed']);
        }
    }

    public function updateDepartUser(DepartUser $departUsers, $newDepartment)
    {
        foreach ($departUsers as $departUser) {
            //将原本的depart_user关系迁移到新的记录中，更换departId
            $result['department_id'] = $newDepartment->id;
            $result['user_id']       = $departUser->user_id;
            $result['is_leader']     = $departUser->is_leader;
            $result['is_primary']    = $departUser->is_primary;
            $newDepartUser           = new DepartUser();
            $newDepartUser->fill($result);
            $newDepartUser->save();
            $departUser->delete();
        }
    }

    private function saveWechatDepartment($data)
    {
        $this->work = app('wechat.work.contacts');

        $wechatDpets = $this->getWechatDepts();

        if (array_key_exists($data['id'], $wechatDpets)) {
            return $this->updateWechatDepartment($data);
        } else {
            return $this->createWechatDepartment($data);
        }
    }

    private function createWechatDepartment($data)
    {
        $this->work = app('wechat.work.contacts');


        $w_data = [
            'id'       => $data['id'],
            'name'     => $data['name'],
            'parentid' => $data['parent_id'],
            'order'    => $data['order'],
        ];

        $r = $this->work->department->create($w_data);

        Log::info("sync wechat department : create " . $data['name'] . "  " . $r['errmsg'] . ' ' . json_encode($w_data));
        return $r;
    }

    private function updateWechatDepartment($data)
    {
        $this->work = app('wechat.work.contacts');

        $w_data = [
            'id'       => $data['id'],
            'name'     => $data['name'],
            'parentid' => $data['parent_id'],
            'order'    => $data['order']
        ];

        $r = $this->work->department->update($data['id'], $w_data);

        Log::info("sync wechat department : update " . $data['name'] . " " . $r['errmsg'] . ' ' . json_encode($w_data));

    }

    private function getWechatDepts()
    {
        $this->work = app('wechat.work.contacts');
        $list       = [];
        $response   = $this->work->department->list();

        Log::info("sync wechat department : get list " . $response['errmsg']);

        if ($response['errcode'] != 0) {
            $this->error('无法获取数据: ' . $response['errmsg']);
        }

        foreach ($response['department'] as $row) {
            //$r = $this->work->department->delete($row['id']);
            //$this->info($row['id'] . $r['errmsg']);

            $list[$row['id']] = $row;
        }

        return $list;
    }

    /**
     * @param $data ['id'=>1,'name'=>1111]
     * @return mixed
     */
    private function deleteWechatDepartment($data)
    {

        $this->work = app('wechat.work.contacts');

        $r = $this->work->department->delete($data['id']);


        Log::info("sync wechat department : delete " . $data['id'] . " " . $r['errmsg']);
        return $r;
    }

    private function getWechatDepartmentList()
    {

        $this->work = app('wechat.work.contacts');

        $r = $this->work->department->list();

        Log::info("sync wechat department : get list  " . $r['errmsg']);
        return $r;
    }

    /**
     * 部门删除
     * @param Request $request
     *
     * @return Json
     */
    public function destroy(Request $request)
    {
        $deptId           = $request->get('deptId');
        $childdepartments = $this->getChildDepartment($deptId);

        $depts = Department::where('id', '=', $deptId)->first()->toArray();

        $childDepartmentIds = collect($childdepartments)->map(function ($entry) {
            return $entry->id;
        })->toArray();

        if ($childDepartmentIds) {
            return response()->json(['status' => 'error', 'messages' => "部门内有子部门"]);
        }

        array_push($childDepartmentIds, intval($deptId));

        $deptUserInfo = DB::table('department_user')->whereIn('department_id', $childDepartmentIds)->get()->toArray();

        if ($deptUserInfo) {
            return response()->json(['status' => 'error', 'messages' => "部门内有员工无法删除！"]);
        }

        //$desStatus = Department::destroy($deptId);
        $desStatus = Department::find($deptId)->delete();
        if ($desStatus) {
            if (env('SYNC_WECHAT')) {
                if ($deptId) {
                    if ($depts['is_sync_wechat']) {
                        $ret = $this->deleteWechatDepartment($depts);
                        if ($ret['errcode'] != 0) {
                            return response()->json(['status' => 'failed']);
                            //throw new \Exception("员工企业微信同步失败");
                        }
                    }

                }


            }

            return response()->json(['status' => 'success', 'messages' => "删除成功"]);
        } else {
            return response()->json(['status' => 'failed', 'messages' => "删除失败"]);
        }
    }

    /*
     * Get all of departmental resources
     * @param   department id  $id
     * @return  department list
     */
    public function allOld($id = 0)
    {

        if (!$id) {
            $sql         = "SELECT * FROM departments ORDER BY parent_id ASC, `order` DESC, id ASC";
            $departments = DB::select($sql);
        } else {
            $subsql      = "(SELECT DISTINCT parent_id FROM departments WHERE id = $id)";
            $sql         = "SELECT * FROM departments WHERE parent_id = $subsql OR id = $subsql OR parent_id = $id";
            $departments = DB::select($sql);
        }

        $departmentsTree = [];
        $node            = [];
        collect($departments)->map(function ($entry) use (&$departmentsTree, &$node) {
            $node['id']        = $entry->id;
            $node['parent_id'] = $entry->parent_id;
            $node['name']      = $entry->name;
            $node['childList'] = [];

            $departmentsTree = $this->insertNode($departmentsTree, $node, $entry->parent_id, $entry->deepth);
        });
        return response()->json($departmentsTree);
    }

    private function insertNode($tree, $node, $parentId, $deepth)
    {
        if (!$tree) {
            if (!$parentId || $deepth == 2) {
                $tree = $node;
                return $tree;
            }
        }

        if (isset($tree['id']) && $tree['id'] == $parentId) {
            array_push($tree['childList'], $node);
        } else {
            if (isset($tree['childList'])) {
                foreach ($tree['childList'] as &$entry) {
                    $entry = $this->insertNode($entry, $node, $parentId, $deepth);
                }
            }
        }
        return $tree;
    }

    /**
     * 获取完整组织架构
     *
     * @param int $id
     *
     * @return JsonResponse
     */
    public function all($id = 0)
    {

        if (!$id) {
            $sql         = "SELECT * FROM departments where `deleted_at` IS NULL OR `deleted_at`='' ORDER BY id ASC ";
            $departments = DB::select($sql);
        } else {
            $subsql      = "(SELECT DISTINCT parent_id FROM departments WHERE id = $id)";
            $sql         = "SELECT * FROM departments WHERE parent_id = $subsql OR id = $subsql OR parent_id = $id and (`deleted_at` IS NULL OR `deleted_at`='')";
            $departments = DB::select($sql);
        }

        $departmentsTree = [];

        $count = 0;
        while (count($departments) > 0) {

            foreach ($departments as $index => $dept) {
                $node                   = [];
                $node['id']             = $dept->id;
                $node['parent_id']      = $dept->parent_id;
                $node['parent_name']    = Department::getDepartmentName($dept->parent_id);
                $node['path']           = $dept->name;
                $node['name']           = $dept->name;
                $node['is_sync_wechat'] = $dept->is_sync_wechat;
                $node['childList']      = [];
                $node['order']          = $dept->order;

                $return = $this->insertNodeNew($departmentsTree, $node);
                if ($return) {
                    unset($departments[$index]);
                }
            }

            $count++;
        }

        return response()->json($departmentsTree);
    }

    private function insertNodeNew(&$tree, $node)
    {
        $parentId = $node['parent_id'];
        if (!$tree) {
            if (!$parentId) {
                $node['path'] = $node['name'];
                $tree         = $node;
                return true;
            }
        }

        if (isset($tree['id']) && $tree['id'] == $parentId) {
            $node['path'] = $tree['path'] . '/' . $node['name'];
            array_push($tree['childList'], $node);

            $order = [];
            foreach ($tree['childList'] as $childNode) {
                $order[] = $childNode['order'];
            }
            array_multisort($order, SORT_DESC, $tree['childList']);
            return true;
        } else {
            if (isset($tree['childList'])) {
                foreach ($tree['childList'] as &$entry) {

                    $return = $this->insertNodeNew($entry, $node);

                    if ($return) {
                        return true;
                    }
                }
            } else {
                return false;
            }
        }
        return false;
    }

    /*
     * Get information about department employees
     * @param   department id  $id
     * @return employees list
     */
    public function user($id = 1, Request $request)
    {

        if ($request->get('departid')) {
            $id = $request->get('departid');
        }

        //分页参数
        $params['departid'] = $id;

        $departmentInfo = $this->getChildDepartment($id);

        $departmentId = collect($departmentInfo)->map(function ($entry) {
            return $entry->id;
        })->toArray();


        if ($id) {
            array_push($departmentId, intval($id));
        }

        //先按照部门Order排序
        $deptSort = Department::whereIn('id', $departmentId)->orderBy('parent_id', 'asc')->orderBy('order', 'desc')->orderBy('id', 'asc')->select('*')->get()->toArray();
        $sortedId = [];
        array_map(function ($every) use (&$sortedId) {
            $sortedId[] = $every['id'];
        }, $deptSort);

        //再按照部门ID，用户ID排序
        $usersModel = DepartUser::with('user')->select('user_id')->whereIn('department_id', $sortedId)
            ->groupBy('user_id')->orderBy('user_id');

        $totalnum = $usersModel->get()->count();

        $users = $usersModel->paginate(30);
        foreach ($users as $value) {
            if ($value->user) {
                $departUser[] = $value->user->id;
            }
        }

        if (isset($departUser)) {
            $userDapartment = DepartUser::with('department')->whereIn('user_id', $departUser)->get();
            foreach ($userDapartment as $key => $every) {
                $pathName                                                      = [];
                $userInfo[$every->user_id]['name'][$every->department_id]      = $every->department->name;
                $userInfo[$every->user_id]['path'][$every->department_id]      = Department::getDeptPath($every->department_id, $pathName);
                $userInfo[$every->user_id]['depart_id'][]                      = $every->department->id;
                $userInfo[$every->user_id]['is_leader'][$every->department_id] = $every->is_leader;
            }

            $pri_dept_id = [];

            //获取主部门ID
            $mainDepart = DepartUser::whereIn('user_id', $departUser)->where('is_primary', '=', 1)->select('department_id', 'user_id')->get()->toArray();
            foreach ($mainDepart as $every) {
                $pri_dept_id[] = $every['department_id'];
            }


            $departName = Department::whereIn('id', $pri_dept_id)->select('name', 'id')->get()->toArray();
            $priSort    = [];

            array_map(function ($every) use (&$priSort, $mainDepart) {
                foreach ($mainDepart as $key => $value) {
                    $pathName = [];
                    if ($every['id'] == $value['department_id']) {
                        $priSort[$value['user_id']]['name']    = $every['name'];
                        $priSort[$value['user_id']]['pripath'] = Department::getDeptPath($value['department_id'], $pathName);
                    }
                }
            }, $departName);


            foreach ($users as &$every) {

                if (isset($priSort[$every->user_id])) {
                    $every->pri_name = $priSort[$every->user_id]['name'];
                    $every->pri_path = $priSort[$every->user_id]['pripath'];
                } else {
                    // TODO
                }

            }

        }

        $primarynum = $this->getPrimaryUserNum($id);

        if ($id) {
            $curDept = Department::where('id', '=', $id)->first();
        } else {
            $curDept = Department::where('parent_id', '=', 0)->first();
        }

        if (!$curDept) {
            abort(500, "当前员工无部门！");
        }

        return view('department.users', compact('users', 'totalnum', 'primarynum', 'curDept', 'userInfo', 'params'));
    }


    /**
     * 设置部门主管
     */
    public function setLeader(Request $request)
    {
        $requestData = $request->all();
        if ($requestData) {
            $leaderInfo = DB::table('department_user')->where('department_id', '=', $requestData['dept_id'])->where('user_id', '=', $requestData['user_id'])
                ->first();

            if ($leaderInfo->is_leader == 1) {
                $cancelLeader = DB::table('department_user')->where('department_id', '=', $requestData['dept_id'])->where('user_id', '=', $requestData['user_id'])
                    ->update(['is_leader' => 0]);
                if ($cancelLeader) {
                    return response()->json(['status' => 'success', 'messages' => "取消部门领导成功！"]);
                } else {
                    return response()->json(['status' => 'failed', 'messages' => "取消部门领导失败！"]);
                }

            } elseif ($leaderInfo->is_leader == 0) {
                $setLeader = DB::table('department_user')->where('department_id', '=', $requestData['dept_id'])->where('user_id', '=', $requestData['user_id'])
                    ->update(['is_leader' => 1]);
                if ($setLeader) {
                    return response()->json(['status' => 'success', 'messages' => "设置部门领导成功！"]);
                } else {
                    return response()->json(['status' => 'failed', 'messages' => "设置部门领导失败！"]);
                }
            }
        } else {
            return response()->json(['status' => 'error', 'messages' => '参数信息传递有误！']);
        }
    }


    private function getChildDepartment($id)
    {
        static $return = [];
        $departmentInfo = Department::where('parent_id', '=', $id)->get();
        if (!$departmentInfo) {
            $innderDepartment = Department::where('id', '=', $id)->first();
            if ($innderDepartment) {
                $return[] = $innderDepartment;
            }
        }

        foreach ($departmentInfo as $entry) {
            $return[] = $entry;
            $this->getChildDepartment($entry->id);
        }

        return $return;
    }


    private function getPrimaryUserNum($id)
    {

        $num = DepartUser::where('department_id', '=', $id)->where('is_primary', '=', 1)->count();
        return $num;

    }

    /**
     * 获取部门列表页
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function depart()
    {
        $departments = Department::paginate(30);

        foreach ($departments as $key => $department) {
            $department->cur_path = Department::getDeptPath($department->id);
        }

        $allDepartments = Department::all();

        if (isset($departments)) {
            foreach ($departments as $key => $every) {
                $departmentInfo                   = Department::where('id', '=', $every->parent_id)->first();
                $departments[$key]['parent_name'] = $departmentInfo ? $departmentInfo->name : '';
                $departments[$key]['parent_path'] = Department::getDeptPath($every->parent_id);
            }
        }
        $totalnum   = $departments->count();
        $primarynum = $this->getPrimaryUserNum($departments);
        return view('department.departs', compact('departs', 'totalnum', 'primarynum', 'departments', 'allDepartments'));
    }

    /**
     * 批量导入部门
     * @param Request $request
     */
    public function batchImport(Request $request){
        $result = $this->upload($request);
        if(empty($result))
            return returnJson('上传文件内没有数据',ConstFile::API_RESPONSE_FAIL);

        try{
            DB::beginTransaction();
                $dept_list = Department::query()->pluck('name','id')->all();
                foreach ($result as $k => $v){
                    $dept_name = $v[0];     //部门名称
                    $dept_position = $v[1];     //部门内的职位
                    $dept_parent_name = $v[2];     //上级部门名称
                    $dept_is_sync_wechat = $v[3];     //是否同步企业微信
                    //查看父级部门是否存在
                    $parent_id = array_search($dept_parent_name, $dept_list);
                    if($parent_id === false)
                        throw new \Exception("文件中第 ".($k + 1)." 行的: '上级部门名称' 不存在，请检查");

                    //添加部门
                    $departParam = [
                        'name' => $dept_name,
                        'order' => 0,
                        'is_sync_wechat' => $dept_is_sync_wechat == "是" ? Department::SYNC_WECHAT_YES : Department::SYNC_WECHAT_NO,
                        'parent_id' => $parent_id,
                    ];
                    $dept = $this->store(new Request($departParam));
                    $dept_result = json_decode($dept->getContent(), true);
                    //关联职位
                    $position_list = explode(',', $dept_position);
                    foreach ($position_list as $pk => $pv){
                        $is_leader = Position::STATUS_IS_LEADER_NO;
                        if($pk == 0){
                            $is_leader = Position::STATUS_IS_LEADER_YES;
                        }
                        $position_param = [
                            'deptId' => $dept_result['dept_id'],
                            'name' => $pv,
                            'is_leader' => $is_leader,
                        ];
                        Position::create($position_param);
                        throw new \Exception("over");
                        /*$position = Position::query()->create([
                            'name' => $pv,
                            'is_leader' => $is_leader,
                        ]);

                        PositionDepartment::query()->create([
                            'position_id' => $position->id,
                            'department_id' => $dept_result['dept_id']
                        ])

                        $position_param[] = [

                            'created_at' => Carbon::now()->toDateTimeString(),
                            'updated_at' => Carbon::now()->toDateTimeString(),
                        ];*/
                    }
                    //Position::query()->insert($position_param);
                }
            DB::commit();
            return returnJson(ConstFile::API_RESPONSE_SUCCESS_MESSAGE, ConstFile::API_RESPONSE_SUCCESS);
        }catch (\Exception $e){
            DB::rollBack();
            return returnJson($e->getMessage(), $e->getCode());
        }
    }

}
