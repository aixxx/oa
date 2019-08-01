<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGoodsAllocationGoodsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('goods_allocation_goods', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('allocation_id')->nullable()->comment('货位id');
            $table->integer('goods_id')->nullable()->comment('商品id');
            $table->integer('number')->nullable()->comment('数量');
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
        Schema::dropIfExists('goods_allocation_goods');
    }
}
