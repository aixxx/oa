<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAttendanceApiOvertimeRuleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attendance_api_overtime_rule', function (Blueprint $table) {
            $table->increments('id')->comment('自动编号');
            $table->tinyInteger('is_working_overtime')->length(1)->comment('工作日允许加班 1-允许 0-不允许')->default(1);
            $table->tinyInteger('working_overtime_type')->length(1)
                ->comment('工作日加班计算方式， 1-需审批，以审批单为准。2-需审批，以打卡为准，但不能超过审批时长。3-无需审批，根据打卡时间为准')->default(1);
            $table->integer('working_begin_time')->length(11)->nullable()->comment('工作日加班起算时间')->default(30);
            $table->integer('working_min_overtime')->length(11)->nullable()->comment('工作日最小加班时长')->default(60);

            $table->tinyInteger('is_rest_overtime')->length(1)->comment('休息日允许加班 1-允许 0-不允许')->default(1);
            $table->tinyInteger('rest_overtime_type')->length(1)
                ->comment('工作日加班计算方式， 1-需审批，以审批单为准。2-需审批，以打卡为准，但不能超过审批时长。3-无需审批，根据打卡时间为准')->default(1);
            $table->integer('rest_min_overtime')->length(11)->nullable()->comment('休息日最小加班时长')->default(60);
            $table->timestamp('created_at')->nullable()->comment('创建时间');
            $table->timestamp('updated_at')->nullable()->comment('更新时间');
            $table->timestamp('deleted_at')->nullable()->comment('删除时间');
            $table->index('deleted_at');
            $table->comment = '加班规则设置';
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('attendance_api_overtime_rule');
    }
}
