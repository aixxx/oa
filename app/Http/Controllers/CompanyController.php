<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 2018/6/13
 * Time: 15:13
 */

namespace App\Http\Controllers;

use App\Http\Controllers\Workflow\EntryController;
use App\Models\Company;
use App\Models\CompanyChange;
use App\Http\Requests\CompanyRequest;
use App\Models\CompanyEquityPledge;
use App\Models\CompanyMainPersonnels;
use App\Models\CompanyShareholders;
use App\Models\Workflow\Entry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\OperateLog;
use DevFixException;
use UserFixException;

class CompanyController extends Controller
{

    public function __construct()
    {
        $this->middleware('afterlog')->only('store','update','destroy');
    }

    public function index()
    {
        $companies = Company::where('status', '<>', Company::STATUS_DELETE)->orderBy('id', SORT_DESC)->paginate(30);
        if (isset($companies))
        {
            foreach ($companies as $key => $each)
            {
                $partentInfo = Company::where('id','=', $each->parent_id)->first();
                $companies[$key]['parent_name'] = $partentInfo ? $partentInfo->name : '';
            }
        }
        return view('companies.index',compact('companies'));
    }

    /**
     * 创建公司及子机构
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        $companies = Company::where('status', '<>', Company::STATUS_DELETE)->get();

        return view('companies.create', compact('companies'));
    }

    public function store(CompanyRequest $request)
    {
        $companyInfo = $request->except('_token');

        $company = new Company;

        DB::beginTransaction();

        try
        {
            $priId = $company->insertGetId($companyInfo);

            if (!$priId)
            {
                throw new DevFixException("公司创建失败！");
            }

            $companyChange = CompanyChange::saveChange(auth()->id(),$priId,null,$companyInfo,"营业执照信息添加",CompanyChange::ADD_COMPANY,CompanyChange::MODULE_COMPANY);

            if (!$companyChange)
            {
                throw new DevFixException("公司信息变更记录失败！");
            }

            DB::commit();
            return redirect()->route('companies.index');
        } catch(\Exception $e) {
            DB::rollBack();
            $messages = $e->getMessage();
            return back()->with('saveError',$messages);
        }
    }

    public function edit($id)
    {
        $companyInfo = Company::findOrFail($id);
        $companies = Company::where('status', '<>', Company::STATUS_DELETE)->get();
        $companyShareholders   = CompanyShareholders::where('company_id', '=', $id)->where('status','<>', CompanyShareholders::STATUS_DELETE)->orderby('id', SORT_DESC)->get();
        $companyMainPersonnels = CompanyMainPersonnels::where('company_id', '=', $id)->where('status','<>', CompanyShareholders::STATUS_DELETE)->orderby('id', SORT_DESC)->get();
        $companyEquityPledge   = CompanyEquityPledge::where('company_id', '=', $id)->where('status','<>', CompanyEquityPledge::STATUS_DELETE)->orderby('id', SORT_DESC)->get();
        return view('companies.edit',compact('companyInfo', 'companies','companyShareholders','companyMainPersonnels','companyEquityPledge'));
    }

    public function update($id, CompanyRequest $request)
    {
        $updateInfo = $request->except('_token');

        $company = Company::findOrFail($id);

        $oldCompany = $company->toArray();

        DB::beginTransaction();

        try
        {
            $updateStatus = $company->update($updateInfo);
            if (!$updateStatus)
            {
                throw new DevFixException("公司信息更新失败");
            }
            $result = CompanyChange::saveChange(auth()->id(),$company->id,$oldCompany,$updateInfo,"营业执照信息编辑",CompanyChange::EDIT_COMPANY,CompanyChange::MODULE_COMPANY);
            if (!$result)
            {
                throw new DevFixException("公司变更信息保存失败");
            }

            DB::commit();
            return redirect()->route('companies.edit',['id' => $id])->with('saveSuccess','公司信息更新成功！');
        } catch (\Exception $e) {
            DB::rollBack();
            $messages = $e->getMessage();
            return back()->with('saveError',$messages);
        }

    }

    public function destroy(Request $request)
    {
        $companyId = $request->get('id');

        DB::beginTransaction();
        try
        {
            if ($companyId)
            {
                $resultEmployee = Company::hasEmployee($companyId);
                if (isset($resultEmployee['status']) && $resultEmployee['status'] == "failed")
                {
                    throw new UserFixException($resultEmployee['messages']);
                }

                $child = Company::where('parent_id', '=', $companyId)->where('status','=',Company::STATUS_NO_DELETE)->get()->toArray();
                if ($child)
                {
                    throw new UserFixException("机构下有子机构，禁止删除！");
                }

                $companyInfo   = Company::findOrFail($companyId);
                $oldCompany    = $companyInfo->toArray();
                $deleteCompany = Company::where('id', '=', $companyId)->update(['status' => Company::STATUS_DELETE]);

                $equityInfo    = CompanyEquityPledge::where('company_id', '=', $companyId)->get()->toArray();

                if ($equityInfo)
                {
                    $deleteEquity  = CompanyEquityPledge::where('company_id', '=', $companyId)->update(['status' => CompanyEquityPledge::STATUS_DELETE]);
                } else {
                    $deleteEquity = 1;
                }

                $mainInfo = CompanyMainPersonnels::where('company_id', '=', $companyId)->get()->toArray();
                if ($mainInfo)
                {
                    $deleteMain    = CompanyMainPersonnels::where('company_id', '=', $companyId)->update(['status' => CompanyMainPersonnels::STATUS_DELETE]);
                } else {
                    $deleteMain = 1;
                }

                $shareInfo = CompanyShareholders::where('company_id', '=', $companyId)->get()->toArray();
                if ($shareInfo)
                {
                    $deleteShare   = CompanyShareholders::where('company_id', '=', $companyId)->update(['status' => CompanyShareholders::STATUS_DELETE]);
                } else {
                    $deleteShare = 1;
                }

                if (!$deleteCompany || !$deleteEquity || !$deleteMain || !$deleteShare)
                {
                    throw new DevFixException("公司删除失败！");
                }

                $result = CompanyChange::saveChange(auth()->id(),$companyId,$oldCompany,null,'公司信息删除',CompanyChange::DELETE_COMPANY,CompanyChange::MODULE_COMPANY);
                if (!$result)
                {
                    throw new DevFixException("公司变更信息保存失败");
                }
            }

            DB::commit();
            return response()->json(['status' => 'success','messages' => '公司删除成功！']);
        } catch (\Exception $e) {
            DB::rollBack();
            $messages = $e->getMessage();
            return response()->json(['status' => 'failed','messages' => $messages]);
        }

    }


    public function show($id)
    {
        $companyInfo           = Company::findOrFail($id);
        $companyShareholders   = CompanyShareholders::where('company_id', '=', $id)->where('status','<>', CompanyShareholders::STATUS_DELETE)->orderby('id', SORT_DESC)->get();
        $companyMainPersonnels = CompanyMainPersonnels::where('company_id', '=', $id)->where('status','<>', CompanyShareholders::STATUS_DELETE)->orderby('id', SORT_DESC)->get();
        $childCompanies        = Company::where('parent_id', '=', $id)->where('status','<>', CompanyShareholders::STATUS_DELETE)->orderby('id', SORT_DESC)->get();
        $companyEquityPledge   = CompanyEquityPledge::where('company_id', '=', $id)->where('status','<>', CompanyEquityPledge::STATUS_DELETE)->orderby('id', SORT_DESC)->get();
        $companyChange = CompanyChange::where('company_id','=',$id)->orderBy('id','desc')->orderBy('change_at','desc')->get();
        $companyChange = CompanyChange::where('company_id','=',$id)->orderBy('id','desc')->orderBy('change_at','desc')->get()->each(function ($item,$key){

            if ($item->operate_user_id)
            {
                $userInfo = User::find($item->operate_user_id);
                if ($userInfo)
                {
                    $item->operate_user_name = $userInfo->chinese_name;
                } else {
                    $item->operate_user_name = "";
                }
            }
        });
        return view('companies.show',
            compact('companyInfo', 'companyShareholders', 'companyMainPersonnels', 'childCompanies', 'companyChange', 'companyEquityPledge'));
    }

    ###########################################股东及出资信息############################################
    /**
     * 创建股东
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function shareholderStore(Request $request)
    {
        $request = $request->all();
        $company = Company::findOrFail($request['company_id']);
        if (!$company) {
            abort('404','未找到相关公司');
        }

        $insertData['company_id']       = trim($request['company_id']);
        $insertData['name']             = trim($request['name']);
        $insertData['shareholder_type'] = trim($request['shareholder_type']);
        $insertData['certificate_type'] = trim($request['certificate_type']);
        $insertData['id_number']        = trim($request['id_number']);

        DB::beginTransaction();
        try
        {
            $oCompanyShareholders = new CompanyShareholders();
            $insertId = $oCompanyShareholders->insertGetId($insertData);
            if (!$insertId)
            {
                throw new DevFixException("创建失败!");
            }

            $result = CompanyChange::saveChange(auth()->id(),$company->id,null,$insertData,"股东及出资信息添加",CompanyChange::ADD_COMPANY,CompanyChange::MODULE_SHARE_HOLDERS);

            if (!$result)
            {
                throw new DevFixException("公司变更信息保存失败");
            }

            DB::commit();
            return response()->json(['status' => 'success', 'messages' => "创建成功"]);

        } catch (\Exception $e) {
            DB::rollBack();
            $messages = $e->getMessage();
            return response()->json(['status' => 'failed', 'messages' => $messages]);
        }

    }

    //股东编辑
    public function shareholderUpdate(Request $request)
    {
        $request = $request->all();
        $companyShareholders = CompanyShareholders::findOrFail($request['id']);
        $oldCompanyShareholders = $companyShareholders->toArray();

        $updateData['name']             = trim($request['name']);
        $updateData['shareholder_type'] = trim($request['shareholder_type']);
        $updateData['certificate_type'] = trim($request['certificate_type']);
        $updateData['id_number']        = trim($request['id_number']);

        DB::beginTransaction();

        try
        {
            $companyShareholders->fill($updateData);
            if (!$companyShareholders->save()) {
                throw new DevFixException("编辑失败");
            }

            $result = CompanyChange::saveChange(auth()->id(),$companyShareholders->company_id,$oldCompanyShareholders,$updateData,'股东及出资信息编辑',CompanyChange::EDIT_COMPANY,CompanyChange::MODULE_SHARE_HOLDERS);
            if (!$result)
            {
                throw new DevFixException("公司变更信息保存失败");
            }
            DB::commit();
            return response()->json(['status' => 'success', 'messages' => "编辑成功"]);
        }catch (\Exception $e) {
            DB::rollBack();
            $messages = $e->getMessage();
            return response()->json(['status' => 'failed', 'messages' => $messages]);
        }

    }

    /**
     * 删除股东
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function shareholderDelete(Request $request)
    {
        $request              = $request->all();
        $oCompanyShareholders = CompanyShareholders::findOrFail($request['id']);

        $oldCompanyShareholders = $oCompanyShareholders->toArray();
        $updateData['status'] = CompanyShareholders::STATUS_DELETE;


        DB::beginTransaction();
        try
        {
            $oCompanyShareholders->fill($updateData);
            if (!$oCompanyShareholders->save())
            {
                throw new DevFixException("删除失败！");
            }
            $result = CompanyChange::saveChange(auth()->id(),$oCompanyShareholders->company_id,$oldCompanyShareholders,null,'股东及出资信息删除',CompanyChange::DELETE_COMPANY,CompanyChange::MODULE_SHARE_HOLDERS);
            if (!$result)
            {
                throw new DevFixException("公司变更信息保存失败");
            }
            DB::commit();
            return response()->json(['status' => 'success', 'messages' => '删除成功！']);
        } catch (\Exception $e) {
            $messages = $e->getMessage();
            return response()->json(['status' => 'failed', 'messages' => $messages]);
        }

    }

    ###########################################主要人员信息############################################
    /**
     * 创建主要人员
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function mainPersonnelsStore(Request $request)
    {
        $request = $request->all();
        $company = Company::findOrFail($request['company_id']);
        if (!$company) {
            abort('404','未找到相关公司');
        }

        $insertData['company_id'] = trim($request['company_id']);
        $insertData['name']       = trim($request['name']);
        $insertData['position']   = trim($request['position']);

        DB::beginTransaction();
        try
        {
            $oCompanyMainPersonnels = new CompanyMainPersonnels();
            $inserId = $oCompanyMainPersonnels->insertGetId($insertData);
            if (!$inserId)
            {
                throw new DevFixException("创建失败");
            }
            $result = CompanyChange::saveChange(auth()->id(),$request['company_id'],null,$insertData,"主要人员信息添加",CompanyChange::ADD_COMPANY,CompanyChange::MODULE_MAIN_PERSONNELS);
            if (!$result)
            {
                throw new DevFixException("公司变更信息保存失败");
            }
            DB::commit();
            return response()->json(['status' => 'success', 'messages' => "创建成功"]);
        } catch (\Exception $e) {
            DB::rollBack();
            $messages = $e->getMessage();
            return response()->json(['status' => 'failed', 'messages' => $messages]);
        }
    }

    //编辑主要人员
    public function mainPersonnelsUpdate(Request $request)
    {
        $request = $request->all();
        $oCompanyMainPersonnels = CompanyMainPersonnels::findOrFail($request['id']);

        $oldCompanyMainPersonnels = $oCompanyMainPersonnels->toArray();

        $updateData['name']     = trim($request['name']);
        $updateData['position'] = trim($request['position']);


        DB::beginTransaction();
        try
        {
            $oCompanyMainPersonnels->fill($updateData);
            if (!$oCompanyMainPersonnels->save())
            {
                throw new DevFixException("编辑失败");
            }
            $result = CompanyChange::saveChange(auth()->id(),$oCompanyMainPersonnels->company_id,$oldCompanyMainPersonnels,$updateData,"主要人员信息编辑",CompanyChange::EDIT_COMPANY,CompanyChange::MODULE_MAIN_PERSONNELS);
            if (!$result)
            {
                throw new DevFixException("公司变更信息保存失败");
            }
            DB::commit();
            return response()->json(['status' => 'success', 'messages' => "编辑成功"]);
        } catch (\Exception $e) {
            $messages = $e->getMessage();
            return response()->json(['status' => 'failed', 'messages' => $messages]);
        }

    }

    /**
     * 删除主要人员
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function mainPersonnelsDelete(Request $request)
    {
        $request              = $request->all();
        $oCompanyMainPersonnels = CompanyMainPersonnels::findOrFail($request['id']);

        $oldCompanyMainPersonnels = $oCompanyMainPersonnels->toArray();
        $updateData['status'] = CompanyMainPersonnels::STATUS_DELETE;

        DB::beginTransaction();
        try
        {
            $oCompanyMainPersonnels->fill($updateData);
            if (!$oCompanyMainPersonnels->save())
            {
                throw new DevFixException("删除失败！");
            }
            $result = CompanyChange::saveChange(auth()->id(),$oCompanyMainPersonnels->company_id,$oldCompanyMainPersonnels,null,"主要人员信息删除",CompanyChange::DELETE_COMPANY,CompanyChange::MODULE_MAIN_PERSONNELS);
            if (!$result)
            {
                throw new DevFixException("公司变更信息保存失败");
            }
            DB::commit();
            return response()->json(['status' => 'success', 'messages' => '删除成功！']);
        } catch (\Exception $e){
            $messages = $e->getMessage();
            return response()->json(['status' => 'failed', 'messages' => $messages]);
        }

    }

    ###########################################机构信息############################################
    /**
     * 删除机构分支
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function childDelete(Request $request)
    {
        $request              = $request->all();
        $oCompany = Company::findOrFail($request['id']);

        $child = Company::where('parent_id', '=', $request['id'])->where('status', '<>', Company::STATUS_DELETE)->get()->toArray();
        $user = Company::hasEmployee($request['id']);

        if ($child || $user) {
            return response()->json(['status' => 'failed', 'messages' => '机构下有员工或子机构，禁止删除！']);
        } else {
            $updateData['status'] = Company::STATUS_DELETE;

            $oCompany->fill($updateData);
            if ($oCompany->save()) {
                return response()->json(['status' => 'success', 'messages' => '删除成功！']);
            } else {
                return response()->json(['status' => 'failed', 'messages' => '删除失败！']);
            }
        }
    }

    ###########################################股权出质############################################
    /**
     * 添加股权出质
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function pledgeStore(Request $request)
    {
        $request = $request->all();
        $company = Company::findOrFail($request['company_id']);
        if (!$company) {
            abort('404','未找到相关公司');
        }

        $insertData['company_id']        = trim($request['company_id']);
        $insertData['code']              = trim($request['code']);
        $insertData['pledgor']           = trim($request['pledgor']);
        $insertData['pledgor_id_number'] = trim($request['pledgorId']);
        $insertData['amount']            = trim($request['amount']);
        $insertData['pledgee']           = trim($request['pledgee']);
        $insertData['pledgee_id_number'] = trim($request['pledgeeId']);
        $insertData['register_date']     = trim($request['register']);
        $insertData['pledge_status']     = trim($request['status']);
        $insertData['public_at']         = trim($request['public']);

        DB::beginTransaction();
        try
        {
            $oCompanyEquityPledge = new CompanyEquityPledge();
            $insertId = $oCompanyEquityPledge->insertGetId($insertData);
            if (!$insertId)
            {
                throw new DevFixException("创建失败");
            }
            $result = CompanyChange::saveChange(auth()->id(),$company->id,null,$insertData,"股权出质登记信息添加",CompanyChange::ADD_COMPANY,CompanyChange::MODULE_EQUITY_PLEDGE);
            if (!$result)
            {
                throw new DevFixException("公司变更信息保存失败");
            }
            DB::commit();
            return response()->json(['status' => 'success', 'messages' => "创建成功"]);
        } catch (\Exception $e) {
            DB::rollBack();
            $messages = $e->getMessage();
            return response()->json(['status' => 'failed', 'messages' => $messages]);
        }
    }

    //编辑主要人员
    public function pledgeUpdate(Request $request)
    {
        $request = $request->all();
        $oCompanyEquityPledge = CompanyEquityPledge::findOrFail($request['id']);

        $oldCompanyEquityPledge = $oCompanyEquityPledge->toArray();

        $updateData['company_id']        = trim($request['company_id']);
        $updateData['code']              = trim($request['code']);
        $updateData['pledgor']           = trim($request['pledgor']);
        $updateData['pledgor_id_number'] = trim($request['pledgorId']);
        $updateData['amount']            = trim($request['amount']);
        $updateData['pledgee']           = trim($request['pledgee']);
        $updateData['pledgee_id_number'] = trim($request['pledgeeId']);
        $updateData['register_date']     = trim($request['register']);
        $updateData['pledge_status']     = trim($request['status']);
        $updateData['public_at']         = trim($request['public']);
        DB::beginTransaction();
        try
        {
            $oCompanyEquityPledge->fill($updateData);
            if (!$oCompanyEquityPledge->save())
            {
                throw new DevFixException("编辑失败");
            }
            $result = CompanyChange::saveChange(auth()->id(),$oCompanyEquityPledge->company_id,$oldCompanyEquityPledge,$updateData,"股权出质登记信息编辑",CompanyChange::EDIT_COMPANY,CompanyChange::MODULE_EQUITY_PLEDGE);
            if (!$result)
            {
                throw new DevFixException("公司变更信息保存失败");
            }
            DB::commit();
            return response()->json(['status' => 'success', 'message' => "编辑成功"]);
        } catch (\Exception $e) {
            DB::rollBack();
            $messages = $e->getMessage();
            return response()->json(['status' => 'failed', 'message' => $messages]);
        }

    }

    /**
     * 删除股权出质
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function pledgeDelete(Request $request)
    {
        $request              = $request->all();
        $oCompanyEquityPledge = CompanyEquityPledge::findOrFail($request['id']);

        $oldCompanyEquityPledge = $oCompanyEquityPledge->toArray();

        $updateData['status'] = CompanyEquityPledge::STATUS_DELETE;


        DB::beginTransaction();
        try
        {
            $oCompanyEquityPledge->fill($updateData);
            if (!$oCompanyEquityPledge->save())
            {
                throw new DevFixException('删除失败！');
            }
            $result = CompanyChange::saveChange(auth()->id(),$oCompanyEquityPledge->company_id,$oldCompanyEquityPledge,null,"股权出质登记信息删除",CompanyChange::DELETE_COMPANY,CompanyChange::MODULE_EQUITY_PLEDGE);
            if (!$result)
            {
                throw new DevFixException("公司变更信息保存失败");
            }
            DB::commit();
            return response()->json(['status' => 'success', 'messages' => '删除成功！']);
        } catch (\Exception $e) {
            $messages = $e->getMessage();
            return response()->json(['status' => 'failed', 'messages' => $messages]);
        }

    }

    /**
     * 修改公司信息流程申请
     * @param Request $request
     */
    public function applyForWorkflow(Request $request)
    {

        $applyData = [];

        $requestData = $request->all();
        $requestCompanyId      = isset($requestData['company_id']) ? $requestData['company_id'] : null;
        $requestCompanyInfo    = isset($requestData['company']) ? $requestData['company'] : [];

        $requestShareholder    = isset($requestData['shareholder']) ? $requestData['shareholder'] : [];
        $requestAddShareholder = isset($requestData['add_shareholder']) ? $requestData['add_shareholder'] : [];
        $requestDelShareholder = (isset($requestData['shareholder_del']) && $requestData['shareholder_del']) ? explode(',', substr($requestData['shareholder_del'],0,-1)) : [];

        $requestPersonnel      = isset($requestData['personnel']) ? $requestData['personnel'] : [];
        $requestAddPersonnel   = isset($requestData['add_personnel']) ? $requestData['add_personnel'] : [];
        $requestDelPersonnel   = (isset($requestData['personnel_del']) && $requestData['personnel_del']) ? explode(',', substr($requestData['personnel_del'],0,-1)) : [];

        $requestPledge         = isset($requestData['pledge']) ? $requestData['pledge'] : [];
        $requestAddPledge      = isset($requestData['add_pledge']) ? $requestData['add_pledge'] : [];
        $requestDelPledge      = (isset($requestData['pledge_del']) && $requestData['pledge_del']) ? explode(',', substr($requestData['pledge_del'],0,-1)) : [];

        //添加值为空处理
        $requestAddShareholder = Company::dataIsNull($requestAddShareholder);
        $requestAddPersonnel   = Company::dataIsNull($requestAddPersonnel);
        $requestAddPledge      = Company::dataIsNull($requestAddPledge);


        $originCompanyData = Company::findOrFail($requestCompanyId);

        if ($requestCompanyId)
        {

            //营业执照信息变动
            $companyChangeData = Company::compareCompany($requestCompanyId,$requestCompanyInfo);
            if ($companyChangeData)
            {
                $applyData['company_change']            = $companyChangeData;
                $applyData['company_change']['comment'] = "营业执照信息";
            }

            //股权信息变动
            $shareHolderChangeData = Company::compareShareHolder($requestCompanyId,$requestShareholder,$requestAddShareholder,$requestDelShareholder);
            if ($shareHolderChangeData)
            {
                $applyData['share_holder']            = $shareHolderChangeData;
                $applyData['share_holder']['comment'] = "股东及出资信息";
            }

            //主要人员信息变动
            $personnelChangeData = Company::comparePersonnel($requestCompanyId,$requestPersonnel,$requestAddPersonnel,$requestDelPersonnel);
            if ($personnelChangeData)
            {
                $applyData['personnel']            = $personnelChangeData;
                $applyData['personnel']['comment'] = "主要人员信息";

            }

            //股权出质信息变动
            $pledgeChangeData = Company::comparePledge($requestCompanyId,$requestPledge,$requestAddPledge,$requestDelPledge);
            if ($pledgeChangeData)
            {
                $applyData['pledge']            = $pledgeChangeData;
                $applyData['pledge']['comment'] = "股权出质登记信息";
            }
        }

        $tpl = ['company_change_info' => $applyData, 'company_id' => $requestCompanyId];
        return EntryController::storeBySystem($request, Entry::WORK_FLOW_NO_COMPANY_INFO, $originCompanyData->name.'企业信息修改', $tpl);
    }

}