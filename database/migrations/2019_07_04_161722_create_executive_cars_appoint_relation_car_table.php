<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExecutiveCarsAppointRelationCarTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('executive_cars_appoint_relation_car', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('cars_appoint_id')->length(10)->comment('派车车记录ID');
            $table->integer('cars_id')->length(10)->comment('车辆ID');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('executive_cars_appoint_relation_car');
    }
}
