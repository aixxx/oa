<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAttendanceApiAnomalyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attendance_api_anomaly', function (Blueprint $table) {
            $table->increments('id')->comment('自动编号');
            $table->integer('user_id')->length(11)->comment('用户ID');
            $table->date('dates')->comment('异常日期');
            $table->tinyInteger('anomaly_type')->length(1)->comment('异常类型 1-迟到，2-早退，3-加班');
            $table->integer('anomaly_time')->length(11)->comment('异常时间')->default(0);
            $table->tinyInteger('is_serious_late')->length(1)->nullable()->comment('是否严重迟到')->default(0);
            $table->tinyInteger('is_absenteeism')->length(1)->nullable()->comment('是否算旷工')->default(0);
            $table->timestamp('created_at')->nullable()->comment('创建时间');
            $table->timestamp('updated_at')->nullable()->comment('更新时间');
            $table->timestamp('deleted_at')->nullable()->comment('删除时间');
            $table->index('deleted_at');
            $table->comment = '考勤异常记录';
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('attendance_api_anomaly');
    }
}
