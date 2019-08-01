<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGoodsFlowTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pas_goods_flow', function (Blueprint $table) {
            $table->increments('id');
            $table->string('sku_name', 100)->nullable()->comment('商品货号');
            $table->integer('sku_id')->nullable()->comment('商品货号');
            $table->integer('goods_id')->nullable()->comment('商品ID');
            $table->string('card_no', 100)->nullable()->comment('编号');
            $table->integer('warehouse_id')->nullable()->comment('仓库id');
            $table->string('type', 20)->nullable()->comment('状态');
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
        Schema::dropIfExists('pas_goods_flow');
    }
}
