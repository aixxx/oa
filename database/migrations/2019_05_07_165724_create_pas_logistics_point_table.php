<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePasLogisticsPointTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pas_logistics_point', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('status')->nullable()->default(0)->comment('状态');
            $table->string('point', 255)->nullable()->default(0)->comment('网点');
            $table->string('tel', 100)->nullable()->default(0)->comment('电话');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pas_logistics_point');
    }
}
