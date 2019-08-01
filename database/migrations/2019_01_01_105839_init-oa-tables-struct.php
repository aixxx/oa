<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class InitOaTablesStruct extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public $sql;

    public function up()
    {
        try {
            $allTableSql = $this->fetchAllInitTables();
            collect($allTableSql)->each(function ($item, $key) {
                DB::statement($item);
            });
        } catch (Exception $e) {

        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }

    private function fetchAllInitTables()
    {
        $abilities = <<<EOF
CREATE TABLE `abilities` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `entity_id` int(10) unsigned DEFAULT NULL,
  `entity_type` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `only_owned` tinyint(1) NOT NULL DEFAULT '0',
  `scope` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `level1_no` int(11) NOT NULL COMMENT '一级菜单',
  `level2_no` int(11) NOT NULL COMMENT '二级菜单',
  `level3_no` int(11) NOT NULL COMMENT '三级菜单',
  `root_code` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '一级菜单代码',
  PRIMARY KEY (`id`),
  UNIQUE KEY `abilities_name_unique` (`name`),
  KEY `abilities_scope_index` (`scope`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
EOF;


        $admins = <<<EOF
CREATE TABLE `admins` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `wechat_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `admins_name_unique` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
EOF;

        $assigned_roles = <<<EOF
CREATE TABLE `assigned_roles` (
  `role_id` int(10) unsigned NOT NULL,
  `entity_id` int(10) unsigned NOT NULL,
  `entity_type` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `scope` int(11) DEFAULT NULL,
  KEY `assigned_roles_entity_index` (`entity_id`,`entity_type`,`scope`),
  KEY `assigned_roles_role_id_index` (`role_id`),
  KEY `assigned_roles_scope_index` (`scope`),
  CONSTRAINT `assigned_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
EOF;

        $attendance_checkinout = <<<EOF
CREATE TABLE `attendance_checkinout` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL COMMENT '考勤机员工id',
  `check_time` datetime NOT NULL COMMENT '签卡时间',
  `sensor_id` int(11) NOT NULL COMMENT '考勤机编号',
  `sn` varchar(32) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '考勤机序列号',
  PRIMARY KEY (`id`),
  KEY `Idx_user_id` (`user_id`),
  KEY `idx_check_time` (`check_time`),
  KEY `attendance_checkinout_user_id_check_time_sensor_id_sn_index` (`user_id`,`check_time`,`sensor_id`,`sn`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='考勤机打卡信息';
EOF;
        $attendance_holidays = <<<EOF
CREATE TABLE `attendance_holidays` (
  `holiday_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键',
  `holiday_date` datetime NOT NULL COMMENT '时间',
  `holiday_status` tinyint(1) unsigned NOT NULL COMMENT '状态(0-工作，1-休息)',
  `holiday_type` tinyint(1) unsigned NOT NULL COMMENT '是否法定节假日(0-否，1-是,周末不是)',
  `created_at` datetime NOT NULL DEFAULT '1000-01-01 00:00:00' COMMENT '创建时间',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`holiday_id`),
  UNIQUE KEY `UN_holiday_date` (`holiday_date`),
  KEY `holiday_date_INDEX` (`holiday_date`) USING BTREE,
  KEY `holiday_create_at_INDEX` (`created_at`) USING BTREE,
  KEY `holiday_update_at_INDEX` (`updated_at`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='节假日时间表';
EOF;
        $attendance_sheets = <<<EOF
CREATE TABLE `attendance_sheets` (
  `attendance_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `attendance_date` datetime NOT NULL COMMENT '日期',
  `attendance_user_id` int(11) DEFAULT NULL COMMENT '员工编号',
  `attendance_work_id` int(11) DEFAULT NULL COMMENT '班值类型',
  `attendance_begin_at` datetime DEFAULT NULL COMMENT '实际考勤上班时间',
  `attendance_end_at` datetime DEFAULT NULL COMMENT '实际考勤下班时间',
  `attendance_time` text COLLATE utf8mb4_unicode_ci COMMENT '打卡时间',
  `attendance_is_abnormal` tinyint(4) DEFAULT NULL COMMENT '是否异常(0:否 1:是)',
  `attendance_abnormal_note` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '异常说明',
  `attendance_create_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `attendance_update_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '修改时间',
  `attendance_length` float DEFAULT NULL,
  `attendance_is_manual` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否手动处理(0:自动;1.手动)',
  `attendance_holiday_type` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '休假类型1',
  `attendance_holiday_type_sub` double(8,2) NOT NULL DEFAULT '0.00' COMMENT '休假时长1',
  `attendance_holiday_entry_id` int(11) NOT NULL DEFAULT '0' COMMENT '流程编号1',
  `attendance_holiday_type_second` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '休假类型2',
  `attendance_holiday_type_sub_second` double(8,2) NOT NULL DEFAULT '0.00' COMMENT '休假时长2',
  `attendance_holiday_entry_id_second` int(11) NOT NULL DEFAULT '0' COMMENT '流程编号2',
  `attendance_travel_interval` double(8,2) NOT NULL DEFAULT '0.00' COMMENT '出差时长',
  `attendance_travel_entry_id` int(11) NOT NULL DEFAULT '0' COMMENT '出差流程编号',
  `attendance_overtime_sub` double(8,2) NOT NULL DEFAULT '0.00' COMMENT '加班时长',
  `attendance_overtime_entry_id` int(11) NOT NULL DEFAULT '0' COMMENT '加班流程编号',
  PRIMARY KEY (`attendance_id`),
  UNIQUE KEY `unique_date_user_id` (`attendance_date`,`attendance_user_id`),
  KEY `idx_date` (`attendance_date`),
  KEY `idx_user_id` (`attendance_user_id`),
  KEY `attendance_sheets_attendance_user_id_attendance_date_index` (`attendance_user_id`,`attendance_date`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='考勤明细';
EOF;
        $attendance_user_info = <<<EOF
CREATE TABLE `attendance_user_info` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL COMMENT '不可编辑系统自动生成',
  `badge_number` int(11) DEFAULT NULL COMMENT '考勤号码',
  `ssn` varchar(32) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '编号',
  `name` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '中文姓名',
  `employee_num` int(11) DEFAULT NULL COMMENT '员工唯一编号',
  PRIMARY KEY (`id`),
  UNIQUE KEY `UN_userid` (`user_id`),
  KEY `Idx_user_id` (`user_id`),
  KEY `Idx_employee_num` (`employee_num`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='考勤机员工信息';
EOF;
        $attendance_vacation_changes = <<<EOF
CREATE TABLE `attendance_vacation_changes` (
  `change_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `change_user_id` int(11) DEFAULT NULL COMMENT '被修改人id',
  `change_type` varchar(32) NOT NULL DEFAULT '' COMMENT '调整假日类型 1:法定年假 2：公司福利年假 3：全薪假 4：调休',
  `change_before_amount` varchar(32) NOT NULL DEFAULT '0' COMMENT '调整前假期余额',
  `change_after_amount` varchar(32) NOT NULL DEFAULT '0' COMMENT '调整后假期余额',
  `change_amount` varchar(32) NOT NULL DEFAULT '0' COMMENT '调整的数量',
  `change_remark` varchar(32) NOT NULL DEFAULT '' COMMENT '调整原因备注',
  `change_unit` varchar(20) NOT NULL DEFAULT 'hour' COMMENT '节假日结算单位(hour/day)',
  `change_entry_id` varchar(128) NOT NULL DEFAULT '' COMMENT '流程编号',
  `created_at` timestamp NULL DEFAULT NULL COMMENT '创建时间',
  `updated_at` timestamp NULL DEFAULT NULL COMMENT '修改时间',
  `update_user_id` varchar(191) DEFAULT NULL,
  PRIMARY KEY (`change_id`),
  KEY `attendance_vacation_changes_change_user_id_index` (`change_user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COMMENT='假期调整记录流水表';
EOF;
        $attendance_vacation_conversions = <<<EOF
CREATE TABLE `attendance_vacation_conversions` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `annual_balance` varchar(32) DEFAULT NULL COMMENT '法定年假结余（小时）',
  `company_benefits_balance` varchar(32) DEFAULT NULL COMMENT '公司福利年假结余（小时）',
  `sum_amount` varchar(32) DEFAULT NULL COMMENT '总结余',
  `state` varchar(32) DEFAULT NULL COMMENT '结算状态：0未处理，1结算成功',
  `date_year` varchar(32) DEFAULT NULL COMMENT '结算年份',
  `created_at` datetime NOT NULL DEFAULT '1000-01-01 00:00:00' COMMENT '创建时间',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COMMENT='假期折算';
EOF;
        $attendance_vacations = <<<EOF
CREATE TABLE `attendance_vacations` (
  `vacation_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL COMMENT '员工编号',
  `annual` varchar(32) NOT NULL DEFAULT '0' COMMENT '法定年假 （小时）',
  `company_benefits` varchar(32) NOT NULL DEFAULT '0' COMMENT '公司福利年假（小时）',
  `marriage` varchar(191) NOT NULL DEFAULT '0' COMMENT '婚嫁（冗余）',
  `funeral` varchar(191) NOT NULL DEFAULT '0' COMMENT '丧假（冗余）',
  `maternity` varchar(191) NOT NULL DEFAULT '0' COMMENT '产假（冗余）',
  `paternity` varchar(191) NOT NULL DEFAULT '0' COMMENT '陪产假（冗余）',
  `check_up` varchar(191) NOT NULL DEFAULT '0' COMMENT '产检假（冗余）',
  `breastfeeding` varchar(191) NOT NULL DEFAULT '0' COMMENT '哺乳假（冗余）',
  `working_injury` varchar(191) NOT NULL DEFAULT '0' COMMENT '工伤假（冗余）',
  `full_pay_sick` varchar(32) NOT NULL DEFAULT '0' COMMENT '全薪病假（小时）',
  `sick` varchar(191) NOT NULL DEFAULT '0' COMMENT '病假（冗余）',
  `extra_day_off` varchar(32) NOT NULL DEFAULT '0' COMMENT '调休（小时）',
  `spring_festival` varchar(191) NOT NULL DEFAULT '0' COMMENT '春节假（冗余）',
  `business_trip` varchar(191) NOT NULL DEFAULT '0' COMMENT '出差假（冗余）',
  `created_at` timestamp NULL DEFAULT NULL COMMENT '创建时间',
  `updated_at` timestamp NULL DEFAULT NULL COMMENT '修改时间',
  `actual_annual` double(15,2) NOT NULL DEFAULT '0.00' COMMENT '实际发放法定年假',
  `actual_company_benefits` double(15,2) NOT NULL DEFAULT '0.00' COMMENT '实际发放福利年假',
  `actual_full_pay_sick` int(11) NOT NULL DEFAULT '0' COMMENT '实际发放全薪病假（小时）',
  PRIMARY KEY (`vacation_id`),
  UNIQUE KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COMMENT='员工可用假期表';
EOF;
        $attendance_white = <<<EOF
CREATE TABLE `attendance_white` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `chinese_name` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '中文名',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `user_id` int(11) NOT NULL DEFAULT '0' COMMENT '员工编号',
  PRIMARY KEY (`id`),
  KEY `attendance_white_chinese_name_index` (`chinese_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
EOF;
        $attendance_work_classes = <<<EOF
CREATE TABLE `attendance_work_classes` (
  `class_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `class_title` varchar(20) NOT NULL DEFAULT '' COMMENT '班值代码',
  `class_name` varchar(64) NOT NULL COMMENT '班值名称',
  `class_begin_at` varchar(32) DEFAULT NULL COMMENT '上班时间',
  `class_end_at` varchar(32) DEFAULT NULL COMMENT '下班时间',
  `class_rest_begin_at` varchar(32) DEFAULT NULL COMMENT '休息开始时间',
  `class_rest_end_at` varchar(32) DEFAULT NULL COMMENT '休息结束时间',
  `class_times` tinyint(1) NOT NULL COMMENT '一日几次班值',
  `class_create_user_id` int(11) NOT NULL COMMENT '创建人',
  `class_update_user_id` int(11) NOT NULL COMMENT '修改人',
  `class_create_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `class_update_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '修改时间',
  `type` tinyint(4) NOT NULL COMMENT '所属类型(1.客服类;2.职能类;3.弹性类)',
  PRIMARY KEY (`class_id`),
  KEY `Idx_class_title` (`class_title`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='排班班值';
EOF;
        $attendance_work_user_logs = <<<EOF
CREATE TABLE `attendance_work_user_logs` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `date` datetime NOT NULL COMMENT '时间',
  `class_title` varchar(20) DEFAULT NULL COMMENT '排班代码',
  `user_id` int(11) DEFAULT NULL COMMENT '员工编号',
  `status` tinyint(11) NOT NULL DEFAULT '1' COMMENT '状态(1:有效；2:无效)',
  `create_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `updat_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '修改时间',
  PRIMARY KEY (`id`),
  KEY `Idx_class_id_user_id` (`user_id`),
  KEY `Idx_date` (`date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='员工班值表流水';
EOF;
        $attendance_workflow_leaves = <<<EOF
CREATE TABLE `attendance_workflow_leaves` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `entry_id` int(11) DEFAULT NULL COMMENT '流程id',
  `user_id` int(11) DEFAULT NULL COMMENT '发起人id',
  `title` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '标题',
  `holiday_type` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '' COMMENT '休假类型',
  `date_begin` datetime NOT NULL COMMENT '开始时间',
  `date_end` datetime NOT NULL COMMENT '结束时间',
  `date_time_sub` float unsigned NOT NULL COMMENT '请假时长',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '申请时间',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '修改时间',
  `finished_at` timestamp NULL DEFAULT NULL COMMENT '审批完成时间',
  `remark` text COLLATE utf8mb4_unicode_ci COMMENT '备注',
  `file_upload` int(11) DEFAULT '0' COMMENT '上传附件',
  `ehr_deal_status` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'ehr数据处理状态',
  `is_resumed` tinyint(4) NOT NULL DEFAULT '0' COMMENT '休假1是否销假:(0:未销;1:已销)',
  PRIMARY KEY (`id`),
  UNIQUE KEY `entry_id_unique` (`entry_id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_entry_id` (`entry_id`),
  KEY `idx_date_begin_end` (`date_begin`,`date_end`),
  KEY `idx_finished_at` (`finished_at`),
  KEY `idx_holiday_type` (`holiday_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='流程请假申请';
EOF;
        $attendance_workflow_overtimes = <<<EOF
CREATE TABLE `attendance_workflow_overtimes` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `entry_id` int(11) DEFAULT NULL COMMENT '流程id',
  `user_id` int(11) DEFAULT NULL COMMENT '发起人id',
  `title` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '标题',
  `begin_time` datetime NOT NULL COMMENT '开始时间',
  `end_time` datetime NOT NULL COMMENT '结束时间',
  `time_sub_by_hour` float unsigned NOT NULL COMMENT '加班时长',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '申请时间',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '修改时间',
  `finished_at` timestamp NULL DEFAULT NULL COMMENT '审批完成时间',
  `note` text COLLATE utf8mb4_unicode_ci COMMENT '备注',
  `file_upload` int(11) NOT NULL DEFAULT '0' COMMENT '上传附件',
  `ehr_deal_status` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'ehr数据处理状态',
  PRIMARY KEY (`id`),
  UNIQUE KEY `entry_id_unique` (`entry_id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_entry_id` (`entry_id`),
  KEY `idx_date_begin_end` (`begin_time`,`end_time`),
  KEY `idx_finished_at` (`finished_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='流程加班申请';
EOF;
        $attendance_workflow_resumptions = <<<EOF
CREATE TABLE `attendance_workflow_resumptions` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `entry_id` int(11) DEFAULT NULL COMMENT '流程id',
  `user_id` int(11) DEFAULT NULL COMMENT '发起人id',
  `title` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '标题',
  `resumption_leave_list` int(11) DEFAULT NULL COMMENT '对应已审批请假流程id',
  `resumption_leave_length` float unsigned NOT NULL COMMENT '销假时长',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '申请时间',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '修改时间',
  `finished_at` timestamp NULL DEFAULT NULL COMMENT '审批完成时间',
  `resumption_leave_cause` text COLLATE utf8mb4_unicode_ci COMMENT '销假原因',
  `file_upload` int(11) NOT NULL DEFAULT '0' COMMENT '上传附件',
  PRIMARY KEY (`id`),
  UNIQUE KEY `entry_id_unique` (`entry_id`),
  UNIQUE KEY `resumption_leave_list_unique` (`resumption_leave_list`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_entry_id` (`entry_id`),
  KEY `idx_finished_at` (`finished_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='流程销假申请';
EOF;
        $attendance_workflow_retroactives = <<<EOF
CREATE TABLE `attendance_workflow_retroactives` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `entry_id` int(11) DEFAULT NULL COMMENT '流程id',
  `user_id` int(11) DEFAULT NULL COMMENT '发起人id',
  `title` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '标题',
  `retroactive_datatime` datetime NOT NULL COMMENT '补签时间',
  `retroactive_type` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '上下班类型',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '申请时间',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '修改时间',
  `finished_at` timestamp NULL DEFAULT NULL COMMENT '审批完成时间',
  `retroactive_reason` text COLLATE utf8mb4_unicode_ci COMMENT '补签原因',
  `note` text COLLATE utf8mb4_unicode_ci COMMENT '备注',
  `file_upload` int(11) DEFAULT '0' COMMENT '上传附件',
  PRIMARY KEY (`id`),
  UNIQUE KEY `entry_id_unique` (`entry_id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_entry_id` (`entry_id`),
  KEY `idx_finished_at` (`finished_at`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='流程签卡申请';
EOF;
        $attendance_workflow_travels = <<<EOF
CREATE TABLE `attendance_workflow_travels` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `entry_id` int(11) DEFAULT NULL COMMENT '流程id',
  `user_id` int(11) DEFAULT NULL COMMENT '发起人id',
  `chinese_name` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '中文姓名',
  `primary_dept` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0' COMMENT '主部门',
  `title` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '标题',
  `date_begin` datetime NOT NULL COMMENT '开始时间',
  `date_end` datetime NOT NULL COMMENT '结束时间',
  `date_interval` float unsigned NOT NULL COMMENT '出差天数（单位：天）',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '申请时间',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '修改时间',
  `finished_at` timestamp NULL DEFAULT NULL COMMENT '审批完成时间',
  `address` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '出差地点',
  `cause` text COLLATE utf8mb4_unicode_ci COMMENT '出差事由',
  `note` text COLLATE utf8mb4_unicode_ci COMMENT '备注',
  `file_upload` int(11) NOT NULL DEFAULT '0' COMMENT '上传附件',
  PRIMARY KEY (`id`),
  UNIQUE KEY `entry_id_unique` (`entry_id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_primary_dept` (`primary_dept`),
  KEY `idx_entry_id` (`entry_id`),
  KEY `idx_date_begin_end` (`date_begin`,`date_end`),
  KEY `idx_finished_at` (`finished_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='流程出差申请';
EOF;
        $companies = <<<EOF
CREATE TABLE `companies` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `legal_person` varchar(12) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `local` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `capital` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '注册资本',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `code` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '统一社会信用代码/注册号',
  `category` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '类型如''其他有限责任公司''',
  `establishment` date DEFAULT NULL COMMENT '成立日期',
  `business_start` date DEFAULT NULL COMMENT '营业期限开始日',
  `business_end` date DEFAULT NULL COMMENT '营业期限截止日',
  `registration_authority` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '登记机关',
  `approval_at` date DEFAULT NULL COMMENT '核准日期',
  `register_status` tinyint(4) DEFAULT '1' COMMENT '登记状态(1开业、2在业、3吊销、4注销、5迁入、6迁出、7停业、8清算)',
  `scope` text COLLATE utf8mb4_unicode_ci COMMENT '经营范围',
  `contact` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '企业联系电话',
  `employe_num` int(11) DEFAULT '0' COMMENT '从业人数',
  `female_num` int(11) DEFAULT '0' COMMENT '其中女性从业人数',
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '企业电子邮箱',
  `parent_id` int(11) DEFAULT '0' COMMENT '上级公司',
  `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '状态1.有效；2.删除',
  `abbr` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '公司名称缩写',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
EOF;
        $company_equity_pledge = <<<EOF
CREATE TABLE `company_equity_pledge` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `company_id` int(11) NOT NULL COMMENT '关联企业',
  `code` varchar(32) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '登记编号',
  `pledgor` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '出质人',
  `pledgor_id_number` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '出质人证照/证件号码',
  `amount` int(11) DEFAULT '0' COMMENT '出质股权数额',
  `pledgee` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '质权人',
  `pledgee_id_number` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '质权人证照/证件号码',
  `register_date` date DEFAULT NULL COMMENT '股权出质设立登记日期',
  `pledge_status` tinyint(4) DEFAULT NULL COMMENT '出质状态(1:有效;2.无效)',
  `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '状态1.有效；2.删除',
  `public_at` date DEFAULT NULL COMMENT '公示日期',
  `created_at` timestamp NULL DEFAULT NULL COMMENT '创建日期',
  `updated_at` timestamp NULL DEFAULT NULL COMMENT '修改日期',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='股权出质登记信息';
EOF;
        $company_main_personnels = <<<EOF
CREATE TABLE `company_main_personnels` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `company_id` int(11) NOT NULL COMMENT '关联企业',
  `name` varchar(32) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '姓名',
  `position` varchar(32) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '职位(董事/监事/经理等)',
  `created_at` timestamp NULL DEFAULT NULL COMMENT '创建时间',
  `updated_at` timestamp NULL DEFAULT NULL COMMENT '修改时间',
  `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '状态1.有效；2.删除',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='主要人员信息';
EOF;
        $company_shareholders = <<<EOF
CREATE TABLE `company_shareholders` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `company_id` int(11) NOT NULL COMMENT '关联企业',
  `name` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '名称',
  `shareholder_type` tinyint(4) DEFAULT '1' COMMENT '类型(1:自然人股东;2.法人股东)',
  `certificate_type` tinyint(4) DEFAULT '1' COMMENT '证照/证件类型(1:非公示项;2.非公司企业法人营业执照;3.合伙企业营业执照;4.企业法人营业执照(公司))',
  `id_number` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '证照/证件号码(非公示项/91230XXXX)',
  `created_at` timestamp NULL DEFAULT NULL COMMENT '创建时间',
  `updated_at` timestamp NULL DEFAULT NULL COMMENT '修改时间',
  `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '状态1.有效；2.删除',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='股东及出资信息';
EOF;
        $department_map_centres = <<<EOF
CREATE TABLE `department_map_centres` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL COMMENT '操作员工ID',
  `department_level1` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '一级部门',
  `department_level2` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '二级部门',
  `department_full_path` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '部门全路径',
  `centre_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '部门对应的中心名称',
  `times` int(11) NOT NULL COMMENT '次数',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
EOF;
        $department_tag = <<<EOF
CREATE TABLE `department_tag` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `tag_id` int(11) NOT NULL,
  `department_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
EOF;
        $department_user = <<<EOF
CREATE TABLE `department_user` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `department_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `is_leader` tinyint(1) NOT NULL DEFAULT '0',
  `is_primary` tinyint(1) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `department_user_department_id_index` (`department_id`),
  KEY `department_user_user_id_index` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
EOF;
        $departments = <<<EOF
CREATE TABLE `departments` (
  `auto_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id` int(11) NOT NULL DEFAULT '0' COMMENT '部门Id',
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `parent_id` int(11) NOT NULL,
  `order` int(11) NOT NULL,
  `deepth` tinyint(1) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `is_sync_wechat` tinyint(4) NOT NULL DEFAULT '1' COMMENT '是否要同步企业微信',
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`auto_id`),
  KEY `departments_id_index` (`id`),
  KEY `departments_auto_id_index` (`auto_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
EOF;
        $failed_jobs = <<<EOF
CREATE TABLE `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
EOF;
        $file_storage = <<<EOF
CREATE TABLE `file_storage` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL COMMENT '操作员工ID',
  `storage_full_path` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '实际存储地址',
  `storage_system` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '实际存储系统',
  `filehash` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '文件hash',
  `filename` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '文件名',
  `mime_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'meta_data',
  `source_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '文件来源类型',
  `source` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '文件来源',
  `content` text COLLATE utf8mb4_unicode_ci COMMENT '内容',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `file_storage_user_id_filehash_index` (`user_id`,`filehash`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
EOF;
        $finance_ap = <<<EOF
CREATE TABLE `finance_ap` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `entry_id` int(10) unsigned NOT NULL COMMENT '流程ID',
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '标题',
  `user_id` int(10) unsigned NOT NULL COMMENT '申请人ID',
  `user_name` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '申请人姓名',
  `department` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '所属部门',
  `company_name` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '所属公司',
  `payment_method` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '付款方式',
  `borrow_amount` bigint(20) unsigned NOT NULL COMMENT '借款金额，单位分',
  `repay_amount` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '还款金额，单位分',
  `repay_finished` int(11) NOT NULL DEFAULT '0' COMMENT '是否还款完成：0-未完成，1-已完成',
  `repay_finished_at` datetime NOT NULL DEFAULT '1000-01-01 00:00:00' COMMENT '还款完成时间',
  `cause` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '借款事由',
  `memo` text COLLATE utf8mb4_unicode_ci COMMENT '借款备注',
  `receive_card_bank` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '收款银行卡号（加密）',
  `receive_card_num` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '收款银行卡开户行（加密）',
  `file_storage_id` int(11) NOT NULL DEFAULT '0' COMMENT '附件',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `idx_entry_id` (`entry_id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='财务暂支单, ap全称advance_payment';
EOF;
        $finance_ap_repayment = <<<EOF
CREATE TABLE `finance_ap_repayment` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `finance_ap_id` int(10) unsigned NOT NULL COMMENT '财务暂支单ID',
  `repay_amount` bigint(20) unsigned NOT NULL COMMENT '还款金额，单位为分',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `idx_finance_ap_id` (`finance_ap_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='财务暂支单还款记录';
EOF;
        $finance_invoice_sign = <<<EOF
CREATE TABLE `finance_invoice_sign` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `applicant_id` int(11) NOT NULL COMMENT '申请人id',
  `finance_invoice_sign_entry_id` int(11) NOT NULL COMMENT '发票签收流程id',
  `finance_payment_entry_id` int(11) NOT NULL COMMENT '付款流程id',
  `supplier` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '供应商名称',
  `invoice_amount_receivable` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '应收发票金额',
  `invoice_num` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '发票号',
  `invoice_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '发票类型',
  `invoice_amount` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '发票金额,（本次开出发票金额）',
  `without_tax_amount` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '不含税金额',
  `tax_amount` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '税额',
  `invoice_sheet` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '发票张数',
  `remain_invoice_amount` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '剩余发票金额',
  `remark` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '备注',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='发票签收流程数据落地表';
EOF;
        $finance_ter = <<<EOF
CREATE TABLE `finance_ter` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `entry_id` int(10) unsigned NOT NULL COMMENT '流程ID',
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '标题',
  `user_id` int(10) unsigned NOT NULL COMMENT '申请人ID',
  `user_name` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '申请人姓名',
  `company_name` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '所属公司',
  `department` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '所属部门',
  `travel_application_id` int(11) NOT NULL COMMENT '出差申请单ID',
  `total_amount` bigint(20) unsigned NOT NULL COMMENT '总报销金额，单位分',
  `actual_amount` bigint(20) unsigned NOT NULL COMMENT '实际报销金额，单位分',
  `payment_method` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '付款方式',
  `memo` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '备注',
  `receive_card_num` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '收款银行卡（已加密）',
  `receive_card_bank` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '收款银行卡卡行（已加密）',
  `file_storage_id` int(11) NOT NULL DEFAULT '0' COMMENT '附件ID：无附件则为0',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `idx_entry_id` (`entry_id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='财务差旅报销单，ter全称travel expense reimbursement';
EOF;
        $finance_ter_item = <<<EOF
CREATE TABLE `finance_ter_item` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `finance_ter_id` int(11) NOT NULL COMMENT '差旅报销单ID',
  `description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '项目事由描述',
  `travel_from` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '出发地',
  `travel_to` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '目的地',
  `telecom_amount` bigint(20) unsigned NOT NULL COMMENT '交通费用，单位为分',
  `hotel_amount` bigint(20) unsigned NOT NULL COMMENT '住宿费用，单位为分',
  `allowance_amount` bigint(20) unsigned NOT NULL COMMENT '出差补贴，单位为分',
  `other_amount` bigint(20) unsigned NOT NULL COMMENT '其他杂项，单位为分',
  `invoice_quantity` int(10) unsigned NOT NULL COMMENT '单据发票张数',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `idx_finance_ter_id` (`finance_ter_id`),
  KEY `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='差旅报销项';
EOF;
        $finance_workflow_payment = <<<EOF
CREATE TABLE `finance_workflow_payment` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `entry_id` int(11) NOT NULL COMMENT '付款申请流程id',
  `title` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '流程标题',
  `beneficiary` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '供应商名称',
  `payment_amount_transfer` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '付款金额（总的应收发票金额（元））',
  `paid_payment_amount_transfer` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '已开发票金额',
  `company` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '所属公司',
  `primary_dept` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '所属部门',
  `applicant_chinese_name` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '申请人',
  `contract_no` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '合同编号',
  `our_main_body` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '我方主体',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `applicant_id` int(11) NOT NULL COMMENT '申请人id',
  `invoice_description` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '发票说明',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='付款申请数据（发票流程使用）落地表';
EOF;
        $jobs = <<<EOF
CREATE TABLE `jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint(3) unsigned NOT NULL,
  `reserved_at` int(10) unsigned DEFAULT NULL,
  `available_at` int(10) unsigned NOT NULL,
  `created_at` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
EOF;
        $message_log = <<<EOF
CREATE TABLE `message_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `template_key` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '模板键值',
  `push_type` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '推送类型',
  `sent_content_md5` char(32) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT '消息内容摘要',
  `sent_to` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '发送用户',
  `sent_cc` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '抄送用户',
  `sent_status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '状态：0（未发送），1（发送成功），-1（发送失败）',
  `sent_at` datetime NOT NULL DEFAULT '1000-01-01 00:00:00' COMMENT '发送时间',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `idx_template_key` (`template_key`),
  KEY `idx_sent_at` (`sent_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='消息日志';
EOF;
        $message_template = <<<EOF
CREATE TABLE `message_template` (
  `template_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键',
  `template_key` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '模板键值',
  `template_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '名称',
  `template_type` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '类型',
  `template_sign` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '签名',
  `template_push_type` enum('email','wechat','system','sms') COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '推送方式：email-邮件，wechat-企业微信，system-系统，sms-短信',
  `template_title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '模板标题',
  `template_content` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '模板内容',
  `template_status` enum('active','inactive') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'inactive' COMMENT '模板状态：active－可用，inactive－不可用',
  `template_memo` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '备注',
  `template_created_user` int(11) NOT NULL COMMENT '创建用户',
  `template_updated_user` int(11) NOT NULL COMMENT '更新用户',
  `template_deleted` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否删除：0-未删除，1-已删除',
  `template_deleted_user` int(11) NOT NULL DEFAULT '0' COMMENT '删除用户',
  `template_deleted_at` datetime NOT NULL DEFAULT '1000-01-01 00:00:00' COMMENT '删除时间',
  `template_created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `template_updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`template_id`),
  KEY `idx_template_key` (`template_key`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='消息模板';
EOF;
        $operate_log = <<<EOF
CREATE TABLE `operate_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `operate_user_id` int(11) DEFAULT NULL COMMENT '操作员工ID',
  `action` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '动作',
  `type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '类型',
  `object_id` int(11) DEFAULT NULL COMMENT '对象ID',
  `object_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '对象名称',
  `content` text COLLATE utf8mb4_unicode_ci COMMENT '内容',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
EOF;
        $password_resets = <<<EOF
CREATE TABLE `password_resets` (
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  KEY `password_resets_email_index` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
EOF;
        $pending_users = <<<EOF
CREATE TABLE `pending_users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '内部系统uid',
  `given_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '中文-名',
  `family_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '中文-姓',
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '邮箱',
  `mobile` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '手机号（加密）',
  `position` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '职位',
  `gender` tinyint(1) NOT NULL DEFAULT '0' COMMENT '性别(1.男;2.女;0.未知)',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `join_at` datetime DEFAULT NULL COMMENT '入职时间',
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `deleted_at` timestamp NULL DEFAULT NULL,
  `company_id` int(11) NOT NULL DEFAULT '1' COMMENT '所属公司id',
  `department_id` int(11) NOT NULL DEFAULT '1' COMMENT '所属部门id',
  `english_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '英文名',
  `is_leader` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否是高管',
  `is_sync_wechat` tinyint(4) NOT NULL DEFAULT '1' COMMENT '是否要同步企业微信',
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '企业微信账号名 例如allenwang',
  `work_address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '工作地点',
  PRIMARY KEY (`id`),
  UNIQUE KEY `pending_users_name_unique` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
EOF;
        $permissions = <<<EOF
CREATE TABLE `permissions` (
  `ability_id` int(10) unsigned NOT NULL,
  `entity_id` int(10) unsigned NOT NULL,
  `entity_type` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `forbidden` tinyint(1) NOT NULL DEFAULT '0',
  `scope` int(11) DEFAULT NULL,
  KEY `permissions_entity_index` (`entity_id`,`entity_type`,`scope`),
  KEY `permissions_ability_id_index` (`ability_id`),
  KEY `permissions_scope_index` (`scope`),
  CONSTRAINT `permissions_ability_id_foreign` FOREIGN KEY (`ability_id`) REFERENCES `abilities` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
EOF;
        $profiles = <<<EOF
CREATE TABLE `profiles` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
EOF;
        $roles = <<<EOF
CREATE TABLE `roles` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `level` int(10) unsigned DEFAULT NULL,
  `scope` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_unique` (`name`,`scope`),
  KEY `roles_scope_index` (`scope`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
EOF;
        $seal_change_logs = <<<EOF
CREATE TABLE `seal_change_logs` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `change_seal_id` int(11) NOT NULL COMMENT '印章id',
  `change_entry_id` varchar(32) NOT NULL DEFAULT '' COMMENT '流程编号',
  `change_lend_user_id` int(11) NOT NULL COMMENT '出借人',
  `change_receive_user_id` int(11) NOT NULL COMMENT '接收人',
  `change_status` int(11) NOT NULL COMMENT '0-流转中，1-持有中',
  `created_at` datetime NOT NULL DEFAULT '1000-01-01 00:00:00' COMMENT '创建时间',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='印章流转记录表';
EOF;
        $seals = <<<EOF
CREATE TABLE `seals` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `company_id` varchar(32) DEFAULT NULL COMMENT '印章主体(公司id)',
  `seal_type` varchar(32) NOT NULL DEFAULT '',
  `seal_hold_user_id` varchar(32) NOT NULL DEFAULT '0' COMMENT '当前责任人',
  `created_at` datetime NOT NULL DEFAULT '1000-01-01 00:00:00' COMMENT '创建时间',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='印章表';
EOF;
        $sessions = <<<EOF
CREATE TABLE `sessions` (
  `id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` int(10) unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int(11) NOT NULL,
  UNIQUE KEY `sessions_id_unique` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
EOF;
        $tag_user = <<<EOF
CREATE TABLE `tag_user` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `tag_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `user_name` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
EOF;
        $tags = <<<EOF
CREATE TABLE `tags` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tags_name_unique` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
EOF;
        $user_bank_card = <<<EOF
CREATE TABLE `user_bank_card` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL DEFAULT '0' COMMENT '银行卡持有人',
  `card_num` text NOT NULL COMMENT '银行卡号（加密）',
  `bank` text NOT NULL COMMENT '开户行（加密）',
  `branch_bank` text NOT NULL COMMENT '支行名称（加密',
  `bank_province` text NOT NULL COMMENT '银行卡属地（省）（加密）',
  `bank_city` text NOT NULL COMMENT '银行卡属地（市）（加密）',
  `bank_type` int(11) DEFAULT NULL COMMENT '银行卡类型 1:主卡 2副卡',
  `created_at` datetime NOT NULL DEFAULT '1000-01-01 00:00:00' COMMENT '创建时间',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  `deleted_at` varchar(32) DEFAULT NULL,
  `bank_abbr` varchar(191) NOT NULL COMMENT '银行名称简写',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COMMENT='员工银行卡信息表';
EOF;
        $user_family = <<<EOF
CREATE TABLE `user_family` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `family_relate` text NOT NULL COMMENT '和家人的关系（加密',
  `family_name` text NOT NULL COMMENT '家人姓名（加密）',
  `family_sex` text NOT NULL COMMENT '家人性别 1:男 2：女（加密）',
  `created_at` datetime NOT NULL DEFAULT '1000-01-01 00:00:00' COMMENT '创建时间',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  `deleted_at` varchar(32) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='员工家庭信息';
EOF;
        $user_log = <<<EOF
CREATE TABLE `user_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `target_user_id` int(11) DEFAULT NULL COMMENT '目标人',
  `operate_user_id` int(11) DEFAULT NULL COMMENT '操作人',
  `action` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '动作',
  `init_data` text COLLATE utf8mb4_unicode_ci COMMENT '原数据',
  `target_data` text COLLATE utf8mb4_unicode_ci COMMENT '目标数据',
  `extra` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '扩展信息（JSON 数据）',
  `note` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '备注信息',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `type` int(11) DEFAULT NULL COMMENT '信息类型',
  `init_json_data` text COLLATE utf8mb4_unicode_ci COMMENT '原始json数据',
  `target_json_data` text COLLATE utf8mb4_unicode_ci COMMENT '变化后json数据',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
EOF;
        $user_urgent_contacts = <<<EOF
CREATE TABLE `user_urgent_contacts` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `relate` text NOT NULL COMMENT '和联系人的关系（加密）',
  `relate_name` text NOT NULL COMMENT '联系人姓名（加密）',
  `relate_phone` text NOT NULL COMMENT '联系人电话（加密）',
  `created_at` datetime NOT NULL DEFAULT '1000-01-01 00:00:00' COMMENT '创建时间',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  `deleted_at` varchar(32) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='员工紧急联系人';
EOF;
        $users = <<<EOF
CREATE TABLE `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '内部系统uid',
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '系统唯一账号名',
  `employee_num` int(11) NOT NULL COMMENT '员工编号KNxxxxxx',
  `chinese_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '中文名',
  `english_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '英文名',
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '邮箱',
  `company_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '公司id',
  `mobile` bigint(11) NOT NULL COMMENT '手机号',
  `position` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '职位',
  `avatar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '头像',
  `gender` tinyint(1) NOT NULL DEFAULT '0' COMMENT '性别(1.男;2.女;0.未知)',
  `isleader` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否高管',
  `telephone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '固定电话',
  `password` text COLLATE utf8mb4_unicode_ci COMMENT '密码（加密）',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `join_at` datetime DEFAULT NULL COMMENT '入职时间',
  `regular_at` datetime DEFAULT NULL COMMENT '转正时间',
  `leave_at` datetime DEFAULT NULL COMMENT '离职时间',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '员工状态',
  `deleted_at` timestamp NULL DEFAULT NULL,
  `remember_token` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '唯一token',
  `is_sync_wechat` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否要同步企业微信 0：不同步， 1：同步',
  `work_address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '工作地点',
  `superior_leaders` int(11) NOT NULL COMMENT '上级领导',
  `work_type` tinyint(4) NOT NULL COMMENT '班值类型(1.客服类;2.职能类;3.弹性类)',
  `work_title` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '班值代码(P01、P02等)',
  `password_modified_at` datetime DEFAULT NULL COMMENT '密码修改日期',
  `password_tips` text COLLATE utf8mb4_unicode_ci COMMENT '密码提示',
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_employee_num_unique` (`employee_num`),
  UNIQUE KEY `users_name_unique` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
EOF;
        $users_detail_info = <<<EOF
CREATE TABLE `users_detail_info` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL COMMENT '员工ID',
  `office_location` text COLLATE utf8mb4_unicode_ci COMMENT '办公地点（加密）',
  `note` text COLLATE utf8mb4_unicode_ci COMMENT '备注（加密）',
  `probation` text COLLATE utf8mb4_unicode_ci COMMENT '试用期（加密）',
  `after_probation` text COLLATE utf8mb4_unicode_ci COMMENT '转正日期（加密）',
  `grade` text COLLATE utf8mb4_unicode_ci COMMENT '岗位职级（加密）',
  `id_name` text COLLATE utf8mb4_unicode_ci COMMENT '身份证姓名（加密）',
  `id_number` text COLLATE utf8mb4_unicode_ci COMMENT '证件号码（加密）',
  `born_time` text COLLATE utf8mb4_unicode_ci COMMENT '出生日期（加密）',
  `ethnic` text COLLATE utf8mb4_unicode_ci COMMENT '民族（加密）',
  `id_address` text COLLATE utf8mb4_unicode_ci COMMENT '身份证地址（加密）',
  `validity_certificate` text COLLATE utf8mb4_unicode_ci COMMENT '证件有效期（加密）',
  `address` text COLLATE utf8mb4_unicode_ci COMMENT '住址（加密）',
  `first_work_time` text COLLATE utf8mb4_unicode_ci COMMENT '首次参见工作时间（加密）',
  `per_social_account` text COLLATE utf8mb4_unicode_ci COMMENT '个人社保账号（加密）',
  `per_fund_account` text COLLATE utf8mb4_unicode_ci COMMENT '个人公积金账号（加密）',
  `highest_education` text COLLATE utf8mb4_unicode_ci COMMENT '最高学历（加密）',
  `graduate_institutions` text COLLATE utf8mb4_unicode_ci COMMENT '毕业院校（加密）',
  `graduate_time` text COLLATE utf8mb4_unicode_ci COMMENT '毕业时间（加密）',
  `major` text COLLATE utf8mb4_unicode_ci COMMENT '所学专业（加密）',
  `bank_card` text COLLATE utf8mb4_unicode_ci COMMENT '银行卡号（加密）',
  `bank` text COLLATE utf8mb4_unicode_ci COMMENT '开户行（加密）',
  `contract_company` text COLLATE utf8mb4_unicode_ci COMMENT '合同公司（加密）',
  `first_contract_start_time` text COLLATE utf8mb4_unicode_ci COMMENT '首次合同起始日（加密）',
  `first_contract_end_time` text COLLATE utf8mb4_unicode_ci COMMENT '首次合同到期日（加密）',
  `cur_contract_start_time` text COLLATE utf8mb4_unicode_ci COMMENT '现合同起始日（加密）',
  `cur_contract_end_time` text COLLATE utf8mb4_unicode_ci COMMENT '现合同到期日（加密）',
  `contract_term` text COLLATE utf8mb4_unicode_ci COMMENT '合同期限（加密）',
  `renew_times` text COLLATE utf8mb4_unicode_ci COMMENT '续签次数（加密）',
  `emergency_contact` text COLLATE utf8mb4_unicode_ci COMMENT '紧急联系人姓名（加密）',
  `contact_relationship` text COLLATE utf8mb4_unicode_ci COMMENT '联系人关系（加密）',
  `contact_mobile` text COLLATE utf8mb4_unicode_ci COMMENT '联系人电话（加密）',
  `has_children` text COLLATE utf8mb4_unicode_ci COMMENT '有无子女（加密）',
  `child_name` text COLLATE utf8mb4_unicode_ci COMMENT '子女姓名（加密）',
  `child_gender` text COLLATE utf8mb4_unicode_ci COMMENT '子女性别(1.男;2.女;0.未知)（加密）',
  `child_born_time` text COLLATE utf8mb4_unicode_ci COMMENT '子女出生日期（加密）',
  `pic_id_pos` mediumblob,
  `pic_id_neg` mediumblob,
  `pic_edu_background` mediumblob,
  `pic_degree` mediumblob,
  `pic_pre_company` mediumblob,
  `pic_user` mediumblob,
  `user_type` text COLLATE utf8mb4_unicode_ci COMMENT '员工类型（加密）',
  `user_status` text COLLATE utf8mb4_unicode_ci COMMENT '员工状态（加密）',
  `census_type` text COLLATE utf8mb4_unicode_ci COMMENT '户籍类型（加密）',
  `politics_status` text COLLATE utf8mb4_unicode_ci COMMENT '政治面貌（加密）',
  `marital_status` text COLLATE utf8mb4_unicode_ci COMMENT '婚姻状况（加密）',
  `contract_type` text COLLATE utf8mb4_unicode_ci COMMENT '合同类型（加密）',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `branch_bank` text COLLATE utf8mb4_unicode_ci COMMENT '支行名称（加密）',
  `bank_province` text COLLATE utf8mb4_unicode_ci COMMENT '银行卡属地：省（加密）',
  `bank_city` text COLLATE utf8mb4_unicode_ci COMMENT '银行卡属地：市（加密）',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
EOF;
        $users_dimission = <<<EOF
CREATE TABLE `users_dimission` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '内部系统uid',
  `user_id` int(11) NOT NULL COMMENT '员工ID',
  `is_voluntary` tinyint(4) NOT NULL DEFAULT '1' COMMENT '是否主动离职(1:是;2:否)',
  `is_sign` tinyint(4) DEFAULT '1' COMMENT '直属领导是否已签字(1:已签;2:未签)',
  `is_complete` tinyint(4) DEFAULT '1' COMMENT '流程手续是否已走完(1:是;2:否)',
  `reason` text COLLATE utf8mb4_unicode_ci COMMENT '原因',
  `note` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '备注信息',
  `interview_result` text COLLATE utf8mb4_unicode_ci COMMENT '面谈结论',
  `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '状态1.有效；2.删除',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='员工离职表';
EOF;
        $users_turnover_stats = <<<EOF
CREATE TABLE `users_turnover_stats` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `stats_date` date DEFAULT NULL COMMENT '统计日',
  `begin_date` date DEFAULT NULL COMMENT '期初日',
  `end_date` date DEFAULT NULL COMMENT '期末日',
  `week` int(11) DEFAULT NULL COMMENT '当月第几周',
  `department_structure` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '部门体系',
  `first_level` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '一级部门',
  `begin_total` int(11) DEFAULT '0' COMMENT '期初人数',
  `end_total` int(11) DEFAULT '0' COMMENT '期末人数',
  `ignore_total` int(11) DEFAULT '0' COMMENT '未统计人数(包括兼职、顾问、高管、临时)',
  `sh_join` int(11) DEFAULT '0' COMMENT '上海入职人数',
  `sh_leave` int(11) DEFAULT '0' COMMENT '上海离职人数',
  `cd_join` int(11) DEFAULT '0' COMMENT '成都入职人数',
  `cd_leave` int(11) DEFAULT '0' COMMENT '成都离职人数',
  `sz_join` int(11) DEFAULT '0' COMMENT '深圳入职人数',
  `sz_leave` int(11) DEFAULT '0' COMMENT '深圳离职人数',
  `bj_join` int(11) DEFAULT '0' COMMENT '北京入职人数',
  `bj_leave` int(11) DEFAULT '0' COMMENT '北京离职人数',
  `px_join` int(11) DEFAULT '0' COMMENT '萍乡入职人数',
  `px_leave` int(11) DEFAULT '0' COMMENT '萍乡离职人数',
  `part_time_worker` int(11) DEFAULT '0' COMMENT '兼职人数',
  `adviser` int(11) DEFAULT '0' COMMENT '顾问人数',
  `leader` int(11) DEFAULT '0' COMMENT '高管人数',
  `temporary` int(11) DEFAULT '0' COMMENT '临时人数',
  `passive_leave` int(11) DEFAULT '0' COMMENT '被动离职人数',
  `voluntary_leave` int(11) DEFAULT '0' COMMENT '自愿离职人数',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='人员流动统计表';
EOF;
        $workflow_authorize_agent = <<<EOF
CREATE TABLE `workflow_authorize_agent` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `authorizer_user_id` int(11) NOT NULL COMMENT '授权人id',
  `authorizer_user_name` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '授权人姓名',
  `authorize_valid_begin` datetime DEFAULT NULL COMMENT '代理权限开始时间',
  `authorize_valid_end` datetime DEFAULT NULL COMMENT '代理权限结束时间',
  `flow_no` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0' COMMENT '代理审批的流程',
  `agent_user_id` int(11) NOT NULL COMMENT '代理人id',
  `agent_user_name` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '代理人姓名',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
EOF;
        $workflow_entries = <<<EOF
CREATE TABLE `workflow_entries` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '标题',
  `user_id` int(11) NOT NULL,
  `flow_id` int(11) NOT NULL,
  `process_id` int(11) NOT NULL,
  `circle` int(11) NOT NULL,
  `status` int(11) NOT NULL COMMENT '当前状态 0处理中 9通过 -1驳回 -2撤销 -9草稿\\n1：流程中\\n9：处理完成',
  `pid` int(11) NOT NULL,
  `enter_process_id` int(11) NOT NULL,
  `enter_proc_id` int(11) NOT NULL,
  `child` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `origin_auth_id` int(11) NOT NULL DEFAULT '0' COMMENT '申请单真实登录人id',
  `origin_auth_name` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '申请单真实登录人姓名',
  `finish_at` timestamp NULL DEFAULT NULL COMMENT '流程审批完成时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
EOF;
        $workflow_entry_data = <<<EOF
CREATE TABLE `workflow_entry_data` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `entry_id` int(11) NOT NULL,
  `flow_id` int(11) NOT NULL,
  `field_name` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `field_value` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `field_remark` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
EOF;
        $workflow_flow_links = <<<EOF
CREATE TABLE `workflow_flow_links` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `flow_id` int(11) NOT NULL,
  `type` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL,
  `process_id` int(11) NOT NULL,
  `next_process_id` int(11) NOT NULL,
  `auditor` varchar(512) COLLATE utf8mb4_unicode_ci NOT NULL,
  `depands` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '依赖',
  `expression` varchar(2048) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '转出条件对应的表达式',
  `sort` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
EOF;
        $workflow_flow_types = <<<EOF
CREATE TABLE `workflow_flow_types` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type_name` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
EOF;
        $workflow_flows = <<<EOF
CREATE TABLE `workflow_flows` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `flow_no` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL,
  `flow_name` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL,
  `template_id` int(11) NOT NULL,
  `type_id` int(11) NOT NULL,
  `flowchart` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `jsplumb` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_publish` tinyint(1) NOT NULL,
  `is_show` tinyint(1) NOT NULL,
  `introduction` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '流程说明',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `leader_link_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '领导人审批条线,business:业务条线;report:汇报关系条线',
  `version` int(11) NOT NULL DEFAULT '0' COMMENT '版本号，以时间戳做版本',
  `is_abandon` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否废弃，1：废弃；0：未废弃',
  `can_view_users` text COLLATE utf8mb4_unicode_ci COMMENT '能看到本流程的用户id集合',
  `can_view_departments` text COLLATE utf8mb4_unicode_ci COMMENT '能看到本流程的部门id集合',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
EOF;
        $workflow_messages = <<<EOF
CREATE TABLE `workflow_messages` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(128) DEFAULT NULL COMMENT '标题',
  `content` text COMMENT '内容',
  `type` enum('mail','wechat','system') NOT NULL DEFAULT 'mail' COMMENT '消息类型',
  `sender` varchar(128) DEFAULT NULL COMMENT '发送方',
  `receiver` varchar(128) DEFAULT NULL COMMENT '接收方',
  `carbon_copy` text COMMENT '抄送方',
  `status` enum('unfinished','finished') NOT NULL DEFAULT 'unfinished' COMMENT '消息状态',
  `created_at` datetime NOT NULL DEFAULT '1000-01-01 00:00:00' COMMENT '创建时间',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
EOF;
        $workflow_process_var = <<<EOF
CREATE TABLE `workflow_process_var` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `process_id` int(11) NOT NULL,
  `flow_id` int(11) NOT NULL,
  `expression_field` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
EOF;
        $workflow_processes = <<<EOF
CREATE TABLE `workflow_processes` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `flow_id` int(11) NOT NULL,
  `process_name` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `limit_time` int(11) NOT NULL DEFAULT '0' COMMENT '限定时间,单位秒',
  `type` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'operation' COMMENT '流程图显示操作框类型',
  `icon` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '流程图显示图标',
  `process_to` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `style` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `style_color` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '#78a300',
  `style_height` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '30',
  `style_width` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '30',
  `position_left` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '100px',
  `position_top` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '200px',
  `position` int(11) NOT NULL DEFAULT '1' COMMENT '步骤位置',
  `child_flow_id` int(11) NOT NULL DEFAULT '0' COMMENT '子流程id',
  `child_after` int(11) NOT NULL DEFAULT '2' COMMENT '子流程结束后 1.同时结束父流程 2.返回父流程',
  `child_back_process` int(11) NOT NULL DEFAULT '0' COMMENT '子流程结束后返回父流程进程',
  `description` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '步骤描述',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `can_merge` tinyint(4) NOT NULL DEFAULT '1' COMMENT '是否可以做节点合并，1:可以;0:不可以',
  `pass_events` varchar(256) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '节点审批通过后触发的事件，以逗号分割',
  `reject_events` varchar(256) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '节点审批拒绝后触发的事件，以逗号分割',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
EOF;
        $workflow_procs = <<<EOF
CREATE TABLE `workflow_procs` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `entry_id` int(11) NOT NULL,
  `flow_id` int(11) NOT NULL COMMENT '流程id',
  `process_id` int(11) NOT NULL,
  `process_name` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` int(11) NOT NULL,
  `user_name` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '审核人名称',
  `dept_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '审核人部门名称',
  `auditor_id` int(11) NOT NULL DEFAULT '0' COMMENT '具体操作人',
  `auditor_name` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '操作人名称',
  `auditor_dept` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '操作人部门',
  `status` int(11) NOT NULL COMMENT '当前处理状态 0待处理 9通过 -1驳回\\n0：处理中\\n-1：驳回\\n9：会签',
  `content` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '批复内容',
  `is_read` tinyint(1) NOT NULL COMMENT '是否查看',
  `is_real` tinyint(1) NOT NULL COMMENT '审核人和操作人是否同一人',
  `circle` int(11) NOT NULL DEFAULT '1',
  `beizhu` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `concurrence` int(11) NOT NULL DEFAULT '0' COMMENT '并行查找解决字段， 部门 角色 指定 分组用',
  `note` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `authorizer_ids` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '授权人id，可能是多个,逗号分隔',
  `authorizer_names` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '授权人姓名，可能是多个,逗号分隔',
  `origin_auth_id` int(11) NOT NULL DEFAULT '0' COMMENT '审批的真实登录用户',
  `origin_auth_name` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '审批的真实登录用户姓名',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
EOF;
        $workflow_role = <<<EOF
CREATE TABLE `workflow_role` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `role_name` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '角色中文名',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` datetime DEFAULT NULL,
  `company_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0' COMMENT '负责的公司,以逗号间隔',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
EOF;
        $workflow_role_user = <<<EOF
CREATE TABLE `workflow_role_user` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `creater_user_id` int(11) NOT NULL DEFAULT '0' COMMENT '创建人id',
  `creater_user_chinese_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '创建人用户名',
  `role_id` int(10) unsigned NOT NULL COMMENT '角色id',
  `user_id` int(10) unsigned NOT NULL COMMENT '用户id',
  `user_chinese_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '用户中文名',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
EOF;
        $workflow_task = <<<EOF
CREATE TABLE `workflow_task` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `task_key` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '任务唯一编码',
  `request` text COLLATE utf8mb4_unicode_ci COMMENT '任务数据，Json格式',
  `response` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '任务执行结果',
  `entry_id` int(11) unsigned NOT NULL COMMENT '工作流单号',
  `proc_id` int(11) unsigned NOT NULL COMMENT '审批流程节点id',
  `task_status` int(11) NOT NULL COMMENT '当前状态,0:处理中;1:处理成功;2:处理失败;3:新建',
  `exec_times` int(11) NOT NULL DEFAULT '0' COMMENT '任务执行的次数',
  `queue_name` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '推送的队列名',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `call_back_res` varchar(1000) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '回调数据记录',
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_idx_task_key` (`task_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
EOF;
        $workflow_template_forms = <<<EOF
CREATE TABLE `workflow_template_forms` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `template_id` int(11) NOT NULL,
  `field` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `field_name` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `placeholder` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'placeholder',
  `field_type` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `field_value` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `field_default_value` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `field_extra_css` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '额外的css样式类',
  `unit` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '单位',
  `rules` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `sort` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `location` int(11) NOT NULL DEFAULT '0' COMMENT '栅格化',
  `required` int(11) NOT NULL DEFAULT '0' COMMENT '必填项',
  `show_in_todo` tinyint(4) NOT NULL COMMENT '是否在待办事项',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
EOF;
        $workflow_templates = <<<EOF
CREATE TABLE `workflow_templates` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `template_name` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `workflow_templates_template_name_unique` (`template_name`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
EOF;
        return [
            $users,
            $user_bank_card,
            $user_family,
            $user_log,
            $user_urgent_contacts,
            $users_detail_info,
            $users_dimission,
            $users_turnover_stats,
            $abilities,
            $roles,
            $admins,
            $assigned_roles,
            $attendance_checkinout,
            $attendance_holidays,
            $attendance_sheets,
            $attendance_user_info,
            $attendance_vacation_changes,
            $attendance_vacation_conversions,
            $attendance_vacations,
            $attendance_white,
            $attendance_work_classes,
            $attendance_work_user_logs,
            $attendance_workflow_leaves,
            $attendance_workflow_overtimes,
            $attendance_workflow_resumptions,
            $attendance_workflow_retroactives,
            $attendance_workflow_travels,
            $companies,
            $company_equity_pledge,
            $company_main_personnels,
            $company_shareholders,
            $department_map_centres,
            $department_tag,
            $department_user,
            $departments,
            $failed_jobs,
            $file_storage,
            $finance_ap,
            $finance_ap_repayment,
            $finance_invoice_sign,
            $finance_ter,
            $finance_ter_item,
            $finance_workflow_payment,
            $jobs,
            $message_log,
            $message_template,
            $operate_log,
            $password_resets,
            $pending_users,
            $permissions,
            $profiles,
            $seal_change_logs,
            $seals,
            $sessions,
            $tag_user,
            $tags,
            $workflow_authorize_agent,
            $workflow_entries,
            $workflow_entry_data,
            $workflow_flow_links,
            $workflow_flow_types,
            $workflow_flows,
            $workflow_messages,
            $workflow_process_var,
            $workflow_processes,
            $workflow_procs,
            $workflow_role,
            $workflow_role_user,
            $workflow_task,
            $workflow_template_forms,
            $workflow_templates
        ];
    }
}
