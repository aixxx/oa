<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnRepairAtToCorporateAssetsRepairTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('corporate_assets_repair', function (Blueprint $table) {
            //
            $table->timestamp('repair_at')->nullable(true)->comment('送修时间');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('corporate_assets_repair', function (Blueprint $table) {
            //
        });
    }
}
