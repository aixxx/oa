<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPIdToPasWarehousingApplyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pas_warehousing_apply', function (Blueprint $table) {
            $table->unsignedBigInteger('p_id')->nullable(true)->length(20)->comment('采购订单id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pas_warehousing_apply', function (Blueprint $table) {
            //
        });
    }
}
