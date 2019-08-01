<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSalaryAttendanceTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('salary_attendance', function(Blueprint $table)
		{
			$table->increments('id');
			$table->bigInteger('user_id')->unsigned();
			$table->integer('year');
			$table->integer('month');
			$table->integer('should_attendance_days')->default(0)->comment('应出勤天数');
			$table->integer('actual_attendance_days')->default(0)->comment('实际出勤天数');
			$table->integer('casual_leave_days')->default(0)->comment('事假天数');
			$table->integer('sick_leave_days')->default(0)->comment('病假天数');
			$table->integer('overtime_days')->default(0)->comment('加班小时数');
			$table->decimal('sick_leave_minus', 10)->default(0.00)->comment('病假扣款');
			$table->decimal('casual_leave_minus', 10)->default(0.00)->comment('事假扣款');
			$table->decimal('overtime_salary', 10)->default(0.00)->comment('加班工资');
			$table->string('remark', 191)->nullable()->comment('备注');
			$table->softDeletes();
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
		Schema::drop('salary_attendance');
	}

}
