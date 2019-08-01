<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterStockCheckAddStatusColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pas_stock_check', function (Blueprint $table) {
            //
            $table->integer('status')->nullable()->comment('状态');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pas_stock_check', function (Blueprint $table) {
            //
            $table->dropColumn('status');
        });
    }
}
