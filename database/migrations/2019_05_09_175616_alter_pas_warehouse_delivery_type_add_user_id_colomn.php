<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterPasWarehouseDeliveryTypeAddUserIdColomn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pas_warehouse_delivery_type', function (Blueprint $table) {
            //
            $table->integer('user_id')->nullable()->comment('用户ID');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pas_warehouse_delivery_type', function (Blueprint $table) {
            //
        });
    }
}
