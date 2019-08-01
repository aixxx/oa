<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePasSaleOrderGoodsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = <<<EOF
CREATE TABLE `pas_sale_order_goods` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) DEFAULT NULL COMMENT '销售订单id',
  `goods_id` int(11) DEFAULT '0' COMMENT '商品id',
  `user_id` int(11) DEFAULT NULL COMMENT '客户id',
  `sku_id` int(11) DEFAULT NULL COMMENT 'skuid',
  `cost_price` decimal(10,2) DEFAULT '0.00' COMMENT '成本价格',
  `sale_price` decimal(10,2) DEFAULT '0.00' COMMENT '零售价',
  `wholesale_price` decimal(10,2) DEFAULT '0.00' COMMENT '批发价',
  `discount` float(5,2) DEFAULT '100.00' COMMENT '商品折扣',
  `price` decimal(10,2) DEFAULT '0.00' COMMENT '本单商品实际g购买价格',
  `num` int(11) unsigned DEFAULT '0' COMMENT '购买数量',
  `money` decimal(10,2) DEFAULT '0.00' COMMENT '商品总金额',
  `status` tinyint(1) DEFAULT '0' COMMENT '订单商品状态',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `gk` (`goods_id`,`sku_id`) USING BTREE,
  KEY `order_id` (`order_id`),
  KEY `goods_id` (`goods_id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 COMMENT='销售订单商品表';
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
        Schema::dropIfExists('pas_sale_order_goods');
    }
}
