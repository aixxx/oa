<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAttendanceVacationsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('attendance_vacations', function(Blueprint $table)
		{
			$table->increments('vacation_id');
			$table->integer('user_id')->unique('user_id')->comment('员工编号');
			$table->string('annual', 32)->default('0')->comment('法定年假 （小时）');
			$table->string('company_benefits', 32)->default('0')->comment('公司福利年假（小时）');
			$table->string('marriage', 191)->default('0')->comment('婚嫁（冗余）');
			$table->string('funeral', 191)->default('0')->comment('丧假（冗余）');
			$table->string('maternity', 191)->default('0')->comment('产假（冗余）');
			$table->string('paternity', 191)->default('0')->comment('陪产假（冗余）');
			$table->string('check_up', 191)->default('0')->comment('产检假（冗余）');
			$table->string('breastfeeding', 191)->default('0')->comment('哺乳假（冗余）');
			$table->string('working_injury', 191)->default('0')->comment('工伤假（冗余）');
			$table->string('full_pay_sick', 32)->default('0')->comment('全薪病假（小时）');
			$table->string('sick', 191)->default('0')->comment('病假（冗余）');
			$table->string('extra_day_off', 32)->default('0')->comment('调休（小时）');
			$table->string('spring_festival', 191)->default('0')->comment('春节假（冗余）');
			$table->string('business_trip', 191)->default('0')->comment('出差假（冗余）');
			$table->timestamps();
			$table->float('actual_annual', 15)->default(0.00)->comment('实际发放法定年假');
			$table->float('actual_company_benefits', 15)->default(0.00)->comment('实际发放福利年假');
			$table->integer('actual_full_pay_sick')->default(0)->comment('实际发放全薪病假（小时）');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('attendance_vacations');
	}

}
