<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlertTableReportTemplatesDropIndex extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        try{
            $sql = <<<EOF
ALTER TABLE `report_templates`
DROP INDEX `workflow_templates_template_name_unique` ,
ADD INDEX `workflow_templates_template_name_unique` (`template_name`) USING BTREE ;
EOF;
            DB::statement($sql);
        }catch (Exception $e){

        }
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
