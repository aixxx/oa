<?php

namespace App\Console;

use App\Console\Commands\AttendanceVacationForAnnual;
use App\Console\Commands\PushMessage;
use App\Http\Helpers\Dh;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //AttendanceVacationForAnnual::class,
        //PushMessage::class,
        Commands\WorkermanHttpServer::class
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule $schedule
     *
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $logPath = storage_path('logs/commands-' . date('Y-m-d') . '.log');

//        $schedule->command('send:mail')->everyFiveMinutes()->withoutOverlapping();
//        $schedule->command('wechat:message')->everyFiveMinutes()->withoutOverlapping();
        $schedule->command('attendanceapi:count')
            ->appendOutputTo($logPath)
            ->dailyAt("06:00")->withoutOverlapping();
        $schedule->command('set:achievements')
            ->appendOutputTo($logPath)
            ->dailyAt("14:30")->withoutOverlapping();
        $schedule->command('send:meetingnews')
            ->appendOutputTo($logPath)
            ->dailyAt("13:00")->withoutOverlapping();
        //$schedule->command('Entry:contract')->daily("06:00");
        $schedule->command('set:task_score')
            ->appendOutputTo($logPath)
            ->dailyAt("06:30")->withoutOverlapping();
        $schedule->command('user_vacation:init')
            ->appendOutputTo($logPath)
            ->everyThirtyMinutes()->withoutOverlapping();
        $schedule->command('push:message')
            ->appendOutputTo($logPath)
            ->everyMinute()->withoutOverlapping();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
