<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRwNumberToPasPurchaseCommodityContentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pas_purchase_commodity_content', function (Blueprint $table) {
            $table->integer('rw_number')->length(11)->nullable(true)->default(0)->comment('入库过后的退货');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pas_purchase_commodity_content', function (Blueprint $table) {
            //
        });
    }
}
