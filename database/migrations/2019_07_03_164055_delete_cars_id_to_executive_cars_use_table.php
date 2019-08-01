<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DeleteCarsIdToExecutiveCarsUseTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('executive_cars_use', function (Blueprint $table) {
            //
            $table->dropColumn('cars_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('executive_cars_use', function (Blueprint $table) {
            //
        });
    }
}
