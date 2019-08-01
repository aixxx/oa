<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAttendanceVacationConversionsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('attendance_vacation_conversions', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('user_id');
			$table->string('annual_balance', 32)->nullable()->comment('法定年假结余（小时）');
			$table->string('company_benefits_balance', 32)->nullable()->comment('公司福利年假结余（小时）');
			$table->string('sum_amount', 32)->nullable()->comment('总结余');
			$table->string('state', 32)->nullable()->comment('结算状态：0未处理，1结算成功');
			$table->string('date_year', 32)->nullable()->comment('结算年份');
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
		Schema::drop('attendance_vacation_conversions');
	}

}
