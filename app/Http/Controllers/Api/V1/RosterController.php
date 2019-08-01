<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/9
 * Time: 10:47
 */

namespace App\Http\Controllers\Api\V1;

use App\Repositories\UsersRepository;
use App\Repositories\RosterRepository;
use Auth;
use Illuminate\Http\Request;

class RosterController extends BaseController
{
    /**
     *
     * @var UserRespository
     */
    protected $users;
    protected $roster;

    //构造函数
    function __construct()
    {
        $this->roster = app()->make(RosterRepository::class);
    }

    public function rosterShow()
    {
        return $this->roster->rosterShows();
    }

    public function shows(Request $request)
    {
        $users = Auth::user();
        return $this->roster->shows($request, $users);
    }

    public function show(Request $request)
    {
        $users = Auth::user();
        return $this->roster->show($request, $users);
    }

    public function showAll(Request $request)
    {
        $users = Auth::user();
        return $this->roster->showAll($request, $users);
    }

    public function search(Request $request)
    {
        return $this->roster->search($request);
    }

    public function userSearch(Request $request)
    {
        return $this->roster->userSearch($request);
    }

    public function oneUserShow(Request $request)
    {
        $id = $request->get('id');
        return $this->roster->userFilesQuery($id);
    }

    public function userNoPerfect()
    {
        return $this->roster->userNoPerfect();
    }

    public function userNumber(Request $request)
    {
        $users = Auth::user();
        return $this->roster->userNumber($request, $users);
    }

    public function holiday(Request $request)
    {
        return $this->roster->holiday($request);
    }

    /**
     * @description 发送完善资料提醒
     * @param Request $request
     * @return false|\Illuminate\Http\JsonResponse|string
     */
    public function send_improving_data_msg(Request $request){
        $data = $request->all();
        return $this->roster->sendImprovingDataMsg($data);
    }

    /**
     * @description 发送申请转正提醒
     * @param Request $request
     * @return false|\Illuminate\Http\JsonResponse|string
     */
    public function send_turn_positive_msg(Request $request){
        $data = $request->all();
        return $this->roster->sendTurnPositiveMsg($data);
    }
}