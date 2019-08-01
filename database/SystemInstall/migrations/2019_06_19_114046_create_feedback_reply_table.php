<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateFeedbackReplyTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('feedback_reply', function(Blueprint $table)
		{
			$table->increments('id');
			$table->bigInteger('cid')->comment('关联id');
			$table->bigInteger('user_id')->comment('回复人id');
			$table->boolean('type')->comment('回复的类型，1:反馈，2：加班');
			$table->string('content')->comment('回复内容');
			$table->dateTime('add_time')->comment('回复时间');
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
		Schema::drop('feedback_reply');
	}

}
