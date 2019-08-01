<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddGoodsUrlToPasPurchaseCommodityContentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pas_purchase_commodity_content', function (Blueprint $table) {
            $table->string('goods_url', 255)->nullable()->comment('商品图片');
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
