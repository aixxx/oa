<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePasGoodsAttributesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = <<<EOF
CREATE TABLE `pas_goods_attributes` (
  `goods_attribute_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '商品属性id自增',
  `goods_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '商品id',
  `attr_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '属性id',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`goods_attribute_id`),
  KEY `goods_id` (`goods_id`) USING BTREE,
  KEY `attr_id` (`attr_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='商品属性关联表';
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
        Schema::dropIfExists('pas_goods_attributes');
    }
}
