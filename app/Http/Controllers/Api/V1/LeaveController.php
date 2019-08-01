<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Repositories\LeaveRepository;
use Auth;

//use App\Repositories\EntryRepository;

class LeaveController extends BaseController
{
    /**
     * @var mixed
     */
    protected $repository;

    public function __construct()
    {
        $this->repository = app()->make(LeaveRepository::class);
    }

    public function storeFireForm(Request $request)
    {
        return $this->repository->storeFireUserForm($request);
    }

    public function showFireForm(Request $request)
    {
        return $this->repository->showFireUsersForm($request);
    }

    public function showLeaveHandOverForm()
    {
        return $this->repository->showLeaveHandOverForm();
    }

    public function showActiveLeaveForm()
    {
        return $this->repository->showActiveLeaveForm();
    }

    public function checkCanApplyLeaveEntry()
    {
        return $this->repository->checkCanApplyEntry(Auth::id());
    }
}
