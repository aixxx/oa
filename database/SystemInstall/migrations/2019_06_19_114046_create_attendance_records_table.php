<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAttendanceRecordsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('attendance_records', function(Blueprint $table)
		{
			$table->increments('id');
			$table->timestamp('month')->default(DB::raw('CURRENT_TIMESTAMP'))->comment('统计月份');
			$table->integer('late')->comment('迟到');
			$table->integer('leave_early')->comment('早退');
			$table->integer('missing_card')->comment('缺卡');
			$table->integer('absenteeism')->comment('旷工');
			$table->integer('overtime')->comment('加班');
			$table->integer('leave')->comment('请假');
			$table->integer('full_attendance')->comment('全勤');
			$table->integer('change')->comment('调休');
			$table->timestamps();
			$table->softDeletes();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('attendance_records');
	}

}
