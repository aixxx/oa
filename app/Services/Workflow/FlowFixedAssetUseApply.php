<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 2018/9/27
 * Time: 18:14
 */

namespace App\Services\Workflow;

use App\Models\Workflow\Entry;
use UserFixException;
class FlowFixedAssetUseApply implements FlowInterface
{
    /**
     * 校验表单数据合法性
     * @param Entry $entry
     */
    public function checkValidate(Entry $entry)
    {

        $customNotRequireList = [
            'primary_dept',
            'applicant_chinese_name',
            'apply_reason',
            'applyer_dept_lvl',
            'is_leader',
        ];
        $tableData            = $entry->entry_data->whereNotIn('field_name', $customNotRequireList);

        $tableData->each(function ($item, $key) {
            $everyLineData = json_decode($item->field_value, true);
            if (!$everyLineData['name']) {
                throw new UserFixException("设备名称必填！");
            }

            if (!$everyLineData['spec']) {
                throw new UserFixException("设备规格必填！");
            }

            if (!$everyLineData['num']) {
                throw new UserFixException("申请数量必填！");
            }
        });
    }
}