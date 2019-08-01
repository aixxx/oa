<?php

namespace App\Http\Controllers\Api\V1;

use App\Repositories\SchedulesRepository;
use Illuminate\Http\Request;
use Request as OurRequest;
use JWTAuth;

class SchedulesController extends BaseController
{
    protected $repository;

    public function __construct()
    {
        $this->repository = app()->make(SchedulesRepository::class);
    }

    public function create()
    {
        $a = OurRequest::all();
        return $this->repository->addRecord(OurRequest::all());
    }

    public function show(Request $request)
    {
        $id = $request->get('id');
        return $this->repository->fetchOne($id);
    }

//    public function index(){
//        return $user = $this->repository->query()->get();
//    }

    public function fetchSchedules()
    {
        return $this->repository->fetchSchedules(OurRequest::all());
    }

    public function fetchUserSchedules()
    {
        return $this->repository->fetchUserSchedules(OurRequest::all());
    }

    public function fetchConfirmStatus()
    {
        return $this->repository->fetchConfirmPerson(OurRequest::all());
    }

    public function edit()
    {
        return $this->repository->updateRecord(OurRequest::all());
    }

    public function confirm()
    {
        return $this->repository->updateUserSchedule(OurRequest::all());
    }

    public function setUserSchedulePromptType()
    {
        return $this->repository->setUserSchedulePromptType(OurRequest::all());
    }
//setUserSchedulePromptType
    public function delete()
    {

    }
}
