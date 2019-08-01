<?php
/**
 * Created by PhpStorm.
 * User: qsq_lipf
 * Date: 18/6/19
 * Time: 下午3:43
 */

namespace App\Http\Helpers;

use Carbon\Carbon;
use DateTime;
use DateInterval;

/**
 * 专门计算时间相关的Helper函数
 *
 * Dh 代表 Date Helper，为写代码方便，用了缩写，因为用得多，所以短一点
 *
 * @author charles
 */
class Dh
{
    const USE_UNIXTIMESTAMP = true;
    const PERIOD_MINUTIE = 60;
    const PERIOD_HOUR = 3600;
    const PERIOD_1DAY = 86400; //60 * 60 * 24 = 86400
    const PERIOD_30DAYS = 2592000; //60 * 60 * 24 * 30 = 2592000
    const DATE_OPERATOR_ADD = 'add';
    const DATE_OPERATOR_SUB = 'sub';
    const DATE_START_DATE = '1970-01-01';
    const DEFAULT_DATETIME_FORMAT = 'Y-m-d H:i:s';
    const DATETIME_FORMAT_TYPE_YM = 'Y-m';

    /**
     * 计算本月的月初
     *
     * @param boolean $unix_timestamp
     *
     * @return mixed
     */
    public static function thisMonthStart($unix_timestamp = self::USE_UNIXTIMESTAMP)
    {
        $timestamp = strtotime(date('Y-m-01 00:00:00', time()));

        return $unix_timestamp ? $timestamp : date('Y-m-d 00:00:00', $timestamp);
    }

    public static function thisMonthEnd($unix_timestamp = self::USE_UNIXTIMESTAMP)
    {
        $timestamp = strtotime(date('Y-m-01 00:00:00', time()) . ' +1 month');

        return $unix_timestamp ? $timestamp : date('Y-m-d 00:00:00', $timestamp);
    }

    public static function calcLastMonthStart($timestamp, $unix_timestamp = self::USE_UNIXTIMESTAMP)
    {
        $lastMonthTime = strtotime(date('Y-m-01 00:00:00', $timestamp) . ' -1 month');

        return $unix_timestamp ? $lastMonthTime : date('Y-m-01 00:00:00', $lastMonthTime);
    }

    public static function calcNextMonthStart($timestamp, $unix_timestamp = self::USE_UNIXTIMESTAMP)
    {
        $nextMonthTime = strtotime(date('Y-m-01 00:00:00', $timestamp) . ' +1 month');

        return $unix_timestamp ? $nextMonthTime : date('Y-m-01 00:00:00', $nextMonthTime);
    }

    public static function calcNextMonthEnd($timestamp, $unix_timestamp = self::USE_UNIXTIMESTAMP)
    {
        $nextMonthTime = strtotime(date('Y-m-01 00:00:00', $timestamp) . ' +2 month');

        return $unix_timestamp ? $nextMonthTime : date('Y-m-01 00:00:00', $nextMonthTime);
    }

    /**
     * 计算给定时间戳的月初始
     *
     * @param int $timestamp
     * @param boolean $unix_timestamp
     *
     * @return mixed
     */
    public static function calcMonthStart($timestamp, $unix_timestamp = self::USE_UNIXTIMESTAMP)
    {
        $time = date('Y-m-01 00:00:00', $timestamp);

        return ($unix_timestamp) ? strtotime($time) : $time;
    }


    /**
     * 计算给定时间戳的月初始
     *
     * @param int $timestamp
     * @param boolean $unix_timestamp
     *
     * @return mixed
     */
    public static function calcMonthStartDueDate($timestamp, $unix_timestamp = self::USE_UNIXTIMESTAMP)
    {
        $time = date('Y-m-01', $timestamp);

        return ($unix_timestamp) ? strtotime($time) : $time;
    }

    /**
     * 计算给定时间戳的月份
     *
     * @param int $timestamp
     * @param boolean $unix_timestamp
     *
     * @return mixed
     */
    public static function calcMonthStartDueMonth($timestamp, $unix_timestamp = self::USE_UNIXTIMESTAMP)
    {
        $time = date('Y-m', $timestamp);

        return ($unix_timestamp) ? strtotime($time) : $time;
    }

    public static function calcMonthEnd($timestamp, $unix_timestamp = self::USE_UNIXTIMESTAMP)
    {
        return self::calcNextMonthStart($timestamp, $unix_timestamp);
    }

    public static function formatDate($dateTime)
    {
        return date("Y-m-d", strtotime($dateTime));
    }

    public static function todayDate($withSpliter = true)
    {
        return $withSpliter ? date('Y-m-d') : date('Ymd');
    }

    public static function todayDateWithHourMinuteSecond($withSpliter = true)
    {
        return $withSpliter ? date('Y-m-d H:i:s') : date('Ymd H:i:s');
    }

    public static function getcurrentDateTime($intervalMinutes = 0)
    {
        $time     = new DateTime();
        $interval = new DateInterval('PT' . abs($intervalMinutes) . 'M');
        if ($intervalMinutes >= 0) {
            $time->add($interval);
        } else {
            $time->sub($interval);
        }

        return $time->format('Y-m-d H:i:s');
    }

    /**
     * 时间增加或减少分钟
     *
     * @param string $dateTime
     * @param int $intervalMinutes
     *
     * @return string
     */
    public static function calcDateTimeFromAddMinutes($dateTime, $intervalMinutes = 0)
    {
        $dateTime = DateTime::createFromFormat('Y-m-d H:i:s', $dateTime);
        $interval = new DateInterval('PT' . abs($intervalMinutes) . 'M');
        if ($intervalMinutes >= 0) {
            $dateTime->add($interval);
        } else {
            $dateTime->sub($interval);
        }

        return $dateTime->format('Y-m-d H:i:s');
    }

    public static function calcSpecialTodayDateTime($time)
    {
        return DateTime::createFromFormat('Y-m-d H:i:s', self::todayDate() . ' ' . $time)->format('Y-m-d H:i:s');
    }

    public static function calcDateTime($datetime, $intervalSeconds)
    {
        $datetime = DateTime::createFromFormat('Y-m-d H:i:s', $datetime);
        $interval = new DateInterval('PT' . abs($intervalSeconds) . 'S');
        if ($intervalSeconds >= 0) {
            $datetime->add($interval);
        } else {
            $datetime->sub($interval);
        }

        return $datetime->format('Y-m-d H:i:s');
    }

    public static function tomorrowDate()
    {
        return date('Y-m-d', self::getTomorrowStart());
    }

    public static function getTomorrowStart($unix_timestamp = self::USE_UNIXTIMESTAMP)
    {
        $time = strtotime('tomorrow');

        return $unix_timestamp ? $time : date('Y-m-d H:i:s', $time);
    }

    public static function getTodayStart($unix_timestamp = self::USE_UNIXTIMESTAMP)
    {
        $time = strtotime('today');

        return $unix_timestamp ? $time : date('Y-m-d H:i:s', $time);
    }

    public static function yesterdayDate()
    {
        return date('Y-m-d', self::getYesterdayStart());
    }

    public static function getYesterdayStart($unix_timestamp = self::USE_UNIXTIMESTAMP)
    {
        $time = strtotime('yesterday');

        return $unix_timestamp ? $time : date('Y-m-d H:i:s', $time);
    }

    public static function checkDate($date)
    {
        return preg_match('/[12]\d{3}-[01]\d-[0-3]\d/', $date, $matches) !== 0;
        /*if (preg_match('/[12]\d{3}-[01]\d-[0-3]\d/', $date, $matches) === false) {
            return false;
        }
        $d = DateTime::createFromFormat('Y-m-d', $matches[0]);

        return $d && $d->format('Y-m-d') == $matches[0];*/
    }

    public static function newCheckDate($date)
    {
        return preg_match('/[12]\d{3}-[01]\d-[0-3]\d/', $date, $matches) !== 0;
    }

    /**
     * 获取开始时间
     *
     * @param string $date 日期，格式如 20141225 或者 2014-12-25
     * @param bool $unix_timestamp Unix时间戳
     *
     * @return false|int|string
     */
    public static function getDateStart($date, $unix_timestamp = self::USE_UNIXTIMESTAMP)
    {
        $datetime = date('Y-m-d 00:00:00', strtotime($date));

        return $unix_timestamp ? strtotime($datetime) : $datetime;
    }

    /**
     * 获取结束时间
     *
     * @param string $date 日期，格式如 20141225 或者 2014-12-25
     * @param bool $unix_timestamp Unix时间戳
     *
     * @return false|int|string
     */
    public static function getDateEnd($date, $unix_timestamp = self::USE_UNIXTIMESTAMP)
    {
        $datetime           = date('Y-m-d 00:00:00', strtotime($date));
        $end_datetime_stamp = strtotime($datetime . ' +1 days');

        return $unix_timestamp ? $end_datetime_stamp : date('Y-m-d H:i:s', $end_datetime_stamp);
    }

    /**
     * 计算两个日期之前的天数，前面一个参数要比后面一个大，否则返回负数
     *
     * @param string $big 日期字符串，如2015-01-02
     * @param string $small 日期字符串，如2015-01-02
     *
     * @return int 天数的差，可能是负数
     */
    public static function calcDays($big, $small)
    {
        $minus = false;
        $time1 = strtotime($big);
        $time2 = strtotime($small);
        if ($time1 < $time2) {
            $minus = true;
        }
        $interval = date_diff(
            date_create(date('Y-m-d', strtotime($big))),
            date_create(date('Y-m-d', strtotime($small)))
        );

        return $minus ? 0 - $interval->format('%a') : intval($interval->format('%a'));
    }

    /**
     * 生成时间闭区间
     *
     * @param string $timeStr
     *
     * @return array
     */
    public static function calcMonthBetween($timeStr)
    {
        if (strlen($timeStr) == 6) {
            // 201409 like string expected.
            $timestamp = strtotime($timeStr . '01');
        } else {
            if (is_string($timeStr)) {
                $timestamp = strtotime($timeStr);
            } else {
                $timestamp = $timeStr;
            }
        }
        $start = self::calcMonthStart($timestamp, false);
        $end   = date('Y-m-d H:i:s', self::calcMonthEnd($timestamp) - 1);

        return [$start, $end];
    }

    /**
     * 两天减要加一天
     *
     * @param $beginDate
     * @param $endDate
     *
     * @return int
     */
    public static function getDaysByInterestDate($beginDate, $endDate)
    {
        return intval((strtotime($endDate) - strtotime($beginDate)) / 86400) + 1;
    }

    public static function addDays($date, $days)
    {
        return self::calcDateFromAddDate($date, $days, 'D', self::DATE_OPERATOR_ADD, 'Y-m-d');
    }

    /**
     * 指定日期减去指定天数
     *
     * @param $date
     * @param $days
     *
     * @return DateTime|string
     */
    public static function subDays($date, $days)
    {
        return self::calcDateFromAddDate($date, $days, 'D', self::DATE_OPERATOR_SUB, 'Y-m-d');
    }

    /**
     * 给定时间计算返回传入日期的加减年月日
     *
     * @param string $beginDate 开始时间
     * @param integer $addDate 累加时间
     * @param string $dateType 时间类型 Y为年,M为月,D为日
     * @param string $operator 'add' 为加 'sub'为减
     * @param string $returnDateType 格式化样式
     *
     * @return Datetime|string
     */
    public static function calcDateFromAddDate(
        $beginDate,
        $addDate,
        $dateType = 'M',
        $operator = self::DATE_OPERATOR_ADD,
        $returnDateType = 'Y-m-d'
    )
    {
        $dateType = strtoupper($dateType);
        if (in_array($dateType, ['Y', 'M', 'D'])) {
            $type = 'P';
        } else {
            $type = 'PT';
        }
        if ($operator === self::DATE_OPERATOR_ADD) {
            $date = date_create($beginDate)
                ->add(new DateInterval("{$type}{$addDate}{$dateType}"))
                ->format($returnDateType);
        } else {
            $date = date_create($beginDate)
                ->sub(new DateInterval("{$type}{$addDate}{$dateType}"))
                ->format($returnDateType);
        }

        return $date;
    }

    /**
     * 计算两个时间的小时差
     *
     * @param $startDate
     * @param $endDate
     *
     * @return float
     * @throws Exception
     */
    public static function calcHours($startDate, $endDate)
    {
        if (!(self::checkDate($startDate) && self::checkDate($endDate))) {
            throw new \Exception('输入时间不正确');
        }
        $startDate = strtotime($startDate);
        $endDate   = strtotime($endDate);

        return (int)round(($startDate - $endDate) / 3600, 0);
    }

    /**
     * excel中数字转日期
     * @param      $number
     * @param bool $unix_timestamp
     *
     * @return false|float|int|string
     */
    public static function number2date($number, $unix_timestamp = self::USE_UNIXTIMESTAMP)
    {
        $beginNumber = 25569;   //1970-1-1 代表的数字
        $time        = ((integer)$number - $beginNumber) * 24 * 60 * 60; //获得秒数

        return $unix_timestamp ? $time : date('Y-m-d H:i:s', $time);
    }

    /**
     * 按照指定格式返回格式化后的当前时间
     * @param string $format 只支持 date() 函数
     * @return bool|int|string
     */
    public static function now($format = self::DEFAULT_DATETIME_FORMAT)
    {
        return date($format);
    }

    /**
     * 分别计算出两日期的年、月、日的差($date1<$date2)
     * @param $date1
     * @param $date2
     * @return array
     */
    public static function compareTwoTimeDiffer($date1, $date2)
    {
        list($Y1,$m1,$d1)=explode('-',$date1);
        list($Y2,$m2,$d2)=explode('-',$date2);
        $Y=$Y2-$Y1;
        $m=$m2-$m1;
        $d=$d2-$d1;
        if($d<0){
            $d+=(int)date('t',strtotime("-1 month $date2"));
            $m--;
        }
        if($m<0){
            $m+=12;
            $Y--;
        }
        $data = [
            'year'=>$Y,
            'month'=>$m,
            'day'=>$d
        ];
        return $data;
    }

    /**
     * 本公司工作一天按8小时处理，其他时间转换慎用！！！
     * @param $day
     * @return int
     */
    public static function dayToHour($day)
    {
        return $day * 8;
    }

    /**
     * 判断两个日期之间的大小
     * @param string $date1   格式Y-m-d
     * @param string $date2
     * @return bool
     */
    public static function compare2Dates($date1, $date2)
    {
        $time1 = strtotime($date1);
        $time2 = strtotime($date2);
        return $time1 > $time2 ? true : false;
    }

    /**
     * 计算时间差
     * @param int $timestamp1 时间戳开始
     * @param int $timestamp2 时间戳结束
     * @param string $rule i=分钟差。 h=小时差
     * @return array
     */
    public static function timeDiff($timestamp1, $timestamp2, $rule = 'i') {
        $minute = 0;
        if ($timestamp2 <= $timestamp1) {
            return $minute;
        }

        if($rule == 'i'){
            //分钟
            $minute = ceil((strtotime($timestamp2)-strtotime($timestamp1)) / 60);
        }else if ($rule == 'h'){
            //小时
            $minute = intval((strtotime($timestamp2)-strtotime($timestamp1)) / 3600);
        }
        return $minute;
    }

    /**
     *   根据日期换算星期几
     */
    public static function getWeek($date_str){
        //封装成数组
        $arr=explode("-", $date_str);

        //参数赋值
        //年
        $year=$arr[0];

        //月，输出2位整型，不够2位右对齐
        $month=sprintf('%02d',$arr[1]);

        //日，输出2位整型，不够2位右对齐
        $day=sprintf('%02d',$arr[2]);

        //时分秒默认赋值为0；
        $hour = $minute = $second = 0;

        //转换成时间戳
        $strap = mktime($hour,$minute,$second,$month,$day,$year);

        //获取数字型星期几
        $number_wk=date("w",$strap);

        //自定义星期数组
        $weekArr=array("7","1","2","3","4","5","6");

        //获取数字对应的星期
        return $weekArr[$number_wk];
    }

    /**
     * 给定时间计算返回传入日期的加时间
     *
     * @param string $beginDate 开始时间
     * @param integer $addDate 累加时间
     * @param string $dates 日期
     * @param string $dateType 时间类型 H为年,i为月,s为日
     *
     * @return Datetime|string
     */
    public static function calcAddTime($beginDate, $addDate, $dates = "", $dateType = 'i'){
        if($dates) {
            return date('Y-m-d H:i:s',strtotime($dates." ".$beginDate) + $addDate * 60);
        }else{
            return date('Y-m-d H:i:s',strtotime($beginDate) + $addDate * 60);
        }
    }
    /**
     * 给定时间计算返回传入日期的加时间
     *
     * @param string $beginDate 开始时间
     * @param integer $subDate 累加时间
     * @param string $dates 日期
     * @param string $dateType 时间类型 H为年,i为月,s为日
     *
     * @return Datetime|string
     */
    public static function calcSubTime($beginDate, $subDate, $dates = "", $dateType = 'i'){
        if($dates){
            return date('Y-m-d H:i:s',strtotime($dates." ".$beginDate) - $subDate * 60);
        }else{
            return date('Y-m-d H:i:s',strtotime($beginDate) - $subDate * 60);
        }
    }

    //给定月份 返回月份开始 结束时间
    public static function getBeginEndByMonth($dates){
        $month_start = Carbon::parse($dates)->startOfMonth()->toDateString();
        $month_end = Carbon::parse($dates)->endOfMonth()->toDateString();
        return [
            'month_start'=> $month_start,
            'month_end'=> $month_end,
        ];
    }

    //给定时间段， 返回区间
    public static function getbetweenDay($begin, $end){
        $begin_date = Carbon::parse($begin)->toDateString();
        $end_date = Carbon::parse($end)->toDateString();
        $i = $begin_date;
        while ($begin_date <= $end_date){
            $between_date[$begin_date] = $begin_date;
            $begin_date = Carbon::parse($begin_date)->addDay()->toDateString();
        }
        return $between_date;
    }
}