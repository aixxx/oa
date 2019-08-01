<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterAssetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //

        Schema::table('corporate_assets_borrow', function (Blueprint $table) {
            //
            $table->string('num')->length(150)->comment('借用单号')->change();
        });

        Schema::table('corporate_assets_depreciation', function (Blueprint $table) {
            //
            $table->string('num')->length(150)->comment('折旧单号')->change();
        });
        Schema::table('corporate_assets_repair', function (Blueprint $table) {
            //
            $table->string('num')->length(150)->comment('送修单号')->change();
        });
        Schema::table('corporate_assets_return', function (Blueprint $table) {
            //
            $table->string('num')->length(150)->comment('归还单号')->change();
        });
        Schema::table('corporate_assets_scrapped', function (Blueprint $table) {
            //
            $table->string('num')->length(150)->comment('报废单号')->change();
        });
        Schema::table('corporate_assets_transfer', function (Blueprint $table) {
            //
            $table->string('num')->length(150)->comment('调拨单号')->change();
        });
        Schema::table('corporate_assets_use', function (Blueprint $table) {
            //
            $table->string('num')->length(150)->comment('领用单号')->change();
        });
        Schema::table('corporate_assets_valueadded', function (Blueprint $table) {
            //
            $table->string('num')->length(150)->comment('增值单号')->change();
        });
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
