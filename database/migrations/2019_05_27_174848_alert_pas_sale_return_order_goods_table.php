<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlertPasSaleReturnOrderGoodsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = <<<EOF
ALTER TABLE `pas_sale_return_order_goods`
ADD COLUMN `in_num`  int(11) NULL DEFAULT 0 COMMENT '入库数量' AFTER `return_num`,
ADD COLUMN `apply_in_num`  int(11) NULL DEFAULT 0 COMMENT '申请入库数量' AFTER `in_num`;
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
