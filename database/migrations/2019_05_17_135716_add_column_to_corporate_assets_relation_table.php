<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnToCorporateAssetsRelationTable extends Migration
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
            $table->string('type_name')->length(100)->nullable(true)->comment('类型名称');

        });
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
