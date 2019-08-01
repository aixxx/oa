<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlertPasSaleReturnOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = <<<EOF
ALTER TABLE `pas_sale_return_orders`
MODIFY COLUMN `status`  tinyint(4) NULL DEFAULT NULL COMMENT '状态 0草稿 1审核中 2已撤回 3已退回 4审核完成(待入库) 5部分入库 6入库完成' AFTER `annex`;
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
