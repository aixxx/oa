<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Repositories\EntryRepository;

class PendingUsersController extends BaseController
{
    /**
     * @var mixed
     */
    protected $repository;

    public function __construct()
    {
        $this->repository = app()->make(EntryRepository::class);
    }

    public function create(Request $request)
    {
        return $this->repository->createPendingUserFlow($request);
    }

    public function showPendingUserForm(Request $request)
    {
        return $this->repository->showPendingUsersForm($request);
    }

    public function fetchPosition(Request $request)
    {
        return $this->repository->fetchPosition($request);
    }
}
