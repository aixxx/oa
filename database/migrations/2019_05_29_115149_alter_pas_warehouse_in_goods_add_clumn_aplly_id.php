<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterPasWarehouseInGoodsAddClumnApllyId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pas_warehouse_in_goods', function (Blueprint $table) {
            //
            $table->integer('type')->nullable()->comment('数据源类型');
            $table->integer('apply_id')->nullable()->comment('申请单ID');
            $table->integer('apply_goods_id')->nullable()->comment('申请单商品主键ID');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pas_warehouse_in_goods', function (Blueprint $table) {
            //
            $table->dropColumn('type');
            $table->dropColumn('apply_id');
            $table->dropColumn('apply_goods_id');
        });
    }
}
