<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterAddColumnLogisticsIdForPasLogisticsPointTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pas_logistics_point', function (Blueprint $table) {
            //
            $table->integer('logistics_id')->nullable()->comment('物流ID');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pas_logistics_point', function (Blueprint $table) {
            //
            $table->dropColumn('logistics_id');
        });
    }
}
