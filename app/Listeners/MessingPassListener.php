<?php

namespace App\Listeners;


use App\Events\ExtraAuditEvent;
use App\Models\Meeting\Meeting;
use App\Models\Workflow\Proc;

/**
 * 会议审批通过监听事件
 * Class ExtraAuditListener
 * @package App\Listeners
 */
class MessingPassListener
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
        //var_dump(111);die;
        $procsId = $event->procsId;
        $process = Proc::where('id', $procsId)->first();
        $entry_id = $process->entry_id;
        try {
            $where['entrise_id']=$entry_id;
            $data['status']=Meeting::API_STATUS_SUCCESS;
            $data['updated_at']=date('Y-m-d H:i:s',time());
            Meeting::where($where)->update($data);
            $info =  Meeting::where($where)->first()->toArray();
            $whereOne[]=['start','<=',$info['start']];
            $whereOne[]=['end','>',$info['start']];
            $whereOne['day']=trim($info['day']);
            $whereOne[]=['status','=',Meeting::API_STATUS_EXAMINE];
            //$whereOne[]=['entrise_id','!=',$entry_id];

            $wheres[]=['start','<',$info['end']];
            $wheres[]=['end','>=',$info['end']];
            $list = Meeting::where($whereOne)->orWhere($wheres)->where('entrise_id','!=',$entry_id)->get()->toArray();
            if($list){
                foreach ($list as $ke=>$lis){
                    $entrise_id=$lis['entrise_id'];
                    $proc = Proc::where('entry_id', $entrise_id)->where('status', 0)->first(['id','user_id','user_name']);
                    //var_dump($proc);die;
                    $datas['auditor_id']=$proc->user_id;
                    $datas['auditor_name']=$proc->user_name;
                    $datas['origin_auth_id']=$proc->user_id;
                    $datas['origin_auth_name']=$proc->user_name;
                    $datas['status']=-1;// 驳回
                    $datas['content']='你的会议申请与其它通过的会议有冲突请重新申请！';
                    $whereone['id']=$proc['id'];
                    Proc::where($whereone)->update($datas);
                    //给申请人发通知

                    //修改会议状态
                    $whereTow['id']=$lis['id'];
                    $data['status']=Meeting::API_STATUS_REFUSE;
                    $data['updated_at']=date('Y-m-d H:i:s',time());
                    Meeting::where($whereTow)->update($data);
                }
            }

        } catch (\Exception $exception) {
            report($exception);
            //Log::error('会议申请审批通过', ['message' => $exception->getMessage(), 'trace' => $exception->getTrace()]);
            throw $exception;
        }
    }
}
