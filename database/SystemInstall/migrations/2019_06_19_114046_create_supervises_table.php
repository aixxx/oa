<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSupervisesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('supervises', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('from_user_id')->default(0)->comment('指派者id');
			$table->integer('user_id')->unsigned()->default(0)->comment('督办用户id');
			$table->integer('relate_id')->comment('关联表主键id');
			$table->boolean('type')->nullable()->default(0)->comment('类型 1任务');
			$table->timestamps();
			$table->dateTime('deleted_at')->default('0000-00-00 00:00:00')->comment('删除时间');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('supervises');
	}

}
