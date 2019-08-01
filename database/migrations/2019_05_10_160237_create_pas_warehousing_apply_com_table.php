<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePasWarehousingApplyComTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = <<<EOF
		CREATE TABLE `pas_warehousing_apply_content` (
          `id` bigint(20) NOT NULL AUTO_INCREMENT,
          `code` varchar(30) DEFAULT '' COMMENT '单号',
          `p_id` bigint(20) DEFAULT '0' COMMENT '入库申请表（退货申请）id',
          `good_url` varchar(255) DEFAULT '' COMMENT '图片地址',
          `good_name` varchar(255) DEFAULT '' COMMENT '商品名称',
          `good_id` bigint(20) DEFAULT '0' COMMENT '商品id',
          `number` int(10) DEFAULT '0' COMMENT '申请数量',
          `r_number` int(11) DEFAULT '0' COMMENT '库成功数量（退货成功数量）',
          `money` decimal(10,2) DEFAULT '0.00' COMMENT '总金额',
          `status` tinyint(1) DEFAULT '1' COMMENT '状态',
          `type` tinyint(1) DEFAULT '1' COMMENT '1申请入库  2申请退货',
          `created_at` timestamp NULL DEFAULT NULL,
          `updated_at` timestamp NULL DEFAULT NULL,
          PRIMARY KEY (`id`)
      ) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COMMENT='入库单商品表';
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
        Schema::dropIfExists('pas_warehousing_apply_content');
    }
}
