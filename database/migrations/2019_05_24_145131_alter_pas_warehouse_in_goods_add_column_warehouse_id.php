<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterPasWarehouseInGoodsAddColumnWarehouseId extends Migration
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
            $table->integer('warehouse_id')->nullable()->comment('仓库ID');
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
            $table->dropColumn('warehouse_id');
        });
    }
}
