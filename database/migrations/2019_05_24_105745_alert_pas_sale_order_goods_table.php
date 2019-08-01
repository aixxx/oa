<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlertPasSaleOrderGoodsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = <<<EOF
ALTER TABLE `pas_sale_order_goods`
ADD COLUMN `out_num`  int(11) NULL DEFAULT 0 COMMENT '出库数量' AFTER `num`,
ADD COLUMN `apply_out_num`  int(11) NULL DEFAULT 0 COMMENT '申请中出库数量' AFTER `out_num`,
ADD COLUMN `back_num`  int(11) NULL DEFAULT 0 COMMENT '退货数量' AFTER `apply_out_num`,
ADD COLUMN `apply_back_num`  int(11) NULL DEFAULT 0 COMMENT '申请中退货数量' AFTER `back_num`;
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
