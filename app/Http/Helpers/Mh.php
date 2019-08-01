<?php
namespace App\Http\Helpers;

/**
 * 金额计算的辅助类，Mh代表 Money Helper，使用缩写是为了短
 */
class Mh
{
    const PRECISION              = 2; //小数点后2位
    const SEP_SYMBOL             = ','; //千分位符号为半角逗号
    const DEP_SYMBOL             = '.'; //小数点符号
    const PER_CENT               = '%'; //百分号
    const ZERO_REPLACE_CHARACTER = '-'; //值0的替换符
    //以上常量定义

    /**
     * 将人民币的元，转换成分
     *
     * @param float $yuan
     *
     * @return float
     */
    public static function y2f($yuan)
    {
        return intval(round($yuan * 100, 0));
    }

    /**
     * 将分转换成元
     *
     * 如果需要格式化，则默认是有千分位的，符号缺省为半角逗号，返回类型变为字符串
     * 如果不需要格式化，返回是float类型，小数点后的0会被省略掉
     *
     * 缺省精确度为2位小数
     *
     * @param float   $fen         要转换的金额，以分为单位
     * @param boolean $format      是否要格式化，缺省是false
     * @param boolean $k_seperator 是否要加千分位，缺省是false
     *
     * @return float|string 如果format参数为true，返回字符串，否则返回float
     */
    public static function f2y($fen, $format = false, $k_seperator = false)
    {
        $yuan = round($fen / 100, self::PRECISION, PHP_ROUND_HALF_EVEN);
        if ($format) {
            $sep  = $k_seperator ? self::SEP_SYMBOL : '';
            $yuan = number_format($yuan, self::PRECISION, self::DEP_SYMBOL, $sep);
        }

        return $yuan;
    }

    /**
     * 将分转换为万
     *
     * @param float   $fen         要转换的金额，以分为单位
     * @param boolean $format      是否要格式化，缺省是true
     * @param boolean $k_seperator 是否要加千分位，缺省是true
     *
     * @return float|string
     */
    public static function f2w($fen, $format = true, $k_seperator = true)
    {
        $yuan = self::f2y($fen, false);
        $bai  = self::f2y($yuan, false);

        return self::f2y($bai, $format, $k_seperator);
    }

    /**
     * 金额格式化
     *
     * @param      $number
     * @param int  $decimals     保留小数位 默认0
     * @param bool $k_sep        开启千分位分隔符 默认 true
     * @param bool $zero_rep     开启值为0的替换符 默认 true
     * @param bool $end_per_cent 开启未尾追加% 默认 false
     *
     * @return string
     */
    public static function format($number, $decimals = 0, $k_sep = true, $zero_rep = true, $end_per_cent = false)
    {
        if ($zero_rep && !($number != 0)) {
            return self::ZERO_REPLACE_CHARACTER;
        }

        $sep = $k_sep ? self::SEP_SYMBOL : '';
        $val = number_format($number, $decimals, self::DEP_SYMBOL, $sep);

        if ($end_per_cent) {
            $val = $val . '%';
        }

        return $val;
    }

    /**
     * 金额格式化 百分比
     *
     * @param $number
     * @param $decimals
     *
     * @return string
     */
    public static function formatRate($number, $decimals)
    {
        return self::format($number, $decimals, false, true, true);
    }

    /**
     * 数量格式化 0用-表示
     *
     * @param $number
     *
     * @return string
     */
    public static function formatCount($number)
    {
        return self::format($number, 0, false, true);
    }

    /**
     * 格式化  0转成空字符
     *
     * @param $number
     *
     * @return string
     */
    public static function formatZeroToEmpty($number)
    {
        return $number != 0 ? $number : '';
    }

    /**
     * 计算利息的算法
     *
     * @param float $principal_by_fen 本金，单位是分
     * @param float $interest_rate    利率，单位是%，如果年化利率7%，这里传入7.0
     * @param float $period_by_day    投资周期，天数
     *
     * @return float 利息，结果摄入到1分
     */
    public static function interest($principal_by_fen, $interest_rate, $period_by_day)
    {
        return round($principal_by_fen * $interest_rate / 100 * $period_by_day / 360, 0, PHP_ROUND_HALF_EVEN);
    }

    /**
     * 年化计算器 单位分 单期贷
     *
     * @param $amount    int        金额
     * @param $rate      double     年化率
     * @param $days      int        收益天数
     * @param $year_days int        年化天数（365、360）
     *
     * @return float
     */
    public static function yearCalculator($amount, $rate, $days, $year_days)
    {
        return self::f2f($amount * $rate * $days / $year_days);
    }

    public static function f2f($fen)
    {
        return intval(round(round($fen, 2)));
    }

    /**
     * 贴息/展期利息、千牛牛平台服务费金额计算,四舍六入五成双
     * @param $fen
     *
     * @return float
     */
    public static function collectFeeCalculator($fen)
    {
        return round($fen, 0, PHP_ROUND_HALF_EVEN);
    }

    /**
     * 比例计算器 单位分
     *
     * @param $amount
     * @param $rate
     *
     * @return float
     */
    public static function proportionalCalculator($amount, $rate)
    {
        return intval(round(round($amount * $rate, 2)));
    }

    /**
     * 比较两个金额
     * @param     $left
     * @param     $right
     * @param int $scale 小数点后所需的位数
     *
     * @return int
     */
    public static function compare($left, $right, $scale = 2)
    {
        return bccomp($left, $right, $scale);
    }

    /**
     * 两个金额相加
     * @param      $left_operand
     * @param      $right_operand
     * @param null $scale
     *
     * @return string
     */
    public static function add($left_operand, $right_operand, $scale = null)
    {
        return bcadd($left_operand, $right_operand, $scale);
    }

    /**
     * 两个金额相减
     * @param      $left_operand
     * @param      $right_operand
     * @param null $scale
     *
     * @return string
     */
    public static function sub($left_operand, $right_operand, $scale = null)
    {
        return bcsub($left_operand, $right_operand, $scale);
    }

    /**
     * @param $number
     * @return bool
     */
    public static function isValidInteger($number)
    {
        if (preg_match("/^(-)?\d{1,15}$/", $number)) {
            return true;
        } else {
            return false;
        }
    }

    public static function int2Percent($number)
    {
        $result = round($number / 100, self::PRECISION, PHP_ROUND_HALF_EVEN);
        return $result.'%';
    }
}
