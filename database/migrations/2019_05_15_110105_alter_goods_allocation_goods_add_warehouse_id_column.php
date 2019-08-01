<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterGoodsAllocationGoodsAddWarehouseIdColumn extends Migration
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
            $table->integer('warehouse_id')->nullable()->default(0)->comment('仓库ID');
            $table->integer('goods_name')->nullable()->default(0)->comment('商品名称');
            $table->integer('goods_no')->nullable()->default(0)->comment('商品码');
            $table->integer('sku_title')->nullable()->default(0)->comment('sku title');
            $table->integer('brand_id')->nullable()->default(0)->comment('品牌');
            $table->integer('category_id')->nullable()->default(0)->comment('分类');
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
            $table->dropColumn('warehouse_id');
            $table->dropColumn('goods_name');
            $table->dropColumn('goods_no');
            $table->dropColumn('sku_title');
            $table->dropColumn('brand_id');
            $table->dropColumn('category_id');
        });
    }
}
