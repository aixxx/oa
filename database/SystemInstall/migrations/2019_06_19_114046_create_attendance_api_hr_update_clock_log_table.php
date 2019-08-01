<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAttendanceApiHrUpdateClockLogTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('attendance_api_hr_update_clock_log', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('admin_id')->comment('管理员ID');
			$table->integer('user_id')->comment('用户ID');
			$table->integer('classes_id')->comment('班次ID');
			$table->date('dates')->comment('日期');
			$table->date('work_time')->comment('上班或者下班时间');
			$table->string('remark')->comment('描述');
			$table->string('remark_image')->comment('描述图片');
			$table->integer('type')->comment('类别 1-上班 2-下班');
			$table->boolean('anomaly_type')->comment('异常类型 0-正常 1-迟到 2-早退 3-加班 4-缺卡 5-旷工');
			$table->integer('anomaly_time')->comment('异常时间');
			$table->integer('anomaly_id');
			$table->integer('clock_nums')->comment('第几次上下班');
			$table->timestamps();
			$table->softDeletes()->index()->comment('删除时间');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('attendance_api_hr_update_clock_log');
	}

}
