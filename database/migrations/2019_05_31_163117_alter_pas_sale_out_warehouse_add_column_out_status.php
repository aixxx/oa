<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterPasSaleOutWarehouseAddColumnOutStatus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pas_sale_out_warehouse', function (Blueprint $table) {
            //
            $table->integer('warehouse_id')->nullable()->comment('仓库ID');
            $table->integer('out_status')->nullable()->comment('出库状态');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pas_sale_out_warehouse', function (Blueprint $table) {
            //
            $table->dropColumn('warehouse_id');
            $table->dropColumn('out_status');
        });
    }
}
