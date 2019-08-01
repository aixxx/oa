<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterIndexToCorporateAssetsRelationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('corporate_assets_relation', function (Blueprint $table) {
            //
            $table->dropUnique('assets_id');
        });
        $sql = <<<EOF
ALTER TABLE `corporate_assets_relation` add unique index(`assets_id`,`event_id`,`type`,`entry_id`);
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
        Schema::table('corporate_assets_relation', function (Blueprint $table) {
            //
        });
    }
}
