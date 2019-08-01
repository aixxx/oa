<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePasPaymentOrderContentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pas_payment_order_content', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('自动编号');
            $table->unsignedBigInteger('p_id')->nullable(true)->length(20)->comment('采购单或是退货单编号');
            $table->unsignedBigInteger('po_id')->nullable(true)->length(20)->comment('付款单编号');
            $table->tinyInteger('type')->nullable(true)->default(1)->length(1)->comment('状态 1采购单 2付款单 ');
            $table->softDeletes();
            $table->timestamps();
        });
        DB::statement("ALTER TABLE `pas_payment_order_content` comment '付款单广联采购单退货单'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pas_payment_order_content');
    }
}
