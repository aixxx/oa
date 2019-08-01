<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterGoodsAllocationGoodsAddSkuIdCloumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('goods_allocation_goods', function (Blueprint $table) {
            //
            $table->integer('sku_id')->nullable()->comment('skuID');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('goods_allocation_goods', function (Blueprint $table) {
            //
            $table->dropColumn('sku_id');
        });
    }
}
