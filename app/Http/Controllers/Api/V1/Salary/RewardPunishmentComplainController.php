<?php

namespace App\Http\Controllers\Api\V1\Salary;

use App\Constant\ConstFile;
use App\Http\Controllers\Api\V1\BaseController;
use App\Repositories\Salary\RewardPunishmentComplainRepository;
use Request;

class RewardPunishmentComplainController extends BaseController
{
    public $repository;
    //构造函数
    function __construct() {
        parent::__construct();
        $this->repository = app()->make(RewardPunishmentComplainRepository::class);
    }
    public function add(){
        $data = Request::all();
        return $this->repository->add($data);
    }

}
