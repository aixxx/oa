<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSchedulesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('schedules', function(Blueprint $table)
		{
			$table->bigInteger('id', true)->unsigned();
			$table->string('content')->comment('日程描述');
			$table->boolean('all_day_yes')->comment('是否全天，1：是，2：否');
			$table->dateTime('start_at')->nullable()->comment('开始时间');
			$table->dateTime('end_at')->nullable()->comment('截止时间');
			$table->boolean('send_type')->comment('发送方式，1：应用，2：短信');
			$table->boolean('prompt_type')->comment('提醒类型，提醒类型，1：截止前15分钟，2：前1小时，3：前3小时，4：前1天');
			$table->boolean('repeat_type')->comment('重复设置，1：重复，2：不重复');
			$table->string('address')->comment('地点');
			$table->bigInteger('create_schedule_user_id')->comment('日程创建者ID');
			$table->timestamps();
			$table->softDeletes()->comment('删除时间');
			$table->integer('report_id')->nullable()->default(0)->comment('汇报id');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('schedules');
	}

}
