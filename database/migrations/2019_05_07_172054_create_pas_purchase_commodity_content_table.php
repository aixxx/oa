<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePasPurchaseCommodityContentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = <<<EOF
		CREATE TABLE `pas_purchase_commodity_content` (
          `id` bigint(20) NOT NULL AUTO_INCREMENT,
          `p_code` varchar(30) DEFAULT '' COMMENT '采购单号',
          `p_id` bigint(20) DEFAULT '0' COMMENT '采购表id',
          `sku` varchar(255) DEFAULT '' COMMENT 'sku (规则组合)',
          `number` int(10) DEFAULT '0' COMMENT '商品(sku)采购的数量',
          `price` decimal(10,2) DEFAULT '0.00' COMMENT '总数量',
          `money` decimal(10,2) DEFAULT '0.00' COMMENT '总金额',
          `r_number` int(10) DEFAULT '0' COMMENT '退货数量',
          `war_number` int(10) DEFAULT '0' COMMENT '可入库数量',
          `wa_number` int(10) DEFAULT '0' COMMENT '申请入库数量',
          `awa_numbe` int(10) DEFAULT '0' COMMENT '已经入库数量',
          `status` tinyint(1) DEFAULT '1' COMMENT '状态 0删除  1正常',
          `created_at` timestamp NULL DEFAULT NULL,
          `updated_at` timestamp NULL DEFAULT NULL,
          PRIMARY KEY (`id`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='采购商品(sku)表';
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
        Schema::dropIfExists('pas_purchase_commodity_content');
    }
}
