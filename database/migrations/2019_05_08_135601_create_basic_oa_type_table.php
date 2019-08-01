<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBasicOaTypeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
         $sql = <<<EOF
CREATE TABLE `basic_oa_type` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '自动编号',
  `title` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '类型名称',
  `code` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '类型编号',
  `status` tinyint(4) NOT NULL COMMENT '状态，1：启用，2：停用',
  `describe` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '描述',
  `created_at` timestamp NULL DEFAULT NULL COMMENT '创建时间',
  `updated_at` timestamp NULL DEFAULT NULL COMMENT '更新时间',
  `deleted_at` timestamp NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `basic_oa_type_title_unique` (`title`),
  UNIQUE KEY `basic_oa_type_code_unique` (`code`),
  KEY `basic_oa_type_deleted_at_index` (`deleted_at`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
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
        Schema::dropIfExists('basic_oa_type');
    }
}
