<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAttendanceWhiteTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('attendance_white', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('chinese_name', 32)->index()->comment('中文名');
			$table->timestamps();
			$table->integer('user_id')->default(0)->comment('员工编号');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('attendance_white');
	}

}
