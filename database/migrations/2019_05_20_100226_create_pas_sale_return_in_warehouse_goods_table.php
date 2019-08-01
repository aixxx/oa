<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePasSaleReturnInWarehouseGoodsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = <<<EOF
CREATE TABLE `pas_sale_return_in_warehouse_goods` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `in_id` int(11) DEFAULT '0' COMMENT '入库订单id',
  `sale_order_goods_id` int(11) DEFAULT '0' COMMENT '销售单商品表主键id',
  `goods_id` int(11) DEFAULT '0' COMMENT '商品id',
  `sku_id` int(11) DEFAULT '0' COMMENT 'skuid',
  `in_num` int(11) DEFAULT '0' COMMENT '入库数量',
  `status` tinyint(1) DEFAULT '0' COMMENT '状态（备用）',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `gk` (`goods_id`,`sku_id`) USING BTREE,
  KEY `order_id` (`in_id`),
  KEY `goods_id` (`goods_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COMMENT='退货订单入库商品表';
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
        Schema::dropIfExists('pas_sale_return_in_warehouse_goods');
    }
}
