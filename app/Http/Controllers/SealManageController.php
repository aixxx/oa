<?php
/**
 * Created by PhpStorm.
 * User: lizhennan
 * Date: 2018/8/16
 * Time: 下午4:35
 */

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Contract;
use App\Models\Seal;
use App\Models\SealChangeLog;
use App\Models\User;
use App\Models\Workflow\Entry;
use App\Models\Workflow\Workflow;
use App\Models\Workflow\WorkflowRole;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use \Exception;
use DevFixException;
use UserFixException;

class SealManageController extends Controller
{
    public function index()
    {
        $isShow = self::isShow();
        //$seals     = Seal::paginate(10);
        $seals = [];//默认不展示，只有搜索才展示印章记录
        return view('sealManage.index', compact('seals', 'isShow'));
    }

    /**
     * 重新加载页面
     * @param $mainBodyName
     * @param $sealType
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function reload($mainBodyName, $sealType)
    {
        if ($mainBodyName) {
            $mainBody = Company::where('name', $mainBodyName)->first()->id;
        } else {
            $mainBody = '';
        }
        $seals = Seal::when($mainBody, function ($query) use ($mainBody) {
            return $query->where('company_id', $mainBody);
        })->when($sealType, function ($query) use ($sealType) {
            return $query->where('seal_type', $sealType);
        })->paginate(10);
        //查询印章默认转出人
        $userRole  = WorkflowRole::firstRoleByName(WorkflowRole::ROLE_SEAL_DEFAULT_MANAGE);
        $roleUsers = $userRole->roleUser;
        $isShow    = self::isShow();
        return view('sealManage.index', compact('seals', 'mainBody', 'sealType', 'mainBodyName', 'roleUsers', 'isShow'));
    }

    public static function isShow()
    {
        $abilityCreate = Seal::SEAL_MANAGE_ABILITY_CREATE;//excel数据导入能力
        $abilityExport = Seal::SEAL_MANAGE_ABILITY_EXPORT;//模版下载能力
        $isHaveCreate  = \Bouncer::can($abilityCreate);
        $isHaveExport  = \Bouncer::can($abilityExport);
        $isShow        = false;
        if ($isHaveCreate || $isHaveExport) {
            $isShow = true;
        }
        return $isShow;
    }

    /**
     * ajax查询申请用章人员
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function ajaxSearch(Request $request)
    {
        $request      = $request->all();
        $sealId       = $request['seal_id'];
        $data         = [];
        $seal         = Seal::findOrFail($sealId);
        $sealMainBody = $seal->company_id;
        $seamType     = $seal->seal_type;
        $flows        = Workflow::getFlowUserDataV1(Entry::WORK_FLOW_NO_APPLY_CERTIFICATE, null, null, Entry::STATUS_FINISHED);
        $userIds      = [];
        $entryIds     = [];
        foreach ($flows as $flow) {
            if (!isset($flow['entry']['user_id'])) {
                throw new DevFixException('缺少参数user_id');
            }

            if (isset($flow['form_data']['company_name_list'])) {
                $companyInfo = Company::getCompanyIdByName($flow['form_data']['company_name_list']['value']);
                $companyId   = $companyInfo ? $companyInfo->id : 0;
                if ($companyId == $sealMainBody && $flow['form_data']['type']['value'] == $seamType) {//筛选出某个印章的使用人员
                    $sealChangeLog = SealChangeLog::where('change_entry_id', $flow['entry']['id'])->first();
                    if (!$sealChangeLog) {//防止流程重复申请
                        $userId            = $flow['entry']['user_id'];
                        $userIds[]         = $userId;
                        $entryIds[$userId] = $flow['entry']['id'];
                    }
                }
            } elseif (isset($flow['form_data']['company_name'])) {
                $companyInfo = Company::getCompanyIdByName($flow['form_data']['company_name']['value']);
                $companyId   = $companyInfo ? $companyInfo->id : 0;
                if ($companyId == $sealMainBody && $flow['form_data']['type']['value'] == $seamType) {//筛选出某个印章的使用人员
                    $sealChangeLog = SealChangeLog::where('change_entry_id', $flow['entry']['id'])->first();
                    if (!$sealChangeLog) {//防止流程重复申请
                        $userId            = $flow['entry']['user_id'];
                        $userIds[]         = $userId;
                        $entryIds[$userId] = $flow['entry']['id'];
                    }
                }
            }
        }
        $users = User::whereIn('id', $userIds)->get();
        foreach ($users as $user) {
            $item_tmp = [
                'id'      => $user['id'],
                'text'    => $user['chinese_name'],
                'entryId' => $entryIds[$user['id']],
            ];
            array_push($data, $item_tmp);
        }
        return response()->json($data);
    }

    /**
     * ajax查询印章记录
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function flowRecord(Request $request)
    {
        $request = $request->all();
        $sealId  = $request['seal_id'];
        $num     = 0;
        if ($request['num']) {
            $num = $request['num'];
        }
        $pageCount   = 2;//一次展示的记录条数
        $offset      = $num * $pageCount;
        $sealChanges = SealChangeLog::where('change_seal_id', $sealId)->orderBy('created_at', 'desc')->offset($offset)
            ->limit($pageCount)->get();
        $data        = [];
        foreach ($sealChanges as $sealChange) {
            if ($sealChange->change_status == SealChangeLog::SEAL_HOLD) {
                $dutyUser = User::findOrFail($sealChange->change_receive_user_id)->chinese_name;
            } else {
                $dutyUser = User::findOrFail($sealChange->change_lend_user_id)->chinese_name;
            }
            $item_tmp = [
                'num'          => $num + 1,
                'duty_user'    => $dutyUser,//责任人
                'lend_user'    => User::findOrFail($sealChange->change_lend_user_id)->chinese_name,
                'receive_user' => $dutyUser = User::findOrFail($sealChange->change_receive_user_id)->chinese_name,
                'date'         => $sealChange->created_at,
            ];
            array_push($data, $item_tmp);
        }
        return response()->json($data);
    }

    /**
     * 印章转出新建流转记录
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function turnOut(Request $request)
    {
        $data                        = [];
        $request                     = $request->all();
        $data['change_seal_id']      = $request['seal_id'];
        $data['change_lend_user_id'] = auth()->id();
        $data['change_status']       = SealChangeLog::SEAL_FLOWING;

        if (strpos($request['seal_receive_user_id'], '(')) {
            $userAndEntry                   = explode('(', $request['seal_receive_user_id']);
            $data['change_receive_user_id'] = intval($userAndEntry['0']);
            $data['change_entry_id']        = explode(')', $userAndEntry['1'])[0];
        } else {
            $data['change_receive_user_id'] = $request['seal_receive_user_id'];
            $data['change_entry_id']        = '';
        }

        if ($data['change_lend_user_id'] == $data['change_receive_user_id']) {
            return response()->json(['status' => 'failed', 'message' => '转出人员不能为当前持有人！']);
        }
        $SealChangeLog = new SealChangeLog();
        $SealChangeLog->fill($data);

        if (!$SealChangeLog->save()) {
            $message = "印章转出失败！";
            $status  = "failed";
        } else {
            $message = "印章转出中！";
            $status  = "success";
        }
        return response()->json(['status' => $status, 'message' => $message]);
    }

    /**
     * 确认接收印章
     * @param $sealId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function sealReceive($sealId)
    {

        $message = '';
        try {
            $sealChangeLog = SealChangeLog::where('change_seal_id', $sealId)->where('change_status', SealChangeLog::SEAL_FLOWING)->first();
            if ($sealChangeLog) {
                //修改转出记录表中的状态为持有中
                $sealChangeLog->change_status = SealChangeLog::SEAL_HOLD;
                $seal                         = Seal::findOrFail($sealId);
                //修改印章表的当前责任人
                if ($sealChangeLog->change_receive_user_id != auth()->id()) {
                    throw new DevFixException('当前操作人和转让记录的接收者不符合');
                }
                $seal->seal_hold_user_id = $sealChangeLog->change_receive_user_id;
                $sealChangeLog->save();
                $seal->save();
            } else {
                throw new DevFixException('没有找到转出记录');
            }
        } catch (Exception $e) {
            $message = $e->getMessage();
        }

        if (!$message) {
            return response()->json(['status' => 'success', 'message' => '接受成功！']);
        } else {
            return response()->json(['status' => 'failed', 'message' => $message]);
        }
    }

    /**
     * 根据印章主体和类型查询
     * @param Request $request
     */
    public function search(Request $request)
    {
        try {
            $mainBodyName      = $request->input('seal_main_body');
            $companies         = Contract::getCompanyIdNameList();
            $isTrueCompanyName = false;
            foreach ($companies as $company) {
                if ($company == $mainBodyName) {
                    $isTrueCompanyName = true;
                    break;
                }
            }

            if (!$isTrueCompanyName) {
                throw new UserFixException('请正确填写印章主体');
            }
            if ($mainBodyName) {
                $mainBody = Company::where('name', $mainBodyName)->first()->id;
            } else {
                $mainBody = '';
            }
            $sealType = $request->input('seal_type');
            if ($mainBody && !$sealType) {
                $seals = Seal::whereCompanyId($mainBody);
            } elseif (!$mainBody && $sealType) {
                $seals = Seal::whereSealType($sealType);
            } elseif ($mainBody && $sealType) {
                $seals = Seal::whereCompanyId($mainBody)->whereSealType($sealType);
            } else {
                $seals = collect();
            }
            $seals = $seals->get();
            //查询印章默认转出人
            $userRole  = WorkflowRole::firstRoleByName(WorkflowRole::ROLE_SEAL_DEFAULT_MANAGE);
            $roleUsers = $userRole->roleUser;
            $isShow    = self::isShow();
        } catch (Exception $e) {
            $messages = $e->getMessage();
            return back()->with('sealError', $messages)->withInput();
        }
        return view('sealManage.index', compact('seals', 'mainBody', 'sealType', 'mainBodyName', 'roleUsers', 'isShow'));
    }

    /**
     * 导入数据excel模版下载
     */
    public function download()
    {
        $spreadsheet = new Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();

        $n = 1;
        $sheet->setCellValue('A' . $n, '公司名称');
        $sheet->setCellValue('B' . $n, '印章名称（合同章、公章、发票专用章、财务专用章、法人章、营业执照、信用代码证、开户许可证）');
        $sheet->setCellValue('C' . $n, '印章所属人名称');

        $fileName = '印章管理' . date("Ymd") . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');//告诉浏览器输出07Excel文件

        header('Content-Disposition: attachment;filename=' . $fileName);//告诉浏览器输出浏览器名称
        header('Cache-Control: max-age=0');//禁止缓存
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');

        exit;
    }

    /**
     * 批量节假日管理数据
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function importSealsInfo()
    {
        // 文件基础判断
        $fileName = $_FILES['order_process_file']['tmp_name'];
        if (empty($fileName)) {
            throw new UserFixException("没有获取到文件！");
        }

        $phpExcel     = IOFactory::load($fileName);
        $objWorksheet = $phpExcel->getActiveSheet();
        $highestRow   = $objWorksheet->getHighestRow();
        $total_column = 'C';//最高有效列数
        $loadData     = self::loadDate($objWorksheet, $highestRow, $total_column);
        if (!$loadData) {
            throw new UserFixException('表数据为空');
        }
        return redirect()->route('sealManage.index')->with('sealFlowSuccess', "添加数据成功！");
    }

    public static $needColumns = [
        'A' => [
            'index' => 'company_id',
            'value' => '公司名称',
        ],
        'B' => [
            'index' => 'seal_type',
            'value' => '印章类型',
        ],
        'C' => [
            'index' => 'seal_hold_user_id',
            'value' => '当前责任人',
        ],
    ];

    /**
     * @param $objWorksheet
     * @param $highestRow
     * @param $total_column
     * @return array
     */
    public static function loadDate($objWorksheet, $highestRow, $total_column)
    {
        $allData   = [];
        $needCount = count(self::$needColumns);
        for ($rowIndex = 2; $rowIndex <= $highestRow; $rowIndex++) {
            $data = [];
            for ($column = 'A'; $column <= $total_column; $column++) {
                if (!isset(self::$needColumns[$column])) {
                    continue;
                }
                $index        = self::$needColumns[$column]['index'];
                $value        = trim($objWorksheet->getCell($column . $rowIndex)->getValue());
                $data[$index] = $value;
            }
            if (count($data) != $needCount) {
                continue;
            }
            $userName    = $data['seal_hold_user_id'];
            $user        = User::where('chinese_name', $userName)->first();
            $companyName = $data['company_id'];
            $company     = Company::where('name', $companyName)->first();
            if (!$company) {
                throw new UserFixException('未找到该公司');
            }
            if (!$user) {
                throw new UserFixException('不存在该用户:' . $userName);
            }
            $data['seal_hold_user_id'] = $user->id;
            $data['company_id']        = $company->id;
            $seal                      = Seal::where('company_id', $data['company_id'])
                ->where('seal_type', $data['seal_type'])
                ->where('seal_hold_user_id', $data['seal_hold_user_id'])->first();
            if (!$seal) {
                $seal = new Seal();
            }
            $seal->fill($data);
            $seal->save();
            $allData[] = $data;
        }
        return $allData;
    }

}
