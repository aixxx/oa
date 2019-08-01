<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPStatusToPasPurchaseTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pas_purchase', function (Blueprint $table) {
            $table->tinyInteger('p_status')->length(1)->comment('付款状态 0未付款 1 付款申请中 2付款完成');
            $table->tinyInteger('w_status')->length(1)->comment('入库状态 0入库未完成 1 入库完成');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pas_purchase', function (Blueprint $table) {
            //
        });
    }
}
