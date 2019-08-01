<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnToCorporateAssetsSyncTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('corporate_assets_sync', function (Blueprint $table) {
            //
            $table->integer('user_id')->length(11)->comment('用户ID');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('corporate_assets_sync', function (Blueprint $table) {
            //
        });
    }
}
