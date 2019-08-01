<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAttendanceApiTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attendance_api', function (Blueprint $table) {
            $table->increments('id')->comment('自动编号');
            $table->string('title', 50)->comment('考勤组名称');
            $table->tinyInteger('system_type')->length(1)->comment('考勤制度类型 1-固定值 2-排班制 3-自由制')->default(1);
            $table->integer('classes_id')->length(11)->nullable()->comment('班次ID');
            $table->string('weeks')->length(50)->nullable()->comment('考勤日期');
            $table->integer('cycle_id')->length(11)->nullable()->comment('班次ID');
            $table->time('clock_node')->nullable()->comment('打卡节点');
            $table->integer('add_clock_num')->length(11)->nullable()->comment('班次ID')->default(0);
            $table->string('address')->length(100)->nullable()->comment('上班地址');
            $table->integer('clock_range')->length(11)->nullable()->comment('允许打卡范围');
            $table->string('wifi_title')->length(100)->nullable()->comment('办公wifi名称');
            $table->integer('head_user_id')->length(11)->nullable()->comment('考勤组负责人');
            $table->integer('overtime_rule_id')->length(11)->nullable()->comment('加班规则ID');
            $table->tinyInteger('is_getout_clock')->length(1)->comment('是否允许外勤打卡， 1-允许 0-不允许')->default(1);
            $table->timestamp('created_at')->nullable()->comment('创建时间');
            $table->timestamp('updated_at')->nullable()->comment('更新时间');
            $table->timestamp('deleted_at')->nullable()->comment('删除时间');
            $table->integer('admin_id')->length(11)->comment('系统最后操作人员ID');
            $table->index('deleted_at');
            $table->comment = '考勤组';
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('attendance_api');
    }
}
