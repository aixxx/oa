<?php

namespace App\Http\Controllers\Api\V1\Message;

use App\Http\Controllers\Api\V1\ApiController;
use App\Models\Message\Message;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DetailController extends ApiController
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
        $messageId = $request->get('message_id');
        // TODO: Implement run() method.
        $message = Message::query()
            ->find($messageId);
        if($message){
            $message->read_status_title = Message::$_read_status[$message->read_status];
        }
        Message::query()
            ->where('id', '=', $messageId)
            ->where('receiver_id', '=', $uid)
            ->update(['read_status'=>Message::READ_STATUS_YES]);
        return $message;
    }
}
