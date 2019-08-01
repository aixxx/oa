<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterPasGoodsFlowAddColumnAllocationId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pas_goods_flow', function (Blueprint $table) {
            //
            $table->integer('plan_id')->nullable()->default(0)->comment('入库计划ID（warehouse_in_card id）');
            $table->integer('allocation_id')->nullable()->default(0)->comment('货位ID');
            $table->integer('apply_id')->nullable()->default(0)->comment('申请单ID');
            $table->integer('type')->nullable()->default(0)->comment('申请单类型')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pas_goods_flow', function (Blueprint $table) {
            //
            $table->dropColumn('plan_id');
            $table->dropColumn('allocation_id');
            $table->dropColumn('apply_id');
        });
    }
}
