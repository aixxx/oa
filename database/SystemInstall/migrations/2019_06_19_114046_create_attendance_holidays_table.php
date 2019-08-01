<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAttendanceHolidaysTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('attendance_holidays', function(Blueprint $table)
		{
			$table->integer('holiday_id', true)->comment('主键');
			$table->dateTime('holiday_date')->index('holiday_date_INDEX')->comment('时间');
			$table->boolean('holiday_status')->comment('状态(0-工作，1-休息)');
			$table->boolean('holiday_type')->comment('是否法定节假日(0-否，1-是,周末不是)');
			$table->timestamps();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('attendance_holidays');
	}

}
