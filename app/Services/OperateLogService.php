<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 2018/7/11
 * Time: 11:36
 */

namespace App\Services;
use App\Contracts\LogContract;
use App\Models\OperateLog;




class OperateLogService implements LogContract
{

    public function save($data)
    {
        if (!$data)
        {
            return false;
        }

        $logModel = new OperateLog;
        $logModel->fill($data);

        if ($logModel->save())
        {
            return true;
        } else {
            return false;
        }
    }

    public function get($Id)
    {
        // TODO: Implement get() method.
    }

    public function record($loginId, $userInfo = null, $userId, $note, $action, $initInfo = null,$type=null)
    {
        // TODO: Implement record() method.
    }
}