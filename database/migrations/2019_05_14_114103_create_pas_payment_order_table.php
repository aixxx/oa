<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePasPaymentOrderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = <<<EOF
		CREATE TABLE `pas_payment_order` (
          `id` bigint(20) NOT NULL AUTO_INCREMENT,
          `code` varchar(30) DEFAULT '' COMMENT '付款单编号',
          `p_code` varchar(30) DEFAULT '' COMMENT '采购单号',
          `business_date` varchar(20) DEFAULT '' COMMENT '业务日期',
          `supplier_id` bigint(30) DEFAULT '0' COMMENT '供应商id',
          `apply_id` bigint(20) DEFAULT '0' COMMENT '经手人id',
          `apply_name` varchar(20) DEFAULT '' COMMENT '经手人名称',
          `payable_money` decimal(10,2) DEFAULT '0.00' COMMENT '此前应付钱',
          `type` tinyint(1) DEFAULT '0' COMMENT '暂未启用保留',
          `remarks` text COMMENT '备注',
          `money` decimal(10,2) DEFAULT '0.00' COMMENT '退货总金额',
          `entrise_id` int(10) DEFAULT '0' COMMENT '会议工作流编号',
          `status` tinyint(1) DEFAULT '1' COMMENT '状态',
          `created_at` timestamp NULL DEFAULT NULL,
          `updated_at` timestamp NULL DEFAULT NULL,
          `user_id` bigint(20) unsigned DEFAULT NULL COMMENT '付款单的用户id',
          `supplier_name` varchar(100) DEFAULT '' COMMENT '供应商名称',
          PRIMARY KEY (`id`),
          KEY `p_code` (`p_code`)
        ) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COMMENT='采购付款单';
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
        Schema::dropIfExists('pas_payment_order');
    }
}
