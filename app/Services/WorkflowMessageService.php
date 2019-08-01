<?php
namespace App\Services;

use App\Models\Roles;
use App\Models\User;
use App\Models\Workflow\Entry;
use App\Models\Workflow\Proc;
use App\Services\Message\MessageService;
use Exception;

class WorkflowMessageService
{
    const TYPE_MAIL   = 'mail';
    const TYPE_WECHAT = 'wechat';

    /**
     * 流程流转入下一节点通知
     *
     * @param Entry $entry
     */
    public static function passNotify(Entry $entry)
    {
        // 通过通知下一个节点的审批人
        $procs    = $entry->procs;
        $params   = $entry->entry_data->pluck('field_value', 'field_name')->toArray();

        foreach ($procs as $proc) {
            if ($proc->status != Proc::STATUS_IN_HAND) {
                // 只取当前待处理节点
                continue;
            }
            if (!$proc->user) {
                continue;
            }
            MessageService::send($entry->flow->flow_no . '_pass', array_merge($params, [
                'applicant' => $entry->user->chinese_name,
                'url'       => sprintf('%s/workflow/proc/%d', self::getHost(), $proc->id),
                'to'        => [
                    'email'  => [$proc->user->email],
                    'wechat' => [$proc->user->name],
                ],
            ]));
        }
    }

    /**
     * 审批驳回通知
     *
     * @param \App\Models\Workflow\Proc $proc
     */
    public static function rejectNotify(Proc $proc)
    {
        // 驳回通知申请人
        $entry  = $proc->entry;
        $params = $entry->entry_data->pluck('field_value', 'field_name')->toArray();

        MessageService::send($entry->flow->flow_no . '_reject', array_merge($params, [
            'applicant' => $entry->user->chinese_name,
            'url'       => sprintf('%s/workflow/entry/%d', self::getHost(), $entry->id),
            'to'        => [
                'email'  => [$entry->user->email],
                'wechat' => [$entry->user->name],
            ],
        ]));
    }

    /**
     * 审批完成通知
     *
     * @param \App\Models\Workflow\Proc $proc
     */
    public static function completeNotify(Proc $proc)
    {
        // 完成通知申请人
        $entry  = $proc->entry;
        $params = $entry->entry_data->pluck('field_value', 'field_name')->toArray();

        MessageService::send($entry->flow->flow_no . '_complete', array_merge($params, [
            'applicant' => $entry->user->chinese_name,
            'url'       => sprintf('%s/workflow/entry/%d', self::getHost(), $entry->id),
            'to'        => [
                'email'  => [$entry->user->email],
                'wechat' => [$entry->user->name],
            ],
        ]));
    }

    /**
     * 发送报警信息给某个系统角色！！！非流程角色
     * @param Exception $exception
     * @param $role
     * @param string $url
     * @author hurs
     */
    public static function warningException(Exception $exception, $role, $url = '')
    {
        $role = Roles::firstByName($role);
        if ($role) {
            if ($role->assigned_roles) {
                $user_ids = $role->assigned_roles->pluck('entity_id');
                $users    = User::findByIds($user_ids)->pluck('name')->toArray();
                $wechat   = [
                    'to'      => [
                        'wechat' => $users,
                    ],
                    'message' => $exception->getMessage(),
                    'url'     => $url,
                ];
                MessageService::send('exception_common_tpl', $wechat);
            }
        }

    }

    public static function getHost()
    {
        $protocol = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ||
            (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https'))
            ? 'https://' : 'http://';

        return $protocol . $_SERVER['HTTP_HOST'];
    }
}
