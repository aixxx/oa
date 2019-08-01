<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAttendanceApiCycleContentTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('attendance_api_cycle_content', function(Blueprint $table)
		{
			$table->increments('id')->comment('自动编号');
			$table->integer('sort')->comment('顺序');
			$table->integer('cycle_id')->comment('周期ID');
			$table->integer('classes_id')->comment('班次ID');
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
		Schema::drop('attendance_api_cycle_content');
	}

}
