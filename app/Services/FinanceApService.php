<?php
namespace App\Services;

use App\Constant\CommonConstant;
use App\Http\Helpers\Mh;
use App\Models\Finance\FinanceAp;
use Illuminate\Support\Facades\Validator;

class FinanceApService
{
    const PAYMENT_METHOD = ['转账', '现金', '支票'];

    /**
     * @param $workflowData
     *
     * @throws \Exception
     */
    public static function workflowImport($workflowData)
    {
        self::validate($workflowData);
        $insertData = self::formatData($workflowData);
        $financeAp  = FinanceAp::where('entry_id', $insertData['entry_id'])->first();
        if ($financeAp) {
            throw new \Exception(sprintf('已存在有暂支记录[%d]', $financeAp->id));
        }
        $financeAp = new FinanceAp();
        $financeAp->fill($insertData);
        if (!$financeAp->save()) {
            throw new \Exception(sprintf('保存暂支记录失败[entry_id:%s]', $insertData['entry_id']));
        }
    }

    /**
     * @param $userId
     *
     * @return array
     */
    public static function getNeedRepayList($userId)
    {
        return FinanceAp::where('user_id', '=', $userId)
            ->with([
                'financeApRepayment' => function ($query) {
                    $query->orderByDesc('id')->select(['repay_amount', 'created_at']);
                },
            ])
            ->where('repay_finished', '=', CommonConstant::FLAG_IS_NOT_FINISHED)
            ->get(['entry_id', 'title', 'borrow_amount', 'repay_amount'])
            ->toArray();
    }

    /**
     * @param $workflowData
     *
     * @throws \InvalidArgumentException
     */
    private static function validate($workflowData)
    {
        $entryValidator = Validator::make($workflowData['entry'], [
            'id'      => 'required|integer',
            'user_id' => 'required|integer',
            'title'   => 'required|max:255|string',
        ]);
        if ($entryValidator->fails()) {
            throw new \InvalidArgumentException(json_encode(
                $entryValidator->errors()->getMessages(),
                JSON_UNESCAPED_UNICODE
            ));
        }

        $formDataValidator = Validator::make($workflowData['form_data'], [
            'applicant_chinese_name.value' => 'required|max:64|string',
            'company.value'                => 'required|max:64|string',
            'primary_dept.value'           => 'required|max:45|string',
            'bank_name.value'              => 'required|string',
            'bank_num.value'               => 'required|string',
            'cause.value'                  => 'required|string',
            'borrow_amount.value'          => [
                'required',
                'numeric',
                function ($attribute, $value, $fail) {
                    if (strlen($value) > 15) {
                        return $fail(sprintf('%s的值(%s)是无效的', $attribute, $value));
                    }
                    if ($value <= 0) {
                        return $fail(sprintf('%s的值(%s)是无效的', $attribute, $value));
                    }
                },
            ],
            'payment_method.value'         => [
                'required',
                'string',
                'max:32',
                function ($attribute, $value, $fail) {
                    if (!in_array($value, self::PAYMENT_METHOD)) {
                        return $fail(sprintf('%s的值(%s)是无效的', $attribute, $value));
                    }
                },
            ],
        ], [
            'required' => ':attribute不能为空',
            'numeric'  => ':attribute必须是数字',
            'max'      => ':attribute长度（数值）不应该大于 :max',
        ]);
        if ($formDataValidator->fails()) {
            throw new \InvalidArgumentException(json_encode(
                $formDataValidator->errors()->getMessages(),
                JSON_UNESCAPED_UNICODE
            ));
        }
    }

    /**
     * @param $workflowData
     *
     * @return array
     */
    private static function formatData($workflowData)
    {
        return [
            'entry_id'          => $workflowData['entry']['id'],
            'title'             => $workflowData['entry']['title'],
            'user_id'           => $workflowData['entry']['user_id'],
            'user_name'         => $workflowData['form_data']['applicant_chinese_name']['value'],
            'department'        => $workflowData['form_data']['primary_dept']['value'],
            'company_name'      => $workflowData['form_data']['company']['value'],
            'payment_method'    => $workflowData['form_data']['payment_method']['value'],
            'borrow_amount'     => Mh::y2f($workflowData['form_data']['borrow_amount']['value']),
            'cause'             => $workflowData['form_data']['cause']['value'],
            'memo'              => $workflowData['form_data']['memo']['value'] ?? '',
            'receive_card_bank' => encrypt($workflowData['form_data']['bank_name']['value']),
            'receive_card_num'  => encrypt($workflowData['form_data']['bank_num']['value']),
            'file_storage_id'   => $workflowData['form_data']['file_upload']['value'] ?? 0,
        ];
    }
}
