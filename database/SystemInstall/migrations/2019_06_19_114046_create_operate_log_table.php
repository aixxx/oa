<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateOperateLogTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('operate_log', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('operate_user_id')->nullable()->comment('操作员工ID');
			$table->string('action', 191)->nullable()->comment('动作');
			$table->string('type', 191)->nullable()->comment('类型');
			$table->integer('object_id')->nullable()->comment('对象ID');
			$table->string('object_name', 191)->nullable()->comment('对象名称');
			$table->text('content', 65535)->nullable()->comment('内容');
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
		Schema::drop('operate_log');
	}

}
