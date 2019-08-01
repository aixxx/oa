<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\User;
use App\Repositories\SealsRepository;
use Request;
use App\Services\OSS;
use Exception;
use Response;
use Auth;
use DB;


class SealsController extends BaseController
{
    protected $repository;

    public function __construct()
    {
        $this->repository = app()->make(SealsRepository::class);
    }

    //添加印章类型
    public function seals_type_add()
    {
        $all = Request::all();
        $user = Auth::user();
        return $this->repository->seals_type_add($all,$user);
    }

    //上传印章
    public function upload_seals()
    {
        $all = Request::all();
        $user = Auth::user();
        return $this->repository->upload_seals($all,$user);
    }
}