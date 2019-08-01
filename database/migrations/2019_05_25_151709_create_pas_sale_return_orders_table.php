<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePasSaleReturnOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = <<<EOF
CREATE TABLE `pas_sale_return_orders` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '自动编号',
  `order_sn` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '销售单号',
  `sale_order_id` int(11) DEFAULT '0' COMMENT '销售单id',
  `user_id` int(11) unsigned DEFAULT '0' COMMENT '申请用户编号',
  `create_user_id` int(11) unsigned DEFAULT '0' COMMENT '制单人',
  `order_money` decimal(11,2) DEFAULT '0.00' COMMENT '销售单实际总金额',
  `total_money` decimal(11,2) DEFAULT '0.00' COMMENT '本单退款金额',
  `refunded_money` decimal(11,2) DEFAULT '0.00' COMMENT '应退金额',
  `real_refund_money` decimal(11,2) DEFAULT '0.00' COMMENT '实退金额',
  `other_money` decimal(11,2) DEFAULT '0.00' COMMENT '其它费用',
  `goods_num` int(11) DEFAULT '0' COMMENT '销售商品总数量',
  `return_num` int(11) DEFAULT '0' COMMENT '退货数量',
  `return_time` datetime DEFAULT NULL COMMENT '退货时间',
  `return_money_time` datetime DEFAULT '0000-00-00 00:00:00' COMMENT '预计退款时间',
  `business_time` datetime DEFAULT NULL COMMENT '业务日期',
  `remark` varchar(300) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '备注',
  `annex` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '附件',
  `status` tinyint(4) DEFAULT NULL COMMENT '状态 0草稿 1审核中 2已撤回 3已退回 4审核完成(待入库) 5部分入库 6入库完成',
  `entrise_id` int(11) DEFAULT '0' COMMENT '审核流程id',
  `bank_name` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '开户银行',
  `subbranch` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '开户支行',
  `bank_account` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '银行账户',
  `account_holder` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '开户人',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='进销存销售退货表';
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
        Schema::dropIfExists('pas_sale_return_orders');
    }
}
