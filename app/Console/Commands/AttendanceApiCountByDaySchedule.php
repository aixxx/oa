<?php

namespace App\Console\Commands;

use App\Constant\ConstFile;
use App\Models\AttendanceApi\AttendanceApiAnomaly;
use App\Models\AttendanceApi\AttendanceApiClock;
use App\Repositories\AttendanceApi\AttendanceApiCountRespository;
use App\Repositories\AttendanceApi\CountsRespository;
use App\Repositories\CronPushRecordRepository;
use Illuminate\Console\Command;
use Carbon\Carbon;

class AttendanceApiCountByDaySchedule extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'attendanceapi:count';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '考勤按天统计';
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
        $dates = Carbon::yesterday()->toDateString();
        $respository = app()->make(CountsRespository::class);
        $res = $respository->countByDay(['dates'=> $dates]);
        $info = json_decode($res->getContent(), true);
        $now = Carbon::now()->toDateTimeString();
        //正常
        $data = [];
        foreach ($info["data"]["normal"] as $k=>$v){
            $data[] = [
                'user_id'=> $v["user_id"],
                'dates'=> $v['dates'],
                'anomaly_type'=> AttendanceApiAnomaly::NORMAL,
                'created_at'=> $now,
                'updated_at'=> $now,
                'clock_nums'=> $v['clock_nums'],
            ];
        }
        //旷工
        foreach ($info["data"]["absenteeism"] as $k=>$v){
            $data[] = [
                'user_id'=> $v["user_id"],
                'dates'=> $v['dates'],
                'anomaly_type'=> AttendanceApiAnomaly::ABSENTEEISM,
                'created_at'=> $now,
                'updated_at'=> $now,
                'clock_nums'=> $v['clock_nums'],
            ];
        }
        //缺卡
        foreach ($info["data"]["missing"] as $k=>$v){
            $data[] = [
                'user_id'=> $v["user_id"],
                'dates'=> $v['dates'],
                'anomaly_type'=> AttendanceApiAnomaly::MISSING,
                'created_at'=> $now,
                'updated_at'=> $now,
                'clock_nums'=> $v['clock_nums'],
            ];
        }
        AttendanceApiAnomaly::query()->insert($data);
    }
}
