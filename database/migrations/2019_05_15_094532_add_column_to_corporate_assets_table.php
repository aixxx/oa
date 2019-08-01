<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnToCorporateAssetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('corporate_assets', function (Blueprint $table) {
            //
            $table->integer('depreciation_months')->length(10)->comment('折旧月数');
            $table->integer('depreciation_status')->length(3)->comment('折旧状态，1：可折旧，2：不可折旧');
            $table->timestamp('remaining_at')->nullable()->comment('折旧月份');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('corporate_assets', function (Blueprint $table) {
            //
        });
    }
}
