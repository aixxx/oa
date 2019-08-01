<?php

namespace App\Repositories\Basic;

use App\Models\Basic\BasicOaType;
use Prettus\Repository\Eloquent\BaseRepository;

class BasicOaTypeRespository extends BaseRepository {


    public function model() {
        return BasicOaType::class;
    }

    public function getList($all) {
        $builder=$this->model->query();

        if(isset($all['search']) && $all['search']){
            $key = trim($all['search']);
            $builder->where('title', 'like', "%".$key."%")->orWhere('code', 'like', "%".$key."%");

        }
        return $builder->orderBy('id', 'desc')->paginate(20);

    }
    /**
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(){
        return $this->model->query();
    }
    /**
     *
     * @return \Illuminate\Database\Query\Builder
     */
    public function builder(){
        return $this->model->query();
    }




}
