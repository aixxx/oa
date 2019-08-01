<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateVoteRecordTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('vote_record', function(Blueprint $table)
		{
			$table->bigInteger('id')->comment('自动编号');
			$table->bigInteger('user_id')->nullable()->comment('用户编号');
			$table->bigInteger('vo_id')->nullable()->comment('选项编号');
			$table->bigInteger('v_id')->nullable()->comment('投票编号');
			$table->integer('v_number')->nullable()->comment('投票票数');
			$table->string('user_name')->nullable()->comment('用户名称');
			$table->timestamps();
			$table->softDeletes()->index();
			$table->string('avatar')->nullable()->comment('用户头像');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('vote_record');
	}

}
