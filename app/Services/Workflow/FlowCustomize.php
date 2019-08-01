<?php

namespace App\Services\Workflow;

use App\Constant\ConstFile;
use App\Exceptions\DiyException;
use App\Models\Executive\Cars;
use App\Models\Executive\CarsUse;
use App\Models\User;
use App\Models\Workflow\AuthorizeAgent;
use App\Models\Workflow\Entry;
use App\Models\Workflow\Flow;
use App\Models\Workflow\Flowlink;
use App\Models\Workflow\Proc;
use App\Models\Workflow\Process;
use App\Models\Workflow\Workflow;
use App\Services\AuthUserShadowService;
use Request;
use Auth;
use DevFixException;

class FlowCustomize
{
    //发起审核流程
    public static function EntryFlow($data, $flow_no, $id = 0){
        $flow_id = Workflow::getWorkFlowIdByFlowNo($flow_no);
        $flow = Flow::findById($flow_id);
        $flow->checkCanApply(); // 校验是否可以提交该流程的申请,不可提交申请的话,创建或者修改申请单,也没有意义
        $data['flow_id'] = $flow_id;
        $entry = self::updateOrCreateEntry($data, $flow_no, $id); // 创建或更新申请单
        if ($entry->isInHand()) {
            $flow_link = Flowlink::firstStepLink($entry->flow_id);
            if($flow_no == Entry::WORK_FLOW_NO_EXECUTIVE_CREATE_CAR_SENDBACK){
                //归还车辆 - 指定首个审批人 为该车辆的负责人
                $car_use = CarsUse::query()->find($data['cars_use_id']);
                if(empty($car_use))
                    throw new DiyException("用车ID数据错误", ConstFile::API_RESPONSE_FAIL);

                $auditor_id = User::query()->where('id', $car_use->driver_id)
                    ->pluck('id')->first();
                self::customizeAuditor($entry, $auditor_id, $flow_link->process_id);
            }else{
                //进程初始化
                (new Workflow())->setFirstProcessAuditor($entry, $flow_link);
            }
        }
        $entry->save();
        return $entry;
    }


    /**
     * 手动指定审批人
     * @param Entry $entry
     * @param $auditor_ids
     * @param $process_id
     * @return bool
     * @throws DevFixException
     */
    public static function customizeAuditor(Entry $entry, $auditor_id, $process_id){
        if (!$auditor_id) {
            $auditors = collect([User::noBody()]);
        } else {
            $auditors = User::find($auditor_id); // 获取审批人信息
        }
        if (empty($auditors)) {
            throw new DevFixException("下一步骤未找到审核人", ConstFile::API_RESPONSE_FAIL);
        }

        $time = time();
        // 生成proc
        Proc::create([
            'entry_id'     => $entry->id,
            'flow_id'      => $entry->flow_id,
            'process_id'   => $process_id,
            'process_name' => Process::find($process_id)->process_name,
            'user_id'      => $auditors->id,
            'user_name'    => $auditors->chinese_name,
            'dept_name'    => '', //$v->dept->dept_name,
            'circle'       => $entry->circle,
            'status'       => Proc::STATUS_IN_HAND,
            'is_read'      => Proc::IS_READ_NO,
            'concurrence'  => $time,
        ]);
        /******************** 生成proc end *******************/

        /******************* 生成代理审批proc begin *****************/
        // 获取当前有效的代理人
        $agents = AuthorizeAgent::getValidAgents([$auditor_id], $entry->flow->flow_no);
        foreach ($agents as $agent) {
            // 生成代理proc
            Proc::create([
                'entry_id'         => $entry->id,
                'flow_id'          => $entry->flow_id,
                'process_id'       => $process_id,
                'process_name'     => Process::find($process_id)->process_name,
                'user_id'          => $agent->agent_user_id,
                'user_name'        => $agent->agent_user_name,
                'dept_name'        => '', //$v->dept->dept_name,
                'circle'           => $entry->circle,
                'status'           => Proc::STATUS_IN_HAND,
                'is_read'          => Proc::IS_READ_NO,
                'concurrence'      => $time,
                'authorizer_ids'   => $agent->authorizer_user_id,
                'authorizer_names' => $agent->authorizer_user_name,
            ]);
        }

        /******************* 生成代理审批proc end *****************/
        return true;
    }


    public static function updateOrCreateEntry($data, $flow_no, $id = 0)
    {
        $data['file_source_type'] = 'workflow';
        $data['file_source'] =$flow_no;
        $data['is_draft'] = null;
        $data['entry_id'] = null;

        if (!$id) {
            $authApplyer = new AuthUserShadowService(); // 以影子用户作为申请人
            $entry = Entry::create([
                'title' => $data['title'],
                'flow_id' => $data['flow_id'],
                'user_id' => $authApplyer->id(),
                'circle' => 1,
                'status' => Entry::STATUS_IN_HAND,
                'origin_auth_id' => Auth::id(),
                'origin_auth_name' => Auth::user()->name,
                'order_no'         => Entry::generateOrderNo($flow_no),
            ]);
        } else {
            $entry = Entry::findOrFail($id);
            $entry->checkEntryCanUpdate(); // 校验申请单是否可以修改
            $entry->update($data);
        }
        if (!empty($data['is_draft'])) {
            $entry->status = Entry::STATUS_DRAFT;
        } else {
            $entry->status = Entry::STATUS_IN_HAND;
        }
        return $entry;
    }
}