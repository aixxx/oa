<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPStatusToPasReturnOrderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pas_return_order', function (Blueprint $table) {
            $table->tinyInteger('p_status')->nullable(true)->default(0)->length(1)->comment('付款状态 0未付款 1 付款申请中 2付款完成 ');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pas_return_order', function (Blueprint $table) {
            //
        });
    }
}
