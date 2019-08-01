<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAttendanceApiCycleTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('attendance_api_cycle', function(Blueprint $table)
		{
			$table->increments('id')->comment('自动编号');
			$table->boolean('type')->comment('类型 1-做一休一 2-两班轮换 3-三班倒');
			$table->string('title', 50)->comment('周期名称');
			$table->integer('cycle_days')->comment('周期天数');
			$table->integer('admin_id')->comment('操作人员ID');
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
		Schema::drop('attendance_api_cycle');
	}

}
