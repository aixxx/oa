<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePasAttributesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = <<<EOF
CREATE TABLE `pas_attributes` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '属性id',
  `name` varchar(60) NOT NULL DEFAULT '' COMMENT '属性名称',
  `type` tinyint(4) DEFAULT '1' COMMENT '属性值类型 1单选 2多选',
  `values` text NOT NULL COMMENT '可选值列表',
  `sort` tinyint(3) unsigned NOT NULL DEFAULT '50' COMMENT '属性排序',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COMMENT='商品属性表';
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
        Schema::dropIfExists('pas_attributes');
    }
}
