<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddApplyIdToPasPurchaseTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pas_purchase', function (Blueprint $table) {
            $table->unsignedBigInteger('apply_id')->nullable(true)->length(20)->comment('经手人用户编号');
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
