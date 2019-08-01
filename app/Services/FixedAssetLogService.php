<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 2018/10/25
 * Time: 14:47
 */

namespace App\Services;

use App\Contracts\LogContract;
use App\Models\Asset\FixedAsset;
use App\Models\Asset\FixedAssetsDetail;
use App\Models\Asset\FixedAssetsLog;
use App\Models\User;
use Exception;

class FixedAssetLogService implements LogContract
{
    public function get($Id)
    {
        $fixedAssetsLogs = FixedAssetsLog::with([
            'user' => function ($query) {
                $query->withTrashed();
            },
        ])->whereFixedAssetDetailId($Id)->orderBy('created_at', 'desc')->get();
        $returnLog       = [];
        $fixedAssetsLogs->each(function ($item, $key) use (&$returnLog) {
            $returnLog[$key]['operate_user_id']       = $item->user->chinese_name;
            $returnLog[$key]['fixed_asset_detail_id'] = $item->fixed_asset_detail_id;
            $returnLog[$key]['created_at']            = $item->created_at;
            $initData                                 = "";
            $targetData                               = "";
            foreach ($item->init_data as $k => $value) {
                if (isset($item->target_data[$k]) && ($value != $item->target_data[$k])) {
                    switch ($k) {
                        case 'apply_time':
                            if (!(strtotime($value) < 0 && !$item->target_data[$k])) {
                                if (strtotime($value) < 0) {
                                    $initData .= FixedAssetsDetail::$assetDetailMap[$k] . "：" . '/';
                                } else {
                                    $initData .= FixedAssetsDetail::$assetDetailMap[$k] . "：" . $value . '/';
                                }
                                if (strtotime($item->target_data[$k]) < 0) {
                                    $targetData .= FixedAssetsDetail::$assetDetailMap[$k] . "：" . '/';
                                } else {
                                    $targetData .= FixedAssetsDetail::$assetDetailMap[$k] . "：" . $item->target_data[$k] . '/';
                                }
                            }
                            break;
                        case 'status':
                            $initData   .= FixedAssetsDetail::$assetDetailMap[$k] . "：" . FixedAsset::$assetStatus[$value] . '/';
                            $targetData .= FixedAssetsDetail::$assetDetailMap[$k] . "：" . FixedAsset::$assetStatus[$item->target_data[$k]] . '/';
                            break;
                        case 'place_id':
                            $initData   .= FixedAssetsDetail::$assetDetailMap[$k] . "：" . FixedAsset::$assetStoragePlace[$value] . '/';
                            $targetData .= FixedAssetsDetail::$assetDetailMap[$k] . "：" . FixedAsset::$assetStoragePlace[$item->target_data[$k]] . '/';
                            break;

                        case 'user_id':
                            if ($value > 0) {
                                $userInfo = User::withTrashed()->find($value);
                                $initData .= FixedAssetsDetail::$assetDetailMap[$k] . "：" . $userInfo->chinese_name . '/';
                            } else {
                                $initData .= FixedAssetsDetail::$assetDetailMap[$k] . "：" . '/';
                            }

                            if ($item->target_data[$k] > 0) {
                                $userInfo   = User::withTrashed()->find($item->target_data[$k]);
                                $targetData .= FixedAssetsDetail::$assetDetailMap[$k] . "：" . $userInfo->chinese_name . '/';
                            } else {
                                $targetData .= FixedAssetsDetail::$assetDetailMap[$k] . "：" . '/';
                            }
                            break;
                        default:
                            $initData   .= FixedAssetsDetail::$assetDetailMap[$k] . "：" . $value . '/';
                            $targetData .= FixedAssetsDetail::$assetDetailMap[$k] . "：" . $item->target_data[$k] . '/';
                            break;
                    }
                }
            }
            $returnLog[$key]['init_data']   = trim($initData, '/');
            $returnLog[$key]['target_data'] = trim($targetData, '/');
        });
        return collect($returnLog);
    }

    public function save($data)
    {
        //数据没有变化则不保存日志信息
        $isSave = false;
        foreach ($data['init_data'] as $key => $value) {
            if (isset($data['target_data'][$key]) && ($value != $data['target_data'][$key])) {
                if ($key == 'apply_time') {
                    if (strtotime($value) < 0) {
                        continue;
                    }
                }
                $isSave = true;
                break;
            }
        }

        if ($isSave) {
            $fixedAssetsLogObj = new FixedAssetsLog();

            $logData['init_data']             = $data['init_data'] ? json_encode($data['init_data']) : "";
            $logData['target_data']           = $data['target_data'] ? json_encode($data['target_data']) : "";
            $logData['operate_user_id']       = $data['operate_user_id'] ? $data['operate_user_id'] : 0;
            $logData['fixed_asset_detail_id'] = $data['fixed_asset_detail_id'] ? $data['fixed_asset_detail_id'] : 0;
            $fixedAssetsLogObj->fill($logData);
            if (!$fixedAssetsLogObj->save()) {
                throw new Exception("资产操作日志失败！");
            }
        }
    }

    public function record($loginId, $userInfo = null, $userId, $note, $action, $initInfo = null, $type = null)
    {
        // TODO: Implement record() method.
    }
}