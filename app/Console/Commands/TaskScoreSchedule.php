<?php

namespace App\Console\Commands;

use App\Constant\ConstFile;
use App\Models\AttendanceApi\AttendanceApiAnomaly;
use App\Models\AttendanceApi\AttendanceApiClock;
use App\Models\Comments\TotalComment;
use App\Models\MyTask\MyTask;
use App\Models\Task\TaskScore;
use App\Repositories\AttendanceApi\AttendanceApiCountRespository;
use App\Repositories\AttendanceApi\CountsRespository;
use App\Repositories\CronPushRecordRepository;
use Illuminate\Console\Command;
use Carbon\Carbon;
use DB;

class TaskScoreSchedule extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'set:task_score';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '系统自动给任务打分';
    protected $repository;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        //$this->repository = app()->make(CronPushRecordRepository::class);
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //获取所有已经完成的并且未打分的任务
        $data = MyTask::query()
            ->where('status', 3)
            ->get();
        //统计完成时间超过一周的数据
        DB::transaction(function () use ($data){
            $now = Carbon::now();
            foreach ($data as $k=>$v){
                $diff = $now->diffInDays($v->finish_time);
                if($diff >= 7){
                    $comment_data = [
                        'type'=> TotalComment::TYPE_TASK,
                        'relation_id'=> $v->id,
                        'uid' => 0,
                        'comment_text' => '系统自动打分',
                        'comment_time' => $now->toDateTimeString(),
                    ];
                    $tc = TotalComment::query()->create($comment_data);

                    //系统自动打分
                    $score_data = [
                        'pid' => $tc->id,
                        'score' => TaskScore::DEFAULT_SCORE,
                        'user_id' => $v->uid,
                        'admin_id' => 0,
                        'my_task_id' => $v->id,
                    ];
                    TaskScore::query()->create($score_data);

                    //修改任务状态为完成
                    MyTask::query()->where('id', $v->id)->update(['status'=> 4, 'comment_time'=>date('Y-m-d H:i:s', time())]);
                }
            }
        });
    }
}
