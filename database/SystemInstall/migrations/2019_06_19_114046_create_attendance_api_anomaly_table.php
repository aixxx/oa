<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAttendanceApiAnomalyTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('attendance_api_anomaly', function(Blueprint $table)
		{
			$table->increments('id')->comment('自动编号');
			$table->integer('user_id')->comment('用户ID');
			$table->date('dates')->comment('异常日期');
			$table->boolean('anomaly_type')->comment('异常类型 1-迟到，2-早退，3-加班');
			$table->integer('anomaly_time')->default(0)->comment('异常时间');
			$table->boolean('is_serious_late')->nullable()->default(0)->comment('是否严重迟到');
			$table->boolean('is_absenteeism')->nullable()->default(0)->comment('是否算旷工');
			$table->timestamps();
			$table->softDeletes()->index()->comment('删除时间');
			$table->boolean('is_count')->default(0)->comment('是否统计调休时间');
			$table->boolean('overtime_date_type')->default(1)->comment('加班日期类型。1-正常工作日，2-周末，3-节假日');
			$table->integer('clock_id')->comment('打卡ID');
			$table->boolean('clock_nums')->default(0)->comment('第几次上下班2');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('attendance_api_anomaly');
	}

}
