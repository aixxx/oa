<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReportRulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = <<<EOF
CREATE TABLE `report_rules` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建者id',
  `company_id` int(11) NOT NULL DEFAULT '0' COMMENT '创建者所属公司id',
  `report_type` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '汇报类型，与汇报模板关联',
  `send_cycle` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '1每天2每周3每月',
  `send_day_date` varchar(50) DEFAULT '' COMMENT '日报周期值（星期一-星期天）',
  `stime` varchar(50) NOT NULL DEFAULT '0' COMMENT '开始时间',
  `etime` varchar(50) NOT NULL DEFAULT '0' COMMENT '截止时间',
  `is_legal_day_send` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '法定假日 0不提交1提交',
  `is_remind` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '0不提醒1提醒',
  `remind_time` tinyint(3) NOT NULL DEFAULT '1' COMMENT '提醒时间',
  `remind_content` varchar(255) DEFAULT NULL COMMENT '提醒内容',
  `select_user` text COMMENT '选择的员工',
  `select_department` text COMMENT '选择的部门',
  `deleted_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '创建时间',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '更新时间',
  `send_week_date` varchar(50) DEFAULT '' COMMENT '周报周期值（星期五-星期一）',
  `send_month_date` varchar(50) DEFAULT '' COMMENT '月报周期值（15号-5号）',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`) USING BTREE COMMENT '员工id',
  KEY `template_id` (`report_type`) USING BTREE COMMENT '汇报模板id',
  KEY `cycle` (`send_cycle`) USING BTREE COMMENT '汇报周期'
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COMMENT='汇报规则表';
EOF;
        DB::statement($sql);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('report_rules');
    }
}
