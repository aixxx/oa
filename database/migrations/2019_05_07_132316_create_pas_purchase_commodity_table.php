<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePasPurchaseCommodityTable extends Migration
{
    /**
     * Run the migrations.
     *采购商品表
     * @return void
     */
    public function up()
    {
        $sql = <<<EOF
		CREATE TABLE `pas_purchase_commodity` (
          `id` bigint(20) NOT NULL AUTO_INCREMENT,
          `p_code` varchar(30) DEFAULT '' COMMENT '采购单号',
          `p_id` bigint(20) DEFAULT '0' COMMENT '采购表id',
          `c_url` varchar(255) DEFAULT '' COMMENT '图片地址',
          `c_name` varchar(255) DEFAULT '' COMMENT '商品名称',
          `c_id` bigint(20) DEFAULT '0' COMMENT '商品id',
          `number` int(10) DEFAULT '0' COMMENT '总数量',
          `money` decimal(10,2) DEFAULT '0.00' COMMENT '总金额',
          `status` tinyint(1) DEFAULT '1' COMMENT '状态',
          `created_at` timestamp NULL DEFAULT NULL,
          `updated_at` timestamp NULL DEFAULT NULL,
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='采购商品表';
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
        Schema::dropIfExists('pas_purchase_commodity');
    }
}
