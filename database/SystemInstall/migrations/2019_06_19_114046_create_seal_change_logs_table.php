<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSealChangeLogsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('seal_change_logs', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('change_seal_id')->comment('印章id');
			$table->string('change_entry_id', 32)->default('')->comment('流程编号');
			$table->integer('change_lend_user_id')->comment('出借人');
			$table->integer('change_receive_user_id')->comment('接收人');
			$table->integer('change_status')->comment('0-流转中，1-持有中');
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
		Schema::drop('seal_change_logs');
	}

}
