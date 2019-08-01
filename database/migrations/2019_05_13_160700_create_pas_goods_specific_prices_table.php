<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePasGoodsSpecificPricesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = <<<EOF
CREATE TABLE `pas_goods_specific_prices` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `goods_id` int(11) DEFAULT '0' COMMENT '商品id',
  `key` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL COMMENT '规格键名',
  `key_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL COMMENT '规格键名中文',
  `cost_price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '成本价格',
  `price` decimal(10,2) DEFAULT NULL COMMENT '零售价',
  `wholesale_price` decimal(10,2) DEFAULT NULL COMMENT '批发价',
  `store_count` int(11) unsigned DEFAULT '0' COMMENT '库存数量',
  `freeze_store_count` int(11) DEFAULT '0' COMMENT '冻结库存',
  `bar_code` varchar(32) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT '' COMMENT '商品条形码',
  `sku` varchar(128) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT '' COMMENT 'SKU',
  `sku_name` varchar(255) DEFAULT NULL COMMENT 'sku健名中文',
  `store_upper_limit` int(11) DEFAULT NULL COMMENT '库存上限',
  `store_lower_limit` int(11) DEFAULT NULL COMMENT '库存下限',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `gk` (`goods_id`,`key`) USING BTREE,
  KEY `goods_id` (`goods_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='商品的规格组合价格表';
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
        Schema::dropIfExists('pas_goods_specific_prices');
    }
}
