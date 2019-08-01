<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePasSaleOutWarehouseTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = <<<EOF
CREATE TABLE `pas_sale_out_warehouse` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT '0' COMMENT '申请人',
  `out_sn` varchar(255) DEFAULT NULL COMMENT '出库单申请编号',
  `order_id` int(11) DEFAULT '0' COMMENT '销售订单id',
  `order_sn` int(11) DEFAULT NULL COMMENT '销售订单编号',
  `num` int(11) unsigned DEFAULT '0' COMMENT '销售商品总数量',
  `out_num` int(11) DEFAULT '0' COMMENT '商品出库总数量',
  `out_time` datetime DEFAULT NULL COMMENT '出库时间',
  `shipping_id` int(11) DEFAULT '0' COMMENT '物流id',
  `status` tinyint(1) DEFAULT '0' COMMENT '申请单状态 0草稿 1申请',
  `remark` varchar(255) DEFAULT NULL COMMENT '备注',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COMMENT='销售订单商品出库申请表';
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
