<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterPasGoodsAllocationAddCloumnNoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pas_goods_allocation', function (Blueprint $table) {
            //
            $table->string('no', 100)->nullable()->comment('货位号');
            $table->integer('warehouse_id')->nullable()->comment('仓库编号');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pas_goods_allocation', function (Blueprint $table) {
            //
            $table->removeColumn('no');
        });
    }
}
