<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlertTableReportTemplatesTemplateNameIndex extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = <<<EOF
ALTER TABLE `report_templates`
DROP INDEX `workflow_templates_template_name_unique` ,
ADD UNIQUE INDEX `template_name_company_id_unique` (`template_name`, `company_id`) USING BTREE ;
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
