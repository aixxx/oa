<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAttendanceApiStaffTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('attendance_api_staff', function(Blueprint $table)
		{
			$table->increments('id')->comment('自动编号');
			$table->integer('attendance_id')->comment('考勤组ID');
			$table->integer('user_id')->comment('员工ID');
			$table->boolean('is_attendance')->default(0)->comment('是否需要参与考勤 1-需要 2-不需要');
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
		Schema::drop('attendance_api_staff');
	}

}
