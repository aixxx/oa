<?php
/**
 * 财务管理后台
 * Finance类
 * lee 2019-4-15
 */

namespace App\Http\Controllers\Finance;

use app\model\Department;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Response;
use Hprose\Http\Client;
use Illuminate\Pagination\LengthAwarePaginator;


class FinanceController extends Controller
{
    public $page_size = 30; //分页数量

    public function __construct()
    {
        //rpc调用
        $domain = config('app.rpc_local_domain').'/hprose/sys/start';
//        $domain = 'http://n.test/hprose/sys/start';
        $this->client = new Client($domain, false);
    }

    //分页类
    private function getShowPage(Request $request, $array_items)
    {
        // Get current page form url e.x. &page=1
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        // Create a new Laravel collection from the array data
        $itemCollection = collect($array_items);
        // Define how many items we want to be visible in each page
        $perPage = $this->page_size;
        // Slice the collection to get the items to display in current page
        $currentPageItems = $itemCollection->slice(($currentPage * $perPage) - $perPage, $perPage)->all();
        // Create our paginator and pass it to the view
        $paginatedItems = new LengthAwarePaginator($currentPageItems , count($itemCollection), $perPage);
        $paginatedItems->setPath($request->url());
        return $paginatedItems;
    }

    //会计科目
    public function accountSubject(Request $request)
    {
        $sofclass = $this->client->sofclassList();
        $sofclass = $this->getShowPage($request, $sofclass);
        $sofzz = $this->client->sofzzList();
        return view('finance.accountSubject.index',compact('sofclass','sofzz'));
    }

    //科目编辑显示
    public function accountSubjectEdit(Request $request)
    {
        $data = $request->all();
        $sofzz = $this->client->sofzzList();
        $one_sof = $this->client->getOneSof($data['sc_id']);
        return view('finance.accountSubject.create',compact('sofzz', 'one_sof'));
    }

    //科目添加
    public function accountSubjectAdd()
    {
        $sofzz = $this->client->sofzzList();
        return view('finance.accountSubject.create',compact('sofzz'));

    }

    //科目添加、编辑入库
    public function accountSubjectStore(Request $request)
    {
        $data = $request->all();

        if(isset($data['sc_id']) && $data['sc_id']) {
            $flag = $this->client->getOneSof($data['sc_id']);
            if(!$flag) {
                return Response::json(['code' => 201, 'message' => '会计科目数据出错']);
            }
            $sof_code = $this->client->getOneSofBySel($data['sc_id'], $data['code']);
            if($sof_code)
                return Response::json(['code' => 203, 'message' => '编号已经存在']);
            $sc_data = [
                'zid' => $data['zid'],
                'code' => $data['code'],
                'class_type' => $data['class_type'],
                'class_name' => $data['class_name'],
                'round_way' => $data['round_way'],
                'remark'    => $data['remark'],
                'status' => 1,
                'created_time' => time(),
            ];
            $this->client->saveSof($data['sc_id'], $sc_data);
            return Response::json(['code' => 200, 'message' => '编辑会计科目成功']);
        }
        if(!$data['zid'] || !$data['class_name'] || !$data['class_type'] ||  !$data['code'] || !$data['round_way']){
            return Response::json(['code' => 201, 'message' => '必填的请输入或选择']);
        }
        $sof_code = $this->client->getOneSofByCode($data['code']);
        if($sof_code)
            return Response::json(['code' => 203, 'message' => '编号已经存在']);

        $flag = $this->client->addSof($data['zid'], $data['code'],  $data['class_type'],$data['class_name'], $data['round_way'], $data['remark']);
        if(!$flag) {
            return Response::json(['code' => 202, 'message' => '保存会计科目失败']);
        }
        return Response::json(['code' => 200, 'message' => '创建会计科目成功']);

    }

    //科目搜索
    public function accountSubjectSearch(Request $request)
    {
        $data = $request->all();
        $sofzz = isset($data['sofzz']) ? $data['sofzz'] : '';
        $class_type = isset($data['class_type']) ? $data['class_type'] : '';
        $class_name = isset($data['class_name']) ? $data['class_name'] : '';
        $sofclass = $this->client->sofSearch($sofzz, $class_type, $class_name);
        $sofclass = $this->getShowPage($request,$sofclass);
        $sofclass = $sofclass->appends(['sofzz' => $sofzz, 'class_type' => $class_type, 'class_name' => $class_name]);
        $sofzz = $this->client->sofzzList();
        return view('finance.accountSubject.index',compact('sofclass','searchData', 'sofzz'));
    }

    //科目删除
    public function accountSubjectDel(Request $request)
    {
        $data = $request->all();

        $one_sof = $this->client->getOneSof($data['sc_id']);

        if(!$one_sof)
            return Response::json(['code' => 201, 'message' => '会计科目数据出错']);

        $this->client->saveSof($data['sc_id'], ['status' => 0]);
        return Response::json(['code' => 200, 'message' => '删除成功']);
    }


    //币种
    public function coin(Request $request)
    {
        $coinList = $this->client->coinList();
        $coinList = $this->getShowPage($request, $coinList);
        return view('finance.coin.index',compact('coinList'));

    }

    //币种搜索
    public function coinSearch(Request $request)
    {
        $data = $request->all();
        $coinList = $this->client->coinSearch(isset($data['coinName']) ? $data['coinName'] : '');
        $coinList = $this->getShowPage($request, $coinList);
        return view('finance.coin.index',compact('coinList'));
    }

    //币种删除
    public function coinDel(Request $request)
    {
        $data = $request->all();

        $one_coin= $this->client->getOneCoin($data['coin_id']);

        if(!$one_coin)
            return Response::json(['code' => 201, 'message' => '币种数据出错']);

        $this->client->saveCoin(['status' => 0],$data['coin_id']);
        return Response::json(['code' => 200, 'message' => '删除成功']);
    }

    //币种编辑显示
    public function coinEdit(Request $request)
    {
        $data = $request->all();
        $one_coin = $this->client->getOneCoin($data['coin_id']);
        return view('finance.coin.create', compact('one_coin'));
    }

    //币种添加
    public function coinAdd()
    {
        return view('finance.coin.create');
    }

    //币种添加、编辑入库
    public function coinStore(Request $request)
    {
        $data = $request->all();
        $coin_data = [
            'name' => $data['name'],
            'name_code' => $data['name_code'],
            'num_code' => $data['num_code'],
            'format' => $data['format'],
            'area' => $data['area'],
            'status' => 1,
            'created_time' => time(),
        ];
        if(!$data['name'] || !$data['name_code'] || !$data['num_code'] ||  !$data['format'] || !$data['area']){
            return Response::json(['code' => 201, 'message' => '全是必填项哦']);
        }
        if(isset($data['coin_id']) && $data['coin_id']) {
            $flag = $this->client->getOneCoin($data['coin_id']);
            if(!$flag) {
                return Response::json(['code' => 201, 'message' => '币种数据出错']);
            }
            $this->client->saveCoin($coin_data, $data['coin_id']);
            return Response::json(['code' => 200, 'message' => '编辑币种成功']);
        }
        $flag = $this->client->saveCoin($coin_data);
        if(!$flag) {
            return Response::json(['code' => 202, 'message' => '保存币种失败']);
        }
        return Response::json(['code' => 200, 'message' => '创建币种成功']);

    }

    //预算模板
    public function costBudget(Request $request)
    {
        $cost_budget = $this->client->costBudgetList();
//        if (!empty($cost_budget)) {
//            foreach ($cost_budget as &$_budget) {
//                $dep = \App\Models\Department::where(['id'=> $_budget['cost_organ_id']])->first();
//                $_budget['organ_name'] = $dep ? $dep->name : '';
//            }
//        }
        $cost_budget = $this->getShowPage($request, $cost_budget);
        return view('finance.budget.index',compact('cost_budget'));
    }

    //预算编辑(列出预算项)
    public function budgetSetting(Request $request)
    {
        $data = $request->all();
        //获取预算详情
        $one_budget = $this->client->getOneCostBudget($data['id']);
//        if(!empty($one_budget)){
//            $dep = \App\Models\Department::where(['id'=> $one_budget['cost_organ_id']])->first();
//            $one_budget['organ_name'] = $dep ? $dep->name : '';
//        }
        //获取类目表
        $categories = $this->client->categoryList();
        $save = [];
        if(!empty($categories)) {
            //初始化类目
            foreach ($categories as $_category) {
                $one_category_item = $this->client->getOneCategoryItem($data['id'], $_category['id']);
                if(empty($one_category_item)){
                    $save[] = [
                        'cost_organ_id' => $one_budget['cost_organ_id'],
                        'cost_budget_id' => $data['id'],
                        'cost_project_id' => $one_budget['cost_project_id'],
                        'cost_department_id' => $one_budget['cost_department_id'],
                        'company_id' => $one_budget['company_id'],
                        'category_id' => $_category['id'],
                        'is_lock' => $_category['is_lock'],
                        'is_over' => $_category['is_over'],
                    ];
                }
            }
            if(!empty($save))
                $this->client->saveAllItem($save);
        }
        //预算类目列表
        $category = $this->client->categoryItem($data['id']);
        $cost_budget_id = $data['id'];
        return view('finance.budget.budgetlist',compact('cost_budget_id', 'one_budget', 'category'));
    }

    //费控预算类目添加
//    public function budgetAdd(Request $request)
//    {
//        $data = $request->all();
//        $cost_budget_id = $data['id'];
//        $one_budget = $this->client->getOneCostBudget($data['id']);
//        return view('finance.budget.create',compact('cost_budget_id','one_budget'));
//    }

    //费控预算类目修改
//    public function budgetEdit(Request $request)
//    {
//        $data = $request->all();
//        $budget_item = $this->client->getOneBudgetItem($data['id']);
//        $cost_budget_id = $budget_item['cost_budget_id'];
//        $one_budget = $this->client->getOneCostBudget($cost_budget_id);
//        return view('finance.budget.create',compact('budget_item','cost_budget_id', 'one_budget'));
//    }

    //费控预算修改（是否锁死、是否超支）
    public function budgetChange(Request $request)
    {
        $data = $request->all();
        $budget = [];
        if(isset($data['is_lock'])) {
            $budget['is_lock'] = $data['is_lock'];
        }
        if(isset($data['is_over'])) {
            $budget['is_over'] = $data['is_over'];
        }
        $flag = $this->client->budgetStore($budget, $data['id']);
        if(!$flag)
            return Response::json(['code' => 202, 'message' => '保存失败']);

        return Response::json(['code' => 200, 'message' => '保存成功']);
    }

    //费控预算类目修改（是否锁死、是否超支）
    public function budgetItemChange(Request $request)
    {
        $data = $request->all();
        $budget = [];
        if(isset($data['is_lock'])) {
            $budget['is_lock'] = $data['is_lock'];
        }
        if(isset($data['is_over'])) {
            $budget['is_over'] = $data['is_over'];
        }
        $flag = $this->client->budgetItemStore($budget, $data['id']);
        if(!$flag)
            return Response::json(['code' => 202, 'message' => '保存失败']);

        return Response::json(['code' => 200, 'message' => '保存成功']);
    }


    //费控预算类目保存
    public function budgetStore(Request $request)
    {
        $data = $request->all();
        $budget_data = [
            'cost_budget_id' => $data['cost_budget_id'],
            'title' => $data['title'],
            'is_lock' => $data['is_lock'],
            'is_over' => $data['is_over'],
            'amount' => $data['amount'],
            'status' => 1,
//            'personnel_count' => $data['personnel_count'],
        ];
        $cost_budget = $this->client->getOneCostBudget($data['cost_budget_id']);
        if(!$cost_budget) {
            return Response::json(['code' => 201, 'message' => '费控预算类目数据出错']);
        }

        $budget_data['cost_organ_id'] = $cost_budget['cost_organ_id'];
        $budget_data['cost_project_id'] = $cost_budget['cost_project_id'];
        $budget_data['cost_department_id'] = $cost_budget['cost_department_id'];
        $budget_data['company_id'] = $cost_budget['company_id'];

        if(!$data['title'] || !$data['amount']){
            return Response::json(['code' => 202, 'message' => '全是必填项哦']);
        }
        if(isset($data['id']) && $data['id']) {
            $this->client->saveBudget($budget_data, $data['id']);
            return Response::json(['code' => 200, 'message' => '编辑费控预算类目成功']);
        }
        $flag = $this->client->saveBudget($budget_data);
        if(!$flag) {
            return Response::json(['code' => 203, 'message' => '保存费控预算类目失败']);
        }

        //判断是否初始化预算项条件
        $one_budget_item =  $this->client->getOneBudgetItemCondition($flag);
        if(!$one_budget_item) {
            //餐饮费的默认*********先写死**********后面其他费用根据需求来变化
            $_data = [
                [
                    'cost_budget_item_id' => $flag,
                    'condition_name' => '职位'
                ],
                [
                    'cost_budget_item_id' => $flag,
                    'condition_name' => '算法'
                ],
                [
                    'cost_budget_item_id' => $flag,
                    'condition_name' => '金额'
                ],
                [
                    'cost_budget_item_id' => $flag,
                    'condition_name' => '我方人数'
                ],
                [
                    'cost_budget_item_id' => $flag,
                    'condition_name' => '对方人数'
                ],
            ];

            $flag1 = $this->client->saveAllBudgetItemCondition($_data);
            if(!$flag1) {
                return Response::json(['code' => 203, 'message' => '保存费控预算类目失败']);
            }
        }
        return Response::json(['code' => 200, 'message' => '创建费控预算类目成功']);
    }

    //费控预算类目条件设置
    public function budgetCondition(Request $request)
    {
        $position = User::select('id', 'position')->groupBy('position')->get()->toArray();
        $data = $request->all();
        $budget_item = $this->client->getOneBudgetItem($data['id']);
        if(!$budget_item)   abort(404, 'NOT FOUND');
        $budget_item_condition = $this->client->getOneBudgetItemCondition($data['id']);
        $settings = $this->client->getBudgetItemConditionSetting($data['id']);
        if($settings) {
            $settings['settings'] = array_chunk(unserialize($settings['settings']),3);
        }
        return view('finance.budget.condition',compact('budget_item','budget_item_condition','position', 'settings'));
    }

    //费控预算类目条件添加
    public function budgetConditionAdd(Request $request)
    {
        $data = $request->all();
        if(!$data['condition_name']){
            return Response::json(['code' => 201, 'message' => '类目条件不能为空哦']);
        }
        $condition = [
            'cost_budget_item_id' => $data['cost_budget_item_id'],
            'condition_name' => $data['condition_name'],
        ];
        $flag = $this->client->saveBudgetItemCondition($condition);
        if(!$flag) {
            return Response::json(['code' => 202, 'message' => '保存费控预算类目条件失败']);
        }
        return Response::json(['code' => 200, 'message' => '创建费控预算类目条件成功']);
    }

    //费控预算类目条件设置保存
    public function budgetConditionSetting(Request $request)
    {
        $data = $request->all();
        $settings = [];
        $condition_type = array_chunk($data['condition_type'], 3);
        $condition_name = array_chunk($data['condition_name'], 3);
        $condition_setting = array_chunk( $data['condition_setting'], 3);

        foreach ($condition_type as $_k => $_v) {
            foreach ($_v as $__k => $__v) {
                $settings[] = [
                    'cost_budget_item_condition_id' => $__v,
                    'cost_budget_item_condition_name' => $condition_setting[$_k][$__k],
                    'title' => $condition_name[$_k][$__k].":".$condition_setting[$_k][$__k],
                    'field' => $condition_name[$_k][$__k],
                ];
            }
        }
        //字段settings 序列化保存
        $data_settings = [
            'cost_budget_item_id' => $data['cost_budget_item_id'],
            'settings' => serialize($settings),
        ];

        if(isset($data['id']) && $data['id']) {
            $this->client->saveBudgetItemConditionSetting($data_settings, $data['id']);
            return Response::json(['code' => 200, 'message' => '编辑费控类目条件成功']);
        }

        $flag = $this->client->saveBudgetItemConditionSetting($data_settings);
        if(!$flag) {
            return Response::json(['code' => 202, 'message' => '费控类目条件设定失败']);
        }
        return Response::json(['code' => 200, 'message' => '费控类目条件设定成功']);
    }

    //删除类目项
    public function budgetDel(Request $request)
    {
        $data = $request->all();

        $one_budget = $this->client->getOneBudgetItem($data['id']);

        if(!$one_budget)
            return Response::json(['code' => 201, 'message' => '凭证模板数据出错']);

        $this->client->delBudget($data['id']);

        return Response::json(['code' => 200, 'message' => '删除成功']);
    }

    //凭证模板
    public function voucher(Request $request)
    {
        $data = $request->all();
        $voucher_list = $this->client->voucherList(isset($data['sof_id']) ? $data['sof_id'] : 0);
        $sofinfo = $this->client->sofinfoList();
        $voucher_list = $this->getShowPage($request, $voucher_list);
        return view('finance.voucher.index',compact('voucher_list','sofinfo'));
    }

    //凭证模板添加
    public function voucherAdd()
    {
        $sofinfo = $this->client->sofinfoList();
        return view('finance.voucher.create',compact('sofinfo'));
    }

    //凭证模板添加
    public function voucherEdit(Request $request)
    {
        $data = $request->all();
        $voucher = $this->client->getOneVoucher($data['id']);
        $sofinfo = $this->client->sofinfoList();
        return view('finance.voucher.create',compact('voucher','sofinfo'));
    }

    //凭证模板保存
    public function voucherStore(Request $request)
    {
        $data = $request->all();
        $voucher_data = [
            'sof_id' => $data['sof_id'],
            'title' => $data['title'],
            'sofpzz_id' => $data['type'],
            'created_time' => time(),
        ];
        if(!$data['title'] || !$data['title'] || !$data['type']){
            return Response::json(['code' => 202, 'message' => '全是必填项哦']);
        }
        if(isset($data['id']) && $data['id']) {
            $this->client->saveVoucherTemplate($voucher_data, $data['id']);
            return Response::json(['code' => 200, 'message' => '编辑凭证模板成功']);
        }
        $flag = $this->client->saveVoucherTemplate($voucher_data);
        if(!$flag) {
            return Response::json(['code' => 203, 'message' => '保存凭证模板失败']);
        }
        return Response::json(['code' => 200, 'message' => '保存凭证模板成功']);
    }

    //凭证模板设置
    public function voucherSetting(Request $request)
    {
        $data = $request->all();
        $voucher_item = $this->client->getVoucherItem($data['id']);
        $voucher = $this->client->getOneVoucher($data['id']);
        return view('finance.voucher.setting',compact('voucher_item','voucher'));
    }

    //获取模板凭证记收付转
    public function voucherSofpzz(Request $request)
    {
        $data = $request->all();
        $sofpzz =  $this->client->voucherSofpzz($data['sof_id']);
//        $voucher_item = $this->client->getVoucherItem($data['id']);
//        $voucher = $this->client->getOneVoucher($data['id']);
        $sofpzz_id = isset($data['sofpzz_id']) ? $data['sofpzz_id'] : '';
        return view('finance.voucher.sofpzz',compact('sofpzz','sofpzz_id'));
    }

    //凭证项目添加
    public function voucherItemAdd(Request $request)
    {
        $data = $request->all();
        $voucher = $this->client->getOneVoucher($data['v_t_id']);
        $voucher_template_id = $data['v_t_id'];
        $sofusClass =  $this->client->getSofusClassList($voucher['sof_id']);
        return view('finance.voucher.itemAdd',compact('voucher_template_id','voucher', 'sofusClass'));
    }

    //凭证项目编辑
    public function voucherItemEdit(Request $request)
    {
        $data = $request->all();
        $voucher_template_id = $data['id'];
        $voucher_item = $this->client->getOneVoucherItem($data['id']);
        if(!$voucher_item)   abort(404, 'NOT FOUND');
        $voucher = $this->client->getOneVoucher($voucher_item['voucher_template_id']);
        $sofusClass =  $this->client->getSofusClassList($voucher['sof_id']);
        return view('finance.voucher.itemAdd',compact('voucher_template_id','voucher_item','voucher', 'sofusClass'));
    }


    //凭证项目保存
    public function voucherItemStore(Request $request)
    {
        $data = $request->all();
        $voucher_data = [
            'title' => $data['title'],
            'sofusclass_id' => $data['sofusclass_id'],
            'round_way' => $data['round_way'],
            'voucher_template_id' => $data['voucher_template_id'],
            'created_time' => time(),
        ];
        if(!$data['title'] || !$data['sofusclass_id'] || !$data['round_way']){
            return Response::json(['code' => 202, 'message' => '全是必填项哦']);
        }
        if(isset($data['id']) && $data['id']) {
            $this->client->saveVoucherItem($voucher_data, $data['id']);
            return Response::json(['code' => 200, 'message' => '编辑凭证项成功']);
        }
        $flag = $this->client->saveVoucherItem($voucher_data);
        if(!$flag) {
            return Response::json(['code' => 203, 'message' => '保存凭证项失败']);
        }
        return Response::json(['code' => 200, 'message' => '保存凭证项成功']);
    }

    //凭证模板删除
    public function voucherDel(Request $request)
    {
        $data = $request->all();

        $one_voucher = $this->client->getOneVoucher($data['id']);

        if(!$one_voucher)
            return Response::json(['code' => 201, 'message' => '凭证模板数据出错']);

        $this->client->saveVoucherTemplate(['status' => 0],$data['id']);

        return Response::json(['code' => 200, 'message' => '删除成功']);
    }

    //凭证模板项目删除
    public function voucherItemDel(Request $request)
    {
        $data = $request->all();

        $one_voucher = $this->client->getOneVoucherItem($data['id']);

        if(!$one_voucher)
            return Response::json(['code' => 201, 'message' => '凭证模板项数据出错']);

        $this->client->delVoucherItem($data['id']);

        return Response::json(['code' => 200, 'message' => '删除成功']);
    }

    //结算类型
    public function balance()
    {
        $balance_list = $this->client->balanceList();
        return view('finance.balance.index',compact('balance_list'));
    }

    //结算类型添加
    public function balanceAdd()
    {
        return view('finance.balance.create');
    }

    //结算类型添加
    public function balanceEdit(Request $request)
    {
        $data = $request->all();
        $balance = $this->client->getOneBalance($data['id']);
        return view('finance.balance.create',compact('balance'));
    }

    //结算类型删除
    public function balanceDel(Request $request)
    {
        $data = $request->all();

        $balance = $this->client->getOneBalance($data['id']);

        if(!$balance)
            return Response::json(['code' => 201, 'message' => '结算类型数据出错']);

        $this->client->saveBalanceType(['status' => 0],$data['id']);

        return Response::json(['code' => 200, 'message' => '删除成功']);
    }

    //凭证模板保存
    public function balanceStore(Request $request)
    {
        $data = $request->all();
        $balance_data = [
            'balance_name' => $data['balance_name'],
        ];
        if(!$data['balance_name']){
            return Response::json(['code' => 201, 'message' => '全是必填项哦']);
        }
        if(isset($data['id']) && $data['id']) {
            $this->client->saveBalanceType($balance_data, $data['id']);
            return Response::json(['code' => 200, 'message' => '编辑结算类型成功']);
        }
        $flag = $this->client->saveBalanceType($balance_data);
        if(!$flag) {
            return Response::json(['code' => 203, 'message' => '保存结算类型失败']);
        }
        return Response::json(['code' => 200, 'message' => '保存结算类型成功']);
    }


    //会计准则
    public function sofzz()
    {
        $sofzz_list = $this->client->sofzzList();
        return view('finance.sofzz.index',compact('sofzz_list'));
    }

    //会计准则添加
    public function sofzzAdd()
    {
        return view('finance.sofzz.create');
    }

    //会计准则编辑
    public function sofzzEdit(Request $request)
    {
        $data = $request->all();
        $sofzz = $this->client->getOneSofzz($data['id']);
        return view('finance.sofzz.create',compact('sofzz'));
    }

    //会计准则保存
    public function sofzzStore(Request $request)
    {
        $data = $request->all();
        $sofzz_data = [
            'name' => $data['name'],
//            'start_time' => time(),
            'created_time' => time()
        ];
        if(!$data['name']){
            return Response::json(['code' => 201, 'message' => '全是必填项哦']);
        }
        if(isset($data['id']) && $data['id']) {
            //编辑不修改start_time
            $this->client->saveSofzz($sofzz_data, $data['id']);
            return Response::json(['code' => 200, 'message' => '编辑会计准则成功']);
        }

        $sofzz_data['start_time'] = time();
        $flag = $this->client->saveSofzz($sofzz_data);
        if(!$flag) {
            return Response::json(['code' => 203, 'message' => '保存会计准则失败']);
        }
        return Response::json(['code' => 200, 'message' => '保存会计准则成功']);
    }

    //会计准则删除
    public function sofzzDel(Request $request)
    {
        $data = $request->all();

        $sofzz = $this->client->getOneSofzz($data['id']);

        if(!$sofzz)
            return Response::json(['code' => 201, 'message' => '会计准则数据出错']);

        $one_sofclass = $this->client->getOneSofclass($data['id']);
        if($one_sofclass)
            return Response::json(['code' => 202, 'message' => '会计准则下有科目，不能删除']);

        $this->client->saveSofzz(['status' => 0],$data['id']);

        return Response::json(['code' => 200, 'message' => '删除成功']);
    }


    //费控维度表
    public function budgetDimension(Request $request)
    {
        $dimension = $this->client->dimensionList();

        return view('finance.budget.dimension', compact('dimension'));
    }

    //费控维度表编辑
    public function budgetDimensionEdit(Request $request)
    {
        $data = $request->all();
        $dimension = $this->client->getOneDimension($data['id']);
        $condition = $this->client->getAllCondition($data['id']);

        return view('finance.budget.dimensionAdd', compact('dimension', 'condition'));
    }

    //费控维度表添加
    public function budgetDimensionAdd(Request $request)
    {
        return view('finance.budget.dimensionAdd');
    }

    //费控维度表保存
    public function budgetDimensionStore(Request $request)
    {
        $data = $request->all();
        if(!$data['title'])
            return Response::json(['code' => 201, 'message' => '标题是必填']);
        //判断标题是否存在
        $is_title = $this->client->getOneDimensionBytitle($data['title'], $data['id'] ? $data['id'] : 0 );
        if($is_title)
            return Response::json(['code' => 204, 'message' => '标题已经存在，请重新输入']);
        $titles = [];
        //判断选项
        foreach ($data['condition_title'] as $_title) {
            $titles[] = $_title['title'];
        }
        //判断选项是否存在
        if(count($titles) < 1)
            return Response::json(['code' => 202, 'message' => '选项至少填写1个']);
        //判断费控名是否重复
        if (count($titles) != count(array_unique($titles)))
            return Response::json(['code' => 2039, 'message' => '选项名称重名，请重新填写']);

        $flag = $this->client->saveDimension($data);
        if(!$flag) {
            return Response::json(['code' => 203, 'message' => '保存失败，请稍后再试']);
        }
        return Response::json(['code' => 200, 'message' => '保存成功']);

    }

    //费控维度表删除
    public function budgetDimensionDel(Request $request)
    {
        $data = $request->all();

        $dimension = $this->client->getOneDimension($data['id']);

        if(!$dimension)
            return Response::json(['code' => 201, 'message' => '维度数据出错']);

        $settings = $this->client->getAllConditionSetting();
        if(!empty($settings)){
            foreach ($settings as $_setting) {
                foreach (unserialize($_setting['settings']) as $_item) {
                    if($_item['id'] == $data['id']) {
                        return Response::json(['code' => 2011, 'message' => '不能删除，该维度已经在使用中']);
                    }
                }
            }
        }
        $this->client->delDimension($data['id']);
        return Response::json(['code' => 200, 'message' => '删除成功']);

    }

    //费控维度表选择判断
    public function isBudgetDimensionCondition(Request $request)
    {
        $data = $request->all();
        if(!$data['id'])
            return Response::json(['code' => 200, 'message' => '可以删除']);
        $settings = $this->client->getAllConditionSetting();
        if(!empty($settings)){
            foreach ($settings as $_setting) {
                foreach (unserialize($_setting['settings']) as $_item) {
                    foreach ($_item['condition'] as $__item) {
                        if($__item['condition_id'] == $data['id'])
                            return Response::json(['code' => 2011, 'message' => '不能删除，该选项已经在使用中']);
                    }
                }
            }
        }
        return Response::json(['code' => 200, 'message' => '可以删除']);
    }

    //费控模板类目列表
    public function budgetCategory(Request $request)
    {
        $type=$request->input('type',0);//dd( $type);
        $category = $this->client->categoryFromFinanceList($type);
        foreach ($category as &$val) {
            $val['title'] = $val['name'];
            $val['is_seted_title'] = $val['is_seted']?'已设置':'未设置';
        }
        unset($val);
        return view('finance.budget.category', compact('category'));
    }
////    备份费控模板类目列表
//    public function budgetCategory(Request $request)
//    {
//        $category = $this->client->categoryList();
//        print_r($category);
//        return view('finance.budget.category', compact('category'));
//    }

    //费控模板类目添加
    public function budgetCategoryAdd(Request $request)
    {
        return view('finance.budget.categoryAdd');
    }

//    // 备份费控模板类目编辑
//    public function budgetCategoryEdit(Request $request)
//    {
//        $data = $request->all();
//        $category = $this->client->getOneCategory($data['id']);
//        return view('finance.budget.categoryAdd', compact('category'));
//    }

    //费控模板类目编辑
    public function budgetCategoryEdit(Request $request)
    {
        $data = $request->all();
        $category = $this->client->getOneCategory($data['id']);

        return view('finance.budget.categoryAdd', compact('category'));
    }
    //费控模板类目保存
    public function budgetCategoryStore(Request $request)
    {
        $data = $request->all();
        $category_data = [
            'title' => $data['title'],
            'explain' => $data['explain'],
            'is_over' => $data['is_over'],
            'is_lock' => $data['is_lock'],
            'created_time' => time()
        ];
        if(!$data['title']){
            return Response::json(['code' => 201, 'message' => '类目名称是必填项哦']);
        }
        //判断类目是否重复
        $is_title = $this->client->getOneCategoryBytitle($data['title'], $data['id']);
        if($is_title)
            return Response::json(['code' => 204, 'message' => '标题已经存在，请重新输入']);
        if(isset($data['id']) && $data['id']) {
            $this->client->saveCategory($category_data, $data['id']);
            return Response::json(['code' => 200, 'message' => '编辑费控类目成功']);
        }

        $flag = $this->client->saveCategory($category_data);
        if(!$flag) {
            return Response::json(['code' => 203, 'message' => '保存费控类目失败']);
        }

        return Response::json(['code' => 200, 'message' => '保存费控类目成功']);

    }

    //费控模板类目停用
    public function budgetCategoryDel(Request $request)
    {
        $data = $request->all();

        $category = $this->client->getOneCategory($data['id']);

        if(!$category)
            return Response::json(['code' => 201, 'message' => '类目数据出错']);
//        $one_item = $this->client->getOneCategoryItem(0, $data['id']);
//        if(!empty($one_item))
//            return Response::json(['code' => 20121, 'message' => '不能删除，该费控类目已经在使用中']);

        $flag = $this->client->delCategory($data['id']);

        if(!$flag) {
            return Response::json(['code' => 203, 'message' => '删除费控类目失败']);
        }
        return Response::json(['code' => 200, 'message' => '删除成功']);

    }

    //设置费控点
    public function budgetPoint(Request $request)
    {
        $data = $request->all();
        $category = $this->client->getOneCategorySelf($data['id']);
        //维度列表
        $dimension = $this->client->dimensionList();
        //条件
        $budget_item = $this->client->getBudgetItemConditionSetting($data['id']);
        $items = [];
        if($budget_item) {
            foreach ($budget_item as &$_item) {
                $_item['settings'] = unserialize($_item['settings']);
                foreach ($_item['settings'] as &$__item) {
                    $items[$__item['id']] = $__item['id'];
                    $__item['categories'] = $this->client->getConditionByDesId($__item['id']);
                    foreach ($__item['condition'] as $_condition) {
                        $__item['category_ids'][] = $_condition['condition_id'];
                    }
                }
            }
            $items = array_values($items);
        }
//        dd($budget_item);
        return view('finance.budget.budgetPoint', compact('dimension', 'category', 'budget_item', 'items'));
    }

    //根据维度ids获取条件
    public function getDesCondition(Request $request)
    {
        $data = $request->all();
        if(!$data['des_id'])
            return Response::json(['code' => 200, 'data' => []]);
        $dimension = $this->client->getConditionByDesId($data['des_id']);
        return Response::json(['code' => 200, 'data' => $dimension]);

    }

    //条件保存
    public function desConditionSave(Request $request)
    {
        $data = $request->all();
        if(!$data['id'])
            return Response::json(['code' => 2031, 'message' => '数据出错']);
        //如果是更新，对比下id是否存在
        if(isset($data['condition_ids']) && $data['condition_ids']) {
            foreach ($data['condition_ids'] as $_condition) {
                if($_condition){
                    $getSetting = $this->client->getConditionSettingById($_condition);
                    if(empty($getSetting)){
                        return Response::json(['code' => 2033, 'message' => '数据出错']);
                    }
                }
            }
        }

        $settings = [];
        //有数据，是更新的操作，把id放入数组
        $condition_settings = $this->client->getBudgetItemConditionSetting($data['id']);
        if(!empty($condition_settings)) {
            foreach ($condition_settings as $_setting) {
                $settings[] = $_setting['id'];
            }
        }
        //判断费控名称
        foreach ($data['point_name'] as $_name) {
            if(!$_name)
                return Response::json(['code' => 2032, 'message' => '请填写费控名称']);
        }

        //判断费控名是否重复
        if (count($data['point_name']) != count(array_unique($data['point_name'])))
            return Response::json(['code' => 2039, 'message' => '费控名称重名，请重新填写']);

        //判断限制金额
        foreach ($data['limit_price'] as $_price) {
            if(!$_price)
                return Response::json(['code' => 2022, 'message' => '请填写限制单价']);
            if(!preg_match("/^[1-9][0-9]*$/",$_price))
                return Response::json(['code' => 2112, 'message' => '限制单价是正整数']);
        }

        //判断条件
        foreach ($data['condition'] as $_k => $_condition) {
            foreach ($_condition as $__condition) {
                if(!isset($__condition['condition'])){
                    return Response::json(['code' => 2042, 'message' => '请选择条件']);
                }
            }
            $save = [
                'category_id' => $data['id'],
                'point_name' => $data['point_name'][$_k],
                'limit_price' => $data['limit_price'][$_k],
                'is_control' => $data['is_control'][$_k],
                'settings' => serialize($_condition)
            ];
            $id = 0;
            if(isset($data['condition_ids']) && $data['condition_ids']) {
                $id = $data['condition_ids'][$_k];
            }

            $setting_id = $this->client->saveBudgetItemConditionSetting($save, $id);

            $show_id = $id ? $id : $setting_id;
            if($show_id) {
                $show_info = $this->client->getBudgetConditionShowById($show_id);
                if(!empty($show_info)){
                    $this->client->delBudgetConditionShow($show_id);
                }
            }
            $show = [];
            foreach ($_condition as $_val) {
                foreach ($_val['condition'] as $__val) {
                    $show[] = [
                        'setting_id' => $show_id,
                        'des_id' => $_val['id'],
                        'des_name' => $_val['title'],
                        'con_id' => $__val['condition_id'],
                        'con_name' => $__val['condition_title'],
                    ];
                }
            }
            $this->client->saveBudgetConditionShow($show);
        }
        //更新的话，如果没有的id被删了，就把它删除
        if(!empty($settings)) {
            foreach ($settings as $_setting) {
                if(!in_array($_setting, $data['condition_ids'])) {
                    $this->client->delBudgetItemConditionSetting($_setting);
                }
            }
        }

        return Response::json(['code' => 200, 'message' => '保存成功']);
    }

}
