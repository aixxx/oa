<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAttendanceUserInfoTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('attendance_user_info', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('user_id')->unsigned()->unique('UN_userid')->comment('不可编辑系统自动生成');
			$table->integer('badge_number')->nullable()->comment('考勤号码');
			$table->string('ssn', 32)->nullable()->comment('编号');
			$table->string('name', 64)->nullable()->comment('中文姓名');
			$table->integer('employee_num')->nullable()->index('Idx_employee_num')->comment('员工唯一编号');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('attendance_user_info');
	}

}
