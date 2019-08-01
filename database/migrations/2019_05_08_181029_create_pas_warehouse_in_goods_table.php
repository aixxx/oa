<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePasWarehouseInGoodsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pas_warehouse_in_goods', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('in_id')->nullable()->comment('入库单id');
            $table->integer('goods_id')->nullable()->comment('商品id');
            $table->string('goods_no')->nullable()->comment('商品编号');
            $table->integer('sku_id')->nullable()->comment('skuId');
            $table->integer('in_num')->nullable()->comment('申请数');
            $table->integer('stored_num')->nullable()->comment('入库数');
//            $table->integer('house_num')->nullable()->comment('库存数');
            $table->integer('status')->nullable()->default(0)->comment('状态');
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
        Schema::dropIfExists('pas_warehouse_in_goods');
    }
}
