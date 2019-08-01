<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddEntriseIdToPasPurchaseTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pas_purchase', function (Blueprint $table) {
            $table->unsignedBigInteger('entrise_id')->nullable(true)->length(20)->comment('数据编号id');
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
