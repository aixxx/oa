<?php

namespace App\Repositories\VueAction;

use App\Models\Power\Routes;
use App\Repositories\ParentRepository;

class ApiRoutesRepository extends ParentRepository
{

    public function model()
    {
        return Routes::class;
    }

    public function insert($data){
        return Routes::query()->insert($data);
    }
}
