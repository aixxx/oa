<?php

namespace App\Repositories\Assets;

use App\Constant\ConstFile;
use App\Constant\CorporateAssetsConstant;
use App\Models\Assets\CorporateAssets;
use App\Models\Assets\CorporateAssetsBorrow;
use App\Models\Assets\CorporateAssetsInnerdb;
use App\Models\Assets\CorporateAssetsRelation;
use App\Models\Workflow\Entry;
use App\Models\Workflow\Flow;
use App\Models\Workflow\Proc;
use App\Services\AuthUserShadowService;
use Carbon\Carbon;
use Prettus\Repository\Eloquent\BaseRepository;
use Exception;
use Illuminate\Http\Request;
use DB;
use Auth;

class CorporateAssetsRepository extends BaseRepository
{

    public function model()
    {
        return CorporateAssets::class;
    }

    /**
     * @param array $data
     * @param $user
     * @return false|\Illuminate\Http\JsonResponse|string
     * @throws \Throwable
     */
    public function created(array $data, $user)
    {
        try {
            $error = $this->checkData($data);
            if ($error) {
                throw new Exception(sprintf('请求参数错误：' . $error), ConstFile::API_RESPONSE_FAIL);
            }
            //这里是权限判断，先保留
            DB::transaction(function () use ($data, $user) {


                $data['status'] = CorporateAssetsConstant::ASSETS_STATUS_IDLE;
                if ($data['nature'] == CorporateAssetsConstant::NATURE_DEPRECIATION_ASSETS) {
                    $data['depreciation_status'] = CorporateAssetsConstant::ASSETS_DEPRECIATION_STATUS_CAN;
                    $data['remaining_at'] = $data['buy_time'];
                }
                $data['company_id'] = $user->company_id;
                $data['photo'] = isset($data['photo']) ? $data['photo'] : '';
                CorporateAssets::create($data);
            });
        } catch (Exception $e) {
            return returnJson($e->getMessage(), $e->getCode());
        }
        return returnJson(ConstFile::API_RESPONSE_SUCCESS_MESSAGE, ConstFile::API_RESPONSE_SUCCESS);
    }

    /**
     * @param $user
     * @return false|\Illuminate\Http\JsonResponse|string
     */
    public function fetchList($user, $status, $attr, $cat, $department_id)
    {
        try {
            $corporateAssets = CorporateAssets::where('company_id', '=', $user->company_id)
                ->when($status, function ($query) use ($status) {
                    $query->where('status', $status);
                })
                ->when($attr, function ($query) use ($attr) {
                    $query->where('attr', $attr);
                })
                ->when($cat, function ($query) use ($cat) {
                    $query->where('cat', $cat);
                })
                ->when($department_id, function ($query) use ($department_id) {
                    $query->where('department_id', $department_id);
                })
                ->orderby('id', SORT_DESC)
                ->paginate(ConstFile::PAGE_SIZE);
            $res = $corporateAssets->toArray();
            $list = $res['data'];
            foreach ($list as $key => $val) {
                $list[$key]['attr_name'] = CorporateAssetsConstant::$attr[$val['attr']];
                $list[$key]['cat_name'] = CorporateAssetsConstant::$category[$val['cat']];
                $list[$key]['source_name'] = CorporateAssetsConstant::$source[$val['source']];
                $list[$key]['method_name'] = CorporateAssetsConstant::$depreciation_method[$val['depreciation_method']];
                $list[$key]['status_msg'] = CorporateAssetsConstant::$assets_status[$val['status']];
            }
            $res['data'] = $list;
            $totalNum = $corporateAssets->count();
            $data = ['corporate_assets' => $res, 'total_num' => $totalNum];
        } catch (Exception $e) {
            return returnJson($e->getMessage(), ConstFile::API_RESPONSE_FAIL);
        }

        return returnJson(ConstFile::API_RESPONSE_SUCCESS_MESSAGE, ConstFile::API_RESPONSE_SUCCESS, $data);
    }

    /**
     * @param $user
     * @param $id
     * @return false|\Illuminate\Http\JsonResponse|string
     */
    public function fetchInfo($user, $id)
    {
        try {
            $corporateAssets = CorporateAssets::find($id);
            $corporateAssetsRelation = $corporateAssets->hasManyCorporateAssetsRelation;

            $info = $corporateAssets->toArray();
            $history = $corporateAssetsRelation->toArray();
            foreach ($history as $key => $val) {
                $history[$key]['type_msg'] = CorporateAssetsConstant::$assets_relation_type[$val['type']];
            }
            $data['info'] = $info;
            $data['history'] = $history;
        } catch (Exception $e) {
            return returnJson($e->getMessage(), ConstFile::API_RESPONSE_FAIL);
        }
        return returnJson(ConstFile::API_RESPONSE_SUCCESS_MESSAGE, ConstFile::API_RESPONSE_SUCCESS, $data);
    }

    /**
     * @param $user
     * @return false|\Illuminate\Http\JsonResponse|string
     */
    public function management($user, $type, $keywords)
    {
        try {
            $corporateAssetsRelation = CorporateAssetsRelation::where('user_id', '=', $user->id)
                ->when($type, function ($query) use ($type) {
                    $query->where('type', $type);
                })
                ->when($keywords, function ($query) use ($keywords) {
                    $query->whereRaw("concat(`type_name`,`created_at`) like '%{$keywords}%'");
                })
                ->orderby('id', SORT_DESC)
                ->paginate(ConstFile::PAGE_SIZE);
            $res = $corporateAssetsRelation->toArray();
            $list = $res['data'];
            foreach ($list as $key => $val) {

            }
            $res['data'] = $list;
            $totalNum = $corporateAssetsRelation->count();
            $data = ['corporate_assets' => $res, 'total_num' => $totalNum];
        } catch (Exception $e) {
            return returnJson($e->getMessage(), ConstFile::API_RESPONSE_FAIL);
        }
        return returnJson(ConstFile::API_RESPONSE_SUCCESS_MESSAGE, ConstFile::API_RESPONSE_SUCCESS, $data);

    }

    /**
     * @param $user
     * @param $tab
     * @param Request $request
     * @return false|\Illuminate\Http\JsonResponse|string
     */
    public function processQuery($user, $tab, Request $request)
    {

        try {
            $myAuditedSearchData = [];
            $myProcsSearchData = [];
            $myApplySearchData = [];
            if ($tab == 'my_audited') {
                $myAuditedSearchData = $request->except('tab');
            } elseif ($tab == 'my_procs') {
                $myProcsSearchData = $request->except('tab');
            } elseif ($tab == 'my_apply') {
                $myApplySearchData = $request->except('tab');
            }
            //$workflow_flow = Flow::getFlowsOfNo(); // 流程列表
            $workflow_flow_corporate = Flow::getFlowsOfNoCorporate(); // 流程列表

            $flows_id[] = $workflow_flow_corporate[Entry::CORPORATE_ASSETS_USE]['id'];
            $flows_id[] = $workflow_flow_corporate[Entry::CORPORATE_ASSETS_BORROW]['id'];
            $flows_id[] = $workflow_flow_corporate[Entry::CORPORATE_ASSETS_RETURN]['id'];
            $flows_id[] = $workflow_flow_corporate[Entry::CORPORATE_ASSETS_TRANSFER]['id'];
            $flows_id[] = $workflow_flow_corporate[Entry::CORPORATE_ASSETS_REPAIR]['id'];
            $flows_id[] = $workflow_flow_corporate[Entry::CORPORATE_ASSETS_SCRAPPED]['id'];
            $flows_id[] = $workflow_flow_corporate[Entry::CORPORATE_ASSETS_VALUEADDED]['id'];
            $flows_id[] = $workflow_flow_corporate[Entry::CORPORATE_ASSETS_DEPRECIATION]['id'];

            $flows[Entry::CORPORATE_ASSETS_USE] = $workflow_flow_corporate[Entry::CORPORATE_ASSETS_USE]['flow_name'];
            $flows[Entry::CORPORATE_ASSETS_BORROW] = $workflow_flow_corporate[Entry::CORPORATE_ASSETS_BORROW]['flow_name'];
            $flows[Entry::CORPORATE_ASSETS_RETURN] = $workflow_flow_corporate[Entry::CORPORATE_ASSETS_RETURN]['flow_name'];
            $flows[Entry::CORPORATE_ASSETS_TRANSFER] = $workflow_flow_corporate[Entry::CORPORATE_ASSETS_TRANSFER]['flow_name'];
            $flows[Entry::CORPORATE_ASSETS_REPAIR] = $workflow_flow_corporate[Entry::CORPORATE_ASSETS_REPAIR]['flow_name'];
            $flows[Entry::CORPORATE_ASSETS_SCRAPPED] = $workflow_flow_corporate[Entry::CORPORATE_ASSETS_SCRAPPED]['flow_name'];
            $flows[Entry::CORPORATE_ASSETS_VALUEADDED] = $workflow_flow_corporate[Entry::CORPORATE_ASSETS_VALUEADDED]['flow_name'];
            $flows[Entry::CORPORATE_ASSETS_DEPRECIATION] = $workflow_flow_corporate[Entry::CORPORATE_ASSETS_DEPRECIATION]['flow_name'];

            //我审批过的
            $authAuditor = new AuthUserShadowService();
            $myAuditedSearchData['flow_id'] = $flows_id;
            $procsAudited = Proc::getUserAuditedByPage($authAuditor->id(), $myAuditedSearchData, ConstFile::PAGE_SIZE);
            $myAuditedSearchData['tab'] = 'my_audited';
            // 我审批过的只需要处理中、结束、拒绝三种过滤状态
            $entryAuditorStatusMap = [
                Entry::STATUS_IN_HAND => Entry::STATUS_MAP[Entry::STATUS_IN_HAND],
                Entry::STATUS_FINISHED => Entry::STATUS_MAP[Entry::STATUS_FINISHED],
                Entry::STATUS_REJECTED => Entry::STATUS_MAP[Entry::STATUS_REJECTED],
            ];
            //待我审批
            $myProcsSearchData['flow_id'] = $flows_id;
            $procsProc = Proc::getUserProcByPage($authAuditor->id(), $myProcsSearchData, ConstFile::PAGE_SIZE);
            $entryProcStatusMap = Entry::STATUS_MAP; // 申请单状态map
            $myProcsSearchData['tab'] = 'my_procs';
            //我提交过的
//            $myApplySearchData['flow_id'] = $flows_id;
//            $entries = Entry::getApplyEntries($authAuditor->id(), $myApplySearchData, ConstFile::PAGE_SIZE);
//
//            $entryEntriesStatusMap = Entry::STATUS_MAP; // 申请单状态map
//            $myApplySearchData['tab'] = 'my_apply';

            $data['procsAudited'] = $procsAudited;
            $data['flows'] = $flows;
            $data['entryAuditorStatusMap'] = $entryAuditorStatusMap;
            $data['procsProc'] = $procsProc;
            $data['entryProcStatusMap'] = $entryProcStatusMap;
//            $data['entries'] = $entries;
//            $data['entryEntriesStatusMap'] = $entryEntriesStatusMap;
            $data['myAuditedSearchData'] = $myAuditedSearchData;
            $data['myProcsSearchData'] = $myProcsSearchData;
            $data['myApplySearchData'] = $myApplySearchData;

        } catch (Exception $e) {
            return returnJson($e->getMessage(), ConstFile::API_RESPONSE_FAIL);
        }
        return returnJson(ConstFile::API_RESPONSE_SUCCESS_MESSAGE, ConstFile::API_RESPONSE_SUCCESS, $data);

    }

    public function getNum($act)
    {
        try {

            switch ($act) {
                case "borrow":
                    //借用
                    DB::beginTransaction();
                    DB::table('corporate_assets_innerdb')->delete();
                    $innerdb = CorporateAssetsInnerdb::create();
                    $num = 1000 + intval($innerdb->id);
                    $data['num'] = 'JY' . date('Ymd') . $num;
                    DB::commit();
                    break;
                case "depreciation":
                    //折旧
                    DB::beginTransaction();
                    DB::table('corporate_assets_innerdb')->delete();
                    $innerdb = CorporateAssetsInnerdb::create();
                    $num = 1000 + intval($innerdb->id);
                    $data['num'] = 'JS' . date('Ymd') . $num;
                    DB::commit();
                    break;
                case "repair":
                    //送修
                    DB::beginTransaction();
                    DB::table('corporate_assets_innerdb')->delete();
                    $innerdb = CorporateAssetsInnerdb::create();
                    $num = 1000 + intval($innerdb->id);
                    $data['num'] = 'SX' . date('Ymd') . $num;
                    DB::commit();
                    break;
                case "return":
                    //归还
                    DB::beginTransaction();
                    DB::table('corporate_assets_innerdb')->delete();
                    $innerdb = CorporateAssetsInnerdb::create();
                    $num = 1000 + intval($innerdb->id);
                    $data['num'] = 'GH' . date('Ymd') . $num;
                    DB::commit();
                    break;
                case "scrapped":
                    //报废
                    DB::beginTransaction();
                    DB::table('corporate_assets_innerdb')->delete();
                    $innerdb = CorporateAssetsInnerdb::create();
                    $num = 1000 + intval($innerdb->id);
                    $data['num'] = 'BF' . date('Ymd') . $num;
                    DB::commit();
                    break;
                case "transfer":
                    //调拨
                    DB::beginTransaction();
                    DB::table('corporate_assets_innerdb')->delete();
                    $innerdb = CorporateAssetsInnerdb::create();
                    $num = 1000 + intval($innerdb->id);
                    $data['num'] = 'DB' . date('Ymd') . $num;
                    DB::commit();
                    break;
                case "use":
                    //领用
                    DB::beginTransaction();
                    DB::table('corporate_assets_innerdb')->delete();
                    $innerdb = CorporateAssetsInnerdb::create();
                    $num = 1000 + intval($innerdb->id);
                    $data['num'] = 'LY' . date('Ymd') . $num;
                    DB::commit();
                    break;
                case "valueadded":
                    //增值
                    DB::beginTransaction();
                    DB::table('corporate_assets_innerdb')->delete();
                    $innerdb = CorporateAssetsInnerdb::create();
                    $num = 1000 + intval($innerdb->id);
                    $data['num'] = 'JS' . date('Ymd') . $num;
                    DB::commit();
                    break;
            }
        } catch (Exception $e) {
            DB::rollBack();
            return returnJson($e->getMessage(), ConstFile::API_RESPONSE_FAIL);
        }
        return returnJson(ConstFile::API_RESPONSE_SUCCESS_MESSAGE, ConstFile::API_RESPONSE_SUCCESS, $data);
    }




    public function report($departmentId)
    {
        try {
            $data['tatol'] = $tatol = CorporateAssets::when($departmentId, function ($query) use ($departmentId) {
                $query->where('department_id', $departmentId);
            })->count();
            $data['fixed'] = $fixed = CorporateAssets::when($departmentId, function ($query) use ($departmentId) {
                $query->where('department_id', $departmentId);
            })->where('attr', CorporateAssetsConstant::ATTR_FIXED_ASSETS)->count();
            $data['fictitious'] = $fictitious = CorporateAssets::when($departmentId, function ($query) use ($departmentId) {
                $query->where('department_id', $departmentId);
            })->where('attr', CorporateAssetsConstant::ATTR_FICTITIOUS_ASSETS)->count();
            $data['valueadded'] = $fictitious = CorporateAssets::when($departmentId, function ($query) use ($departmentId) {
                $query->where('department_id', $departmentId);
            })->where('nature', CorporateAssetsConstant::NATURE_VALUE_ADDED_ASSETS)->count();
            $data['depreciation'] = $fictitious = CorporateAssets::when($departmentId, function ($query) use ($departmentId) {
                $query->where('department_id', $departmentId);
            })->where('nature', CorporateAssetsConstant::NATURE_DEPRECIATION_ASSETS)->count();

            $data['idle'] = $fictitious = CorporateAssets::when($departmentId, function ($query) use ($departmentId) {
                $query->where('department_id', $departmentId);
            })->where('status', CorporateAssetsConstant::ASSETS_STATUS_IDLE)->count();

            $data['using'] = $fictitious = CorporateAssets::when($departmentId, function ($query) use ($departmentId) {
                $query->where('department_id', $departmentId);
            })->where('status', CorporateAssetsConstant::ASSETS_STATUS_USING)->count();

            $data['transfer'] = $fictitious = CorporateAssets::when($departmentId, function ($query) use ($departmentId) {
                $query->where('department_id', $departmentId);
            })->where('status', CorporateAssetsConstant::ASSETS_STATUS_TRANSFER)->count();

            $data['repair'] = $fictitious = CorporateAssets::when($departmentId, function ($query) use ($departmentId) {
                $query->where('department_id', $departmentId);
            })->where('status', CorporateAssetsConstant::ASSETS_STATUS_REPAIR)->count();
            $data['scrapped'] = $fictitious = CorporateAssets::when($departmentId, function ($query) use ($departmentId) {
                $query->where('department_id', $departmentId);
            })->where('status', CorporateAssetsConstant::ASSETS_STATUS_SCRAPPED)->count();

        } catch (Exception $e) {
            return $e->getMessage();
        }
        return $data;
    }


    public function checkData($data)
    {

        if (empty($data)) {
            return '请求数据不能为空';
        }
        if (!isset($data['name']) || empty($data['name'])) {
            return '资产名称不能为空';
        }
        if (!isset($data['num']) || empty($data['num'])) {
            return '资产编号不能为空';
        }

        if (!isset($data['attr']) || empty($data['attr'])) {
            return '请选择资产属性';
        }
        if (!isset($data['cat']) || empty($data['cat'])) {
            return '请选择资产分类';
        }
        if (!isset($data['department_id']) || empty($data['department_id'])) {
            return '请选择所属部门';
        }
        if (!isset($data['source']) || empty($data['source'])) {
            return '请选择资产来源';
        }
        if (!isset($data['price']) || empty($data['price'])) {
            return '请输入价格';
        }
        if (!isset($data['metering']) || empty($data['metering'])) {
            return '请输入计量单位';
        }
        if (!isset($data['buy_time']) || empty($data['buy_time'])) {
            return '请选择购买时间';
        }
        if (!isset($data['nature']) || empty($data['nature'])) {
            return '请选择资产性质';
        }
        if ($data['nature'] == CorporateAssetsConstant::NATURE_DEPRECIATION_ASSETS) {
            if (!isset($data['depreciation_cycle']) || empty($data['depreciation_cycle'])) {
                return '请输入折旧周期';
            }
            if (!isset($data['depreciation_interval']) || empty($data['depreciation_interval'])) {
                return '请输入折旧间隔';
            }
            if (!isset($data['depreciation_method']) || empty($data['depreciation_method'])) {
                return '请选择折旧方法';
            }
        }
        if (!isset($data['location']) || empty($data['location'])) {
            return '请输入资产位置';
        }
        return null;
    }


}
