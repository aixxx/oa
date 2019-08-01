<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlertTableMyTaskAddParentId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = <<<EOF
ALTER TABLE `my_task`
ADD COLUMN `parent_id`  int(11) NULL DEFAULT 0 COMMENT '父级任务id，此表' AFTER `pid`,
ADD COLUMN `level`  tinyint(3) NULL DEFAULT 0 COMMENT '任务级别' AFTER `parent_id`,
ADD COLUMN `parent_ids`  varchar(300) NULL DEFAULT 0 COMMENT '所有父级' AFTER `level`;
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
