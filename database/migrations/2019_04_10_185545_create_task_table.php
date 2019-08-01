<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTaskTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('task', function (Blueprint $table) {
            $table->increments('id')->comment('自动编号');
            $table->text('info')->comment('任务详情');
            $table->char('enclosure')->nullable()->comment('任务附件');
            $table->tinyInteger('send_type')->length(3)->nullable()->comment('发送类型：1应用通知，2短信');
            $table->timestamp('deadline')->nullable()->comment('截止时间');
            $table->tinyInteger('remind_time')->length(3)->nullable()->comment('提醒时间:1:15i。2:1h。3:3h。4:1d');
            $table->integer('create_user_id')->length(20)->nullable()->comment('创建人id');
            $table->timestamp('send_time')->nullable()->comment('发送时间');
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
        Schema::dropIfExists('task');
    }
}
