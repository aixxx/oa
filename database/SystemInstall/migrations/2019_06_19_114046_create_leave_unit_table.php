<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateLeaveUnitTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('leave_unit', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('uname', 100)->comment('请假单位名称');
			$table->integer('type')->comment('类型 1:最小请假单位 2:计算请假时长方式 3:余额发放形式 4:有效期规则 5:新员工何时可以请假');
			$table->softDeletes()->comment('1');
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
		Schema::drop('leave_unit');
	}

}
