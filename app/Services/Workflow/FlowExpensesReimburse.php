<?php

namespace App\Services\Workflow;

use App\Http\Helpers\StringHelper;
use App\Models\Workflow\Entry;
use App\Models\Workflow\Proc;
use App\Services\Message\MessageService;
use App\Services\WorkflowMessageService;
use UserFixException;
/**
 * 费用报销表单业务
 * User: aike
 * Date: 2018/7/30
 * Time: 下午5:08
 */
class FlowExpensesReimburse implements FlowInterface
{
    const MAX_AMOUNT = 50 * 1000; // 报销金额不能超过5万

    //费用科目类型
    public static $expenseType = [
        '业务招待费',
        '人事招聘费',
        '人事管理费',
        '会务费',
        '其他福利',
        '办公耗材',
        '加班交通费',
        '劳务费',
        '咨询费',
        '团建费',
        '培训费',
        '审计费',
        '市内交通费',
        '广告宣传',
        '律师费',
        '快递费',
        '房租物业',
        '水电费',
        '电话费',
        '福利费-下午茶',
        '福利费-图书馆',
        '福利费-早餐关怀',
        '福利费-生日会',
        '福利费-社团经费',
        '福利费-节日庆祝',
        '税金',
        '网络费',
        '营销活动',
        '装修费',
        '设备租赁',
        '诉讼费',
        '车位租赁',
        '车辆使用费'
    ];

    public static $personnelType = [
        '福利费-社团经费',
        '团建费',
        '培训费'
    ];

    /**
     * 校验表单数据合法性
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
            //不在模板中的字段不校验
            if (!isset($templateFormsMap[$entryData->field_name])) {
                continue;
            }

            $formItemInfo = $templateFormsMap[$entryData->field_name];
            $fileNameDes  = $formItemInfo->field_name; // 字段名称
            if (StringHelper::isEmpty($entryData->field_value) && $formItemInfo->required == 1) {
                throw new UserFixException(sprintf('%s不能为空,传递的值为[%s]', $fileNameDes, $entryData->field_value));
            }

            if ($entryData->field_name == 'expense_amount') {
                $entryData->field_value = str_replace(',', '', $entryData->field_value);
                if (!is_numeric($entryData->field_value)) {
                    throw new UserFixException(sprintf('%s必须为数字,填写值为%s', $fileNameDes, $entryData->field_value));
                }

                if ($entryData->field_value <= 0) {
                    throw new UserFixException(sprintf('%s必须大于0', $fileNameDes));
                }

                if ($entryData->field_value >= self::MAX_AMOUNT) {
                    throw new UserFixException(sprintf('%s不能超过%s元,填写值为%s', $fileNameDes, self::MAX_AMOUNT, $entryData->field_value));
                }
            }

            if ($entryData->field_name == 'num_sum') {
                if ($entryData->field_value <= 0) {
                    throw new UserFixException(sprintf('%s必须大于0', $fileNameDes));
                }
            }

            //报销列表内容校验
            if ($entryData->field_name == 'expense_reimburse_list_multiple') {
                $listData = json_decode($entryData->field_value, true);

                foreach ($listData as $index => $item) {
                    //整行为空，不校验，跳过；只校验一行部分有值
                    if ($item['expense_order_type'] == null &&
                        $item['expense_order_date'] == null &&
                        $item['expense_order_reason'] == null &&
                        $item['expense_order_amount'] == null &&
                        $item['expense_order_num'] == null) {
                        continue;
                    }

                    if ($item['expense_order_type'] == null ||
                        $item['expense_order_date'] == null ||
                        $item['expense_order_reason'] == null ||
                        $item['expense_order_amount'] == null ||
                        $item['expense_order_num'] == null) {
                        throw new UserFixException(sprintf("第%s行数据有误，不能空", $index));
                    }
                }
            }
        }
    }

    /**
     * 支付回调
     * @param \App\Models\Workflow\Proc $proc
     * @param                           $params
     */
    public function payCallBack(Proc $proc, $params)
    {
        $entry = $proc->entry;
        MessageService::send($entry->flow->flow_no . '_callback', array_merge($params, [
            'applicant' => $entry->user->chinese_name,
            'to'        => [
                'email'  => [$entry->user->email],
                'wechat' => [$entry->user->name],
            ],
        ]));
    }
}
