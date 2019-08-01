<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTaskScoreTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('task_score', function(Blueprint $table)
		{
			$table->increments('id')->comment('自动编号');
			$table->integer('pid')->index()->comment('任务评论id');
			$table->boolean('score')->comment('任务分数');
			$table->timestamps();
			$table->softDeletes()->comment('删除时间');
			$table->integer('user_id');
			$table->integer('my_task_id')->default(0)->comment('我的任务ID');
			$table->integer('admin_id')->default(0)->comment('评分人ID，0:系统');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('task_score');
	}

}
