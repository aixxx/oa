<?php

namespace App\Repositories\Basic;

use App\Models\Basic\BasicOaOption;
use Prettus\Repository\Eloquent\BaseRepository;


class BasicOaOptionRespository extends BaseRepository {

    public function model() {
        return BasicOaOption::class;
    }
}
