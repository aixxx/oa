<?php

namespace App\Repositories\AttendanceApi;

use App\Models\AttendanceApi\AttendanceApiStaff;
use App\Repositories\Repository;

class AttendanceApiStaffRespository extends Repository {

    public function model() {
        return AttendanceApiStaff::class;
    }
}
