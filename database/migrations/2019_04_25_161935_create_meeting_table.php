<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMeetingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('meeting', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('自动编号');
            $table->unsignedBigInteger('mr_id')->nullable(true)->length(20)->comment('会议室id');
            $table->string('title')->length('100')->nullable()->comment('会议名称');
            $table->text('describe')->nullable()->comment('会议描述');
            $table->unsignedBigInteger('user_id')->nullable(true)->length(20)->comment('会议创建人id');
            $table->unsignedBigInteger('host_id')->nullable(true)->length(20)->comment('主持人id');
            $table->dateTime('start')->nullable()->comment('开始时间');
            $table->dateTime('end')->nullable()->comment('结束时间');
            $table->string('day')->length('20')->nullable(true)->comment('年月日');
            $table->string('remind')->length('100')->nullable()->comment('提示设置');
            $table->text('meeting_file')->nullable()->comment('会议文件');
            $table->text('meeting_summary')->nullable()->comment('会议纪要');
            $table->Integer('number')->nullable(true)->default(0)->length(11)->comment('参与人数');
            $table->tinyInteger('repeat_type')->nullable(true)->default(1)->length(1)->comment('重复状态 0重复 1不重复');
            $table->tinyInteger('send_type')->nullable(true)->default(1)->length(1)->comment('发送方式 0应用 1短信');
            $table->tinyInteger('status')->nullable(true)->default(0)->length(1)->comment('状态 0已取消 1可使用');
            $table->timestamps();

        });
        DB::statement("ALTER TABLE `meeting` comment '会议表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('meeting');
    }
}
