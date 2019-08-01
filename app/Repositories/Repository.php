<?php

namespace App\Repositories;
use Prettus\Repository\Eloquent\BaseRepository as Base;

//use Bosnadev\Repositories\Eloquent\Repository as Base ;

class Repository extends Base {



    public function model() {
        
    }
    
    /**
     * 
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function entity(){
        return $this->model;
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
    
    /**
     * 
     * @return \Illuminate\Database\Connection
     */
    public function getConnection(){
        return $this->builder()->getConnection();
    }

    /**
     * @param array $data
     * @param $id
     * @param string $attribute
     * @return mixed
     */
    public function update(array $data, $id, $attribute="id") {
        if($attribute=="id"){
            return $this->updateRich($data, $id);
        }
        return parent::update($data,$id,$attribute);
    }

    /**
     * 拼装like字符串
     * @author wucheng
     * @param $key
     * @param $fileds
     * @return string
     */
    public function allFieldsLike($key, $fileds)
    {
        $str = "";
        foreach ($fileds as $f => $i) {
            $str .= "`$i` LIKE '%$key%' OR";
        }
        return rtrim($str, "OR");
    }

    /**
     * 列出模型中的所有字段
     * @author wucheng
     * @param $model
     * @return mixed
     */
    public function allFields($model)
    {
        $fillable = $model->fillable;
        //array_push($fillable,"id");
        return $fillable;
    }

}

