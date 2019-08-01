<?php

namespace App\Listeners;


use App\Events\ExtraAuditEvent;
use App\Models\Performance\PerformanceTemplate;
use App\Models\User;
use App\Models\Workflow\Proc;
use App\Repositories\ContractRepository;

/**
 * 员工离职修改绩效模板   监听事件
 * Class ExtraAuditListener
 * @package App\Listeners
 */
class PerformanceListener
{
    /**
     * Create the event listener.
     *
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  ExtraAuditEvent  $event
     * @return mixed
     */
    public function handle($event)
    {
        $process = Proc::with('entry')->findOrFail($event->procsId);
        $user_id =  $process->entry->user_id;
        try {
            //获取绩效id
            $user=User::where('id',$user_id)->first();
            $contractRepository = app()->make(ContractRepository::class);
            $id = $contractRepository->getPerformanceId($user);

            $where['id']=$id;
            $info = PerformanceTemplate::where($where)->first(['usage_number','userarr']);

            if($info){
                $info=$info->toArray();
                $data['usage_number'] = 0;
                if($info['usage_number'] > 0){
                    $data['usage_number'] = $info['usage_number'] -1;
                }
                $userArr = explode(',',$info['userarr']);
                $userArr = array_diff($userArr,[$user_id]);
                $userStr = implode(',',$userArr);

                $data['userarr'] = $userStr;
                PerformanceTemplate::where($where)->update($data);
            }

        } catch (\Exception $exception) {
            report($exception);
            Log::error('员工离职修改绩效模板', ['message' => $exception->getMessage(), 'trace' => $exception->getTrace()]);
            throw $exception;
        }
    }
}
