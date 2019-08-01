<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSchedulesTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('schedules', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('content')->length(255)->comment('日程描述');
            $table->tinyInteger('all_day_yes')->length(1)->comment('是否全天，1：是，2：否');
            $table->timestamp('start_at')->nullable()->comment('开始时间');
            $table->timestamp('end_at')->nullable()->comment('截止时间');
            $table->tinyInteger('send_type')->length(1)->comment('发送方式，1：应用，2：短信');
            $table->tinyInteger('prompt_type')->length(1)->comment('提醒类型，提醒类型，1：截止前15分钟，2：前1小时，3：前3小时，4：前1天');
            $table->tinyInteger('repeat_type')->length(1)->comment('重复设置，1：重复，2：不重复');
            $table->string('address')->length(255)->comment('地点');
            $table->bigInteger('create_schedule_user_id')->length(10)->comment('日程创建者ID');
            $table->timestamp('created_at')->nullable()->comment('创建时间');
            $table->timestamp('updated_at')->nullable()->comment('更新时间');
            $table->timestamp('deleted_at')->nullable()->comment('删除时间');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('schedules');
    }

}
