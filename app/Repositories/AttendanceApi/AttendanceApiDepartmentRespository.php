<?php

namespace App\Repositories\AttendanceApi;

use App\Models\AttendanceApi\AttendanceApiDepartment;
use App\Repositories\Repository;

class AttendanceApiDepartmentRespository extends Repository {

    public function model() {
        return AttendanceApiDepartment::class;
    }
}
