<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMeetingRoomTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('meeting_room', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('自动编号');
            $table->string('title')->length('100')->nullable()->comment('会议室名称');
            $table->string('code')->length('20')->nullable()->comment('会议室编号');
            $table->unsignedBigInteger('user_id')->nullable(true)->length(20)->comment('会议室添加人id');
            $table->string('position')->length('100')->nullable()->comment('会议室位置');
            $table->string('configure')->length('100')->nullable()->comment('设备配置（白板,会议桌椅,投影仪）');
            $table->string('number')->length('10')->nullable()->comment('会议人数');
            $table->char('start')->length('2')->nullable()->comment('开始预约');
            $table->char('end')->length('2')->nullable()->comment('截止预约时间');
            $table->text('remarks')->nullable()->comment('备注');
            $table->tinyInteger('status')->nullable(true)->default(0)->length(1)->comment('0已删除  1可使用 2已停用 ');

            $table->timestamps();
        });
        DB::statement("ALTER TABLE `performance_application` comment '会议室表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('meeting_room');
    }
}
