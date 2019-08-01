<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterPasWarehouseOutCardAddApplyIdColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pas_warehouse_out_card', function (Blueprint $table) {
            //
            $table->integer('apply_id')->nullable()->comment('出库单申请单据ID');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pas_warehouse_out_card', function (Blueprint $table) {
            //
            $table->dropColumn('apply_id');
        });
    }
}
