<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlertPasSaleOutWarehouseTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = <<<EOF
ALTER TABLE `pas_sale_out_warehouse`
MODIFY COLUMN `status`  tinyint(1) NULL DEFAULT 0 COMMENT '申请单状态 状态 0草稿 1审核中 2已撤回 3已驳回 4审核完成' AFTER `shipping_id`,
ADD COLUMN `entrise_id`  int(11) NULL DEFAULT 0 COMMENT '审批流id' AFTER `status`;
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
