<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMeetingParticipantTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('meeting_participant', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('自动编号');
            $table->unsignedBigInteger('user_id')->nullable(true)->length(20)->comment('用户编号');
            $table->unsignedBigInteger('m_id')->nullable(true)->length(20)->comment('会议编号id');
            $table->tinyInteger('signin')->nullable(true)->default(0)->length(1)->comment('类型 0未签到 1已签到');
            $table->tinyInteger('type')->nullable(true)->default(0)->length(1)->comment('类型 0参与人 1抄送人');
            $table->tinyInteger('status')->nullable(true)->default(1)->length(1)->comment('状态 0删除 1未查看  2已查看');
            $table->timestamps();
        });
        DB::statement("ALTER TABLE `meeting_participant` comment '会议参与人（抄送人）'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('meeting_participant');
    }
}
