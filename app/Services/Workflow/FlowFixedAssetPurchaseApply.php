<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 2018/9/27
 * Time: 12:13
 */

namespace App\Services\Workflow;

use App\Models\Workflow\Entry;
use App\Models\Workflow\Flow;
use Exception;

use UserFixException;
class FlowFixedAssetPurchaseApply implements FlowInterface
{
    /**
     * 校验表单数据合法性
     * @param Entry $entry
     */
    public function checkValidate(Entry $entry)
    {
        $templateForms          = $entry->flow->template->template_form;
        $customNotRequireList   = [
            'primary_dept',
            'applicant_chinese_name',
            'apply_date',
            'hidden_text_2',
            'expected_arrival_date',
            'hidden_text_1',
            'is_it',
            'total_all_price',
        ];
        $tableRequiredEntryData = $entry->entry_data->whereNotIn('field_name', $customNotRequireList);

        $totalAllPrice = $entry->entry_data->where('field_name', 'total_all_price')->first();
        $tableRequiredEntryData->each(function ($item, $key) {
            $tableData = collect(json_decode($item->field_value, true));
            if (!isset($tableData['cat']) || !$tableData['cat']) {
                throw new UserFixException("采购类别必填！");
            }

            $onlyAssetInfo = $tableData->except('cat', 'other_cat');
            $onlyAssetInfo->each(function ($every, $k) {
                if (!$every['name']) {
                    throw new UserFixException("名称必填！");
                }

                if (!$every['purchase_price']) {
                    throw new UserFixException("单价必填！");
                }

                $exp = '/(^[1-9]\d*(\.\d{1,2})?$)|(^0(\.\d{1,2})?$)/';

                if (!preg_match_all($exp,$every['purchase_price'])) {
                    throw new UserFixException("单价填写有误");
                }


                if (!$every['stock']) {
                    throw new UserFixException("数量必填！");
                }

                if (!$every['total_price']) {
                    throw new UserFixException("总价必填！");
                }
            });
        });
        if (!$totalAllPrice->field_value) {
            throw new UserFixException("金额合计必填！");
        }
    }


    /**
     * 获取已完成的采购申请单
     * @return Entry[]|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public static function getFinishedAssetPurchaseApply()
    {
        $curTimeStamp = time();
        $currentTime  = date('Y-m-d H:i:s', $curTimeStamp);
        //30天期间内的采购流程
        $date_before_thirty = date('Y-m-d H:i:s', $curTimeStamp - 30 * 24 * 60 * 60);

        $flowInfo = Flow::whereFlowNo(Entry::WORK_FLOW_NO_FIXED_ASSET_PURCHASE_APPLY)->publish()->Unabandon()->first();
        if ($flowInfo) {
            $entriesInfo = Entry::whereFlowId($flowInfo->id)->whereStatus(Entry::STATUS_FINISHED)->whereBetween('created_at',
                [$date_before_thirty, $currentTime])->get();
        } else {
            $entriesInfo = collect();
        }
        return $entriesInfo;
    }
}