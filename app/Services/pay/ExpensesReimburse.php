<?php

namespace App\Services\pay;

use App\Http\Helpers\Dh;
use App\Http\Helpers\Mh;
use App\Models\Workflow\Entry;
use App\Models\Workflow\Proc;
use App\Services\Workflow\WorkFlowBusinessService;
use App\Services\WorkflowTaskService;
use Illuminate\Support\Facades\Request;
use App\Services\WorkflowUserService;

/**
 * 费用报销
 * User: aike
 * Date: 2018/7/25
 * Time: 下午6:08
 *
 * log:   多课目报销
 */
class ExpensesReimburse extends PayCreateBase
{
    public function entryToPayParams($orderNo, Entry $entry)
    {
        $reason      = $entry->title; // 付款原因
        $amount      = 0; // 付款金额，单位分
        $payeeCard   = ''; // 收款人(公司)银行卡号
        $bankName    = ''; // 开户行名称
        $userIdNum   = ''; // 用户身份证号
        $costCenter  = ''; // 成本中心
        $primaryDept = ''; // 所属部门
        $company     = ''; // 所属公司
        $detail      = ''; //报销明细
        $entryDatas  = $entry->entry_data;

        $deptPath = WorkflowUserService::fetchUserPrimaryDeptPath($entry->user_id);
        $deptArr  = explode('/', $deptPath);
        array_shift($deptArr);
        $deptLevel1Name     = $deptArr['0'] ?? '';
        $deptLevel2Name     = $deptArr['1'] ?? '';

        foreach ($entryDatas as $entryData) {
            if ($entryData->field_name == 'expense_amount') {
                // 报销金额
                $amount = Mh::y2f($entryData->field_value);
            } elseif ($entryData->field_name == 'bank_num') {
                // 收款银行卡号
                $payeeCard = $entryData->field_value;
            } elseif ($entryData->field_name == 'bank_name') {
                // 开户银行
                $bankName = $entryData->field_value;
            } elseif ($entryData->field_name == 'user_id_num') {
                // 申请人身份证号
                $userIdNum = $entryData->field_value;
            } elseif ($entryData->field_name == 'cost_center') {
                // 成本中心
                $costCenter = $entryData->field_value;
            } elseif ($entryData->field_name == 'primary_dept') {
                // 所属部门
                $primaryDept = $entryData->field_value;
            } elseif ($entryData->field_name == 'company') {
                // 所属公司
                $company = $entryData->field_value;
            } elseif ($entryData->field_name == 'expense_reimburse_list_multiple') {
                // 费用科目
                $listData = json_decode($entryData->field_value, true);
                $detail = $this->setDetailForMultiple($listData);
            } elseif ($entryData->field_name == 'select1') {    //兼容旧模板
                // 费用科目
                $fee_type = $entryData->field_value;

                $detail = $this->setDetailForSingle($entryDatas, $fee_type);
            }
        }

        $payCallBack = config('capital.pay_callback_url'); // env('CAPITAL_PAY_CALL_BACK_URL');

        $data = [
            'trade_report_code'                 => $orderNo,
            'trade_report_payer_name'           => $company, // 复用报销付款公司就是申请人所在公司
            'trade_report_reason'               => $reason,
            'trade_report_amount'               => $amount,
            'trade_report_type'                 => self::TRADE_REPORT_TYPE_PRIVATE, // 费用报销都是对私的
            'trade_report_payee_name'           => $entry->user->chinese_name, // 收款人姓名
            'trade_report_payee_card'           => $payeeCard,
            'trade_report_payee_identity'       => $userIdNum, // 身份证号
            'trade_report_payee_number'         => $entry->user->employee_num . '', // 员工编号
            'trade_report_payee_cost_name'      => $costCenter,
            'trade_report_payee_depart_lvl1'    => $deptLevel1Name,
            'trade_report_payee_depart_lvl2'    => $deptLevel2Name,
            'trade_report_payee_depart'         => $primaryDept,
            'trade_report_payee_happen_time'    => $entry->finish_at,
            'trade_report_callback'             => $payCallBack,
            'payee_bank_branch'                 => $bankName,
            'payee_bank_subbranch'              => '', // 低于5万以下不传支行,现在先不传
            'trade_report_detail'               => $detail, //报销明细数据多科目
        ];

        // 将数据封装成财务系统要求的请求类型
        return $this->formatRequestParams($data, 'OAPAY');
    }

    /**
     * 设置报销明细 多科目
     *
     * @param array $data
     * @return array
     */
    private function setDetailForMultiple(array $data)
    {
        $result = [];
        $row    = [];
        foreach ($data as $item) {
            //整行为空，不校验，跳过；只校验一行部分有值
            if ($item['expense_order_type'] == null &&
                $item['expense_order_date'] == null &&
                $item['expense_order_reason'] == null &&
                $item['expense_order_amount'] == null &&
                $item['expense_order_num'] == null) {
                continue;
            }

            $row['trade_report_date']          = $item['expense_order_date'];
            $row['trade_report_account_name']  = $item['expense_order_type'];
            $row['trade_report_detail_reason'] = $item['expense_order_reason'];
            $row['trade_report_detail_amount'] = intval(Mh::y2f($item['expense_order_amount'])); //金额转整型 单位分
            $row['trade_report_detail_num']    = intval($item['expense_order_num']);    //单数转整型

            array_push($result, $row);
        }

        return $result;
    }

    /**
     * 设置报销明细 单科目
     *
     * @param array $data
     * @param string $feeType
     * @return array
     */
    private function setDetailForSingle($datas, $feeType)
    {
        $result = [];

        $fieldNameStr = '';
        foreach ($datas as $entryData) {
            $fieldNameStr .= $entryData->field_name .'__';
        }

        $pattern = "/\d+/";
	    preg_match_all($pattern, $fieldNameStr, $numList);

	    $rowLength = max($numList[0]); //最大行

        for($row =1; $row <= $rowLength; $row++) {
            foreach ($datas as $entryData) {
                $item['expense_order_type'] = $feeType;
                //date
                if ($entryData->field_name == 'expense_order_date_' . $row) {
                    $item['expense_order_date'] = $entryData->field_value;
                }

                //reason
                if ($entryData->field_name == 'expense_order_reason_' . $row) {
                    $item['expense_order_reason'] = $entryData->field_value;
                }

                //amount
                if ($entryData->field_name == 'expense_order_amount_' . $row) {
                    $item['expense_order_amount'] = $entryData->field_value;
                }

                //num
                if ($entryData->field_name == 'expense_order_num_' . $row) {
                    $item['expense_order_num'] = $entryData->field_value;
                }
            }

            //整行为空，不校验，跳过；只校验一行部分有值
            if ($item['expense_order_type'] == '' &&
                $item['expense_order_date'] == '' &&
                $item['expense_order_reason'] == '' &&
                $item['expense_order_amount'] == '' &&
                $item['expense_order_num'] == '') {
                continue;
            }

            //金额空则跳过
            if (!$item['expense_order_amount']) {
                continue;
            }

            $rowData['trade_report_date']          = $item['expense_order_date'] ?? Dh::formatDate(Dh::getTodayStart());
            $rowData['trade_report_account_name']  = $item['expense_order_type'];
            $rowData['trade_report_detail_reason'] = $item['expense_order_reason'] ?? '多科目支持新接口';
            $rowData['trade_report_detail_amount'] = intval(Mh::y2f($item['expense_order_amount'])); //金额转整型 单位分
            $rowData['trade_report_detail_num']    = intval($item['expense_order_num']) ?? 1;    //单数转整型

            array_push($result, $rowData);
        }

        return $result;
    }

    /**
     * 支付回调
     * @param $param
     */
    public function payCallBack($request)
    {
        $params      = json_decode($request, true);
        $taskService = new WorkflowTaskService($params['code']);
        // 保存任务回调信息
        $taskService->saveCallBackRes($request);
        // 获取审批节点
        $proc = $taskService->proc();
        // 业务流程
        $flowBusiness = WorkFlowBusinessService::getFlowBusiness($proc->flow);
        // 业务流程执行支付回调
        $params['status_des'] = $params['status'] == self::PAY_RES_STATUS_SUCCESS ? '成功' : '失败';
        $flowBusiness->payCallBack($proc, $params);
    }
}
