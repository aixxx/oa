<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAttendanceCheckinoutTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('attendance_checkinout', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('user_id')->nullable()->index('Idx_user_id')->comment('考勤机员工id');
			$table->dateTime('check_time')->index('idx_check_time')->comment('签卡时间');
			$table->integer('sensor_id')->comment('考勤机编号');
			$table->string('sn', 32)->nullable()->comment('考勤机序列号');
			$table->index(['user_id','check_time','sensor_id','sn']);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('attendance_checkinout');
	}

}
