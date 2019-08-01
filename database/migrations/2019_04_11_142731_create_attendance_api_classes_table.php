<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAttendanceApiClassesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attendance_api_classes', function (Blueprint $table) {
            $table->increments('id')->comment('自动编号');
            $table->string('title', 50)->comment('班次名称');
            $table->string('code', 10)->comment('班次编号，最多10个字符');
            $table->tinyInteger('type')->length(1)->comment('上下班类别 1-一次，2-两次，3-三次');
            $table->time('work_time_begin1')->nullable()->comment('上班时间1');
            $table->time('work_time_end1')->nullable()->comment('下班时间1');
            $table->time('work_time_begin2')->nullable()->comment('上班时间2');
            $table->time('work_time_end2')->nullable()->comment('下班时间2');
            $table->time('work_time_begin3')->nullable()->comment('上班时间3');
            $table->time('work_time_end3')->nullable()->comment('下班时间3');
            $table->tinyInteger('is_siesta')->length(1)->comment('是否开启午休 1-开启， 2-关闭');
            $table->time('begin_siesta_time')->nullable()->comment('午休开始时间');
            $table->time('end_siesta_time')->nullable()->comment('午休结束时间');
            $table->integer('clock_time_begin1')->length(11)->nullable()->comment('允许上班打卡时间1');
            $table->integer('clock_time_end1')->length(11)->nullable()->comment('允许下班打卡时间1');
            $table->integer('clock_time_begin2')->length(11)->nullable()->comment('允许上班打卡时间2');
            $table->integer('clock_time_end2')->length(11)->nullable()->comment('允许下班打卡时间2');
            $table->integer('clock_time_begin3')->length(11)->nullable()->comment('允许上班打卡时间3');
            $table->integer('clock_time_end3')->length(11)->nullable()->comment('允许下班打卡时间3');
            $table->integer('elastic_min')->length(11)->nullable()->comment('弹性标准');
            $table->integer('serious_late_min')->length(11)->nullable()->comment('严重迟到标准');
            $table->integer('absenteeism_min')->length(11)->nullable()->comment('旷工标准');
            $table->integer('admin_id')->length(11)->nullable()->comment('操作人员ID');
            $table->timestamp('created_at')->nullable()->comment('创建时间');
            $table->timestamp('updated_at')->nullable()->comment('更新时间');
            $table->timestamp('deleted_at')->nullable()->comment('删除时间');
            $table->index('deleted_at');
            $table->comment = '考勤班次表';
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('attendance_api_classes');
    }
}
