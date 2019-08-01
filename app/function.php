<?php
/**
 * Created by PhpStorm.
 * User: aike
 * Date: 18/9/10
 * Time: 上午10:42
 */

/**
 * 解密，如果是当前登录用户信息错误，就报错，不然显示密文
 * @param $value
 * @param int $user_id
 * @return mixed
 * @throws Exception
 * @author hurs
 */
function decrypt_no_user_exception($value, $user_id = 0)
{
    try {
        return app('encrypter')->decrypt($value);
    } catch (Exception $e) {
        if (Auth::id() && $user_id == Auth::id()) {
            throw new UserFixException('系统检查到您的数据存在问题，请联系管理员');
        }
        return $value;
    }
}

function encrypt_with_password($password, $data)
{
    $encrypt = new \Illuminate\Encryption\Encrypter($password, 'AES-256-CBC');
    return $encrypt->encrypt($data);
}

function decrypt_with_password($password, $data)
{
    try {
        $encrypt = new \Illuminate\Encryption\Encrypter($password, 'AES-256-CBC');
        return $encrypt->decrypt($data);
    } catch (Exception $e) {
        throw new UserFixException('您的密码无法解密这份数据');
    }
}

//validate_mobile_format函数是自己写的手机号码验证函数
if (!function_exists('validate_mobile_format')) {
    /**
     * 验证手机号格式是否合法
     * @param string $mobile
     *
     * @return bool
     */
    function validate_mobile_format($mobile)
    {
        return preg_match('#^13[\d]{9}$|^14[0-9]\d{8}|^15[0-9]\d{8}$|^16[0-9]\d{8}$|^17[0-9]\d{8}|^18[0-9]\d{8}$|^19[0-9]\d{8}$#', $mobile);
    }
}
/**
 * API返回接口数据
 */
if (!function_exists('returnJson')) {
    /**
     * API 返回使用
     * @param string $message
     * @param int $code
     * @param array $data
     * @return \Illuminate\Http\JsonResponse
     */
    /**
     * 格式化API返回接口数据
     */
    if (!function_exists('returnJson')) {
        function returnJson($message = 'ok', $code = 200, $data = [])
        {
            header('Content-type: application/json; charset=utf-8');
            if ($code != 200) {
                return json_encode(['code' => $code, 'message' => $message, 'data' => $data], JSON_UNESCAPED_UNICODE);//JSON_UNESCAPED_UNICODE的作用是当返回结果中有中文的时候直接展示中文,更加友好。
            }
            return response()->json(['code' => $code, 'message' => $message, 'data' => $data]);
        }
    }
}

/**
 *
 * @todo 给对象赋值，用于排除“property of non-object”错误
 * @author wucheng
 * @return null
 */
function Q()
{
    $args = func_get_args();

    if (!isset($args[0])) {
        return null;
    }

    $count = count($args);
    if ($count > 1) {
        $obj = null;
        if (isset($args[0]->{$args[1]})) {
            $obj = $args[0]->{$args[1]};
        } elseif (isset($args[0][$args[1]])) {
            $obj = $args[0][$args[1]];
        }
        if ($count > 2) {
            for ($i = 2; $i < $count; $i++) {
                if (!isset($obj->{$args[$i]})) {
                    if (isset($obj[$args[$i]])) {
                        $obj = $obj[$args[$i]];
                    } else {
                        return null;
                    }
                } else {
                    $obj = $obj->{$args[$i]};
                }
            }
        }
        return $obj;
    } else {
        return isset($args[0]) ? $args[0] : null;
    }
}


/**
 * @百分比
 * @param array $arr
 * @return int
 */
function percent($arr = [])
{
    $is_empty = 17;
    if (is_array($arr)) {
        foreach ($arr as $k => $v) {
            if (empty($v)) {
                $is_empty = 0;
                break;
            }
        }
        return $is_empty;
    }
}

/**
 * @param $param
 * @param bool $key
 * @param null $ret
 * @param bool $sub
 * @return null
 */
function emptyParam($param, $key = false, $ret = null, $sub = false)
{
    if ($sub === false) {
        if ($key) {
            return isset($param[$key]) && !empty($param[$key]) ? $param[$key] : $ret;
        }
        return isset($param) && !empty($param) ? $param : $ret;
    } else {
        if ($key) {
            return isset($param[$sub][$key]) && !empty($param[$sub][$key]) ? $param[$sub][$key] : $ret;
        }
        return isset($param[$sub]) && !empty($param[$sub]) ? $param[$sub] : $ret;
    }
}

/**
 * 百分比计算
 */
if (!function_exists('percentage')) {
    /**
     * 格式化API返回接口数据
     */
    if (!function_exists('percentage')) {
        /**
         * 百分比计算
         * @param int $total
         * @param int $number
         * @return \Illuminate\Http\JsonResponse
         */
        function percentage($total = 0, $number = 0)
        {
            if ($total == 0) {
                return false;
            }
            return ($number / $total) * 100;
        }
    }
}
//二维数组去重
function unique_arr($a)
{

    foreach ($a[0] as $k => $v) {  //二维数组的内层数组的键值都是一样，循环第一个即可
        $ainner[] = $k;   //先把二维数组中的内层数组的键值使用一维数组保存
    }


    foreach ($a as $k => $v) {
        $v = join(",", $v);    //将 值用 顿号连接起来
        $temp[$k] = $v;
    }


    $temp = array_unique($temp);    //去重

    foreach ($temp as $k => $v) {
        $a = explode(",", $v);   //拆分后的重组
        $arr_after[$k] = array_combine($ainner, $a);  //将原来的键与值重新合并
    }
    return $arr_after;
}

//生成唯一编号
function getCode($type = '')
{
    return $type . date('YmdHis') . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);
}


/*
 * 二维数组指定字段作为健名
 */
function array_field_as_key($arr, $field, $unset_field = [])
{
    if (!is_array($arr) || empty($arr) || empty($field)) return [];

    $result = array();
    foreach ($arr as $k => $v) {
        $key = $v[$field];
        if (!empty($unset_field)) {
            foreach ($unset_field as $vv) {
                unset($v[$vv]);
            }
        }
        $result[$key] = $v;
    }
    return $result;
}


/*
 * 二维数组指定字段作为健名
 */
function array_field_as_keys($arr, $field, $unset_field = [])
{
    if (!is_array($arr) || empty($field)) return false;

    $result = array();
    foreach ($arr as $k => $v) {
        $vs=$v;
        $key = $v[$field];
        if (!empty($unset_field)) {
            foreach ($unset_field as $vv) {
                unset($v[$vv]);
            }
        }
        if(empty($result[$key])){
            $result[$key]= $v;
            $result[$key]['list'][] = $vs;
        }else{
            $result[$key]['list'][] = $vs;
        }
    }
    return $result;
}



/*
 * 将时间戳转换成天时分秒
 * */
function time2string($second, $arr)
{
    $day = floor($second / (3600 * 24));
    $second = $second % (3600 * 24);//除去整天之后剩余的时间
    $hour = floor($second / 3600);
    $second = $second % 3600;//除去整小时之后剩余的时间
    $minute = floor($second / 60);
    $second = $second % 60;//除去整分钟之后剩余的时间

    $res = '';//返回字符串
    foreach ($arr as $k => $v) {
        $res .= $$k . $v;
    }

    return $res;//return $day.'天'.$hour.'小时'.$minute.'分'.$second.'秒';
}

//调试输出 SQL 语句的简便方法
if (!function_exists('getSql')) {
    function getSql()
    {
        DB::listen(function ($sql) {
            dump($sql);
            $singleSql = $sql->sql;
            if ($sql->bindings) {
                foreach ($sql->bindings as $replace) {
                    $value = is_numeric($replace) ? $replace : "'" . $replace . "'";
                    $singleSql = preg_replace('/\?/', $value, $singleSql, 1);
                }
                dump($singleSql);
            } else {
                dump($singleSql);
            }
        });
    }
}


//php获取中文字符拼音首字母
function getFirstCharter($str)
{
    if (empty($str)) {
        return '';
    }
    if (is_numeric($str{0})) return $str{0};// 如果是数字开头 则返回数字
    $fchar = ord($str{0});
    if ($fchar >= ord('A') && $fchar <= ord('z')) return strtoupper($str{0}); //如果是字母则返回字母的大写
    $s1 = iconv('UTF-8', 'gb2312', $str);
    $s2 = iconv('gb2312', 'UTF-8', $s1);
    $s = $s2 == $str ? $s1 : $str;
    $asc = ord($s{0}) * 256 + ord($s{1}) - 65536;
    if ($asc >= -20319 && $asc <= -20284) return 'A';//这些都是汉字
    if ($asc >= -20283 && $asc <= -19776) return 'B';
    if ($asc >= -19775 && $asc <= -19219) return 'C';
    if ($asc >= -19218 && $asc <= -18711) return 'D';
    if ($asc >= -18710 && $asc <= -18527) return 'E';
    if ($asc >= -18526 && $asc <= -18240) return 'F';
    if ($asc >= -18239 && $asc <= -17923) return 'G';
    if ($asc >= -17922 && $asc <= -17418) return 'H';
    if ($asc >= -17417 && $asc <= -16475) return 'J';
    if ($asc >= -16474 && $asc <= -16213) return 'K';
    if ($asc >= -16212 && $asc <= -15641) return 'L';
    if ($asc >= -15640 && $asc <= -15166) return 'M';
    if ($asc >= -15165 && $asc <= -14923) return 'N';
    if ($asc >= -14922 && $asc <= -14915) return 'O';
    if ($asc >= -14914 && $asc <= -14631) return 'P';
    if ($asc >= -14630 && $asc <= -14150) return 'Q';
    if ($asc >= -14149 && $asc <= -14091) return 'R';
    if ($asc >= -14090 && $asc <= -13319) return 'S';
    if ($asc >= -13318 && $asc <= -12839) return 'T';
    if ($asc >= -12838 && $asc <= -12557) return 'W';
    if ($asc >= -12556 && $asc <= -11848) return 'X';
    if ($asc >= -11847 && $asc <= -11056) return 'Y';
    if ($asc >= -11055 && $asc <= -10247) return 'Z';
    return null;
}

function judge($arr)
{
    return isset($arr) ? $arr : "";
}
