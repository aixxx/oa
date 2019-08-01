<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatPasSaleReturnInWarehouseTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = <<<EOF
CREATE TABLE `pas_sale_return_in_warehouse` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT '0' COMMENT '申请人',
  `in_sn` varchar(255) DEFAULT NULL COMMENT '出库单申请编号',
  `sale_order_id` int(11) DEFAULT '0' COMMENT '销售订单id',
  `sale_order_sn` varchar(50) DEFAULT NULL COMMENT '销售订单编号',
  `return_order_id` int(11) DEFAULT '0' COMMENT '退货单id',
  `return_order_sn` varchar(50) DEFAULT NULL COMMENT '退货单编号',
  `num` int(11) unsigned DEFAULT '0' COMMENT '销售商品总数量',
  `in_num` int(11) DEFAULT '0' COMMENT '入库数量',
  `in_time` datetime DEFAULT NULL COMMENT '入库时间',
  `shipping_id` int(11) DEFAULT '0' COMMENT '物流id',
  `status` tinyint(1) DEFAULT '0' COMMENT '申请单状态 状态 0草稿 1审核中 2已撤回 3已驳回 4审核完成',
  `entrise_id` int(11) DEFAULT '0' COMMENT '审核流id',
  `remark` varchar(255) DEFAULT NULL COMMENT '备注',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `order_id` (`sale_order_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='销售退货单入库申请表';
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
        Schema::table('warehouse', function (Blueprint $table) {
            //
        });
    }
}
