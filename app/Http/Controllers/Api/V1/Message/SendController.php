<?php

namespace App\Http\Controllers\Api\V1\Message;

use App\Http\Controllers\Api\V1\ApiController;
use App\Models\Message\Message;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SendController extends ApiController
{
    public function __construct()
    {
        parent::__construct();
        $this->middleware('auth:api');
    }
    //
    public function run(Request $request)
    {
        // TODO: Implement run() method.
        $uid = \Auth::id();
        $receiver_id = $request->get('receiver_id');
        $receiver_id = intval($receiver_id);
        $content = $request->get('content');
        $title = $request->get('title');

        $rules = [
            'receiver_id'=> 'required',
            'content'=> 'required',
//            'title'=> 'required',
        ];
        $messages = [
            'receiver_id.required' => '接收人不能为空',
            'content.required' => '消息内容不能为空',
//            'title.required' => '消息内容标题不能为空',
        ];
        $this->checkParam($request, $rules, $messages);

        $data = [
            'sender_id' => $uid,
            'receiver_id' => $receiver_id,
            'content' => $content,
            'type' => Message::MESSAGE_TYPE_NORMAL,
            'title' => $title,
        ];
        $message = Message::query()->create($data);
        return $message;
    }
}
