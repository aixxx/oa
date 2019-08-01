<?php

namespace App\Console\Commands;

use App\Models\Message\CronPushRecord;
use App\Models\Message\Message;
use App\Notifications\MessageNotification;
use App\Repositories\CronPushRecordRepository;
use Illuminate\Console\Command;

class PushMessage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'push:message';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '推送消息通知';
    protected $repository;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->repository = app()->make(CronPushRecordRepository::class);
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
//        $this->createSeed();
        $objects = $this->repository->getList();
        foreach ($objects as $object ){
            /** @var CronPushRecord $object */
            $noticeType = $object->notice_type;
            $receiver_ids = explode(',', $object->target_uids);
            foreach ($receiver_ids as $receiver_id){
                $data = [
                    'receiver_id' => $receiver_id,
                    'sender_id' => Message::SENDER_SYSTEM_DEFAULT,
                    'content' => $object->content,
                    'type' => $object->type,
                    'type_pid' => $object->type_pid,
                ];
                Message::query()->create($data);
            }
            if($object->diff_minute <= 0 && $object->push_times + 1 >= $object->times){  //执行间隔大于等于1分钟 需要手动失效
                $object->is_expire = 1;
            }
            $object->push_times = $object->push_times + 1;
            $object->push_at = $object->push_at + $object->diff_minute * 60;
            $object->save();
        }
    }

    public function createSeed()
    {
// 新增消息通知 推送
        $obj = new CronPushRecord();
        $obj->push_at = 12345678;  //推送的时间 单位：s
        $obj->type = CronPushRecordRepository::PUSH_TYPE_SCHEDULE;  //1、任务 2、日程
        $obj->type_pid = 2;  //任务主键id or 日程主键id
        $obj->type_title = '日程/任务';
        $obj->content = '您有一个日程：xxxx待处理'; // 推送内容
        $obj->target_uids = '1,2,3';//逗号分隔
        /* 以下非必填 */
        $obj->diff_minute = 0;
        $obj->channel = 1;//推送频道（暂时没有用到）
        $obj->notice_type = CronPushRecordRepository::NOTICE_TYPE_INNER;  //推送渠道： 1、站内 2、手机
        $obj->times = 1; //执信次数
        $obj->push_times = 0; //已推送次数
        $obj->is_expire = 0;  //1、失效 0、有效
        $obj->remark = '';
        $res = $this->repository->insertRecord($obj);
    }
}
