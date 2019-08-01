<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSkuIdToPasPurchaseCommodityContentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pas_purchase_commodity_content', function (Blueprint $table) {
            $table->string('sku_id', 255)->nullable()->comment('sku组合id');
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
