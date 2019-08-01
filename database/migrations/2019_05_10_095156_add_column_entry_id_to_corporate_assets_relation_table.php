<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnEntryIdToCorporateAssetsRelationTable extends Migration
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
            $table->integer('entry_id')->length(11)->comment('审批流ID');
            $table->string('user_name')->length(255)->comment('用户名称');
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
