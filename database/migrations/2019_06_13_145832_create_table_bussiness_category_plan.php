<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableBussinessCategoryPlan extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = <<<EOF
CREATE TABLE `bussiness_category_plans` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '用户id',
  `company_id` int(11) NOT NULL COMMENT '企业id',
  `department_id` int(11) NOT NULL COMMENT '部门id',
  `plan_id` int(11) NOT NULL COMMENT '计划id',
  `category_id` int(11) NOT NULL DEFAULT '0' COMMENT '类目id',
  `content` varchar(255) DEFAULT NULL COMMENT '计划内容',
  `money` decimal(12,2) DEFAULT '0.00' COMMENT '计划金额',
  `unit_type` varchar(50) CHARACTER SET utf8 DEFAULT NULL COMMENT '来往单位类型',
  `unit` int(11) DEFAULT NULL COMMENT '来往单位id',
  `program_id` int(11) DEFAULT '0' COMMENT '项目id',
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  KEY `user_id` (`user_id`) USING BTREE,
  KEY `department_id` (`department_id`) USING BTREE,
  KEY `category_id` (`category_id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;
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
