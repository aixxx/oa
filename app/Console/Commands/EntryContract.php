<?php

namespace App\Console\Commands;

use App\Repositories\ContractRepository;
use Illuminate\Console\Command;

class EntryContract extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Entry:contract';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '添加合同到期状态';
    protected $repository;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->repository = app()->make(ContractRepository::class);
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

    }
}
