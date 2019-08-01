<?php

namespace App\Services\Workflow;

use App\Http\Helpers\StringHelper;
use App\Models\Workflow\Entry;
use UserFixException;
class FlowAttendanceRetroactive implements FlowInterface
{
    /**
     * 校验表单数据合法性
     *
     * @param \App\Models\Workflow\Entry $entry
     *
     * @throws \Exception
     */
    public function checkValidate(Entry $entry)
    {
        $templateForms    = $entry->flow->template->template_form;
        $templateFormsMap = []; // 以field为key的map
        foreach ($templateForms as $templateForm) {
            $templateFormsMap[$templateForm->field] = $templateForm;
        }

        $entryDatas = $entry->entry_data;

        foreach ($entryDatas as $entryData) {
            $formItemInfo = $templateFormsMap[$entryData->field_name];
            $fileNameDes  = $formItemInfo->field_name; // 字段名称
            if (StringHelper::isEmpty($entryData->field_value) && $formItemInfo->required == 1) {
                throw new UserFixException(sprintf('%s不能为空,传递的值为[%s]', $fileNameDes, $entryData->field_value));
            }

            if (('retroactive_datatime' == $entryData->field_name) && (strtotime($entryData->field_value) > time())) {
                throw new UserFixException(sprintf('%s不能大于当前时间,传递的值为[%s]', $fileNameDes, $entryData->field_value));
            }
        }
    }
}