<?php

namespace App\Console\Commands;
use Illuminate\Console\Command;
use App\Repositories\Performance\PerformanceTemplateRepository;
class BatchApply extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'set:achievements';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '给所有人发绩效申请';
    protected $repository;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->repository = app()->make(PerformanceTemplateRepository::class);
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->repository->setBatchApply();
    }

}
