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
use Exception;

class SystemInstall extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'system:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '系统数据库初始化';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try{
            DB::transaction(function (){

                $shell = "php artisan migrate --path=database/SystemInstall/migrations";
                echo "正在初始化...";
                system($shell, $status);
                //注意shell命令的执行结果和执行返回的状态值的对应关系
                if( $status ){
                    echo "ERROR!!!";
                    exit();
                } else {
                    echo "数据表 初始化完成";
                    $shell = "php artisan db:seed --class=SystemInstall";
                    echo "正在初始化数据...";
                    system($shell, $status);
                    //注意shell命令的执行结果和执行返回的状态值的对应关系
                    if( $status ){
                        echo "ERROR!!!";
                        exit();
                    } else {
                        echo "数据 初始化完成";
                    }
                }
            });
        }catch (Exception $e){
            echo $e->getMessage();
        }
    }
}
