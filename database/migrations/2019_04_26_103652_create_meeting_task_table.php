<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMeetingTaskTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('meeting_task', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('自动编号');
            $table->unsignedBigInteger('user_id')->nullable(true)->length(20)->comment('指派人编号id');
            $table->unsignedBigInteger('m_id')->nullable(true)->length(20)->comment('会议编号id');
            $table->text('count')->nullable()->comment('任务类容');
            $table->dateTime('end')->nullable()->comment('任务结束时间');
            $table->tinyInteger('status')->nullable(true)->default(1)->length(1)->comment('状态 0删除');
            $table->timestamps();
        });
        DB::statement("ALTER TABLE `meeting_task` comment '会议任务表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('meeting_task');
    }
}
