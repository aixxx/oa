<?php

namespace App\Models\Attendance;

use App\Http\Helpers\Dh;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * App\Models\Attendance\AttendanceWorkUserLogs
 *
 * @mixin \Eloquent
 * @property int $id
 * @property string $date 时间
 * @property string|null $class_title 排班代码
 * @property int|null $user_id 员工编号
 * @property int $status 状态(1:有效；2:无效)
 * @property string|null $create_at 创建时间
 * @property string|null $updat_at 修改时间
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Attendance\AttendanceWorkUserLogs whereClassTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Attendance\AttendanceWorkUserLogs whereCreateAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Attendance\AttendanceWorkUserLogs whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Attendance\AttendanceWorkUserLogs whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Attendance\AttendanceWorkUserLogs whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Attendance\AttendanceWorkUserLogs whereUpdatAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Attendance\AttendanceWorkUserLogs whereUserId($value)
 * @property-read \App\Models\Attendance\AttendanceWorkClass $workClass
 */
class AttendanceWorkUserLogs extends Model
{
    protected $table = 'attendance_work_user_logs';

    protected $primaryKey = 'id';

    public $timestamps = false;

    protected $fillable = [
        'id',
        'date',
        'class_title',
        'user_id',
        'status',
        'create_at',
        'updat_at',
    ];

    /**
     * 白名单
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function workClass()
    {
        return $this->hasOne('App\Models\Attendance\AttendanceWorkClass', 'class_title', 'class_title');
    }

    /**
     * 获取排班员工工作日
     *
     * @param $date
     * @param $userId
     * @return Model|null|object|static
     */
    public static function findWorkDateByIdByDate($date, $userId)
    {
        return self::with('workClass')->where('date', $date)->where('user_id', $userId)->where('class_title', '<>', '')->first();
    }

    /**
     * 根据起止时间获取当月排班
     *
     * @param $startDate
     * @param $endDate
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Support\Collection|static[]
     */
    public static function getAppointmentsOnMonth($startDate, $endDate)
    {
        return self::with('workClass')
            ->leftJoin('users','user_id', 'users.id')
            ->where('date', '>=', $startDate)
            ->where('date', '<', $endDate)
            ->orderBy('date')
            ->orderBy('user_id')
            ->get();
    }

    /**
     * 导入排班excel
     * @param $objWorksheet
     * @param $highestRow
     * @param $hightestColumn
     *
     * @return array
     */
    public static function loadData($objWorksheet, $highestRow, $hightestColumn)
    {
        $allData = [];

        //实际最大列
        $actualHightest = '';
        $rowIndex = 1;

        for ($column = 'A'; strlen($column) < strlen($hightestColumn) or $column <= $hightestColumn; $column++) {
            $value = trim($objWorksheet->getCell($column . $rowIndex)->getValue());
            if ($value) {
                $actualHightest = $column;
            }
        }

        //从第三行第三列读
        $columnA = 'A';
        $rowDate = 2;
        for ($rowIndex = 3; $rowIndex <= $highestRow; $rowIndex++) {
            $data = [];
            $employeeNum = trim($objWorksheet->getCell($columnA . $rowIndex)->getValue());
            $FormatEmployeeNum = $num = sprintf('%d', substr($employeeNum, 2)); //KN00987 -> 987

            $user = User::where('employee_num', '=', $FormatEmployeeNum)->first();

            if ($user) {
                for ($column = 'C'; strlen($column) < strlen($hightestColumn) or
                $column <= $actualHightest; $column++) {


                    $rowDateValue = trim($objWorksheet->getCell($column . $rowDate)->getValue());
                    $value = trim($objWorksheet->getCell($column . $rowIndex)->getValue());

                    //已排班则删除，重新排班
                    $isAppoint = AttendanceWorkUserLogs::where('user_id', '=', $user->id)
                        ->where('date', '=', Dh::formatDate(Dh::number2date($rowDateValue, false)))->first();
                    if ($isAppoint) {
                        $isAppoint->delete();
                    }

                    $insertData['user_id']     = $user->id;
                    $insertData['date']        = Dh::formatDate(Dh::number2date($rowDateValue, false));
                    $insertData['class_title'] = $value;
                    $insertData['create_at']   = Dh::getcurrentDateTime();

                    DB::table('attendance_work_user_logs')->insertGetId($insertData);

                    $data[$FormatEmployeeNum] = $insertData;
                    $allData[] = $data;
                }
            }
        }
        return $allData;
    }
}