<?php

namespace App\Http\Controllers\Api\V1\Message;

use App\Http\Controllers\Api\V1\ApiController;
use App\Models\Message\Message;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DeleteController extends ApiController
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
        if($messageId){
            $obj = Message::query()->where('receiver_id', '=', $uid)->find($messageId);
            $obj->receiver_status = 1;
            $obj->save();
            $obj = Message::query()->where('sender_id', '=', $uid)->find($messageId);
            $obj->sender_status = 1;
            $obj->save();
        }
        return true;
    }
}
