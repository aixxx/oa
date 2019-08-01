<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVacationTripRecordTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vacation_trip_record', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('business_trip_id')->comment('出差记录id');
            $table->string('fd_traffic', 20)->comment('交通工具');
            $table->string('fd_go_and_back', 20)->comment('单程往返');
            $table->integer('fd_start_of')->comment('出发城市');
            $table->integer('fd_purpose')->comment('目的城市');
            $table->timestamp('fd_begin_time')->comment('开始时间');
            $table->timestamp('fd_end_time')->comment('结束时间');
            $table->integer('fd_time_sub_by_day')->comment('时长(天)');
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
        Schema::dropIfExists('vacation_trip_record');
    }
}
