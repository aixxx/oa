<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePasSaleOrdersTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = <<<EOF
CREATE TABLE `pas_sale_orders` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '自动编号',
  `order_sn` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '销售单号',
  `user_id` int(11) unsigned DEFAULT '0' COMMENT '申请用户编号',
  `buy_user_id` int(11) unsigned DEFAULT '0' COMMENT '销售的客户',
  `goods_num` int(8) DEFAULT '0' COMMENT '销售的商品总数',
  `goods_money` decimal(11,2) DEFAULT '0.00' COMMENT '商品总金额',
  `total_money` decimal(11,2) DEFAULT '0.00' COMMENT '此单金额',
  `receivable_money` decimal(11,2) DEFAULT '0.00' COMMENT '应收金额',
  `actual_money` decimal(11,2) DEFAULT '0.00' COMMENT '实收金额',
  `zero_money` decimal(11,2) DEFAULT '0.00' COMMENT '抹零金额',
  `other_money` decimal(11,2) DEFAULT '0.00' COMMENT '其它费用金额',
  `discount` float(5,2) DEFAULT '100.00' COMMENT '订单折扣',
  `expected_pay_time` datetime DEFAULT NULL COMMENT '预计付款时间',
  `bank_name` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '开户银行',
  `subbranch` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '开户支行',
  `bank_account` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '银行账户',
  `account_holder` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '开户人',
  `business_time` datetime DEFAULT NULL COMMENT '业务日期',
  `account_period` mediumint(9) DEFAULT '0' COMMENT '账期 (单位天)',
  `sale_user_id` int(11) DEFAULT '0' COMMENT '销售员',
  `invoice_id` int(11) DEFAULT '0' COMMENT '发货方式',
  `remark` varchar(300) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '备注',
  `annex` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '附件',
  `status` tinyint(4) DEFAULT NULL COMMENT '状态 0草稿 1审核中 2已撤回 3已退回 4审核完成(待出库) 5部分出库 6出库完成',
  `entrise_id` int(11) DEFAULT '0' COMMENT '审核流程id',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='进销存销售表';
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
