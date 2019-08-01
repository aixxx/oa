<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAttendanceRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attendance_records', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamp('month')->comment('统计月份');
            $table->integer('late')->comment('迟到');
            $table->integer('leave_early')->comment('早退');
            $table->integer('missing_card')->comment('缺卡');
            $table->integer('absenteeism')->comment('旷工');
            $table->integer('overtime')->comment('加班');
            $table->integer('leave')->comment('请假');
            $table->integer('full_attendance')->comment('全勤');
            $table->integer('change')->comment('调休');
            //迟到 早退 缺卡 旷工 加班 请假 全勤 调休
            $table->timestamps();
            $table->softDeletes();
        });
        DB::statement("ALTER TABLE `attendance_records` comment '员工考勤统计记录表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('attendance_records');
    }
}
