<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 2018/11/9
 * Time: 11:06
 */

namespace App\Services\Workflow;


use App\Models\Workflow\Entry;
use UserFixException;
use App\Models\Company;

class FlowFinancePayment implements FlowInterface
{
    public function checkValidate(Entry $entry)
    {
        $entryDatas = $entry->entry_data;
        
        $fileUpload = $entryDatas->where('field_name', 'file_upload')->first();
        if (!$fileUpload) {
            throw new UserFixException("附件没有上传！");
        }

        if (!$fileUpload->field_value) {
            throw new UserFixException("附件没有上传！");
        }

        $entryDatas->each(function ($item, $key) {
            if ($item->field_name == 'our_main_body') {
                $validCompany = Company::validCompanyName($item->field_value);
                if (!$validCompany) {
                    throw new UserFixException("我方主体填写错误，请重新填写！");
                }
            }

            if ($item->field_name == 'payment_amount') {
                $paymentAmount = json_decode($item->field_value, true);
                if (!$paymentAmount['type'] || !$paymentAmount['amount'] || !$paymentAmount['amount_upper']) {
                    throw new UserFixException("付款金额必填！");
                }
            }

            if ($item->field_name == 'payment_amount_transfer') {
                if (!$item->field_name) {
                    throw new UserFixException("付款金额必填！");
                }
            }

        });
    }
}