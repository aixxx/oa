<?php

namespace App\Http\Controllers\Workflow;

use App\Models\Attendance\AttendanceHoliday;
use App\Models\Contract;
use App\Services\Attendance\CalcTimeService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Support\Facades\Auth;
use Response;
use App\Http\Helpers\Dh;
use UserFixException;
class CommonController extends Controller
{
    public function getWorkTime(Request $request)
    {
        try {
            $time = $request->input('times');
            $type = $request->input('type');

            if (isset($time[0]) && isset($time[1])) {
                if ($time[0] > $time[1]) {
                    $hour = CalcTimeService::getLeaveLength($time[1], $time[0], $type, Auth::id());
                } else {
                    $hour = CalcTimeService::getLeaveLength($time[0], $time[1], $type, Auth::id());
                }
                switch ($type) {
                    case '婚假':
                        if ($hour > 80) {
                            throw new UserFixException("婚假不能超过10天");
                        }
                        break;
                    case '丧假':
                        if ($hour > 24) {
                            throw new UserFixException("丧假不能超过3天");
                        }
                        break;
                    case '产检假':
                        if ($hour > 8) {
                            throw new UserFixException("产检假单次最多8小时");
                        }
                        break;
                }

                return Response::json([
                    'code'    => 0,
                    'status'  => 'success',
                    'message' => '计算成功',
                    'data'    => ['hour' => $hour, 'unit' => '小时'],
                ]);
            } else {
                return Response::json([
                    'code'    => 0,
                    'status'  => 'success',
                    'message' => '参数错误',
                    'data'    => ['hour' => 0, 'unit' => '小时'],
                ]);
            }
        } catch (Exception $exception) {
            return Response::json([
                'code'    => $exception->getCode(),
                'status'  => 'error',
                'message' => $exception->getMessage(),
                'data'    => [
                    'hour' => 0,
                    'unit' => '小时',
                ],
            ]);
        }
    }

    public function getWorkTimeByHour(Request $request)
    {
        try {
            $time = $request->input('times');
            $user_id = $request->input('user_id');

            if (isset($time[0]) && isset($time[1])) {
                if ($time[0] > $time[1]) {
                    $hour = CalcTimeService::getOvertimeLength($time[1], $time[0], $user_id);
                } else {
                    $hour = CalcTimeService::getOvertimeLength($time[0], $time[1], $user_id);
                }

                return Response::json([
                    'code'    => 0,
                    'status'  => 'success',
                    'message' => '计算成功',
                    'data'    => ['hour' => $hour, 'unit' => '小时'],
                ]);
            } else {
                return Response::json([
                    'code'    => 0,
                    'status'  => 'success',
                    'message' => '参数错误',
                    'data'    => ['hour' => 0, 'unit' => '小时'],
                ]);
            }
        } catch (Exception $exception) {
            return Response::json([
                'code'    => $exception->getCode(),
                'status'  => 'error',
                'message' => $exception->getMessage(),
                'data'    => [
                    'hour' => 0,
                    'unit' => '小时',
                ],
            ]);
        }
    }

    /**
     * 计算天数差
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDateInterval(Request $request)
    {
        try {
            $time = $request->input('times');

            if (isset($time[0]) && isset($time[1])) {
                if ($time[0] > $time[1]) {
                    $hour = CalcTimeService::getDateIntervalLength($time[1], $time[0]);
                } else {
                    $hour = CalcTimeService::getDateIntervalLength($time[0], $time[1]);
                }

                return Response::json([
                    'code'    => 0,
                    'status'  => 'success',
                    'message' => '计算成功',
                    'data'    => ['hour' => $hour, 'unit' => '天'],
                ]);
            } else {
                return Response::json([
                    'code'    => 0,
                    'status'  => 'success',
                    'message' => '参数错误',
                    'data'    => ['hour' => 0, 'unit' => '天'],
                ]);
            }
        } catch (Exception $exception) {
            return Response::json([
                'code'    => $exception->getCode(),
                'status'  => 'error',
                'message' => $exception->getMessage(),
                'data'    => [
                    'hour' => 0,
                    'unit' => '小时',
                ],
            ]);
        }
    }

    public function getCompanyList()
    {
        $companyList = collect(Contract::getCompanyNameList())->transform(function ($item, $key) {
            return ['name' => $item];
        });

        return Response::json([
            'code'    => 0,
            'status'  => 'success',
            'message' => '',
            'data'    => $companyList,
        ]);
    }

    public function getOwnContracts()
    {
        $endTime   = Dh::todayDateWithHourMinuteSecond();
        $beginTime = Dh::calcLastMonthStart(strtotime($endTime), false);

        return Response::json([
            'code'    => 0,
            'status'  => 'success',
            'message' => '',
            'data'    => Contract::getLatestContract($beginTime, $endTime),
        ]);
    }
}
