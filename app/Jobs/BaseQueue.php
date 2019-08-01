<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 2018/11/27
 * Time: 11:10
 */

namespace App\Jobs;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

abstract class BaseQueue implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    const QUEUE_ORDER_HIGH_THREE = 'high-3';
    const QUEUE_ORDER_HIGH_TWO   = 'high-2';
    const QUEUE_ORDER_HIGH_ONE   = 'high-1';
    const QUEUE_ORDER_MIDDLE     = 'middle';
    const QUEUE_ORDER_LOW_THREE  = 'low-3';
    const QUEUE_ORDER_LOW_TWO    = 'low-2';
    const QUEUE_ORDER_LOW_ONE    = 'low-1';

    /**
     * 超时时间。
     *
     * @var int
     */
    public $timeout = 1200;

    public $tries    = 3;
    public $order    = self::QUEUE_ORDER_MIDDLE; //队列名 优先级 high-3,high-2,high-1,middle,low-3,low-2,low-1
    public $postpone = 0; //延迟


    private $arguments;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(array $arguments)
    {
        //
        $this->connection = 'database';
        $this->queue      = $this->order;
        $this->delay      = $this->postpone;
        foreach ($arguments as $key => $value) {
            $this->$key = $value;
        }
        $this->arguments = $arguments;
    }


    abstract public function handle();

    /**
     * 执行失败的任务。
     *
     * @param  Exception $exception
     * @return void
     */
    public function failed(Exception $exception)
    {
        // 给用户发送失败的通知等等...
        report($exception);
    }

    /**
     * @param null $name
     * @param null $arguments
     * @return array
     */
    public function getArguments()
    {
        return $this->arguments;
    }
}
