<?php
namespace App\Services\Workflow;

use App\Http\Helpers\Mh;
use App\Models\Workflow\Entry;
use App\Models\Workflow\Flow;
use App\Services\Attendance\TravelsService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use UserFixException;

class FlowFinanceTravelReimburse implements FlowInterface
{
    public function checkValidate(Entry $entry)
    {
        // 检查在途流程
        $existWorkflowCount = Flow::leftJoin('workflow_entries', 'workflow_flows.id', 'workflow_entries.flow_id')
            ->where('workflow_entries.id', '!=', $entry->id)
            ->where('workflow_flows.flow_no', $entry->flow->flow_no)
            ->where('workflow_entries.status', Entry::STATUS_IN_HAND)
            ->where('workflow_entries.user_id', $entry->user_id)
            ->count();
        if ($existWorkflowCount > 0) {
            throw new UserFixException('已有在途审批中流程');
        }

        $entryData = $entry->entry_data;

        // 校验付款方式
        $paymentMethod = $entryData->where('field_name', 'payment_method')->first()->field_value;
        if (!in_array($paymentMethod, TravelsService::PAYMENT_METHOD)) {
            throw new UserFixException(sprintf('付款方式(%s)不支持', $paymentMethod));
        }

        // 报销项校验
        $reimbursementField = $entryData->where('field_name', 'reimbursement')->first();
        if (empty($reimbursementField->field_value)) {
            throw new UserFixException('报销项不能为空');
        }
        $reimbursementData = json_decode($reimbursementField->field_value, true);
        if (!is_array($reimbursementData)) {
            Log::error('无法解析报销项数据', ['field_value' => $reimbursementField->field_value]);
            throw new UserFixException('无法解析报销项数据');
        }
        if (empty($reimbursementData)) {
            throw new UserFixException('报销项不能为空');
        }

        // 报销总额
        $totalAmount    = $entryData->where('field_name', 'total_amount')->first()->field_value;
        $totalAmountFen = Mh::y2f($totalAmount);
        if ($totalAmountFen <= 0) {
            throw new UserFixException(sprintf('费用合计金额(%s)不正确', $totalAmount));
        }
        // 还款总额
        $totalRepayAmountFen = 0;

        // 报销项统计总额
        $reimbursementTotalAmountFen = 0;
        foreach ($reimbursementData as $key => $reimbursement) {
            if (empty($reimbursement['description'])) {
                throw new UserFixException(sprintf('第%s项报销项目事由不能为空', $key + 1));
            }
            if (empty($reimbursement['travel_from'])) {
                throw new UserFixException(sprintf('第%s项报销出发地不能为空', $key + 1));
            }
            if (empty($reimbursement['travel_to'])) {
                throw new UserFixException(sprintf('第%s项报销目的地不能为空', $key + 1));
            }
            if (empty($reimbursement['telecom_amount']) && empty($reimbursement['hotel_amount']) &&
                empty($reimbursement['allowance_amount']) && empty($reimbursement['other_amount'])) {
                throw new UserFixException(sprintf('第%s项报销交通费用、住宿费用、出差补贴、其他杂项不能同时为空', $key + 1));
            }
            $reimbursementTotalAmountFen += Mh::y2f($reimbursement['telecom_amount'] ?? 0) +
                Mh::y2f($reimbursement['hotel_amount'] ?? 0) +
                Mh::y2f($reimbursement['allowance_amount'] ?? 0) +
                Mh::y2f($reimbursement['other_amount'] ?? 0);
            if (empty($reimbursement['invoice_quantity'])) {
                throw new UserFixException(sprintf('第%s项报销发票张数不能为空', $key + 1));
            }
        }

        // 销暂支校验
        if ($paymentMethod == TravelsService::PAYMENT_METHOD_REPAY) {
            $financeApData = $entryData->where('field_name', 'finance_ap')->first();
            if (empty($financeApData->field_value)) {
                throw new UserFixException('销暂支数据不能为空');
            }
            $financeApDataArr = json_decode($financeApData->field_value, true);
            if (!is_array($financeApDataArr)) {
                Log::error('无法解析销暂支数据', ['field_value' => $financeApData->field_value]);
                throw new UserFixException('无法解析销暂支数据');
            }
            if (Collection::make($financeApDataArr)->where('repay_amount', '>', '0')->count() < 1) {
                throw new UserFixException('销暂支金额输入不正确');
            }
            $repayAmountSum        = Collection::make($financeApDataArr)->pluck('repay_amount')->sum();
            $totalRepayAmountField = $entryData->where('field_name', 'total_repay_amount')->first();
            if (empty($totalRepayAmountField->field_value) ||
                Mh::y2f($repayAmountSum) != ($totalRepayAmountFen = Mh::y2f($totalRepayAmountField->field_value))) {
                throw new UserFixException('销暂支总额有误');
            }
        }

        // 报销金额校验
        if ($reimbursementTotalAmountFen != $totalAmountFen) {
            Log::error('报销总额统计有误', [
                'reimbursementTotalAmountFen' => $reimbursementTotalAmountFen,
                'totalAmountFen'              => $totalAmountFen,
            ]);
            throw new UserFixException('报销总额统计有误');
        }
        $actualAmount    = $entryData->where('field_name', 'actual_amount')->first()->field_value;
        $actualAmountFen = Mh::y2f($actualAmount);
        if ($totalAmountFen < $actualAmountFen) {
            throw new UserFixException(sprintf('费用合计金额(%s)不能小于待支付金额(%s)', $totalAmount, $actualAmount));
        }
        if ($actualAmountFen < 0 || ($totalAmountFen - $totalRepayAmountFen) != $actualAmountFen) {
            throw new UserFixException(sprintf('待支付金额(%s)不正确', $actualAmount));
        }
    }
}
