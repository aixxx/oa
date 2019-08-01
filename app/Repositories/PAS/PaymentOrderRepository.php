<?php

namespace App\Repositories\PAS;
use App\Models\PAS\Purchase\PaymentOrder;
use App\Models\PAS\Purchase\PaymentOrderContent;
use App\Models\PAS\Purchase\PurchasePayableMoney;
use App\Models\PAS\Purchase\ReturnOrder;
use App\Models\PAS\Purchase\Supplier;
use App\Models\Workflow\Entry;
use App\Repositories\ParentRepository;
use App\Repositories\RpcRepository;
use App\Services\Workflow\FlowCustomize;
use Illuminate\Http\Request;
use App\Models\PAS\Purchase\Purchase;
use App\Models\Workflow\Proc;
use App\Models\Workflow\Workflow;
use App\Models\OperateLog;
use App\Constant\ConstFile;
use Carbon\Carbon;
use App\Models\User;
use Exception;
use Auth;
use DB;


/**
 * Class UsersRepositoryEloquent.
 *付款单
 * @package namespace App\Repositories;
 */
class PaymentOrderRepository extends ParentRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Purchase::class;
    }

    /*2019-05-08
     * 付款单编号
     */
    public function getCode()
    {
        $codes = $this->getCodes('FK');
        return returnJson($message = 'OK', $code = ConstFile::API_RESPONSE_SUCCESS, $codes);
    }

    /*2019-05-13
    * 添加付款单
    * type 1是添添加付款单  其他表示 保存草稿
    */
    public function setAdd($user, $arr)
    {
        $user_id = $user->id;
        $user_name = $user->chinese_name;
        if (empty($arr['type'])) {
            return returnJson($message = '类型不能为空！', $code = ConstFile::API_RESPONSE_FAIL);
        }
        $type = intval($arr['type']);
        if (empty($arr['code'])) {
            return returnJson($message = '付款单号不能为空！', $code = ConstFile::API_RESPONSE_FAIL);
        }
        $PaymentOrder = PaymentOrder::where('code', trim($arr['code']))->first(['id']);
        if ($PaymentOrder) {
            return returnJson($message = '付款单号不能重复！', $code = ConstFile::API_RESPONSE_FAIL);
        }
        if (empty($arr['p_code'])) {
            return returnJson($message = '采购单号不能为空！', $code = ConstFile::API_RESPONSE_FAIL);
        }
        $Purchase = Purchase::where('code', trim($arr['p_code']))->where('status', 5)->first(['id', 'discount', 'p_status']);
        if (!$Purchase) {
            return returnJson($message = '没有审核通过的采购单号不能申请付款！', $code = ConstFile::API_RESPONSE_FAIL);
        }
        if ($Purchase->p_status > 0) {
            return returnJson($message = '采购单有付款申请！', $code = ConstFile::API_RESPONSE_FAIL);
        }

        if (empty($arr['day'])) {
            return returnJson($message = '业务日期不能为空！', $code = ConstFile::API_RESPONSE_FAIL);
        }
        if (empty($arr['supplier_id'])) {
            return returnJson($message = '供应商不能为空！', $code = ConstFile::API_RESPONSE_FAIL);
        }
        if (empty($arr['apply_id'])) {
            return returnJson($message = '经手人不能为空！', $code = ConstFile::API_RESPONSE_FAIL);
        }

        if (!empty($arr['remark']) && isset($arr['remark'])) {//备注
            $data['remarks'] = trim($arr['remark']);
        }
        if (intval($arr['payable_money'] == 0)) {//此前应付钱
            $data['payable_money'] = 0;
        } else {
            if (empty($arr['payable_money'])) {
                return returnJson($message = '此前应付不能为空！', $code = ConstFile::API_RESPONSE_FAIL);
            }
            $data['payable_money'] = intval($arr['payable_money']);
        }

        $data['user_id'] = $user_id;
        $data['code'] = trim($arr['code']);
        $data['p_id'] = $Purchase->id;
        $data['p_code'] = trim($arr['p_code']);
        $data['business_date'] = trim($arr['day']);
        $data['supplier_id'] = intval($arr['supplier_id']);
        $infoArr=app()->make(RpcRepository::class)->getCustomerById(intval($arr['supplier_id']));
        $supplierTitle = $infoArr['cusname'];
        if (!$supplierTitle) {
            return returnJson($message = '供应商数据错误！', $code = ConstFile::API_RESPONSE_FAIL);
        }
        $data['supplier_name'] = $supplierTitle;
        $data['apply_id'] = intval($arr['apply_id']);
        $data['money'] = floatval($arr['money']);
        $chinese_name = User::where('id', $data['apply_id'])->value('chinese_name');
        if (!$chinese_name) {
            return returnJson($message = '经手人数据错误！', $code = ConstFile::API_RESPONSE_FAIL);
        }
        //dd($arr['cost']);
        $data['apply_name'] = $chinese_name;
        $data['created_at'] = date('Y-m-d H:i:s', time());
        $data['updated_at'] = date('Y-m-d H:i:s', time());
        try {
            DB::transaction(function () use ($data, $user, $type, $arr) {
                $data['status'] = PaymentOrder::STATUS_DRAFT;//草稿
                if ($type == 1) {//直接添加数据
                    $data['status'] = PaymentOrder::STATUS_REVIEW;//
                    $dataOne['title'] = '付款申请单号为' . trim($data['code']);
                    $entry = FlowCustomize::EntryFlow($dataOne, 'pas_payment_order');//添加进销存采购申请单 审核流程
                    $data['entrise_id'] = $entry->id;

                    $where['code'] = $data['p_code'];
                    $datas['p_status'] = 1;//付款完成
                    Purchase::where($where)->where('status', '=', Purchase::API_STATUS_SUCCESS)->update($datas);
                }
                $n = PaymentOrder::insertGetId($data);
            });
            if ($type == 1) {
                return returnJson($message = '提交成功', $code = ConstFile::API_RESPONSE_SUCCESS);
            }
            return returnJson($message = '草稿保存成功', $code = ConstFile::API_RESPONSE_SUCCESS);
        } catch (Exception $e) {
            return returnJson($e->getMessage(), $code = ConstFile::API_RESPONSE_FAIL);
        }
    }

    /*2019-05-13
    * 付款单修改
    * type 1是添加退货单  其他表示 保存草稿
    */
    public function setUpdate($user, $arr)
    {
        $user_id = $user->id;
        $user_name = $user->chinese_name;
        if (empty($arr['id'])) {
            return returnJson($message = '付款单号id不能为空！', $code = ConstFile::API_RESPONSE_FAIL);
        }
        $id = intval($arr['id']);

        if (empty($arr['type'])) {
            return returnJson($message = '类型不能为空！', $code = ConstFile::API_RESPONSE_FAIL);
        }
        $type = intval($arr['type']);
        if (empty($arr['code'])) {
            return returnJson($message = '付款单号不能为空！', $code = ConstFile::API_RESPONSE_FAIL);
        }
        $PaymentOrder = PaymentOrder::where('code', trim($arr['code']))->where('id', '!=', intval($arr['id']))->first(['id']);
        if ($PaymentOrder) {
            return returnJson($message = '付款单号不能重复！', $code = ConstFile::API_RESPONSE_FAIL);
        }
        if (empty($arr['p_code'])) {
            return returnJson($message = '采购单号不能为空！', $code = ConstFile::API_RESPONSE_FAIL);
        }

        $Purchase = Purchase::where('code', trim($arr['p_code']))->where('status', 5)->first(['id', 'p_status', 'discount']);
        if (!$Purchase) {
            return returnJson($message = '没有审核通过的采购单号不能申请付款！', $code = ConstFile::API_RESPONSE_FAIL);
        }
        if ($Purchase->p_status > 0) {
            return returnJson($message = '采购单有付款申请！', $code = ConstFile::API_RESPONSE_FAIL);
        }
        if (empty($arr['day'])) {
            return returnJson($message = '业务日期不能为空！', $code = ConstFile::API_RESPONSE_FAIL);
        }
        if (empty($arr['supplier_id'])) {
            return returnJson($message = '供应商不能为空！', $code = ConstFile::API_RESPONSE_FAIL);
        }
        if (empty($arr['apply_id'])) {
            return returnJson($message = '经手人不能为空！', $code = ConstFile::API_RESPONSE_FAIL);
        }
        if (!empty($arr['is_warehousing']) && isset($arr['is_warehousing'])) {//是否入库
            $data['type'] = intval($arr['is_warehousing']);
        } else {
            $data['type'] = 0;
        }

        if (!empty($arr['remark']) && isset($arr['remark'])) {//备注
            $data['remarks'] = trim($arr['remark']);
        }
        if (intval($arr['payable_money'] == 0)) {//此前应付钱
            $data['payable_money'] = 0;
        } else {
            if (empty($arr['payable_money'])) {
                return returnJson($message = '此前应付不能为空！', $code = ConstFile::API_RESPONSE_FAIL);
            }
            $data['payable_money'] = intval($arr['payable_money']);
        }


        $data['user_id'] = $user_id;
        $data['code'] = trim($arr['code']);
        $data['p_id'] = $Purchase->id;
        $data['p_code'] = trim($arr['p_code']);
        $data['business_date'] = trim($arr['day']);
        $data['supplier_id'] = intval($arr['supplier_id']);
        $infoArr=app()->make(RpcRepository::class)->getCustomerById(intval($arr['supplier_id']));
        $supplierTitle = $infoArr['cusname'];
        if (!$supplierTitle) {
            return returnJson($message = '供应商数据错误！', $code = ConstFile::API_RESPONSE_FAIL);
        }
        $data['supplier_name'] = $supplierTitle;
        $data['apply_id'] = intval($arr['apply_id']);
        $chinese_name = User::where('id', $data['apply_id'])->value('chinese_name');
        if (!$chinese_name) {
            return returnJson($message = '经手人数据错误！', $code = ConstFile::API_RESPONSE_FAIL);
        }
        //dd($arr['cost']);
        $data['apply_name'] = $chinese_name;
        $data['money'] = floatval($arr['money']);
        $data['updated_at'] = date('Y-m-d H:i:s', time());

        try {
            DB::transaction(function () use ($data, $user, $type, $arr) {
                $where['id'] = intval($arr['id']);
                $where['user_id'] = $user->id;
                $entrise_id = PaymentOrder::where($where)->value('entrise_id');
                $data['status'] = PaymentOrder::STATUS_DRAFT;
                if ($type == 1) {//直接添加数据
                    $data['status'] = PaymentOrder::STATUS_REVIEW;
                    if (!$entrise_id) {
                        $dataOne['title'] = '付款申请单号为' . trim($data['code']);
                        $entry = FlowCustomize::EntryFlow($dataOne, 'pas_payment_order');//添加进销存采购申请单 审核流程
                        $data['entrise_id'] = $entry->id;

                        $where['code'] = $data['p_code'];
                        $datas['p_status'] = 1;//付款成功
                        Purchase::where($where)->where('status', '=', Purchase::API_STATUS_SUCCESS)->update($datas);
                    }
                }
                PaymentOrder::where($where)->update($data);
            });
            if ($type == 1) {
                return returnJson($message = '提交成功', $code = ConstFile::API_RESPONSE_SUCCESS);
            } else {
                return returnJson($message = '草稿修改成功', $code = ConstFile::API_RESPONSE_SUCCESS);
            }

        } catch (Exception $e) {
            return returnJson($e->getMessage(), $code = ConstFile::API_RESPONSE_FAIL);
        }
    }


    /**
     * 我申请的付款单列表
     */
    public function getWeList($user, $arr)
    {
        $user_id = $user->id;
        $where['user_id'] = $user_id;

        if (!empty($arr['status']) && isset($arr['status'])) {
            $where['status'] = intval($arr['status']);
        }
        if (!empty($arr['title']) && isset($arr['title'])) {
            $list = PaymentOrder::where($where)->where(function ($query) use ($arr) {
                $query->orWhere('code', 'like', '%' . trim($arr['title']) . '%')
                    ->orWhere('supplier_name', 'like', '%' . trim($arr['title']) . '%');
            })->orderBy('created_at', 'desc')->select(['id', 'code', 'supplier_name', 'business_date', 'money', 'status'])->paginate(10);
        } else {
            $list = PaymentOrder::where($where)->orderBy('created_at', 'desc')->select(['id', 'code', 'supplier_name', 'business_date', 'money', 'status'])->paginate(10);
        }
        if ($list) {
            $list = $list->toArray();
            unset($list['first_page_url']);
            unset($list['from']);
            unset($list['last_page']);
            unset($list['last_page_url']);
            unset($list['next_page_url']);
            unset($list['path']);
            unset($list['prev_page_url']);
        } else {
            $list = [];
        }
        return returnJson($message = '修改成功', $code = ConstFile::API_RESPONSE_SUCCESS, $list);
    }

    /**
     * 付款单详情
     */
    public function getInfo($user, $arr)
    {
        $user_id = $user->id;
        $where['id'] = intval($arr['id']);
        $info = PaymentOrder::where($where)->first();
        if ($info) {
            $info = $info->toArray();
            //获取采购单
            $whereOne['code'] = $info['p_code'];
            $Purchase = Purchase::where($whereOne)->first(['code', 'earnest_money', 'turnover_amount', 'business_date']);
            $whereOnes['p_code'] = $info['p_code'];
            $whereOnes['status'] = 5;
            $returnOrder = ReturnOrder::where($whereOnes)->get(['code', 'money', 'business_date']);
            if ($returnOrder) {
                $returnOrders = $returnOrder->toArray();

            } else {
                $returnOrders = [];
            }
            $info['purchase'] = $Purchase->toArray();
            $info['returnOrder'] = $returnOrders;
            $entry_id = $info['entrise_id'];
            $info['process'] = [];
            if ($entry_id) {
                $entry = Entry::findOrFail($entry_id);
                $info['process'] = $this->fetchEntryProcess($entry);
            }
        } else {
            $info = [];
        }
        return returnJson($message = 'OK', $code = ConstFile::API_RESPONSE_SUCCESS, $info);
    }

    /**
     * 我申请的付款单详情
     */
    public function getInfoOne($user, $arr)
    {
        $user_id = $user->id;
        $where['user_id'] = $user_id;
        $where['entrise_id'] = intval($arr['id']);
        $info = PaymentOrder::where($where)->first();
        if ($info) {
            $info = $info->toArray();
            //获取采购单
            $whereOne['code'] = $info['p_code'];
            $Purchase = Purchase::where($whereOne)->first(['code', 'earnest_money', 'turnover_amount', 'business_date']);
            $whereOnes['p_code'] = $info['p_code'];
            $whereOnes['status'] = 5;
            $returnOrder = ReturnOrder::where($whereOnes)->get(['code', 'money', 'business_date']);
            $sumMoney = 0;
            if ($returnOrder) {
                $returnOrders = $returnOrder->toArray();

            } else {
                $returnOrders = [];
                $sumMoney = $Purchase->turnover_amount;
            }
            $info['purchase'] = $Purchase->toArray();
            $info['returnOrder'] = $returnOrders;
            $entry_id = $info['entrise_id'];
            $info['process'] = [];
            if ($entry_id) {
                $entry = Entry::findOrFail($entry_id);
                $info['process'] = $this->fetchEntryProcess($entry);
            }
        } else {
            $info = [];
        }
        return returnJson($message = 'OK', $code = ConstFile::API_RESPONSE_SUCCESS, $info);
    }


    /**
     * 我审核的付款单详情
     */
    public function getInfoTow($user, $arr)
    {
        $user_id = $user->id;

        if (empty($arr['id'])) {
            return returnJson($message = '参数错误!', $code = ConstFile::API_RESPONSE_FAIL);
        }
        $where['id'] = intval($arr['id']);
        $where['user_id'] = $user->id;
        //var_dump($where);die;
        $entry_id = Proc::where($where)->value('entry_id');
        if (!$entry_id) {
            return returnJson($message = '参数错误!', $code = ConstFile::API_RESPONSE_FAIL);
        }
        $wheres['entrise_id'] = $entry_id;

        $info = PaymentOrder::where($where)->first();
        if ($info) {
            $info = $info->toArray();
            //获取采购单
            $whereOne['code'] = $info['p_code'];
            $Purchase = Purchase::where($whereOne)->first(['code', 'earnest_money', 'turnover_amount', 'business_date']);
            $whereOnes['p_code'] = $info['p_code'];
            $whereOnes['status'] = 5;
            $returnOrder = ReturnOrder::where($whereOnes)->get(['code', 'money', 'business_date']);
            $sumMoney = 0;
            if ($returnOrder) {
                $returnOrders = $returnOrder->toArray();
                $money = 0;
                foreach ($returnOrders as $key => $value) {
                    $money = $money + $value['money'];
                }
                $sumMoney = $Purchase->turnover_amount - $money;
            } else {
                $returnOrders = [];
                $sumMoney = $Purchase->turnover_amount;
            }
            $info['purchase'] = $Purchase->toArray();
            $info['returnOrder'] = $returnOrders;
            $info['sum_money'] = $sumMoney;
            $entry_id = $info['entrise_id'];
            $info['process'] = [];
            if ($entry_id) {
                $entry = Entry::findOrFail($entry_id);
                $info['process'] = $this->fetchEntryProcess($entry);
            }
        } else {
            $info = [];
        }
        return returnJson($message = 'OK', $code = ConstFile::API_RESPONSE_SUCCESS, $info);
    }

    /**
     * 4-28
     * gaolu
     * 工作流申请审核人的状态数据转换
     */
    private function fetchEntryProcess(Entry $entry)
    {

        $processes = (new Workflow())->getProcs($entry);
        //dd($entry->toArray());die;
        if (empty($processes)) {
            throw new Exception('流程没有配置审批节点');
        }
        $status = $entry->status;
        $processAuditors = $temp = [];

        foreach ($processes as $key => $process) {
            $temp['process_name'] = $process->process_name;
            $temp['auditor_name'] = '';
            $temp['approval_content'] = $process->proc ? $process->proc->content : '';

            if ($process->proc && $process->proc->auditor_name) {
                $temp['auditor_name'] = $process->proc->auditor_name;
            } elseif ($process->proc && $process->proc->user_name) {
                $temp['auditor_name'] = $process->proc->user_name;
            } else {
                $temp['auditor_name'] = $process->auditors;
            }

            $temp['status'] = $process->proc ? $process->proc->status : '';
            $temp['status_name'] = '';
            if ($status == -2) {
                //echo 1;die;
                if ($process->proc && $process->proc->status == Proc::STATUS_REJECTED) {
                    $temp['status_name'] = '完成';
                } elseif ($process->proc && $process->proc->status == Proc::STATUS_PASSED) {
                    $temp['status_name'] = '已撤回';
                } else {
                    $temp['status_name'] = '完成';
                }
            } else {
                if ($process->proc && $process->proc->status == Proc::STATUS_REJECTED) {
                    $temp['status_name'] = '驳回';
                } elseif ($process->proc && $process->proc->status == Proc::STATUS_PASSED) {
                    $temp['status_name'] = '完成';
                } else {
                    $temp['status_name'] = '待处理';
                }
            }
            $processAuditors[] = $temp;
        }

        return $processAuditors;
    }

    /**
     * 撤回
     */

    public function withdraw($user, $arr)
    {
        $where['user_id'] = $user->id;
        $where['status'] = 1;
        if (empty($arr['id'])) {
            return returnJson($message = '参数错误！', $code = ConstFile::API_RESPONSE_FAIL);
        }
        $where['id'] = intval($arr['id']);
        $PaymentOrder = PaymentOrder::where($where)->first(['id', 'entrise_id', 'p_code']);
        if (!$PaymentOrder) {
            return returnJson($message = '参数错误！', $code = ConstFile::API_RESPONSE_FAIL);
        }
        $data['status'] = 2;
        $data['updated_at'] = date('Y-m-d H:i:s', time());
        try {
            DB::transaction(function () use ($data, $user, $where, $PaymentOrder) {
                //修改付款单状态
                PaymentOrder::where($where)->update($data);
                //$wheres['id']=$entrise_id;
                //Entry::where($wheres)->update();
                $datats['p_status'] = 0;
                Purchase::where('code', '=', $PaymentOrder->p_code)->update($datats);
                $n = $this->cancel($PaymentOrder->entrise_id, '', $user->id);//流的撤销
                if (!$n) {
                    return returnJson($message = '已有审核记录，不能撤销操作', $code = ConstFile::API_RESPONSE_FAIL);
                }
            });
            return returnJson($message = '撤销成功', $code = ConstFile::API_RESPONSE_SUCCESS);
        } catch (Exception $e) {
            return returnJson($e->getMessage(), $code = ConstFile::API_RESPONSE_FAIL);
        }
    }

    /**
     * 查询付款单单跟采购单退货单关联页面的数据
     */
    public function getRelationInfo($user, $arr)
    {
        if (empty($arr['id'])) {
            return returnJson($message = '参数错误！', $code = ConstFile::API_RESPONSE_FAIL);
        }
        $whereOne['id'] = intval($arr['id']);
        $Purchase = Purchase::where($whereOne)->where('status', 5)
            ->first(['id', 'code', 'supplier_id', 'supplier_name', 'payable_money', 'apply_id', 'apply_name', 'earnest_money', 'turnover_amount', 'business_date']);
        if ($Purchase) {
            $Purchase = $Purchase->toArray();
            $whereOnes['p_code'] = $Purchase['code'];
            $whereOnes['status'] = 5;
            $returnOrder = ReturnOrder::where($whereOnes)->get(['id', 'code', 'money', 'business_date']);
            $sumMoney = 0;
            if ($returnOrder) {
                $returnOrders = $returnOrder->toArray();
                $money = 0;
                foreach ($returnOrders as $key => $value) {
                    $money = $money + $value['money'];
                }
                $sumMoney = $Purchase['turnover_amount'] - $money;
            } else {
                $returnOrders = [];
                $sumMoney = $Purchase['turnover_amount'];
            }
            $info['purchase'] = $Purchase;
            $info['returnOrder'] = $returnOrders;
            $info['sum_money'] = $sumMoney;
            $money = PurchasePayableMoney::where('supplier_id', $Purchase['supplier_id'])->value('money');
            $info['payable_money'] = $money;
            $info['code'] = $this->getCodes('FK');

            return returnJson($message = 'ok', $code = ConstFile::API_RESPONSE_SUCCESS, $info);
        }
        return returnJson($message = 'ok', $code = ConstFile::API_RESPONSE_SUCCESS, []);
    }


    /**
     * 撤销
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|string
     */
    public function cancel($entry_id, $content = '', $uid)
    {

        $entryObj = Entry::find($entry_id);
        //已经有审核的记录了 不让撤销
        $cnt = Proc::query()->where('entry_id', '=', $entry_id)
            ->where('status', '=', Proc::STATUS_PASSED)
            ->where('user_id', '!=', $uid)
            ->count();
        if ($cnt > 0) {
            return 0;
        }
        if (empty($content)) {
            $content = Carbon::now()->toDateString() . '用户:' . Auth::user()->chinese_name . '撤销了ID为' . $entry_id . '的流程申请';
        }
        $data = [
            'operate_user_id' => $uid,
            'action' => 'cancel',
            'type' => OperateLog::TYPE_WORKFLOW,
            'object_id' => $entry_id,
            'object_name' => $entryObj->title,
            'content' => $content,
        ];
        OperateLog::query()->insert($data);
        $entryObj->status = Entry::STATUS_CANCEL;
        $res = $entryObj->save();
        //
        return $res;
    }

    /*************************************************************/
    /*2019-05-13
    * 添加付款单
    * type 1是添添加付款单  其他表示 保存草稿
    */
    public function setAddOne($user, $arr)
    {
        $user_id = $user->id;
        $user_name = $user->chinese_name;

        if (empty($arr['code'])) {
            return returnJson($message = '付款单号不能为空！', $code = ConstFile::API_RESPONSE_FAIL);
        }
        $PaymentOrder = PaymentOrder::where('code', trim($arr['code']))->first(['id']);
        if ($PaymentOrder) {
            return returnJson($message = '付款单号不能重复！', $code = ConstFile::API_RESPONSE_FAIL);
        }

        if (empty($arr['day'])) {
            return returnJson($message = '业务日期不能为空！', $code = ConstFile::API_RESPONSE_FAIL);
        }
        if (empty($arr['supplier_id'])) {
            return returnJson($message = '供应商不能为空！', $code = ConstFile::API_RESPONSE_FAIL);
        }
        if (empty($arr['apply_id'])) {
            return returnJson($message = '经手人不能为空！', $code = ConstFile::API_RESPONSE_FAIL);
        }

        if (!empty($arr['remark']) && isset($arr['remark'])) {//备注
            $data['remarks'] = trim($arr['remark']);
        }
        if (empty($arr['count'])) {//备注
            return returnJson($message = '付款单重的采购跟退货单不能为空!', $code = ConstFile::API_RESPONSE_FAIL);
        }
        $count = $arr['count'];
        if (intval($arr['payable_money'] == 0)) {//此前应付钱
            $data['payable_money'] = 0;
        } else {
            if (empty($arr['payable_money'])) {
                return returnJson($message = '此前应付不能为空！', $code = ConstFile::API_RESPONSE_FAIL);
            }
            $data['payable_money'] = intval($arr['payable_money']);
        }

        $data['user_id'] = $user_id;
        $data['code'] = trim($arr['code']);
        $data['business_date'] = trim($arr['day']);
        $data['supplier_id'] = intval($arr['supplier_id']);
        $supplierTitle = Supplier::where('id', intval($arr['supplier_id']))->where('status', 1)->value('title');
        if (!$supplierTitle) {
            return returnJson($message = '供应商数据错误！', $code = ConstFile::API_RESPONSE_FAIL);
        }
        $data['supplier_name'] = $supplierTitle;
        $data['apply_id'] = intval($arr['apply_id']);
        $chinese_name = User::where('id', $data['apply_id'])->value('chinese_name');
        if (!$chinese_name) {
            return returnJson($message = '经手人数据错误！', $code = ConstFile::API_RESPONSE_FAIL);
        }
        //dd($arr['cost']);
        $data['apply_name'] = $chinese_name;
        $data['created_at'] = date('Y-m-d H:i:s', time());
        $data['updated_at'] = date('Y-m-d H:i:s', time());
        try {
            DB::transaction(function () use ($data, $user, $count) {
                if ($count) {
                    $money = 0;
                    $data['status'] = 1;
                    $dataOne['title'] = '付款申请单号为' . trim($data['code']);
                    $entry = FlowCustomize::EntryFlow($dataOne, 'pas_payment_order');//添加进销存采购申请单 审核流程
                    $data['entrise_id'] = $entry->id;
                    $n = PaymentOrder::insertGetId($data);

                    foreach ($count as $key => &$val) {
                        $val['created_at'] = date('Y-m-d H:i:s', time());
                        $val['updated_at'] = date('Y-m-d H:i:s', time());
                        $id = intval($val['p_id']);
                        $typess = intval($val['type']);
                        $val['po_id'] = $n;
                        //查询采购单
                        if ($typess == 1) {
                            $PurchaseMoney = Purchase::where('id', $id)->first(['earnest_money', 'turnover_amount']);
                            $money = $money + ($PurchaseMoney->turnover_amount - $PurchaseMoney->earnest_money);
                            $dt['p_status'] = 1;
                            $dt['created_at'] = date('Y-m-d H:i:s', time());
                            Purchase::where('id', $id)->update($dt);
                        } else {//查询退货单
                            $moneys = ReturnOrder::where('id', $id)->value('money');
                            $money = $money - $moneys;
                            $dt['p_status'] = 1;
                            $dt['created_at'] = date('Y-m-d H:i:s', time());
                            ReturnOrder::where('id', $id)->update($dt);
                        }
                    }
                    $datas['money'] = floatval($money);
                    PaymentOrder::where('id', $n)->update($datas);
                    PaymentOrderContent::insert($count);
                }
            });

            return returnJson($message = '提交成功', $code = ConstFile::API_RESPONSE_SUCCESS);
        } catch (Exception $e) {
            return returnJson($e->getMessage(), $code = ConstFile::API_RESPONSE_FAIL);
        }
    }

    /**  获取供应商 下面的所有采购单跟退货单
     * @param $user
     * @param $arr
     * @return \Illuminate\Http\JsonResponse|string
     */
    public function getOrder($user, $arr)
    {
        if (empty($arr['supplier_id'])) {
            return returnJson('供应商数据id不能为空!', $code = ConstFile::API_RESPONSE_FAIL);
        }
        $purchase=Purchase::where('supplier_id', intval($arr['supplier_id']))
            ->where('status', Purchase::API_STATUS_SUCCESS)
            ->where('p_status', 0)//未付款数据
            ->get(['code', 'business_date', 'earnest_money', 'turnover_amount']);
        if(!$purchase){
            $purchase=[];
        }

        $returnOrder=ReturnOrder::where('supplier_id', intval($arr['supplier_id']))
            ->where('status', 4)//保留需要修改
            ->where('p_status', 0)//未付款数据
            ->get(['code', 'business_date', 'money']);

        if(!$returnOrder){
            $returnOrder=[];
        }

        $list['purchase']=$purchase;
        $list['returnOrder']=$returnOrder;
        return returnJson($message = 'ok', $code = ConstFile::API_RESPONSE_SUCCESS,$list);
    }


    /**
     * 撤回
     */

    public function withdrawOne($user, $arr)
    {
        $where['user_id'] = $user->id;
        $where['status'] = PaymentOrder::STATUS_REVIEW;
        if (empty($arr['id'])) {
            return returnJson($message = '参数错误！', $code = ConstFile::API_RESPONSE_FAIL);
        }
        $where['id'] = intval($arr['id']);
        $PaymentOrder = PaymentOrder::where($where)->first(['id', 'entrise_id', 'p_code']);
        if (!$PaymentOrder) {
            return returnJson($message = '参数错误！', $code = ConstFile::API_RESPONSE_FAIL);
        }
        $data['status'] = PaymentOrder::STATUS_WITHDRAWAL;
        $data['updated_at'] = date('Y-m-d H:i:s', time());
        try {
            DB::transaction(function () use ($data, $user, $where, $PaymentOrder) {
                //修改付款单状态
                PaymentOrder::where($where)->update($data);
                //$wheres['id']=$entrise_id;
                //Entry::where($wheres)->update();
                $datats['p_status'] = 0;

                $countArr= PaymentOrderContent::where('po_id',$PaymentOrder->id)->get(['p_id','type']);
                if($countArr){
                    $countArr=$countArr->toArray();
                    foreach ($countArr as $key =>$las){
                        $whres['id']=$las['p_id'];
                        $datas['p_status']=0;//未付款数据
                        $datas['updated_at'] = date('Y-m-d H:i:s', time());
                        if($las['type']==PaymentOrderContent::TYPE_PURCHASE){
                            Purchase::where($whres)->update($datas);
                        }else{
                            ReturnOrder::where($whres)->update($datas);
                        }
                    }
                }
                $n = $this->cancel($PaymentOrder->entrise_id, '', $user->id);//流的撤销
                if (!$n) {
                    return returnJson($message = '已有审核记录，不能撤销操作', $code = ConstFile::API_RESPONSE_FAIL);
                }
            });
            return returnJson($message = '撤销成功', $code = ConstFile::API_RESPONSE_SUCCESS);
        } catch (Exception $e) {
            return returnJson($e->getMessage(), $code = ConstFile::API_RESPONSE_FAIL);
        }
    }

    /**
     * 付款单详情
     */
    public function getOneInfo($user, $arr)
    {
        $user_id = $user->id;
        $where['id'] = intval($arr['id']);
        $info = PaymentOrder::where($where)->first();
        if ($info) {
            $info = $info->toArray();
            //获取采购单
            $whereOne['po_id'] = $info['id'];
            $purchaseList = PaymentOrderContent::with('purchaseList')->where($whereOne)->where('type',1)->first(['p_id','type']);

            $returnorderList = PaymentOrderContent::with('returnorderList')->where($whereOne)->where('type',2)->first(['p_id','type']);
            $info['purchase']=[];
            if($purchaseList){
                $info['purchase'] =$purchaseList->toArray();
            }
            $info['returnOrder']=[];
            if($purchaseList){
                $info['returnOrder'] =$returnorderList->toArray();
            }
            $entry_id = $info['entrise_id'];
            $info['process'] = [];
            if ($entry_id) {
                $entry = Entry::findOrFail($entry_id);
                $info['process'] = $this->fetchEntryProcess($entry);
            }
        } else {
            $info = [];
        }
        return returnJson($message = 'OK', $code = ConstFile::API_RESPONSE_SUCCESS, $info);
    }

    /**
     * 我审核的付款单详情
     */
    public function getPassInfoTow($user, $arr)
    {
        $user_id = $user->id;

        if (empty($arr['id'])) {
            return returnJson($message = '参数错误!', $code = ConstFile::API_RESPONSE_FAIL);
        }
        $where['id'] = intval($arr['id']);
        $where['user_id'] = $user->id;
        //var_dump($where);die;
        $entry_id = Proc::where($where)->value('entry_id');
        if (!$entry_id) {
            return returnJson($message = '参数错误!', $code = ConstFile::API_RESPONSE_FAIL);
        }
        $wheres['entrise_id'] = $entry_id;

        $info = PaymentOrder::where($where)->first();
        if ($info) {
            $info = $info->toArray();
            $info['w_id']=intval($arr['id']);
            //获取采购单
            $whereOne['po_id'] = $info['id'];
            $purchaseList = PaymentOrderContent::with('purchaseList')->where($whereOne)->where('type',PaymentOrderContent::TYPE_PURCHASE)->first(['p_id','type']);

            $returnorderList = PaymentOrderContent::with('returnorderList')->where($whereOne)->where('type',PaymentOrderContent::TYPE_RETURN_ORDER)->first(['p_id','type']);
            $info['purchase']=[];
            if($purchaseList){
                $info['purchase'] =$purchaseList->toArray();
            }
            $info['returnOrder']=[];
            if($purchaseList){
                $info['returnOrder'] =$returnorderList->toArray();
            }
            $entry_id = $info['entrise_id'];
            $info['process'] = [];
            if ($entry_id) {
                $entry = Entry::findOrFail($entry_id);
                $info['process'] = $this->fetchEntryProcess($entry);
            }
        } else {
            $info = [];
        }
        return returnJson($message = 'OK', $code = ConstFile::API_RESPONSE_SUCCESS, $info);
    }



    /**
     * 我申请的付款单详情
     */
    public function getPassInfoOne($user, $arr)
    {
        $user_id = $user->id;
        $where['user_id'] = $user_id;
        $where['entrise_id'] = intval($arr['id']);
        $info = PaymentOrder::where($where)->first();
        if ($info) {
            $info = $info->toArray();
            //获取采购单
            $whereOne['po_id'] = $info['id'];
            $purchaseList = PaymentOrderContent::with('purchaseList')->where($whereOne)->where('type',PaymentOrderContent::TYPE_PURCHASE)->first(['p_id','type']);

            $returnorderList = PaymentOrderContent::with('returnorderList')->where($whereOne)->where('type',PaymentOrderContent::TYPE_RETURN_ORDER)->first(['p_id','type']);
            $info['purchase']=[];
            if($purchaseList){
                $info['purchase'] =$purchaseList->toArray();
            }
            $info['returnOrder']=[];
            if($purchaseList){
                $info['returnOrder'] =$returnorderList->toArray();
            }
            $entry_id = $info['entrise_id'];
            $info['process'] = [];
            if ($entry_id) {
                $entry = Entry::findOrFail($entry_id);
                $info['process'] = $this->fetchEntryProcess($entry);
            }
        } else {
            $info = [];
        }
        return returnJson($message = 'OK', $code = ConstFile::API_RESPONSE_SUCCESS, $info);
    }
}
