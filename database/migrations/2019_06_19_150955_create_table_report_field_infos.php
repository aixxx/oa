<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableReportFieldInfos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = <<<EOF
CREATE TABLE `report_field_infos` (
  `auto_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id` int(11) NOT NULL COMMENT '实际增量id',
  `template_id` int(11) NOT NULL COMMENT '模板id',
  `report_id` int(11) NOT NULL DEFAULT '0' COMMENT '汇报id',
  `field_id` int(11) NOT NULL DEFAULT '0' COMMENT '模板字段id',
  `field` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '字段名称',
  `field_name` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '字段展示名',
  `field_type_value` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '字段类型值',
  `field_value` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '字段值',
  `required` int(11) NOT NULL DEFAULT '0' COMMENT '必填项',
  `schedule_id` int(11) NOT NULL DEFAULT '0' COMMENT '关联日程id',
  PRIMARY KEY (`auto_id`) USING BTREE,
  KEY `template_id` (`template_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='汇报提交的自定义数据信息表';
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
        //
    }
}
