<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePasPurchasePayableMoneyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = <<<EOF
            CREATE TABLE `pas_purchase_payable_money` (
              `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '自动编号',
              `type` tinyint(4) DEFAULT '1' COMMENT '状态 1表示采购单',
              `supplier_id` bigint(20) unsigned DEFAULT '0' COMMENT '供应商id',
              `money` decimal(10,2) DEFAULT '0.00' COMMENT '金额',
              `status` tinyint(4) DEFAULT '1' COMMENT '状态 1 0删除',
              `created_at` timestamp NULL DEFAULT NULL,
              `updated_at` timestamp NULL DEFAULT NULL,
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB AUTO_INCREMENT=37 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='进销存采购单此前应付金额';
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
        Schema::dropIfExists('pas_purchase_payable_money');
    }
}
