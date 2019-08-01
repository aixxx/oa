<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAttendanceAnnualRuleTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('attendance_annual_rule', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('min')->comment('区间启始值');
			$table->integer('max')->comment('区间结束值');
			$table->integer('value')->comment('区间值');
			$table->integer('type')->nullable()->comment('年假方案');
			$table->string('description', 191)->comment('描述');
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
		Schema::drop('attendance_annual_rule');
	}

}
