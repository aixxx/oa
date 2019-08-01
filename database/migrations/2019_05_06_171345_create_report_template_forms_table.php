<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReportTemplateFormsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = <<<EOF
CREATE TABLE `report_template_forms` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `template_id` int(11) NOT NULL COMMENT '模板id',
  `field` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '字段名称',
  `field_name` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '字段展示名',
  `placeholder` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '字段提示语',
  `field_type` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '字段类型',
  `field_value` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '字段值',
  `field_default_value` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '字段默认值',
  `field_extra_css` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '额外的css样式类',
  `unit` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '单位',
  `rules` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `sort` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `location` int(11) NOT NULL DEFAULT '0' COMMENT '栅格化',
  `required` int(11) NOT NULL DEFAULT '0' COMMENT '必填项',
  `show_in_todo` tinyint(4) NOT NULL COMMENT '是否在待办事项',
  PRIMARY KEY (`id`),
  KEY `template_id` (`template_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='汇报模板字段设置表';
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
        Schema::dropIfExists('report_template_forms');
    }
}
