<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddGoodsIdToPasPurchaseCommodityContentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pas_purchase_commodity_content', function (Blueprint $table) {
            $table->unsignedBigInteger('goods_id')->nullable(true)->length(20)->comment('商品id');
            $table->string('goods_name', 255)->nullable()->comment('商品名称');
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
