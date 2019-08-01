<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTaskScoreLogByMonthTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('task_score_log_by_month', function(Blueprint $table)
		{
			$table->increments('id');
			$table->date('dates')->comment('统计日期');
			$table->integer('user_id');
			$table->integer('score');
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
		Schema::drop('task_score_log_by_month');
	}

}
