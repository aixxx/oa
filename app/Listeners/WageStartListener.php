<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/18
 * Time: 9:31
 */

namespace App\Listeners;

use App\Events\WageStartEvent;
use App\Models\Describe\Describe;
use App\Models\User;
use App\Models\Workflow\Proc;
use App\Constant\ConstFile;
use App\Models\Workflow\WorkflowUserSync;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Auth;

class WageStartListener
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
            $res = WorkflowUserSync::where('user_id', $data['user_id'])->delete();
            if ($res) {
                $arr = [
                    'apply_user_id' => Auth::id(),
                    'user_id' => $data['user_id'],
                    'status' => ConstFile::WORKFLOW_USER_SYNC_STATUS_PAY_PACKAGE,
                    'content_json' => json_encode(['content' => '定薪申请提交完成'], JSON_UNESCAPED_UNICODE),
                    'entry_id' => $process->entry_id
                ];
                $result = (new WorkflowUserSync)->fill($arr)->save();
                if (!$result) {
                    throw new \Exception('保存数据失败:' . json_encode($result, JSON_UNESCAPED_UNICODE));
                }
            }
            $arr = ['wage_classes' => $data['wage_classes'], 'salary_scale' => $data['salary_scale'], 'points_scale' => $data['points_scale']];
            Describe::where('user_id', $data['user_id'])->update($arr);
        } catch (\Exception $exception) {
            report($exception);
            Log::error('转正审批', ['message' => $exception->getMessage(), 'trace' => $exception->getTrace()]);
            throw $exception;
        }
    }
}