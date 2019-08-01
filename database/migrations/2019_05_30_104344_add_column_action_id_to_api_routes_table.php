<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnActionIdToApiRoutesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('api_routes', function (Blueprint $table) {
            //
            $table->unsignedInteger('action_id')->length(10)->nullable(true)->comment('前台ID');
            $table->foreign('action_id')->references('id')->on('api_vue_action');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('api_routes', function (Blueprint $table) {
            //
        });
    }
}
