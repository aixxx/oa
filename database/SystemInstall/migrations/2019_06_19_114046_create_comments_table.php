<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCommentsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('comments', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('user_id')->comment('评分人id');
			$table->integer('o_id')->comment('任务id');
			$table->string('score', 100)->comment('评分数');
			$table->string('comments', 100)->comment('评论');
			$table->integer('comment_time')->comment('评论时间');
			$table->integer('type')->comment('0：个人打分 1：自动打分；');
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
		Schema::drop('comments');
	}

}
