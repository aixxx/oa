<?php

namespace App\Repositories\Power;

use App\Constant\ConstFile;
use App\Models\Power\RolesUsers;
use App\Repositories\ParentRepository;
use Mockery\CountValidator\Exception;

class PowerRepository extends ParentRepository
{
    public function model()
    {
        return RolesUsers::class;
    }

    public function getVueAction()
    {
        try {
            $user = \Auth::user();
            $this->data = $this->getUserPower($user);;
        } catch (Exception $e) {
            $this->message = $e->getMessage();
            $this->code = $e->getCode();
        }
        return $this->returnApiJson();
    }

    public function getUserPower($user)
    {
        if (!$user) {
            throw new Exception(sprintf('找不到用户对象'), ConstFile::API_RESPONSE_FAIL);
        }
        $rolesUser = RolesUsers::with('belongsToManyVueAction')->where('user_id', $user->id)->get();

        if (!$rolesUser) {
            throw new Exception(sprintf('暂无权限'), ConstFile::API_RESPONSE_FAIL);
        }
        $data = [];
        foreach ($rolesUser as $key => $val) {
            foreach ($val->belongsToManyVueAction as $k => $v) {
                $data[$key][$k]['id'] = $v->id;
                $data[$key][$k]['title'] = $v->title;
                $data[$key][$k]['vue_path'] = $v->vue_path;
            }
        }
        $result = array_reduce($data, function ($result, $value) {
            return array_merge($result, array_values($value));
        }, array());
        return $result;
    }
}
