<?php

namespace App\Console\Commands;
use App\Repositories\Meeting\MeetingRepository;
use Illuminate\Console\Command;
class Meeting extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:meetingnews';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '会议提示消息发送';
    protected $repository;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->repository = app()->make(MeetingRepository::class);
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->repository->remindTime();
    }

}
