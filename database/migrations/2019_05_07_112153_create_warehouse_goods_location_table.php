<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWarehouseGoodsLocationTable extends Migration
{
    /**
     * 入库单和货位关联表
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pas_warehouse_goods_location', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('warehouse_id')->comment('仓库ID');
            $table->integer('goods_allocation_id')->comment('货位ID');
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
        Schema::dropIfExists('pas_warehouse_goods_location');
    }
}
