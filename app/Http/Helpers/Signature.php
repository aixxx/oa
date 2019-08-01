<?php
namespace App\Http\Helpers;

class Signature
{
    private $md5Key;

    public function __construct($md5Key)
    {
        $this->md5Key = $md5Key;
    }

    protected function buildSignWord($arr_params)
    {
        ksort($arr_params);
        $sign_word = '';
        foreach ($arr_params as $key => $value) {
            if (is_array($value)) {
                $value = $this->buildSignWord($value);
            }
            if ($value === '' or is_null($value)) {
                continue;
            }
            $sign_word .= "$key=$value&";
        }
        return $sign_word;
    }

    public function sign($arr_params)
    {
        unset($arr_params['sign']);
        $sign_word = $this->buildSignWord($arr_params) . $this->md5Key;
        return md5($sign_word);
    }

    public function attachSign($arr_params)
    {
        if (!isset($arr_params['sign'])) {
            $sign = $this->sign($arr_params);
            $arr_params['sign'] = $sign;
            return $arr_params;
        }

        return $arr_params;
    }
}
