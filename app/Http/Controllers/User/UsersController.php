<?php

namespace App\Http\Controllers\User;

use App\Constant\ConstFile;
use App\Http\Helpers\Dh;
use App\Http\Requests\UserRequest;
use App\Models\Attendance\AttendanceVacation;
use App\Models\Attendance\AttendanceWorkClass;
use App\Models\OperateLog;
use App\Models\PendingUser;
use App\Models\TagUser;
use App\Models\User;
use App\Models\UserBankCard;
use App\Models\UserFamily;
use App\Models\UsersDimission;
use App\Models\UserUrgentContact;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use App\Models\UsersDetailInfo;
use App\Models\DepartUser;
use App\Models\Company;
use App\Models\Department;
use Overtrue\Pinyin\Pinyin;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use App\Http\Controllers\Controller;
use \Exception;
use Auth;
use Response;
use Bouncer;
use DevFixException;
use UserFixException;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Str;

class UsersController extends Controller
{
    public $status = [1, 9];

    public function __construct()
    {
        $this->userlog = \app()->make('userlog');
        $this->middleware('afterlog')->only('store', 'update', 'destroy');
    }

    /**
     * 当前用户
     * @return mixed
     */
    public function me()
    {

        $user = \Auth::user();
        return $user;
    }

    public function index()
    {
        $users = User::orderBy('status', 'asc')->orderBy('id', 'desc')
                 ->whereIn('status', $this->status)->paginate(30);
        return view('user.users.index', compact('users'));
    }


    /**
     * 进入个人资料页面
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function personalDataShow()
    {
        $banks     = UserBankCard::$banks;
        $user      = User::with('workClass')->where('id', auth()->id())->first();
        $id        = auth()->id();
        $priDepart = DepartUser::with('department')->where('user_id', '=', $id)->where('is_primary', '=', 1)->first();

        if ($priDepart) {
            $priDepartPath = Department::getDeptPath($priDepart->department->id);
            //主部门信息
            if ($user->id == $priDepart->user_id) {
                $user->pri_depart = '<span data-toggle="tooltip" title="' . $priDepartPath .
                    '" data-placement="bottom">' . $priDepart->department->name . '</span>';
            }
        }

        if (!isset($user->departUser)) {
            abort("500", "该员工不存在！");
        }

        //部门信息
        if ($user->departUser) {
            foreach ($user->departUser as $every) {
                $departId[] = $every->department_id;
            }
        }


        if (isset($departId) && $departId) {
            $departInfo = Department::getByIds($departId, false)->toArray();
            $departName = "";
            array_map(function ($every) use (&$departName) {
                $path       = Department::getDeptPath($every['id']);
                $departName = $departName . '<span data-toggle="tooltip" title="' . $path .
                    '" data-placement="bottom">' . $every['name'] . '</span>' . ';';
            }, $departInfo);
        }

        $user->depart_name = isset($departName) ? $departName : "";


        //公司信息
        $companyId   = explode(',', $user->company_id);
        $companyInfo = DB::table('companies')->select('name')->whereIn('id', $companyId)->get()->toArray();

        $companyName = '';
        array_map(function ($every) use (&$companyName) {
            $companyName = $companyName . $every->name . ';';
        }, $companyInfo);

        $user->company_name = $companyName;


        //个人材料图片
        $picType = 'image/jpg';


        if ($user->detail) {
            if ($user->detail->pic_id_pos) {
                $user->detail->pic_id_pos = $this->picParse($user->detail->pic_id_pos, $picType);
            }

            if ($user->detail->pic_id_neg) {
                $user->detail->pic_id_neg = $this->picParse($user->detail->pic_id_neg, $picType);
            }

            if ($user->detail->pic_edu_background) {
                $user->detail->pic_edu_background = $this->picParse($user->detail->pic_edu_background, $picType);
            }

            if ($user->detail->pic_degree) {
                $user->detail->pic_degree = $this->picParse($user->detail->pic_degree, $picType);
            }

            if ($user->detail->pic_pre_company) {
                $user->detail->pic_pre_company = $this->picParse($user->detail->pic_pre_company, $picType);
            }

            if ($user->detail->pic_user) {
                $user->detail->pic_user = $this->picParse($user->detail->pic_user, $picType);
            }
        }

        //离职信息
        $user->dimission = UsersDimission::where('user_id', '=', $id)
            ->where('status', '=', UsersDimission::STATUS_YES)
            ->orderByDesc('id')
            ->select('id')
            ->first();
        return view('user.user_info.show', compact('user', 'banks'));
    }

    /**
     * 添加银行卡
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function addBankCard(Request $request)
    {
        $allData      = $request->all();
        $userId       = auth()->id();
        $cardNum      = str_replace(' ', '', $allData['bank_card_num']);
        $bank         = $allData['bank'];
        $branchBank   = $allData['bank_branch'] ?: '';
        $bankProvince = $allData['bank_province'] ?: '';
        $bankCity     = $allData['bank_city'];
        $bankType     = $allData['bank_type'];
        $bankCard     = UserBankCard::createBankCard(
            $userId,
            $cardNum,
            $bank,
            $branchBank,
            $bankProvince,
            $bankCity,
            $bankType
        );
        if ($bankCard['status'] == 'success') {
            $note = "添加银行卡成功";
            $this->userlog->record($userId, $allData, $userId, $note, $this->userlog::ADD_USER_CARD, null, null);
        }
        $bankCard['code'] = 0;
        $bankCard['flag'] = "bank-card-info";
        return Response::json($bankCard, $bankCard['status'] == 'success' ? 200 : 400);
    }

    /**
     * 管理员添加员工银行卡信息
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @author hurs
     */
    public function adminAddBankCard(Request $request)
    {
        $this->validate($request, [
            'user_id'       => 'required',
            'bank_card_num' => 'required',
            'bank'          => 'required',
            'bank_city'     => 'required',
            'bank_type'     => 'required',
        ], [
            'user_id.required'       => '员工必填',
            'bank_card_num.required' => '银行卡号必填',
            'bank.required'          => '开户行必填',
            'bank_city.required'     => '银行卡属地(市)必填',
            'bank_type.required'     => '银行卡类型必填',
        ]);

        $allData      = $request->except('_token');
        $userId       = $allData['user_id'];
        $cardNum      = str_replace(' ', '', $allData['bank_card_num']);
        $bank         = $allData['bank'];
        $branchBank   = $allData['bank_branch'] ?: '';
        $bankProvince = $allData['bank_province'] ?: '';
        $bankCity     = $allData['bank_city'];
        $bankType     = $allData['bank_type'];
        $bankCard     = UserBankCard::createBankCard(
            $userId,
            $cardNum,
            $bank,
            $branchBank,
            $bankProvince,
            $bankCity,
            $bankType
        );
        if ($bankCard['status'] == 'success') {
            $note = "添加银行卡成功";
            $this->userlog->record(Auth::id(), $allData, $userId, $note, $this->userlog::ADD_USER_CARD, null, null);
        }
        $bankCard['code'] = 0;
        return Response::json($bankCard, $bankCard['status'] == 'success' ? 200 : 400);
    }


    /**
     * 删除银行卡
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws Exception
     * @author hurs
     */
    public function deleteBankCard($id)
    {

        $bankCard = UserBankCard::findOrFail($id);
        if ($bankCard->user_id != Auth::id() && !Bouncer::can(User::ABILITY_USERS_SETTING_DELETE)) {
            throw new DevFixException('权限错误', 500);
        }
        $result = UserBankCard::deleteBankCard($bankCard);
        if ($result) {
            $note = "删除银行卡成功";
            $this->userlog->record(
                auth()->id(),
                null,
                $bankCard->user_id,
                $note,
                $this->userlog::DEL_USER_CARD,
                $bankCard->toArray(),
                null
            );
        }
        return response()->json([
            'status'  => 'success',
            'message' => '银行卡删除成功',
            'code'    => 0,
        ]);
    }

    /**
     * 添加紧急联系人
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function addUrgentUser(Request $request)
    {
        $allData     = $request->all();
        $userId      = auth()->id();
        $relate      = $allData['user_relate'];
        $relateName  = $allData['user_name'];
        $relatePhone = $allData['user_phone'];
        $urgentUser  = UserUrgentContact::createUrgentContact($userId, $relate, $relateName, $relatePhone);
        if ($urgentUser) {
            $message = '紧急联系人添加成功';
        } else {
            $message = '紧急联系人添加失败';
        }
        return response()->json([
            'status'  => 'success',
            'message' => $message,
            'code'    => 0,
        ]);
    }

    /**
     *删除紧急联系人
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deleteUrgentUser($id)
    {
        $UserUrgentContact = UserUrgentContact::findOrFail($id);
        if ($UserUrgentContact->user_id != Auth::id()) {
            throw new DevFixException('权限错误', 500);
        }
        $result = UserUrgentContact::deleteUrgentContact($UserUrgentContact);
        if ($result) {
            $message = '删除紧急联系人成功';
        } else {
            $message = '删除紧急联系人失败';
        }
        return response()->json([
            'status'  => 'success',
            'message' => $message,
            'code'    => 0,
        ]);
    }

    /**
     * 添加家庭信息
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function addFamily(Request $request)
    {
        $allData = $request->all();


        $userId       = auth()->id();
        $familyRelate = $allData['user_relate'];
        $familyName   = $allData['user_name'];
        $familySex    = $allData['user_sex'];
        $userFamily   = UserFamily::createUserFamily($userId, $familyRelate, $familyName, $familySex);
        if ($userFamily) {
            $message = '添加家庭信息成功';
        } else {
            $message = '添加家庭信息失败';
        }
        return response()->json([
            'status'  => 'success',
            'message' => $message,
            'code'    => 0,
        ]);
    }

    /**
     * 删除家庭信息
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteFamily($id)
    {
        $userFamily = UserFamily::findOrFail($id);
        UserFamily::deleteUserFamily($userFamily);
        if ($userFamily) {
            $message = '家庭信息删除成功';
        } else {
            $message = '家庭信息删除失败';
        }
        return response()->json([
            'status'  => 'success',
            'message' => $message,
            'code'    => 0,
        ]);
    }

    public function show($id)
    {
        $user = User::with('detail', 'departUser', 'userLog', 'urgentUser', 'family', 'bankCard', 'workClass')
            ->where('id', '=', $id)->whereIn('status', $this->status)->first();
        $user->employee_num = $user->getPrefixEmployeeNum();
        $priDepart          = DepartUser::with('department')->where('user_id', '=', $id)
                            ->where('is_primary', '=', DepartUser::DEPARTMENT_PRIMARY_YES)
                            ->first();

        if ($priDepart) {
            $priDepartPath = Department::getDeptPath($priDepart->department->id);
            //主部门信息
            if ($user->id == $priDepart->user_id) {
                $user->pri_depart = '<span data-toggle="tooltip" title="' . $priDepartPath .
                                    '" data-placement="bottom">' . $priDepart->department->name . '</span>';
            }
        }

        if (!isset($user->departUser)) {
            abort("500", "该员工不存在！");
        }

        //部门信息
        if ($user->departUser) {
            foreach ($user->departUser as $every) {
                $departId[] = $every->department_id;
            }
        }


        if (isset($departId) && $departId) {
            $departInfo = Department::getByIds($departId, false)->toArray();
            $departName = "";
            array_map(function ($every) use (&$departName) {
                $path       = Department::getDeptPath($every['id']);
                $departName = $departName . '<span data-toggle="tooltip" title="' . $path .
                    '" data-placement="bottom">' . $every['name'] . '</span>' . ';';
            }, $departInfo);
        }

        $user->depart_name = isset($departName) ? $departName : "";


        //公司信息
        $companyId   = explode(',', $user->company_id);
        $companyInfo = DB::table('companies')->select('name')->whereIn('id', $companyId)->get()->toArray();

        $companyName = '';
        array_map(function ($every) use (&$companyName) {
            $companyName = $companyName . $every->name . ';';
        }, $companyInfo);

        $user->company_name = $companyName;


        //个人材料图片
        $picType = 'image/jpg';


        if ($user->detail) {
            if ($user->detail->pic_id_pos) {
                $user->detail->pic_id_pos = $this->picParse($user->detail->pic_id_pos, $picType);
            }

            if ($user->detail->pic_id_neg) {
                $user->detail->pic_id_neg = $this->picParse($user->detail->pic_id_neg, $picType);
            }

            if ($user->detail->pic_edu_background) {
                $user->detail->pic_edu_background = $this->picParse($user->detail->pic_edu_background, $picType);
            }

            if ($user->detail->pic_degree) {
                $user->detail->pic_degree = $this->picParse($user->detail->pic_degree, $picType);
            }

            if ($user->detail->pic_pre_company) {
                $user->detail->pic_pre_company = $this->picParse($user->detail->pic_pre_company, $picType);
            }

            if ($user->detail->pic_user) {
                $user->detail->pic_user = $this->picParse($user->detail->pic_user, $picType);
            }
        }

        //离职信息
        $user->dimission = UsersDimission::where('user_id', '=', $id)
            ->where('status', '=', UsersDimission::STATUS_YES)
            ->orderByDesc('id')
            ->select('id')
            ->first();
        $userChangeInfo = $this->userlog->getLog($id);
        return view('user.users.show', compact('user', 'userChangeInfo'));
    }

    public function create(User $user)
    {
        $companies      = Company::where('status', '<>', Company::STATUS_DELETE)->get();
        $sql            = "SELECT * FROM departments ORDER BY parent_id ASC, `order` DESC, id ASC";
        $departments    = DB::select($sql);
        $newEmployeeNum = User::genUniqueNum();
        $allUsers       = User::getAll();
        $workClass      = AttendanceWorkClass::orderBy('type')->get();

        return view('user.users.create', compact(
            'companies',
            'departments',
            'newEmployeeNum',
            'allUsers',
            'workClass'
        ));
    }


    public function edit($id, Request $request)
    {

        $type               = $request->get('type');
        $user               = User::with('detail')->findOrFail($id);
        $user->employee_num = $user->getPrefixEmployeeNum();
//        if ($user->status == 9)
//        {
//            return back()->with('editError','该员工已离职无法编辑！');
//        }


        //邮箱处理
//        if ($user->email)
//        {
//            $emailInfo = explode("@",$user->email);
//            $user->prefixemail = $emailInfo[0];
//            $suffix = explode('.',$emailInfo[1]);
//            $user->suffixemail = $suffix[0];
//        }


        $priDepart = DepartUser::with('department')->where('user_id', '=', $id)->where('is_primary', '=', 1)->first();

        if ($priDepart) {
            $user->pri_dept_id = $priDepart->department->id;
        } else {
            $user->pri_dept_id = "";
        }


        $userCompanies = explode(',', $user->company_id);

        $pic_columns = ['pic_id_pos', 'pic_id_neg', 'pic_edu_background', 'pic_degree', 'pic_pre_company', 'pic_user'];

        if ($user->detail) {
            foreach ($pic_columns as $column) {
                if ($user->detail->$column) {
                    $user->detail->$column = $this->picParse($user->detail->$column, 'image/jpg');
                } else {
                    $user->detail->$column = "";
                }
            }
        }

        $companies = Company::where('status', '<>', Company::STATUS_DELETE)->get();

        $userDepartments     = DepartUser::select('department_id')->where('user_id', '=', $id)->get()->toArray();
        $userDepartmentsInfo = array_map(function ($every) {
            return $every['department_id'];
        }, $userDepartments);

        $departments=[];
        $departments = Department::all();

        if($departments){
            foreach ($departments as $key => &$value) {
                $value->path = Department::getDeptPath($value->id);
            }
        }


        //部门领导信息
        $deptLeader=[];
        $deptLeader = DepartUser::with('department')->where('user_id', '=', $id)->get();
        if($deptLeader){
            foreach ($deptLeader as $key => &$value) {
                $value->path = Department::getDeptPath($value->department->id);
            }
        }


        //获取所有员工信息
        $allUsers = User::getAll();

        //银行卡相关信息
        $banks=[];
        $banks = UserBankCard::$banks;

        //排班信息
        $workClass = AttendanceWorkClass::orderBy('type')->get();
        return view('user.users.edit', compact(
            'companies',
            'departments',
            'user',
            'userCompanies',
            'userDepartmentsInfo',
            'response',
            'deptLeader',
            'allUsers',
            'type',
            'banks',
            'workClass'
        ));
    }


    /**
     *
     * 解析图片
     * @param $contents
     * @param $mime
     * @return string
     */
    private function picParse($contents, $mime)
    {

        $base64 = base64_encode(decrypt($contents));

        return ('data:' . $mime . ';base64,' . $base64);
    }

    public function store(UserRequest $request)
    {
        DB::beginTransaction();
        try {
            $allData = $request->all();
            if (!$allData['isleader']) {//高管不校验英文名规则
                //英文名生成规则校验
                $validEnglishName = User::validEnglishName(
                    trim($allData['first_chinese_name']),
                    strtolower(trim($allData['english_name']))
                );
                if (!$validEnglishName) {
                    throw new UserFixException("英文名填写不符合规则，请重新填写！");
                }
            }

            //校验系统唯一账号生成规则
            if (strtolower(trim($allData['english_name'])) != strtolower(trim($allData['name']))) {
                throw new UserFixException("系统唯一账号与英文名不一致，请重新填写！");
            }

            $checkuser = User::where('name', '=', strtolower(trim($allData['name'])))->lockForUpdate()->count();

            if ($checkuser) {
                throw new UserFixException("系统唯一账号已存在请重新填写！");
            } else {
                $user = new User;
            }

            $userInfo['name']           = strtolower(trim($allData['name']));
            $userInfo['chinese_name']   = trim($allData['first_chinese_name']) . trim($allData['last_chinese_name']);
            $userInfo['english_name']   = trim($allData['english_name']);
            $userInfo['employee_num']   = User::parseEmployeeNum($allData['employee_num']);
            $userInfo['email']          = $allData['email'];
            $userInfo['mobile']         = (str_replace(' ', '', $allData['mobile']));
            $userInfo['password']       = \Hash::make(substr($allData['mobile'], 4, 10));
            $userInfo['position']       = $allData['position'];
            $userInfo['gender']         = $allData['gender'];
            $userInfo['is_sync_wechat'] = $allData['is_sync_wechat'];
            $userInfo['telephone']      = $allData['telephone'] ? $allData['telephone'] : "";
            $userInfo['company_id']     = $allData['company_id'];
            $userInfo['join_at']        = !empty($allData['join_at']) ?
                                           date('Y-m-d', strtotime($allData['join_at'])) : Dh::todayDate();
            $userInfo['work_address']   = $allData['work_address'];
            $userInfo['isleader']       = $allData['isleader'];
            $userInfo['work_type']      = $allData['work_type'];
            $userInfo['work_title']     = $allData['work_type'] != User::WORK_TYPE_SCHEDULE ? $allData['work_title'] :
                                          '';
            $user->fill($userInfo);
            if ($user->save()) {
                $userDetail = new UsersDetailInfo;
                $userDetail->fill(['user_id' => $user->id]);

                //department_user
                if (isset($allData['departments']) && $allData['departments']) {
                    foreach ($allData['departments'] as $key => $value) {
                        $insertData[$key]['department_id'] = $value;
                        $insertData[$key]['user_id']       = $user->id;
                        //设置部门领导
                        if (isset($allData['deptleader']) && $allData['deptleader']) {
                            if (in_array($value, $allData['deptleader'])) {
                                $insertData[$key]['is_leader'] = 1;
                            } else {
                                $insertData[$key]['is_leader'] = 0;
                            }
                        } else {
                            $insertData[$key]['is_leader'] = 0;
                        }

                        $insertData[$key]['is_primary'] = ($allData['pri_dept_id'] == $value) ? 1 : 0;
                    }
                } else {
                    throw new UserFixException("部门信息必填");
                }

                //写入department_user
                foreach ($insertData as $insert) {
                    $departmentUserStatus = DepartUser::createOrUpdateNewDepartUser($insert);
                }

                //新建用户个人假期记录
                $data['user_id'] = $user->id;
                $data['annual']  = $data['company_benefits'] = $data['full_pay_sick'] = $data['extra_day_off'] = 0;
                AttendanceVacation::createNewAttendanceVacation($data);


                //删除待入职员工
                if (isset($allData['join']) && !empty($allData['join'])) {
                    $pendingUser      = PendingUser::findOrFail($allData['join']);
                    $update['status'] = PendingUser::STATUS_DELETED;
                    $pendingUser->fill($update);
                    $pendingUser->save();
                }

                if ($userDetail->save() && $departmentUserStatus) {
                    $note      = "创建员工成功！";
                    $logrecord = $this->userlog->record(
                        auth()->id(),
                        $userInfo,
                        $user->id,
                        $note,
                        $action = $this->userlog::ADD_USER,
                        null,
                        $type = $this->userlog::TYPE_ADD_USER
                    );//记录变更日志
                    if (!$logrecord) {
                        throw new UserFixException("员工信息变更记录失败！");
                    }
                }


                //赋予员工权限
                $attachResult = User::grantPlainUser($user->id);

                if (env('SYNC_WECHAT')) {
                    if ($userInfo['is_sync_wechat']) {
                        $userInfo['mobile'] = decrypt($userInfo['mobile']);
                        $ret                = $this->createWechatUser($userInfo, $allData);
                        if ($ret['errcode'] != 0) {
                            throw new DevFixException("员工企业微信同步失败");
                        }

                        $retTag = $this->saveWechatTagUsers($userInfo);
                        if ($retTag['errcode'] != 0) {
                            throw new DevFixException("员工企业微信同步失败--标签同步失败");
                        }
                    }
                    //写入tag_user
                    $tag = TagUser::saveTagUser($userInfo['company_id'], $user->id, $user->name);
                    if (!$tag) {
                        throw new DevFixException("同步tag失败");
                    }
                }

                DB::commit();
            } else {
                throw new DevFixException('保存员工信息失败！');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            $messages = $e->getMessage();
            report($e);
            return back()->with('storeError', $messages)->withInput();
        }

        return redirect()->route('users.index')->with('storeSuccess', "创建员工成功！");
    }

    /**
     * 更新
     *
     * @param $id user_id
     */
    public function update(Request $request, $id)
    {
        $wechatNameUpdated = false;
        //表单校验
        $validModel = new User();
        $validModel->validUpdateData($request, $id);

        $requestData = $request->all();
        $user        = User::findOrFail($id);
        $oldUserData = $user->toArray();

        DB::beginTransaction();
        try {
            // basic user information
            $basicColumns  = Schema::getColumnListing('users');
            $userBasicData = $this->getUpdateData($basicColumns, $requestData, $request, 'basic');
            // detailed user infortmation
            $detailedColumns  = Schema::getColumnListing('users_detail_info');
            $userDetailedData = $this->getUpdateData($detailedColumns, $requestData, $request, 'detail');
            // department user information
            $departmentUserData['departments'] = isset($requestData['departments']) ? $requestData['departments'] : [];
            $departmentUserData['deptleader']  = isset($requestData['deptleader']) ? $requestData['deptleader'] : [];
            $departmentUserData['pri_dept_id'] = isset($requestData['pri_dept_id']) ? $requestData['pri_dept_id'] : [];


            if ($userBasicData) {
                //部分员工因历史问题不校验生成规则

                $count = User::where("id", "<", $id)->count();
                if ($count > User::PART_CHECK_PEOPLE_NUM) {
                    if (!$requestData['isleader']) {//高管不校验英文名规则
                        $validEnglishName = User::validEnglishName(
                            $userBasicData['chinese_name'],
                            strtolower($userBasicData['english_name'])
                        );
                        if (!$validEnglishName) {
                            throw new UserFixException("英文名填写不符合规则，请重新填写！");
                        }
                    }
                }

                if (!isset($requestData['regular_at'])) { //临时处理 转正日期regular_at
                    $usercheck = User::where("name", '=', $userBasicData['name'])
                        ->where('id', '<>', $id)->lockForUpdate()->count();
                    if ($usercheck) {
                        throw new UserFixException("系统唯一账号已存在请重新填写！");
                    }
                }

                $preUpdateUser  = $user;
                $updateUserData = $userBasicData;
                $userStatus     = $user->update($userBasicData);

                if ($userStatus) {
                    $note      = "更新基础信息成功！";
                    $logrecord = $this->userlog->record(
                        auth()->id(),
                        $updateUserData,
                        $id,
                        $note,
                        $this->userlog::MODIFY_USER_BASIC,
                        $preUpdateUser
                    );
                    if (!$logrecord) {
                        throw new DevFixException("员工信息变更记录失败！");
                    }
                }

                if (!$userStatus) {
                    throw new DevFixException("员工信息更新失败！");
                }
            }

            if ($userDetailedData) {
                $userDetailInfo      = UsersDetailInfo::where('user_id', '=', $id)->first();
                $preUpdateDetailInfo = $userDetailInfo;
                $updateDetailInfo    = $userDetailedData;
                if ($userDetailInfo) {
                    $userDetailStatus = $userDetailInfo->update($userDetailedData);
                    //记录详细信息日志
                    if ($userDetailStatus) {
                        $action = "";
                        $note   = "";
                        switch ($requestData['infotype']) {
                            case "job":
                                $action = $this->userlog::MODIFY_USER_JOB;
                                $note   = "更新工作信息成功！";
                                break;
                            case "person":
                                $action = $this->userlog::MODIFY_USER_ID;
                                $note   = "更新身份信息成功！";
                                break;
                            case "edu":
                                $action = $this->userlog::MODIFY_USER_EDU;
                                $note   = "更新学历信息成功！";
                                break;
                            case "card":
                                $action = $this->userlog::MODIFY_USER_CARD;
                                $note   = "更新银行卡信息成功！";
                                break;
                            case "contract":
                                $action = $this->userlog::MODIFY_USER_CONTRACT;
                                $note   = "更新合同信息成功！";
                                break;
                            case "emerge":
                                $action = $this->userlog::MODIFY_USER_EMERGE;
                                $note   = "更新紧急联系人成功！";
                                break;
                            case "family":
                                $action = $this->userlog::MODIFY_USER_FAMILY;
                                $note   = "更新紧急联系人成功！";
                                break;
                            case "pic":
                                $action = $this->userlog::MODIFY_USER_PIC;
                                $note   = "更新个人材料成功！";
                                break;
                            default:
                                break;
                        }
                        /*$logrecord = $this->userlog->record(
                            auth()->id(),
                            $updateDetailInfo,
                            $id,
                            $note,
                            $action,
                            $preUpdateDetailInfo
                        );
                        if (!$logrecord) {
                            throw new DevFixException("员工信息变更记录失败！");
                        }*/
                    }
                } else { //不存在添加一条用户详细信息
                    $userDetailedData['user_id'] = $id;
                    $userDetailStatus =UsersDetailInfo::create($userDetailedData);

                }
                if (!$userDetailStatus) {
                    throw new DevFixException("员工详细信息更新失败");
                }
            }

            if (isset($departmentUserData)) {
                if (isset($departmentUserData['departments']) && $departmentUserData['departments']) {
                    foreach ($departmentUserData['departments'] as $key => $value) {
                        $insertData[$key]['department_id'] = $value;
                        $insertData[$key]['user_id']       = $user->id;
                        if (isset($departmentUserData['deptleader']) && $departmentUserData['deptleader']) {
                            if (in_array($value, $departmentUserData['deptleader'])) {
                                $insertData[$key]['is_leader'] = 1;
                            } else {
                                $insertData[$key]['is_leader'] = 0;
                            }
                        } else {
                            $insertData[$key]['is_leader'] = 0;
                        }

                        $insertData[$key]['is_primary'] = ($departmentUserData['pri_dept_id'] == $value) ? 1 : 0;
                    }

                    //找出员工的部门，编辑后如果新增部门则插入，减少则删除
                    $departmentUser   = DepartUser::getByUserId($id)->toArray();
                    $oldDepartUserIds = [];
                    $newDepartUserIds = [];
                    foreach ($insertData as $insert) {
                        $newDepartUserIds[] = $insert['department_id'];
                    }
                    if($departmentUser){
                        foreach ($departmentUser as $departUser) {//删除减少的部门
                            $oldDepartUserIds[] = $departUser['department_id'];
                            if (!in_array($departUser['department_id'], $newDepartUserIds)) {
                                DepartUser::find($departUser['id'])->delete();
                            }
                        }
                    }

                    $departmentUserStatus = true;
                    foreach ($insertData as $insert) {
                        if (!in_array($insert['department_id'], $oldDepartUserIds)) {//添加新部门
                            $departmentUserStatus = DepartUser::createOrUpdateNewDepartUser($insert);
                        } else {//部门没变但是是否主部门或者部门领导变化也新建，老记录删除
                            $insertDepartUser = DepartUser::getByDepartIdAndUserId(
                                $insert['department_id'],
                                $insert['user_id'],
                                false,
                                false
                            );
                            if ($insertDepartUser->is_leader != $insert['is_leader'] ||
                                $insertDepartUser->is_primary != $insert['is_primary']) {
                                $departmentUserStatus = DepartUser::createOrUpdateNewDepartUser($insert);
                                $insertDepartUser->delete();
                            }
                        }
                    }
                    $note      = "更新基础信息成功！";
                    $logrecord = $this->userlog->recordDeptUser(
                        auth()->id(),
                        $id,
                        $departmentUser,
                        $departmentUserData,
                        $note
                    );
                    if (!$logrecord) {
                        throw new DevFixException("员工信息变更记录失败！");
                    }

                    if (!$departmentUserStatus) {
                        throw new DevFixException("员工部门信息更新失败");
                    }
                    //更新tag_user 有记录则先删除，后插入
                    $isCleared = TagUser::deleteTagUserByUserId($id);
                    if ($isCleared) {
                        $tag = TagUser::saveTagUser($requestData['company_id'], $id, $requestData['name']);
                        if (!$tag) {
                            throw new DevFixException("员工部门信息更新失败");
                        }
                    }
                }
            }

            if (env('SYNC_WECHAT')) {
                if ($userBasicData) {
                    $userBasicData['mobile'] = decrypt($userBasicData['mobile']);
                    if ($userBasicData['is_sync_wechat']) {
                        $weUser = $this->getWechatUser($userBasicData);

                        if ($weUser['errcode'] != 0) {
                            $ret = $this->createWechatUser($userBasicData, $departmentUserData);
                            if ($ret['errcode'] != 0) {
                                throw new DevFixException("员工企业微信同步失败");
                            } else {
                                $this->saveWechatTagUsers($userBasicData);
                            }
                        }
                        $ret = $this->updateWechatUser($userBasicData, $departmentUserData);

                        if ($ret['errcode'] != 0) {
                            throw new DevFixException("员工企业微信同步失败");
                        }

                        //如公司对应的标签名有变化，则同步标签
                        if ($oldUserData['company_id'] != $userBasicData['company_id']) {
                            $this->resetWechatTagUsers($oldUserData);
                            $this->saveWechatTagUsers($userBasicData);
                        }
                    } else {
                        //不同步
                        //删除
                        if ($oldUserData['is_sync_wechat'] == 1) {
                            $weUser = $this->getWechatUser($userBasicData);
                            if ($weUser['errcode'] == 0) {
                                $ret = $this->deleteWechatUser($userBasicData);
                                if ($ret['errcode'] != 0) {
                                    throw new DevFixException("员工企业微信同步失败");
                                }
                            }
                        }
                    }
                }
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();

            $messages = $e->getMessage();
            return back()->with('updateError', $messages);
        }

        return redirect()->route('users.show', ['id' => $id])->with('updateSuccess', '用户信息更新成功！');
    }


    public function uploadImg($id, Request $request)
    {

        $pic_columns = ['pic_id_pos', 'pic_id_neg', 'pic_edu_background', 'pic_degree', 'pic_pre_company', 'pic_user'];
        $imgInfo     = $request->file();
        foreach ($pic_columns as $value) {
            if (isset($imgInfo[$value])) {
                $picData         = encrypt(file_get_contents($imgInfo[$value]->path()));
                $imgInfo[$value] = $picData;
            }
        }

        if (isset($imgInfo) && $imgInfo) {
            $updateImg = UsersDetailInfo::where('user_id', '=', $id)->update($imgInfo);
            $picType   = 'image/jpg';
            if ($updateImg) {
                return response()->json(['status' => 'success',
                                         'messages' => '图片上传成功！',
                                         'picdata' => $this->picParse($picData, $picType)
                                         ]);
            } else {
                return response()->json(['status' => 'failed', 'messages' => '图片上传失败！']);
            }
        } else {
            return response()->json(['status' => 'failed', 'messages' => '图片上传失败！']);
        }
    }


    /**
     *删除员工 支持批量
     *
     * @return mixed
     */
    public function destroy(Request $request)
    {
        //
        $ids = $request->input('ids');

        DB::beginTransaction();

        try {
            $date = date('Y-m-d', time());
            if (is_array($ids)) {
                $userStatus           = User::whereIn('id', $ids)->update(['status' => 9, 'leave_at' => $date]);
                $departmentUserStatus = DepartUser::whereIn('user_id', $ids)->delete();
            } else {
                $userStatus           = User::where('id', '=', $ids)
                                        ->update(['status' => 9, 'leave_at' => $date]);
                $departmentUserStatus = DepartUser::where('user_id', '=', $ids)->delete();
            }

            if ($userStatus && $departmentUserStatus) {
                if (env('SYNC_WECHAT')) {
                    if (is_array($ids)) {
                        $users = User::whereIn('id', $ids)->get()->toArray();
                    } else {
                        $users = User::where('id', '=', $ids)->get()->toArray();
                    }
                    foreach ($users as $user) {
                        $weUser = $this->getWechatUser($user);
                        if ($weUser['errcode'] == 0) {
                            $ret = $this->deleteWechatUser($user);
                            if ($ret['errcode'] != 0) {
                                throw new DevFixException("员工企业微信同步失败");
                            } else {
                                //离职员工同步后，将是否同步字段改称否
                                $updateUserSyncWechat = User::whereId($user['id'])->update(['is_sync_wechat' => 0]);
                                if (!$updateUserSyncWechat) {
                                    throw new DevFixException("更姓员工信息同步状态失败");
                                }
                            }
                        }
                    }
                }

                $note      = "员工离职成功！";
                $logrecord = $this->userlog->record(
                    auth()->id(),
                    null,
                    $ids,
                    $note,
                    $this->userlog::USER_RESIGNATION,
                    null,
                    $this->userlog::TYPE_USER_RESIGNATION
                );
                if (!$logrecord) {
                    throw new DevFixException("员工信息变更记录失败！");
                }

                //离职员工权限回收
                $RecyclePermissions = User::deleteAbilities($ids);

                DB::commit();

                return response()->json(['status' => 'success', 'messages' => $note]);
            } else {
                throw new DevFixException('员工离职失败!');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            $messages = $e->getMessage();
            return response()->json(['status' => 'failed', 'messages' => $messages]);
        }
    }

    /**
     * @param  update columns $columns
     * @param  form data $requestData
     * @param  request object $request
     * @return array  $returnData
     */
    private function getUpdateData($columns, $requestData, $request, $datatype)
    {
        $returnData = [];

        $dataColumns = [
            'join_at',
            'leave_at',
            'after_probation',
            'born_time',
            'validity_certificate',
            'first_work_time',
            'graduate_time',
            'first_contract_start_time',
            'first_contract_end_time',
            'cur_contract_start_time',
            'cur_contract_end_time',
        ];

        foreach ($columns as $value) {
            if ($request->has($value)) {
                if ($value == 'password') {
                    $returnData[$value] = \Hash::make($requestData[$value]);
//                } elseif ($value == 'mobile') {
//                    $returnData[$value] = encrypt($requestData[$value]);
                } elseif ($value == 'name') {
                    $returnData[$value] = strtolower(trim($requestData[$value]));
                } elseif ($value == 'work_title') {
                    $returnData[$value] = $requestData['work_type'] != User::WORK_TYPE_SCHEDULE ? $requestData[$value] :
                        '';
                } elseif ($value == 'employee_num') {
                    $returnData[$value] = User::parseEmployeeNum($requestData[$value]);
                } else {
                    $returnData[$value] = $requestData[$value];
                }
                if (in_array($value, $dataColumns)) {
                    if ($requestData[$value]) {
                        $returnData[$value] = date('Y-m-d', strtotime($requestData[$value]));
                    }

                    //数据库存储的是完整日期时间格式
                    if (in_array($value, ['join_at', 'leave_at'])) {
                        if ($requestData[$value]) {
                            $returnData[$value] = date('Y-m-d H:i:s', strtotime($requestData[$value]));
                        }
                    }
                }
            }
        }

        // deal with picture
        $pic_columns = ['pic_id_pos', 'pic_id_neg', 'pic_edu_background', 'pic_degree', 'pic_pre_company', 'pic_user'];


        if ($datatype == 'detail') {
            foreach ($returnData as $key => $every) {
                if (in_array($key, $pic_columns)) {
                    $returnData[$key] = file_get_contents($every->path());
                }
            }
        }


        //用户详细信息加密
        if ($datatype == 'detail') {
            foreach ($returnData as $key => &$every) {
                if ($every) {
                    $every = encrypt($every);
                }
            }
        }

        return $returnData;
    }


    /**
     * Get information from process approval department leader
     * @param user_id $id
     * @return department leader list
     */
    public function process($id = null)
    {

        $departmentId = DB::table('department_user')->select('department_id')->where('user_id', '=', $id)
            ->where('primary', '=', 1)->first();
        if ($departmentId) {
            $leaderIds  = $this->idOfLeader($departmentId->department_id, $id);
            $leaderInfo = User::whereIn('id', $leaderIds)->get();
            $data       = new UserResource($leaderInfo);

            return $data;
        }

        return null;
    }

    private function idOfLeader($departmentId, $userId = null)
    {
        static $leaderIds = [];
        $departmentLeader = DB::table('department_user')
            ->where('department_id', $departmentId)->where('isleader', '=', 1)
            ->where('primary', '=', 1)->where('user_id', '<>', $userId)->first();
        if ($departmentLeader) {
            $leaderIds[]     = $departmentLeader->user_id;
            $upperDepartment = Department::where('id', '=', $departmentLeader->department_id)->first();
            if ($upperDepartment && $upperDepartment->parent_id) {
                $this->idOfLeader($upperDepartment->parent_id);
            }
        }
        return $leaderIds;
    }

    private function createWechatUser($data, $deptsData)
    {
        $this->work = app('wechat.work.contacts');

        $wechatDpets = $this->getWechatDepts();
        $deps        = [];
        foreach ($deptsData['departments'] as $dept) {
            if (!array_key_exists($dept, $wechatDpets)) {
                return ['errmsg' => 'department no exist,please sync wechat department!', 'errcode' => 9999];
            }
            $deps[] = $dept;
        }
        $w_data = [
            'userid'       => $data['name'],
            'name'         => $data['chinese_name'],
            'english_name' => $data['english_name'],
            'department'   => $deps,
            'mobile'       => $data['mobile'],
            'email'        => $data['email'],
            'position'     => $data['position'],
            'gender'       => $data['gender'],
            'to_invite'    => true,
        ];
        $r      = $this->work->user->create($w_data);
        Log::info("sync wechat : create " . $data['name'] . " @ " . $data['chinese_name'] . $r['errmsg'] . ' ' .
                    json_encode($w_data));
        return $r;
    }

    private function updateWechatUser($data, $deptsData)
    {
        $this->work = app('wechat.work.contacts');

        $wechatDpets = $this->getWechatDepts();

        $deps = [];
        foreach ($deptsData['departments'] as $dept) {
            if (!array_key_exists($dept, $wechatDpets)) {
                return ['errmsg' => 'department no exist,please sync wechat department!', 'errcode' => 9999];
            }
            $deps[] = $dept;
        }
        $w_data = [
            'userid'       => $data['name'],
            'name'         => $data['chinese_name'],
            'english_name' => $data['english_name'],
            'department'   => $deps,
            'mobile'       => $data['mobile'],
            'email'        => $data['email'],
            'position'     => $data['position'],
            'gender'       => $data['gender'],
            'to_invite'    => false,
        ];

        $r = $this->work->user->update($w_data['name'], $w_data);

        Log::info("sync wechat user : update " . $data['name'] . " @ " . $data['chinese_name'] . " " . $r['errmsg'] .
            ' ' . json_encode($w_data));

        return $r;
    }

    private function deleteWechatUser($data)
    {

        $this->work = app('wechat.work.contacts');

        $r = $this->work->user->delete($data['name']);


        Log::info("sync wechat user : delete " . $data['name'] . " @ " . $data['chinese_name'] . $r['errmsg']);
        return $r;
    }

    private function getWechatUser($data)
    {

        $this->work = app('wechat.work.contacts');

        $r = $this->work->user->get($data['name']);

        Log::info("sync wechat user : get " . $data['name'] . " @ " . $data['chinese_name'] . $r['errmsg']);
        return $r;
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
            $list[$row['id']] = $row;
        }
        return $list;
    }

    /**
     * API获取企业微信标签列表
     * @return array
     */
    private function getWechatTags()
    {
        $this->work = app('wechat.work.contacts');
        $list       = [];
        $response   = $this->work->tag->list();
        Log::info("sync wechat tag : get list " . $response['errmsg']);
        if ($response['errcode'] != 0) {
            $this->error('无法获取数据: ' . $response['errmsg']);
        }
        foreach ($response['taglist'] as $row) {
            $list[$row['tagid']] = $row['tagname'];
        }
        return $list;
    }


    /**
     * API 据tagId获取企业微信获取标签成员
     * @return array
     */
    private function getWechatTagUsers($tagId)
    {
        $this->work = app('wechat.work.contacts');
        $list       = [];
        $response   = $this->work->tag->get($tagId);
        Log::info("sync wechat tag user: get list " . $response['errmsg']);
        if ($response['errcode'] != 0) {
            $this->error('无法获取数据: ' . $response['errmsg']);
        }
        foreach ($response['userlist'] as $row) {
            $list[$row['userid']] = $row['name'];
        }
        return $list;
    }

    //增加企业微信标签用户关系
    private function saveWechatTagUsers($data)
    {
        $this->work = app('wechat.work.contacts');
        $wechatTags = $this->getWechatTags();
        $tag        = TagUser::getTagByCompanyId($data['company_id']);
        $tagId      = array_search($tag['name'], $wechatTags);
        $r = $this->work->tag->tagUsers($tagId, [$data['name']]);
        Log::info("sync wechat tag : add taguser " . $tagId . "  " . $data['name'] . " " . $r['errmsg'] . ' ' .
                    json_encode($data));

        return $r;
    }

    //企业微信上该用户的标签关系
    private function resetWechatTagUsers($oldUser)
    {
        $this->work = app('wechat.work.contacts');

        $wechatTags = $this->getWechatTags();
        $tag        = TagUser::getTagByCompanyId($oldUser['company_id']);
        $tagId      = array_search($tag['name'], $wechatTags);


        $r = $this->work->tag->untagUsers($tagId, [$oldUser['name']]);
        Log::info("sync wechat tag : add taguser " . $tagId . "  " . $oldUser['name'] . " " . $r['errmsg'] . ' ' .
                    json_encode($oldUser));

        return $r;
    }

    /**
     * 员工筛选
     * 1.筛选项：中文名，职位，是否同步 ，员工编号， 邮箱
     *
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function search(Request $request)
    {
        $searchData = $request->all();

        $where = [];

        if (isset($searchData['chinese_name']) && $searchData['chinese_name']) {
            $where[] = ['chinese_name', 'like', '%' . $searchData['chinese_name'] . '%'];
        }

        if (isset($searchData['english_name']) && $searchData['english_name']) {
            $where[] = ['english_name', 'like', '%' . $searchData['english_name'] . '%'];
        }

        $mobile = (isset($searchData['mobile']) && $searchData['mobile']) ? $searchData['mobile'] : "";

        if ($mobile) {
            $users = User::where($where)->get()
                ->filter(function ($item) use ($mobile) {
                    if ($mobile) {
                        if ($mobile == decrypt($item->mobile)) {
                            return $item;
                        }
                    } else {
                        return $item;
                    }
                });
        } else {
            $users = User::where($where)->paginate(15);
        }

        return view('user.users.index', compact('users', 'searchData')); //$searchData;  //搜索页标志
    }

    /**
     *唯一企业微信名name校验
     *
     * @return mixed
     */
    public function userCheck(Request $request)
    {
        $request = $request->all();
        $name    = strtolower($request['name']);

        $user = User::where('name', '=', $name)->count();
        if (!$user) {
            return response()->json(['status' => 'success', 'messages' => '企业微信名唯一']);
        } else {
            return response()->json(['status' => 'failed', 'messages' => '企业微信名不唯一，请修改']);
        }
    }

    /**
     *
     * ajax异步查询上级领导名字
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function userAjaxSearch(Request $request, $type)
    {
        $data['results'] = [];
        $request         = $request->all();

        $name = strtolower($request['q']);

        if (isset($type) && ($type == 'all')) {
            $user = User::where('chinese_name', 'like', '%' . $name . '%')->get()->toArray();
        } else {
            $user = User::where(
                'chinese_name',
                'like',
                '%' . $name . '%'
            )->where('status', '=', User::STATUS_JOIN)->get()->toArray();
        }

        foreach ($user as $item) {
            $item_tmp = [
                'id'   => $item['id'],
                'text' => $item['chinese_name'],
            ];
            array_push($data['results'], $item_tmp);
        }

        return response()->json($data);
    }

####################################员工离职####################################

    /**
     * 离职添加页面
     *
     * @param $id
     *
     * @return View
     */
    public function dimissionCreate($id)
    {
        $user = User::findOrFail($id);
        return view('user.dimission.create', compact('user'));
    }

    /**
     * 保存离职信息
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return RedirectResponse、view
     */
    public function dimissionStore(Request $request)
    {
        $allResult = $request->all();

        if (isset($allResult['id'])) {
            $oUsersDimission = UsersDimission::findOrFail($allResult['id']);
        } else {
            $oUsersDimission = new UsersDimission();
        }

        DB::beginTransaction();
        try {
            $data['user_id']          = $allResult['user_id'];
            $data['is_voluntary']     = $allResult['is_voluntary'];
            $data['is_sign']          = $allResult['is_sign'];
            $data['is_complete']      = $allResult['is_complete'];
            $data['reason']           = $allResult['reason'];
            $data['interview_result'] = $allResult['interview_result'];
            $data['note']             = $allResult['note'];

            $oUsersDimission->fill($data);
            $oUsersDimission->save();
            DB::commit();
            return redirect()->route('users.show', ['id' => $data['user_id']]);
        } catch (\Exception $e) {
            DB::rollBack();
            $messages = $e->getMessage();
            return back()->with('saveError', $messages);
        }
    }

    /**
     * 编辑离职信息
     * @param $id
     *
     * @return View
     */
    public function dimissionEdit($id)
    {
        $dimission = UsersDimission::findOrFail($id);
        $user      = User::findOrFail($dimission->user_id);

        return view('user.dimission.edit', compact('user', 'dimission'));
    }

    /**
     * 更新离职信息
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function dimissionUpdate(Request $request)
    {
        $allResult = $request->all();

        if (isset($allResult['id'])) {
            $oUsersDimission = UsersDimission::findOrFail($allResult['id']);
        } else {
            $oUsersDimission = new UsersDimission();
        }

        DB::beginTransaction();
        try {
            $data['user_id']          = $allResult['user_id'];
            $data['is_voluntary']     = $allResult['is_voluntary'];
            $data['is_sign']          = $allResult['is_sign'];
            $data['is_complete']      = $allResult['is_complete'];
            $data['reason']           = $allResult['reason'];
            $data['interview_result'] = $allResult['interview_result'];
            $data['note']             = $allResult['note'];

            $oUsersDimission->fill($data);
            $oUsersDimission->save();
            DB::commit();
            return redirect()->route('users.show', ['id' => $data['user_id']]);
        } catch (\Exception $e) {
            DB::rollBack();
            $messages = $e->getMessage();
            return back()->with('saveError', $messages);
        }
    }

    /**
     * 离职信息
     * @param $id
     *
     * @return View
     */
    public function dimissionShow($id)
    {
        if ($id) {
            $dimission = UsersDimission::findOrFail($id);
            $user      = User::findOrFail($dimission->user_id);

            return view('user.dimission.show', compact('user', 'dimission'));
        } else {
            return view('user.dimission.create', compact('user'));
        }
    }

    /**
     * Get the password reset validation rules.
     *
     * @return array
     */
    protected function rules()
    {
        return [
            '_token' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed|min:6',
        ];
    }

    /**
     * 批量导入用户
     * @param Request $request
     */
    public function batchImport(Request $request){
        if ($request->hasFile('inputImport') && $request->file('inputImport')->isValid()) {
            try {
                $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load( $request->file('inputImport') );

                // $cells 是包含 Excel 表格数据的数组
                foreach ( $spreadsheet->getWorksheetIterator() as $cell ) {
                    $cells = $cell->toArray();
                }

                // 去掉表头
                unset( $cells[ 0 ] );

                foreach ( $cells as $cell ) {
                    dd($cell);
                }

            } catch ( \Exception $e ) {
                return returnJson($e->getMessage(), $e->getCode());
            }
        }else{
            return returnJson('文件上传失败',ConstFile::API_RESPONSE_FAIL);
        }
    }
}
