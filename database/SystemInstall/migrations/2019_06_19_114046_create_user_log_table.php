<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUserLogTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('user_log', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('target_user_id')->nullable()->comment('目标人');
			$table->integer('operate_user_id')->nullable()->comment('操作人');
			$table->string('action')->nullable()->comment('动作');
			$table->text('init_data', 65535)->nullable()->comment('原数据');
			$table->text('target_data', 65535)->nullable()->comment('目标数据');
			$table->string('extra')->nullable()->comment('扩展信息（JSON 数据）');
			$table->string('note')->nullable()->comment('备注信息');
			$table->timestamps();
			$table->integer('type')->nullable()->comment('信息类型');
			$table->text('init_json_data', 65535)->nullable()->comment('原始json数据');
			$table->text('target_json_data', 65535)->nullable()->comment('变化后json数据');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('user_log');
	}

}
