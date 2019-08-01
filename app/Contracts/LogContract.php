<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 2018/6/22
 * Time: 17:58
 */
namespace App\Contracts;


interface LogContract
{
    public function record($loginId, $userInfo=null,$userId,$note,$action,$initInfo=null,$type=null);

    public function get($Id);

    public function save($data);
}