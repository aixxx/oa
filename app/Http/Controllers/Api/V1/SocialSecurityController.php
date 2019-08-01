<?php

namespace App\Http\Controllers\Api\V1;

use App\Repositories\SocialSecurityRepository;
use Request;
use Auth;

class SocialSecurityController extends BaseController
{
    /**
     * @var mixed
     */
    protected $repository;

    public function __construct()
    {
        $this->repository = app()->make(SocialSecurityRepository::class);
    }

    public function index()
    {
        return $user = $this->repository->query()->get();
    }

    public function create()
    {
        $all = Request::all();
        $user = Auth::user();
        return $this->repository->created($all, $user);
    }

    public function delete()
    {
        $id = Request::get('id');
        $user = Auth::user();
        return $this->repository->deleted($id, $user);
    }

    public function showusersocialsecurity()
    {
        $user = Auth::user();
        return $this->repository->fetchUserSocialSecurity($user);
    }

    public function createparticipant()
    {
        $all = Request::all();
        $user = Auth::user();
        return $this->repository->createParticipant($all, $user);
    }

    public function getUserSocialSecurity()
    {
        return $this->repository->getUserSocialSecurity(64);
    }
}
