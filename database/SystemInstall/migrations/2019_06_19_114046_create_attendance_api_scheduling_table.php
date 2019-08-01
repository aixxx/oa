<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAttendanceApiSchedulingTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('attendance_api_scheduling', function(Blueprint $table)
		{
			$table->increments('id')->comment('自动编号');
			$table->integer('attendance_id')->comment('考勤组ID');
			$table->integer('user_id')->comment('用户ID');
			$table->date('dates')->nullable()->comment('排班日期');
			$table->integer('classes_id')->nullable()->comment('班次ID');
			$table->date('take_effect_dates')->nullable()->comment('生效时间日期');
			$table->integer('admin_id')->nullable()->comment('操作人员ID');
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
		Schema::drop('attendance_api_scheduling');
	}

}
