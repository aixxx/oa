<?php
/**
 *注意：
 *添加新的路由之后需求在下面添加新的能力对应关系规则如下：
 * level=1的层级的no默认是上一个同级的一级菜单的no加1
 * * level=2的层级的no默认是父级的no*100+1（新的同级no继续+1，以此类推）
 * * *level=3的层级的no默认是父级的no*100+1（新的同级no继续+1，以此类推）
 *
 * 举例如下：
 *
 * 'system'  => [
            'no'       => 12,
            'title'    => '系统管理',
            'children' => [
                     1201 => [
                        'icon'        => 'icon dripicons-message',
                        'title'       => '消息模板',
                        'level'       => 2,
                        'no'          => 1201,
                        'default_url' => 'message-template/index',
                        'children'    => [
                            'message_template' => [ /***此处的值：message_template要和下面的 prefix的值一致
                                'title'       => '消息模板',
                                'prefix'      => 'message_template',
                                'default_url' => 'message-template/index',
                                'no'          => 1251,
                                'abilities'   => [
                                'show'   => [
                                    'message.template.index',
                                    'message.template.view',
                                            ],
                                ],
                            ],
                        ],
                    ],
            ],
    ],
 *
 */

//能力与路由的对应关系
$titleMap = [
    'create'      => '增加',
    'delete'      => '删除',
    'edit'        => '编辑',
    'show'        => '查看',
];
//权限管理

$adminAbilities = [
    'title'       => '权限',
    'prefix'      => 'admin_abilities',
    'default_url' => 'admin/abilities',
    'no'          => 301,
    'abilities'   => [
        'create' => [
            'admin.abilities.create',
            'admin.abilities.store',
        ],
        'edit'   => [
            'admin.abilities.edit',
            'admin.abilities.update',
        ],
        'delete' => [
            'admin.abilities.destroy',
        ],
        'show'   => [
            'admin.abilities.index',
            'admin.abilities.show',
        ],
    ],
];

//员工管理子项目
$users = [
    'title'       => '花名册',
    'level'       => 3,
    'default_url' => 'users',
    'prefix'      => 'users',
    'no'          => 821,
    'abilities'   => [
        'create'    => [
            'users.create',
            'users.store',
            'users.uploadimg',
        ],
        'delete'    => [
            'users.delete',
        ],
        'edit'      => [
            'users.edit',
            'users.update',
            'users.admin_add_bank_card',
        ],
        'show'      => [
            'users.index',
            'users.show',
            'users.search',
        ],
        'dimission' => [
            'dimission.create',
            'dimission.store',
            'dimission.update',
            'dimission.edit',
            'dimission.show',
        ],
        'export'    => [
            'users.download',
        ],
    ],
];

$approval = [
    'title'       => '审批',
    'prefix'      => 'approval',
    'default_url' => 'approval/index',
    'no'          => 842,
    'abilities'   => [
        'show' => [
            'workflow.approval.index',
            'workflow.approval.flow',
            'workflow.approval.flow_export',
            'workflow.entry.hr_show',
        ],
    ],
];

$adminRoles = [
    'title'       => '角色',
    'prefix'      => 'admin_roles',
    'default_url' => 'admin/roles',
    'no'          => 302,
    'abilities'   => [
        'create' => [
            'admin.roles.create',
            'admin.roles.store',
        ],
        'edit'   => [
            'admin.roles.edit',
            'admin.roles.update',
        ],
        'delete' => [
            'admin.roles.destroy',
        ],
        'show'   => [
            'admin.roles.index',
            'admin.roles.show',
        ],
    ],
];

$adminUsers = [
    'title'       => '管理员',
    'prefix'      => 'admin_users',
    'default_url' => 'admin/users',
    'no'          => 303,
    'abilities'   => [
        'create' => [
            'admin.users.create',
            'admin.users.store',
        ],
        'edit'   => [
            'admin.users.edit',
            'admin.users.update',
        ],
        'delete' => [
            'admin.users.destroy',
        ],
        'show'   => [
            'admin.users.index',
            'admin.users.show',
        ],
    ],
];

$deptDepart = [
    'title'       => '部门管理列表',
    'prefix'      => 'deptdepart',
    'default_url' => 'deptuser/depart',
    'no'          => 811,
    'abilities'   => [
        'create' => [
            'departments.create',
            'departments.store',
        ],
        'delete' => [
            'departments.delete',
        ],
        'edit'   => [
            'departments.edit',
            'departments.update',
        ],
        'show'   => [
            'departments.show',
            'departments.all',
            'dept.depart',
        ],
    ],
];

$workflowRole = [
    'title'       => '审批角色',
    'prefix'      => 'workflow_role',
    'default_url' => 'workflow/role',
    'no'          => 1331,
    'abilities'   => [
        'create' => [
            'workflow.role.create',
            'workflow.role.store',
            'workflow.role_user.create',
            'workflow.role_user.store',
        ],
        'edit'   => [
            'workflow.role.edit',
            'workflow.role.update',
        ],
        'delete' => [
            'workflow.role.destroy',
            'workflow.role_user.destroy',
        ],
        'show'   => [
            'workflow.role.index',
            'workflow.role.show',
            'workflow.role_user.index',
        ],
    ],
];

$flow_manage     = [
    'title'       => '流程管理',
    'prefix'      => 'flow_manage',
    'default_url' => 'workflow/flow',
    'no'          => 1311,
    'abilities'   => [
        'create' => [
            'workflow.flow.index',
            'workflow.flow.create',
            'workflow.flow.store',
            'workflow.flow.publish',
            'workflow.flow.clone_new_version',
            'workflow.flow.import',
        ],
        'edit'   => [
            'workflow.flow.edit',
            'workflow.flow.update',
            'workflow.flow.design',
            'workflow.process.condition',
            'workflow.process.attribute',
            'workflow.process.begin',
            'workflow.process.stop',
            'workflow.process.index',
            'workflow.process.create',
            'workflow.process.store',
            'workflow.process.edit',
            'workflow.process.update',
            'workflow.process.show',
            'workflow.process.destroy',
            'workflow.flowlink.auth.dept',
            'workflow.flowlink.auth.role',
            'workflow.flowlink.auth.emp',
            'workflow.flowlink',
        ],
        'delete' => [
            'workflow.flow.destroy',
            'workflow.flow.set_abandon',
        ],
        'show'   => [
            'workflow.flow.index',
            'workflow.flow.show',
            'workflow.flow.export',
        ],
    ],
];
$template_manage = [
    'title'       => '模板管理',
    'prefix'      => 'template_manage',
    'default_url' => 'workflow/template',
    'no'          => 1321,
    'abilities'   => [
        'create' => [
            'workflow.template.create',
            'workflow.template.store',
            'workflow.template_form.create',
            'workflow.template_form.store',
        ],
        'edit'   => [
            'workflow.template.edit',
            'workflow.template.update',
            'workflow.template_form.edit',
            'workflow.template_form.update',
        ],
        'delete' => [
            'workflow.template.destroy',
            'workflow.template_form.destroy',
        ],
        'show'   => [
            'workflow.template.index',
            'workflow.template.show',
        ],
    ],
];

//流程申请
$applyForProcess = [
    'title'       => '流程申请',
    'prefix'      => 'workflow_apply',
    'default_url' => 'workflow/entry/create',
    'no'          => 711,
    'abilities'   => [
        'show' => [
            'workflow.common.work-time',
            'workflow.common.date-interval',
            'workflow.common.work-time-by-hour',
            'workflow.common.company-list',
            'workflow.common.contracts-list',
            'workflow.pass',
            'workflow.pass-next',
            'workflow.proc.unpass_all',
            'workflow.proc.pass_all',
            'workflow.reject',
            'workflow.reject-next',
            'workflow.proc.children',
            'workflow.proc.index',
            'workflow.proc.show',
            'workflow.proc.children',
            'workflow.entry.create',
            'workflow.entry.index',
            'workflow.entry.edit',
            'workflow.entry.destroy',
            'workflow.entry.store',
            'workflow.entry.show',
            'workflow.entry.todo-list',
            'workflow.entry.store-system',
            'workflow.entry.resend',
            'workflow.entry.cancel',
            'workflow.entry.my_apply',
            'workflow.entry.my_procs',
            'workflow.entry.my_audited',
            'workflow.entry.data',
        ],
        'edit' => [
            'workflow.entry.update',
        ],
    ],
];
//待办事项
$toDoList = [
    'title'       => '待办事项',
    'prefix'      => 'toDoList',
    'default_url' => 'workflow/entry/todo-list',
    'no'          => 712,
    'abilities'   => [
        'show' => [
            'workflow.entry.index',
            'workflow.entry.todo-list',
        ],
    ],
];

//流程查询
$processQuery = [
    'title'       => '流程查询',
    'prefix'      => 'processQuery',
    'default_url' => 'workflow/entry/process_query',
    'no'          => 714,
    'abilities'   => [
        'show' => [
            'workflow.entry.process_query',
        ],
    ],
];

//财务管理
$accountSubject = [
    'title'       => '会计科目管理',
    'prefix'      => 'financialManage',
    'default_url' => 'finance/financialManage/accountSubject',
    'no'          => 1999,
    'abilities'   => [
        'create' => [
            'finance.financialManage.create',
            'finance.financialManage.store',
        ],
        'delete' => [
            'finance.financialManage.delete',
        ],
        'edit'   => [
            'finance.financialManage.edit',
            'finance.financialManage.update',
        ],
        'show'   => [
            'finance.financialManage.index',
            'finance.financialManage.show',
            'finance.financialManage.check',
        ],
    ],
];

return [
    'menu'     => [
        'mine'  => [
            'no'       => 7,
            'title'    => '我的',
            'level'    => 1,
            'children' => [
                71 => [
                    'icon'     => 'icon dripicons-ticket',
                    'title'    => '我的工作流',
                    'level'    => 2,
                    'no'       => 71,
                    'children' => [
                        'workflow_apply'  => $applyForProcess,
                        'toDoList'        => $toDoList,
                        'processQuery'    => $processQuery,
                    ],
                ],
            ],
        ],
        'hr'    => [
            'no'       => 8,
            'title'    => '人事管理',
            'level'    => 1,
            'children' => [
                81 => [
                    'icon'        => 'icon dripicons-folder',
                    'title'       => '部门管理',
                    'level'       => 2,
                    'no'          => 81,
                    'default_url' => 'deptuser/depart',
                    'children'    => [
                        'deptdepart' => $deptDepart,
                    ],
                ],
				82 => [
                    'icon'        => 'icon dripicons-folder',
                    'title'       => '花名册',
                    'level'       => 2,
                    'no'          => 82,
                    'default_url' => 'users',
                    'children'    => [
                        'deptdepart' => $users,
                    ],
                ],
            ],
			
			
        ],
		
        'workflow'  => [
            'no'       => 13,
            'title'    => '流程管理',
            'level'    => 1,
            'children' => [
                131 => [
                    'icon'        => 'icon dripicons-code',
                    'title'       => '流程设置',
                    'level'       => 2,
                    'no'          => 131,
                    'default_url' => $flow_manage['default_url'],
                    'children'    => [
                        'flow_manage' => $flow_manage,
                    ],
                ],
                132 => [
                    'icon'        => 'icon dripicons-tag',
                    'title'       => '模板管理',
                    'level'       => 2,
                    'no'          => 132,
                    'default_url' => $template_manage['default_url'],
                    'children'    => [
                        'template_manage' => $template_manage,
                    ],
                ],
                133 => [
                    'icon'        => 'icon dripicons-user-group',
                    'title'       => '审批角色',
                    'level'       => 2,
                    'no'          => 133,
                    'default_url' => 'workflow/role',
                    'children'    => [
                        'workflow_role' => $workflowRole,
                    ],
                ],
            ],
        ],

        'finance'    => [
            'no'       => 14,
            'title'    => '财务管理',
            'children' => [
                141 => [
                    'icon'        => 'icon dripicons-gear',
                    'title'       => '会计科目管理',
                    'level'       => 2,
                    'no'          => 141,
//                    'default_url' => 'financialManage/sofzz',
                    'children'    => [
                        'sofzz' => [
                            'title'       => '会计准则',
                            'level'       => 3,
                            'prefix'      => 'sofzz',
                            'default_url' => 'financialManage/sofzz',
                            'no'          => 831,
                            'abilities'   => [
                            ],
                        ],
                        'account_subject' => [
                            'title'       => '会计科目',
                            'level'       => 3,
                            'prefix'      => 'account_subject',
                            'default_url' => 'financialManage/accountSubject',
                            'no'          => 842,
                            'abilities'   => [
                            ],
                        ],
                    ],
                ],

                142 => [
                    'icon'        => 'icon dripicons-gear',
                    'title'       => '币种管理',
                    'level'       => 2,
                    'no'          => 142,
                    'default_url' => 'financialManage/coin',
                    'children'    => [
                        'coin' => [
                            'title'       => '币种管理',
                            'prefix'      => 'coin',
                            'default_url' => 'financialManage/coin',
                            'no'          => 1453,
                            'abilities'   => [

                            ],
                        ],
                    ],
                ],

                133 => [
                    'icon'        => 'icon dripicons-gear',
                    'title'       => '费控预算管理',
                    'level'       => 2,
                    'no'          => 133,
                    'default_url' => 'financialManage/costBudget',
                    'children'    => [
                        'budget' => [
                            'title'       => '费控预算管理',
                            'prefix'      => 'budget',
                            'default_url' => 'financialManage/costBudget',
                            'no'          => 1454,
                            'abilities'   => [

                            ],
                        ],
                    ],
                ],

                134 => [
                    'icon'        => 'icon dripicons-tag',
                    'title'       => '凭证模板',
                    'level'       => 2,
                    'no'          => 134,
                    'default_url' => 'financialManage/voucher',
                    'children'    => [
                        'voucher' => [
                            'title'       => '凭证模板',
                            'prefix'      => 'voucher',
                            'default_url' => 'financialManage/voucher',
                            'no'          => 1455,
                            'abilities'   => [

                            ],
                        ],
                    ],
                ],

                125 => [
                    'icon'        => 'icon dripicons-gear',
                    'title'       => '结算类型管理',
                    'level'       => 2,
                    'no'          => 125,
                    'default_url' => 'financialManage/balance',
                    'children'    => [
                        'voucher' => [
                            'title'       => '结算类型管理',
                            'prefix'      => 'balance',
                            'default_url' => 'financialManage/balance',
                            'no'          => 1456,
                            'abilities'   => [

                            ],
                        ],
                    ],
                ],
            ],
        ],
        'customer'    => [
            'no'       => 15,
            'title'    => '客户管理',
            'children' => [
//                151 => [
//                    'icon'        => 'icon dripicons-code',
//                    'title'       => '客户标签设置',
//                    'level'       => 2,
//                    'no'          => 151,
//                    'default_url' => 'customerManage/customerType',
//                    'children'    => [
//                        'customerType' => [
//                            'title'       => '客户标签设置',
//                            'prefix'      => 'coin',
//                            'default_url' => 'customerManage/customerType',
//                            'no'          => 1551,
//                            'abilities'   => [
//
//                            ],
//                        ],
//                    ],
//                ],
                152 => [
                    'icon'        => 'icon dripicons-code',
                    'title'       => '客户公海设置',
                    'level'       => 2,
                    'no'          => 152,
                    'default_url' => 'customerManage/seasPublic',
                    'children'    => [
                        'seasPublic' => [
                            'title'       => '客户公海设置',
                            'prefix'      => 'coin',
                            'default_url' => 'customerManage/seasPublic',
                            'no'          => 1552,
                            'abilities'   => [

                            ],
                        ],
                    ],
                ],
                153 => [
                    'icon'        => 'icon dripicons-code',
                    'title'       => '防撞单设置',
                    'level'       => 2,
                    'no'          => 153,
                    'default_url' => 'customerManage/avoidance',
                    'children'    => [
                        'avoidance' => [
                            'title'       => '防撞单设置',
                            'prefix'      => 'coin',
                            'default_url' => 'customerManage/avoidance',
                            'no'          => 1553,
                            'abilities'   => [

                            ],
                        ],
                    ],
                ],
            ],
        ],
        'systems'    => [
            'no'       => 16,
            'title'    => '系统管理',
            'level'    => 1,
            'children' => [
                161 => [
                    'icon'        => 'icon dripicons-gear',
                    'title'       => '基础配置管理',
                    'level'       => 2,
                    'no'          => 161,
                    'default_url' => 'Basic/index',
                ],
            ],
        ],
    ],
    'titleMap' => $titleMap,
];




