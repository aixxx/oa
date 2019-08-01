<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateApiVueRoutesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('api_vue_routes', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('route_id');
            $table->unsignedInteger('action_id');
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('action_id')->references('id')->on('api_vue_action');
            $table->foreign('route_id')->references('id')->on('api_routes');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('api_vue_routes');
    }
}
