<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePasSaleReturnOrderGoodsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = <<<EOF
CREATE TABLE `pas_sale_return_order_goods` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `return_order_id` int(11) DEFAULT '0' COMMENT '退货订单id',
  `sale_order_id` int(11) DEFAULT NULL COMMENT '销售订单id',
  `sale_order_goods_id` int(11) DEFAULT '0' COMMENT '销售单商品主键id',
  `goods_id` int(11) DEFAULT '0' COMMENT '商品id',
  `sku_id` int(11) DEFAULT NULL COMMENT 'skuid',
  `goods_money` decimal(10,2) DEFAULT '0.00' COMMENT '商品总金额',
  `return_money` decimal(10,2) DEFAULT '0.00' COMMENT '退货金额',
  `num` int(11) unsigned DEFAULT '0' COMMENT '购买数量',
  `return_num` int(11) DEFAULT '0' COMMENT '退货数量',
  `status` tinyint(1) DEFAULT '0' COMMENT '退货商品状态',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `gk` (`goods_id`,`sku_id`) USING BTREE,
  KEY `order_id` (`sale_order_id`),
  KEY `goods_id` (`goods_id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COMMENT='销售退货商品表';
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
        Schema::dropIfExists('pas_sale_return_order_goods');
    }
}
