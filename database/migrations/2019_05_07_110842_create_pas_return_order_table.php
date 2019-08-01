<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePasReturnOrderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = <<<EOF
		CREATE TABLE `pas_return_order` (
          `id` bigint(20) NOT NULL AUTO_INCREMENT,
          `code` varchar(30) DEFAULT '' COMMENT '退货单号',
          `p_code` varchar(30) DEFAULT '' COMMENT '采购单号',
          `business_date` varchar(20) DEFAULT '' COMMENT '业务日期',
          `supplier_id` bigint(30) DEFAULT '0' COMMENT '供应商id',
          `apply_id` bigint(20) DEFAULT '0' COMMENT '经手人id',
          `apply_name` varchar(20) DEFAULT '' COMMENT '经手人名称',
          `payable_money` decimal(10,2) DEFAULT '0.00' COMMENT '此前应付钱',
          `type` tinyint(1) DEFAULT '0' COMMENT '0未入库  1表示已入库',
          `remarks` text COMMENT '备注',
          `number` int(10) DEFAULT '0' COMMENT '退货总数',
          `money` decimal(10,2) DEFAULT '0.00' COMMENT '退货总金额',
          `entrise_id` int(10) DEFAULT '0' COMMENT '会议工作流编号',
          `status` tinyint(1) DEFAULT '1' COMMENT '状态',
          `created_at` timestamp NULL DEFAULT NULL,
          `updated_at` timestamp NULL DEFAULT NULL,
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='采购退货单';
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
        Schema::dropIfExists('pas_return_order');
    }
}
