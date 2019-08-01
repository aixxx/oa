<?php

namespace App\Repositories;

use App\Models\Schedules\Schedules;
use App\Models\Schedules\UserSchedules;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use App\Constant\ConstFile;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use DB;
use Auth;
use Illuminate\Support\Facades\Request;

class SchedulesRepository extends Repository
{
    public function model()
    {
        return Schedules::class;
    }

    /**
     * 添加新的日程
     * @param array $data
     * @return \Illuminate\Http\JsonResponse|string
     */
    public function addRecord(array $data)
    {
        try {
            $result = ['code' => ConstFile::API_RESPONSE_SUCCESS, 'message' => ConstFile::API_RESPONSE_SUCCESS_MESSAGE];
            $error = $this->checkData($data);
            throw_if($error, new Exception('请求参数错误：' . $error));

            $participants = $data['participant_ids'];
            unset($data['participant_ids']);
            $participantsCollect = collect($participants)->push(Auth::id())->unique();//日程创建人也是默认接收人

            DB::transaction(function () use ($data, $participantsCollect) {
                //创建日程
                $userId = Auth::id();
                $userName = Auth::user()->chinese_name;
                $data['create_schedule_user_id'] = $userId;
                $schedule = Schedules::create($data);
                //创建日程和参与人的对应记录
                Collect($participantsCollect)->each(function ($item, $key) use ($data, $schedule, $userId, $userName) {
                    $user = User::find($item);
                    throw_if(!$user, new Exception('请求参数错误：' . sprintf("ID为%s的用户不存在", $item)));

                    $userData = null;
                    $userData['create_schedule_user_id'] = $data['create_schedule_user_id'];
                    $userData['schedule_id'] = $schedule->id;
                    $userData['content'] = $schedule->content;
                    $userData['user_id'] = $item;
                    $userData['create_schedule_user_name'] = $userName;
                    $userData['prompt_type'] = $data['prompt_type'];
                    $userData['user_name'] = $user->chinese_name;
                    $userData['confirm_yes'] = ConstFile::SCHEDULE_STATUS_CONFIRM_NO;
                    UserSchedules::create($userData);
                });
            });
        } catch (Exception $e) {
            $result['code'] = ConstFile::API_RESPONSE_FAIL;
            $result['message'] = $e->getMessage();
        }
        return returnJson($result['message'], $result['code']);
    }

    /**
     * 获取日程列表:创建人角度
     * @param array $data
     * @return \Illuminate\Http\JsonResponse|string
     */
    public function fetchSchedules(array $data)
    {
        try {
            $result = ['code' => ConstFile::API_RESPONSE_SUCCESS, 'message' => ConstFile::API_RESPONSE_SUCCESS_MESSAGE];
            $error = $this->checkSearchData($data);
            throw_if($error, new Exception('请求参数错误：' . $error));

            $query = Schedules::where(['create_schedule_user_id' => Auth::id()]);
            if (!empty($data['key_words'])) {
                $query->where('content', 'like', '%' . $data['key_words'] . '%');
            }
            $lastId = empty($data['last_id']) ? 0 : $data['last_id'];
            $query->where('id', '>', $lastId);
            //$temp     = $query->limit(10)->get();
            $temp = $query->get();
            $resultDB = [];
            if (empty($temp)) {
                $result['data'] = [];
                return returnJson($result['message'], $result['code'], $result['data']);
            }
            $promptList = ConstFile::$schedulePromptTypeList;

            foreach ($temp as $key => $item) {
                $resultDB[$key]['id'] = $item['id'];
                $resultDB[$key]['content'] = $item['content'];
                $resultDB[$key]['start_at'] = $item['start_at'];
                $resultDB[$key]['end_at'] = $item['end_at'];
                $resultDB[$key]['prompt_title'] = isset($promptList[$item['prompt_type']]) ? $promptList[$item['prompt_type']] : '暂无';
                $resultDB[$key]['address'] = $item['address'];
            }

            return returnJson(ConstFile::API_RESPONSE_SUCCESS_MESSAGE, ConstFile::API_RESPONSE_SUCCESS, $resultDB);
        } catch (Exception $e) {
            return returnJson($e->getMessage(), ConstFile::API_RESPONSE_FAIL);
        }
    }

    /**
     * 获取日程列表:创建人和接收人混合角度
     * @param $data
     * @return false|\Illuminate\Http\JsonResponse|string
     * @throws \Throwable
     */
    public function fetchUserSchedules($data)
    {
        try {
            $result = ['code' => ConstFile::API_RESPONSE_SUCCESS, 'message' => ConstFile::API_RESPONSE_SUCCESS_MESSAGE];
            $error = $this->checkSearchData($data);
            throw_if($error, new Exception('请求参数错误：' . $error));

            $end = Carbon::now()->toDateTimeString();
            $start = Carbon::now()->subDays(7)->toDateTimeString();
            if (isset($data['date']) && !empty($data['date'])) {
                $start = Carbon::createFromFormat('Y-m-d', $data['date'])->toDateString();
                $end = Carbon::createFromFormat('Y-m-d', $data['date'])->toDateString();
            }
            $query = UserSchedules::with('hasOneSchedules', 'hasOneSchedules.hasManyScheduleUsers')
                ->whereHas('hasOneSchedules', function ($query) use ($start, $end) {
                    $query->whereRaw("(DATE_FORMAT(`start_at`,'%Y-%m-%d') = '{$start}'  OR DATE_FORMAT(`end_at`,'%Y-%m-%d') ='{$end}')");
                })
                ->whereRaw('(user_id = ' . Auth::id() . ' OR create_schedule_user_id = '. Auth::id() .')');

            if (isset($data['key_words']) && !empty($data['key_words'])) {
                $query->where('content', 'like', '%' . $data['key_words'] . '%');
            }
            $temp = $query->get()->toArray();
            $resultDB = [];
            $resultAllDayDB = [];
            if (empty($temp)) {
                $result['data'] = [];
                return returnJson($result['message'], $result['code'], $result['data']);
            }

            $ids = array_column($temp, 'schedule_id');
            $sql = DB::raw('schedule_id,count(*) as num');

            if (!empty($data['date'])) {
                $q = UserSchedules::where('create_schedule_user_id', '=', Auth::id())->whereBetween('created_at', [$start, $end]);
            } else {
                $q = UserSchedules::where('create_schedule_user_id', '=', Auth::id())->whereIn('schedule_id', $ids);
            }

            $scheduleNum = $q->select('confirm_yes as status', $sql)
                ->groupBy('confirm_yes', 'schedule_id')
                ->get()
                ->toArray();
            $re = $this->fetchConfirmStatusArray($scheduleNum);
            $i = 0;
            $h = 0;
            foreach ($temp as $key => $item) {

                $schedule = $item['has_one_schedules'];

                if($schedule['all_day_yes'] == ConstFile::SCHEDULE_ALL_DAY_YES){
                    $resultAllDayDB[$i]['user_schedule_id'] = $item['id'];
                    $resultAllDayDB[$i]['schedule_id'] = $schedule['id'];
                    $resultAllDayDB[$i]['content'] = $item['content'];
                    $resultAllDayDB[$i]['prompt_type'] = $item['prompt_type'];
                    $resultAllDayDB[$i]['created_at'] = $schedule['created_at'];
                    $resultAllDayDB[$i]['start_at'] = date('Y-m-d H:i', strtotime($schedule['start_at']));
                    $resultAllDayDB[$i]['end_at'] = date('Y-m-d H:i', strtotime($schedule['end_at']));
                    $resultAllDayDB[$i]['duration'] = intval((strtotime($schedule['end_at']) - strtotime($schedule['start_at'])) / ConstFile::HOUR);
                    $resultAllDayDB[$i]['address'] = $schedule['address'];
                    $resultAllDayDB[$i]['creator'] = $item['create_schedule_user_id'] == Auth::id() ? '我发出的' : $item['create_schedule_user_name'];
                    $resultAllDayDB[$i]['receive_users'] = $item['create_schedule_user_id'] != Auth::id() ? '' : array_column($schedule['has_many_schedule_users'], 'user_name');
                    $resultAllDayDB[$i]['confirm_num'] = isset($re[$item['schedule_id']]) ? $re[$item['schedule_id']] : [];
                    $i ++;
                } else {
                    $resultDB[$h]['user_schedule_id'] = $item['id'];
                    $resultDB[$h]['schedule_id'] = $schedule['id'];
                    $resultDB[$h]['content'] = $item['content'];
                    $resultDB[$h]['prompt_type'] = $item['prompt_type'];
                    $resultDB[$h]['created_at'] = $schedule['created_at'];
                    $resultDB[$h]['start_at'] = date('Y-m-d H:i', strtotime($schedule['start_at']));
                    $resultDB[$h]['end_at'] = date('Y-m-d H:i', strtotime($schedule['end_at']));
                    $resultDB[$h]['duration'] = intval((strtotime($schedule['end_at']) - strtotime($schedule['start_at'])) / ConstFile::HOUR);
                    $resultDB[$h]['address'] = $schedule['address'];
                    $resultDB[$h]['creator'] = $item['create_schedule_user_id'] == Auth::id() ? '我发出的' : $item['create_schedule_user_name'];
                    $resultDB[$h]['receive_users'] = $item['create_schedule_user_id'] != Auth::id() ? '' : array_column($schedule['has_many_schedule_users'], 'user_name');
                    $resultDB[$h]['confirm_num'] = isset($re[$item['schedule_id']]) ? $re[$item['schedule_id']] : [];
                    $h ++;
                }
            }
            $long = [];
            for ($i = 0; $i < 24; $i++) {
                $getTime = strtotime($data['date']) + ($i * ConstFile::HOUR);
                $resultLong = 0;
                foreach ($resultDB as $key => $item) {
                    $start = strtotime($item['start_at']);
                    $end = strtotime($item['end_at']);
                    if ($getTime >= $start && $getTime <= $end) {
                        $resultLong++;
                    }
                }
                foreach ($resultAllDayDB as $key => $item) {
                    $start = strtotime($item['start_at']);
                    $end = strtotime($item['end_at']);
                    if ($getTime >= $start && $getTime <= $end) {
                        $resultLong++;
                    }
                }
                $long[$i] = $resultLong;
            }

            $result['data']['long'] = max($long);
            $result['data']['data'] = $resultDB;
            $result['data']['all_day_data'] = $resultAllDayDB;
            return returnJson($result['message'], $result['code'], $result['data']);
        } catch (Exception $e) {
            $result['code'] = ConstFile::API_RESPONSE_FAIL;
            $result['message'] = $e->getMessage();
            return returnJson($result['message'], $result['code']);
        }
    }

    /**
     * 获取本人创建的日程的的各个状态对应的人数(未接受,接受,确认)
     * @param $data
     * @return \Illuminate\Http\JsonResponse|string
     */
    public function fetchConfirmPerson($data)
    {
        try {
            $result = ['code' => ConstFile::API_RESPONSE_SUCCESS, 'message' => ConstFile::API_RESPONSE_SUCCESS_MESSAGE];
            $scheduleId = $data['schedule_id'];
            $confirmStatus = $data['confirm_status'];
            $temp = UserSchedules::with('hasOneUser')->where('create_schedule_user_id', '=', Auth::id())
                ->where('schedule_id', '=', $scheduleId)
                ->where('confirm_yes', '=', $confirmStatus)
                ->get();

            $resultData = [];
            $temp->each(function ($item, $key) use (&$resultData) {
                $resultData[$key]['user_id'] = $item['user_id'];
                $resultData[$key]['user_name'] = $item['user_name'];
                $resultData[$key]['avatar'] = $item['hasOneUser']['avatar'] ?? config('user.const.avatar');
            });

            $result['data'] = $resultData;
            return returnJson($result['message'], $result['code'], $result['data']);
        } catch (Exception $e) {
            $result['code'] = ConstFile::API_RESPONSE_FAIL;
            $result['message'] = $e->getMessage();
            return returnJson($result['message'], $result['code']);
        }
    }

    /**
     * 更新日程:创建者角度
     * @param array $data
     * @return \Illuminate\Http\JsonResponse|string
     */
    public function updateRecord(array $data)
    {
        try {
            $result = ['code' => ConstFile::API_RESPONSE_SUCCESS, 'message' => ConstFile::API_RESPONSE_SUCCESS_MESSAGE];
            $error = $this->checkData($data, true);

            if ($error) {
                throw new Exception('请求参数错误：' . $error);
            }
            $participants = $data['participant_ids'];
            unset($data['participant_ids']);
            $participantsArray = json_decode($participants, true);

            DB::transaction(function () use ($data, $participantsArray) {
                //创建日程
                $userId = Auth::id();
                $userName = Auth::user()->chinese_name;
                $data['create_schedule_user_id'] = $userId;
                $schedule = Schedules::find($data['id']);
                if (!$schedule) {
                    throw new Exception(sprintf('不存id为%s的日程', $data['id']));
                }
                unset($data['id']);
                $result = $schedule->fill($data)->save();
                if (!$result) {
                    throw new Exception(sprintf('更新日程失败'));
                }
                $schedule->hasManyScheduleUsers()->delete();
                //创建日程和参与人的对应记录
                Collect($participantsArray)->each(function ($item, $key) use ($data, $schedule, $userId, $userName) {
                    $user = User::find($item);
                    $userData = null;
                    $userData['create_schedule_user_id'] = $data['create_schedule_user_id'];
                    $userData['schedule_id'] = $schedule->id;
                    $userData['content'] = $schedule->content;
                    $userData['user_id'] = $item;
                    $userData['create_schedule_user_name'] = $userName;
                    $userData['user_name'] = $user->name;
                    $userData['confirm_yes'] = ConstFile::SCHEDULE_STATUS_CONFIRM_NO;
                    UserSchedules::create($userData);
                });
            });
        } catch (Exception $e) {
            $result['code'] = ConstFile::API_RESPONSE_FAIL;
            $result['message'] = $e->getMessage();
        }
        return returnJson($result['message'], $result['code']);
    }

    /**
     * 更新日程:混合角度
     * @param array $data
     * @return \Illuminate\Http\JsonResponse|string
     */
    public function updateUserSchedule(array $data)
    {
        $result = ['code' => ConstFile::API_RESPONSE_SUCCESS, 'message' => ConstFile::API_RESPONSE_SUCCESS_MESSAGE];
        try {
            if (empty($data) || !isset($data['id']) || empty($data['id']) || empty($data['confirm_yes'])) {
                throw new Exception('请求数据不能为空');
            }
            $userSchedule = UserSchedules::find($data['id']);
            if (!$userSchedule) {
                throw new Exception('请求数据对应的记录不存在');
            }

            if (($userSchedule->user_id != Auth::id()) || !array_key_exists($data['confirm_yes'], ConstFile::$scheduleStatusList)) {
                throw new Exception('非法操作');
            }

            if ($userSchedule->confirm_at) {
                throw new Exception('状态已经更新过了,不能二次更新');
            }

            $data['confirm_at'] = Carbon::today()->toDateTimeString();
            $userSchedule->fill($data)->save();
        } catch (Exception $e) {
            $result['code'] = ConstFile::API_RESPONSE_FAIL;
            $result['message'] = $e->getMessage();
        }
        return returnJson($result['message'], $result['code']);
    }

    /**
     * 日程接受人设置自己的日程提醒类型
     * @param array $data
     * @return \Illuminate\Http\JsonResponse|string
     */
    public function setUserSchedulePromptType(array $data)
    {
        $result = ['code' => ConstFile::API_RESPONSE_SUCCESS, 'message' => ConstFile::API_RESPONSE_SUCCESS_MESSAGE];
        try {
            if (empty($data) || !isset($data['id']) || empty($data['id']) || !isset($data['prompt_type'])) {
                throw new Exception('请求数据不能为空');
            }

            $userSchedule = UserSchedules::find($data['id']);
            if (!$userSchedule) {
                throw new Exception('请求数据对应的记录不存在');
            }

            if (($userSchedule->user_id != Auth::id()) || !array_key_exists($data['prompt_type'], ConstFile::$schedulePromptTypeList)) {
                throw new Exception('非法操作');
            }

            $userSchedule->fill($data)->save();
        } catch (Exception $e) {
            $result['code'] = ConstFile::API_RESPONSE_FAIL;
            $result['message'] = $e->getMessage();
        }
        return returnJson($result['message'], $result['code']);
    }

    /**
     * 获取日程单条记录
     * @param $id
     * @return \Illuminate\Http\JsonResponse|string
     */
    public function fetchOne($id)
    {
        $result = ['code' => ConstFile::API_RESPONSE_SUCCESS, 'message' => ConstFile::API_RESPONSE_SUCCESS_MESSAGE];
        $data = [];
        try {
            $row = (new UserSchedules)->findOrFail($id);
            if ($row->user_id != Auth::id()) {
                throw new Exception('非法操作');
            }

            $resultDB = $re = [];
            if (Auth::id() == $row->create_schedule_user_id) {
                $sql = DB::raw('schedule_id,count(*) as num');
                $q = UserSchedules::where('create_schedule_user_id', '=', Auth::id());
                $scheduleNum = $q->select('confirm_yes as status', $sql)
                    ->groupBy('confirm_yes', 'schedule_id')
                    ->get()
                    ->toArray();
                $re = $this->fetchConfirmStatusArray($scheduleNum);
            }

            $schedule = Schedules::with(['hasManyScheduleUsers', 'reportInfo'])->findOrFail($row->schedule_id)->toArray();//待优化
            $resultDB['schedule_id'] = $schedule['id'];
            $resultDB['content'] = $schedule['content'];
            $resultDB['created_at'] = $schedule['created_at'];
            $resultDB['start_at'] = $schedule['start_at'];
            $resultDB['end_at'] = $schedule['end_at'];
            $resultDB['prompt_type'] = $row['prompt_type'];
            $resultDB['all_day_yes'] = $schedule['all_day_yes'];
            $resultDB['send_type'] = $schedule['send_type'];
            $resultDB['repeat_type'] = $schedule['repeat_type'];
            $now = time();
            $end_at = strtotime($schedule['end_at']);
            $resultDB['overdue_days'] = $now < $end_at ? 0 : Carbon::today()->diffInDays($schedule['end_at']);
            $resultDB['address'] = $schedule['address'];
            $resultDB['creator'] = $row['create_schedule_user_id'] == Auth::id() ? '我发出的' : $row['create_schedule_user_name'];
            $resultDB['receive_users'] = $row['create_schedule_user_id'] != Auth::id() ? '' : array_column($schedule['has_many_schedule_users'], 'user_name');
            $resultDB['confirm_num'] = isset($re[$row['schedule_id']]) ? $re[$row['schedule_id']] : [];

            //关联汇报
            $resultDB['report_info'] = $schedule['reportInfo'];

            $result['data'] = $resultDB;
            return returnJson($result['message'], $result['code'], $result['data']);
        } catch (Exception $e) {
            $result['code'] = ConstFile::API_RESPONSE_FAIL;
            $result['message'] = $e->getMessage();
        }
        return returnJson($result['message'], $result['code'], $data);
    }

    /**
     *验证数据
     * @param $data
     * @param bool $editFlag
     * @return null|string
     */
    private function checkData($data, $editFlag = false)
    {
        if (empty($data)) {
            return '请求数据不能为空';
        }

        if ($editFlag && (!isset($data['id']) || empty($data['id']))) {
            return '日程ID不能为空';
        }

        if (!isset($data['content']) || empty($data['content'])) {
            return '日程内容不能为空';
        }
        if (!isset($data['all_day_yes'])) {
            return '是否全天对应的数据为空';
        }
        if (!isset($data['start_at']) || empty($data['start_at'])) {
            return '开始时间不能为空';
        }
        if (!isset($data['end_at']) || empty($data['end_at'])) {
            return '截止不能为空';
        }
        if (!isset($data['send_type']) || empty($data['send_type'])) {
            return '发送方式不能为空';
        }
        if (!isset($data['prompt_type']) || !array_key_exists($data['prompt_type'], ConstFile::$schedulePromptTypeList)) {
            return '提醒方式不能为空';
        }
        if (!isset($data['repeat_type']) || !array_key_exists((int)$data['prompt_type'], ConstFile::$scheduleStatusRepeatList)) {
            return '重复方式不能为空';
        }

        if (!isset($data['address']) || empty($data['address'])) {
            return '地点不能为空';
        }

        if (!isset($data['participant_ids']) || empty($data['participant_ids'])) {
            return '参与人不能为空';
        }
        return null;
    }

    private function checkSearchData($data)
    {
//        if(empty($data)){
//            return '请求参数不能为空';
//        }
//
//        if(!isset($data['user_id'])){
//            return '用户的ID不能为空';
//        }

        if(!isset($data['date'])){
            return '时间不能为空';
        }
        return null;
    }

    /**
     * 获取日程状态对应的接收人列表(包括个人信息)
     * @param $scheduleNum
     * @return array
     */
    private function fetchConfirmStatusArray($scheduleNum)
    {
        $t = [];
        foreach ($scheduleNum as $b) {
            $t[$b['schedule_id']][$b['status']] = $b['num'];
        }

        $re = [];

        foreach ($t as $k => $r) {
            if (!isset($r[ConstFile::SCHEDULE_STATUS_CONFIRM_NO])) {
                $r[ConstFile::SCHEDULE_STATUS_CONFIRM_NO] = 0;
            }
            if (!isset($r[ConstFile::SCHEDULE_STATUS_CONFIRM_YES])) {
                $r[ConstFile::SCHEDULE_STATUS_CONFIRM_YES] = 0;
            }
            if (!isset($r[ConstFile::SCHEDULE_STATUS_CONFIRM_REJECT])) {
                $r[ConstFile::SCHEDULE_STATUS_CONFIRM_REJECT] = 0;
            }
            $re[$k] = $r;
        }
        return $re;
    }

}
