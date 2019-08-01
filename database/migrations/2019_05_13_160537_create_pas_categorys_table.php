<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePasCategorysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = <<<EOF
CREATE TABLE `pas_categorys` (
  `auto_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id` int(11) NOT NULL DEFAULT '0' COMMENT '分类Id',
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '分类名称',
  `parent_id` int(11) NOT NULL COMMENT '父级id',
  `sort` int(11) NOT NULL COMMENT '排序',
  `deepth` tinyint(1) DEFAULT NULL COMMENT '深度',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`auto_id`),
  UNIQUE KEY `id_index` (`id`) USING BTREE,
  UNIQUE KEY `name` (`name`) USING BTREE,
  KEY `auto_id_index` (`auto_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='商品分类表';
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
        Schema::dropIfExists('pas_categorys');
    }
}
