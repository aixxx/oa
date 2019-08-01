<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/18
 * Time: 9:31
 */

namespace App\Listeners;

use App\Events\userPositiveEvent;
use App\Models\Describe\Describe;
use App\Models\User;
use App\Models\Workflow\Proc;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\Workflow\WorkflowUserSync;
use Illuminate\Support\Facades\Log;
use App\Constant\ConstFile;
use Auth;

class PositiveStartListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  BusinessTripEvent $event
     * @return void
     */
    public function handle($event)
    {
        //
        $procsId = $event->procsId;

        $process = Proc::where('id', $procsId)->first();
        $entry_data = $process->entry->entry_data->toArray();
        foreach ($entry_data as $key => $val) {
            $data[$val['field_name']] = $val['field_value'];
        }

        try {
            WorkflowUserSync::where('user_id', $data['user_id'])->delete();
            $arr = [
                'apply_user_id' => Auth::id(),
                'user_id' => $data['user_id'],
                'status' => ConstFile::WORKFLOW_USER_SYNC_STATUS_WAITING_TO_RUEN_POSITIVE,
                'content_json' => json_encode(['content' => '转正申请提交完成'], JSON_UNESCAPED_UNICODE),
                'entry_id' => $process->entry_id
            ];
            $result = (new WorkflowUserSync)->fill($arr)->save();
            if (!$result) {
                throw new \Exception('保存数据失败:' . json_encode($result, JSON_UNESCAPED_UNICODE));
            }
            Describe::create($data);
            User::where('id', $data['user_id'])->update(["is_positive" => User::STATUS_ISNO_POSITIVE]);
        } catch (\Exception $exception) {
            report($exception);
            Log::error('转正审批', ['message' => $exception->getMessage(), 'trace' => $exception->getTrace()]);
            throw $exception;
        }

    }
}