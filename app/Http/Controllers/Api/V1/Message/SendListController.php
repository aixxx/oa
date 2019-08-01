<?php

namespace App\Http\Controllers\Api\V1\Message;

use App\Http\Controllers\Api\V1\ApiController;
use App\Models\Message\Message;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SendListController extends ApiController
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
        $messages = Message::query()
            ->where('sender_id', '=', $uid)
            ->with('receiver')
            ->orderBy('created_at', 'desc')
            ->get();
        foreach ($messages as $message){
            $message->read_status_title = Message::$_read_status[$message->read_status];
        }
        return $messages;
    }
}
