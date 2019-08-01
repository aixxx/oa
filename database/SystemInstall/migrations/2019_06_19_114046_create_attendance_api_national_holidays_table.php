<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAttendanceApiNationalHolidaysTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('attendance_api_national_holidays', function(Blueprint $table)
		{
			$table->increments('id')->comment('自动编号');
			$table->date('dates')->comment('日期');
			$table->boolean('type')->comment('类型 1-工作日改为休息  2-休息日改为上班');
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
		Schema::drop('attendance_api_national_holidays');
	}

}
