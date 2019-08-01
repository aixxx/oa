<?php

namespace App\Http\Controllers\Rpc;
use App\Http\Controllers\Controller;
use Hprose\Http\Server;


class HproseController extends Controller
{
    public function __construct()
    {
        $this->server=new Server();
        $this->server->addInstanceMethods($this);

    }
    public function start() {
        $this->server->start();
    }
}
