<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateMeetingTaskTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('meeting_task', function(Blueprint $table)
		{
			$table->bigInteger('id', true)->unsigned()->comment('自动编号');
			$table->bigInteger('user_id')->unsigned()->nullable()->comment('指派人编号id');
			$table->bigInteger('m_id')->unsigned()->nullable()->comment('会议编号id');
			$table->text('count', 65535)->nullable()->comment('任务类容');
			$table->dateTime('end')->nullable()->comment('任务结束时间');
			$table->boolean('status')->nullable()->default(1)->comment('状态 0删除');
			$table->timestamps();
			$table->string('chinese_name', 50)->nullable()->comment('用户名');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('meeting_task');
	}

}
