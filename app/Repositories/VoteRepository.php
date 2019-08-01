<?php

namespace App\Repositories;

use App\Constant\ConstFile;
use App\Models\Message\Message;
use App\Models\User;
use App\Models\Vote\Vote;
use App\Models\Vote\VoteDepartment;
use App\Models\VoteOption;
use App\Models\VoteParticipant;
use App\Models\VoteRecord;
use App\Models\VoteRule;
use App\Models\VoteType;
use Carbon\Carbon;
use Exception;
use DB;

class VoteRepository extends Repository
{
    public function model()
    {
        return Vote::class;
    }

    /**
     * @description 创建投票
     * @author liushaobo
     * @time 2019/3/23
     * @param array $data
     * @param $user
     * @return false|\Illuminate\Http\JsonResponse|string
     * @throws \Throwable
     */
    public function createOrEditVote(array $data, $user)
    {
        try {
            switch ($data['act']) {
                case 'create':
                    $error = $this->checkData($data);
                    if ($error) {
                        throw new Exception('请求参数错误：' . $error);
                    }
                    $department = app()->make(UsersRepository::class);
                    $participantsArray = $department->getVoteChildUsers($data['department_id']);//这是通过部门获取模拟用户id
                    if (!$participantsArray) {
                        throw new Exception('选择的部门没有人员');
                    }
                    $optionNamesArray = $data['vote_option'];
                    unset($data['act']);
                    unset($data['vote_option']);
                    DB::transaction(function () use ($data, $participantsArray, $optionNamesArray, $user) {
                        //创建投票
                        $userName = $user->chinese_name;//Auth::user()->name;//先写死
                        $data['create_vote_user_id'] = $user->id;//暂时先写死，实际应为auth id
                        $data['created_at'] = date('Y-m-d H:i:s');
                        $data['user_name'] = $userName;
                        $data['company_id'] = $user->company_id;
                        $Vote = Vote::create($data);
                        //创建投票与选项的记录
                        Collect($optionNamesArray)->each(function ($item, $key) use ($data, $Vote) {
                            $optionData = array();
                            $optionData['v_id'] = $Vote->id;
                            $optionData['option_name'] = $item;
                            $optionData['created_at'] = date('Y-m-d H:i:s');
                            VoteOption::create($optionData);
                        });
                        $userData = array();
                        foreach ($participantsArray as $key => $item) {
                            $userData[$key]['create_vote_user_id'] = $user->id;
                            $userData[$key]['v_id'] = Q($Vote, 'id');
                            $userData[$key]['describe'] = Q($Vote, 'describe');
                            $userData[$key]['user_id'] = $item['user_id'];
                            $userData[$key]['created_at'] = date('Y-m-d H:i:s');
                            $userData[$key]['create_vote_user_name'] = $user->chinese_name;

                            $userData[$key]['user_name'] = $item['chinese_name'];//$user->name == '' ? '' : $user->name;
                            $userData[$key]['confirm_yes'] = Vote::VOTE_STATUS_CONFIRM_DEFAULT;
                            $userData[$key]['avatar'] = $item['avatar'];

                        }
                        VoteParticipant::query()->insert($userData);
                        $voteDepartmentData = array();
                        foreach ($data['department_id'] as $key => $val) {
                            $voteDepartmentData[$key]['company_id'] = Q($user,'company_id');
                            $voteDepartmentData[$key]['department_id'] = $val;
                            $voteDepartmentData[$key]['v_id'] = Q($Vote, 'id');
                            $voteDepartmentData[$key]['created_at'] = date('Y-m-d H:i:s');
                        }
                        VoteDepartment::query()->insert($voteDepartmentData);
                    });
                    break;
                case 'edit':
                    break;
                case 'cancel':
                    if (!isset($data) || empty($data) || empty($data['id'])) {
                        throw new Exception(sprintf('请求数据不能为空'));
                    }
                    $id = $data['id'];
                    $Vote = $this->object_to_array(DB::table('vote')->where(['id' => $id, 'create_vote_user_id' => $user->id])->first());
                    if (!$Vote) {
                        throw new Exception(sprintf('不存id为%s的投票', $id));
                    }
                    $data['updated_at'] = date('Y-m-d H:i:s');
                    $result = DB::table('vote')->where('id', $id)->update(['state' => 2]);
                    if ($result === false) {
                        throw new Exception(sprintf('投票取消失败'));
                    }
                    break;
                default:
                    throw new Exception('请填写请求类型（act=create：添加，edit：编辑）');
                    break;
            }
        } catch (Exception $e) {
            return returnJson($e->getMessage(), $e->getCode());
        }
        return returnJson(ConstFile::API_RESPONSE_SUCCESS_MESSAGE);
    }


    /**
     * @description 投票
     * @author liushaobo
     * @time 2019\3\24
     * @param array $data
     * @param $user
     * @return false|\Illuminate\Http\JsonResponse|string
     * @throws \Throwable
     */
    public function votingOperation(array $data, $user)
    {
        try {
            if (!isset($data) || empty($data) || empty($data['v_id']) || empty($data['vo_id']) || !is_array($data['vo_id'])) {
                throw new Exception(sprintf('请选择投票选项'));
            }
            $userId = $user->id;//用户ID
            $voteId = $data['v_id'];
            $optionIdArray = $data['vo_id'];
            //DB::connection()->enableQueryLog();#开启执行日志
            $vote = Vote::find($voteId);
            $voteParticipant = $this->object_to_array(DB::table('vote_participant')->where(['v_id' => $voteId, 'user_id' => $userId])->first());
            $voteRule = $this->object_to_array(DB::table('vote_rule')->where(['is_show' => 1])->orderBy('id', 'desc')->first());
            $voteOption = DB::table('vote_option')->whereIn('id', $optionIdArray)->pluck('id');
            $voteRecord = DB::table('vote_record')->where(['v_id' => $voteId, 'user_id' => $userId])->first();
            $end_at = strtotime($vote->end_at);
            $time = time();

            foreach ($voteOption as $k => $v) {
                if (empty($v) && !in_array($k, $optionIdArray)) throw new Exception(sprintf('投票选项不存在'));
            }
            if (!$vote) {
                throw new Exception(sprintf('该投票已失效'));
            }
            if ($vote->state != Vote::VOTE_STATE_NORMAL) {
                throw new Exception(sprintf('该投票'.Vote::$voteStateList[$vote->state]));
            }
            if ($time >= $end_at) {
                throw new Exception(sprintf('该投票已结束'));
            }
            if (!$voteParticipant) {
                throw new Exception(sprintf('您不是该投票的参与者'));
            }
            if ($voteParticipant['confirm_yes'] == Vote::VOTE_STATUS_CONFIRM_YSE) {
                throw new Exception(sprintf('您已投过票了'));
            }
            if ($voteRecord) {
                throw new Exception(sprintf('您已投过票了'));
            }
            $voteRule = app()->make(UsersRepository::class);
            $voteRuleArray = $voteRule->getVoteCount($userId, $voteId);//这里是职级权重票数

            if (!$voteRuleArray) {
                throw new Exception(sprintf('尚未参与该投票'));
            }
            $data['number'] = $voteRuleArray['count'];//默认为1票
            DB::transaction(function () use ($data, $optionIdArray, $user, $vote, $voteRuleArray) {
                //创建投票
                $voteId = $data['v_id'];
                $increment = $vote->where(['id' => $voteId])->increment('number', $data['number']);
                //创建投票的记录
                $voteRecordData = array();
                foreach ($optionIdArray as $key => $item) {
                    $voteRecordData[$key]['user_id'] = $user->id;
                    $voteRecordData[$key]['vo_id'] = $item;
                    $voteRecordData[$key]['v_id'] = $voteId;
                    $voteRecordData[$key]['v_number'] = $data['number'];
                    $voteRecordData[$key]['user_name'] = $user->name;
                    $voteRecordData[$key]['avatar'] = $user->fetchAvatar();//Q($user, 'avatar');//$user->avatar;
                    $voteRecordData[$key]['created_at'] = Carbon::create();
                }
                VoteRecord::query()->insert($voteRecordData);

                $voteOptionlist = $vote->voteOption->toArray();
                if (!$voteOptionlist) {
                    throw new Exception(sprintf('投票选项不存在'));
                }
                $voteParticipantList = $vote->hasManyVoteParticipant;
                Collect($voteOptionlist)->each(function ($item, $key) use ($vote, $voteRuleArray, $voteParticipantList) {
                    $voteNumber = VoteRecord::where('vo_id', $item['id'])->sum('v_number');
                    $optionData = array();
                    $numberCount = count($voteParticipantList->toArray());
                    $percentage = percentage($numberCount, $voteNumber);
                    $optionData['percentage'] = $percentage;
                    $optionData['vote_number'] = $voteNumber;
                    if ($percentage >= $voteRuleArray['passing_rate']) {
                        $optionData['state'] = Vote::VOTE_OPTION_STATE_ADOPT;
                        Vote::query()->where('id', $vote['id'])->update(['state' => Vote::VOTE_STATE_ADOPT]);
                    }
                    VoteOption::query()->where('id', $item['id'])->update($optionData);
                });
                $voteParticipant['confirm_yes'] = 1;
                VoteParticipant::query()->where(['v_id' => $voteId, 'user_id' => $user->id])->update($voteParticipant);
                $voteInfo = Vote::find($voteId);
                if ($voteInfo['state'] == Vote::VOTE_STATE_ADOPT) {
                    $messageData = array();
                    foreach ($voteParticipantList->toArray() as $key => $item) {
                        $messageData[$key]['receiver_id'] = $item['user_id'];
                        $messageData[$key]['sender_id'] = $voteInfo['create_vote_user_id'];//系统发送
                        $messageData[$key]['content'] = $voteInfo['vote_title'];
                        $messageData[$key]['created_at'] = Carbon::create();
                        $messageData[$key]['relation_id'] = $voteId;
                        $messageData[$key]['type'] = Message::MESSAGE_TYPE_VOTE;
                    }
                    Message::query()->insert($messageData);
                }
            });
            if (!$voteParticipant) {
                throw new Exception(sprintf('尚未参与该投票'));
            }
        } catch (Exception $e) {
            return returnJson($e->getMessage(), ConstFile::API_RESPONSE_FAIL);
        }
        return returnJson(ConstFile::API_RESPONSE_SUCCESS_MESSAGE);
    }

    /**
     * @description 投票列表
     * @author liushaobo
     * @time 2019\3\27
     * @param array $data
     * @return false|\Illuminate\Http\JsonResponse|string
     */
    public function getVoteList(array $data, $user)
    {
        try {
            $userId = $user->id;//模拟用户ID 先写死
            $build = $this->builder();

            $voteParticipant = DB::table('vote_participant');
            if (empty($data) || !isset($data['perpage']) || empty($data['perpage']) || !isset($data['page']) || empty($data['page']) || !isset($data['act']) || empty($data['act'])) {
                throw new Exception('请求数据不能为空', ConstFile::API_RESPONSE_FAIL);
            }
            switch ($data['act']) {
                case 'creator':

                    isset($data['keywords']) && !empty($data['keywords']) ? $build->where('vote_title', 'like', '%' . $data['keywords'] . '%') : '';
                    $votelist = $build->leftJoin('vote_participant', 'vote.id', '=', 'vote_participant.v_id')
                        ->whereRaw('(vote.create_vote_user_id = ? or vote_participant.user_id = ?)', [$userId, $userId])
                        ->orderBy('vote.id', 'desc')
                        ->groupBy(['id'])
                        ->get(['vote.*', 'vote_participant.user_id'])
                        ->paginate($data['perpage']);

                    if (!$votelist) {
                        throw new Exception('没有数据了', ConstFile::API_RESPONSE_SUCCESS);
                    }
                    $list = $votelist->toArray();
                    $data = Collect($list['data'])->transform(function ($item, $key) use ($user) {
                        if ($item['create_vote_user_id'] == $user->id) {
                            $item['typeMsg'] = '我发出的';
                        } elseif ($item['user_id'] == $user->id) {
                            $item['typeMsg'] = $item['user_name'];
                        }
                        $voteParticipant = VoteParticipant::where('v_id', $item['id'])->select('id', 'user_name', 'confirm_yes')->get();
                        if ($voteParticipant) {
                            $item['participantstatol'] = count($voteParticipant->toArray());
                            $item['participants'] = $voteParticipant->toArray();
                            $confirm_yes = 0;//初始化已投票
                            $confirm_no = 0;//初始化未投票
                            foreach ($voteParticipant->toArray() as $key => $val) {
                                if ($val['confirm_yes'] == 1) {
                                    $confirm_yes++;
                                }
                            }
                            $confirm_no = $item['participantstatol'] - $confirm_yes;
                            $item['confirm_yes'] = $confirm_yes;
                            $item['onfirm_no'] = $confirm_no;
                        }
                        $voteoption = VoteOption::where('v_id', $item['id'])->with(['hasManyVoteRecord', 'hasManyMyVoteRecord' => function ($query) use ($user) {
                            $query->where('user_id', $user->id);
                        }])->select(['id', 'option_name', 'percentage', 'state', 'vote_number'])->get();
                        $voteOptionList = $voteoption->toArray();
                        foreach ($voteOptionList as $key => $val) {
                            $voteOptionList[$key]['has_many_my_vote_record'] = count($val['has_many_my_vote_record']);
                            if ($item['participantstatol'] == 0) {
                                $percentage = 0;
                            } else {
                                $percentage = $val['vote_number'] / $item['participantstatol'] * 100;
                            }
                            $voteOptionList[$key]['percentage'] = sprintf("%.1f", round($percentage, 1));
                        }


                        //$voteoption->with('hasManyVoteRecordCount');


                        if ($voteoption) {
                            $item['voteoptionstatol'] = count($voteOptionList);
                            $item['voteoptions'] = $voteOptionList;
                        }
                        return $item;
                    })->toArray();
                    break;
                case 'participant':
                    $confirm_yes = isset($data['confirm_yes']) && !empty($data['confirm_yes']) ? 1 : 0;
                    $void = isset($data['vo_id']) && !empty($data['vo_id']) ? $data['vo_id'] : 0;

                    if (!isset($data['v_id']) || empty($data['v_id'])) {
                        throw new Exception('请传入投票编号', ConstFile::API_RESPONSE_FAIL);
                    }
                    $voteParticipants = VoteParticipant::where(['v_id' => $data['v_id'], 'confirm_yes' => $confirm_yes])
                        ->when($void, function ($query) use ($void) {
                            $query->whereHas('hasManyVoteRecord', function ($qeury) use ($void) {
                                $qeury->where('vo_id', $void);
                            });
                        })
                        ->with(['hasOneUser'])
                        ->paginate($data['perpage']);
                    if (!$voteParticipants) {
                        throw new Exception('没有数据了', ConstFile::API_RESPONSE_SUCCESS);
                    }
                    foreach ($voteParticipants as $item) {
                        $item->avatar = $item->hasOneUser->fetchAvatar();
                    }
                    $list = $voteParticipants->toArray();
                    if (empty($list['data'])) {
                        throw new Exception('没有数据了', ConstFile::API_RESPONSE_SUCCESS);
                    }

                    $data = $list['data'];
                    break;
                default:
                    throw new Exception('请填写正确的请求类型');
                    break;
            }
        } catch (Exception $e) {
            return returnJson($e->getMessage(), $e->getCode());
        }
        return returnJson(ConstFile::API_RESPONSE_SUCCESS_MESSAGE_DATA, ConstFile::API_RESPONSE_SUCCESS, $data);
    }

    /**
     * @description 投票详情
     * @author liushaobo
     * @time 2019\3\27
     * @param $id
     * @return false|\Illuminate\Http\JsonResponse|string
     */
    public function getVoteInfo($id, $user)
    {
        try {
            if (empty($id) || !isset($id)) {
                throw new Exception(sprintf('请求数据不能为空'));
            }
            $userId = $user->id;
            $vote = Vote::find($id);
            if (empty($vote)) {
                throw new Exception(sprintf('未找到该投票'));
            }
            $voteinfo = $vote->toArray();

            $users = User::find($vote['create_vote_user_id']);
            $voteParticipant = VoteParticipant::where(['v_id' => $id, 'user_id' => $userId])->first();
            if ($voteinfo['create_vote_user_id'] != $userId && empty($voteParticipant)) {
                throw new Exception(sprintf('抱歉，你不是该投票的发起者，不能查看该投票哦'));
            } elseif ($voteinfo['create_vote_user_id'] == $userId) {
                $typeMsg = '我发出的';
            } elseif (Q($voteParticipant, 'user_id') == $userId) {
                $typeMsg = $users->chinese_name;
            }
            $voteRecord = VoteRecord::where('v_id', $id)->get();
            if (!empty($voteRecord)) {
                $voteRecord = $voteRecord->toArray();
            }

            $myVoteRecord = VoteRecord::where(['user_id' => $userId, 'v_id' => $id])->first();

            $voteParticipantList = VoteParticipant::where('v_id', $id)->select('id', 'user_name', 'confirm_yes')->get();

            $data['typeMsg'] = $typeMsg;
            $data['created_at'] = $vote['created_at'];
            $data['vote_title'] = $vote['vote_title'];
            $data['describe'] = $vote['describe'];
            $data['enclosure_url'] = $vote['enclosure_url'];
            $data['record_sum'] = count($voteRecord);
            $data['record_list'] = $voteRecord;
            $data['vote_type_name'] = $vote['vote_type_name'];
            $data['selection_type'] = $vote['selection_type'];
            $data['end_at'] = $vote['end_at'];
            $data['vo_id'] = Q($myVoteRecord, 'vo_id');
            if ($voteParticipantList) {
                $data['participantstatol'] = count($voteParticipantList->toArray());
                $data['participants'] = $voteParticipantList->toArray();
                $confirm_yes = 0;//初始化已投票
                $confirm_no = 0;//初始化未投票
                foreach ($data['participants'] as $key => $val) {
                    if ($val['confirm_yes'] == 1) {
                        $confirm_yes++;
                    }
                }
                $confirm_no = $data['participantstatol'] - $confirm_yes;
                $data['confirm_yes'] = $confirm_yes;
                $data['onfirm_no'] = $confirm_no;

            }
            $voteOption = VoteOption::where('v_id', $id)->with(['hasManyVoteRecord', 'hasManyMyVoteRecord' => function ($query) use ($user) {
                $query->where('user_id', $user->id);
            }])->select(['id', 'option_name', 'percentage', 'state', 'vote_number'])->get();;
            $voteOptionList = $voteOption->toArray();
            foreach ($voteOptionList as $key => $val) {
                $voteOptionList[$key]['has_many_my_vote_record'] = count($val['has_many_my_vote_record']);
                if ($data['participantstatol'] == 0) {
                    $percentage = 0;
                } else {
                    $percentage = $val['vote_number'] / $data['participantstatol'] * 100;
                }
                $voteOptionList[$key]['percentage'] = sprintf("%.1f", round($percentage, 1));
            }
            $data['option_list'] = $voteOptionList;
        } catch (Exception $e) {
            return returnJson($e->getMessage(), ConstFile::API_RESPONSE_FAIL);
        }
        return returnJson(ConstFile::API_RESPONSE_SUCCESS_MESSAGE_DATA, ConstFile::API_RESPONSE_SUCCESS, $data);
    }

    /**
     * @description 投票选项
     * @author liushaobo
     * @time 2019\4\16
     * @param $id
     * @param $user
     * @return false|\Illuminate\Http\JsonResponse|string
     */
    public function getVoteOption($id, $user)
    {
        try {
            if (empty($id) || !isset($id)) {
                throw new Exception(sprintf('请求数据不能为空'));
            }
            $userId = $user->id;
            $vote = Vote::find($id);
            if (empty($vote)) {
                throw new Exception(sprintf('不存id为%s的投票', $id), ConstFile::API_RESPONSE_FAIL);
            }
            $data = $vote->voteOption->toArray();


        } catch (Exception $e) {
            return returnJson($e->getMessage(), $e->getCode());
        }
        return returnJson(ConstFile::API_RESPONSE_SUCCESS_MESSAGE_DATA, ConstFile::API_RESPONSE_SUCCESS, $data);
    }

    /**
     * @description 投票初始化数据
     * @author liushaobo
     * @time 2019\4\9
     * @return false|\Illuminate\Http\JsonResponse|string
     */
    public function voteInitialise()
    {

        try {
            $voteType = VoteType::get()->toArray();
            $voteRule = VoteRule::get()->toArray();
            $data = [
                'voteType' => $voteType,
                'voteRule' => $voteRule,
            ];
        } catch (Exception $e) {
            return returnJson($e->getMessage(), ConstFile::API_RESPONSE_FAIL);
        }

        return returnJson(ConstFile::API_RESPONSE_SUCCESS_MESSAGE_DATA, ConstFile::API_RESPONSE_SUCCESS, $data);
    }


    /**
     * @description 投票详情
     * @author liushaobo
     * @time 2019\3\27
     * @param $id
     * @return false|\Illuminate\Http\JsonResponse|string
     */
    public function getVoteInfoUp($id, $user)
    {
        try {
            if (empty($id) || !isset($id)) {
                throw new Exception(sprintf('请求数据不能为空'));
            }
            $message = Message::find($id);
            $message->update(['read_status' => Message::READ_STATUS_YES]);
            if (!$message) {
                throw new Exception(sprintf('不存在id为%s的消息', $id));
            }
            $voteId = $message->relation_id;
            $userId = $user->id;
            $vote = Vote::find($voteId);
            if (empty($vote)) {
                throw new Exception(sprintf('未找到该投票'));
            }
            $voteinfo = $vote->toArray();
            $users = User::find($vote['create_vote_user_id']);
            $voteParticipant = VoteParticipant::where(['v_id' => $voteId, 'user_id' => $userId])->first();
            if ($voteinfo['create_vote_user_id'] != $userId && empty($voteParticipant)) {
                throw new Exception(sprintf('抱歉，你不是该投票的发起者，不能查看该投票哦'));
            } elseif ($voteinfo['create_vote_user_id'] == $userId) {
                $typeMsg = '我发出的';
            } elseif (Q($voteParticipant, 'user_id') == $userId) {
            }
            $voteRecord = VoteRecord::where('v_id', $voteId)->get();
            if (!empty($voteRecord)) {
                $voteRecord = $voteRecord->toArray();
            }

            $myVoteRecord = VoteRecord::where(['user_id' => $userId, 'v_id' => $voteId])->first();

            $voteParticipantList = VoteParticipant::where('v_id', $voteId)->select('id', 'user_name', 'confirm_yes')->get();

            $data['typeMsg'] = $typeMsg;
            $data['created_at'] = $vote['created_at'];
            $data['vote_title'] = $vote['vote_title'];
            $data['enclosure_url'] = $vote['enclosure_url'];
            $data['record_sum'] = count($voteRecord);
            $data['record_list'] = $voteRecord;
            $data['vote_type_name'] = $vote['vote_type_name'];
            $data['selection_type'] = $vote['selection_type'];
            $data['end_at'] = $vote['end_at'];
            $data['vo_id'] = Q($myVoteRecord, 'vo_id');
            if ($voteParticipantList) {
                $data['participantstatol'] = count($voteParticipantList->toArray());
                $data['participants'] = $voteParticipantList->toArray();
                $confirm_yes = 0;//初始化已投票
                $confirm_no = 0;//初始化未投票
                foreach ($data['participants'] as $key => $val) {
                    if ($val['confirm_yes'] == 1) {
                        $confirm_yes++;
                    }
                }
                $confirm_no = $data['participantstatol'] - $confirm_yes;
                $data['confirm_yes'] = $confirm_yes;
                $data['onfirm_no'] = $confirm_no;

            }
            $voteOption = VoteOption::where('v_id', $voteId)->with(['hasManyVoteRecord', 'hasManyMyVoteRecord' => function ($query) use ($user) {
                $query->where('user_id', $user->id);
            }])->select(['id', 'option_name', 'percentage', 'state', 'vote_number'])->get();;
            $voteOptionList = $voteOption->toArray();
            foreach ($voteOptionList as $key => $val) {
                $voteOptionList[$key]['has_many_my_vote_record'] = count($val['has_many_my_vote_record']);
                $percentage = $val['vote_number'] / $data['participantstatol'] * 100;
                $voteOptionList[$key]['percentage'] = sprintf("%.1f", round($percentage, 1));
            }
            $data['option_list'] = $voteOptionList;

            Message::query()->where('id', $id)->update(['read_status' => Message::READ_STATUS_YES]);
        } catch (Exception $e) {
            return returnJson($e->getMessage(), ConstFile::API_RESPONSE_FAIL);
        }
        return returnJson(ConstFile::API_RESPONSE_SUCCESS_MESSAGE_DATA, ConstFile::API_RESPONSE_SUCCESS, $data);
    }

    public function voteRankRule()
    {

    }

    /**
     * @description 检查参数
     * @author liushaobo
     * @time 2019\3\21
     * @param $data
     * @return string|null
     */
    private function checkData($data)
    {
        if (empty($data)) {
            return '请求数据不能为空';
        }
        if (!isset($data['act']) || empty($data['act'])) {
            return '请填写请求类型（act=create：添加，edit：编辑）';
        }
        if (!isset($data['vote_title']) || empty($data['vote_title'])) {
            return '投票主题不能为空';
        }
        if (!isset($data['vote_option']) || empty($data['vote_option'])) {
            return '请选择投票选项';
        }
        if (!isset($data['end_at']) || empty($data['end_at'])) {
            return '截止时间不能为空';
        }
        if (Carbon::createFromTimeString($data['end_at'])->timestamp < time()) {
            return '投票截止时间不能小于当前时间';
        }
        if (!isset($data['rule_id']) || empty($data['rule_id'])) {
            return '请选择投票规则';
        }
        if (!isset($data['prompt_type']) || empty($data['prompt_type'])) {
            return '提醒方式不能为空';
        }
        if (!isset($data['department_id']) || empty($data['department_id']) || !is_array($data['department_id'])) {
            return '请选择参与人';
        }
        if (!isset($data['vote_type_id']) || empty($data['vote_type_id'])) {
            return '请选择投票类型';
        }
        if (!isset($data['passing_rate']) || empty($data['passing_rate'])) {
            return '请选择投票类型';
        }

        return null;
    }

    /**
     * @description object转array
     * @author liushaobo
     * @time 2019/3/23
     * @param $obj
     * @return array|void
     */
    public function object_to_array($obj)
    {
        $obj = (array)$obj;
        foreach ($obj as $k => $v) {
            if (gettype($v) == 'resource') {
                return;
            }
            if (gettype($v) == 'object' || gettype($v) == 'array') {
                $obj[$k] = (array)object_to_array($v);
            }
        }
        return $obj;
    }
}
