<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateExecutiveCarsSendbackTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('executive_cars_sendback', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('title')->comment('用途');
			$table->integer('cars_id')->comment('车ID');
			$table->integer('cars_use_id')->comment('用车记录ID');
			$table->integer('people_number')->comment('用车人数');
			$table->date('begin_time')->comment('开始时间');
			$table->date('end_time')->comment('返回时间');
			$table->integer('mileage')->comment('里程数');
			$table->string('remark')->nullable()->comment('备注');
			$table->integer('entrise_id');
			$table->boolean('status')->comment('审核状态');
			$table->integer('user_id')->comment('归还人');
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
		Schema::drop('executive_cars_sendback');
	}

}
