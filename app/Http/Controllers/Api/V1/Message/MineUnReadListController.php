<?php

namespace App\Http\Controllers\Api\V1\Message;

use App\Http\Controllers\Api\V1\ApiController;
use App\Models\Message\Message;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class MineUnReadListController extends ApiController
{
    public function __construct()
    {
        parent::__construct();
        $this->middleware('auth:api');
    }

    //
    public function run(Request $request)
    {
        $uid = \Auth::id();
        // TODO: Implement run() method.
        $cnt = Message::query()
            ->where('receiver_id', '=', $uid)
            ->where('type', '=', Message::MESSAGE_TYPE_NORMAL)
            ->whereNull('deleted_at')
            ->where('read_status', '=', Message::READ_STATUS_NO)
            ->count();
        return $cnt;
    }
}
