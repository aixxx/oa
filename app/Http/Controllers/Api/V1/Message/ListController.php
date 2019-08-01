<?php

namespace App\Http\Controllers\Api\V1\Message;

use App\Http\Controllers\Api\V1\ApiController;
use App\Models\Message\Message;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ListController extends ApiController
{

    public function __construct()
    {
        parent::__construct();
        $this->middleware('auth:api');
    }
    //
    public function run(Request $request)
    {
        $page = $request->input('page', 1);
        $size = $request->input('size', 20);
        // TODO: Implement run() method.
        $uid = \Auth::id();
        $messages = Message::query()
            ->where('sender_id', '!=', -1)
            ->where('type', '=', Message::MESSAGE_TYPE_NORMAL)
            ->where(function($query) use($uid){
                /** @var Builder $query */
                $query->orWhere('sender_id', '=', $uid);
                $query->orWhere('receiver_id', '=', $uid);
            })
            ->has('sender')
            ->has('receiver')
            ->with(['receiver', 'sender'])
            ->orderBy('id', 'desc')
            ->paginate($size, ['*'], 'page', $page);
        $data = $messages->items();
        foreach ($data as $k=> &$message){
            $message->read_status_title = Message::$_read_status[$message->read_status];
        }
        return $messages;
    }
}
