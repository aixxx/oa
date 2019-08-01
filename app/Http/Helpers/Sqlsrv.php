<?php
/**
 * Created by PhpStorm.
 * User: qsq_lipf
 * Date: 18/7/26
 * Time: 下午2:26
 */

namespace App\Http\Helpers;


use App\Models\Attendance\AttendanceCheckinout;
use App\Models\Attendance\AttendanceUserInfo;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Sqlsrv
{
    // 数据库用户
    protected $strDbUser = 'pengfei';

    // 数据库密码
    protected $strDbPass = 'pengfei@54321';

    // 数据库服务器地址
    protected $strDbHost = '10.1.0.17,1433';

    // 数据库名称
    protected $strDbName = 'kaoqin';

    //同步数据开始时间
    private $startTime = '2018-07-01';


    /**
     * 创建sqlsrv连接
     * @return \PDO
     */
    public function createConn()
    {
        // 连接数据库的字符串定义
        $strDsn = "sqlsrv:Server={$this->strDbHost};Database={$this->strDbName};";
        // 生成pdo对象
        $objDB = new \PDO($strDsn, $this->strDbUser, $this->strDbPass);

        return $objDB;
    }

    /**
     * 同步考勤机员工信息
     */
    public function syncUserInfo($conn)
    {
        DB::beginTransaction();
        try {
            $num = 0;
            foreach ($conn->query('SELECT * FROM USERINFO') as $row) {
                //oa员工编号与考勤机id唯一
                $employeeNum = $this->getEmployeeNum($row);
                $user        = AttendanceUserInfo::findByIdAndNum($row['USERID'], $employeeNum)->count();
                if (!$user) {
                    $userInfo['user_id']      = $row['USERID'];
                    $userInfo['badge_number'] = $row['BADGENUMBER'];
                    $userInfo['ssn']          = $row['SSN'];
                    $userInfo['name']         = $row['NAME'];
                    $userInfo['employee_num'] = $employeeNum;

                    if (DB::table('attendance_user_info')->insertGetId($userInfo)) {
                        $num++;
                    }
                }
            }

            Db::commit();

            echo sprintf('===同步考勤员工成功，新增了%s条===', $num);
            Log::info(sprintf('===同步考勤员工成功，新增了%s条===', $num));
        } catch (\Exception $e) {
            DB::rollBack();
            $messages = $e->getMessage();

            echo sprintf("同步考勤员工失败，原因【%s】", json_encode($messages));
            Log::info(sprintf("同步考勤员工失败，原因【%s】", json_encode($messages)));
        }
    }

    /**
     * 首次同步考勤机打卡信息自2018-07-01日
     */
    public function syncCheckincoutFull($conn)
    {
        DB::beginTransaction();
        //清空原表
        $truncateSql = "truncate table attendance_checkinout";
        DB::statement($truncateSql);

        try {
            $num = 0;
            $query = $conn->query("SELECT * FROM CHECKINOUT WHERE CHECKTIME >= '$this->startTime' ORDER BY CHECKTIME ASC");
            if ($query) {
                foreach ($query as $row) {
                    $check['user_id']    = $row['USERID'];
                    $check['check_time'] = $row['CHECKTIME'];
                    $check['sensor_id']  = $row['SENSORID'];
                    $check['sn']         = $row['sn'];

                    if (DB::table('attendance_checkinout')->insertGetId($check)) {
                        $num++;
                    }
                }

                Db::commit();
            }
            echo sprintf('===全量同步考勤打卡成功，%s条===', $num) . PHP_EOL;
            Log::info(sprintf('===全量同步考勤打卡成功，%s条===', $num) . PHP_EOL);
        } catch (\Exception $e) {
            DB::rollBack();
            $messages = $e->getMessage();

            echo sprintf("全量同步考勤打卡失败，原因【%s】", json_encode($messages));
            Log::info(sprintf("全量同步考勤打卡失败，原因【%s】", json_encode($messages)));
        }
    }

    /**
     * 增量同步近三天的考勤机打卡信息
     * 上周五，周六，周日，本周一
     */
    public function syncCheckincoutIncrement($conn, $beginDate = null, $endDate = null)
    {

        if ($beginDate == null && $endDate == null) {
            $startDate = Dh::subDays(Dh::todayDate(), 3);
            $sql = "SELECT * FROM CHECKINOUT WHERE CHECKTIME >= '$startDate' ORDER BY CHECKTIME ASC";
        } else {
            $sql = "SELECT * FROM CHECKINOUT WHERE CHECKTIME >= '$beginDate' AND CHECKTIME <= '$endDate' ORDER BY CHECKTIME ASC";
        }

        $query = $conn->query($sql);

        try {
            $num = 0;
            $data = [];

            if ($query) {
                foreach ($query as $row) {
                    $checkinout = AttendanceCheckinout::where('user_id', $row['USERID'])
                        ->where('check_time', $row['CHECKTIME'])
                        ->where('sensor_id', $row['SENSORID'])
                        ->where('sn', $row['sn'])
                        ->first();

                    if (!$checkinout) {
                        $num++;
                        $check = [
                            'user_id'    => $row['USERID'],
                            'check_time' => $row['CHECKTIME'],
                            'sensor_id'  => $row['SENSORID'],
                            'sn'         => $row['sn'],
                        ];

                        array_push($data, $check);
                    }
                }

                //批量入库
                DB::table('attendance_checkinout')->insert($data);

                Log::info("sync attendance : data " . $row['USERID']  . json_encode($data));
            } else {
                Log::info("sync attendance : 空 " . json_encode($query));
            }
            echo sprintf('===同步最近两天考勤打卡成功，%s条===', $num) . PHP_EOL;
        } catch (\Exception $e) {
            $messages = $e->getMessage();

            echo sprintf("同步最近四天考勤打卡失败，原因【%s】", json_encode($messages));
            Log::info(sprintf("同步最近四天考勤打卡失败，原因【%s】", json_encode($messages)));
        }
    }

    /**
     *
     * 截取字符串获取员工编号
     * @param $row
     * @return null|string|string[]
     * @throws \Exception
     */
    private function getEmployeeNum($row)
    {
        if ($row && $row['SSN']) {
            $num = User::parseEmployeeNum($row['SSN']);
        } else {
            $num = $row['BADGENUMBER'];
        }

        return $num;
    }
}
