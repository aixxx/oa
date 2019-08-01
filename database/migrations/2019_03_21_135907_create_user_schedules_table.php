<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserSchedulesTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_schedules', function(Blueprint $table)
        {
                $table->bigIncrements('id')->comment('自动编号');
                $table->unsignedBigInteger('create_schedule_user_id')->length(10)->comment('日程创建者ID');
                $table->unsignedBigInteger('schedule_id')->length(10)->comment('日程id');
                $table->string('content')->length(255)->comment('日程描述');
                $table->unsignedBigInteger('user_id')->length(10)->comment('用户id');
                $table->string('create_schedule_user_name')->length(255)->comment('发起人名字');
                $table->string('user_name')->length(255)->comment('接收人名字');
                $table->unsignedTinyInteger('confirm_yes')->comment('1待确认，2已确认');
                $table->timestamp('confirm_at')->nullable()->comment('确认时间');
                $table->timestamp('created_at')->nullable()->comment('创建时间');
                $table->timestamp('updated_at')->nullable()->comment('更新时间');
                $table->timestamp('deleted_at')->nullable()->comment('删除时间');
                $table->index('deleted_at');
                $table->index('schedule_id');
                $table->index('user_id');
                $table->index('create_schedule_user_id');
                $table->index('create_schedule_user_name');
                $table->index('user_name');
                $table->foreign('schedule_id')->references('id')->on('schedules');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('user_schedules');
    }

}
