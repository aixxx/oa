<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTripAgendaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trip_agenda', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('trip_id')->length(11)->comment('出差id');
            $table->tinyInteger('vehicle')->length(1)->comment('交通工具 (1.飞机 2.火车 3.汽车 4.其他)');
            $table->tinyInteger('go_type')->length(1)->comment('行程类型 （1.单程  2.往返）');
            $table->string('depart_city')->comment('出差城市');
            $table->string('whither_city')->comment('目的城市');
            $table->timestamp('begin_time')->nullable()->comment('开始时间');
            $table->timestamp('end_time')->nullable()->comment('结束时间');
            $table->tinyInteger('time_count')->length(1)->comment('时长统计（天）');
            $table->timestamps();
            $table->softDeletes();
            $table->index('trip_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('trip_agenda');
    }
}
