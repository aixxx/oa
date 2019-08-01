<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAttendanceApiHrUpdateClockLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attendance_api_hr_update_clock_log', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('admin_id')->length(11)->comment('管理员ID');
            $table->integer('user_id')->length(11)->comment('用户ID');
            $table->integer('classes_id')->length(11)->comment('班次ID');
            $table->date('dates')->comment('日期');
            $table->date('work_time')->comment('上班或者下班时间');
            $table->string('remark', 255)->comment('描述');
            $table->string('remark_image', 255)->comment('描述图片');
            $table->integer('type')->length(11)->comment('类别 1-上班 2-下班');
            $table->tinyInteger('anomaly_type')->length(4)->comment('异常类型 0-正常 1-迟到 2-早退 3-加班 4-缺卡 5-旷工');
            $table->integer('anomaly_time')->length(11)->comment('异常时间');
            $table->integer('anomaly_id')->length(11)->comment('异常ID');
            $table->integer('clock_nums')->length(11)->comment('第几次上下班');
            $table->timestamp('created_at')->nullable()->comment('创建时间');
            $table->timestamp('updated_at')->nullable()->comment('更新时间');
            $table->timestamp('deleted_at')->nullable()->comment('删除时间');
            $table->index('deleted_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('attendance_api_hr_update_clock_log');
    }
}
