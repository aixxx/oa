<?php
/**
 * Created by PhpStorm.
 * User: lizhennan
 * Date: 2018/7/30
 * Time: 下午5:04
 */

namespace App\Services;

use App\Models\Attendance\AttendanceHoliday;

class HolidayService
{
    public static $needColumns = [
        'A' => [
            'index' => 'holiday_date',
            'value' => '时间'
        ],
        'B' => [
            'index' => 'holiday_status',
            'value' => '状态(0-工作，1-休息)'
        ],
        'C' => [
            'index' => 'holiday_type',
            'value' => '是否法定节假日(0-否，1-是,周末不是)'
        ],
    ];

    /**
     * @param $objWorksheet
     * @param $highestRow
     * @param $total_column
     * @return array
     */
    public static function loadDate($objWorksheet, $highestRow, $total_column)
    {
        $allData = [];
        $needCount = count(self::$needColumns);
        for ($rowIndex = 2; $rowIndex <= $highestRow; $rowIndex++) {
            $data = array();
            for ($column = 'A'; $column <= $total_column; $column++) {
                if (!isset(self::$needColumns[$column])) {
                    continue;
                }
                $index = self::$needColumns[$column]['index'];
                $value = trim($objWorksheet->getCell($column . $rowIndex)->getValue());
                $data[$index] = $value;
            }
            if (count($data) != $needCount) {
                continue;
            }
            $t                    = $data['holiday_date'];
            $data['holiday_date'] = gmdate('Y-m-d', intval(($t - 25569) * 3600 * 24));//将excel日期的值转化为正常形式
            $attendanceHoliday = AttendanceHoliday::where('holiday_date', $data['holiday_date'])->first();
            if (!$attendanceHoliday) {
                $attendanceHoliday = new AttendanceHoliday();
            }
            $attendanceHoliday->fill($data);
            $attendanceHoliday->save();
            $allData[] = $data;
        }
        return $allData;
    }
}