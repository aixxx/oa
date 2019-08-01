<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlertTableReportTemplateFormsDropShowInTodo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = <<<EOF
ALTER TABLE `report_template_forms`
DROP COLUMN `show_in_todo`,
MODIFY COLUMN `sort`  int(11) NOT NULL COMMENT '排序' AFTER `rules`;
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
