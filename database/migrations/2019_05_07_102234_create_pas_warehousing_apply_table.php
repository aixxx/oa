<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePasWarehousingApplyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
         $sql = <<<EOF
		CREATE TABLE `pas_warehousing_apply` (
		  `id` bigint(20) NOT NULL AUTO_INCREMENT,
		  `code` varchar(30) DEFAULT '' COMMENT '入库单号',
		  `p_code` varchar(30) DEFAULT '' COMMENT '采购单号',
		  `business_date` varchar(20) DEFAULT '' COMMENT '业务日期',
		  `supplier_id` bigint(30) DEFAULT '0' COMMENT '供应商id',
		  `apply_id` bigint(20) DEFAULT '0' COMMENT '经手人id',
		  `apply_name` varchar(20) DEFAULT '' COMMENT '经手人名称',
		  `payable_money` decimal(10,2) DEFAULT '0.00' COMMENT '此前应付钱',
		  `remarks` text COMMENT '备注',
		  `status` tinyint(1) DEFAULT '1' COMMENT '状态  0草稿 1待入库未安排 2待入库 - 仓库已安排 3部分入库 4全部入库 ',
		  `created_at` timestamp NULL DEFAULT NULL,
		  `updated_at` timestamp NULL DEFAULT NULL,
		  PRIMARY KEY (`id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='入库申请';
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
        Schema::dropIfExists('pas_warehousing_apply');
    }
}
