<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAllotCardGoodsTable extends Migration
{
    /**
     * Run the migrations.
     * 调拨单对应单商品
     * @return void
     */
    public function up()
    {
        Schema::create('pas_allot_card_goods', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('allot_id')->nullable()->comment('调拨单id');
            $table->integer('goods_id')->nullable()->comment('商品ID');
            $table->integer('sku_id')->nullable()->comment('sku_id');
            $table->integer('number')->nullable()->comment('调拨数量');
            $table->integer('status')->nullable()->comment('状态');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pas_allot_card_goods');
    }
}
