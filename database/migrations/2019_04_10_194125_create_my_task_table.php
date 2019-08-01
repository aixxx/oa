<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMyTaskTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('my_task', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('tid')->length(20)->comment('任务id');
            $table->bigInteger('uid')->length(20)->comment('用户id');
            $table->integer('pid')->length(20)->comment('部门项目id');
            $table->string('type_name')->nullable()->comment('类型名称');
            $table->tinyInteger('status')->default(0)->length(3)->nullable()->comment('任务状态（-1拒绝,0默认,1待确认,2待办理,3待评价）');
            $table->tinyInteger('user_type')->length(3)->nullable()->comment('用户类型（1接收人，2抄送人，3部门下的项目）');
            $table->integer('create_user_id')->length(3)->comment('创建者id');
            $table->text('success_info')->comment('完成后评价');
            $table->timestamp('finish_time')->comment('完成时间');
            $table->timestamp('start_time')->comment('任务开始时间');
            $table->timestamp('end_time')->comment('任务结束时间');
            $table->softDeletes();
            $table->timestamps();
            $table->index('tid');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('my_task');
    }
}
