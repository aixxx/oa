<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 2018/7/13
 * Time: 10:29
 */

/**
 * 操作类型 action
 */
//const LOGIN_SYS     = "login_sys"; //登录系统
//const ACTION_STORE  = "store"; //创建
//const ACTION_UPDATE = "update"; //更新
//const ACTION_DELETE = "delete"; //删除
//
///**
// * 对象类型 type
// */
//const TYPE_USER            = "user"; //员工
//const TYPE_DEPARTMENT      = "department";   //部门
//const TYPE_COMPANY         = "company";      //公司
//const TYPE_PENDING_USER    = "pending_user"; //带入职


return [
    'default' => [
        //登录路由配置
        'login'              => [
            'action' => "login_sys",
            'type'   => "user",
        ],
        //员工路由配置
        'users.store'        => [
            'action' => "store",
            'type'   => "user",
        ],
        'users.update'       => [
            'action' => "update",
            'type'   => "user",
        ],
        'users.delete'       => [
            'action' => "delete",
            'type'   => "user",
        ],

        //部门路由配置
        'departments.store'  => [
            'action' => "store",
            'type'   => "department",
        ],
        'departments.update' => [
            'action' => "update",
            'type'   => "department",
        ],
        'departments.delete' => [
            'action' => "delete",
            'type'   => "department",
        ],

        //公司路由配置
        'companies.store'    => [
            'action' => "store",
            'type'   => "company",
        ],

        'companies.update' => [
            'action' => "update",
            'type'   => "company",
        ],

        'companies.delete'   => [
            'action' => "delete",
            'type'   => "company",
        ],

        //待入职路由配置
        'pendingusers.store' => [
            'action' => "store",
            'type'   => "pending_user",
        ],

        'pendingusers.update' => [
            'action' => "update",
            'type'   => "pending_user",
        ],

        'pendingusers.delete' => [
            'action' => "delete",
            'type'   => "pending_user",
        ],

        'attendance.update' => [
            'action' => "update",
            'type'   => "attendance_update",
        ],
    ],
    'admin'   => [
        //管理员登录路由配置
        'admin.login'         => [
            'action' => "admin_login_sys",
            'type'   => "admin",
        ],
        //管理员路由配置
        'admin.users.store'   => [
            'action' => "store",
            'type'   => "admin_user",
        ],
        'admin.users.update'  => [
            'action' => "update",
            'type'   => "admin_user",
        ],
        'admin.roles.store'   => [
            'action' => "store",
            'type'   => "admin_roles",
        ],
        'admin.roles.update'  => [
            'action' => "update",
            'type'   => "admin_roles",
        ],
        'admin.roles.destroy' => [
            'action' => "destroy",
            'type'   => "admin_roles",
        ],
    ],
];
