<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMeetingRoomConfigTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('meeting_room_config', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('自动编号');
            $table->unsignedBigInteger('mr_id')->nullable(true)->length(20)->comment('会议室id');
            $table->unsignedBigInteger('config_id')->nullable(true)->length(20)->comment('会议室id');
            $table->tinyInteger('status')->nullable(true)->default(0)->length(1)->comment('0已删除  1可使用');
            $table->timestamps();

        });
        DB::statement("ALTER TABLE `performance_application` comment '会议室配置表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('meeting_room_config');
    }
}
