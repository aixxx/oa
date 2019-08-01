<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableBussinessPlan extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = <<<EOF
CREATE TABLE `bussiness_plans` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '用户id',
  `department_id` int(11) NOT NULL DEFAULT '0' COMMENT '部门id',
  `company_id` int(11) NOT NULL DEFAULT '0' COMMENT '企业id',
  `title` varchar(255) NOT NULL COMMENT '计划名称',
  `month` varchar(50) CHARACTER SET utf8 DEFAULT NULL COMMENT '计划月份',
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  KEY `user_id` (`user_id`) USING BTREE,
  KEY `department_id` (`department_id`) USING BTREE
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
