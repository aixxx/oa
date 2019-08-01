<?php
/**
 * Created by PhpStorm.
 * User: Alex
 * Date: 2018/8/8
 * Time: 下午5:30
 */
return [
    'attendance_retroactive_times' => env('WORKFLOW_ATTENDANCE_RETROACTIVE_TIMES', 3),//用户每个月的补签次数上限

    'export_import_file' => '/data/www/workflow.json',//工作流导入导出时数据存储的文件路径
];