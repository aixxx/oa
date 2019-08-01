<?php

namespace App\Repositories;

use App\Constant\ConstFile;
use Prettus\Repository\Eloquent\BaseRepository;
use Illuminate\Container\Container as Application;
use Hprose\Http\Client;

class ParentRepository extends BaseRepository
{
    protected $message = ConstFile::API_RESPONSE_SUCCESS_MESSAGE;
    protected $code = ConstFile::API_RESPONSE_SUCCESS;
    protected $data = [];

    public function __construct(Application $app)
    {
        parent::__construct($app);
    }

    public function model()
    {
        //return Entry::class;
    }

    protected function returnApiJson()
    {
        return returnJson($this->message, $this->code, $this->data);
    }

    protected function fetchActiveValue($key = null, $customInfo)
    {
        return array_key_exists($key, $customInfo) ? ($customInfo)[$key] : null;
    }

    protected function fetchTemplateWithCustomData(array $template, $customInfo)
    {
        return collect($template['template_form'])->transform(function ($item, $key) use ($customInfo) {
            $item['field_value'] = $this->fetchActiveValue($item['field'], $customInfo);
            return $item;
        })->all();
    }
    /**
     * 生成单号公共方法
     */
    public function  getCodes($code='CG'){
        $data=date('ymdHis',time());
        $codes=$code.$data. mt_rand(1000, 9999);
        return $codes;
    }


}
