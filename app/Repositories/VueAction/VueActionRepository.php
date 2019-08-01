<?php

namespace App\Repositories\VueAction;

use App\Models\Power\VueAction;
use App\Repositories\ParentRepository;
use Prettus\Repository\Eloquent\BaseRepository;

class VueActionRepository extends ParentRepository
{

    public function model()
    {
        return VueAction::class;
    }
    /**
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(){
        return $this->model->query();
    }
}
