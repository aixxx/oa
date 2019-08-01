<?php

namespace App\Http\Controllers\Api\V1\Power;

use App\Http\Controllers\Api\V1\BaseController;
use App\Repositories\Power\PowerRepository;

class PowerController extends BaseController
{
    /**
     * @var mixed
     */
    protected $respository;

    public function __construct()
    {
        $this->respository = app()->make(PowerRepository::class);
    }

    public function index()
    {
        return $this->respository->getVueAction();
    }

}
