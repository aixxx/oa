<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 2018/11/19
 * Time: 17:34
 */

namespace App\Services\Workflow;


use App\Models\Workflow\Entry;
use DevFixException;

class FlowFinanceInvoiceSign implements FlowInterface
{


    public static $noValidColumn = [
        'applicant_chinese_name',
        'primary_dept',
        'company',
        'remark',
        'file_upload',
    ];

    public static $needValidMaps = [
        'payment_amount_flow'   => '付款流程',
        'invoice_num'           => '发票号',
        'invoice_type'          => '发票类型',
        'invoice_amount'        => '发票金额',
        'remain_invoice_amount' => '剩余应收发票金额',
    ];

    public function checkValidate(Entry $entry)
    {
        // TODO: Implement checkValidate() method.

        $entryDatas    = $entry->entry_data;
        $needValidData = $entryDatas->whereNotIn('field_name', self::$noValidColumn);
        $needValidData->each(function ($item, $key) {
            $currentData = json_decode($item->field_value, true);
            if ($currentData) {
                foreach ($currentData as $key => $value) {
                    if ($value === null) {
                        if (isset(self::$needValidMaps[$key])) {
                            throw new DevFixException(self::$needValidMaps[$key] . "必填。");
                        }
                    }
                }
            }
        });
    }
}