<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWarehouseOutGoodsTable extends Migration
{
    /**
     * Run the migrations.
     *外出单 对应的商品信息表
     * @return void
     */
    public function up()
    {
        Schema::create('pas_warehouse_out_goods', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('out_id')->nullable()->comment('外出单id');
            $table->integer('goods_id')->nullable()->comment('商品id');
            $table->string('goods_no')->nullable()->comment('商品编号');
            $table->integer('sku_id')->nullable()->comment('skuId');
            $table->integer('apply_num')->nullable()->comment('申请数');
            $table->integer('outed_num')->nullable()->comment('出库数');
            $table->integer('house_num')->nullable()->comment('库存数');
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
        Schema::dropIfExists('pas_warehouse_out_goods');
    }
}
