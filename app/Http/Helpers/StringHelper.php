<?php
namespace App\Http\Helpers;

/**
 * 字符处理类
 *
 * @author light
 */
class StringHelper
{
    /**
     * 获取最小长度22位的唯一字符串，具体长度取决于$index的长度
     *
     * @param string $index
     *
     * @return string
     */
    public static function genUniqueString($index = null)
    {
        $str = (self::isNullOrEmpty($index) ? '' : $index) . (intval(date('Y')) - 2016) .
            strtoupper(dechex(date('m'))) . date('d') . substr(time(), -5) . substr(microtime(), 2, 6) .
            sprintf('%d', rand(10, 99));

        return $str . substr(md5($str), rand(0, 29), 1) . substr(md5($str), rand(0, 29), 1) .
            substr(md5($str), rand(0, 29), 1);
    }

    /**
     * 验证一个String是空或者是空字符串
     *
     * @param string $string
     *
     * @return bool
     */
    public static function isNullOrEmpty($string)
    {
        return (!isset($string) || strlen(trim($string)) == 0);
    }

    /**
     * Returns a value indicating whether the give value is "empty".
     *
     * The value is considered "empty", if one of the following conditions is satisfied:
     *
     * - it is `null`,
     * - an empty string (`''`),
     * - a string containing only whitespace characters,
     * - or an empty array.
     *
     * @param mixed $value
     *
     * @return bool if the value is empty
     */
    public static function isEmpty($value)
    {
        return $value === '' || $value === [] || $value === null || is_string($value) && trim($value) === '';
    }

    /**
     * 下划线转驼峰
     *
     * @param string $underlineStr
     * @param string $separator
     *
     * 1.原字符串转小写,原字符串中的分隔符用空格替换,在字符串开头加上分隔符
     * 2.将字符串中每个单词的首字母转换为大写,再去空格,去字符串首部附加的分隔符.
     *
     * @return string
     */
    public static function underlineToCamel($underlineStr, $separator = '_')
    {
        $underlineStr = $separator . str_replace($separator, " ", strtolower($underlineStr));

        return ltrim(str_replace(" ", "", ucwords($underlineStr)), $separator);
    }

    /**
     * Excel读取单元格值，单引号处理
     *
     * @param $str
     *
     * @return mixed|string
     */
    public static function excelReplaceQuotes($str)
    {
        if (self::isEmpty($str)) {
            return '';
        }
        $str = trim($str);
        if (self::lastIndexOf($str, '\'') == 0) {
            return str_replace('\'', '', $str);
        }

        return $str;
    }

    /**
     * 从$sourceStr找$findstr第一次出现的位置，基于0开始,如果找到则返回其所在位置，没找到返回-1，如indexOf("123","1")返回0
     *
     * @param string $sourceString
     *            被找字符串
     * @param string $findString
     *            要查找的字符串
     * @param bool   $caseInsCompare
     *            是否区分大小写，默认区分
     * @param int    $start_index
     *            查找起始位置，以0开始
     *
     * @return number 查找的位置，没找到返回-1
     */
    public static function indexOf($sourceString, $findString, $caseInsCompare = true, $start_index = 0)
    {
        if (self::isNullOrEmpty($sourceString)) {
            return -1;
        }
        if ($caseInsCompare) {
            $idx_result = strpos($sourceString, $findString, $start_index);
        } else {
            $idx_result = stripos($sourceString, $findString, $start_index);
        }
        if ($idx_result === false) {
            return -1;
        }

        return $idx_result;
    }

    /**
     * 从$sourceStr找$findstr最后一次出现的位置，基于0开始,如果找到则返回其所在位置，没找到返回-1，如indexOf("121","1")返回2
     *
     * @param string $sourceString
     *            被找字符串
     * @param string $findString
     *            要查找的字符串
     * @param bool   $caseInsCompare
     *            是否区分大小写，默认区分
     * @param int    $start_index
     *            查找起始位置，以0开始
     *
     * @return number 查找的位置，没找到返回-1
     */
    public static function lastIndexOf($sourceString, $findString, $caseInsCompare = true, $start_index = 0)
    {
        if (self::isNullOrEmpty($sourceString)) {
            return -1;
        }
        if ($caseInsCompare) {
            $idx_result = strrpos($sourceString, $findString, $start_index);
        } else {
            $idx_result = strripos($sourceString, $findString, $start_index);
        }
        if ($idx_result === false) {
            return -1;
        } else {
            return $idx_result;
        }
    }

    /**
     * 转换为int
     *
     * @param string $str
     *
     * @return number
     */
    public static function toInt($str)
    {
        return (int)$str;
    }

    /**
     * 转换为float
     *
     * @param string $str
     *
     * @return number
     */
    public static function toFloat($str)
    {
        return (float)$str;
    }

    /**
     * 字符串分隔为数组
     *
     * @param string $str
     *                      要分隔的字符串
     * @param string $seprator
     *                      分隔符号，非正则表达式
     * @param bool   $isreg 是否是正则表达式
     *
     * @return array 分隔后的数组
     */
    public static function split($str, $seprator, $isreg = false)
    {
        $result = null;
        if ($isreg) {
            $result = preg_split($seprator, $str);
        } else {
            $result = explode($seprator, $str);
        }

        return $result;
    }

    /**
     * 判断是否为整数
     * @param $val
     *
     * @return bool
     */
    public static function checkIsInt($val)
    {
        if (is_int($val)) {
            return true;
        }

        if (!is_numeric($val) || floor($val) != $val) {
            return false;
        }

        return true;
    }
}
