<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUserSchedulesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('user_schedules', function(Blueprint $table)
		{
			$table->bigInteger('id', true)->unsigned()->comment('自动编号');
			$table->bigInteger('create_schedule_user_id')->unsigned()->index()->comment('日程创建者ID');
			$table->bigInteger('schedule_id')->unsigned()->index()->comment('日程id');
			$table->string('content')->comment('日程描述');
			$table->bigInteger('user_id')->unsigned()->index()->comment('用户id');
			$table->string('create_schedule_user_name')->index()->comment('发起人名字');
			$table->string('user_name')->index()->comment('接收人名字');
			$table->integer('confirm_yes')->unsigned()->comment('1:未接受，2:接受,3:拒绝');
			$table->dateTime('confirm_at')->nullable()->comment('确认时间');
			$table->timestamps();
			$table->softDeletes()->index()->comment('删除时间');
			$table->boolean('prompt_type')->comment('提醒类型，提醒类型，0:不提醒,1：截止前15分钟，2：前1小时，3：前3小时，4：前1天');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('user_schedules');
	}

}
